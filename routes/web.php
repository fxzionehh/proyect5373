<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\PedidosController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\InsumoController;
use App\Http\Controllers\Barista\PrepararPedidoController;
use App\Http\Controllers\Barista\MesaController;
use App\Http\Controllers\Client\PedidoController;
use App\Http\Controllers\Admin\UsuarioController;
//Route::get('/', [IndexController::class, 'index'])->name('inicio');
use App\Http\Controllers\ForgotPasswordController;


Route::get('/', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy']);


Route::middleware(['auth'])->group(function () {

     Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');

    Route::get('/dashboard/roles', [RoleController::class, 'index'])
        ->middleware('permission:ver roles');    

    Route::get('/roles/edit/{id}', [RoleController::class, 'edit'])
        ->middleware('permission:editar rol');

    Route::post('/dashboard/roles/store', [RoleController::class, 'store'])
        ->middleware('permission:actualizar rol');

    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:eliminar rol');

    Route::get('/dashboard/usuarios', [UsuarioController::class, 'index']);

    Route::get('/usuarios/edit/{id}', [UsuarioController::class, 'edit']);

    Route::post('/dashboard/usuarios', [UsuarioController::class, 'store']);

    Route::delete('/dashboard/usuarios/{usuario}', [UsuarioController::class, 'destroy']);

    Route::get('/dashboard/productos', [ProductoController::class, 'index'])
        ->middleware('permission:ver producto');

    Route::get('/productos/edit/{id}', [ProductoController::class, 'edit'])
        ->middleware('permission:editar producto');

    Route::post('/dashboard/productos', [ProductoController::class, 'store'])
        ->middleware('permission:actualizar producto');

    Route::delete('/dashboard/productos/{producto}', [ProductoController::class, 'destroy'])
        ->middleware('permission:eliminar producto');

    Route::get('/dashboard/insumos', [InsumoController::class, 'index'])
        ->middleware('permission:ver insumo');

    Route::get('/dashboard/insumos/edit/{id}', [InsumoController::class, 'edit'])
        ->middleware('permission:editar insumo');

    Route::post('/dashboard/insumos', [InsumoController::class, 'store'])
        ->middleware('permission:actualizar insumo');

    Route::delete('/dashboard/insumos/{insumo}', [InsumoController::class, 'destroy'])
        ->middleware('permission:eliminar insumo');

    Route::get('/dashboard/pedidos', [PedidosController::class, 'index'])
        ->middleware('permission:ver pedido');

    Route::get('/dashboard/pedidos/edit/{id}', [PedidosController::class, 'edit'])
        ->middleware('permission:editar pedido');

    Route::post('/dashboard/pedidos', [PedidosController::class, 'store'])
        ->middleware('permission:actualizar pedido');

    Route::delete('/dashboard/pedidos/{pedido}', [PedidosController::class, 'destroy'])
        ->middleware('permission:eliminar pedido');

    Route::get('/dashboard/mesas', [MesaController::class, 'index'])
        ->middleware('permission:ver mesa');

    Route::get('/dashboard/mesas/edit/{id}', [MesaController::class, 'edit'])
        ->middleware('permission:editar mesa');
        
    Route::post('/dashboard/mesas', [MesaController::class, 'store'])
        ->middleware('permission:actualizar mesa');

    Route::delete('/dashboard/mesas/{mesa}', [MesaController::class, 'destroy'])
        ->middleware('permission:eliminar mesa');


    Route::get('/dashboard/reportes', [ReporteController::class, 'index'])
        ->middleware('permission:ver reporte');

    Route::get('/dashboard/preparacion', [PrepararPedidoController::class, 'index'])
        ->middleware('permission:ver preparacion');

    Route::post('/dashboard/preparacion/{pedido}/estado', [PrepararPedidoController::class, 'store'])
        ->middleware('permission:editar preparacion');
});

Route::post('/pedidos', [PedidoController::class, 'store'])
    ->name('pedidos.store');

Route::get('/mesa/{mesa}', [MesaController::class, 'show'])
    ->name('mesas.show');

Route::get('/pedidos/{pedido}', [PedidoController::class, 'show']);