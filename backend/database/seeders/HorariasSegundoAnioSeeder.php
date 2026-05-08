<?php

namespace Database\Seeders;

use App\Models\CursoEtapa;
use App\Models\CursoEtapaMateria;
use App\Models\CursoMateria;
use Illuminate\Database\Seeder;

class HorariasSegundoAnioSeeder extends Seeder
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
        $map = config('horas_catedra_segundo_anio', []);
        if (empty($map)) {
            $this->command->info('No se encontraron horas en config/horas_catedra_segundo_anio.php');
            return;
        }

        $creadas = $this->crearCursoEtapaMateriasPorAnio('2°');
        $this->command->info("CursoEtapaMateria creadas para 2°: {$creadas}");

        $normMap = [];
        foreach ($map as $name => $hours) {
            $normMap[$this->normalizar($name)] = (int) $hours;
        }

        $actualizadas = 0;

        CursoEtapaMateria::with(['cursoEtapa.curso', 'cursoMateria.materia'])
            ->get()
            ->each(function (CursoEtapaMateria $cem) use ($normMap, &$actualizadas) {
                $cursoNombre = $cem->cursoEtapa?->curso?->nombre ?? null;
                if ($cursoNombre !== '2°') {
                    return;
                }

                $materiaNombre = $cem->cursoMateria?->materia?->nombre ?? null;
                if (! $materiaNombre) {
                    return;
                }

                $key = $this->normalizar($materiaNombre);
                if (isset($normMap[$key])) {
                    $cem->horas_catedra = $normMap[$key];
                    $cem->save();
                    $actualizadas++;
                }
            });

        if ($actualizadas === 0) {
            $this->command->warn('No se actualizaron horas cátedra de 2°. Revisá coincidencias con config/horas_catedra_segundo_anio.php.');
        } else {
            $this->command->info("Horas cátedra 2° actualizadas: {$actualizadas}");
        }

        $this->command->info('Seeder HorariasSegundoAnioSeeder finalizado.');
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
