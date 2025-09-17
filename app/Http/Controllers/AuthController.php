<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    // tao token de thuc hien cac chuc nang khac nhu getAll
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active'
        ]);

         $token = $user->createToken('api-token')->plainTextToken;

         return response()->json([
            'success' => true,
            'message' => 'User resgister successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }
    public function login(Request $request)
    {
        try{
          $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }


        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
        }catch(Exception $e){
            dd($e);
            return response()->json([
                'success' => true,
                'message' => 'cant not login',
                'error' => $e->getMessage(),
            ]);
        }
    }
    public function logout(Request $request)
    {
        $request()->user()->tokens()->delete();

        return response()->json([
            'succes' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => $request->user(),
        ]);
    }
}
