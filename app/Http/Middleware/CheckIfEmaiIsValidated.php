<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class CheckIfEmaiIsValidated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = ["status" => 1, "data" => [], "msg" => ""];

        $userEmail = $request->only('email');
        $user = User::where('email', $userEmail)->first();

        if ($user) {
            if ($user->hasVerifiedEmail()) {
                return $next($request);
            } else {
                event(new Registered($user));
    
                $response['status'] = 0;
                $response['data']['errors'] = "";
                $response['msg'] = "Valide Su Correo Electrónico";
    
                return response()->json($response, 406);
            }
        } else {
            $response['status'] = 0;
            $response['data']['errors'] = "";
            $response['msg'] = "No existe este usuario";

            return response()->json($response, 406);
        }        
    }
}
