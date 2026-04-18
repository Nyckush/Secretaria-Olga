<?php

namespace App\Filament\Resources\Docentes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DocenteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100),
                TextInput::make('apellido')
                    ->label('Apellido')
                    ->required()
                    ->maxLength(100),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->nullable(),
                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(20),
                TextInput::make('direccion')
                    ->label('Dirección')
                    ->maxLength(255),
                TextInput::make('dni')
                    ->label('DNI')
                    ->required()
                    ->minLength(7)
                    ->maxLength(8)
                    ->rule('regex:/^[0-9]+$/')
                    ->unique(ignoreRecord: true),
                TextInput::make('cuil')
                    ->label('CUIL')
                    ->required()
                    ->length(11)
                    ->rule('regex:/^[0-9]+$/')
                    ->unique(ignoreRecord: true),
            ]);
    }
}
