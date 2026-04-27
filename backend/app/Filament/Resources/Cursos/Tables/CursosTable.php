<?php

namespace App\Filament\Resources\Cursos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CursosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('anexo.nombre')
                    ->label('Anexo')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('curso_completo')
                    ->label('Curso')
                    ->getStateUsing(fn ($record) => "{$record->nombre} {$record->division}")
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('nombre', $direction)
                                    ->orderBy('division', $direction);
                    })
                    ->searchable(['nombre', 'division']),
                TextColumn::make('turno')
                    ->label('Turno')
                    ->sortable(),
                TextColumn::make('ciclo_lectivo')
                    ->label('Ciclo Lectivo')
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
