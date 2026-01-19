<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function loginMovil(Request $request)
    {
        // 1. Validamos que lleguen los datos
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Intentamos el login
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // 3. Generamos el Token de Sanctum
            $token = $user->createToken('android_token')->plainTextToken;

            // 4. Respondemos a Android
            return response()->json([
                'success' => true,
                'message' => 'Autenticación exitosa',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role // Asegúrate de que tu tabla users tenga esta columna
                ]
            ], 200);
        }

        // 5. Si falla el login
        return response()->json([
            'success' => false,
            'message' => 'Correo o contraseña incorrectos',
            'token' => null
        ], 401);
    }
}