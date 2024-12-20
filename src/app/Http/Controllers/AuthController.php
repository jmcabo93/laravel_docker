<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    
    public function login(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        // Si el usuario no existe o la contraseña es incorrecta, devolver un error en formato JSON
        if (!$user || !Hash::check($validated['password'], $user->password)) {
           return response()->json([
             'message' => 'Credenciales incorrectas.'
           ], 401); // Código de estado HTTP 401 para "No autorizado"
        }

        // Create the token
        $token = $user->createToken('YourAppName')->plainTextToken;

        // Return the token to the user
        return response()->json([
            'message' => 'Autenticado exitosamente.',
            'token' => $token,
        ]);
    }

    // Logout Method
    public function logout(Request $request)
    {
        // Get the currently authenticated user
        $user = $request->user();

        // Revoke all tokens for the user
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        // Return a response
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}

