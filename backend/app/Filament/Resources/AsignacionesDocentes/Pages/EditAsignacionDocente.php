<?php

namespace App\Filament\Resources\AsignacionesDocentes\Pages;

use App\Filament\Resources\AsignacionesDocentes\AsignacionDocenteResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAsignacionDocente extends EditRecord
{
    protected static string $resource = AsignacionDocenteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
