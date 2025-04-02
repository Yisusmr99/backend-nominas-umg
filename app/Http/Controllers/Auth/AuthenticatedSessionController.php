<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $authResponse = $request->authenticate();
        $responseData = json_decode($authResponse->getContent(), true);
        
        if (!$responseData['result']) {
            return $authResponse;
        }
        
        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return ApiResponse::success([
            'user' => $user,
            'token' => $token
        ], 'Inicio de sesión exitoso');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        try {
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
                
                return ApiResponse::success(
                    ['tokens_remaining' => $request->user()->tokens()->count()],
                    'Sesión cerrada exitosamente'
                );
            }

            return ApiResponse::error(
                null,
                'No se encontró una sesión activa',
                401
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                null,
                'Error durante el cierre de sesión: ' . $e->getMessage(),
                500
            );
        }
    }
}
