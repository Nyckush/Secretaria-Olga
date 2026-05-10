<?php

namespace App\Http\Controllers\Cursos;

use App\Http\Controllers\Controller;
use App\Models\AsignacionDocente;
use App\Models\BloqueHorario;
use App\Models\CursoEtapa;
use App\Models\CursoEtapaMateria;
use App\Models\CursoMateria;
use App\Models\Docente;
use App\Models\Horario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\View\View;

class CursoEtapaHorarioController extends Controller
{
    private const DIAS_SEMANA = [
        'Lunes',
        'Martes',
        'Miércoles',
        'Jueves',
        'Viernes',
    ];

    public function show(CursoEtapa $cursoEtapa): View
    {
        $cursoEtapa->load([
            'curso:id,nombre,division,turno,ciclo_lectivo',
            'etapa:id,nombre,orden',
            'modulo:id,nombre',
        ]);

        $cursoEtapaMaterias = $this->obtenerOCrearCursoEtapaMaterias($cursoEtapa);

        $bloques = BloqueHorario::query()
            ->orderBy('orden')
            ->get(['id', 'orden', 'hora_inicio', 'hora_fin']);

        $docentes = Docente::query()
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get(['id', 'apellido', 'nombre', 'dni']);

        $cursoEtapaMateriaIds = $cursoEtapaMaterias->pluck('id');

        $asignaciones = AsignacionDocente::query()
            ->with([
                'cursoEtapaMateria.cursoMateria.materia:id,nombre',
                'docente:id,nombre,apellido',
            ])
            ->whereIn('curso_etapa_materia_id', $cursoEtapaMateriaIds)
            ->activas()
            ->orderByDesc('id')
            ->get(['id', 'curso_etapa_materia_id', 'docente_id', 'situacion_revista', 'fecha_desde', 'hasta']);

        $asignacionesPorMateria = $asignaciones
            ->groupBy('curso_etapa_materia_id')
            ->map(function ($items) {
                $a = $items->first();

                return [
                    'id' => $a->id,
                    'curso_etapa_materia_id' => $a->curso_etapa_materia_id,
                    'docente_id' => $a->docente_id,
                    'situacion_revista' => $a->situacion_revista,
                    'fecha_desde' => optional($a->fecha_desde)->format('Y-m-d'),
                    'horas_catedra' => $a->cursoEtapaMateria?->horas_catedra,
                    'hasta' => $a->hasta,
                ];
            })
            ->toArray();

        $asignacionesPorId = $asignaciones
            ->mapWithKeys(function (AsignacionDocente $asignacion): array {
                return [
                    $asignacion->id => [
                        'id' => $asignacion->id,
                        'curso_etapa_materia_id' => $asignacion->curso_etapa_materia_id,
                        'docente_id' => $asignacion->docente_id,
                        'situacion_revista' => $asignacion->situacion_revista,
                        'fecha_desde' => optional($asignacion->fecha_desde)->format('Y-m-d'),
                        'horas_catedra' => $asignacion->cursoEtapaMateria?->horas_catedra,
                        'hasta' => $asignacion->hasta,
                    ],
                ];
            })
            ->toArray();

        $asignacionesOpciones = $asignaciones
            ->mapWithKeys(function (AsignacionDocente $asignacion): array {
                $materia = $asignacion->cursoEtapaMateria?->cursoMateria?->materia?->nombre ?? ('Materia #' . $asignacion->curso_etapa_materia_id);
                $docente = trim(($asignacion->docente?->apellido ?? '') . ', ' . ($asignacion->docente?->nombre ?? ''));

                return [$asignacion->id => $materia . ' - ' . trim($docente, ', ')];
            })
            ->toArray();

        $slots = [];

        if ($asignaciones->isNotEmpty()) {
            $horarios = Horario::query()
                ->whereIn('asignacion_docente_id', $asignaciones->pluck('id'))
                ->get(['id', 'asignacion_docente_id', 'bloque_id', 'dia_semana']);

            foreach ($horarios as $horario) {
                $slots[$horario->dia_semana][$horario->bloque_id] = $horario->asignacion_docente_id;
            }
        }

        return view('filament.cursos.horarios', [
            'cursoEtapa' => $cursoEtapa,
            'diasSemana' => self::DIAS_SEMANA,
            'bloques' => $bloques,
            'docentes' => $docentes,
            'cursoEtapaMaterias' => $cursoEtapaMaterias,
            'asignacionesPorMateria' => $asignacionesPorMateria,
            'asignacionesPorId' => $asignacionesPorId,
            'asignacionesOpciones' => $asignacionesOpciones,
            'slots' => $slots,
        ]);
    }

