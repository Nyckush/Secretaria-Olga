<?php

use App\Http\Controllers\Cursos\CursoEtapaHorarioController;
use App\Http\Controllers\Reportes\TareasPedagogicasController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function (): void {
    Route::get('/admin/reportes/tareas-pedagogicas/c1/pdf-preview', [TareasPedagogicasController::class, 'previewC1Pdf'])
        ->name('reportes.tareas-pedagogicas.c1.pdf-preview');

    Route::get('/admin/curso-etapas/{cursoEtapa}/horarios', [CursoEtapaHorarioController::class, 'show'])
        ->name('curso-etapas.horarios');

    Route::get('/admin/curso-etapas/{cursoEtapa}/horarios/pdf-preview', [CursoEtapaHorarioController::class, 'previewPdf'])
        ->name('curso-etapas.horarios.pdf-preview');

    Route::post('/admin/curso-etapas/{cursoEtapa}/horarios', [CursoEtapaHorarioController::class, 'store'])
        ->name('curso-etapas.horarios.store');
    Route::post('/admin/curso-etapas/{cursoEtapa}/asignaciones/ajax', [CursoEtapaHorarioController::class, 'ajaxCreateAsignacion'])
        ->name('curso-etapas.asignaciones.ajax');
});