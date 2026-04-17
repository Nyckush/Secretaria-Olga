<?php

namespace App\Filament\Resources\Anexos\Pages;

use App\Filament\Resources\Anexos\AnexoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnexo extends EditRecord
{
    protected static string $resource = AnexoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
