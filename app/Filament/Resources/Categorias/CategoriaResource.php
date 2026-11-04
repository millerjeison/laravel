<?php

namespace App\Filament\Resources\Categorias;

use App\Filament\Resources\Categorias\Pages\CreateCategoria;
use App\Filament\Resources\Categorias\Pages\EditCategoria;
use App\Filament\Resources\Categorias\Pages\ListCategorias;
use App\Models\Categoria;
use App\Models\Producto;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriaResource extends Resource
{
    protected static ?string $model = Categoria::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static ?string $modelLabel = 'categoría';

    protected static ?string $pluralModelLabel = 'categorías';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->required()
                ->maxLength(80)
                ->regex('/^[\pL\pN ,.()\-]+$/u'),
            TextInput::make('id')
                ->label('Identificador')
                ->helperText('Va en el enlace de la categoría. Solo minúsculas, números y guiones. No se puede cambiar después.')
                ->required()
                ->maxLength(40)
                ->regex('/^[a-z0-9\-]+$/')
                ->unique(ignoreRecord: true)
                ->disabledOn('edit'),
            TextInput::make('posicion')
                ->label('Orden')
                ->integer()
                ->minValue(0)
                ->maxValue(999)
                ->default(0)
                ->required(),
            Select::make('portada_producto_id')
                ->label('Producto de portada')
                ->helperText('Su foto aparece en el cuadro de la categoría.')
                ->searchable()
                ->options(fn (?Categoria $record): array => $record
                    ? Producto::query()->where('categoria_id', $record->id)->orderBy('nombre')->pluck('nombre', 'id')->all()
                    : [])
                ->hiddenOn('create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('posicion')
            ->columns([
                TextColumn::make('posicion')->label('Orden')->sortable(),
                TextColumn::make('nombre')->searchable(),
                TextColumn::make('productos_count')->label('Productos')->counts('productos'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (Categoria $record): bool => $record->productos()->exists()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategorias::route('/'),
            'create' => CreateCategoria::route('/create'),
            'edit' => EditCategoria::route('/{record}/edit'),
        ];
    }
}
