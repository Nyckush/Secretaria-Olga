(function () {
    function debounce(fn, wait) {
        let t;
        return function (...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    function createList() {
        const list = document.createElement('div');
        list.className = 'autocomplete-list';
        return list;
    }

    async function fetchDocentes(q) {
        const url = new URL('/admin/api/docentes', location.origin);
        url.searchParams.set('q', q);
        const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return [];
        return await res.json();
    }

    function bindAutocomplete(input) {
        const targetSelector = input.dataset.target;
        if (!targetSelector) return;
        const hidden = document.querySelector(targetSelector);
        const wrapper = document.createElement('div');
        wrapper.style.position = 'relative';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        const list = createList();
        wrapper.appendChild(list);

        let items = [];
        let selected = -1;

        const render = () => {
            list.innerHTML = '';
            if (!items.length) { list.style.display = 'none'; return; }
            list.style.display = 'block';
            items.forEach((it, idx) => {
                const el = document.createElement('div');
                el.className = 'autocomplete-item' + (idx === selected ? ' active' : '');
                el.textContent = it.text;
                el.dataset.id = it.id;
                el.addEventListener('mousedown', (ev) => {
                    ev.preventDefault();
                    choose(idx);
                });
                list.appendChild(el);
            });
        };

        const choose = (idx) => {
            const it = items[idx];
            if (!it) return;
            input.value = it.text;
            hidden.value = it.id;
            items = [];
            selected = -1;
            render();
        };

        const doSearch = debounce(async function () {
            const q = input.value.trim();
            if (q.length < 1) { items = []; render(); return; }
            items = await fetchDocentes(q);
            selected = -1;
            render();
        }, 250);

        input.addEventListener('input', () => {
            hidden.value = '';
            doSearch();
        });

        input.addEventListener('keydown', (e) => {
            if (!items.length) return;
            if (e.key === 'ArrowDown') { e.preventDefault(); selected = Math.min(selected + 1, items.length - 1); render(); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); selected = Math.max(selected - 1, 0); render(); }
            else if (e.key === 'Enter') { e.preventDefault(); choose(selected === -1 ? 0 : selected); }
            else if (e.key === 'Escape') { items = []; render(); }
        });

        document.addEventListener('click', (ev) => {
            if (!wrapper.contains(ev.target)) { items = []; render(); }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.docente-autocomplete').forEach(bindAutocomplete);
    });
})();

    // Tabs: mostrar/ocultar secciones (Grilla / Asignación)
    (function () {
        function selectTab(name) {
            const tabs = ['grilla', 'asignacion'];
            tabs.forEach(t => {
                const el = document.getElementById('tab-' + t);
                const btn = document.querySelector('[data-tab="' + t + '"]');
                if (!el || !btn) return;
                if (t === name) {
                    el.classList.remove('hidden');
                    btn.classList.add('tab-active');
                } else {
                    el.classList.add('hidden');
                    btn.classList.remove('tab-active');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-tab]').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const tab = this.dataset.tab;
                    selectTab(tab);
                });
            });

            // Default: mostrar grilla
            selectTab('grilla');
        });
    })();