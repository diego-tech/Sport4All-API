<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/userinfo', [AuthController::class, 'infouser'])->middleware('auth:sanctum');
Route::get('/recoverpass', [AuthController::class, 'recoverPass']);
Route::post('/usermodify', [AuthController::class, 'modifyUser'])->middleware('auth:sanctum');
<<<<<<< HEAD

// CLUBS
Route::post('/registerclub', [ClubsController::class, 'register']);
Route::get('/listclubs', [ClubsController::class, 'listClubs'])->middleware('auth:sanctum');
Route::post('/registerfavclub', [ClubsController::class, 'registerFavClub'])->middleware('auth:sanctum'); // El user tendrá que estar logeado para poder añadir clubes favoritos
=======
Route::post('/passmodify', [AuthController::class, 'modifyPass'])->middleware('auth:sanctum');
>>>>>>> Login+getuser
