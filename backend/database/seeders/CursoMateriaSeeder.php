<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\CursoMateria;
use App\Models\Materia;
use Illuminate\Database\Seeder;

class CursoMateriaSeeder extends Seeder
{
    /**
     * Orden de materias para el esquema de CUPoF de 1°.
     * El valor final es base_division + indice_materia.
     */
    private const ORDEN_MATERIAS_PRIMER_ANIO = [
        'LENGUA Y LITERATURA',
        'MATEMATICA',
        'GEOGRAFIA',
        'HISTORIA',
        'CIENCIAS BIOLOGICAS',
        'FILOSOFIA',
        'LENGUA EXTRANJERA',
        'PSICOLOGIA',
        'SOCIOLOGIA',
        'ARTES VISUALES',
        'CONSTRUCCION PARA LA CIUDADANIA',
        'EDUCACION DIGITAL',
        'TALLER PRODUCCION GRAFICA Y DIGITAL',
    ];

    /**
     * Orden de materias para el esquema de CUPoF de 2° (divisiones estándar).
     */
    private const ORDEN_MATERIAS_SEGUNDO_ANIO = [
        'LENGUA Y LITERATURA',
        'MATEMATICA',
        'GEOGRAFIA',
        'HISTORIA',
        'QUIMICA',
        'FISICA',
        'CIENCIAS BIOLOGICAS',
        'ECONOMIA',
        'MUSICA',
        'TECNOLOGIA',
        'SISTEMA DE INFORMACION CONTABLE',
        'ECOLOGIA Y PROBLEMATICA AMBIENTAL',
    ];

    /**
     * Orden de materias para la división 12° de 2° año (orden distinto).
     */
    private const ORDEN_MATERIAS_SEGUNDO_ANIO_DIV12 = [
        'LENGUA Y LITERATURA',
        'MATEMATICA',
        'CIENCIAS BIOLOGICAS',
        'TECNOLOGIA',
        'GEOGRAFIA',
        'HISTORIA',
        'ECONOMIA',
        'SISTEMA DE INFORMACION CONTABLE',
        'FISICA',
        'QUIMICA',
        'ECOLOGIA Y PROBLEMATICA AMBIENTAL',
        'MUSICA',
    ];

    /**
     * Base de CUPoF por división de 2° año.
     */
    private const BASE_CUPOF_SEGUNDO_ANIO = [
        '1°'  => 231525,
        '2°'  => 231600,
        '3°'  => 231612,
        '4°'  => 231538,
        '5°'  => 231624,
        '6°'  => 231551,
        '7°'  => 231564,
        '8°'  => 231636,
        '9°'  => 231576,
        '10°' => 231588,
        '11°' => 231648,
        '12°' => 263702,
    ];

