<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// importar las rutas del archivo auth.php
require __DIR__ . '/auth.php';

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('healthcheck', function () {
    return response()->json(['message' => 'API is running']);
});