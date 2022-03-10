<?php

namespace App\Http\Controllers;

use App\Models\Matchs;
use App\Models\MatchUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MatchController extends Controller
{
    //Futuro cambiar a tener que estar logueado como club
    /**
     * Crear Partido
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function createMatch(Request $request)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        $validatedData = Validator::make(
            $request->all(),
            [
                'club_id' => 'required|exists:clubs,id',
                'court_id' => 'required|exists:courts,id',
                'price_people' => 'required|numeric',
                'lights' => 'required|boolean',
                'day' => 'required|date_format:Y-m-d',
                'start_time' => 'required|date_format:H:i:s',
                'end_time' => 'required|date_format:H:i:s',

            ],
            [
                'club_id.required' => 'Has de introducir un Club',
                'club_id.exists' => 'Introduce un club que exista',
                'court_id.required' => 'Introduce una pista',
                'court_id.exists' => 'Introduce una pista que exista',
                'start_time.required' => 'Introduce fecha de inicio del partido',
                'start_time.date_format' => 'Introduce el formato de la fecha de esta manera: H:i:s',
                'end_time.required' => 'Introduce fecha a la que acaba el partido',
                'end_time.date_format' => 'Introduce el formato de la fecha de esta manera: H:i:s',
            ]
        );

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors()->all();
            $response['msg'] = 'Partido No Registrado';

            return response()->json($response, 406);
        } else {
            try {
                $QR = mt_rand(1000, 9999);
                $final_time = $request->input('day') . " " . $request->input('end_time');
                $start_time = $request->input('day') . " " . $request->input('start_time');
                $Match = Matchs::create([
                    'QR' => $QR,
                    'club_id' => $request->input('club_id'),
                    'court_id' => $request->input('court_id'),
                    'lights' => $request->input('lights'),
                    'day' => $request->input('day'),
                    'price_people' => $request->input('price_people'),
                    'start_time' => $request->input('start_time'),
                    'end_time' => $request->input('end_time'),
                    'final_time' => $final_time,
                    'start_Datetime' => $start_time,
                ]);

                $response['status'] = 1;
                $response['data'] = $Match;
                $response['msg'] = 'Partido Registrado Correctamente';


                return response()->json($response, 200);
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 406);
            }
        }
    }
    /**
     *Ver Partidos
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function seeMatches(Request $request)
    {
        $response = ["status" => 1, "msg" => "", "data" => []];
        try {
            $Matchs = DB::table('matchs')
                ->join('clubs', 'club_id', 'clubs.id')
                ->join('courts', 'court_id', 'courts.id')
                ->select(
                    'clubs.name as Club',
                    'courts.name as Pista',
                    'matchs.start_dateTime as Hora inicio',
                    'matchs.end_dateTime as Hora finalizacion',
                    'matchs.price_people as Precio persona'
                )
                ->get();

            // Imágenes de los usuarior inscritos
            // Array dentro de array que muestre el día y la hora del partido

            $response['data'] = $Matchs;
            $response['msg'] = "Partidos";
            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 406);
        }
    }

    /**
     * Inscribirse a partido
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function matchInscription(Request $request)
    {
        $response = ["status" => 1, "msg" => "", "data" => []];

        $validatedData = Validator::make(
            $request->all(),
            [
                'match_id' => 'required|exists:matchs,id',
            ],
            [
                'match_id.required' => 'Introduce un partido',
                'match_id.exists' => 'Introduce un partido que exista',
            ]
        );

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors()->all();
            $response['msg'] = 'No se te ha podido registrar en el partido ';

            return response()->json($response, 406);
        } else {
            $count = MatchUser::where('match_id', $request->input('match_id'))->count();
            $usercheck = MatchUser::where('user_id', Auth::id())->where('match_id', $request->input('match_id'))->get();
            try {
                if ($count == 4) {
                    $response['status'] = 0;
                    $response['msg'] = 'El partido ya esta completo';
                    return response()->json($response, 406);
                } elseif (!$usercheck->isEmpty()) {
                    $response['status'] = 0;
                    $response['msg'] = 'No te puedes inscribir 2 veces al mismo partido';
                    return response()->json($response, 406);
                } else {
                    $MatchUser = MatchUser::create([
                        'match_id' => $request->input('match_id'),
                        'user_id' => Auth::id()
                    ]);

                    $response['status'] = 1;
                    $response['data'] = $MatchUser;
                    $response['msg'] = 'Inscrito al partido correctamente';


                    return response()->json($response, 200);
                }
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 406);
            }
        }
    }

    /**
     * Ver partidos finalizados
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     * 
     */
    public function ended_matches(Request $request)
    {
        $response = ["status" => 1, "msg" => "", "data" => []];

        try {
            $query = DB::table('matchs')
                ->join('match_user', 'matchs.id', '=', 'match_user.match_id')
                ->select('matchs.*')
                ->where('match_user.user_id', Auth::id())
                ->where('matchs.final_time', '<', now())
                ->get();

            $response['status'] = 1;
            $response['data'] = $query;
            $response['msg'] = 'Partidos finalizados';


            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 406);
        }
    }
}
