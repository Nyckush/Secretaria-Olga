<!doctype html>
<html lang="es" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Horarios - {{ $cursoEtapa->curso->nombre }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/horarios.css') }}">
</head>
<body>
    <div class="container">
        @include('cursos.partials.topbar')

        <div class="tabs" role="tablist" aria-label="Secciones Horarios">
            <button class="tab-btn" data-tab="grilla" role="tab" aria-controls="tab-grilla">Grilla Semanal</button>
            <button class="tab-btn" data-tab="asignacion" role="tab" aria-controls="tab-asignacion">Asignación de Docentes</button>
        </div>

        <form method="POST" action="{{ route('curso-etapas.horarios.store', ['cursoEtapa' => $cursoEtapa]) }}">
            @csrf

            <div id="tab-grilla">
                @include('cursos.partials.grilla')
            </div>

            <div id="tab-asignacion" class="hidden">
                @include('cursos.partials.asignacion')
            </div>

            @include('cursos.partials.form-actions')

        </form>
    </div>

    <script src="{{ asset('js/horarios.js') }}" defer></script>
</body>
</html>