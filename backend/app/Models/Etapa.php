<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etapa extends Model
{
    protected $table = 'etapas';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'orden',
    ];

    public function cursoEtapas(): HasMany
    {
        return $this->hasMany(CursoEtapa::class);
    }
}
