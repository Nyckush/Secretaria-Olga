<div class="section">
    <h2>1) Asignaciones</h2>
    <p>Definí docente y período por cada materia del curso en esta etapa.</p>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th class="col-materia">Materia</th>
                    <th class="col-docente">Docente</th>
                    <th class="col-sit">Sit. Rev.</th>
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
                        $situacionRevistaValue = old("asignaciones.$materiaId.situacion_revista", data_get($asignacionActual, 'situacion_revista'));
                        $horasCatedraValue = old("asignaciones.$materiaId.horas_catedra", $cursoEtapaMateria->horas_catedra ?? 0);
                        $fechaDesdeRaw = old("asignaciones.$materiaId.fecha_desde", data_get($asignacionActual, 'fecha_desde'));
                        $fechaDesdeValue = filled($fechaDesdeRaw) ? explode('T', (string) $fechaDesdeRaw)[0] : null;
                        $hastaValue = old("asignaciones.$materiaId.hasta", data_get($asignacionActual, 'hasta'));
                    @endphp
                    <tr>
                        <td class="col-materia">
                            {{ $cursoEtapaMateria->cursoMateria?->materia?->nombre ?? ('Materia #' . $materiaId) }}
                        </td>
                        <td class="col-docente">
                            <div class="relative">
                                <input type="text" class="docente-search-row" placeholder="Buscar docente" autocomplete="off" value="{{ $docentes->firstWhere('id', $docenteValue)?->apellido ? $docentes->firstWhere('id', $docenteValue)->apellido . ', ' . $docentes->firstWhere('id', $docenteValue)->nombre : '' }}">
                                <input type="hidden" name="asignaciones[{{ $materiaId }}][docente_id]" id="docente-id-row-{{ $materiaId }}" value="{{ $docenteValue }}">
                                <div class="docente-suggestions-row"></div>
                            </div>
                        </td>
                        <td class="col-sit">
                            <select name="asignaciones[{{ $materiaId }}][situacion_revista]">
                                <option value="">- Seleccionar sit/option>
                                <option value="INT" @selected((string) $situacionRevistaValue === 'INT')>INT - Titular</option>
                                <option value="SUP" @selected((string) $situacionRevistaValue === 'SUP')>SUP - Suplente</option>
                                <option value="PRO" @selected((string) $situacionRevistaValue === 'PRO')>PRO - Provisional</option>
                            </select>
                        </td>
                        <td>
                            <input
                                type="number"                                min="0"
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
                        <td colspan="6">No hay materias cargadas para esta etapa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="actions">
    <button type="submit" class="btn btn-save">Guardar asignaciones</button>
    <span class="hint">Guardá sólo las asignaciones modificadas.</span>
</div>
