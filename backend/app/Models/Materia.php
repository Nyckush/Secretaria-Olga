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
    ];

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }
    public function modulo()
    {
        return $this->belongsTo(Modulo::class);
    }
}
