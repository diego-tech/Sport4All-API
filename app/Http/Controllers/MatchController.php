<?php

namespace App\Http\Controllers;

use App\Models\Matchs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MatchController extends Controller
{
    //Futuro cambiar a tener que estar logueado como club
    public function createMatch(Request $request){
        $response = ["status" => 1, "data" => [], "msg" => ""];

        $validatedData = Validator::make($request->all(),
        [
            'club_id' => 'required|exists:clubs,id',
            'court_id' => 'required|exists:courts,id',
            'price_people' => 'required|numeric',
            'lights' => 'required|boolean',
            'start_dateTime' => 'required|date_format:Y-m-d H:i:s',
            'end_dateTime' => 'required|date_format:Y-m-d H:i:s',

        ],
        [
            'club_id.required' =>'Has de introducir un Club',
            'club_id.exists' => 'Introduce un club que exista',
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
            $response['msg'] = 'Partido No Registrado';

            return response()->json($response, 406);
        }else{
            try{
                $QR = mt_rand(1000,9999);
                $Match = Matchs::create([
                    'QR' => $QR,
                    'club_id' => $request->input('club_id'),
                    'court_id' => $request->input('court_id'),
                    'lights' => $request->input('lights'),
                    'price_people' => $request->input('price_people'),
                    'start_dateTime' => $request->input('start_dateTime'),
                    'end_dateTime' => $request->input('end_dateTime'),
                ]);

                $response['status'] = 1;
                $response['data'] = $Match;
                $response['msg'] = 'Partido Registrado Correctamente';

                
                return response()->json($response, 200);

            }catch(\Exception $e){
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 406);
            }
        }

        
    }
}
