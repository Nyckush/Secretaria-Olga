<?php

namespace Database\Seeders;

use App\Models\Materia;
use App\Models\Modulo;
use Illuminate\Database\Seeder;

class MateriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materiasPorModulo = [
            'CIUDADANIA' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 4],
                ['nombre' => 'Matemática', 'horas_semanales' => 4],
                ['nombre' => 'Ciencias Biológicas', 'horas_semanales' => 3],
                ['nombre' => 'Educación Digital', 'horas_semanales' => 3],
                ['nombre' => 'Sociología', 'horas_semanales' => 3],
                ['nombre' => 'Historia', 'horas_semanales' => 3],
                ['nombre' => 'Filosofía', 'horas_semanales' => 3, 'tarea_pedagogica' => true],
                ['nombre' => 'Construcción para la Ciudadanía', 'horas_semanales' => 3, 'tarea_pedagogica' => true],
                ['nombre' => 'Geografía', 'horas_semanales' => 2, 'tarea_pedagogica' => true],
                ['nombre' => 'Psicología', 'horas_semanales' => 2, 'tarea_pedagogica' => true],
            ],
            'COMUNICACION,CULTURA Y AUTONOMIA' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 4],
                ['nombre' => 'Lengua Extranjera', 'horas_semanales' => 4, 'tarea_pedagogica' => true],
                ['nombre' => 'Taller de Producción Gráfica y Digital', 'horas_semanales' => 4, 'tarea_pedagogica' => true],
                ['nombre' => 'Matemática', 'horas_semanales' => 4],
                ['nombre' => 'Ciencias Biológicas', 'horas_semanales' => 3],
                ['nombre' => 'Educación Digital', 'horas_semanales' => 3],
                ['nombre' => 'Sociología', 'horas_semanales' => 3],
                ['nombre' => 'Historia', 'horas_semanales' => 3],
                ['nombre' => 'Artes Visuales', 'horas_semanales' => 2, 'tarea_pedagogica' => true],
            ],
            'DESARROLLO SOCIO ECONOMICO DE LA REGION' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 3],
                ['nombre' => 'Matemática', 'horas_semanales' => 5],
                ['nombre' => 'Ciencias Biológicas', 'horas_semanales' => 4],
                ['nombre' => 'Tecnología', 'horas_semanales' => 4, 'tarea_pedagogica' => true],
                ['nombre' => 'Geografía', 'horas_semanales' => 3],
                ['nombre' => 'Historia', 'horas_semanales' => 3],
                ['nombre' => 'Economía', 'horas_semanales' => 3, 'tarea_pedagogica' => true],
                ['nombre' => 'Sistema de Información Contable I', 'horas_semanales' => 5, 'tarea_pedagogica' => true],
            ],
            'AMBIENTE Y DESARROLLO SOSTENIBLE' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 3],
                ['nombre' => 'Matemática', 'horas_semanales' => 5],
                ['nombre' => 'Ciencias Biológicas', 'horas_semanales' => 4],
                ['nombre' => 'Física', 'horas_semanales' => 4 , 'tarea_pedagogica' => true],
                ['nombre' => 'Química', 'horas_semanales' => 3 , 'tarea_pedagogica' => true],
                ['nombre' => 'Ecología y Problemática Ambiental', 'horas_semanales' => 3],
                ['nombre' => 'Geografía', 'horas_semanales' => 3],
                ['nombre' => 'Historia', 'horas_semanales' => 3],
                ['nombre' => 'Música', 'horas_semanales' => 2, 'tarea_pedagogica' => true],
            ],
            'DIVERSIDAD SOCIOCULTURAL Y DESIGUALDAD' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 4],
                ['nombre' => 'Lengua Extranjera', 'horas_semanales' => 3, 'tarea_pedagogica' => true],
                ['nombre' => 'Matemática', 'horas_semanales' => 3],
                ['nombre' => 'Ciencias Biológicas', 'horas_semanales' => 2],
                ['nombre' => 'Geografía', 'horas_semanales' => 3],
                ['nombre' => 'Historia', 'horas_semanales' => 4],
                ['nombre' => 'Fundamentos y Técnicas de la Investigación Acción Participativa', 'horas_semanales' => 3, 'tarea_pedagogica' => true],
                ['nombre' => 'Psicología Social', 'horas_semanales' => 3, 'tarea_pedagogica' => true],
                ['nombre' => 'Antropología Social y Cultural', 'horas_semanales' => 3],
                ['nombre' => 'Danza', 'horas_semanales' => 2, 'tarea_pedagogica' => true],
            ],
            'GESTION COMUNITARIA DE PROJECTOS SOCIOSCULTURALES' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 4],
                ['nombre' => 'Matemática', 'horas_semanales' => 3],
                ['nombre' => 'Ciencias Biológicas', 'horas_semanales' => 2],
                ['nombre' => 'Geografía', 'horas_semanales' => 3],
                ['nombre' => 'Historia', 'horas_semanales' => 4],
                ['nombre' => 'Taller de Producción de Proyectos Socioeducativos', 'horas_semanales' => 4, 'tarea_pedagogica' => true],
                ['nombre' => 'Economía Social', 'horas_semanales' => 4, 'tarea_pedagogica' => true],
                ['nombre' => 'Problemáticas Sociales Locales', 'horas_semanales' => 4, 'tarea_pedagogica' => true],
                ['nombre' => 'Teatro', 'horas_semanales' => 2, 'tarea_pedagogica' => true],
            ],
            'DESARROLLO TERRITORIAL Y ECONOMIA SOCIAL' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 3],
                ['nombre' => 'Lengua Extranjera', 'horas_semanales' => 2 , 'tarea_pedagogica' => true],
                ['nombre' => 'Matemática', 'horas_semanales' => 3],
                ['nombre' => 'Biotecnología', 'horas_semanales' => 2],
                ['nombre' => 'Cs. Físico-Química', 'horas_semanales' => 2],
                ['nombre' => 'Derecho Económico y Administrativo', 'horas_semanales' => 2],
                ['nombre' => 'Articulación con FP', 'horas_semanales' => 4, 'tarea_pedagogica' => true],
                ['nombre' => 'Geografía', 'horas_semanales' => 3, 'tarea_pedagogica' => true],
                ['nombre' => 'Sistema de Información Contable II', 'horas_semanales' => 4, 'tarea_pedagogica' => true],
                ['nombre' => 'Economía Social', 'horas_semanales' => 3],
                ['nombre' => 'Danza', 'horas_semanales' => 2, 'tarea_pedagogica' => true],
            ],
            'GESTION COMUNITARIA DE PROJECTOS PRODUCTIVOS' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 3],
                ['nombre' => 'Matemática', 'horas_semanales' => 3],
                ['nombre' => 'Biotecnología', 'horas_semanales' => 2],
                ['nombre' => 'Cs. Físico-Química', 'horas_semanales' => 2],
                ['nombre' => 'Administración de las Organizaciones', 'horas_semanales' => 4, 'tarea_pedagogica' => true],
                ['nombre' => 'Derecho Económico y Administrativo', 'horas_semanales' => 2],
                ['nombre' => 'Economía Social', 'horas_semanales' => 3],
                ['nombre' => 'Articulación con FP', 'horas_semanales' => 4],
                ['nombre' => 'Gestión de Pymes y Cooperativas', 'horas_semanales' => 5 , 'tarea_pedagogica' => true],
                ['nombre' => 'Teatro', 'horas_semanales' => 2, 'tarea_pedagogica' => true],
            ],
        ];

       foreach ($materiasPorModulo as $nombreModulo => $materias) {
            if (empty($materias)) {
                continue;
            }

            $modulo = Modulo::where('nombre', $nombreModulo)->first();
            if (!$modulo) {
                continue;
            }

            foreach ($materias as $materia) {
                Materia::query()->updateOrCreate(
                    [
                        'nombre' => $materia['nombre'],
                        'modulo_id' => $modulo->id,
                    ],
                    [
                        'horas_semanales' => $materia['horas_semanales'],
                        // AGREGAR ESTA LÍNEA:
                        'tarea_pedagogica' => $materia['tarea_pedagogica'] ?? false,
                    ]
                );
            }
        }
    }
}
