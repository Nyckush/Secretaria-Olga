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
                ['nombre' => 'Filosofía', 'horas_semanales' => 3],
                ['nombre' => 'Construcción para la Ciudadanía', 'horas_semanales' => 3],
                ['nombre' => 'Geografía', 'horas_semanales' => 2],
                ['nombre' => 'Psicología', 'horas_semanales' => 2],
            ],
            'COMUNICACION,CULTURA Y AUTONOMIA' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 4],
                ['nombre' => 'Lengua Extranjera', 'horas_semanales' => 4],
                ['nombre' => 'Taller de Producción Gráfica y Digital', 'horas_semanales' => 4],
                ['nombre' => 'Matemática', 'horas_semanales' => 4],
                ['nombre' => 'Ciencias Biológicas', 'horas_semanales' => 3],
                ['nombre' => 'Educación Digital', 'horas_semanales' => 3],
                ['nombre' => 'Sociología', 'horas_semanales' => 3],
                ['nombre' => 'Historia', 'horas_semanales' => 3],
                ['nombre' => 'Artes Visuales', 'horas_semanales' => 2],
            ],
            'DESARROLLO SOCIO ECONOMICO DE LA REGION' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 3],
                ['nombre' => 'Matemática', 'horas_semanales' => 5],
                ['nombre' => 'Ciencias Biológicas', 'horas_semanales' => 4],
                ['nombre' => 'Tecnología', 'horas_semanales' => 4],
                ['nombre' => 'Geografía', 'horas_semanales' => 3],
                ['nombre' => 'Historia', 'horas_semanales' => 3],
                ['nombre' => 'Economía', 'horas_semanales' => 3],
                ['nombre' => 'Sistema de Información Contable I', 'horas_semanales' => 5],
            ],
            'AMBIENTE Y DESARROLLO SOSTENIBLE' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 3],
                ['nombre' => 'Matemática', 'horas_semanales' => 5],
                ['nombre' => 'Ciencias Biológicas', 'horas_semanales' => 4],
                ['nombre' => 'Física', 'horas_semanales' => 4],
                ['nombre' => 'Química', 'horas_semanales' => 3],
                ['nombre' => 'Ecología y Problemática Ambiental', 'horas_semanales' => 3],
                ['nombre' => 'Geografía', 'horas_semanales' => 3],
                ['nombre' => 'Historia', 'horas_semanales' => 3],
                ['nombre' => 'Música', 'horas_semanales' => 2],
            ],
            'DIVERSIDAD SOCIOCULTURAL Y DESIGUALDAD' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 4],
                ['nombre' => 'Lengua Extranjera', 'horas_semanales' => 3],
                ['nombre' => 'Matemática', 'horas_semanales' => 3],
                ['nombre' => 'Ciencias Biológicas', 'horas_semanales' => 2],
                ['nombre' => 'Geografía', 'horas_semanales' => 3],
                ['nombre' => 'Historia', 'horas_semanales' => 4],
                ['nombre' => 'Fundamentos y Técnicas de la Investigación Acción Participativa', 'horas_semanales' => 3],
                ['nombre' => 'Psicología Social', 'horas_semanales' => 3],
                ['nombre' => 'Antropología Social y Cultural', 'horas_semanales' => 3],
                ['nombre' => 'Danza', 'horas_semanales' => 2],
            ],
            'GESTION COMUNITARIA DE PROJECTOS SOCIOSCULTURALES' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 4],
                ['nombre' => 'Matemática', 'horas_semanales' => 3],
                ['nombre' => 'Ciencias Biológicas', 'horas_semanales' => 2],
                ['nombre' => 'Geografía', 'horas_semanales' => 3],
                ['nombre' => 'Historia', 'horas_semanales' => 4],
                ['nombre' => 'Taller de Producción de Proyectos Socioeducativos', 'horas_semanales' => 4],
                ['nombre' => 'Economía Social', 'horas_semanales' => 4],
                ['nombre' => 'Problemáticas Sociales Locales', 'horas_semanales' => 4],
                ['nombre' => 'Teatro', 'horas_semanales' => 2],
            ],
            'DESARROLLO TERRITORIAL Y ECONOMIA SOCIAL' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 3],
                ['nombre' => 'Lengua Extranjera', 'horas_semanales' => 2],
                ['nombre' => 'Matemática', 'horas_semanales' => 3],
                ['nombre' => 'Biotecnología', 'horas_semanales' => 2],
                ['nombre' => 'Cs. Físico-Química', 'horas_semanales' => 2],
                ['nombre' => 'Derecho Económico y Administrativo', 'horas_semanales' => 2],
                ['nombre' => 'Articulación con FP', 'horas_semanales' => 4],
                ['nombre' => 'Geografía', 'horas_semanales' => 3],
                ['nombre' => 'Sistema de Información Contable II', 'horas_semanales' => 4],
                ['nombre' => 'Economía Social', 'horas_semanales' => 3],
                ['nombre' => 'Danza', 'horas_semanales' => 2],
            ],
            'GESTION COMUNITARIA DE PROJECTOS PRODUCTIVOS' => [
                ['nombre' => 'Lengua y Literatura', 'horas_semanales' => 3],
                ['nombre' => 'Matemática', 'horas_semanales' => 3],
                ['nombre' => 'Biotecnología', 'horas_semanales' => 2],
                ['nombre' => 'Cs. Físico-Química', 'horas_semanales' => 2],
                ['nombre' => 'Administración de las Organizaciones', 'horas_semanales' => 4],
                ['nombre' => 'Derecho Económico y Administrativo', 'horas_semanales' => 2],
                ['nombre' => 'Economía Social', 'horas_semanales' => 3],
                ['nombre' => 'Articulación con FP', 'horas_semanales' => 4],
                ['nombre' => 'Gestión de Pymes y Cooperativas', 'horas_semanales' => 5],
                ['nombre' => 'Teatro', 'horas_semanales' => 2],
            ],
        ];

        foreach ($materiasPorModulo as $nombreModulo => $materias) {
            // Si no hay materias, saltar
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
                    ]
                );
            }
        }
    }
}
