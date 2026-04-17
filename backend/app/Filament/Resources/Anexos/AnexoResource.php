<?php

namespace App\Filament\Resources\Anexos;

use App\Filament\Resources\Anexos\Pages\CreateAnexo;
use App\Filament\Resources\Anexos\Pages\EditAnexo;
use App\Filament\Resources\Anexos\Pages\ListAnexos;
use App\Filament\Resources\Anexos\RelationManagers\CursosRelationManager;
use App\Filament\Resources\Anexos\Schemas\AnexoForm;
use App\Filament\Resources\Anexos\Tables\AnexosTable;
use App\Models\Anexo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AnexoResource extends Resource
{
    protected static ?string $model = Anexo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Anexo';

    protected static ?string $pluralModelLabel = 'Anexos';

    protected static ?string $navigationLabel = 'Anexos';

    protected static UnitEnum|string|null $navigationGroup = 'Academico';

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return AnexoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnexosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CursosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnexos::route('/'),
            'create' => CreateAnexo::route('/create'),
            'edit' => EditAnexo::route('/{record}/edit'),
        ];
    }
}
