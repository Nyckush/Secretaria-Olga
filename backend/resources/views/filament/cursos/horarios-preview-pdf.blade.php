<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vista previa de horarios</title>
    <style>
        @page {
            size: portrait;
            margin: 1cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 8px;
            margin-bottom: 10px;
            
        }



        .header-table {
        width: 100%;
        border: none;
        border-bottom: 1px solid #d1d5db;
        margin-bottom: 10px;
    }
    .header-table td {
        border: none; /* Quitamos los bordes de la tabla de diseño */
        vertical-align: middle;
        padding: 5px;
    }
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }

        .header h2 { font-size: 14px; margin: 2px 0; }
        .header h3 { font-size: 12px; margin: 2px 0; }

        .meta {
            margin-bottom: 10px;
            text-align: center;
            font-size: 14px;
            line-height: 1.4;
        }

        .periodo {
            margin-top: 15px; /* Espaciado para separar de la tabla anterior */
            margin-bottom: 8px;
            background: #f3f4f6;
            text-align: center;
            padding: 6px;
            font-weight: bold;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px; /* Espacio después de cada tabla */
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 4px 2px;
            vertical-align: middle;
            text-align: center;
            word-wrap: break-word;
        }

        th {
            background: #39a6ef;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
        }

        .bloque {
            width: 60px;
            background: #f9fafb;
            font-size: 9px;
            font-weight: bold;
        }

        .celda {
            height: 45px;
        }

        .materia {
            display: block;
            font-weight: bold;
            font-size: 9px;
            line-height: 1.1;
        }

        .docente {
            display: block;
            margin-top: 2px;
            font-size: 8px;
            color: #4b5563;
            line-height: 1.1;
        }
        
        /* Evita que una tabla se parta a la mitad entre dos hojas si es posible */
        table { page-break-inside: avoid; }
    </style>
</head>
<body>
   <table class="header-table">
    <tr>
        <td class="text-left" style="width: 15%;">
           <img src="{{ public_path('images/Olga.png') }}" alt="Logo" style="width: 60px;">
        </td>
        <td class="text-center" style="width: 70%;">
            <h2>COLEGIO SECUNDARIO N°59 "OLGA M. DE AREDEZ"</h2>
            <h3>MODALIDAD DE JOVENES Y ADULTOS</h3>
        </td>
        <td class="text-right" style="width: 15%;">
            <img src="{{ public_path('images/escudo-argentina.png') }}" alt="Logo" style="width: 50px;">
        </td>
    </tr>
</table>

    <div class="meta">
        <strong>Anexo:</strong> {{ $curso->anexo->nombre ?? ($cursoEtapas->first()->curso->anexo->nombre ?? 'N/D') }} |
        <strong>Curso:</strong> {{ $curso->nombre }}
        @if(filled($curso->division))
            - División {{ $curso->division }}
        @endif
        | <strong>Ciclo:</strong> {{ $curso->ciclo_lectivo ?? 'N/D' }}
        
    </div>

    {{-- Bucle de etapas --}}
    @foreach($cursoEtapas as $cursoEtapa)
        
        @php
            $hasContenido = false;
            foreach (['C1', 'C2'] as $periodoKey) {
                foreach ($bloques as $bloque) {
                    foreach ($diasSemana as $dia) {
                        $cell = data_get($matricesPorCursoEtapa, "{$cursoEtapa->id}.{$periodoKey}.{$dia}.{$bloque->id}");
                        if (!empty($cell['materia'] ?? null)) {
                            $hasContenido = true;
                            break 3;
                        }
                    }
                }
            }
        @endphp

        @if($hasContenido)
            <div class="periodo">
                {{ $cursoEtapa->etapa->nombre }} 
                @if(filled($cursoEtapa->modulo->nombre)) - Módulo: {{ $cursoEtapa->modulo->nombre }}@endif
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">HORARIO</th>
                        @foreach($diasSemana as $dia)
                            <th>{{ strtoupper(substr($dia, 0, 3)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($bloques as $bloque)
                        <tr>
                            <td class="bloque">
                                {{ substr((string) $bloque->hora_inicio, 0, 5) }}<br>
                                {{ substr((string) $bloque->hora_fin, 0, 5) }}
                            </td>
                            @foreach($diasSemana as $dia)
                                @php
                                    $itemC1 = data_get($matricesPorCursoEtapa, "{$cursoEtapa->id}.C1.{$dia}.{$bloque->id}");
                                    // Podrías sumar C2 aquí si fuera necesario
                                @endphp
                                <td class="celda">
                                    @if(filled($itemC1['materia'] ?? null))
                                        <span class="materia">{{ $itemC1['materia'] }}</span>
                                        <span class="docente">{{ $itemC1['docente'] ?: 'Sin docente' }}</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

</body>
</html>