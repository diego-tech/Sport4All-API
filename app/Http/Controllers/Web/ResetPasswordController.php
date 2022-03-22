<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{

    /**
     * Muestra la vista de creación de nueva contraseña
     * 
     * return view()
     */
    public function showViewPass()
    {
        return view('indexPassword');
    }
    
    public function resetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed']
        ]);

        dd($request->all());
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response([
                'message' => 'Password Reset Succesfully'
            ]);
        }

        return response([
            'message' => __($status)
        ], 500);
    }


    // public function webModifyPass(Request $req)
    // {
    //     $response = ["status" => 1, "data" => [], "msg" => ""];

    //     // $this->user;

    //     $password = ($req->input('password'));
    //     $confirmPassword = ($req->input('confirmPassword'));

    //     $validatedData = Validator::make(
    //         $req->all(),
    //         [
    //             'password' => 'bail|required|string|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{6,}/',
    //             'confirmPassword' => 'bail|required|string|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{6,}/',
    //         ]
    //     );

    //     if ($validatedData->fails()) {
    //         $response['status'] = 0;
    //         $response['data']['errors'] = $validatedData->errors()->first();
    //         $response['msg'] = 'Contraseña No Válida';

    //         return response()->json($response, 406);
    //     } else {
    //         try {

    //             if ($password != $confirmPassword) {

    //                 $response['status'] = 0;
    //                 $response['data']['errors'] = $validatedData->errors()->first();
    //                 $response['msg'] = 'Las contraseñas no coinciden';
    //             } else {

    //                 // Modificar la contraseña en la base de datos

    //                 $password = $confirmPassword;
    //             }
    //         } catch (\Exception $e) {
    //             $response['status'] = 0;
    //             $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

    //             return response()->json($response, 406);
    //         }
    //     }

    //     if ($response['status'] == 1) {
    //         return view('succesPassword');
    //     } else {
    //         return view('indexPassword', ["password" => $password, "confirmPassword" => $confirmPassword]);
    //     }
    // }
}
