<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Favourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClubsController extends Controller
{
    /**
     * Registro de Clubes
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function register(Request $request)
    {
        // Validación de los campos
        $validatedData = Validator::make(
            $request->all(),
            [
                'name' => 'bail|required|string|max:255',
                'club_img' => 'string|max:255|nullable',
                'club_banner' => 'string|max:255|nullable',
                'direction' => 'required|string|max:255',
                'tlf' => 'required|string|regex:/[0-9]{9}/',
                'email' => 'bail|required|string|email|max:255|unique:clubs'
            ],
            [
                'name.required' => 'Introduzca un nombre para el club',
                'name.string' => 'El nombre debe ser un String',
                'name.max' => 'El nombre no puede superar 255 caracteres',
                'direction.required' => 'Intruduzca una dirección para el club',
                'direction.string' => 'La dirección debe ser un String',
                'direction.max' => 'La dirección no puede superar 255 caracteres',
                'tlf.required' => 'Introduzca un teléfono para el club',
                'tlf.regex' => 'El teléfono debe contener 9 números',
                'email.required' => 'Introduce un email correcto',
                'email.string' => 'El email debe ser un string',
                'email.email' => 'Introduce formato válido de email',
                'email.unique' => 'Este email ya está registrado'
            ]
        );

        if ($validatedData->fails()) {
            // Comprobación si hay algún dato incorrecto
            return $this->sendError('Datos incorrectos, club no registrado', $validatedData->errors()->all(), 406);
        } else {
            // Si los campos son correctos se crea el club
            try {
                $club = Club::create([
                    'name' => $request->input('name'),
                    'club_img' => $request->input('club_img'),
                    'club_banner' => $request->input('club_banner'),
                    'direction' => $request->input('direction'),
                    'tlf' => $request->input('tlf'),
                    'email' => $request->input('email')
                ]);

                $token = $club->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'access_Token' => $token,
                    'token_type' => 'Bearer'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error al registrar el club',
                    'error' => $e
                ], 401);
            }
        }
    }

    /**
     * Listado Completo de Clubes
     * 
     * @param 
     * @return response()->json($response)
     */
    public function listClubs()
    {
        $clubs = Club::all();
        return $clubs;
    }

    /**
     * Registrar Clubes como Favoritos
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function registerFavClub(Request $request)
    {
        $answer = ['status' => 1, 'msg' => []];

        // Token del usuario que va a realizar el registro de favorito
        $user = $request->user();

        $club = $request->getContent();
        $club = json_decode($club);

        // Validación de los campos
        $validatedData = Validator::make(
            $request->all(),
            [
                'club_id' => 'required|exists:clubs,id'
            ],
            [
                'club_id.required' => 'Introduzca el id del club',
                'club_id.exists' => 'El club seleccionado no existe'
            ]
        );

        // Recogemos el id del club seleccionado
        $idClubPostman = $request->input('club_id');

        // Comprobación de que no esté ya añadido a favoritos
        $userId = $user->id;

        $check = DB::table('favourites')
            ->select('user_id', 'club_id')
            ->where('user_id', $userId)
            ->where('club_id', $idClubPostman)
            ->first();

        // Comprobación si hay algún dato incorrecto
        if ($validatedData->fails()) {
            $answer['msg']['error'] = $validatedData->errors()->all();
            return response()->json($answer, 406);
        } else {
            try {
                // Si la relación ya existe en la tabla de favoritos no les puedo volver a añadir
                if ($check) {
                    $answer['msg'] = "Este club ya se encuentra añadido a favoritos";
                } else {
                    // Añado el club a favoritos
                    $clubFav = new Favourite();
                    $clubFav->user_id = $userId;
                    $clubFav->club_id = $idClubPostman;
                    $clubFav->save();
                    $answer['msg'] = "El club ha sido añadido a favoritos";
                }
            } catch (\Exception $e) {
                $answer['msg'] = $e->getMessage();
                $answer['status'] = 0;
            }
        }
        
        return response()->json($answer);
    }
}
