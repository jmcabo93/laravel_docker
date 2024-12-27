<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
           return response()->json([
             'message' => 'Credenciales incorrectas.'
           ], 401);
        }

        $token = $user->createToken('YourAppName')->plainTextToken;

        return response()->json([
            'message' => 'Autenticado exitosamente.',
            'token' => $token,
        ]);
    }

    
    public function logout(Request $request)
    {
        $user = $request->user();

        $user->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}


