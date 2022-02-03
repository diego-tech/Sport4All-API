<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Registro de Imagen
     * 
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function getUploadImage(Request $request)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        $fileName = Storage::putFile("profileImages", $request->file('fileName'));

        $response['status'] = 1;
        $response['data']['errors'] = "";
        $response["msg"] = $fileName;

        return response()->json($response);
    }

    /**
     * Registro de Usuario
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function register(Request $request)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        $validatedData = Validator::make(
            $request->all(),
            [
                'name' => 'bail|required|string|max:255',
                'email' => 'bail|required|string|email|max:255|unique:users',
                'password' => 'bail|required|string|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{6,}/',
                'genre' => 'required|in:Hombre,Mujer,Otro',
                'surname' => 'required|string|max:255',
            ],
            [
                'name.required' => 'Introduce tu nombre',
                'name.string' => 'El nombre debe ser un String',
                'name.max' => 'El nombre no puede superar 255 caracteres',
                'email.required' => 'Introduce un email correcto',
                'email.string' => 'El email debe ser un string',
                'email.email' => 'Introduce formato valido de email',
                'email.unique' => 'Este email ya esta registrado',
                'password.required' => 'Introduce una contraseña correcta debe tener minimo 8 caracteres 1 letra, una mayuscula y un caracter especial',
                'password.regex' => 'Introduce una contraseña correcta debe tener minimo 8 caracteres 1 letra, una mayuscula y un caracter especial',
                'surname.required' => 'Introduce tu apellido'
            ]
        );

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors()->first();
            $response['msg'] = 'Usuario No Registrado';
            
            return response()->json($response, 406);
        } else {
            try {
                $user = User::create([
                    'name' => $request->input('name'),
                    'surname' => $request->input('surname'),
                    'image' => $request->input('image'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'genre' => $request->input('genre')
                ]);

                $response['status'] = 1;
                $response['data'] = $user;
                $response['msg'] = 'Usuario Registrado Correctamente';

                return response()->json($response, 200);
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 406);
            }
        }
    }

    /**
     * Login de Usuario
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function login(Request $request)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request['email'])->first();

        try {
            if ($user) {
                if (!Auth::attempt($credentials)) {
                    $response['status'] = 0;
                    $response['data']['errors'] = "";
                    $response['msg'] = "Email o Contraseña incorrectos";

                    return response()->json($response, 401);
                } else {
                    $user->tokens()->delete();
                    $token = $user->createToken('auth_token')->plainTextToken;

                    $response['status'] = 1;
                    $response['data'] = $user;
                    $response['data']['token'] = $token;
                    $response['msg'] = "Sesión Iniciada Correctamente";

                    return response()->json($response, 200);
                }
            }
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 406);
        }
    }

    /**
     * Información del Usuario
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function infouser(Request $request)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        try {
            $response['status'] = 1;
            $response['data'] = $request->user();
            $response['msg'] = "Datos del Usuario";

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
            $response['status'] = 0;

            return response()->json($response, 406);
        }
    }

    /**
     * Recuperar Contraseña
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function recoverPass(Request $request)
    {
        $response = ["status" => 1, "msg" => ""];

        $pass_pattern = "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/";

        $user = User::where('email', $request->email)->first();

        try {
            if ($user) {
                do {
                    $password = Str::random(8);
                } while (!preg_match($pass_pattern, $password)); //hacer para que se envie por correo??
                $user->password = Hash::make($password);
                $user->save();

                $response['status'] = 1;
                $response['msg'] = "Contraseña: " . $password;

                return response()->json($response, 200);
            } else {
                $response['status'] = 0;
                $response['msg'] = "No se encuentra el usuario en el sistema";

                return response()->json($response, 404);
            }
        } catch (\Exception $e) {
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
            $response['status'] = 0;

            return response()->json($response, 406);
        }
    }

    /**
     * Modificar Datos del Usuario
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function modifyUser(Request $request)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        $validatedData = Validator::make(
            $request->all(),
            [
                'name' => 'bail|string|max:255|nullable',
                'email' => 'bail|string|email|max:255|unique:users|nullable',
                'genre' => 'in:Hombre,Mujer,Otro|nullable',
                'surname' => 'string|max:255|nullable',
                'image' => 'string|max:255|nullable'
            ],
            [
                'name.string' => 'El nombre debe ser un String',
                'name.max' => 'El nombre no puede superar 255 caracteres',
                'email.string' => 'El email debe ser un string',
                'email.email' => 'Introduce formato valido de email',
                'email.unique' => 'Este email ya esta registrado',
            ]
        );
        try {
            if ($validatedData->fails()) {
                $response['status'] = 0;
                $response['data']['errors'] = $validatedData->errors();
                $response['msg'] = "Ha Ocurrido un Error";

                return response()->json($response, 400);
            } else {
                $user = $request->user();

                if (isset($request->name)) {
                    $user->name = $request->name;
                }
                if (isset($request->email)) {
                    $user->email = $request->email;
                }
                if (isset($request->surname)) {
                    $user->surname = $request->surname;
                }
                if (isset($request->image)) {
                    $user->image = $request->image;
                }
                if (isset($request->genre)) {
                    $user->genre = $request->genre;
                }

                $user->save();

                $response['status'] = 1;
                $response['data'] = $user;
                $response['msg'] = "Usuario Modificado Correctamente";

                return response()->json($response, 200);
            }
        } catch (\Exception $e) {
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
            $response['status'] = 0;

            return response()->json($response, 406);
        }
    }

    /**
     * Modificar Contraseña
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function modifyPass(Request $request)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        $validatedData = Validator::make(
            $request->all(),
            [
                'password' => 'bail|required|string|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{6,}/'
            ],
            [
                'password.required' => 'Introduce una contraseña correcta debe tener minimo 8 caracteres 1 letra, una mayuscula y un caracter especial',
                'password.regex' => 'Introduce una contraseña correcta debe tener minimo 8 caracteres 1 letra, una mayuscula y un caracter especial'
            ]
        );
        try {
            if ($validatedData->fails()) {
                $response['status'] = 0;
                $response['data']['errors'] = $validatedData->errors()->all();
                $response['msg'] = "Ha Ocurrido un Error";

                return response()->json($response, 406);
            } else {
                $user = $request->user();

                if (isset($request->password)) {
                    $user->password = Hash::make($request->password);
                }
                $user->save();

                $response['status'] = 1;
                $response['data']['errors'] = "";
                $response['msg'] = "Contraseña Modificada Correctamente";

                return response()->json($response, 200);
            }
        } catch (\Exception $e) {
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
            $response['status'] = 0;

            return response()->json($response, 406);
        }
    }
}
