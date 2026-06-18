<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Permission;
use App\Models\User;
use App\Models\Role;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\ProductoInsumo;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create([
            'nombre' => 'administrador'
        ]);

        $baristaRole = Role::create([
            'nombre' => 'barista'
        ]);

        $clienteRole = Role::create([
            'nombre' => 'cliente'
        ]);

        User::create([
            'name' => 'Fabián Mendoza',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'name' => 'clienteuno',
            'email' => 'clienteuno@gmail.com',
            'password' => Hash::make('12345678'),
            'role_id' => $clienteRole->id,
        ]);

        User::create([
            'name' => 'Barista',
            'email' => 'barista@gmail.com',
            'password' => Hash::make('12345678'),
            'role_id' => $baristaRole->id,
        ]);



        $vasoNano = Insumo::create([
            'nombre' => 'Vaso Nano 120ml',
            'stock' => 30,
            'unidad' => 'unidad',
        ]);

        $vasoMini = Insumo::create([
            'nombre' => 'Vaso Mini 240ml',
            'stock' => 25,
            'unidad' => 'unidad',
        ]);

        $vasoNormal = Insumo::create([
            'nombre' => 'Vaso Normal 360ml',
            'stock' => 20,
            'unidad' => 'unidad',
        ]);

        $vasoMax = Insumo::create([
            'nombre' => 'Vaso Max 480ml',
            'stock' => 15,
            'unidad' => 'unidad',
        ]);

       $verPreparacion = Permission::create(['nombre' => 'ver preparacion']);
$editarPreparacion = Permission::create(['nombre' => 'editar preparacion']);

$verPedido = Permission::create(['nombre' => 'ver pedido']);
$crearPedido = Permission::create(['nombre' => 'crear pedido']);
$editarPedido = Permission::create(['nombre' => 'editar pedido']);
$actualizarPedido = Permission::create(['nombre' => 'actualizar pedido']);
$eliminarPedido = Permission::create(['nombre' => 'eliminar pedido']);


$verProducto = Permission::create(['nombre' => 'ver producto']);
$crearProducto = Permission::create(['nombre' => 'crear producto']);
$editarProducto = Permission::create(['nombre' => 'editar producto']);
$actualizarProducto = Permission::create(['nombre' => 'actualizar producto']);
$eliminarProducto = Permission::create(['nombre' => 'eliminar producto']);

$verInsumo = Permission::create(['nombre' => 'ver insumo']);
$crearInsumo = Permission::create(['nombre' => 'crear insumo']);
$editarInsumo = Permission::create(['nombre' => 'editar insumo']);
$actualizarInsumo = Permission::create(['nombre' => 'actualizar insumo']);
$eliminarInsumo = Permission::create(['nombre' => 'eliminar insumo']);

$verRol = Permission::create(['nombre' => 'ver rol']);
$crearRol = Permission::create(['nombre' => 'crear rol']);
$editarRol = Permission::create(['nombre' => 'editar rol']);
$actualizarRol = Permission::create(['nombre' => 'actualizar rol']);
$eliminarRol = Permission::create(['nombre' => 'eliminar rol']);

$verMesa = Permission::create(['nombre' => 'ver mesa']);
$crearMesa = Permission::create(['nombre' => 'crear mesa']);
$actualizarMesa = Permission::create(['nombre' => 'actualizar mesa']);
$eliminarMesa = Permission::create(['nombre' => 'eliminar mesa']);

$verReporte = Permission::create(['nombre' => 'ver reporte']);

$adminRole->permissions()->sync([
    $verPreparacion->id,
    $editarPreparacion->id,

    $verProducto->id,
    $crearProducto->id,
    $editarProducto->id,
    $actualizarProducto->id,
    $eliminarProducto->id,

    $verInsumo->id,
    $crearInsumo->id,
    $editarInsumo->id,
    $actualizarInsumo->id,
    $eliminarInsumo->id,

    $verRol->id,
    $crearRol->id,
    $editarRol->id,
    $actualizarRol->id,
    $eliminarRol->id,

    $verPedido->id,
    $crearPedido->id,
    $editarPedido->id,
    $actualizarPedido->id,
    $eliminarPedido->id,

    $verMesa->id,
    $crearMesa->id,
    $actualizarMesa->id,
    $eliminarMesa->id,

    $verReporte->id,

]);
$baristaRole->permissions()->sync([
    $verPreparacion->id,
]);
    }
}