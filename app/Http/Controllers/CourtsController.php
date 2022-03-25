<?php

namespace App\Http\Controllers;

use App\Http\Helpers\AuxFunctions;
use App\Models\Club;
use App\Models\Court;
use App\Models\Favourite;
use App\Models\Matchs;
use App\Models\Reserve;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CourtsController extends Controller
{

    /**
     * Registro de Pista
     * Futuro cambiar a tener que estar logueado como club
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function CourtRegister(Request $request)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        $validatedData = Validator::make(
            $request->all(),
            [
                'club_id' => 'required|exists:clubs,id',
                'name' => 'required|string|max:255',
                'type' => ['required', Rule::in('Indoor', 'Outdoor')],
                'sport' => ['required', Rule::in('Padel', 'Tenis')],
                'surface' => ['required', Rule::in('Hierba', 'Pista Rápida', 'Tierra Batida', 'Moqueta', 'Cesped')],
            ],
            [
                'name.required' => 'Introduce nombre de la pista',
                'name.string' => 'El nombre debe ser un String',
                'name.max' => 'El nombre no puede superar 255 caracteres',
                'club_id.required' => 'Has de introducir un club',
                'club_id.exists' => 'Ese club no existe',
                'type.required' => 'Elige tipo de pista',
                'price.required' => 'Pon precio a la pista',
                'price.numeric' => 'Debes introducir un número',
            ]
        );

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors()->all();
            $response['msg'] = 'Pista No Registrada';

            return response()->json($response, 406);
        } else {
            try {
                $court = Court::create([
                    'club_id' => $request->input('club_id'),
                    'name' => $request->input('name'),
                    'type' => $request->input('type'),
                    'sport' => $request->input('sport'),
                    'surfaces' => $request->input('surface'),
                ]);

                $response['status'] = 1;
                $response['data'] = $court;
                $response['msg'] = 'Pista Registrado Correctamente';

                return response()->json($response, 200);
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['data']['errors'] = "";
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 406);
            }
        }
    }

    /**
     * Reservar pista
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function CourtReserve(Request $request)
    {
        $response = ["status" => 1, "msg" => "", "data" => []];

        $validatedData = Validator::make(
            $request->all(),
            [
                'court_id' => 'required|exists:courts,id',
                'club_id' => 'required|exists:clubs,id',
                'lights' => 'required|boolean',
                'day' => 'required|date_format:Y-m-d',
                'start_time' => 'required|date_format:H:i:s',
                'time' => 'required',
            ],
            [
                'court_id.required' => 'Introduce una pista',
                'court_id.exists' => 'Introduce una pista que exista',
                'start_dateTime.required' => 'Introduce fecha de inicio del partido',
                'start_dateTime.date_format' => 'Introduce el formato de la fecha de esta manera: H:i:s',
                'day.required' => 'Introduce dia de la reserva',
                'day.date_format' => 'Introduce dia de la reserva en este formato: Y-m-d ',
            ]
        );

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors()->all();
            $response['msg'] = 'No se te ha podido reservar la pista ';

            return response()->json($response, 406);
        } else {
            try {
                $time = Carbon::parse($request->input('start_time'));
                $endTime = $time->addMinutes($request->input('time'));
                $parsedEndTime = $endTime->format('H:i:s');
                $final_time = $request->input('day') . " " . $parsedEndTime;
                $start_time = $request->input('day') . " " . $request->input('start_time');
                $QR = mt_rand(1000, 9999);
                $Reserve = Reserve::create([
                    'QR' => strval($QR),
                    'user_id' => Auth::id(),
                    'court_id' => $request->input('court_id'),
                    'club_id' => $request->input('club_id'),
                    'lights' => $request->input('lights'),
                    'start_time' => $request->input('start_time'),
                    'end_time' => $parsedEndTime,
                    'day' => $request->input('day'),
                    'final_time' => $final_time,
                    'start_Datetime' => $start_time,
                ]);

                $response['status'] = 1;
                $response['data'] = $Reserve;

                return response()->json($response, 200);
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 406);
            }
        }
    }

    /**
     * Obtener pistas libres
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function freeCourts(Request $request)
    {
        $response = ['status' => 1, 'data' => [], 'msg' => ''];

        $courtsResults = [];

        $validatedData = Validator::make($request->all(), [
            'day' => 'required|date_format:Y-m-d',
            'hour' => 'required|date_format:H:i:s'
        ]);

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors()->all();
            $response['msg'] = 'No se te ha podido reservar la pista ';

            return response()->json($response, 406);
        } else {
            $club_id = $request->input('club_id');
            $day = $request->input('day');
            $hour = $request->input('hour');

            $courts1 = Court::with('reserves', 'Courtprices')
                ->leftJoin('reserves', 'courts.id', '=', 'reserves.court_id')
                ->select('courts.*')
                ->where('courts.club_id', $club_id)
                ->where('start_time', '<=', $hour)
                ->where('end_time', '>=', $hour)
                ->where('day', $day);

            $courts = Court::with('reserves', 'Courtprices')
                ->leftJoin('matchs', 'courts.id', '=', 'matchs.court_id')
                ->select('courts.*')
                ->union($courts1)
                ->where('courts.club_id', $club_id)
                ->where('start_time', '<=', $hour)
                ->where('end_time', '>=', $hour)
                ->where('day', $day)
                ->pluck('id')
                ->toArray();


            $courtsAll = Court::all()->pluck('id')->toArray();
            $results = array_diff($courtsAll, $courts);

            foreach ($results as $court) {
                $court = Court::with('Courtprices')->where('id', $court)->get();
                $courtsResults[] = $court[0];
            }

            $response['msg'] = 'Pistas libres';
            $response['data'] = $courtsResults;

            return response()->json($response, 200);
        }
    }

    public function pending_reserves()
    {
        $response = ["status" => 1, "msg" => "", "data" => []];

        try {
            $query = Reserve::query()
                ->join('courts', 'reserves.court_id', '=', 'courts.id')
                ->join('clubs', 'courts.club_id', '=', 'clubs.id')
                ->select(
                    'reserves.*',
                    'clubs.name as clubName',
                    'clubs.direction as clubLocation',
                    'courts.name',
                    'courts.type',
                    'courts.sport',
                    'courts.surfaces',
                    'clubs.club_img as clubImg'
                )
                ->where('reserves.user_id', Auth::id())
                ->where('reserves.final_time', '>', Carbon::now('Europe/Madrid'))
                ->get();

            $response['status'] = 1;
            $response['data'] = $query;
            $response['msg'] = 'Reservas pendientes';
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 406);
        }
    }

    public function ended_reserves()
    {
        $response = ["status" => 1, "msg" => "", "data" => []];

        try {
            $query = Reserve::join('courts', 'reserves.court_id', '=', 'courts.id')
                ->join('clubs', 'courts.club_id', '=', 'clubs.id')
                ->select(
                    'reserves.*',
                    'clubs.*',
                    'clubs.name as clubName',
                    'clubs.direction as clubLocation',
                    'courts.name',
                    'courts.type',
                    'courts.sport',
                    'courts.surfaces',
                    'clubs.club_img as clubImg'
                )   
                ->where('reserves.user_id', Auth::id())
                ->where('reserves.final_time', '<', Carbon::now('Europe/Madrid'))
                ->get();

            $response['status'] = 1;
            $response['data'] = $query;
            $response['msg'] = 'Reservas finalizadas';

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 406);
        }
    }

    public function qr_validator(Request $request)
    {
        $response = ["status" => 1, "msg" => ""];

        try {
            $queryM = Matchs::join('match_user', 'matchs.id', '=', 'match_user.match_id')
                ->where('QR', $request->input('qr'))
                ->where('match_user.user_id', Auth::id());
            $testM = $queryM->get();

            $queryR = Reserve::where('QR', $request->input('qr'))->where('user_id', Auth::id());

            $substractMinutes = Carbon::now('Europe/Madrid')->subMinutes(10);
            $addMinutes = Carbon::now('Europe/Madrid')->addMinutes(10);

            $testR = $queryR->get();
            if (!$testM->isEmpty()) {
                $matchQRvalidated = $queryM
                    ->where('start_Datetime', '<=', $substractMinutes)
                    ->where('final_time', '>=', $addMinutes)
                    ->get();
                if (!$matchQRvalidated->isEmpty()) {
                    $response['status'] = 1;
                    $response['msg'] = 'Puerta abierta';
                    return response()->json($response, 200);
                }
            } elseif (!$testR->isEmpty()) {
                $reserveQRvalidated = $queryR
                    ->where('start_Datetime', '<=', $substractMinutes)
                    ->where('final_time', '>=', $addMinutes)
                    ->get();
                if (!$reserveQRvalidated->isEmpty()) {
                    $response['status'] = 1;
                    $response['msg'] = 'Puerta abierta';
                    return response()->json($response, 200);
                }
            }

            $response['status'] = 0;
            $response['msg'] = 'Horario incorrecto puerta cerrada';
            return response()->json($response, 403);
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 406);
        }
    }

    public function get_club_info_ended_events(Request $request)
    {
        $response = ["status" => 1, "msg" => "", "data" => []];

        try {
            $query = Club::find($request->input('id'));
            
            $ClubArray['id'] = $query->id;
            $ClubArray['name'] = $query->name;
            $ClubArray['club_img'] = $query->club_img;
            $ClubArray['club_banner'] = $query->club_banner;
            $ClubArray['direction'] = $query->direction;
            $ClubArray['tlf'] = $query->tlf;
            $ClubArray['email'] = $query->email;
            $ClubArray['web'] = $query->web;
            $ClubArray['description'] = $query->description;
            $ClubArray['first_hour'] = $query->first_hour;
            $ClubArray['last_hour'] = $query->last_hour;
            $queryFav = Favourite::where('user_id', Auth::id())->where('club_id', $query->id)->value('id');
            if ($queryFav) {
                $ClubArray['fav'] = True;
            } else {
                $ClubArray['fav'] = False;
            }
            $ClubArray['services'] = AuxFunctions::Get_services_from_club($query->id);

            $response['status'] = 1;
            $response['data'] = $ClubArray;
            $response['msg'] = "Todos los Clubes";
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 406);
        }
    }
}
