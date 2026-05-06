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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
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
            'docente_id' => $a->docente_id,
            'situacion_revista' => $a->situacion_revista,
            'fecha_desde' => optional($a->fecha_desde)->format('Y-m-d'),
            'hasta' => $a->hasta,
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
            'asignacionesOpciones' => $asignacionesOpciones,
            'slots' => $slots,
        ]);
    }

    public function store(Request $request, CursoEtapa $cursoEtapa): RedirectResponse
    {
        $bloqueIds = BloqueHorario::query()->orderBy('orden')->pluck('id');
        $docenteIds = Docente::query()->pluck('id');
        $cursoEtapaMaterias = $this->obtenerOCrearCursoEtapaMaterias($cursoEtapa);
        $cursoEtapaMateriaIds = $cursoEtapaMaterias->pluck('id');
        $cursoEtapaMateriasPorId = $cursoEtapaMaterias->keyBy('id');

        $validator = Validator::make(
            $request->all(),
            [
                'asignaciones' => ['nullable', 'array'],
                'asignaciones.*' => ['array'],
                'asignaciones.*.docente_id' => ['nullable', 'integer'],
                'asignaciones.*.situacion_revista' => ['nullable', 'in:INT,SUP,PRO'],
                'asignaciones.*.horas_catedra' => ['nullable', 'integer', 'min:0', 'max:255'],
                'asignaciones.*.fecha_desde' => ['nullable', 'date'],
                'asignaciones.*.hasta' => ['nullable', 'string', 'max:50'],
                'slots' => ['nullable', 'array'],
                'slots.*' => ['array'],
                'slots.*.*' => ['nullable', 'integer'],
            ]
        );

        $validator->after(function ($validator) use ($request, $bloqueIds, $docenteIds, $cursoEtapaMateriaIds): void {
            foreach ((array) $request->input('asignaciones', []) as $cursoEtapaMateriaId => $asignacion) {
                if (! $cursoEtapaMateriaIds->contains((int) $cursoEtapaMateriaId)) {
                    $validator->errors()->add("asignaciones.$cursoEtapaMateriaId", 'La materia no pertenece a esta etapa del curso.');
                    continue;
                }

                $docenteId = data_get($asignacion, 'docente_id');
                $situacionRevista = data_get($asignacion, 'situacion_revista');
                $fechaDesde = data_get($asignacion, 'fecha_desde');
                $hasta = data_get($asignacion, 'hasta');

                $hayDatosAsignacion = filled($docenteId) || filled($situacionRevista) || filled($fechaDesde) || filled($hasta);

                if (! $hayDatosAsignacion) {
                    continue;
                }

                if (blank($docenteId) || blank($situacionRevista) || blank($fechaDesde) || blank($hasta)) {
                    $validator->errors()->add("asignaciones.$cursoEtapaMateriaId", 'Completa docente, situación de revista, fecha desde y hasta para la asignación.');
                    continue;
                }

                if (! $docenteIds->contains((int) $docenteId)) {
                    $validator->errors()->add("asignaciones.$cursoEtapaMateriaId.docente_id", 'El docente seleccionado no es válido.');
                }
            }

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

        $saveSection = $request->input('save_section');

        $asignacionesExistentes = AsignacionDocente::query()
            ->with(['bajas'])
            ->whereIn('curso_etapa_materia_id', $cursoEtapaMateriaIds)
            ->get()
            ->groupBy('curso_etapa_materia_id');

        $asignacionIdsVigentes = [];

        if ($saveSection !== 'grilla') {
            foreach ($cursoEtapaMateriaIds as $cursoEtapaMateriaId) {
                $inputAsignacion = data_get($data, "asignaciones.$cursoEtapaMateriaId", []);
                $horasCatedra = blank($inputAsignacion['horas_catedra'] ?? null) ? 0 : (int) $inputAsignacion['horas_catedra'];
                $docenteId = blank($inputAsignacion['docente_id'] ?? null) ? null : (int) $inputAsignacion['docente_id'];
                $situacionRevista = blank($inputAsignacion['situacion_revista'] ?? null) ? 'INT' : (string) $inputAsignacion['situacion_revista'];
                $fechaDesde = $inputAsignacion['fecha_desde'] ?? null;
                $hasta = $inputAsignacion['hasta'] ?? null;
                $asignacionesMateria = $asignacionesExistentes->get($cursoEtapaMateriaId, collect())->sortByDesc('id')->values();

                // Separar activas (sin baja) de históricas (con baja) — las históricas nunca se tocan
                $asignacionesActivas = $asignacionesMateria->filter(fn ($a) => $a->bajas->isEmpty())->values();
                $asignacionesConBaja = $asignacionesMateria->filter(fn ($a) => $a->bajas->isNotEmpty());

                /** @var CursoEtapaMateria|null $cursoEtapaMateria */
                $cursoEtapaMateria = $cursoEtapaMateriasPorId->get($cursoEtapaMateriaId);

                if ($cursoEtapaMateria && $cursoEtapaMateria->horas_catedra !== $horasCatedra) {
                    $cursoEtapaMateria->update([
                        'horas_catedra' => $horasCatedra,
                    ]);
                }

                if (blank($docenteId) && blank($fechaDesde) && blank($hasta)) {
                    // Solo eliminar activas; las históricas (con baja) se conservan
                    foreach ($asignacionesActivas as $asignacionEliminar) {
                        $asignacionEliminar->delete();
                    }

                    continue;
                }

                /** @var AsignacionDocente|null $asignacion */
                $asignacion = $asignacionesActivas->shift();

                $payloadAsignacion = [
                    'curso_etapa_materia_id' => $cursoEtapaMateriaId,
                    'docente_id' => $docenteId,
                    'situacion_revista' => $situacionRevista,
                    'fecha_desde' => $fechaDesde,
                    'hasta' => $hasta,
                ];

                if ($asignacion) {
                    $asignacion->update($payloadAsignacion);
                } else {
                    $asignacion = AsignacionDocente::create($payloadAsignacion);
                }

                $asignacionIdsVigentes[] = $asignacion->id;

                // Solo eliminar activas duplicadas extra; nunca las históricas
                foreach ($asignacionesActivas as $duplicada) {
                    $duplicada->delete();
                }
            }
        } else {
            // Guardando sólo la grilla: no tocar asignaciones, usar las vigentes actuales
            $asignacionIdsVigentes = AsignacionDocente::query()
                ->whereIn('curso_etapa_materia_id', $cursoEtapaMateriaIds)
                ->activas()
                ->pluck('id')
                ->toArray();
        }

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

        if ($saveSection !== 'asignaciones') {
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
        }

        return redirect()
            ->route('curso-etapas.horarios', ['cursoEtapa' => $cursoEtapa])
            ->with('status', 'Horarios guardados correctamente.');
    }

    public function ajaxCreateAsignacion(Request $request, CursoEtapa $cursoEtapa)
    {
        $data = $request->validate([
            'curso_etapa_materia_id' => ['required', 'integer'],
            'docente_id' => ['required', 'integer'],
            'situacion_revista' => ['required', 'in:INT,SUP,PRO'],
            'fecha_desde' => ['required', 'date'],
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

        // Verificar docente
        if (! Docente::query()->where('id', $data['docente_id'])->exists()) {
            return response()->json(['error' => 'Docente inválido.'], 422);
        }

        $asignacion = AsignacionDocente::create([
            'curso_etapa_materia_id' => $data['curso_etapa_materia_id'],
            'docente_id' => $data['docente_id'],
            'situacion_revista' => $data['situacion_revista'],
            'fecha_desde' => $data['fecha_desde'],
            'hasta' => $data['hasta'] ?? null,
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
}
