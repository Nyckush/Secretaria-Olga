<?php

namespace App\Filament\Resources\AsignacionesDocentes\Pages;

use App\Exports\AsignacionesDocentesExport;
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
            CreateAction::make(),
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
        ];
    }
}
