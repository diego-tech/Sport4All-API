<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubsController;
use App\Http\Controllers\CourtsController;
use App\Http\Controllers\MatchController;

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

// Rutas Usuarios
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/userinfo', [AuthController::class, 'infouser'])->middleware('auth:sanctum');
Route::post('/recoverpass', [AuthController::class, 'recoverPass']);
Route::post('/usermodify', [AuthController::class, 'modifyUser'])->middleware('auth:sanctum');
Route::post('/passmodify', [AuthController::class, 'modifyPass'])->middleware('auth:sanctum');
<<<<<<< HEAD
// Diego Upload Image and User Exits
Route::post('/getUploadImage', [AuthController::class, 'getUploadImage']);
Route::post('/checkIfUserExists', [AuthController::class, 'checkIfUserExists']);
Route::get('/listevents', [AuthController::class, 'listevents']); // Tendrá que pasar por un middleware
Route::get('/listfavs', [AuthController::class, 'listfavs'])->middleware('auth:sanctum');
Route::get('/searchclubs', [AuthController::class, 'searchClubs'])->middleware('auth:sanctum');
Route::post('/joinevent', [AuthController::class, 'joinEvent'])->middleware('auth:sanctum');
=======
Route::post('/matchinscription',[MatchController::class, 'matchInscription'])->middleware('auth:sanctum');
Route::post('/courtreserve',[CourtsController::class, 'CourtReserve'])->middleware('auth:sanctum');
>>>>>>> pistas

// Rutas Clubes
Route::post('/registerclub', [ClubsController::class, 'register']);
Route::get('/listclubs', [ClubsController::class, 'listClubs'])->middleware('auth:sanctum');
Route::post('/registerfavclub', [ClubsController::class, 'registerFavClub'])->middleware('auth:sanctum'); // El user tendrá que estar logeado para poder añadir clubes favoritos
<<<<<<< HEAD
Route::post('/registercourt', [CourtsController::class, 'courtRegister']);
Route::post('/registerevent', [ClubsController::class, 'registerEvent']); // Tendrá que pasar por un middleware para poder registrarlos
=======
Route::post('/registercourt', [CourtsController::class, 'CourtRegist']); //Futuro cambiar a tener que estar logueado como club
Route::post('/creatematch',[MatchController::class, 'createMatch']); //Futuro cambiar a tener que estar logueado como club
Route::get('/seematches',[MatchController::class, 'seeMatches']);

// Cambio para merge
>>>>>>> pistas
