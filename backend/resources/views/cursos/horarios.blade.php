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
@include('cursos.index')
        <form method="POST" action="{{ route('curso-etapas.horarios.store', ['cursoEtapa' => $cursoEtapa]) }}">