    public function store(Request $request, CursoEtapa $cursoEtapa): RedirectResponse
    {
        $bloqueIds = BloqueHorario::query()->orderBy('orden')->pluck('id');
        $cursoEtapaMaterias = $this->obtenerOCrearCursoEtapaMaterias($cursoEtapa);
        $cursoEtapaMateriaIds = $cursoEtapaMaterias->pluck('id');

        $validator = Validator::make(
            $request->all(),
            [
                'slots' => ['nullable', 'array'],
                'slots.*' => ['array'],
                'slots.*.*' => ['nullable', 'integer'],
            ]
        );

        $validator->after(function ($validator) use ($request, $bloqueIds, $cursoEtapaMateriaIds): void {
            $asignacionIdsValidas = AsignacionDocente::query()
                ->whereIn('curso_etapa_materia_id', $cursoEtapaMateriaIds)
                ->activas()
                ->pluck('id');

            foreach (self::DIAS_SEMANA as $dia) {
                foreach ($bloqueIds as $bloqueId) {
                    $asignacionId = data_get($request->input('slots', []), "$dia.$bloqueId");

                    if (blank($asignacionId)) {
                        continue;
                    }

                    if (! $asignacionIdsValidas->contains((int) $asignacionId)) {
                        $validator->errors()->add("slots.$dia.$bloqueId", "La asignación seleccionada para $dia (bloque $bloqueId) no es válida.");
                    }
                }
            }
        });

        $data = $validator->validate();

        $asignacionIdsVigentes = AsignacionDocente::query()
            ->whereIn('curso_etapa_materia_id', $cursoEtapaMateriaIds)
            ->activas()
            ->pluck('id')
            ->toArray();

        $asignacionesVigentes = AsignacionDocente::query()
            ->whereIn('id', $asignacionIdsVigentes)
            ->get(['id', 'curso_etapa_materia_id', 'docente_id', 'situacion_revista', 'fecha_desde', 'hasta'])
            ->keyBy('id');

        $horariosExistentes = Horario::query()
            ->whereHas('asignacionDocente', function ($query) use ($cursoEtapaMateriaIds) {
                $query->whereIn('curso_etapa_materia_id', $cursoEtapaMateriaIds);
            })
            ->get()
            ->groupBy(fn (Horario $horario): string => $horario->dia_semana . '-' . $horario->bloque_id);

        foreach (self::DIAS_SEMANA as $dia) {
            foreach ($bloqueIds as $bloqueId) {
                $asignacionDocenteId = blank(data_get($data, "slots.$dia.$bloqueId")) ? null : (int) data_get($data, "slots.$dia.$bloqueId");
                $clave = $dia . '-' . $bloqueId;
                $horariosSlot = $horariosExistentes->get($clave, collect());

                if (blank($asignacionDocenteId) || ! in_array($asignacionDocenteId, $asignacionIdsVigentes, true)) {
                    foreach ($horariosSlot as $horarioExistente) {
                        $horarioExistente->delete();
                    }

                    continue;
                }

                /** @var Horario|null $horario */
                $horario = $horariosSlot->shift();
                $asignacion = $asignacionesVigentes->get($asignacionDocenteId);

                if (! $asignacion) {
                    foreach ($horariosSlot as $horarioExistente) {
                        $horarioExistente->delete();
                    }

                    if ($horario) {
                        $horario->delete();
                    }

                    continue;
                }

                $payload = [
                    'asignacion_docente_id' => $asignacionDocenteId,
                    'curso_etapa_materia_id' => $asignacion->curso_etapa_materia_id,
                    'docente_id' => $asignacion->docente_id,
                    'bloque_id' => $bloqueId,
                    'dia_semana' => $dia,
                    'fecha_desde' => $asignacion->fecha_desde,
                    'hasta' => $asignacion->hasta,
                ];

                if ($horario) {
                    $horario->update($payload);
                } else {
                    Horario::create($payload);
                }

                foreach ($horariosSlot as $duplicado) {
                    $duplicado->delete();
                }
            }
        }

        return redirect()
            ->route('curso-etapas.horarios', ['cursoEtapa' => $cursoEtapa])
            ->with('status', 'Horarios guardados correctamente.');
    }

