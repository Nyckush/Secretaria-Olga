<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    protected $table = 'horarios';

    protected $fillable = [
        'curso_etapa_id',
        'materia_id',
        'docente_id',
        'dia_semana',
        'hora_catedra',
    ];

    public function cursoEtapa(): BelongsTo
    {
        return $this->belongsTo(CursoEtapa::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }
}
