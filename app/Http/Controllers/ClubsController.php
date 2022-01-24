<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ClubsController extends Controller
{
    /**
     * Función para registrar clubs
     */
    public function register(Request $request){

        $validatedData = Validator::make($request->all(),[
            'name' => 'bail|required|string|max:255',
            'club_img' => 'string|max:255|nullable',
            'club_banner' => 'string|max:255|nullable',
            'direction' => 'required|string|max:255',
            'tlf' => 'required|string|regex:/[0-9]{9}/',
            'email' => 'bail|required|string|email|max:255|unique:clubs'
        ],
        [
            'name.required' => 'Introduzca un nombre para el club',
            'name.string' => 'El nombre debe ser un String',
            'name.max' => 'El nombre no puede superar 255 caracteres',
            'direction.required' => 'Intruduzca una dirección para el club',
            'direction.string' => 'La dirección debe ser un String',
            'direction.max' => 'La dirección no puede superar 255 caracteres',
            'tlf.required' => 'Introduzca un teléfono para el club',
            'tlf.regex' => 'El teléfono debe contener 9 números',
            'email.required' => 'Introduce un email correcto',
            'email.string' => 'El email debe ser un string',
            'email.email' => 'Introduce formato válido de email',
            'email.unique' => 'Este email ya está registrado'
        ]);

        if ($validatedData->fails()) {
            return $this->sendError('Datos incorrectos, club no registrado', $validatedData->errors()->all(),406);
        }else{
            try{
                $club = Club::create([
                'name' => $request->input('name'),
                'club_img' => $request->input('club_img'),
                'club_banner' => $request->input('club_banner'),
                'direction' => $request->input('direction'),
                'tlf' => $request->input('tlf'),
                'email' => $request->input('email')
                ]);

                $token = $club->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'access_Token' => $token,
                    'token_type' => 'Bearer'
                ]);

            }catch(\Exception $e){
                return response()->json([
                    'message' => 'Error al registrar el club',
                    'error' => $e
                ], 401); 
            }
        }

    }

    /**
     * Función para listar todos los clubs
     */
    public function listClubs(Request $request){
        $clubs = Club::all();
        return $clubs;
    }

}
