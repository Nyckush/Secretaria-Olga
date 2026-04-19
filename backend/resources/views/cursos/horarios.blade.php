<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Horarios - {{ $cursoEtapa->curso->nombre }} - {{ $cursoEtapa->etapa->nombre }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f7f7f7;
            color: #1f2937;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 20px 16px 28px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        h1 {
            margin: 0;
            font-size: 1.35rem;
        }

        .muted {
            color: #6b7280;
            font-size: 0.92rem;
        }

        .section {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 14px;
        }

        .section h2 {
            margin: 0 0 10px;
            font-size: 1.05rem;
        }

        .section p {
            margin: 0 0 10px;
            color: #6b7280;
            font-size: 0.9rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #e5e7eb;
            vertical-align: top;
            text-align: left;
            padding: 8px;
        }

        thead th {
            background: #f3f4f6;
        }

        input,
        select {
            width: 100%;
            min-height: 34px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 5px 8px;
            background: #fff;
        }

        .bloque {
            min-width: 170px;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid transparent;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-save {
            background: #1d4ed8;
            color: #fff;
        }

        .btn-back {
            background: #fff;
            color: #1f2937;
            border-color: #d1d5db;
        }

        .flash {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .flash-ok {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .flash-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .hint {
            color: #6b7280;
            font-size: 0.82rem;
        }

        @media (max-width: 900px) {
            .section {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top">
            <div>
                <h1>Armar Horarios</h1>
                <div class="muted">
                    Curso: <strong>{{ $cursoEtapa->curso->nombre }}</strong>
                    @if(filled($cursoEtapa->curso->division))
                        - División {{ $cursoEtapa->curso->division }}
                    @endif
                    | Etapa: <strong>{{ $cursoEtapa->etapa->nombre }}</strong>
                </div>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-back">Volver</a>
        </div>

        <form method="POST" action="{{ route('curso-etapas.horarios.store', ['cursoEtapa' => $cursoEtapa]) }}">
            @csrf

            @if(session('status'))
                <div class="flash flash-ok">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="flash flash-danger">
                    <strong>Revisá los datos cargados:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="section">
                <h2>1) Asignaciones</h2>
                <p>Definí docente y período por cada materia del curso en esta etapa.</p>
                <div style="overflow:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th>Docente</th>
                                <th>Horas Cátedra</th>
                                <th>Fecha desde</th>
                                <th>Hasta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cursoEtapaMaterias as $cursoEtapaMateria)
                                @php
                                    $materiaId = $cursoEtapaMateria->id;
                                    $asignacionActual = $asignacionesPorMateria[$materiaId] ?? null;
                                    $docenteValue = old("asignaciones.$materiaId.docente_id", data_get($asignacionActual, 'docente_id'));
                                    $horasCatedraValue = old("asignaciones.$materiaId.horas_catedra", $cursoEtapaMateria->horas_catedra ?? 0);
                                    $fechaDesdeRaw = old("asignaciones.$materiaId.fecha_desde", data_get($asignacionActual, 'fecha_desde'));
                                    $fechaDesdeValue = filled($fechaDesdeRaw) ? explode('T', (string) $fechaDesdeRaw)[0] : null;
                                    $hastaValue = old("asignaciones.$materiaId.hasta", data_get($asignacionActual, 'hasta'));
                                @endphp
                                <tr>
                                    <td>
                                        {{ $cursoEtapaMateria->cursoMateria?->materia?->nombre ?? ('Materia #' . $materiaId) }}
                                    </td>
                                    <td>
                                        <select name="asignaciones[{{ $materiaId }}][docente_id]">
                                            <option value="">- Seleccionar docente -</option>
                                            @foreach($docentes as $docente)
                                                <option value="{{ $docente->id }}" @selected((string) $docenteValue === (string) $docente->id)>
                                                    {{ $docente->apellido }}, {{ $docente->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            min="0"
                                            max="255"
                                            step="1"
                                            name="asignaciones[{{ $materiaId }}][horas_catedra]"
                                            value="{{ $horasCatedraValue }}"
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="date"
                                            name="asignaciones[{{ $materiaId }}][fecha_desde]"
                                            value="{{ $fechaDesdeValue }}"
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            name="asignaciones[{{ $materiaId }}][hasta]"
                                            value="{{ $hastaValue }}"
                                            maxlength="50"
                                            placeholder="Ejemplo: fin del ciclo"
                                        >
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No hay materias cargadas para esta etapa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section">
                <h2>2) Grilla de Horarios</h2>
                <p>Seleccioná una asignación (Materia - Docente) en cada bloque y día.</p>
                <div style="overflow:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th class="bloque">Bloque</th>
                                @foreach($diasSemana as $dia)
                                    <th>{{ $dia }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bloques as $bloque)
                                <tr>
                                    <td class="bloque">
                                        <strong>Bloque {{ $bloque->orden }}</strong><br>
                                        <span class="hint">{{ $bloque->hora_inicio }} - {{ $bloque->hora_fin }}</span>
                                    </td>
                                    @foreach($diasSemana as $dia)
                                        @php
                                            $slotValue = old("slots.$dia.{$bloque->id}", data_get($slots, "$dia.{$bloque->id}"));
                                        @endphp
                                        <td>
                                            <select name="slots[{{ $dia }}][{{ $bloque->id }}]">
                                                <option value="">- Sin asignar -</option>
                                                @foreach($asignacionesOpciones as $asignacionId => $etiqueta)
                                                    <option value="{{ $asignacionId }}" @selected((string) $slotValue === (string) $asignacionId)>
                                                        {{ $etiqueta }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="actions" style="margin-top: 12px;">
                    <button type="submit" class="btn btn-save">Guardar</button>
                    <span class="hint">La grilla solo usa asignaciones ya definidas en la sección superior.</span>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
