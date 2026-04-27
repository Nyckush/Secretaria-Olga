<?php

namespace App\Filament\Resources\Docentes\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class DocenteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Datos del docente')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('Datos Personales')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('NOMBRE')
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('apellido')
                                    ->label('APELLIDO')
                                    ->required()
                                    ->maxLength(100),
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
                                TextInput::make('email')
                                    ->label('EMAIL')
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->nullable(),
                                TextInput::make('telefono')
                                    ->label('TELÉFONO')
                                    ->tel()
                                    ->maxLength(20),
                                TextInput::make('direccion')
                                    ->label('DIRECCIÓN')
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Datos Laborales')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                TextInput::make('legajo_junta')
                                    ->label(' Nª DE LEGAJO')
                                    ->maxLength(50),
                                
                                Toggle::make('trabaja_otras_instituciones')
                                    ->label('¿TRABAJA EN OTRAS INSTITUCIONES?')
                                    ->default(false)
                                    ->live()
                                    ->columnSpanFull(),
                                Textarea::make('otras_instituciones')
                                    ->label('DETALLE DE OTRAS INSTITUCIONES')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->visible(fn (Get $get): bool => (bool) $get('trabaja_otras_instituciones')),
                              
                                TextInput::make('antiguedad_institucion')
                                    ->label('ANTIGÜEDAD EN LA INSTITUCIÓN')
                                    ->inputMode('numeric')
                                    ->maxLength(2)
                                    ->rule('regex:/^[0-9]{1,2}$/')
                                    ->extraInputAttributes([
                                        'maxlength' => 2,
                                        'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2)",
                                    ])
                                    ->minValue(0)
                                    ->maxValue(99),
                                TextInput::make('antiguedad_docente')
                                    ->label('ANTIGÜEDAD COMO DOCENTE')
                                    ->inputMode('numeric')
                                    ->maxLength(2)
                                    ->rule('regex:/^[0-9]{1,2}$/')
                                    ->extraInputAttributes([
                                        'maxlength' => 2,
                                        'oninput' => "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 2)",
                                    ])
                                    ->minValue(0)
                                    ->maxValue(99),

                                Checkbox::make('cobra_asignaciones_familiares')
                                    ->label('COBRA ASIGNACIONES FAMILIARES'),
                                Checkbox::make('tiene_abono_docente')
                                    ->label('TIENE ABONO DOCENTE'),
                            ])
                            ->columns(2),

                        Tabs\Tab::make('Formación')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Select::make('titulos')
                                    ->label('Títulos')
                                    ->multiple()
                                    ->relationship('titulos', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->label('Nombre del título')
                                            ->required()
                                            ->maxLength(150),
                                    ])
                                    ->createOptionAction(fn ($action) => $action->modalHeading('Registrar nuevo título'))
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
