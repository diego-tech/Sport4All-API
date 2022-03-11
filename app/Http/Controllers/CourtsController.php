<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Matchs;
use App\Models\Reserve;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDO;

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
                'price' => 'required|numeric',
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
                    'price' => $request->input('price'),
                ]);

                $response['status'] = 1;
                $response['data'] = $court;
                $response['msg'] = 'Pista Registrado Correctamente';

                return response()->json($response, 200);
            } catch (\Exception $e) {
                $response['status'] = 0;
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
                'lights' => 'required|boolean',
                'day' => 'required|date_format:Y-m-d',
                'start_time' => 'required|date_format:H:i:s',
                'time' => ['required', Rule::in(['60', '90', '120'])],
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
                    'QR' => $QR,
                    'user_id' => Auth::id(),
                    'court_id' => $request->input('court_id'),
                    'lights' => $request->input('lights'),
                    'start_time' => $request->input('start_time'),
                    'end_time' => $parsedEndTime,
                    'day' => $request->input('day'),
                    'final_time' => $final_time,
                    'start_Datetime' => $start_time,
                ]);

                $court = Court::find($request->input('court_id'));

                $response['status'] = 1;
                $response['data'] = $Reserve;
                if ($request->input('time') == '60') {
                    $price = $court->price;
                    $response['msg'] = 'Reserva creada Correctamente, precio de la pista: ' . $price . '€';
                } elseif ($request->input('time') == '90') {
                    $price = $court->price * 1.5;
                    $response['msg'] = 'Reserva creada Correctamente, precio de la pista: ' . $price . '€';
                } else {
                    $price = $court->price * 2;
                    $response['msg'] = 'Reserva creada Correctamente, precio de la pista: ' . $price . '€';
                }

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
        $freecourt = [];

        $validatedData = Validator::make($request->all(), [
            'day' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i:s'
        ]);

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors()->all();
            $response['msg'] = 'No se te ha podido reservar la pista ';

            return response()->json($response, 406);
        } else {
            $day = $request->input('day');
            $hour = $request->input('hour');

            $reserves = Reserve::select('id')

                ->where('reserves.day', $day)
                ->where('reserves.start_time', $hour)
                ->pluck('id')
                ->toArray();

            $courts = Court::select('id')->where('club_id', $request->input('club_id'))->pluck('id')->toArray();
            $result = array_diff($courts, $reserves);

            foreach ($result as $court) {
                $freecourt[] = Court::find($court);
            }

            $response['msg'] = 'Pistas libres';
            $response['data'] = $freecourt;
            return response()->json($response, 200);
        }
    }
}
