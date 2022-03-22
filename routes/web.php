<?php

use App\Http\Controllers\ClubsController;
use App\Http\Controllers\Web\EmailVerificationController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ClubsController::class, 'showListClubsWeb'])->name('index');


// Verificación de email
Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])->name('verification.verify');

// Recuperar Contraseña
Route::get('reset-password/{token}', function ($token) {
    return view('indexPassword', ['token' => $token]);
})->name('password.reset');

Route::post('/resetPassword', function (Request $request) {
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
})->name('password.update');
