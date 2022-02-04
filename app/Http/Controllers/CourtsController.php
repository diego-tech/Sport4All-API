<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourtsController extends Controller
{
    public function CourtRegist(Request $request){
        $response = ["status" => 1, "data" => [], "msg" => ""];

        $validatedData = Validator::make($request->all(),
        [
            'club_id' => 'required|exists:clubs,id',
            'name' => 'required|string|max:255',
            'type' => ['required',Rule::in('Indoor','Outdoor')],
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
        ]);

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors()->all();
            $response['msg'] = 'Pista No Registrada';

            return response()->json($response, 406);
        }else{
            try{

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

            }catch(\Exception $e){
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 406);
            }
        }
    }
}
