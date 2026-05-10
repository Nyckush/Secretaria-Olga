<?php

namespace App\Filament\Resources\Cursos\Pages;

use App\Filament\Resources\Cursos\CursoResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCursos extends ListRecords
{
    protected static string $resource = CursoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('planilla_tp_c1_pdf')
                ->label('Planilla TP C1 (PDF)')
                ->url(route('reportes.tareas-pedagogicas.c1.pdf-preview'))
                ->openUrlInNewTab(),
            CreateAction::make(),
        ];
    }
}
