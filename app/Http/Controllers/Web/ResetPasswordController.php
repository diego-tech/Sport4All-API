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
    public function resetPasswordView($token)
    {
        return view('indexPassword', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}/',
        ], [
            'email.exists' => 'Este usuario no existe',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.regex' => 'Introduce una contraseña correcta debe tener minimo 8 caracteres 1 letra, una mayuscula y un caracter especial'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? view('succesPassword')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
