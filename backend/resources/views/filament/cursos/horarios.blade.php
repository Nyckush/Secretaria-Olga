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
            @include('filament.cursos.grilla-semanal')
        </form>

        <script>
            (function() {
                const formGrilla = document.getElementById('form-grilla');

                // Mantener la referencia usada por el resto del script.
                if (formGrilla) {
                    formGrilla.style.display = '';
                }
            })();
        </script>

        <!-- Modal para crear asignación rápida -->
        <div id="modal-asignar" class="formModal">
            <div class="modalForm">
                <h3 id="modal-title" style="margin-top:0;">Crear asignación rápida</h3>
                <form id="form-ajax-asign" >
                    <input type="hidden" name="asignacion_id" id="asignacion-id">
                  <label>
                        Materia
                        <select name="curso_etapa_materia_id" id="materia-select" required>
                            <option value="" selected disabled hidden>Sin Materia asignada</option>

                            @foreach($cursoEtapaMaterias as $cem)
                                <option value="{{ $cem->id }}">
                                    {{ $cem->cursoMateria?->materia?->nombre ?? ('Materia #' . $cem->id) }}
                                </option>
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
                        Hs. Cátedra
                        <input type="number" name="horas_catedra" id="horas-catedra" min="0" max="255" step="1" placeholder="0">
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
                const formGrilla = document.getElementById('form-grilla');
                const cancelBtn = document.getElementById('modal-cancel');
                const modalTitle = document.getElementById('modal-title');
                const submitBtn = formAjax.querySelector('button[type="submit"]');
                const submitBtnCreateText = submitBtn ? submitBtn.textContent : 'Crear y asignar';
                const submitBtnEditText = 'Guardar cambios';
                let submitBtnActionText = submitBtnCreateText;
                const draftKey = 'curso-etapa-asignacion-draft-{{ $cursoEtapa->id }}';
                const materiaSelect = document.getElementById('materia-select');
                const docenteSearchInput = document.getElementById('docente-search');
                const docenteIdInput = document.getElementById('docente-id');
                const asignacionIdInput = document.getElementById('asignacion-id');
                const situacionRevistaInput = document.getElementById('situacion-revista');
                const fechaDesdeInput = document.getElementById('fecha-desde');
                const horasCatedraInput = document.getElementById('horas-catedra');
                const hastaInput = document.getElementById('hasta');
                let isSubmitting = false;
                let targetInput = null;
                let targetCell = null;
                let targetDia = null;
                let targetBloque = null;

                function setSubmitting(state) {
                    isSubmitting = state;

                    if (submitBtn) {
                        submitBtn.disabled = state;
                        submitBtn.textContent = state ? 'Guardando...' : submitBtnActionText;
                    }

                    if (cancelBtn) {
                        cancelBtn.disabled = state;
                    }

                    [materiaSelect, docenteSearchInput, situacionRevistaInput, fechaDesdeInput, horasCatedraInput, hastaInput]
                        .forEach(function (el) {
                            if (el) {
                                el.disabled = state;
                            }
                        });
                }

                function saveDraft() {
                    const draft = {
                        asignacion_id: asignacionIdInput?.value || '',
                        curso_etapa_materia_id: materiaSelect?.value || '',
                        docente_id: docenteIdInput?.value || '',
                        docente_label: docenteSearchInput?.value || '',
                        situacion_revista: situacionRevistaInput?.value || '',
                        fecha_desde: fechaDesdeInput?.value || '',
                        horas_catedra: horasCatedraInput?.value || '',
                        hasta: hastaInput?.value || '',
                    };

                    try {
                        sessionStorage.setItem(draftKey, JSON.stringify(draft));
                    } catch (error) {
                        // Ignorar errores de almacenamiento local.
                    }
                }

                function restoreDraft() {
                    try {
                        const raw = sessionStorage.getItem(draftKey);

                        if (!raw) {
                            return;
                        }

                        const draft = JSON.parse(raw);

                        if (asignacionIdInput) {
                            asignacionIdInput.value = draft.asignacion_id || '';
                        }

                        if (materiaSelect && draft.curso_etapa_materia_id) {
                            materiaSelect.value = draft.curso_etapa_materia_id;
                        }

                        if (docenteSearchInput) {
                            docenteSearchInput.value = draft.docente_label || '';
                        }

                        if (docenteIdInput) {
                            docenteIdInput.value = draft.docente_id || '';
                        }

                        if (situacionRevistaInput && draft.situacion_revista) {
                            situacionRevistaInput.value = draft.situacion_revista;
                        }

                        if (fechaDesdeInput) {
                            fechaDesdeInput.value = draft.fecha_desde || '';
                        }

                        if (horasCatedraInput) {
                            horasCatedraInput.value = draft.horas_catedra || '';
                        }

                        if (hastaInput) {
                            hastaInput.value = draft.hasta || '';
                        }
                    } catch (error) {
                        // Ignorar borradores inválidos.
                    }
                }

                function setModalMode(mode) {
                    const isEditMode = mode === 'edit';
                    submitBtnActionText = isEditMode ? submitBtnEditText : submitBtnCreateText;

                    if (modalTitle) {
                        modalTitle.textContent = isEditMode ? 'Editar asignación' : 'Crear asignación rápida';
                    }

                    if (submitBtn && !isSubmitting) {
                        submitBtn.textContent = submitBtnActionText;
                    }
                }

                function fillFormFromAsignacion(asignacion) {
                    if (!asignacion) {
                        return;
                    }

                    asignacionIdInput.value = asignacion.id || '';
                    materiaSelect.value = asignacion.curso_etapa_materia_id || '';
                    docenteIdInput.value = asignacion.docente_id || '';

                    const docente = DOCENTES.find(d => String(d.id) === String(asignacion.docente_id));
                    docenteSearchInput.value = docente ? docente.label : '';
                    situacionRevistaInput.value = asignacion.situacion_revista || 'INT';
                    fechaDesdeInput.value = asignacion.fecha_desde || '';
                    horasCatedraInput.value = asignacion.horas_catedra ?? '';
                    hastaInput.value = asignacion.hasta || '';
                }

                function openModalFor(inputEl, options = {}) {
                    targetInput = inputEl;

                    if (options.assignmentId) {
                        setModalMode('edit');
                        const asignacion = ASIGNACIONES_POR_ID[String(options.assignmentId)] || ASIGNACIONES_POR_ID[options.assignmentId];
                        fillFormFromAsignacion(asignacion);
                    } else {
                        setModalMode('create');
                        restoreDraft();
                        if (asignacionIdInput) {
                            asignacionIdInput.value = '';
                        }
                    }

                    modal.style.display = 'flex';
                }

                function closeModal() {
                    if (isSubmitting) {
                        return;
                    }

                    modal.style.display = 'none';
                    targetInput = null;
                    targetCell = null;
                    targetDia = null;
                    targetBloque = null;
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

                document.addEventListener('click', function(e){
                    const btn = e.target.closest('.btn-edit-slot');
                    if (!btn) return;
                    const cell = btn.closest('td');
                    const input = cell ? cell.querySelector('input.slot-value') : null;
                    if (!input) return;
                    targetCell = cell;
                    targetDia = btn.dataset.dia;
                    targetBloque = btn.dataset.bloque;
                    openModalFor(input, { assignmentId: btn.dataset.asignacionId });
                });



                const DOCENTES = @json($docentes->map(fn($d) => ['id' => $d->id, 'label' => trim(($d->apellido ?? '') . ', ' . ($d->nombre ?? ''). ' ( dni: ' . ($d->dni ?? 'sin doc') . ')')]));
                const ASIGNACIONES_POR_MATERIA = @json($asignacionesPorMateria);
                const ASIGNACIONES_POR_ID = @json($asignacionesPorId);
                const HORAS_POR_MATERIA = @json($cursoEtapaMaterias->mapWithKeys(fn($cem) => [$cem->id => $cem->horas_catedra]));

                function autocompletarHorasPorMateria(cemId, force = false) {
                    if (!cemId || !horasCatedraInput) {
                        return;
                    }

                    const asignacion = ASIGNACIONES_POR_MATERIA[cemId];
                    const horasSugeridas = asignacion?.horas_catedra ?? (HORAS_POR_MATERIA[cemId] ?? '');

                    if (force || horasCatedraInput.value === '') {
                        horasCatedraInput.value = horasSugeridas;
                    }
                }

                // Autocomplete al cambiar materia
                materiaSelect.addEventListener('change', function() {
                    const cemId = this.value;
                    const asignacion = ASIGNACIONES_POR_MATERIA[cemId];
                    if (asignacion) {
                        // Llenar docente
                        const docente = DOCENTES.find(d => d.id == asignacion.docente_id);
                        if (docente) {
                            docenteSearchInput.value = docente.label;
                            docenteIdInput.value = docente.id;
                        }
                        // Situación revista
                        situacionRevistaInput.value = asignacion.situacion_revista;
                        // Fecha desde
                        fechaDesdeInput.value = asignacion.fecha_desde;
                        // Hs. cátedra
                        autocompletarHorasPorMateria(cemId, true);
                        // Hasta
                        hastaInput.value = asignacion.hasta || '';



                    } else {
                        // Limpiar si no hay asignación
                        docenteSearchInput.value = '';
                        docenteIdInput.value = '';
                        situacionRevistaInput.value = '';
                        fechaDesdeInput.value = '';
                        autocompletarHorasPorMateria(cemId, true);
                        hastaInput.value = '';
                    }

                    saveDraft();
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
                                saveDraft();
                            });
                            suggestionsEl.appendChild(div);
                        }
                        suggestionsEl.style.display = '';
                    }

                    document.addEventListener('input', function(e){
                        const el = e.target;
                        if (el.id !== 'docente-search') return;
                        const q = el.value.trim().toLowerCase();
                        const suggestionsEl = document.getElementById('docente-suggestions');
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
                    });
                })();

                [docenteSearchInput, situacionRevistaInput, fechaDesdeInput, horasCatedraInput, hastaInput].forEach(function (el) {
                    if (!el) {
                        return;
                    }

                    const eventName = el.tagName === 'SELECT' ? 'change' : 'input';
                    el.addEventListener(eventName, saveDraft);
                });

                restoreDraft();
                setModalMode('create');
                autocompletarHorasPorMateria(materiaSelect?.value);

                formAjax.addEventListener('submit', async function(e){
                    e.preventDefault();
                    if (!targetInput) return;
                    if (isSubmitting) return;

                    const docenteIdVal = docenteIdInput.value;
                    if (!docenteIdVal) {
                        alert('Seleccioná un docente de la lista.');
                        return;
                    }

                    const url = '{{ route("curso-etapas.asignaciones.ajax", ["cursoEtapa" => $cursoEtapa]) }}';
                    const formData = new FormData(formAjax);
                    setSubmitting(true);
                    saveDraft();

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
                            setSubmitting(false);
                            return;
                        }

                        const data = await res.json();

                        targetInput.value = String(data.id);

                        // Actualizar visualmente la celda
                        if (targetCell) {
                            const partes = data.etiqueta.split(' - ');
                            const materia = partes[0] || '';
                            const docente = partes[1] || '';
                            const newContent = `<button type="button" class="slot-assigned btn-edit-slot" data-dia="${targetDia}" data-bloque="${targetBloque}" data-asignacion-id="${data.id}"><strong>${materia}</strong><br><span class="hint">${docente}</span></button>`;
                            targetCell.innerHTML = `<div style="display:flex; gap:6px; align-items:start; flex-direction:column;"><input type="hidden" name="slots[${targetDia}][${targetBloque}]" class="slot-value" value="${data.id}">${newContent}</div>`;
                        }

                        if (formGrilla) {
                            formGrilla.submit();
                            return;
                        }

                        setSubmitting(false);
                        closeModal();
                    } catch (err) {
                        console.error(err);
                        alert('Error al crear asignación');
                        setSubmitting(false);
                    }
                });
            })();
        </script>
    </div>
</x-filament-panels::layout.index>
