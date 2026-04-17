<?php

namespace App\Filament\Resources\Modulos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ModuloForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Select::make('cursado')
                    ->options([
                        1 => '1 ª año',
                        2 => '2 ª año',
                        3 => '3 ª año',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('horas_total')
                    ->required()
                    ->numeric()
                    ->default(30),
            ]);
    }
}
