<header class="top-bar">
    <div>
        <h1>Gestión de Horarios</h1>
        <div class="context-info">
            Curso: <strong>{{ $cursoEtapa->curso->nombre }} {{ $cursoEtapa->curso->division }}</strong>
            <span style="margin: 0 8px opacity: 0.3">|</span>
            Etapa: <strong>{{ $cursoEtapa->etapa->nombre }}</strong>
        </div>
    </div>
    <a href="{{ url()->previous() }}" class="btn btn-outline">← Volver</a>
</header>