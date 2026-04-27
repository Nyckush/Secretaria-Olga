<?php

namespace App\Filament\Resources\AsignacionesDocentes;

use App\Filament\Resources\AsignacionesDocentes\Pages\CreateAsignacionDocente;
use App\Filament\Resources\AsignacionesDocentes\Pages\EditAsignacionDocente;
use App\Filament\Resources\AsignacionesDocentes\Pages\ListAsignacionesDocentes;
use App\Filament\Resources\AsignacionesDocentes\Schemas\AsignacionDocenteForm;
use App\Filament\Resources\AsignacionesDocentes\Tables\AsignacionesDocentesTable;
use App\Models\AsignacionDocente;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AsignacionDocenteResource extends Resource
{
    protected static ?string $model = AsignacionDocente::class;

    protected static ?string $modelLabel = 'Asignacion Docente';

    protected static ?string $pluralModelLabel = 'Asignaciones Docentes';

    protected static ?string $navigationLabel = 'P.O.F ';

    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static UnitEnum|string|null $navigationGroup = 'Gestión Académica';

    public static function form(Schema $schema): Schema
    {
        return AsignacionDocenteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AsignacionesDocentesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAsignacionesDocentes::route('/'),
            'create' => CreateAsignacionDocente::route('/create'),
            'edit' => EditAsignacionDocente::route('/{record}/edit'),
        ];
    }
}
