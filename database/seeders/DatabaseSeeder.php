<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Role;
use App\Models\Mesa;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $adminRole = Role::create([
            'nombre' => 'administrador'
        ]);

        $baristaRole = Role::create([
            'nombre' => 'barista'
        ]);

        $clienteRole = Role::create([
            'nombre' => 'cliente'
        ]);

        // Usuarios
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role_id' => $adminRole->id,
        ]);

        User::create([
            'name' => 'Cliente',
            'email' => 'cliente@gmail.com',
            'password' => Hash::make('12345678'),
            'role_id' => $clienteRole->id,
        ]);

        // Mesas
        for ($i = 1; $i <= 5; $i++) {

            Mesa::create([
                'numero' => $i,
                'estado' => 'libre',
                'activa' => true,
                'qr_token' => Str::random(32),
            ]);
        }
    }
}