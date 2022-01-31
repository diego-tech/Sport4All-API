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
        $response = ["status" => 1, "data" => [], "msg" => ""];

        // Validación de los campos
        $validatedData = Validator::make(
            $request->all(),
            [
                'name' => 'bail|required|string|max:255',
                'club_img' => 'required|string|max:255',
                'club_banner' => 'required|string|max:255',
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
                'email.unique' => 'Este email ya está registrado',
                'club_img.required' => 'Introduzca una Imagen de Perfil',
                'club_banner.required' => 'Introduzca una Imagen de Banner'
            ]
        );

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors();
            $response['msg'] = "Datos Incorrectos, club no registrado";

            // Comprobación si hay algún dato incorrecto
            return response()->json($response, 406);
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

                $response['status'] = 1;
                $response['data'] = $club;
                $response['msg'] = 'Usuario Registrado Correctamente';

                return response()->json($response, 200);
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 406);
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
        $response = ["status" => 1, "data" => [], "msg" => ""];

        try {
            $clubs = Club::all();

            $response['status'] = 1;
            $response['data'] = $clubs;
            $response['msg'] = "Todos los Clubes";

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 406);
        }
    }

    /**
     * Registrar Clubes como Favoritos
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function registerFavClub(Request $request)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

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
        $idClub = $request->input('club_id');

        // Comprobación de que no esté ya añadido a favoritos
        $userId = $user->id;

        $check = DB::table('favourites')
            ->select('user_id', 'club_id')
            ->where('user_id', $userId)
            ->where('club_id', $idClub)
            ->first();

        // Comprobación si hay algún dato incorrecto
        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors();
            $response['msg'] = "Ha Ocurrido un Error";

            return response()->json($response, 406);
        } else {
            try {
                // Si la relación ya existe en la tabla de favoritos no les puedo volver a añadir
                if ($check) {
                    $response['status'] = 0;
                    $response['msg'] = "Este club ya se encuentra añadido a favoritos";

                    return response()->json($response, 406);
                } else {
                    // Añado el club a favoritos
                    $clubFav = new Favourite();
                    $clubFav->user_id = $userId;
                    $clubFav->club_id = $idClub;
                    $clubFav->save();

                    $response['status'] = 1;
                    $response['msg'] = "El club ha sido añadido a favoritos";

                    return response()->json($response, 200);
                }
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 406);
            }
        }
    }
}
