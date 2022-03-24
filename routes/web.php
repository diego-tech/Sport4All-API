<?php

use App\Http\Controllers\ClubsController;
use App\Http\Controllers\Web\EmailVerificationController;
use App\Http\Controllers\Web\ResetPasswordController;
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
Route::get('/succesEmail', function () {
    return view('succesEmail');
});

// Verificación de email
Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])->name('verification.verify');

// Recuperar Contraseña
Route::get('reset-password/{token}', [ResetPasswordController::class, 'resetPasswordView'])->name('password.reset');

Route::post('/resetPassword', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
