<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CursoMateria extends Model
{
    protected $table = 'curso_materia';

    protected $fillable = [
        'curso_id',
        'materia_id',
        'periodo',
        'nro_cupof',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function cursoEtapaMaterias(): HasMany
    {
        return $this->hasMany(CursoEtapaMateria::class);
    }
}
