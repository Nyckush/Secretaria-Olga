<?php

namespace App\Filament\Resources\AsignacionesDocentes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AsignacionesDocentesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('docente.apellido')
                    ->label('Docente')
                    ->formatStateUsing(fn ($state, $record) => trim(($record->docente?->apellido ?? '') . ' ' . ($record->docente?->nombre ?? '')))
                    ->sortable()
                    ->searchable(['docentes.apellido', 'docentes.nombre']),

                TextColumn::make('situacion_revista')
                    ->label('Sit Rev.')
                    ->badge()
                    ->sortable(),

                TextColumn::make('cursoEtapaMateria.cursoEtapa.curso.nombre')
                    ->label('Curso')
                    ->formatStateUsing(fn ($state, $record) => trim(($record->cursoEtapaMateria?->cursoEtapa?->curso?->nombre ?? '') . ' ' . ($record->cursoEtapaMateria?->cursoEtapa?->curso?->division ?? '')))
                    ->sortable()
                    ->searchable(['cursos.nombre', 'cursos.division']),
                TextColumn::make('cursoEtapaMateria.cursoEtapa.etapa.nombre')
                    ->label('Etapa')
                    ->formatStateUsing(function (?string $state): string {
                        return match ($state) {
                            '1ª Cuatrimestre', '1er Cuatrimestre', 'Primera Cuatrimestre', 'Primer Cuatrimestre' => '1ªC',
                            '2ª Cuatrimestre', '2do Cuatrimestre', 'Segunda Cuatrimestre', 'Segundo Cuatrimestre' => '2ªC',
                            default => $state ?? '-',
                        };
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cursoEtapaMateria.cursoMateria.materia.nombre')
                    ->label('Materia')
                    ->sortable()
                    ->searchable(),
              
                TextColumn::make('fecha_desde')
                    ->label('Fecha desde')
                    ->date()
                    ->sortable(),
                TextColumn::make('hasta')
                    ->label('Hasta')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
