<?php

use App\Http\Controllers\ClubsController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\Web\EmailVerificationController;
use Illuminate\Support\Facades\Route;

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

// Recuperar contraseña
Route::get('/showView', [ResetPasswordController::class, 'showViewPass'])->name('showView');
Route::match(['get', 'post'], '/modifyPassword', [ResetPasswordController::class, 'webModifyPass'])->name('indexPassword');
Route::get('/succesPassword', [ResetPasswordController::class, 'succesPassword'])->name('succesPassword');