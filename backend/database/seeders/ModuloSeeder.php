<?php

namespace Database\Seeders;

use App\Models\Modulo;
use Illuminate\Database\Seeder;

class ModuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modulos = [
            ['nombre' => 'CIUDADANIA', 'cursado' => 1, 'horas_total' => 30],
            ['nombre' => 'COMUNICACION,CULTURA Y AUTONOMIA', 'cursado' => 1, 'horas_total' => 30],

            ['nombre' => 'DESARROLLO SOCIO ECONOMICO DE LA REGION', 'cursado' => 2, 'horas_total' => 30],
            ['nombre' => 'AMBIENTE Y DESARROLLO SOSTENIBLE', 'cursado' => 2, 'horas_total' => 30],

            ['nombre' => 'DIVERSIDAD SOCIOCULTURAL Y DESIGUALDAD', 'cursado' => 3, 'horas_total' => 30],
            ['nombre' => 'GESTION COMUNITARIA DE PROJECTOS SOCIOSCULTURALES', 'cursado' => 3, 'horas_total' => 30],
            ['nombre' => 'DESARROLLO TERRITORIAL Y ECONOMIA SOCIAL', 'cursado' => 3, 'horas_total' => 30],
            ['nombre' => 'GESTION COMUNITARIA DE PROJECTOS PRODUCTIVOS', 'cursado' => 3, 'horas_total' => 30],
        ];

        foreach ($modulos as $modulo) {
            Modulo::query()->updateOrCreate(
                [
                    'nombre' => $modulo['nombre'],
                    'cursado' => $modulo['cursado'],
                ],
                [
                    'horas_total' => $modulo['horas_total'],
                ]
            );
        }
    }
}
