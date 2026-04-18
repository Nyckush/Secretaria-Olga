<?php

namespace App\Filament\Resources\Docentes;

use App\Filament\Resources\Docentes\Pages\CreateDocente;
use App\Filament\Resources\Docentes\Pages\EditDocente;
use App\Filament\Resources\Docentes\Pages\ListDocentes;
use App\Filament\Resources\Docentes\Schemas\DocenteForm;
use App\Filament\Resources\Docentes\Tables\DocentesTable;
use App\Models\Docente;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class DocenteResource extends Resource
{
    protected static ?string $model = Docente::class;

    protected static ?string $modelLabel = 'Docente';

    protected static ?string $pluralModelLabel = 'Docentes';

    protected static ?string $navigationLabel = 'Docentes';

    protected static UnitEnum|string|null $navigationGroup = 'Academico';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return DocenteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocentesTable::configure($table);
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
            'index' => ListDocentes::route('/'),
            'create' => CreateDocente::route('/create'),
            'edit' => EditDocente::route('/{record}/edit'),
        ];
    }
}
