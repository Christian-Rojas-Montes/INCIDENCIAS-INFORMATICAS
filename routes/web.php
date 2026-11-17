<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\PrioridadController;
use App\Http\Controllers\SeguimientoController;
use App\Http\Controllers\TecnicoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');
Route::resource('usuarios', UsuarioController::class);
Route::resource('tecnicos', TecnicoController::class);
Route::resource('prioridades', PrioridadController::class)->parameters(['prioridades' => 'prioridad']);
Route::resource('incidencias', IncidenciaController::class);
Route::resource('seguimientos', SeguimientoController::class);
