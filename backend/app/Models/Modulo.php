<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $fillable = [
        'nombre',
        'cursado',
        'horas_total',
    ];

    public function materias()
    {
        return $this->hasMany(Materia::class);
    }
}
