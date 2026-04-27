<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BajaAsignacionDocente extends Model
{
    protected $table = 'bajas_asignaciones_docentes';

    protected $fillable = [
        'asignacion_docente_id',
        'motivo',
        'fecha_baja',
        'tipo_baja',
    ];

    protected $casts = [
        'fecha_baja' => 'date',
    ];

    // Relación: pertenece a una asignación
    public function asignacion()
    {
        return $this->belongsTo(AsignacionDocente::class, 'asignacion_docente_id');
    }
}