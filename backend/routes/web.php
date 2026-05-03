<?php

use App\Http\Controllers\Cursos\CursoEtapaHorarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function (): void {
    Route::get('/admin/curso-etapas/{cursoEtapa}/horarios', [CursoEtapaHorarioController::class, 'show'])
        ->name('curso-etapas.horarios');

    Route::post('/admin/curso-etapas/{cursoEtapa}/horarios', [CursoEtapaHorarioController::class, 'store'])
        ->name('curso-etapas.horarios.store');

    // Export de bajas registradas
    Route::get('/admin/exports/bajas-registradas', [\App\Http\Controllers\BajasExportController::class, 'download'])
        ->name('exports.bajas_registradas');

    // Endpoint para búsqueda de docentes (autocomplete)
    Route::get('/admin/api/docentes', [\App\Http\Controllers\Api\DocenteSearchController::class, 'search'])
        ->name('api.docentes.search');
});