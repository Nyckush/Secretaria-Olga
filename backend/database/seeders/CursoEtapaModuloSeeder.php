<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Modulo;
use Illuminate\Database\Seeder;

class CursoEtapaModuloSeeder extends Seeder
{
    /**
     * Cursos de 3° año con orientación A.
     * Clave: "nombre division"
     */
    private array $orientacionA = [
        '3° 2°',
        '3° 3°',
        '3° 5°',
        '3° 8°',
        '3° 11°',
    ];

    /**
     * Cursos de 3° año con orientación B.
     */
    private array $orientacionB = [
        '3° 1°',
        '3° 4°',
        '3° 6°',
        '3° 7°',
        '3° 9°',
        '3° 10°',
    ];

    public function run(): void
    {
        // Módulos por año y etapa (orden)
        // cursado 1 (1° año): etapa orden 1 → CIUDADANIA, orden 2 → COMUNICACION,CULTURA Y AUTONOMIA
        // cursado 2 (2° año): etapa orden 1 → DESARROLLO SOCIO ECONOMICO DE LA REGION, orden 2 → AMBIENTE Y DESARROLLO SOSTENIBLE
        $modulosPorCursadoYOrden = [
            1 => [
                1 => 'CIUDADANIA',
                2 => 'COMUNICACION,CULTURA Y AUTONOMIA',
            ],
            2 => [
                1 => 'DESARROLLO SOCIO ECONOMICO DE LA REGION',
                2 => 'AMBIENTE Y DESARROLLO SOSTENIBLE',
            ],
        ];

        $modulosPorOrientacion = [
            'A' => [
                1 => 'DIVERSIDAD SOCIOCULTURAL Y DESIGUALDAD',
                2 => 'GESTION COMUNITARIA DE PROJECTOS SOCIOSCULTURALES',
            ],
            'B' => [
                1 => 'DESARROLLO TERRITORIAL Y ECONOMIA SOCIAL',
                2 => 'GESTION COMUNITARIA DE PROJECTOS PRODUCTIVOS',
            ],
        ];

        // Cargar todos los módulos indexados por nombre para evitar N+1
        $modulos = Modulo::all()->keyBy('nombre');

        $cursos = Curso::with('cursoEtapas.etapa')->get();

        foreach ($cursos as $curso) {
            $cursoKey = $curso->nombre . ' ' . $curso->division;

            foreach ($curso->cursoEtapas as $cursoEtapa) {
                $orden = $cursoEtapa->etapa->orden;
                $moduloNombre = null;

                if ($curso->nombre === '1°') {
                    $moduloNombre = $modulosPorCursadoYOrden[1][$orden] ?? null;
                } elseif ($curso->nombre === '2°') {
                    $moduloNombre = $modulosPorCursadoYOrden[2][$orden] ?? null;
                } elseif ($curso->nombre === '3°') {
                    if (in_array($cursoKey, $this->orientacionA)) {
                        $moduloNombre = $modulosPorOrientacion['A'][$orden] ?? null;
                    } elseif (in_array($cursoKey, $this->orientacionB)) {
                        $moduloNombre = $modulosPorOrientacion['B'][$orden] ?? null;
                    }
                }

                if ($moduloNombre && isset($modulos[$moduloNombre])) {
                    $cursoEtapa->update([
                        'modulo_id' => $modulos[$moduloNombre]->id,
                    ]);
                }
            }
        }
    }
}