    /**
     * CUPoF explícito por división y materia de 3° año.
     * Clave: nombre normalizado (sin tildes, mayúsculas, sin caracteres especiales).
     */
    private const CUPOF_TERCER_ANIO = [
        '1°' => [
            'LENGUA Y LITERATURA'                                            => 241512,
            'LENGUA EXTRANJERA'                                              => 241513,
            'MATEMATICA'                                                     => 241514,
            'BIOTECNOLOGIA'                                                  => 241515,
            'CS FISICO QUIMICA'                                              => 241516,
            'DERECHO ECONOMICO Y ADMINISTRATIVO'                             => 241517,
            'ARTICULACION CON FP'                                            => 241518,
            'GEOGRAFIA'                                                      => 241519,
            'SISTEMA DE INFORMACION CONTABLE II'                             => 241520,
            'ECONOMIA SOCIAL'                                                => 241521,
            'DANZA'                                                          => 241522,
            'ADMINISTRACION DE LAS ORGANIZACIONES'                           => 241523,
            'GESTION DE PYMES Y COOPERATIVAS'                                => 241524,
            'TEATRO'                                                         => 241525,
        ],
        '2°' => [
            'LENGUA Y LITERATURA'                                            => 241440,
            'MATEMATICA'                                                     => 241441,
            'LENGUA EXTRANJERA'                                              => 241442,
            'CIENCIAS BIOLOGICAS'                                            => 241443,
            'GEOGRAFIA'                                                      => 241444,
            'HISTORIA'                                                       => 241445,
            'FUNDAMENTOS Y TECNICAS DE LA INVESTIGACION ACCION PARTICIPATIVA'=> 241446,
            'PSICOLOGIA SOCIAL'                                              => 241447,
            'ANTROPOLOGIA SOCIAL Y CULTURAL'                                 => 241448,
            'DANZA'                                                          => 241449,
            'TALLER DE PRODUCCION DE PROYECTOS SOCIOEDUCATIVOS'              => 241450,
            'ECONOMIA SOCIAL'                                                => 241451,
            'PROBLEMATICAS SOCIALES LOCALES'                                 => 241452,
            'TEATRO'                                                         => 241453,
        ],
        '3°' => [
            'LENGUA Y LITERATURA'                                            => 241454,
            'LENGUA EXTRANJERA'                                              => 241455,
            'MATEMATICA'                                                     => 241456,
            'CIENCIAS BIOLOGICAS'                                            => 241457,
            'GEOGRAFIA'                                                      => 241458,
            'HISTORIA'                                                       => 241459,
            'FUNDAMENTOS Y TECNICAS DE LA INVESTIGACION ACCION PARTICIPATIVA'=> 241460,
            'PSICOLOGIA SOCIAL'                                              => 241461,
            'ANTROPOLOGIA SOCIAL Y CULTURAL'                                 => 241462,
            'DANZA'                                                          => 241463,
            'TALLER DE PRODUCCION DE PROYECTOS SOCIOEDUCATIVOS'              => 241464,
            'ECONOMIA SOCIAL'                                                => 241465,
            'PROBLEMATICAS SOCIALES LOCALES'                                 => 241466,
            'TEATRO'                                                         => 241467,
        ],
        '4°' => [
            'LENGUA Y LITERATURA'                                            => 241526,
            'LENGUA EXTRANJERA'                                              => 241527,
            'MATEMATICA'                                                     => 241528,
            'BIOTECNOLOGIA'                                                  => 241529,
            'CS FISICO QUIMICA'                                              => 241530,
            'DERECHO ECONOMICO Y ADMINISTRATIVO'                             => 241531,
            'ARTICULACION CON FP'                                            => 241532,
            'GEOGRAFIA'                                                      => 241533,
            'SISTEMA DE INFORMACION CONTABLE II'                             => 241534,
            'ECONOMIA SOCIAL'                                                => 241535,
            'DANZA'                                                          => 241536,
            'ADMINISTRACION DE LAS ORGANIZACIONES'                           => 241537,
            'GESTION DE PYMES Y COOPERATIVAS'                                => 241538,
            'TEATRO'                                                         => 241539,
        ],
        '5°' => [
            'LENGUA Y LITERATURA'                                            => 241468,
            'LENGUA EXTRANJERA'                                              => 241469,
            'MATEMATICA'                                                     => 241470,
            'CIENCIAS BIOLOGICAS'                                            => 241471,
            'GEOGRAFIA'                                                      => 241472,
            'HISTORIA'                                                       => 241473,
            'FUNDAMENTOS Y TECNICAS DE LA INVESTIGACION ACCION PARTICIPATIVA'=> 241474,
            'PSICOLOGIA SOCIAL'                                              => 241475,
            'ANTROPOLOGIA SOCIAL Y CULTURAL'                                 => 241476,
            'DANZA'                                                          => 241477,
            'TALLER DE PRODUCCION DE PROYECTOS SOCIOEDUCATIVOS'              => 241478,
            'ECONOMIA SOCIAL'                                                => 241479,
            'PROBLEMATICAS SOCIALES LOCALES'                                 => 241480,
            'TEATRO'                                                         => 241481,
        ],
        '6°' => [
            'LENGUA Y LITERATURA'                                            => 241568,
            'LENGUA EXTRANJERA'                                              => 241569,
            'MATEMATICA'                                                     => 241570,
            'BIOTECNOLOGIA'                                                  => 241571,
            'CS FISICO QUIMICA'                                              => 241572,
            'DERECHO ECONOMICO Y ADMINISTRATIVO'                             => 241573,
            'ARTICULACION CON FP'                                            => 241574,
            'GEOGRAFIA'                                                      => 241575,
            'SISTEMA DE INFORMACION CONTABLE II'                             => 241576,
            'ECONOMIA SOCIAL'                                                => 241577,
            'DANZA'                                                          => 241578,
            'ADMINISTRACION DE LAS ORGANIZACIONES'                           => 241579,
            'GESTION DE PYMES Y COOPERATIVAS'                                => 241580,
            'TEATRO'                                                         => 241581,
        ],
        '7°' => [
            'LENGUA Y LITERATURA'                                            => 241540,
            'LENGUA EXTRANJERA'                                              => 241541,
            'MATEMATICA'                                                     => 241542,
            'BIOTECNOLOGIA'                                                  => 241543,
            'CS FISICO QUIMICA'                                              => 241544,
            'DERECHO ECONOMICO Y ADMINISTRATIVO'                             => 241545,
            'ARTICULACION CON FP'                                            => 241546,
            'GEOGRAFIA'                                                      => 241547,
            'SISTEMA DE INFORMACION CONTABLE II'                             => 241548,
            'ECONOMIA SOCIAL'                                                => 241549,
            'DANZA'                                                          => 241550,
            'ADMINISTRACION DE LAS ORGANIZACIONES'                           => 241551,
            'GESTION DE PYMES Y COOPERATIVAS'                                => 241552,
            'TEATRO'                                                         => 241553,
        ],
        '8°' => [
            'LENGUA Y LITERATURA'                                            => 241483,
            'LENGUA EXTRANJERA'                                              => 241484,
            'MATEMATICA'                                                     => 241485,
            'CIENCIAS BIOLOGICAS'                                            => 241486,
            'GEOGRAFIA'                                                      => 241487,
            'HISTORIA'                                                       => 241488,
            'FUNDAMENTOS Y TECNICAS DE LA INVESTIGACION ACCION PARTICIPATIVA'=> 241489,
            'PSICOLOGIA SOCIAL'                                              => 241490,
            'ANTROPOLOGIA SOCIAL Y CULTURAL'                                 => 241491,
            'DANZA'                                                          => 241492,
            'TALLER DE PRODUCCION DE PROYECTOS SOCIOEDUCATIVOS'              => 241493,
            'ECONOMIA SOCIAL'                                                => 241494,
            'PROBLEMATICAS SOCIALES LOCALES'                                 => 241495,
            'TEATRO'                                                         => 241496,
        ],
        '9°' => [
            'LENGUA Y LITERATURA'                                            => 241554,
            'LENGUA EXTRANJERA'                                              => 241555,
            'MATEMATICA'                                                     => 241556,
            'BIOTECNOLOGIA'                                                  => 241557,
            'CS FISICO QUIMICA'                                              => 241558,
            'DERECHO ECONOMICO Y ADMINISTRATIVO'                             => 241559,
            'ARTICULACION CON FP'                                            => 241560,
            'GEOGRAFIA'                                                      => 241561,
            'SISTEMA DE INFORMACION CONTABLE II'                             => 241562,
            'ECONOMIA SOCIAL'                                                => 241563,
            'DANZA'                                                          => 241564,
            'ADMINISTRACION DE LAS ORGANIZACIONES'                           => 241565,
            'GESTION DE PYMES Y COOPERATIVAS'                                => 241566,
            'TEATRO'                                                         => 241567,
        ],
        '10°' => [
            'LENGUA Y LITERATURA'                                            => 241582,
            'LENGUA EXTRANJERA'                                              => 241583,
            'MATEMATICA'                                                     => 241584,
            'BIOTECNOLOGIA'                                                  => 241585,
            'CS FISICO QUIMICA'                                              => 241586,
            'DERECHO ECONOMICO Y ADMINISTRATIVO'                             => 241587,
            'ARTICULACION CON FP'                                            => 241588,
            'GEOGRAFIA'                                                      => 241589,
            'SISTEMA DE INFORMACION CONTABLE II'                             => 241590,
            'ECONOMIA SOCIAL'                                                => 241591,
            'DANZA'                                                          => 241592,
            'ADMINISTRACION DE LAS ORGANIZACIONES'                           => 241593,
            'GESTION DE PYMES Y COOPERATIVAS'                                => 241594,
            'TEATRO'                                                         => 241595,
        ],
        '11°' => [
            'LENGUA Y LITERATURA'                                            => 241497,
            'LENGUA EXTRANJERA'                                              => 241498,
            'MATEMATICA'                                                     => 241499,
            'CIENCIAS BIOLOGICAS'                                            => 241500,
            'GEOGRAFIA'                                                      => 241501,
            'HISTORIA'                                                       => 241502,
            'FUNDAMENTOS Y TECNICAS DE LA INVESTIGACION ACCION PARTICIPATIVA'=> 241503,
            'PSICOLOGIA SOCIAL'                                              => 241504,
            'ANTROPOLOGIA SOCIAL Y CULTURAL'                                 => 241505,
            'DANZA'                                                          => 241506,
            'TALLER DE PRODUCCION DE PROYECTOS SOCIOEDUCATIVOS'              => 241507,
            'ECONOMIA SOCIAL'                                                => 241508,
            'PROBLEMATICAS SOCIALES LOCALES'                                 => 241509,
            'TEATRO'                                                         => 241510,
        ],
    ];

