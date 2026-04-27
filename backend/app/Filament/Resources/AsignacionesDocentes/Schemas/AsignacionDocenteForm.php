<?php

namespace App\Filament\Resources\AsignacionesDocentes\Schemas;

use App\Models\AsignacionDocente;
use App\Models\CursoEtapaMateria;
use App\Models\Docente;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AsignacionDocenteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('curso_etapa_materia_id')
                    ->label('Curso / Etapa / Materia')
                    ->options(
                        CursoEtapaMateria::query()
                            ->with([
                                'cursoEtapa.curso',
                                'cursoEtapa.etapa',
                                'cursoMateria.materia',
                            ])
                            ->get()
                            ->mapWithKeys(function (CursoEtapaMateria $cursoEtapaMateria): array {
                                $curso = $cursoEtapaMateria->cursoEtapa?->curso?->nombre;
                                $division = $cursoEtapaMateria->cursoEtapa?->curso?->division;
                                $etapa = $cursoEtapaMateria->cursoEtapa?->etapa?->nombre;
                                $materia = $cursoEtapaMateria->cursoMateria?->materia?->nombre;

                                $label = trim(implode(' - ', array_filter([
                                    trim(($curso ?? '') . ' ' . ($division ?? '')),
                                    $etapa,
                                    $materia,
                                ])));

                                return [
                                    $cursoEtapaMateria->id => $label !== '' ? $label : 'Registro #' . $cursoEtapaMateria->id,
                                ];
                            })
                    )
                    ->searchable()
                    ->preload()
                    ->rule(function ($record) {
                        return function (string $attribute, $value, Closure $fail) use ($record): void {
                            if (blank($value)) {
                                return;
                            }

                            $existeAsignacionActiva = AsignacionDocente::query()
                                ->activas()
                                ->where('curso_etapa_materia_id', $value)
                                ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();

                            if ($existeAsignacionActiva) {
                                $fail('Esta materia ya tiene una asignación activa. Primero debes dar de baja la actual para poder reasignarla.');
                            }
                        };
                    })
                    ->required(),
                Select::make('docente_id')
                    ->label('Docente')
                    ->options(
                        Docente::query()
                            ->orderBy('apellido')
                            ->orderBy('nombre')
                            ->get()
                            ->mapWithKeys(function (Docente $docente): array {
                                $label = trim(($docente->apellido ?? '') . ', ' . ($docente->nombre ?? ''));

                                return [
                                    $docente->id => trim($label, ', ') !== '' ? trim($label, ', ') : 'Docente #' . $docente->id,
                                ];
                            })
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('situacion_revista')
                    ->label('Situación de revista')
                    ->options([
                        'INT' => 'Interino (INT)',
                        'SUP' => 'Suplente (SUP)',
                        'PRO' => 'Provisional (PRO)',
                    ])
                    ->required()
                    ->default('INT')
                    ->native(false),
                DatePicker::make('fecha_desde')
                    ->label('Fecha desde')
                    ->required(),
                TextInput::make('hasta')
                    ->label('Hasta')
                    ->required()
                    ->maxLength(50),
            ]);
    }
}
