<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request){

        $validatedData = $request->validate([
            'name' => 'bail|required|string|max:255',
            'email' => 'bail|required|string|email|max:255|unique:users',
            'password' => 'bail|required|string|regex:/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/',
            'genre' => 'required|in:Hombre,Mujer,Otro',
            'surname' => 'required|string|max:255',
            'image' => 'string|max:255|nullable'
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'surname' => $validatedData['surname'],
            'image' => $validatedData['image'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'genre' => $validatedData['genre']
        ]);

        //$token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'Sistema' => 'Registro completado correctamente'
        ]);
    }

    public function login(Request $request){

        if (!Auth::attempt($request->only('email', 'password'))) { //cambiar email por name para entrega se inicia con usuario
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = User::where('email',$request['email'])->firstOrFail(); //cambiar email por name para entrega se inicia con usuario

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_Token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function infouser(Request $request){
        return $request->user();
    }

    public function recoverPass(Request $request){


        $Pass_pattern = "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/";

        $user = User::where('email',$request->email)->first();

        if ($user) {
            do{
                $password = Str::random(8);
            }while(!preg_match($Pass_pattern, $password)); //hacer para que se envie por correo??
            $user->password = Hash::make($password);
            $user->save();

            return response()->json([
            'Mensaje' => $password
        ]);
        }else{
            return response()->json([
                'Mensaje' => 'No se encunetra el usuario en el sistema'
            ], 404);
        }
    }
}
