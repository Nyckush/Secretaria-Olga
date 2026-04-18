<?php

namespace App\Imports;

use App\Models\Docente;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class DocentesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;

    /**
     * Se ejecuta antes de la validación
     * Limpia DNI y CUIL (quita guiones, espacios, etc.)
     */
    public function prepareForValidation($data, $index)
    {
        if (isset($data['cuil'])) {
            $data['cuil'] = preg_replace('/[^0-9]/', '', $data['cuil']);
        }

        if (isset($data['dni'])) {
            $data['dni'] = preg_replace('/[^0-9]/', '', $data['dni']);
        }

        return $data;
    }

    public function model(array $row): ?Docente
    {
        // Ignorar filas vacías o sin datos críticos
        if (empty($row['apellido_y_nombre']) || empty($row['dni']) || empty($row['cuil'])) {
            return null;
        }

        $nombreCompleto = trim($row['apellido_y_nombre']);

        if (str_contains($nombreCompleto, ',')) {
            [$apellido, $nombre] = array_map('trim', explode(',', $nombreCompleto, 2));
        } elseif (str_contains($nombreCompleto, ' ')) {
            $partes   = explode(' ', trim($nombreCompleto), 2);
            $apellido = $partes[0];
            $nombre   = $partes[1];
        } else {
            $apellido = trim($nombreCompleto);
            $nombre   = '';
        }

        return Docente::updateOrCreate(
            ['dni' => trim($row['dni'])], // clave única
            [
                'nombre'    => $nombre,
                'apellido'  => $apellido,
                'email'     => trim($row['email'] ?? '') ?: null,
                'telefono'  => trim($row['telefono'] ?? '') ?: null,
                'direccion' => trim($row['direccion'] ?? '') ?: null,
                'cuil'      => trim($row['cuil']),
            ]
        );
    }

    public function rules(): array
    {
        return [
            'apellido_y_nombre' => ['required'],
            'dni'               => ['required', 'digits_between:7,8'],
            'cuil'              => ['required', 'digits:11'],
        ];
    }
}