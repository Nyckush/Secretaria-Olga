<?php

namespace App\Filament\Resources\Docentes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocentesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_completo')
                    ->label('Docente')
                    ->getStateUsing(fn ($record) => "{$record->apellido}, {$record->nombre}")
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderBy('apellido', $direction)
                                    ->orderBy('nombre', $direction);
                    })
                    ->searchable(['nombre', 'apellido']),
                        
                TextColumn::make('dni')
                    ->label('DNI')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('cuil')
                    ->label('CUIL')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('telefono')
                    ->label('TELÉFONO')
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
