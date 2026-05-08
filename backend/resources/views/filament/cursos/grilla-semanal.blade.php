<div class="grillaContainer">
    <div style="overflow:auto;">
        <table>
            <thead>
                <tr>
                    <th class="bloque">Horarios</th>
                    @foreach($diasSemana as $dia)
                        <th>{{ $dia }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($bloques as $bloque)
                    <tr>
                        <td class="bloque">
                        
                           <span class="hint">
                        {{ \Carbon\Carbon::parse($bloque->hora_inicio)->format('H:i') }} 
                         <br>
                        {{ \Carbon\Carbon::parse($bloque->hora_fin)->format('H:i') }}
                            </span>
                        </td>
                        @foreach($diasSemana as $dia)
                            @php
                                $slotValue = old("slots.$dia.{$bloque->id}", data_get($slots, "$dia.{$bloque->id}"));
                                $slotTieneAsignacion = filled($slotValue);
                            @endphp
                            <td>
                                <div style="display:flex; gap:6px; align-items:start; flex-direction:column;">
                                    <input type="hidden" name="slots[{{ $dia }}][{{ $bloque->id }}]" class="slot-value" value="{{ $slotValue }}">
                                    @if($slotTieneAsignacion)
                                        @php
                                            $etiqueta = $asignacionesOpciones[$slotValue] ?? '';
                                            $partes = explode(' - ', $etiqueta, 2);
                                            $materia = $partes[0] ?? '';
                                            $docente = $partes[1] ?? '';
                                        @endphp
                                        <button
                                            type="button"
                                            class="slot-assigned btn-edit-slot"
                                            data-dia="{{ $dia }}"
                                            data-bloque="{{ $bloque->id }}"
                                            data-asignacion-id="{{ $slotValue }}"
                                        >
                                            <strong>{{ $materia }}</strong><br>
                                            <span class="hint">{{ $docente }}</span>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-ghost btn-assign" data-dia="{{ $dia }}" data-bloque="{{ $bloque->id }}">+</button>
                                    @endif
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="actions" style="margin-top: 12px;">
        <span class="hint">Los cambios se guardan al usar "Crear y asignar".</span>
    </div>
</div>
