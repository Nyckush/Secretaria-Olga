<?php

namespace App\Filament\Resources\AsignacionesDocentes\Pages;

use App\Exports\AsignacionesDocentesExport;
use App\Exports\BajasRegistradasExport;
use App\Filament\Resources\AsignacionesDocentes\AsignacionDocenteResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ListAsignacionesDocentes extends ListRecords
{
    protected static string $resource = AsignacionDocenteResource::class;

    protected function getHeaderActions(): array
    {
        return [
          
            Action::make('exportarExcel')
                ->label('Exportar Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (): BinaryFileResponse {
                    return Excel::download(
                        new AsignacionesDocentesExport(),
                        'asignaciones_docentes_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                    );
                }),
            Action::make('descargarBajas')
                ->label('Descargar Planilla de Baja')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->action(function (): BinaryFileResponse {
                    return Excel::download(
                        new BajasRegistradasExport(),
                        'bajas_registradas_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
                    );
                }),
        ];
    }
}
