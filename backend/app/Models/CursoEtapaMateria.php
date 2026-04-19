<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CursoEtapaMateria extends Model
{
    protected $table = 'curso_etapa_materia';

    protected $fillable = [
        'curso_etapa_id',
        'curso_materia_id',
        'horas_catedra',
    ];

    public function cursoEtapa(): BelongsTo
    {
        return $this->belongsTo(CursoEtapa::class);
    }

    public function cursoMateria(): BelongsTo
    {
        return $this->belongsTo(CursoMateria::class);
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    public function asignacionesDocentes(): HasMany
    {
        return $this->hasMany(AsignacionDocente::class);
    }
}
