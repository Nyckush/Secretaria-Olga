<?php

namespace App\Filament\Resources\Cursos\RelationManagers;

use App\Models\Materia;
use App\Models\Modulo;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class CursoMateriasRelationManager extends RelationManager
{
    protected static string $relationship = 'cursoMaterias';

    protected static ?string $title = 'Materias';

    public function form(Schema $schema): Schema
    {
        $moduloIds = $this->getOwnerRecord()
            ->cursoEtapas()
            ->whereNotNull('modulo_id')
            ->pluck('modulo_id')
            ->unique()
            ->values();

        $moduloOptions = Modulo::query()
            ->whereIn('id', $moduloIds)
            ->orderBy('nombre')
            ->pluck('nombre', 'id');

        $materias = Materia::query()
            ->with('modulo:id,nombre')
            ->whereIn('modulo_id', $moduloIds)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'modulo_id']);

        $materiasAgrupadasPorModulo = $materias
            ->groupBy(fn (Materia $materia) => $materia->modulo?->nombre ?? 'Sin módulo')
            ->map(fn ($items) => $items->pluck('nombre', 'id')->toArray())
            ->toArray();

        return $schema
            ->components([
                Select::make('modulo_id')
                    ->label('Módulo')
                    ->options($moduloOptions)
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated(false)
                    ->afterStateHydrated(function ($record, Set $set, $state): void {
                        if (filled($state) || blank($record?->materia?->modulo_id)) {
                            return;
                        }

                        $set('modulo_id', $record->materia->modulo_id);
                    })
                    ->afterStateUpdated(fn (Set $set) => $set('materia_id', null))
                    ->helperText('Filtra las materias por módulo asignado al curso.'),
                Select::make('materia_id')
                    ->label('Materia')
                    ->options(function (Get $get) use ($materias, $materiasAgrupadasPorModulo) {
                        if (blank($get('modulo_id'))) {
                            return $materiasAgrupadasPorModulo;
                        }

                        return $materias
                            ->where('modulo_id', (int) $get('modulo_id'))
                            ->pluck('nombre', 'id')
                            ->toArray();
                    })
                    ->disabled($moduloIds->isEmpty())
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set) use ($materias): void {
                        $moduloId = $materias->firstWhere('id', (int) $state)?->modulo_id;

                        $set('modulo_id', $moduloId);
                    })
                    ->helperText('Solo se muestran materias de los módulos asignados al curso en sus etapas.')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('curso_id', $this->getOwnerRecord()->id),
                    )
                    ->rule(function () use ($moduloIds) {
                        return Rule::exists('materias', 'id')->where(function ($query) use ($moduloIds) {
                            $query->whereIn('modulo_id', $moduloIds);
                        });
                    }),
                Select::make('periodo')
                    ->label('Periodo')
                    ->options([
                        'A' => 'Anual',
                        'C1' => 'Cuatrimestre 1',
                        'C2' => 'Cuatrimestre 2',
                    ])
                    ->default('A')
                    ->required()
                    ->native(false),
                TextInput::make('nro_cupof')
                    ->label('Nro Cupof')
                    ->numeric()
                    ->integer()
                    ->nullable()
                    ->unique(ignoreRecord: true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('materia.nombre')
            ->columns([
                TextColumn::make('materia.nombre')
                    ->label('Materia')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('materia.modulo.nombre')
                    ->label('Módulo')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('periodo')
                    ->label('Periodo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'C1' => 'Cuatrimestre 1',
                        'C2' => 'Cuatrimestre 2',
                        default => 'Anual',
                    })
                    ->sortable(),
                TextColumn::make('nro_cupof')
                    ->label('Nro Cupof')
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
