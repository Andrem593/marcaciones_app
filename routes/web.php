<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canResetPassword' => Route::has('password.request'),
        'status' => session('status'),
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/Reporte/detalle/{empleado}/{fecha}', function (Application $app, $empleado, $fecha) {        
        return Inertia::render('Reports/detalle', [
            'empleado' => $empleado,
            'fecha' => $fecha,
        ]);
    })->name('reporte.detalle');
});
