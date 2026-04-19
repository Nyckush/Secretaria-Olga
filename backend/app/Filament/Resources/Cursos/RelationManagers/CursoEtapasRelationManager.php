<?php

namespace App\Filament\Resources\Cursos\RelationManagers;

use App\Models\Etapa;
use App\Models\Modulo;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CursoEtapasRelationManager extends RelationManager
{
    protected static string $relationship = 'cursoEtapas';

    protected static ?string $title = 'Etapas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('etapa_id')
                    ->label('Etapa')
                    ->options(Etapa::orderBy('orden')->pluck('nombre', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('modulo_id')
                    ->label('Módulo')
                    ->options(Modulo::orderBy('nombre')->pluck('nombre', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('etapa.nombre')
            ->columns([
                TextColumn::make('etapa.nombre')
                    ->label('Etapa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('etapa.orden')
                    ->label('Orden')
                    ->sortable(),
                TextColumn::make('modulo.nombre')
                    ->label('Módulo')
                    ->badge()
                    ->getStateUsing(fn ($record): string => $record->modulo?->nombre ?? 'SIN ASIGNAR')
                    ->color(fn ($record): string => blank($record->modulo_id) ? 'danger' : 'gray')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
