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

    protected static function booted(): void
    {
        static::created(function (Curso $curso) {
            $etapas = Etapa::orderBy('orden')->get();

            foreach ($etapas as $etapa) {
                CursoEtapa::create([
                    'curso_id' => $curso->id,
                    'etapa_id' => $etapa->id,
                ]);
            }
        });
    }

    public function anexo(): BelongsTo
    {
        return $this->belongsTo(Anexo::class);
    }

    public function cursoEtapas(): HasMany
    {
        return $this->hasMany(CursoEtapa::class);
    }

    public function cursoMaterias(): HasMany
    {
        return $this->hasMany(CursoMateria::class);
    }
}
