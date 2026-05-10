<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\AsignacionDocente;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class TareasPedagogicasController extends Controller
{
    public function previewC1Pdf(): Response
    {
        $registros = AsignacionDocente::query()
            ->with([
                'docente:id,nombre,apellido',
                'cursoEtapaMateria:id,curso_etapa_id,curso_materia_id',
                'cursoEtapaMateria.cursoMateria:id,curso_id,materia_id,periodo',
                'cursoEtapaMateria.cursoMateria.curso:id,nombre,division,anexo_id',
                'cursoEtapaMateria.cursoMateria.curso.anexo:id,nombre',
                'cursoEtapaMateria.cursoMateria.materia:id,nombre,tarea_pedagogica',
            ])
            ->activas()
            ->whereHas('cursoEtapaMateria.cursoMateria', function ($query): void {
                $query->whereIn('periodo', ['A', 'C1'])
                    ->whereHas('materia', fn ($materiaQuery) => $materiaQuery->where('tarea_pedagogica', true));
            })
            ->orderByDesc('id')
            ->get()
            ->map(function (AsignacionDocente $asignacion): array {
                $curso = $asignacion->cursoEtapaMateria?->cursoMateria?->curso;
                $materia = $asignacion->cursoEtapaMateria?->cursoMateria?->materia;
                $docente = $asignacion->docente;

                return [
                    'anexo' => $curso?->anexo?->nombre ?? 'N/D',
                    'curso' => trim((string) ($curso?->nombre ?? '') . ' ' . (string) ($curso?->division ?? '')),
                    'materia' => $materia?->nombre ?? 'N/D',
                    'docente' => trim((string) ($docente?->apellido ?? '') . ', ' . (string) ($docente?->nombre ?? ''), ', '),
                ];
            })
            ->filter(fn (array $row): bool => filled($row['materia']))
            ->unique(fn (array $row): string => implode('|', [$row['anexo'], $row['curso'], $row['materia'], $row['docente']]))
            ->sortBy(['anexo', 'curso', 'materia', 'docente'])
            ->values();

        $pdf = Pdf::loadView('filament.reportes.tareas-pedagogicas-c1-preview-pdf', [
            'registros' => $registros,
            'generadoEn' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('planilla-tareas-pedagogicas-c1.pdf');
    }
}
