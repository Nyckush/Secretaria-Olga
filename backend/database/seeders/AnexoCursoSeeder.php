<?php

namespace Database\Seeders;

use App\Models\Anexo;
use App\Models\Curso;
use Illuminate\Database\Seeder;

class AnexoCursoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cicloLectivo = (int) date('Y');

        $cursosPorAnexo = [
            'CENTRAL' => [
                ['nombre' => '1°', 'division' => '1°'],
                ['nombre' => '1°', 'division' => '2°'],
                ['nombre' => '1°', 'division' => '10°'],
                ['nombre' => '2°', 'division' => '1°'],
                ['nombre' => '2°', 'division' => '2°'],
                ['nombre' => '2°', 'division' => '10°'],
                ['nombre' => '3°', 'division' => '1°'],
                ['nombre' => '3°', 'division' => '2°'],
                ['nombre' => '3°', 'division' => '10°'],
            ],
            'PALPALA' => [
                ['nombre' => '1°', 'division' => '3°'],
                ['nombre' => '2°', 'division' => '3°'],
                ['nombre' => '3°', 'division' => '3°'],
            ],
            'PERICO' => [
                ['nombre' => '1°', 'division' => '4°'],
                ['nombre' => '2°', 'division' => '4°'],
                ['nombre' => '3°', 'division' => '4°'],
            ],
            'SAN PEDRO' => [
                ['nombre' => '1°', 'division' => '5°'],
                ['nombre' => '1°', 'division' => '12°'],
                ['nombre' => '2°', 'division' => '5°'],
                ['nombre' => '2°', 'division' => '12°'],
                ['nombre' => '3°', 'division' => '5°'],
            ],
            'EL CARMEN' => [
                ['nombre' => '1°', 'division' => '11°'],
                ['nombre' => '2°', 'division' => '11°'],
                ['nombre' => '3°', 'division' => '11°'],
            ],
            'LIBERTADOR GRAL. SAN MARTIN' => [
                ['nombre' => '1°', 'division' => '6°'],
                ['nombre' => '1°', 'division' => '13°'],
                ['nombre' => '2°', 'division' => '6°'],
                ['nombre' => '3°', 'division' => '6°'],
            ],
            'HUMAHUACA' => [
                ['nombre' => '1°', 'division' => '7°'],
                ['nombre' => '1°', 'division' => '8°'],
                ['nombre' => '2°', 'division' => '7°'],
                ['nombre' => '2°', 'division' => '8°'],
                ['nombre' => '3°', 'division' => '7°'],
                ['nombre' => '3°', 'division' => '8°'],
            ],
            'LA QUIACA' => [
                ['nombre' => '1°', 'division' => '9°'],
                ['nombre' => '2°', 'division' => '9°'],
                ['nombre' => '3°', 'division' => '9°'],
            ],
        ];

        foreach ($cursosPorAnexo as $nombreAnexo => $cursos) {
            $anexo = Anexo::query()->firstOrCreate([
                'nombre' => $nombreAnexo,
            ]);

            foreach ($cursos as $curso) {
                Curso::query()->updateOrCreate(
                    [
                        'anexo_id' => $anexo->id,
                        'nombre' => $curso['nombre'],
                        'division' => $curso['division'],
                        'ciclo_lectivo' => $cicloLectivo,
                    ],
                    [
                        'turno' => 'Noche',
                    ]
                );
            }
        }
    }
}