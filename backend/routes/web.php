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
    Route::post('/admin/curso-etapas/{cursoEtapa}/asignaciones/ajax', [CursoEtapaHorarioController::class, 'ajaxCreateAsignacion'])
        ->name('curso-etapas.asignaciones.ajax');
});