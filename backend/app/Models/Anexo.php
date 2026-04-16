<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anexo extends Model
{
    protected $table = 'anexos';

    protected $fillable = [
        'nombre',
    ];

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class);
    }
}
