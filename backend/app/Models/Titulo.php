<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Titulo extends Model
{
    protected $table = 'titulos';

    protected $fillable = [
        'nombre',
    ];

    public function docentes(): BelongsToMany
    {
        return $this->belongsToMany(Docente::class, 'docente_titulo');
    }
}
