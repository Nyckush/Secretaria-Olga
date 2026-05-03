<section class="card">
    <div class="card-header">
        <h2 class="card-title"><span>01.</span> Asignación de Docentes</h2>
        <p class="card-desc">Víncula a los profesores con sus respectivas materias para este ciclo.</p>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Materia</th>
                    <th style="width: 250px">Docente</th>
                    <th>Revista</th>
                    <th style="width: 80px">Horas</th>
                    <th>Desde</th>
                    <th>Hasta (Observaciones)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cursoEtapaMaterias as $cursoEtapaMateria)
                    @php
                        $materiaId = $cursoEtapaMateria->id;
                        $asignacionActual = $asignacionesPorMateria[$materiaId] ?? null;

                        $docenteValue = old("asignaciones.$materiaId.docente_id", data_get($asignacionActual, 'docente_id'));
                        $situacionRevistaValue = old("asignaciones.$materiaId.situacion_revista", data_get($asignacionActual, 'situacion_revista', 'INT'));
                        $horasCatedraValue = old("asignaciones.$materiaId.horas_catedra", $cursoEtapaMateria->horas_catedra ?? 0);

                        $fechaDesdeRaw = old("asignaciones.$materiaId.fecha_desde", data_get($asignacionActual, 'fecha_desde'));
                        $fechaDesdeValue = filled($fechaDesdeRaw) ? explode('T', (string) $fechaDesdeRaw)[0] : null;

                        $hastaValue = old("asignaciones.$materiaId.hasta", data_get($asignacionActual, 'hasta'));
                    @endphp
                    <tr>
                        <td style="font-weight: 500;">
                            {{ $cursoEtapaMateria->cursoMateria?->materia?->nombre ?? ('Materia #' . $materiaId) }}
                        </td>
                        <td>
                            @php
                                $selectedDocente = $docentes->firstWhere('id', $docenteValue);
                                $selectedLabel = $selectedDocente ? (trim($selectedDocente->apellido . ', ' . $selectedDocente->nombre) . ($selectedDocente->dni ? ' (' . $selectedDocente->dni . ')' : '')) : '';
                            @endphp

                            <input type="hidden" name="asignaciones[{{ $materiaId }}][docente_id]" id="asignaciones_{{ $materiaId }}_docente_id" value="{{ $docenteValue }}">
                            <input
                                type="text"
                                class="docente-autocomplete"
                                data-target="#asignaciones_{{ $materiaId }}_docente_id"
                                placeholder="Buscar docente..."
                                value="{{ $selectedLabel }}"
                                autocomplete="off"
                            >
                        </td>
                        <td>
                            <select name="asignaciones[{{ $materiaId }}][situacion_revista]">
                                <option value="INT" @selected($situacionRevistaValue === 'INT')>INT - Titular</option>
                                <option value="SUP" @selected($situacionRevistaValue === 'SUP')>SUP - Suplente</option>
                                <option value="PRO" @selected($situacionRevistaValue === 'PRO')>PRO - Prov.</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="asignaciones[{{ $materiaId }}][horas_catedra]" value="{{ $horasCatedraValue }}">
                        </td>
                        <td>
                            <input type="date" name="asignaciones[{{ $materiaId }}][fecha_desde]" value="{{ $fechaDesdeValue }}">
                        </td>
                        <td>
                            <input type="text" name="asignaciones[{{ $materiaId }}][hasta]" value="{{ $hastaValue }}" placeholder="Ej: ICL/26">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>