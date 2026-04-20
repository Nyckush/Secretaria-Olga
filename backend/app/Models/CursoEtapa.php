<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CursoEtapa extends Model
{
    protected $table = 'curso_etapa';

    protected $fillable = [
        'curso_id',
        'etapa_id',
        'modulo_id',
    ];

    protected static function booted(): void
    {
        static::saved(function (CursoEtapa $cursoEtapa): void {
            if (blank($cursoEtapa->modulo_id)) {
                return;
            }

            if (! $cursoEtapa->wasRecentlyCreated && ! $cursoEtapa->wasChanged('modulo_id')) {
                return;
            }

            $materiaIds = Materia::query()
                ->where('modulo_id', $cursoEtapa->modulo_id)
                ->pluck('id');

            foreach ($materiaIds as $materiaId) {
                CursoMateria::firstOrCreate([
                    'curso_id' => $cursoEtapa->curso_id,
                    'materia_id' => $materiaId,
                ], [
                    'periodo' => 'A',
                ]);
            }
        });
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function etapa(): BelongsTo
    {
        return $this->belongsTo(Etapa::class);
    }

    public function horarios(): HasManyThrough
    {
        return $this->hasManyThrough(
            Horario::class,
            CursoEtapaMateria::class,
            'curso_etapa_id',
            'curso_etapa_materia_id'
        );
    }

    public function cursoEtapaMaterias(): HasMany
    {
        return $this->hasMany(CursoEtapaMateria::class);
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class);
    }
}
