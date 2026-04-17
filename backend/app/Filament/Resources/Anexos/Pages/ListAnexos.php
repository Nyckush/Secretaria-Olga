<?php

namespace App\Filament\Resources\Anexos\Pages;

use App\Filament\Resources\Anexos\AnexoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnexos extends ListRecords
{
    protected static string $resource = AnexoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
