<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Matchs;
use App\Models\Reserve;
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
    public function CourtRegister(Request $request){
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
    public function CourtReserve(Request $request){
        $response = ["status" => 1, "msg" => "", "data" => []];

        $validatedData = Validator::make($request->all(),[
            'court_id' => 'required|exists:courts,id',
            'lights' => 'required|boolean',
            'start_dateTime' => 'required|date_format:Y-m-d H:i:s',
            'end_dateTime' => 'required|date_format:Y-m-d H:i:s',
        ],
        [
            'court_id.required' => 'Introduce una pista',
            'court_id.exists' => 'Introduce una pista que exista',
            'start_dateTime.required' => 'Introduce fecha de inicio del partido',
            'start_dateTime.date_format' => 'Introduce el formato de la fecha de esta manera: Y-m-d H:i:s',
            'end_dateTime.required' => 'Introduce fecha a la que acaba el partido',
            'end_dateTime.date_format' => 'Introduce el formato de la fecha de esta manera: Y-m-d H:i:s',
        ]);

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors()->all();
            $response['msg'] = 'No se te ha podido reservar la pista ';

            return response()->json($response, 406);
        }else{
            //falta condicional para no poder reservar 2 a la vez en el mismo intervalo de tiempo
            $query1 = Reserve::where('start_dateTime','<=', $request->input('start_dateTime'))
                                ->where('end_dateTime','>=', $request->input('start_dateTime'))
                                ->get();
                            
            $query2 = Reserve::where('start_dateTime', '<=', $request->input('end_dateTime'))
                                ->Where('end_dateTime','>=',$request->input('end_dateTime'))
                                ->get();
            
            $query3 = Reserve::where('start_dateTime','>=', $request->input('start_dateTime'))
                                ->where('end_dateTime','<=', $request->input('end_dateTime'))
                                ->get();
            $response['data']['query1'] = $query1;
            $response['data']['query2'] = $query2;
            $response['data']['query3'] = $query3;
            return response()->json($response);
            try{        
                $QR = mt_rand(1000,9999);
                $Reserve = Reserve::create([
                    'QR' => $QR,
                    'user_id' => Auth::id(),
                    'court_id' => $request->input('court_id'),
                    'lights' => $request->input('lights'),
                    'start_dateTime' => $request->input('start_dateTime'),
                    'end_dateTime' => $request->input('end_dateTime'),
                ]);

                $court = Court::find($request->input('court_id'));

                $response['status'] = 1;
                $response['data'] = $Reserve;
                $response['msg'] = 'Reserva creada Correctamente, precio de la pista: ' . $court->price . '€';

                
                return response()->json($response, 200);
            }catch(\Exception $e){
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 406);
            }
        }
    }

    public function freeCourts(Request $request){
        $response = ["status" => 1, "msg" => "", "data" => []];

        $validatedData = Validator::make($request->all(),[
            'day' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format: H:i:s'
        ]);

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors()->all();
            $response['msg'] = 'No se te ha podido reservar la pista ';

            return response()->json($response, 406);
        }else{
            
        }


    }
}
