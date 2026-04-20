<?php

namespace App\Filament\Resources\AsignacionesDocentes\Pages;

use App\Filament\Resources\AsignacionesDocentes\AsignacionDocenteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAsignacionesDocentes extends ListRecords
{
    protected static string $resource = AsignacionDocenteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
