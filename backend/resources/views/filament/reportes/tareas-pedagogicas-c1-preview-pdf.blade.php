<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Planilla de Profesores Afectados a Tareas Pedagógicas - C1</title>
    <style>
        @page {
            size: portrait;
            margin: 1.2cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
            padding: 0;
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
            text-align: right;
            font-size: 9px;
            margin-bottom: 8px;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 6px 5px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background: #111827;
            color: #ffffff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        td {
            font-size: 9px;
        }

        .col-anexo { width: 22%; }
        .col-curso { width: 20%; }
        .col-materia { width: 33%; }
        .col-docente { width: 25%; }

        .empty {
            margin-top: 18px;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
    </style>
</head>
<body>
   <table class="header-table">
    <tr>
        <td class="text-left" style="width: 15%;">
           <img src="{{ public_path('images/Olga.png') }}" alt="Logo" style="width: 60px;">
        </td>
        <td class="text-center" style="width: 70%;">
            <h2>PLANILLA DE PROFESORES AFECTADOS A TAREAS PEDAGÓGICAS</h2>
   
        </td>
        <td class="text-right" style="width: 15%;">
            <img src="{{ public_path('images/escudo-argentina.png') }}" alt="Logo" style="width: 50px;">
        </td>
    </tr>
</table>

    <div class="meta">
        Generado: {{ $generadoEn->format('d/m/Y H:i') }}
    </div>

    @if($registros->isEmpty())
        <div class="empty">No se encontraron asignaciones para tareas pedagógicas en el primer cuatrimestre.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th class="col-anexo">Anexo</th>
                    <th class="col-curso">Curso</th>
                    <th class="col-materia">Materia</th>
                    <th class="col-docente">Docente</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registros as $registro)
                    <tr>
                        <td>{{ $registro['anexo'] }}</td>
                        <td>{{ $registro['curso'] }}</td>
                        <td>{{ $registro['materia'] }}</td>
                        <td>{{ $registro['docente'] ?: 'Sin docente' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