    /**
     * Base de CUPoF por división de 1° año.
     */
    private const BASE_CUPOF_PRIMER_ANIO = [
        '1°' => 227805,
        '2°' => 227805,
        '3°' => 227831,
        '4°' => 227844,
        '5°' => 227857,
        '6°' => 227870,
        '7°' => 227883,
        '8°' => 227896,
        '9°' => 227909,
        '10°' => 231468,
        '11°' => 231496,
        '12°' => 231509,
        '13°' => 231482,
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materiasPorModulo = Materia::query()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'modulo_id'])
            ->groupBy('modulo_id');

        $cursos = Curso::query()
            ->with([
                'cursoEtapas:id,curso_id,etapa_id,modulo_id',
                'cursoEtapas.etapa:id,orden',
            ])
            ->get(['id', 'nombre', 'division']);

        foreach ($cursos as $curso) {
            $materiasDelCurso = [];

            foreach ($curso->cursoEtapas as $cursoEtapa) {
                if (blank($cursoEtapa->modulo_id)) {
                    continue;
                }

                $ordenEtapa = (int) ($cursoEtapa->etapa->orden ?? 0);
                $materiasModulo = $materiasPorModulo->get($cursoEtapa->modulo_id, collect());

                foreach ($materiasModulo as $materia) {
                    if (! isset($materiasDelCurso[$materia->id])) {
                        $materiasDelCurso[$materia->id] = [
                            'nombre' => $materia->nombre,
                            'ordenes' => [],
                        ];
                    }

                    if ($ordenEtapa > 0) {
                        $materiasDelCurso[$materia->id]['ordenes'][$ordenEtapa] = true;
                    }
                }
            }

            if (empty($materiasDelCurso)) {
                continue;
            }

            uasort($materiasDelCurso, static fn (array $a, array $b): int => strcmp($a['nombre'], $b['nombre']));

            $nroCupof = 1;

            foreach ($materiasDelCurso as $materiaId => $info) {
                $periodo = $this->resolverPeriodo(array_keys($info['ordenes']));

                $nroCupofAsignado = $this->resolverCupofPrimerAnio(
                    $curso->nombre,
                    $curso->division,
                    $info['nombre']
                ) ?? $this->resolverCupofSegundoAnio(
                    $curso->nombre,
                    $curso->division,
                    $info['nombre']
                ) ?? $this->resolverCupofTercerAnio(
                    $curso->nombre,
                    $curso->division,
                    $info['nombre']
                );

                $nroCupofFinal = $nroCupofAsignado ?? $nroCupof;

                CursoMateria::query()->updateOrCreate(
                    [
                        'curso_id' => $curso->id,
                        'materia_id' => $materiaId,
                    ],
                    [
                        'periodo' => $periodo,
                        'nro_cupof' => $nroCupofFinal,
                    ]
                );

                $nroCupof++;
            }
        }
    }

    private function resolverCupofTercerAnio(string $nombreCurso, string $division, string $nombreMateria): ?int
    {
        if ($nombreCurso !== '3°') {
            return null;
        }

        $mapa = self::CUPOF_TERCER_ANIO[$division] ?? null;
        if ($mapa === null) {
            return null;
        }

        $materiaNormalizada = $this->normalizarTexto($nombreMateria);

        return $mapa[$materiaNormalizada] ?? null;
    }

    private function resolverCupofSegundoAnio(string $nombreCurso, string $division, string $nombreMateria): ?int
    {
        if ($nombreCurso !== '2°') {
            return null;
        }

        $base = self::BASE_CUPOF_SEGUNDO_ANIO[$division] ?? null;
        if ($base === null) {
            return null;
        }

        $materiaNormalizada = $this->normalizarTexto($nombreMateria);

        // Alias: el seeder almacena "Sistema de Información Contable I"
        if (str_starts_with($materiaNormalizada, 'SISTEMA DE INFORMACION CONTABLE')) {
            $materiaNormalizada = 'SISTEMA DE INFORMACION CONTABLE';
        }

        $orden = $division === '12°'
            ? self::ORDEN_MATERIAS_SEGUNDO_ANIO_DIV12
            : self::ORDEN_MATERIAS_SEGUNDO_ANIO;

        $indice = array_search($materiaNormalizada, $orden, true);
        if ($indice === false) {
            return null;
        }

        return $base + $indice;
    }

    private function resolverCupofPrimerAnio(string $nombreCurso, string $division, string $nombreMateria): ?int
    {
        if ($nombreCurso !== '1°') {
            return null;
        }

        $base = self::BASE_CUPOF_PRIMER_ANIO[$division] ?? null;
        if ($base === null) {
            return null;
        }

        $materiaNormalizada = $this->normalizarTexto($nombreMateria);
        if ($materiaNormalizada === 'TALLER DE PRODUCCION GRAFICA Y DIGITAL') {
            $materiaNormalizada = 'TALLER PRODUCCION GRAFICA Y DIGITAL';
        }

        $indice = array_search($materiaNormalizada, self::ORDEN_MATERIAS_PRIMER_ANIO, true);
        if ($indice === false) {
            return null;
        }

        return $base + $indice;
    }

    private function normalizarTexto(string $texto): string
    {
        $texto = mb_strtoupper($texto, 'UTF-8');
        $texto = strtr($texto, [
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'Ü' => 'U',
        ]);

        $texto = preg_replace('/[^A-Z0-9\s]/', ' ', $texto) ?? $texto;
        $texto = preg_replace('/\s+/', ' ', $texto) ?? $texto;

        return trim($texto);
    }

    private function resolverPeriodo(array $ordenes): string
    {
        return 'A';
    }
}