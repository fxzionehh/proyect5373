<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'name' => 'Barista',
            'email' => 'barista@gmail.com',
            'password' => Hash::make('12345678'),
            'role_id' => $baristaRole->id,
        ]);

        for ($i = 1; $i <= 5; $i++) {
            Mesa::create([
                'numero' => $i,
                'estado' => 'libre',
                'activa' => 1,
                'qr_token' => Str::random(32),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Productos
        |--------------------------------------------------------------------------
        */

        $cafe = Producto::create([
            'nombre' => 'Café',
            'precio' => 1500,
            'stock' => 20,
        ]);

        $capuccino = Producto::create([
            'nombre' => 'Capuccino',
            'precio' => 2500,
            'stock' => 15,
        ]);

        $latte = Producto::create([
            'nombre' => 'Latte',
            'precio' => 3000,
            'stock' => 15,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Insumos (Actualizados a capacidades reales)
        |--------------------------------------------------------------------------
        */

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

        $cafeMolido = Insumo::create([
            'nombre' => 'Café molido',
            'stock' => 50,
            'unidad' => 'porción',
        ]);

        $leche = Insumo::create([
            'nombre' => 'Leche',
            'stock' => 40,
            'unidad' => 'porción',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Preparaciones / Recetas (Sincronizadas con Frontend y Controlador)
        |--------------------------------------------------------------------------
        */

        // ==================== CAFÉ ====================
        // Tamaño Nano
        ProductoInsumo::create([
            'producto_id' => $cafe->id,
            'insumo_id' => $vasoNano->id,
            'tamano' => 'nano',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $cafe->id,
            'insumo_id' => $cafeMolido->id,
            'tamano' => 'nano',
            'cantidad' => 1,
        ]);

        // Tamaño Mini
        ProductoInsumo::create([
            'producto_id' => $cafe->id,
            'insumo_id' => $vasoMini->id,
            'tamano' => 'mini',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $cafe->id,
            'insumo_id' => $cafeMolido->id,
            'tamano' => 'mini',
            'cantidad' => 1,
        ]);

        // Tamaño Normal
        ProductoInsumo::create([
            'producto_id' => $cafe->id,
            'insumo_id' => $vasoNormal->id,
            'tamano' => 'normal',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $cafe->id,
            'insumo_id' => $cafeMolido->id,
            'tamano' => 'normal',
            'cantidad' => 1,
        ]);

        // Tamaño Max
        ProductoInsumo::create([
            'producto_id' => $cafe->id,
            'insumo_id' => $vasoMax->id,
            'tamano' => 'max',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $cafe->id,
            'insumo_id' => $cafeMolido->id,
            'tamano' => 'max',
            'cantidad' => 2, // Lleva doble porción de café por el tamaño
        ]);


        // ==================== CAPUCCINO ====================
        // Tamaño Nano
        ProductoInsumo::create([
            'producto_id' => $capuccino->id,
            'insumo_id' => $vasoNano->id,
            'tamano' => 'nano',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $capuccino->id,
            'insumo_id' => $cafeMolido->id,
            'tamano' => 'nano',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $capuccino->id,
            'insumo_id' => $leche->id,
            'tamano' => 'nano',
            'cantidad' => 1,
        ]);

        // Tamaño Mini
        ProductoInsumo::create([
            'producto_id' => $capuccino->id,
            'insumo_id' => $vasoMini->id,
            'tamano' => 'mini',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $capuccino->id,
            'insumo_id' => $cafeMolido->id,
            'tamano' => 'mini',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $capuccino->id,
            'insumo_id' => $leche->id,
            'tamano' => 'mini',
            'cantidad' => 1,
        ]);

        // Tamaño Normal
        ProductoInsumo::create([
            'producto_id' => $capuccino->id,
            'insumo_id' => $vasoNormal->id,
            'tamano' => 'normal',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $capuccino->id,
            'insumo_id' => $cafeMolido->id,
            'tamano' => 'normal',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $capuccino->id,
            'insumo_id' => $leche->id,
            'tamano' => 'normal',
            'cantidad' => 1,
        ]);


        // ==================== LATTE ====================
        // Tamaño Nano
        ProductoInsumo::create([
            'producto_id' => $latte->id,
            'insumo_id' => $vasoNano->id,
            'tamano' => 'nano',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $latte->id,
            'insumo_id' => $cafeMolido->id,
            'tamano' => 'nano',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $latte->id,
            'insumo_id' => $leche->id,
            'tamano' => 'nano',
            'cantidad' => 1,
        ]);

        // Tamaño Mini
        ProductoInsumo::create([
            'producto_id' => $latte->id,
            'insumo_id' => $vasoMini->id,
            'tamano' => 'mini',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $latte->id,
            'insumo_id' => $cafeMolido->id,
            'tamano' => 'mini',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $latte->id,
            'insumo_id' => $leche->id,
            'tamano' => 'mini',
            'cantidad' => 1,
        ]);

        // Tamaño Normal
        ProductoInsumo::create([
            'producto_id' => $latte->id,
            'insumo_id' => $vasoNormal->id,
            'tamano' => 'normal',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $latte->id,
            'insumo_id' => $cafeMolido->id,
            'tamano' => 'normal',
            'cantidad' => 1,
        ]);
        ProductoInsumo::create([
            'producto_id' => $latte->id,
            'insumo_id' => $leche->id,
            'tamano' => 'normal',
            'cantidad' => 1,
        ]);
    }
}