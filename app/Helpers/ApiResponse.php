<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, string $message = 'Operación exitosa', int $code = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'result' => true,
            'message' => $message
        ], $code);
    }

    public static function error($data = null, string $message = 'Error en la operación', int $code = 400): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'result' => false,
            'message' => $message
        ], $code);
    }
}
