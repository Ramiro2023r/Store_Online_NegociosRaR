<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador RaR',
            'email' => 'admin@negociosrar.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '999999999',
        ]);

        User::create([
            'name' => 'Trabajador Demo',
            'email' => 'trabajador@negociosrar.com',
            'password' => Hash::make('trabajador123'),
            'role' => 'trabajador',
            'phone' => '988888888',
        ]);

        User::create([
            'name' => 'Cliente Demo',
            'email' => 'cliente@negociosrar.com',
            'password' => Hash::make('cliente123'),
            'role' => 'cliente',
            'phone' => '977777777',
        ]);
    }
}
