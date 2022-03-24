<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
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
        $response = ["status" => 1, "msg" => ""];

        $userEmail = $request->only('email');
        $user = User::where('email', $userEmail)->first();

        if ($user->hasVerifiedEmail()) {
            return $next($request);
        } else {
            $response['status'] = 0;
            $response['msg'] = "Correo Electrónico no Validado";

            return response()->json($response, 406);
        }
    }
}
