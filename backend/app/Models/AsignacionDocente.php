<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsignacionDocente extends Model
{
    protected $table = 'asignaciones_docentes';

    protected $fillable = [
        'curso_etapa_materia_id',
        'docente_id',
        'situacion_revista',
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

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }
}
