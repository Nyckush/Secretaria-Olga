<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Horario extends Model
{
    protected $table = 'horarios';

    protected $fillable = [
        'curso_etapa_materia_id',
        'docente_id',
        'bloque_id',
        'dia_semana',
        'fecha_desde',
        'hasta',
    ];

    protected $casts = [
        'fecha_desde' => 'date',
    ];

    public function cursoEtapaMateria(): BelongsTo
    {
        return $this->belongsTo(CursoEtapaMateria::class);
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }

    public function bloque(): BelongsTo
    {
        return $this->belongsTo(BloqueHorario::class, 'bloque_id');
    }
}
