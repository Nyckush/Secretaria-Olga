<?php

namespace App\Filament\Resources\Cursos\Schemas;

use App\Models\Anexo;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CursoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('anexo_id')
                    ->label('Anexo')
                    ->options(Anexo::pluck('nombre', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100),
                TextInput::make('division')
                    ->label('División')
                    ->required()
                    ->maxLength(50),
                TextInput::make('turno')
                    ->label('Turno')
                    ->required()
                    ->maxLength(50),
                TextInput::make('ciclo_lectivo')
                    ->label('Ciclo Lectivo')
                    ->required()
                    ->maxLength(50),
            ]);
    }
}
