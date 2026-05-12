<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Client\CatalogoController;
use App\Http\Controllers\Client\PedidoController;


Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');

    Route::get('/dashboard/productos', [ProductoController::class, 'index'])
        ->name('dashboard.productos.index');

    Route::post('/dashboard/productos', [ProductoController::class, 'store'])
        ->name('dashboard.productos.store');

    Route::put('/dashboard/productos/{producto}', [ProductoController::class, 'update'])
        ->name('dashboard.productos.update');

    Route::delete('/dashboard/productos/{producto}', [ProductoController::class, 'destroy'])
        ->name('dashboard.productos.destroy');
});

Route::get('/', [CatalogoController::class, 'index'])
    ->name('inicio');

Route::get('/mesa/{token}', [CatalogoController::class, 'mesa'])
    ->name('catalogo.mesa');

Route::post('/pedidos', [PedidoController::class, 'store'])
    ->name('pedidos.store');
    
Route::get('/', [CatalogoController::class, 'index'])->name('inicio');