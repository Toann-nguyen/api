<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthSatum
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra người dùng đã đăng nhập và có role là 'satum'
        if (auth()->check() && auth()->user()->role === 'satum') {
            return $next($request);
        }
        // Kiểm tra auth trước
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Kiểm tra role 'satum'
        if (auth()->user()->role !== 'satum') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập.'
            ], 403);
        }
// Kiểm tra auth trước
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Kiểm tra role 'satum'
        if (auth()->user()->role !== 'satum') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập.'
            ], 403);
        }

        return $next($request);
        // Nếu không hợp lệ, chuyển hướng về trang login
        // return redirect('/login')->withErrors('Bạn không có quyền truy cập.');
    }
}
