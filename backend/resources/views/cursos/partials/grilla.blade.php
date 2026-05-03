<section class="card">
    <div class="card-header">
        <h2 class="card-title"><span>02.</span> Grilla Semanal</h2>
        <p class="card-desc">Distribuye las materias en los bloques horarios disponibles.</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="time-col">Horario</th>
                    @foreach($diasSemana as $dia)
                        <th>{{ $dia }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($bloques as $bloque)
                    <tr>
                        <td class="time-col">
                            Bloque {{ $bloque->orden }}
                            <span class="time-range">{{ $bloque->hora_inicio }} - {{ $bloque->hora_fin }}</span>
                        </td>
                        @foreach($diasSemana as $dia)
                            <td>
                                <select name="slots[{{ $dia }}][{{ $bloque->id }}]" style="font-size: 0.75rem;">
                                    <option value="">- Vacío -</option>
                                    @foreach($asignacionesOpciones as $asignacionId => $etiqueta)
                                        <option value="{{ $asignacionId }}" @selected((string) ($slots[$dia][$bloque->id] ?? '') === (string) $asignacionId)>
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
</section>