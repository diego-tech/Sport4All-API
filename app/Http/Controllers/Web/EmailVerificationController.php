<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

    public function webModifyPass(Request $request) {

        // Route::match(['get', 'post'],'/indexPassword', function (Request $req) {

        //     $password = ($req->input('password'));
        //     $confirmPassword = ($req->input('confirmPassword'));

        //     print($password);
        //     print($confirmPassword);
        
        //     return view('indexPassword', ["indexPassword" => $password->format('bail|required|string|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{6,}/')]);
        // }) -> name("indexPassword");

        return view('indexPassword');
    }
}
