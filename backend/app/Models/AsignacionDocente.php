<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function bajas()
    {
        return $this->hasMany(BajaAsignacionDocente::class, 'asignacion_docente_id');
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->whereDoesntHave('bajas');
    }

    public function scopeConBaja(Builder $query): Builder
    {
        return $query->whereHas('bajas');
    }

    public function hasBajaRegistrada(): bool
    {
        return $this->relationLoaded('bajas')
            ? $this->bajas->isNotEmpty()
            : $this->bajas()->exists();
    }
}
