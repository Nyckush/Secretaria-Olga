<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    protected $table = 'materias';

    protected $fillable = [
        'nombre',
        'horas_semanales',
        'modulo_id',
        'tarea_pedagogica',
    ];

    public function cursoEtapaMaterias(): HasMany
    {
        return $this->hasMany(CursoEtapaMateria::class);
    }

    public function cursoMaterias(): HasMany
    {
        return $this->hasMany(CursoMateria::class);
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }
}
