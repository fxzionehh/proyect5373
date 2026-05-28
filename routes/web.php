<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Client\MenuController;
use App\Http\Controllers\Admin\PedidosController;
use App\Http\Controllers\Barista\PrepararPedidoController;
use App\Http\Controllers\Barista\MesaController;
use App\Http\Controllers\Client\PedidoController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\ReporteController;

Route::get('/', [MenuController::class, 'index'])->name('inicio');
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy']);


Route::get('/mesa/{mesa}', [MenuController::class, 'mesa'])->name('mesa');


//cliente !!Corregir  a 404 para que no entren a este ruta /pedidos.
Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');





Route::middleware(['auth', 'role:administrador'])->group(function () {

    Route::get('/dashboard/roles', [RoleController::class, 'index'])
        ->name('dashboard.roles.index');

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

    Route::get('/dashboard/pedidos', [PedidosController::class, 'index'])
        ->name('dashboard.pedidos.index');

    Route::get('/dashboard/configuracion', [ConfiguracionController::class, 'index'])
        ->name('dashboard.configuracion.index');

    Route::patch('/dashboard/configuracion/mesas/{mesa}/toggle', [MesaController::class, 'toggleEstado']);
    Route::post('/dashboard/configuracion/mesas/{mesa}/generar-qr', [MesaController::class, 'generarQr']);

    Route::get('/dashboard/reportes', [ReporteController::class, 'index'])
    ->name('reportes.index');
});

Route::middleware(['auth', 'role:barista'])->group(function () {

    Route::get('/barista/pedidos', [PrepararPedidoController::class, 'index'])
        ->name('barista.index');

    Route::post('/barista/pedidos/{pedido}/estado', [PrepararPedidoController::class, 'store'])
        ->name('barista.pedidos.store');

    Route::get('/barista/mesas', [MesaController::class, 'index'])
        ->name('barista.mesas.index');

    Route::post('/barista/mesas/{mesa}/liberar', [MesaController::class, 'liberarMesa']);
});


