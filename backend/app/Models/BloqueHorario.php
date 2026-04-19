<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloqueHorario extends Model
{
    protected $table = 'bloques_horarios';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'orden',
        'hora_inicio',
        'hora_fin',
    ];

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'bloque_id');
    }
}
