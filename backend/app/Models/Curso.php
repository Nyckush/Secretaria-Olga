<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    protected $table = 'cursos';

    protected $fillable = [
        'anexo_id',
        'nombre',
        'division',
        'turno',
        'ciclo_lectivo',
    ];

    public function anexo(): BelongsTo
    {
        return $this->belongsTo(Anexo::class);
    }

    public function cursoEtapas(): HasMany
    {
        return $this->hasMany(CursoEtapa::class);
    }
}
