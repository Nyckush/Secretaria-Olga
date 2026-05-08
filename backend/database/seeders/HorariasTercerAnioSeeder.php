<?php

namespace Database\Seeders;

use App\Models\CursoEtapa;
use App\Models\CursoEtapaMateria;
use App\Models\CursoMateria;
use Illuminate\Database\Seeder;

class HorariasTercerAnioSeeder extends Seeder
{
    private function normalizar(string $s): string
    {
        $s = mb_strtoupper($s, 'UTF-8');
        $s = strtr($s, [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U',
        ]);
        $s = preg_replace('/[^A-Z0-9\s]/', ' ', $s) ?? $s;
        $s = preg_replace('/\s+/', ' ', $s) ?? $s;
        return trim($s);
    }

    public function run(): void
    {
        $map = config('horas_catedra_tercer_anio', []);
        if (empty($map)) {
            $this->command->info('No se encontraron horas en config/horas_catedra_tercer_anio.php');
            return;
        }

        $creadas = $this->crearCursoEtapaMateriasPorAnio('3°');
        $this->command->info("CursoEtapaMateria creadas para 3°: {$creadas}");

        // Divisiones que corresponden a cada orientación (formato '3° X°')
        $orientacionEconomia = [
            '3° 1°','3° 4°','3° 6°','3° 7°','3° 9°','3° 10°'
        ];

        $orientacionCiencias = [
            '3° 2°','3° 3°','3° 5°','3° 8°','3° 11°'
        ];

        $normMapEconomia = [];
        foreach ($map['economia_administracion'] ?? [] as $k => $v) {
            $normMapEconomia[$this->normalizar($k)] = (int) $v;
        }

        $normMapCiencias = [];
        foreach ($map['ciencias_sociales'] ?? [] as $k => $v) {
            $normMapCiencias[$this->normalizar($k)] = (int) $v;
        }

        $actualizadasEconomia = 0;
        $actualizadasCiencias = 0;

        CursoEtapaMateria::with(['cursoEtapa.curso', 'cursoMateria.materia'])
            ->get()
            ->each(function (CursoEtapaMateria $cem) use ($orientacionEconomia, $orientacionCiencias, $normMapEconomia, $normMapCiencias, &$actualizadasEconomia, &$actualizadasCiencias) {
                $curso = $cem->cursoEtapa?->curso ?? null;
                if (! $curso) return;
                if (($curso->nombre ?? null) !== '3°') return;

                $cursoKey = trim(($curso->nombre ?? '') . ' ' . ($curso->division ?? ''));

                $materiaNombre = $cem->cursoMateria?->materia?->nombre ?? null;
                if (! $materiaNombre) return;

                $key = $this->normalizar($materiaNombre);

                if (in_array($cursoKey, $orientacionEconomia, true)) {
                    if (isset($normMapEconomia[$key])) {
                        $cem->horas_catedra = $normMapEconomia[$key];
                        $cem->save();
                        $actualizadasEconomia++;
                    }
                    return;
                }

                if (in_array($cursoKey, $orientacionCiencias, true)) {
                    if (isset($normMapCiencias[$key])) {
                        $cem->horas_catedra = $normMapCiencias[$key];
                        $cem->save();
                        $actualizadasCiencias++;
                    }
                    return;
                }

                // Si la división no está en ninguna lista, no hacemos nada.
            });

        $totalActualizadas = $actualizadasEconomia + $actualizadasCiencias;
        if ($totalActualizadas === 0) {
            $this->command->warn('No se actualizaron horas cátedra de 3°. Revisá coincidencias con config/horas_catedra_tercer_anio.php.');
        } else {
            $this->command->info("Horas 3° Economía actualizadas: {$actualizadasEconomia}");
            $this->command->info("Horas 3° Ciencias actualizadas: {$actualizadasCiencias}");
            $this->command->info("Horas cátedra 3° actualizadas (total): {$totalActualizadas}");
        }

        $this->command->info('Seeder HorariasTercerAnioSeeder finalizado.');
    }

    private function crearCursoEtapaMateriasPorAnio(string $nombreCurso): int
    {
        $creadas = 0;

        $cursoEtapas = CursoEtapa::query()
            ->whereHas('curso', fn ($q) => $q->where('nombre', $nombreCurso))
            ->whereNotNull('modulo_id')
            ->get(['id', 'curso_id', 'modulo_id']);

        foreach ($cursoEtapas as $cursoEtapa) {
            $cursoMaterias = CursoMateria::query()
                ->where('curso_id', $cursoEtapa->curso_id)
                ->whereHas('materia', fn ($q) => $q->where('modulo_id', $cursoEtapa->modulo_id))
                ->get(['id']);

            foreach ($cursoMaterias as $cursoMateria) {
                $cem = CursoEtapaMateria::firstOrCreate([
                    'curso_etapa_id' => $cursoEtapa->id,
                    'curso_materia_id' => $cursoMateria->id,
                ]);

                if ($cem->wasRecentlyCreated) {
                    $creadas++;
                }
            }
        }

        return $creadas;
    }
}
