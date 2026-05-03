<?php

namespace Database\Seeders;

use App\Models\CursoEtapaMateria;
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

        $normMap = [];
        foreach ($map as $name => $hours) {
            $normMap[$this->normalizar($name)] = (int) $hours;
        }

        CursoEtapaMateria::with(['cursoEtapa.curso', 'cursoMateria.materia'])
            ->get()
            ->each(function (CursoEtapaMateria $cem) use ($normMap) {
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
                    $this->command->info("Actualizada horas 2°: {$materiaNombre} -> {$normMap[$key]}");
                }
            });

        $this->command->info('Seeder HorariasSegundoAnioSeeder finalizado.');
    }
}
