<?php

namespace App\Http\Helpers;

use App\Models\Matchs;
use App\Models\Service;

class AuxFunctions
{
    /**
     * Obtener servicios de los clubes
     * 
     * @param \App\Models\Club->id
     * @return $query
     */
    public static function Get_services_from_club($clubId)
    {
        $query = Service::join('clubs_services', 'services.id', '=', 'clubs_services.service_id')
            ->select('services.name')
            ->where('clubs_services.club_id', $clubId)
            ->get();
        return $query;
    }

    public static function get_users_from_matchs($matchId){
        $query = Matchs::join('match_user', 'matchs.id', '=','match_user.match_id')
                ->join('users','match_user.user_id','=','users.id')
                ->select('users.name','users.image')
                ->where('match_user.match_id', $matchId)
                ->get();
        return $query;
    }
}
