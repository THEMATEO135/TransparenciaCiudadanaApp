<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OperadoresSeeder extends Seeder
{
    public function run(): void
    {
        $operadores = [
            [
                'name' => 'Operador 1',
                'email' => 'operador1@transparencia.com',
                'password' => Hash::make('password123'),
                'role' => 'operador',
                'is_active' => true,
            ],
            [
                'name' => 'Operador 2',
                'email' => 'operador2@transparencia.com',
                'password' => Hash::make('password123'),
                'role' => 'operador',
                'is_active' => true,
            ],
            [
                'name' => 'Supervisor Principal',
                'email' => 'supervisor@transparencia.com',
                'password' => Hash::make('password123'),
                'role' => 'supervisor',
                'is_active' => true,
            ],
        ];

        foreach ($operadores as $operador) {
            User::firstOrCreate(
                ['email' => $operador['email']],
                $operador
            );
        }

        $this->command->info('✅ Operadores creados exitosamente.');
        $this->command->warn('⚠️  Contraseña por defecto: password123');
    }
}
