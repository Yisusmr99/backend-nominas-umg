<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Http\Requests\User\EditRequest;
use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponse;

class UserController extends Controller
{

    public function all(): JsonResponse
    {
        try {
            $users = User::with('employee')->get();
            return ApiResponse::success(
                $users,
                'Usuarios obtenidos exitosamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                null,
                'Error al obtener usuarios: ' . $e->getMessage(),
                500
            );
        }
    }

    public function update(EditRequest $request, User $user): JsonResponse
    {
        try {
            $user->update($request->all());
            return ApiResponse::success(
                $user,
                'Usuario actualizado exitosamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                null,
                'Error al actualizar usuario: ' . $e->getMessage(),
                500
            );
        } 
    }
}
