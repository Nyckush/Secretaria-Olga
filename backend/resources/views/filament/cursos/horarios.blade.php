<x-filament-panels::layout.index>
    <meta name="csrf-token" content="{{ csrf_token() }}">
  
 
        @include('filament.cursos.horariosStilos')



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
            @include('filament.cursos.grilla-semanal')
        </form>

        <form id="form-asign" method="POST" action="{{ route('curso-etapas.horarios.store', ['cursoEtapa' => $cursoEtapa]) }}" style="display:none;">
            @csrf
            <input type="hidden" name="save_section" value="asignaciones">
            @include('filament.cursos.asignacion-docentes')
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

                const initial = '{{ old("save_section", "grilla") }}';
                if (initial === 'asignaciones') {
                    showAsign();
                } else {
                    showGrilla();
                }
            })();
        </script>

        <!-- Modal para crear asignación rápida -->
        <div id="modal-asignar" class="formModal">
            <div class="modalForm">
                <h3 style="margin-top:0;">Crear asignación rápida</h3>
                <form id="form-ajax-asign" >
                    <label>
                        Materia
                        <select name="curso_etapa_materia_id" id="materia-select" required>
                            @foreach($cursoEtapaMaterias as $cem)
                                <option value="{{ $cem->id }}">{{ $cem->cursoMateria?->materia?->nombre ?? ('Materia #' . $cem->id) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label style="position:relative;">
                        Docente
                        <input type="text" id="docente-search" placeholder="Buscar docente por apellido o nombre" autocomplete="off" required>
                        <input type="hidden" name="docente_id" id="docente-id">
                        <div id="docente-suggestions" class="docente-suggestionsModal"></div>
                    </label>
                    <label>
                        Situación revista
                        <select name="situacion_revista" id="situacion-revista" required>
                            <option value="INT">INT - Titular</option>
                            <option value="SUP">SUP - Suplente</option>
                            <option value="PRO">PRO - Provisional</option>
                        </select>
                    </label>
                    <label>
                        Fecha desde
                        <input type="date" name="fecha_desde" id="fecha-desde" required>
                    </label>
                    <label>
                        Hasta
                        <input type="text" name="hasta" id="hasta" maxlength="50" placeholder="Ejemplo: fin del ciclo">
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
                let targetInput = null;
                let targetCell = null;
                let targetDia = null;
                let targetBloque = null;

                function openModalFor(inputEl) {
                    targetSelect = null; // No hay select en slots vacíos
                    targetInput = inputEl;
                    modal.style.display = 'flex';
                }

                function closeModal() {
                    modal.style.display = 'none';
                    targetSelect = null;
                    targetInput = null;
                    targetCell = null;
                    targetDia = null;
                    targetBloque = null;
                    formAjax.reset();
                }

                cancelBtn.addEventListener('click', closeModal);

                document.addEventListener('click', function(e){
                    const btn = e.target.closest('.btn-assign');
                    if (!btn) return;
                    const cell = btn.closest('td');
                    const input = cell ? cell.querySelector('input.slot-value') : null;
                    if (!input) return;
                    targetCell = cell;
                    targetDia = btn.dataset.dia;
                    targetBloque = btn.dataset.bloque;
                    openModalFor(input);
                });

                document.addEventListener('change', function(e){
                    const select = e.target.closest('select.slot-select');
                    if (!select) return;
                    const cell = select.closest('td');
                    const input = cell ? cell.querySelector('input.slot-value') : null;
                    if (!input) return;
                    input.value = select.value || '';
                });



                const DOCENTES = @json($docentes->map(fn($d) => ['id' => $d->id, 'label' => trim(($d->apellido ?? '') . ', ' . ($d->nombre ?? ''). ' ( dni: ' . ($d->dni ?? 'sin doc') . ')')]));
                const ASIGNACIONES_POR_MATERIA = @json($asignacionesPorMateria);

                // Autocomplete al cambiar materia
                document.getElementById('materia-select').addEventListener('change', function() {
                    const cemId = this.value;
                    const asignacion = ASIGNACIONES_POR_MATERIA[cemId];
                    if (asignacion) {
                        // Llenar docente
                        const docente = DOCENTES.find(d => d.id == asignacion.docente_id);
                        if (docente) {
                            document.getElementById('docente-search').value = docente.label;
                            document.getElementById('docente-id').value = docente.id;
                            console.log('Asignación encontrada para materia', cemId, '-> Docente:', docente.label);
                        }
                        // Situación revista
                        document.getElementById('situacion-revista').value = asignacion.situacion_revista;
                        // Fecha desde
                        document.getElementById('fecha-desde').value = asignacion.fecha_desde;
                        // Hasta
                        document.getElementById('hasta').value = asignacion.hasta || '';



                    } else {
                        // Limpiar si no hay asignación
                        document.getElementById('docente-search').value = '';
                        document.getElementById('docente-id').value = '';
                        document.getElementById('situacion-revista').value = '';
                        document.getElementById('fecha-desde').value = '';
                        document.getElementById('hasta').value = '';
                    }
                });

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

                    document.addEventListener('click', function(e){
                        if (!e.target.closest('#docente-search') && !e.target.closest('#docente-suggestions')) {
                            const modalSug = document.getElementById('docente-suggestions');
                            if (modalSug) modalSug.style.display = 'none';
                        }
                        if (!e.target.closest('.docente-search-row') && !e.target.closest('.docente-suggestions-row')) {
                            document.querySelectorAll('.docente-suggestions-row').forEach(s => s.style.display = 'none');
                        }
                    });
                })();

                formAjax.addEventListener('submit', async function(e){
                    e.preventDefault();
                    if (!targetInput) return;

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

                        const opt = document.createElement('option');
                        opt.value = data.id;
                        opt.textContent = data.etiqueta;
                        if (targetSelect) {
                            targetSelect.appendChild(opt);
                            targetSelect.value = String(data.id);
                        }

                        targetInput.value = String(data.id);

                        // Actualizar visualmente la celda
                        if (targetCell) {
                            const partes = data.etiqueta.split(' - ');
                            const materia = partes[0] || '';
                            const docente = partes[1] || '';
                            const newContent = `<div><strong>${materia}</strong><br><span class="hint">${docente}</span></div>`;
                            targetCell.innerHTML = `<div style="display:flex; gap:6px; align-items:start; flex-direction:column;"><input type="hidden" name="slots[${targetDia}][${targetBloque}]" class="slot-value" value="${data.id}">${newContent}</div>`;
                        }

                        closeModal();
                    } catch (err) {
                        console.error(err);
                        alert('Error al crear asignación');
                    }
                });
            })();
        </script>
    </div>
</x-filament-panels::layout.index>
