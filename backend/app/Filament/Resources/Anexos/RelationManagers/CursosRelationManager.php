<?php

namespace App\Filament\Resources\Anexos\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CursosRelationManager extends RelationManager
{
    protected static string $relationship = 'cursos';

    protected static ?string $title = 'Cursos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100),
                TextInput::make('division')
                    ->label('Division')
                    ->required()
                    ->maxLength(10),
                Select::make('turno')
                    ->label('Turno')
                    ->options([
                        'Mañana' => 'Mañana',
                        'Tarde' => 'Tarde',
                        'Noche' => 'Noche',
                    ])
                    ->required(),
                TextInput::make('ciclo_lectivo')
                    ->label('Ciclo lectivo')
                    ->numeric()
                    ->required()
                    ->minValue(2000)
                    ->maxValue(2100),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('division')
                    ->label('Division')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('turno')
                    ->label('Turno')
                    ->badge(),
                TextColumn::make('ciclo_lectivo')
                    ->label('Ciclo lectivo')
                    ->sortable(),
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