<?php

namespace App\Filament\Resources\Cursos\RelationManagers;

use App\Models\Etapa;
use App\Models\Modulo;
use App\Models\CursoEtapa;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

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
                Action::make('invertir_modulos')
                    ->label('Invertir módulos')
                    ->requiresConfirmation()
                    ->modalHeading('Invertir módulos entre etapas')
                    ->modalDescription('Esta acción intercambia el módulo de la 1ª etapa con el módulo de la 2ª etapa del curso actual.')
                    ->modalSubmitActionLabel('Sí, invertir')
                    ->color('warning')
                    ->action(function (): void {
                        $curso = $this->getOwnerRecord();

                        $etapas = CursoEtapa::query()
                            ->with('etapa:id,orden')
                            ->where('curso_id', $curso->id)
                            ->get()
                            ->sortBy(fn (CursoEtapa $cursoEtapa): int => (int) ($cursoEtapa->etapa?->orden ?? 0))
                            ->values();

                        if ($etapas->count() < 2) {
                            Notification::make()
                                ->title('No se pudo invertir módulos')
                                ->body('El curso no tiene dos etapas para realizar el intercambio.')
                                ->danger()
                                ->send();

                            return;
                        }

                        /** @var CursoEtapa $primeraEtapa */
                        $primeraEtapa = $etapas->get(0);
                        /** @var CursoEtapa $segundaEtapa */
                        $segundaEtapa = $etapas->get(1);

                        if ((int) ($primeraEtapa->modulo_id ?? 0) === (int) ($segundaEtapa->modulo_id ?? 0)) {
                            Notification::make()
                                ->title('Sin cambios')
                                ->body('Las etapas ya tienen el mismo módulo, no hubo cambios para aplicar.')
                                ->warning()
                                ->send();

                            return;
                        }

                        DB::transaction(function () use ($primeraEtapa, $segundaEtapa): void {
                            $moduloPrimeraEtapa = $primeraEtapa->modulo_id;

                            $primeraEtapa->update([
                                'modulo_id' => $segundaEtapa->modulo_id,
                            ]);

                            $segundaEtapa->update([
                                'modulo_id' => $moduloPrimeraEtapa,
                            ]);
                        });

                        Notification::make()
                            ->title('Módulos invertidos correctamente')
                            ->success()
                            ->send();
                    }),

                Action::make('vista_previa_pdf')
                    ->label('Vista previa PDF')
                    ->url(fn ($record = null): string => route('curso-etapas.horarios.pdf-preview', ['cursoEtapa' => $record ?? $this->getOwnerRecord()]))
                    ->openUrlInNewTab(),
            ])
            ->recordActions([
                Action::make('horarios')
                    ->label('Horarios')
                    ->url(fn ($record): string => route('curso-etapas.horarios', ['cursoEtapa' => $record]))
                    ->openUrlInNewTab(),
                
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
