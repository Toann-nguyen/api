<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$soles): Response
    {
        //authentication kiem tra xem user da login chua
        if(!auth()->check()){
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }
        // authorization lay thong tin cua nguoi dung
        $user = auth()->user();
        if(!in_array($user->role,  $soles)){
            return response()->json([
                'message' => 'ban chua co perrmission',
            ]);
        }
        return $next($request);
    }
}
