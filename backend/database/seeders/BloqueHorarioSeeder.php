<?php

namespace Database\Seeders;

use App\Models\BloqueHorario;
use Illuminate\Database\Seeder;

class BloqueHorarioSeeder extends Seeder
{
    public function run(): void
    {
        $bloques = [
            ['orden' => 1, 'hora_inicio' => '19:00:00', 'hora_fin' => '19:40:00'],
            ['orden' => 2, 'hora_inicio' => '19:40:00', 'hora_fin' => '20:20:00'],
            ['orden' => 3, 'hora_inicio' => '20:25:00', 'hora_fin' => '21:05:00'],
            ['orden' => 4, 'hora_inicio' => '21:05:00', 'hora_fin' => '21:45:00'],
            ['orden' => 5, 'hora_inicio' => '21:50:00', 'hora_fin' => '22:30:00'],
            ['orden' => 6, 'hora_inicio' => '22:30:00', 'hora_fin' => '23:10:00'],
        ];

        foreach ($bloques as $bloque) {
            BloqueHorario::updateOrCreate(
                ['orden' => $bloque['orden']],
                [
                    'hora_inicio' => $bloque['hora_inicio'],
                    'hora_fin' => $bloque['hora_fin'],
                ]
            );
        }
    }
}
