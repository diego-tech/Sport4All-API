<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event;
use App\Models\Favourite;
use App\Models\Service;
use Illuminate\Validation\Rule;
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
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function listClubs()
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        try {
            $query = Club::all();
            $clubs_array = [];

            foreach ($query as $clubs) {
                $ClubArray['id'] = $clubs->id;
                $ClubArray['name'] = $clubs->name;
                $ClubArray['club_img'] = $clubs->club_img;
                $ClubArray['club_banner'] = $clubs->club_banner;
                $ClubArray['direction'] = $clubs->direction;
                $ClubArray['tlf'] = $clubs->tlf;
                $ClubArray['email'] = $clubs->email;
                $ClubArray['services'] = $this->Get_services_from_club($clubs->id);
    
                $clubs_array[] = $ClubArray;
            }

            $response['status'] = 1;
            $response['data'] = $clubs_array;
            $response['msg'] = "Todos los Clubes";

            return response()->json($response, 200);
        } catch (\Exception $e) {
            $response['status'] = 0;
            $response['data'] = "";
            $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

            return response()->json($response, 406);
        }
    }

    /**
     * Obtener servicios de los clubes
     * 
     * @param \App\Models\Club->id
     * @return $query
     */
    public function Get_services_from_club($clubId){
        $query = Service::join('clubs_services','services.id','=','clubs_services.service_id')
                        ->select('services.name')
                        ->where('clubs_services.club_id',$clubId)
                        ->get();
        return $query;
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
                    $response['data']['errors'] = "";
                    $response['msg'] = "Este club ya se encuentra añadido a favoritos";

                    return response()->json($response, 406);
                } else {
                    // Añado el club a favoritos
                    $clubFav = new Favourite();
                    $clubFav->user_id = $userId;
                    $clubFav->club_id = $idClub;
                    $clubFav->save();

                    $response['status'] = 1;
                    $response['data']['errors'] = "";
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

    /**
     * Registro de Eventos
     * 
     * @param \Illuminate\Http\Request $request
     * @return response()->json($response)
     */
    public function registerEvent(Request $request)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        // Validación de los campos
        $validatedData = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'visibility' => ['required',Rule::in(['Publico','Privado','Oculto'])],
                'people_left' => 'required|integer|min:0',
                'type' => 'required|string|max:255',
                'price' => 'required|min:0',
                'club_id' => 'required|exists:clubs,id',
                'day' => 'required|date_format:Y-m-d',
                'start_time' => 'required|date_format:H:i:s',
                'end_time' => 'required|date_format:H:i:s',

            ],
            [
                'name.required' => 'Introduzca un nombre para el evento',
                'name.string' => 'El nombre debe ser un String',
                'name.max' => 'El nombre no puede superar 255 caracteres',
                'visibility.required' => 'Introduzca un estado de visibilidad',
                'people_left.required' => 'Introduzca una cantidad de personas',
                'people_left.integer' => 'La cantidad de personas debe ser un Integer',
                'people_left.min' => 'La cantidad de personas no puede ser inferior a 0',
                'type.required' => 'Introduzca el tipo de evento',
                'type.string' => 'El tipo debe ser un String',
                'type.max' => 'El tipo no puede superar 255 caracteres',
                'price.required' => 'Introduzca el precio',
                'price.min' => 'El precio no puede ser inferior a 0',
                'start_time.required' => 'Introduce fecha de inicio del partido',
                'start_time.date_format' => 'Introduce el formato de la fecha de esta manera: H:i:s',
                'end_time.required' => 'Introduce fecha a la que acaba el partido',
                'end_time.date_format' => 'Introduce el formato de la fecha de esta manera: H:i:s',
            ]
        );

        if ($validatedData->fails()) {
            $response['status'] = 0;
            $response['data']['errors'] = $validatedData->errors();
            $response['msg'] = "Datos incorrectos, evento no registrado";

            return response()->json($response, 406);
        } else {
            try {
                $final_time = $request->input('day') . " ". $request->input('end_time');
                $event = Event::create([
                    'name' => $request->input('name'),
                    'visibility' => $request->input('visibility'),
                    'people_left' => $request->input('people_left'),
                    'type' => $request->input('type'),
                    'price' => $request->input('price'),
                    'club_id' => $request->input('club_id'),
                    'day' => $request->input('day'),
                    'start_time' => $request->input('start_time'),
                    'end_time' => $request->input('end_time'),
                    'final_time' => $final_time,
                ]);

                $response['status'] = 1;
                $response['data'] = $event;
                $response['msg'] = 'Evento registrado correctamente';

                return response()->json($response, 200);
            } catch (\Exception $e) {
                $response['status'] = 0;
                $response['msg'] = (env('APP_DEBUG') == "true" ? $e->getMessage() : $this->error);

                return response()->json($response, 406);
            }
        }
    }

}
