<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{

    /**
     * Envío de correo para recuperaciónde contraseña
     * 
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function forgotPassword(Request $request)
    {

        $response = ['status' => 1, 'msg' => ''];

        $email = ($request->input('email'));

        $validatedData = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
        ]);

        

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors()->first();
            $response['msg'] = 'Email No Válido';

            return response()->json($response, 406);
        } else {
            

            try {
                $user = User::where('email', $email)->first();

                if ($user) {

                    $link = "https://www.linkrecoverpassword.com";

                    Mail::to($user->email)->send(new ResetPassword('Nueva contraseña', $user->name." ".$user->surname , $link));

                    $response['msg'] = "An email to reset your password has been sent.";
                } else {
                
                    $response['msg'] = "User not found";
                }
            } catch (\Exception $e){
                $response['status'] = 0;
                $response['msg'] = "Se ha producido un error al enviar la contraseña. ".$e->getMessage();

            } 
        }

        return response()->json($response);

    }

    /**
     * Muestra la vista de creación de nueva contraseña
     * 
     * return view()
     */
    public function showViewPass () {

        return view('indexPassword');

    }

    
    public function webModifyPass(Request $req) {

        $response = ["status" => 1, "data" => [], "msg" => ""];

        // $this->user;

        $password = ($req->input('password'));
        $confirmPassword = ($req->input('confirmPassword'));

        $validatedData = Validator::make(
            $req->all(),
            [
                'password' => 'bail|required|string|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{6,}/',
                'confirmPassword' => 'bail|required|string|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{6,}/',
            ]);

            if ($validatedData->fails()) {
                $response['status'] = 0;
                $response['data']['errors'] = $validatedData->errors()->first();
                $response['msg'] = 'Contraseña No Válida';
    
                return response()->json($response, 406);
            } else {
                try {

                    if($password != $confirmPassword) {

                        $response['status'] = 0;
                        $response['data']['errors'] = $validatedData->errors()->first();
                        $response['msg'] = 'Las contraseñas no coinciden';

                    } else {

                        // Modificar la contraseña en la base de datos

                        $password = $confirmPassword;
                    }


                } catch (\Exception $e) {
                    $response['status'] = 0;
                    $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
    
                    return response()->json($response, 406);
                }
            }

            if ($response['status'] == 1) {
                return view('succesPassword');
            } else {
                return view('indexPassword', ["password" => $password, "confirmPassword" => $confirmPassword]);
            }
    }

}
