<?php

namespace App\Filament\Resources\Docentes\Pages;

use App\Exports\DocentesExport;
use App\Filament\Resources\Docentes\DocenteResource;
use App\Imports\DocentesImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ListDocentes extends ListRecords
{
    protected static string $resource = DocenteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('exportarExcel')
                ->label('Descargar Listado ')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (): BinaryFileResponse {
                    return Excel::download(
                        new DocentesExport(),
                        'docentes_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                    );
                }),
            Action::make('importarExcel')
                ->label('Importar Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('archivo')
                        ->label('Archivo Excel (.xlsx)')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                        ])
                        ->disk('local')
                        ->directory('imports/docentes')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        Excel::import(
                            new DocentesImport(),
                            $data['archivo'],
                            'local'
                        );

                        Notification::make()
                            ->title('Importación completada')
                            ->body('Los docentes fueron importados correctamente.')
                            ->success()
                            ->send();
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('Error al importar')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
