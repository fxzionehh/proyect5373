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



     
       
    }
}