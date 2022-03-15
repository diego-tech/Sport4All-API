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
    //Rutas del Login, registro, y perfil
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('verifiedEmail');
Route::get('/userinfo', [AuthController::class, 'infouser'])->middleware('auth:sanctum');
Route::post('/recoverpass', [AuthController::class, 'recoverPass']);
Route::post('/usermodify', [AuthController::class, 'modifyUser'])->middleware('auth:sanctum');
Route::post('/passmodify', [AuthController::class, 'modifyPass'])->middleware('auth:sanctum');
Route::post('/getUploadImage', [AuthController::class, 'getUploadImage']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    //Rutas de eventos
Route::get('/listevents', [AuthController::class, 'listevents'])->middleware('auth:sanctum'); 
Route::get('/pendingevents',[AuthController::class, 'pending_events'])->middleware('auth:sanctum');
Route::get('/listfavouritegevents',[AuthController::class, 'list_events_by_favourites'])->middleware('auth:sanctum');
Route::post('/joinevent', [AuthController::class, 'joinEvent'])->middleware('auth:sanctum');
Route::get('/endedevents',[AuthController::class, 'ended_events'])->middleware('auth:sanctum');

    //Rutas favoritos
Route::get('/listfavs', [AuthController::class, 'listfavs'])->middleware('auth:sanctum');
Route::delete('/deletefav',[AuthController::class, 'delete_favs'])->middleware('auth:sanctum');
Route::post('/registerfavclub', [ClubsController::class, 'registerFavClub'])->middleware('auth:sanctum');// El user tendrá que estar logeado para poder añadir clubes favoritos


    //Reservas y partidos
Route::post('/matchinscription',[MatchController::class, 'matchInscription'])->middleware('auth:sanctum');
Route::post('/courtreserve',[CourtsController::class, 'CourtReserve'])->middleware('auth:sanctum');
Route::get('/endedmatches',[MatchController::class, 'ended_matches'])->middleware('auth:sanctum');
Route::get('/seematches',[MatchController::class, 'seeMatches'])->middleware('auth:sanctum');



// Rutas Clubes
Route::post('/registerclub', [ClubsController::class, 'register']);
Route::get('/searchclubs', [AuthController::class, 'searchClubs'])->middleware('auth:sanctum');
Route::get('/listclubs', [ClubsController::class, 'listClubs'])->middleware('auth:sanctum');
Route::post('/registerevent', [ClubsController::class, 'registerEvent']); // Tendrá que pasar por un middleware para poder registrarlos
Route::post('/registercourt', [CourtsController::class, 'CourtRegister']); //Futuro cambiar a tener que estar logueado como club
Route::post('/creatematch',[MatchController::class, 'createMatch']); //Futuro cambiar a tener que estar logueado como club
Route::get('/mostrated', [ClubsController::class, 'most_rated_clubs'])->middleware('auth:sanctum');

//Lista pistas libres

Route::get('/freecourts',[CourtsController::class, 'freeCourts'])->middleware('auth:sanctum');
