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
                                <div style="display:flex; gap:6px; align-items:start;">
                                    <select name="slots[{{ $dia }}][{{ $bloque->id }}]" class="slot-select" data-dia="{{ $dia }}" data-bloque="{{ $bloque->id }}">
                                        <option value="">- Sin asignar -</option>
                                        @foreach($asignacionesOpciones as $asignacionId => $etiqueta)
                                            <option value="{{ $asignacionId }}" @selected((string) $slotValue === (string) $asignacionId)>
                                                {{ $etiqueta }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-back btn-assign" data-dia="{{ $dia }}" data-bloque="{{ $bloque->id }}">Asignar</button>
                                </div>
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
