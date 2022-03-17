<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class EmailVerificationController extends Controller
{
    /**
     * Verificación Email
     * 
     * 
     * @param \Illuminate\Http\Request $request
     * @return redirect()
     */
    public function verifyEmail(Request $request)
    {
        $user = User::find($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException();
        }

        if ($user->markEmailAsVerified())
            event(new Verified($user));

        return redirect('/');
    }

    public function webModifyPass(Request $req) {

        $response = ["status" => 1, "data" => [], "msg" => ""];

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

                        $password = "OK";

                    }


                } catch (\Exception $e) {
                    $response['status'] = 0;
                    $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);
    
                    return response()->json($response, 406);
                }

            }
    
        return view('indexPassword', ["password" => $password, "confirmPassword" => $confirmPassword]);
    }
}
