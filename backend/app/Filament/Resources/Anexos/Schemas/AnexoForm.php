<?php

namespace App\Filament\Resources\Anexos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnexoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
