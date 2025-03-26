<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse|Response
    {
        $request->authenticate();
        
        // Obtenemos el usuario autenticado directamente de Auth
        $user = Auth::user();
        
        // Generar token para APIs
        $token = $user->createToken('api-token')->plainTextToken;

        // Retornar el token en la respuesta
        return response()->json(['token' => $token, 'user' => $user]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse|Response
    {
        try {
            // Revocar el token actual que se está usando para la solicitud
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
                
                return response()->json([
                    'message' => 'Logged out successfully',
                    'token_deleted' => true,
                    'tokens_remaining' => $request->user()->tokens()->count()
                ]);
            }

            return response()->json(['message' => 'No active session found'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error during logout: ' . $e->getMessage()], 500);
        }
    }
}
