<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarcacionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('marcaciones', [MarcacionController::class, 'index']);

Route::get('marcaciones/{empleado}/{fecha}', [MarcacionController::class, 'show']);