    public function previewPdf(CursoEtapa $cursoEtapa): Response
    {
        $cursoEtapa->load(['curso:id,nombre,division,turno,ciclo_lectivo']);

        $curso = $cursoEtapa->curso;

        $bloques = BloqueHorario::query()
            ->orderBy('orden')
            ->get(['id', 'orden', 'hora_inicio', 'hora_fin']);

        $cursoEtapas = CursoEtapa::query()
            ->with([
                'etapa:id,nombre,orden',
                'modulo:id,nombre',
                'curso.anexo:id,nombre',
            ])
            ->where('curso_id', $curso->id)
            ->get()
            ->sortBy(fn ($ce) => $ce->etapa->orden ?? 0)
            ->values();

        $matricesPorCursoEtapa = [];

        foreach ($cursoEtapas as $ce) {
            $cursoEtapaMateriaIds = $this->obtenerOCrearCursoEtapaMaterias($ce)->pluck('id');

            $horarios = Horario::query()
                ->with([
                    'bloque:id,orden,hora_inicio,hora_fin',
                    'asignacionDocente:id,curso_etapa_materia_id,docente_id',
                    'asignacionDocente.docente:id,nombre,apellido',
                    'asignacionDocente.cursoEtapaMateria:id,curso_materia_id',
                    'asignacionDocente.cursoEtapaMateria.cursoMateria:id,materia_id,periodo',
                    'asignacionDocente.cursoEtapaMateria.cursoMateria.materia:id,nombre',
                ])
                ->whereIn('curso_etapa_materia_id', $cursoEtapaMateriaIds)
                ->orderBy('bloque_id')
                ->get(['id', 'asignacion_docente_id', 'curso_etapa_materia_id', 'bloque_id', 'dia_semana']);

            $matricesPorCursoEtapa[$ce->id] = [
                'C1' => $this->construirMatrizHoraria($horarios, 'C1'),
                'C2' => $this->construirMatrizHoraria($horarios, 'C2'),
            ];
        }

        $pdf = Pdf::loadView('filament.cursos.horarios-preview-pdf', [
            'curso' => $curso,
            'cursoEtapas' => $cursoEtapas,
            'diasSemana' => self::DIAS_SEMANA,
            'bloques' => $bloques,
            'matricesPorCursoEtapa' => $matricesPorCursoEtapa,
            'generadoEn' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("horarios-curso-{$curso->id}.pdf");
    }

    public function ajaxCreateAsignacion(Request $request, CursoEtapa $cursoEtapa)
    {
        $data = $request->validate([
            'asignacion_id' => ['nullable', 'integer'],
            'curso_etapa_materia_id' => ['required', 'integer'],
            'docente_id' => ['required', 'integer'],
            'situacion_revista' => ['required', 'in:INT,SUP,PRO'],
            'fecha_desde' => ['required', 'date'],
            'horas_catedra' => ['nullable', 'integer', 'min:0', 'max:255'],
            'hasta' => ['nullable', 'string', 'max:50'],
        ]);

        // Verificar que la materia pertenece a este cursoEtapa
        $cursoEtapaMateria = CursoEtapaMateria::query()
            ->where('id', $data['curso_etapa_materia_id'])
            ->where('curso_etapa_id', $cursoEtapa->id)
            ->first();

        if (! $cursoEtapaMateria) {
            return response()->json(['error' => 'La materia no pertenece a esta etapa del curso.'], 422);
        }

        $horasCatedra = blank($data['horas_catedra'] ?? null)
            ? 0
            : (int) $data['horas_catedra'];

        if ((int) ($cursoEtapaMateria->horas_catedra ?? 0) !== $horasCatedra) {
            $cursoEtapaMateria->update([
                'horas_catedra' => $horasCatedra,
            ]);
        }

        // Verificar docente
        if (! Docente::query()->where('id', $data['docente_id'])->exists()) {
            return response()->json(['error' => 'Docente inválido.'], 422);
        }

        $asignacionSolicitada = null;

        if (filled($data['asignacion_id'] ?? null)) {
            $asignacionSolicitada = AsignacionDocente::query()
                ->where('id', (int) $data['asignacion_id'])
                ->whereIn('curso_etapa_materia_id', function ($query) use ($cursoEtapa) {
                    $query->select('id')
                        ->from('curso_etapa_materia')
                        ->where('curso_etapa_id', $cursoEtapa->id);
                })
                ->activas()
                ->first();

            if (! $asignacionSolicitada) {
                return response()->json(['error' => 'La asignación que intentás editar no es válida o ya no está activa.'], 422);
            }
        }

        // Regla de negocio: una materia del curso-etapa no debe quedar con más de un docente activo.
        // Si ya existe asignación activa para esa materia, se reutiliza y se actualiza.
        $asignacionExistenteMateria = AsignacionDocente::query()
            ->where('curso_etapa_materia_id', $data['curso_etapa_materia_id'])
            ->activas()
            ->orderByDesc('id')
            ->first();

        $asignacion = null;

        if (
            $asignacionSolicitada
            && (int) $asignacionSolicitada->curso_etapa_materia_id === (int) $data['curso_etapa_materia_id']
        ) {
            $asignacion = $asignacionSolicitada;
        } elseif ($asignacionExistenteMateria) {
            $asignacion = $asignacionExistenteMateria;
        }

        $payload = [
            'curso_etapa_materia_id' => $data['curso_etapa_materia_id'],
            'docente_id' => $data['docente_id'],
            'situacion_revista' => $data['situacion_revista'],
            'fecha_desde' => $data['fecha_desde'],
            'hasta' => $data['hasta'] ?? null,
        ];

        if ($asignacion) {
            $asignacion->update($payload);
        } else {
            $asignacion = AsignacionDocente::create($payload);
        }

        // Si ya tenía bloques cargados, reflejar de inmediato docente/fechas al editar la asignación.
        Horario::query()
            ->where('asignacion_docente_id', $asignacion->id)
            ->update([
                'curso_etapa_materia_id' => $asignacion->curso_etapa_materia_id,
                'docente_id' => $asignacion->docente_id,
                'fecha_desde' => $asignacion->fecha_desde,
                'hasta' => $asignacion->hasta,
            ]);

        $materiaNombre = $cursoEtapaMateria->cursoMateria?->materia?->nombre ?? ('Materia #' . $cursoEtapaMateria->id);
        $docente = Docente::find($data['docente_id']);
        $etiqueta = $materiaNombre . ' - ' . trim(($docente->apellido ?? '') . ', ' . ($docente->nombre ?? ''));

        return response()->json([
            'id' => $asignacion->id,
            'etiqueta' => $etiqueta,
            'asignacion' => $asignacion,
        ]);
    }

    private function obtenerOCrearCursoEtapaMaterias(CursoEtapa $cursoEtapa): Collection
    {
        $cursoMaterias = CursoMateria::query()
            ->with('materia:id,nombre,modulo_id')
            ->where('curso_id', $cursoEtapa->curso_id)
            ->when(
                filled($cursoEtapa->modulo_id),
                fn ($query) => $query->whereHas('materia', fn ($materiaQuery) => $materiaQuery->where('modulo_id', $cursoEtapa->modulo_id))
            )
            ->get(['id', 'materia_id']);

        if ($cursoMaterias->isEmpty()) {
            return collect();
        }

        foreach ($cursoMaterias as $cursoMateria) {
            CursoEtapaMateria::firstOrCreate([
                'curso_etapa_id' => $cursoEtapa->id,
                'curso_materia_id' => $cursoMateria->id,
            ]);
        }

        return CursoEtapaMateria::query()
            ->with('cursoMateria.materia:id,nombre')
            ->where('curso_etapa_id', $cursoEtapa->id)
            ->whereIn('curso_materia_id', $cursoMaterias->pluck('id'))
            ->get(['id', 'curso_materia_id', 'horas_catedra']);
    }

    private function construirMatrizHoraria(Collection $horarios, string $periodo): array
    {
        $matriz = [];

        foreach ($horarios as $horario) {
            $cursoMateriaPeriodo = $horario->asignacionDocente?->cursoEtapaMateria?->cursoMateria?->periodo;

            if (! in_array($cursoMateriaPeriodo, ['A', $periodo], true)) {
                continue;
            }

            $dia = (string) $horario->dia_semana;
            $bloqueId = (int) $horario->bloque_id;
            $materia = $horario->asignacionDocente?->cursoEtapaMateria?->cursoMateria?->materia?->nombre;
            $docenteApellido = $horario->asignacionDocente?->docente?->apellido;
            $docenteNombre = $horario->asignacionDocente?->docente?->nombre;
            $docente = trim((string) $docenteApellido . ', ' . (string) $docenteNombre, ', ');

            $matriz[$dia][$bloqueId] = [
                'materia' => $materia,
                'docente' => $docente,
            ];
        }

        return $matriz;
    }
}
