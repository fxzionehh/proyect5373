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
use App\Http\Controllers\Admin\InsumoController;


Route::get('/', [MenuController::class, 'index'])->name('inicio');
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy']);



Route::middleware(['auth', 'role:administrador'])->group(function () {

Route::get('/mesa/{mesa}', [MesaController::class, 'show'])
    ->name('mesas.show');
    
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

    Route::post('/dashboard/pedidos', [PedidosController::class, 'store'])
        ->name('dashboard.pedidos.store');

    Route::put('/dashboard/pedidos/{pedido}', [PedidosController::class, 'update'])
        ->name('dashboard.pedidos.update');

    Route::delete('/dashboard/pedidos/{pedido}', [PedidosController::class, 'destroy'])
        ->name('dashboard.pedidos.destroy');
    Route::get('/dashboard/reportes', [ReporteController::class, 'index'])
    ->name('reportes.index');

    Route::get('/dashboard/insumos', [InsumoController::class, 'index'])
        ->name('dashboard.insumos.index');

    Route::post('/dashboard/insumos', [InsumoController::class, 'store'])
        ->name('dashboard.insumos.store');

    Route::put('/dashboard/insumos/{insumo}', [InsumoController::class, 'update'])
        ->name('dashboard.insumos.update');

    Route::delete('/dashboard/insumos/{insumo}', [InsumoController::class, 'destroy'])
        ->name('dashboard.insumos.destroy');
Route::get('/dashboard/mesas/{mesa}/edit', [MesaController::class, 'edit'])->name('dashboard.mesas.edit');
    
    // ... tus otras rutas ...
    Route::get('/dashboard/mesas', [MesaController::class, 'index'])->name('dashboard.mesas.index');
    Route::post('/dashboard/mesas', [MesaController::class, 'store'])->name('dashboard.mesas.store');
    Route::delete('/dashboard/mesas/{mesa}', [MesaController::class, 'destroy'])->name('dashboard.mesas.destroy');
    });

    Route::middleware(['auth', 'role:barista'])->group(function () {

        Route::get('/barista/pedidos', [PrepararPedidoController::class, 'index'])
            ->name('barista.index');

        Route::post('/barista/pedidos/{pedido}/estado', [PrepararPedidoController::class, 'store'])
            ->name('barista.pedidos.store');

       

    });


    Route::middleware(['auth', 'role:cliente'])->group(function () {
// routes/web.php

// La ruta ahora es intuitiva: GET /mesa/{mesa}

    });