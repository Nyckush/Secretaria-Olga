<?php

namespace App\Filament\Resources\Docentes\RelationManagers;

use App\Models\AsignacionDocente;
use App\Models\CursoEtapaMateria;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AsignacionesDocentesRelationManager extends RelationManager
{
    protected static string $relationship = 'asignacionesDocentes';

    protected static ?string $title = 'Materias asignadas';

    public function form(Schema $schema): Schema
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('cursoEtapaMateria.cursoMateria.materia.nombre')
                    ->label('Materia')
                    ->sortable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->state(fn (AsignacionDocente $record): string => $record->hasBajaRegistrada() ? 'Baja' : 'Activa')
                    ->badge()
                    ->color(fn (AsignacionDocente $record): string => $record->hasBajaRegistrada() ? 'warning' : 'success'),
                TextColumn::make('situacion_revista')
                    ->label('Sit Rev.')
                    ->badge()
                    ->sortable(),
                TextColumn::make('cursoEtapaMateria.cursoEtapa.curso.nombre')
                    ->label('Curso')
                    ->formatStateUsing(fn ($state, $record) => trim(($record->cursoEtapaMateria?->cursoEtapa?->curso?->nombre ?? '') . ' ' . ($record->cursoEtapaMateria?->cursoEtapa?->curso?->division ?? '')))
                    ->sortable(),
                TextColumn::make('cursoEtapaMateria.cursoEtapa.etapa.nombre')
                    ->label('Etapa')
                    ->formatStateUsing(function (?string $state): string {
                        return match ($state) {
                            '1ª Cuatrimestre', '1er Cuatrimestre', 'Primera Cuatrimestre', 'Primer Cuatrimestre' => '1ªC',
                            '2ª Cuatrimestre', '2do Cuatrimestre', 'Segunda Cuatrimestre', 'Segundo Cuatrimestre' => '2ªC',
                            default => $state ?? '-',
                        };
                    })
                    ->sortable(),
                
                TextColumn::make('fecha_desde')
                    ->label('Fecha desde')
                    ->date()
                    ->sortable(),
                TextColumn::make('hasta')
                    ->label('Hasta')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('darDeBaja')
                    ->label('Dar de baja')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->modalHeading('Registrar baja de asignación')
                    ->hidden(fn (AsignacionDocente $record): bool => $record->hasBajaRegistrada())
                    ->form([
                        DatePicker::make('fecha_baja')
                            ->label('Fecha de baja')
                            ->required()
                            ->default(now()),
                        Select::make('tipo_baja')
                            ->label('Tipo de baja')
                            ->options([
                                'Renuncia' => 'Renuncia',
                                'Finalizacion' => 'Finalización',
                                'Reemplazo' => 'Reemplazo',
                                'Otro' => 'Otro',
                            ])
                            ->required()
                            ->default('Otro')
                            ->native(false),
                        Textarea::make('motivo')
                            ->label('Motivo')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->action(function (AsignacionDocente $record, array $data): void {
                        DB::transaction(function () use ($record, $data): void {
                            $record->bajas()->create([
                                'motivo' => $data['motivo'],
                                'fecha_baja' => $data['fecha_baja'],
                                'tipo_baja' => $data['tipo_baja'],
                            ]);

                            $record->update([
                                'hasta' => $data['fecha_baja'],
                            ]);
                        });

                        Notification::make()
                            ->title('Baja registrada')
                            ->body('Se registró la baja y se cerró la asignación correctamente.')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
