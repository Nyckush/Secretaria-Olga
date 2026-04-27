<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Docente extends Model
{
    protected $table = 'docentes';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'direccion',
        'dni',
        'cuil',
        'legajo_junta',
        'cobra_asignaciones_familiares',
        'trabaja_otras_instituciones',
        'otras_instituciones',
        'tiene_abono_docente',
        'antiguedad_institucion',
        'antiguedad_docente',

    ];

    protected $casts = [
        'cobra_asignaciones_familiares' => 'boolean',
        'trabaja_otras_instituciones' => 'boolean',
        'tiene_abono_docente' => 'boolean',
        'antiguedad_institucion' => 'integer',
        'antiguedad_docente' => 'integer',
    ];

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    public function asignacionesDocentes(): HasMany
    {
        return $this->hasMany(AsignacionDocente::class);
    }

    public function titulos(): BelongsToMany
    {
        return $this->belongsToMany(Titulo::class, 'docente_titulo');
    }
}
