# Copilot instructions

## Repository entrypoint

- The actual application lives in `backend/`. Run Composer, Artisan, PHPUnit, Pint, and npm commands from that directory.
- This is a Laravel 12 application with a Filament 4 admin panel and Excel import/export support via `maatwebsite/excel`.

## Build, test, and lint

Run these from `backend/`:

```bash
php artisan test
php artisan test tests/Feature/ExampleTest.php
php artisan test --filter=ExampleTest
vendor/bin/pint --test
vendor/bin/pint
npm install
npm run build
composer run dev
php artisan migrate --seed
```

- The committed test suite is still minimal: only the default `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php` are present.
- `npm run build` depends on installing frontend packages first; the repo does not include `node_modules`.
- `composer run dev` starts the local Laravel server, queue listener, log tail, and Vite together.

## High-level architecture

- There is a single Filament admin panel mounted at `/admin`, configured in `backend/app/Providers/Filament/AdminPanelProvider.php`. Resources, pages, and widgets are auto-discovered from `backend/app/Filament`.
- The app models an academic planning workflow around `Curso`, `Etapa`, `Modulo`, `Materia`, `Docente`, and scheduling/assignment records.
- `Curso` creation automatically creates one `CursoEtapa` record per existing `Etapa`. Saving `CursoEtapa.modulo_id` automatically seeds matching `CursoMateria` rows for that course based on the module's materias.
- Scheduling is not handled by a Filament page. The `Horarios` action in `CursoEtapasRelationManager` opens a custom Blade screen at `backend/resources/views/cursos/horarios.blade.php`, backed by `backend/app/Http/Controllers/Cursos/CursoEtapaHorarioController.php`.
- That scheduling flow materializes `CursoEtapaMateria` rows on demand, edits assignment metadata and weekly slots in one form, and then syncs `Horario` rows from the selected active `AsignacionDocente` records.
- Seeders are domain-critical rather than sample data: `DatabaseSeeder` loads anexos, modulos, materias, etapas, generated courses, module assignments per stage, CUPoF values, and the nightly block schedule.

## Key conventions

- Filament resources follow the Filament 4 split-file pattern: `Resource.php` delegates to `Schemas/*Form.php`, `Tables/*Table.php`, `Pages/*`, and `RelationManagers/*`. Follow that layout instead of putting everything in a single resource file.
- Domain terminology and UI copy stay in Spanish. Preserve existing field names and enums such as `situacion_revista` (`INT`, `SUP`, `PRO`), `periodo` (`A`, `C1`, `C2`), `horas_catedra`, `anexo`, and `etapa`.
- Several relationship tables use singular custom names (`curso_etapa`, `curso_materia`, `curso_etapa_materia`, `docente_titulo`), so Eloquent models often need explicit `$table` declarations and careful foreign-key naming.
- Historical bajas are intentionally preserved. `AsignacionDocente::activas()` excludes rows that already have related bajas, and the scheduling controller only updates/deletes active assignments while leaving historical records intact.
- `CursoMateriasRelationManager` only allows materias from modules already assigned through the course's etapas. Do not widen those selectors to all materias unless the underlying business rule changes.
- The default local setup is SQLite with database-backed queue/session/cache. The repo already includes `backend/database/database.sqlite`, and `.env.example` is configured for SQLite by default.
