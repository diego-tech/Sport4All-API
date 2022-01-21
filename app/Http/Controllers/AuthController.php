<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    
    public function register(Request $request){

        $validatedData = Validator::make($request->all(),[
            'name' => 'bail|required|string|max:255',
            'email' => 'bail|required|string|email|max:255|unique:users',
            'password' => 'bail|required|string|regex:/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/',
            'genre' => 'required|in:Hombre,Mujer,Otro',
            'surname' => 'required|string|max:255',
            'image' => 'string|max:255|nullable'
        ],
        [
            'name.required' => 'Introduce tu nombre',
            'name.string' => 'El nombre debe ser un String',
            'name.max' => 'El nombre no puede superar 255 caracteres',
            'email.required' => 'Introduce un email correcto',
            'email.string' => 'El email debe ser un string',
            'email.email' => 'Introduce formato valido de email',
            'email.unique' => 'Este email ya esta registrado',
            'password.required' => 'Introduce una contraseña correcta debe tener minimo 8 caracteres 1 letra y una mayuscula',
            'password.regex' => 'Introduce una contraseña correcta debe tener minimo 8 caracteres 1 letra y una mayuscula',
            'surname.required' => 'Introduce tu apellido'
        ]);

        if ($validatedData->fails()) {
            return $this->sendError('Usuario no registrado', $validatedData->errors()->all(),406);
        }else{
            try{
            $user = User::create([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'image' => $request->input('image'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'genre' => $request->input('genre')
            ]);

             return $this->sendResponse(['info' => 'Token no created, Login to make token'],'Usuario registrado correctamenre');
            }catch(\Exception $e){
            return $this->sendError('Usuario no registrado', $e->getMessage(),406); 
            }
        }
    }

    public function login(Request $request){

        if (!Auth::attempt($request->only('email', 'password'))) { 
            return $this->sendError('Credenciales incorrectas','Email o password incorrectos', 401);
        }

        $user = User::where('email',$request['email'])->firstOrFail(); 

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->sendResponse(['access_Token' => $token,
            'token_type' => 'Bearer'], 'Sesion iniciada correctamente');
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




    public function sendResponse($result, $message){
        $response = [
            'success' => '1',
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code){
        $response = [
            'success' => '0',
            'message' => $error,
        ];

        if(!empty($errorMessages))
            $response['data']['error'] = $errorMessages;
        
        return response()->json($response, $code);         
    }
}
