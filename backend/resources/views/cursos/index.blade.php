<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        <div class="tabs" style="display:flex; gap:8px; margin-bottom:12px;">
            <button type="button" id="tab-grilla-btn" class="btn btn-back" aria-pressed="true">Grilla semanal</button>
            <button type="button" id="tab-asign-btn" class="btn btn-back" aria-pressed="false">Asignación docentes</button>
        </div>

        @if(session('status'))
            <div class="flash flash-ok">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="flash flash-danger">
                <strong>Revisá los datos cargados:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="form-grilla" method="POST" action="{{ route('curso-etapas.horarios.store', ['cursoEtapa' => $cursoEtapa]) }}">
            @csrf
            <input type="hidden" name="save_section" value="grilla">
            @include('cursos.grilla-semanal')
        </form>

        <form id="form-asign" method="POST" action="{{ route('curso-etapas.horarios.store', ['cursoEtapa' => $cursoEtapa]) }}" style="display:none;">
            @csrf
            <input type="hidden" name="save_section" value="asignaciones">
            @include('cursos.asignacion-docentes')
        </form>

        <script>
            (function() {
                const grillaBtn = document.getElementById('tab-grilla-btn');
                const asignBtn = document.getElementById('tab-asign-btn');
                const formGrilla = document.getElementById('form-grilla');
                const formAsign = document.getElementById('form-asign');

                function showGrilla() {
                    formGrilla.style.display = '';
                    formAsign.style.display = 'none';
                    grillaBtn.classList.add('btn-save');
                    asignBtn.classList.remove('btn-save');
                    grillaBtn.setAttribute('aria-pressed', 'true');
                    asignBtn.setAttribute('aria-pressed', 'false');
                }

                function showAsign() {
                    formGrilla.style.display = 'none';
                    formAsign.style.display = '';
                    asignBtn.classList.add('btn-save');
                    grillaBtn.classList.remove('btn-save');
                    asignBtn.setAttribute('aria-pressed', 'true');
                    grillaBtn.setAttribute('aria-pressed', 'false');
                }

                grillaBtn.addEventListener('click', showGrilla);
                asignBtn.addEventListener('click', showAsign);

                // Default: mostrar grilla, o la sección enviada previamente
                const initial = '{{ old("save_section", "grilla") }}';
                if (initial === 'asignaciones') {
                    showAsign();
                } else {
                    showGrilla();
                }
            })();
        </script>

        <!-- Modal para crear asignación rápida -->
        <div id="modal-asignar" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); align-items:center; justify-content:center;">
            <div style="background:#fff; width:520px; max-width:96%; border-radius:8px; padding:16px;">
                <h3 style="margin-top:0;">Crear asignación rápida</h3>
                <form id="form-ajax-asign" style="display:flex; flex-direction:column; gap:8px;">
                    <label>
                        Materia
                        <select name="curso_etapa_materia_id" required>
                            @foreach($cursoEtapaMaterias as $cem)
                                <option value="{{ $cem->id }}">{{ $cem->cursoMateria?->materia?->nombre ?? ('Materia #' . $cem->id) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label style="position:relative;">
                        Docente
                        <input type="text" id="docente-search" placeholder="Buscar docente por apellido o nombre" autocomplete="off" required>
                        <input type="hidden" name="docente_id" id="docente-id">
                        <div id="docente-suggestions" style="position:absolute; left:0; right:0; background:#fff; border:1px solid #ddd; max-height:200px; overflow:auto; z-index:40; display:none;"></div>
                    </label>
                    <label>
                        Situación revista
                        <select name="situacion_revista" required>
                            <option value="INT">INT - Titular</option>
                            <option value="SUP">SUP - Suplente</option>
                            <option value="PRO">PRO - Provisional</option>
                        </select>
                    </label>
                    <label>
                        Fecha desde
                        <input type="date" name="fecha_desde" required>
                    </label>
                    <label>
                        Hasta
                        <input type="text" name="hasta" maxlength="50" placeholder="Ejemplo: fin del ciclo">
                    </label>
                    <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:6px;">
                        <button type="button" id="modal-cancel" class="btn btn-back">Cancelar</button>
                        <button type="submit" class="btn btn-save">Crear y asignar</button>
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
            </div>
        </div>

        <script>
            (function(){
                const modal = document.getElementById('modal-asignar');
                const formAjax = document.getElementById('form-ajax-asign');
                const cancelBtn = document.getElementById('modal-cancel');
                let targetSelect = null;

                function openModalFor(selectEl) {
                    targetSelect = selectEl;
                    modal.style.display = 'flex';
                }

                function closeModal() {
                    modal.style.display = 'none';
                    targetSelect = null;
                    formAjax.reset();
                }

                cancelBtn.addEventListener('click', closeModal);

                // Delegación: abrir modal cuando se hace click en botón .btn-assign
                document.addEventListener('click', function(e){
                    const btn = e.target.closest('.btn-assign');
                    if (!btn) return;
                    // Encontrar select en misma celda
                    const cell = btn.closest('td');
                    const select = cell ? cell.querySelector('select.slot-select') : null;
                    if (!select) return;
                    openModalFor(select);
                });

                // Inicializar datos de docentes para autocompletar
                const DOCENTES = @json($docentes->map(fn($d) => ['id' => $d->id, 'label' => trim(($d->apellido ?? '') . ', ' . ($d->nombre ?? ''). ' ( dni: ' . ($d->dni ?? 'sin doc') . ')')]));

                (function initDocenteSearch(){
                    function renderSuggestionsFor(inputEl, suggestionsEl, items){
                        suggestionsEl.innerHTML = '';
                        if (!items.length) { suggestionsEl.style.display = 'none'; return; }
                        for (const it of items) {
                            const div = document.createElement('div');
                            div.textContent = it.label;
                            div.style.padding = '6px 8px';
                            div.style.cursor = 'pointer';
                            div.addEventListener('click', function(){
                                inputEl.value = it.label;
                                // find hidden input in same container
                                const hidden = inputEl.parentElement.querySelector('input[type="hidden"]');
                                if (hidden) hidden.value = it.id;
                                suggestionsEl.style.display = 'none';
                            });
                            suggestionsEl.appendChild(div);
                        }
                        suggestionsEl.style.display = '';
                    }

                    document.addEventListener('input', function(e){
                        const el = e.target;
                        if (!el.matches('.docente-search-row') && el.id !== 'docente-search') return;
                        const q = el.value.trim().toLowerCase();
                        // determine suggestions container
                        let suggestionsEl;
                        if (el.id === 'docente-search') {
                            suggestionsEl = document.getElementById('docente-suggestions');
                        } else {
                            suggestionsEl = el.parentElement.querySelector('.docente-suggestions-row');
                        }
                        if (!suggestionsEl) return;
                        if (!q) { suggestionsEl.style.display = 'none'; return; }
                        const results = DOCENTES.filter(d => d.label.toLowerCase().includes(q)).slice(0, 20);
                        renderSuggestionsFor(el, suggestionsEl, results);
                    });

                    // cerrar sugerencias al click fuera
                    document.addEventListener('click', function(e){
                        if (!e.target.closest('#docente-search') && !e.target.closest('#docente-suggestions')) {
                            // hide modal suggestions
                            const modalSug = document.getElementById('docente-suggestions');
                            if (modalSug) modalSug.style.display = 'none';
                        }
                        // hide row suggestions
                        if (!e.target.closest('.docente-search-row') && !e.target.closest('.docente-suggestions-row')) {
                            document.querySelectorAll('.docente-suggestions-row').forEach(s => s.style.display = 'none');
                        }
                    });
                })();

                formAjax.addEventListener('submit', async function(e){
                    e.preventDefault();
                    if (!targetSelect) return;

                    const docenteIdVal = document.getElementById('docente-id').value;
                    if (!docenteIdVal) {
                        alert('Seleccioná un docente de la lista.');
                        return;
                    }

                    const url = '{{ route("curso-etapas.asignaciones.ajax", ["cursoEtapa" => $cursoEtapa]) }}';
                    const formData = new FormData(formAjax);

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        if (!res.ok) {
                            const err = await res.json().catch(()=>({error:'Error'}));
                            alert(err.error || 'Error al crear asignación');
                            return;
                        }

                        const data = await res.json();

                        // Añadir opción a select objetivo y seleccionarla
                        const opt = document.createElement('option');
                        opt.value = data.id;
                        opt.textContent = data.etiqueta;
                        targetSelect.appendChild(opt);
                        targetSelect.value = data.id;

                        closeModal();
                    } catch (err) {
                        console.error(err);
                        alert('Error al crear asignación');
                    }
                });
            })();
        </script>
    </div>
</body>
</html>
