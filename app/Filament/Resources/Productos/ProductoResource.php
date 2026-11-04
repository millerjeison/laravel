<?php

namespace App\Filament\Resources\Productos;

use App\Filament\Resources\Productos\Pages\CreateProducto;
use App\Filament\Resources\Productos\Pages\EditProducto;
use App\Filament\Resources\Productos\Pages\ListProductos;
use App\Models\Producto;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static ?string $modelLabel = 'producto';

    protected static ?string $pluralModelLabel = 'productos';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->required()
                ->maxLength(200),
            Select::make('categoria_id')
                ->label('Categoría')
                ->relationship('categoria', 'nombre')
                ->preload()
                ->searchable()
                ->required(),
            TagsInput::make('referencias')
                ->helperText('Los códigos de SAINT de este producto. Con ellos se conecta el precio en vivo.')
                ->placeholder('Escribe un código y pulsa Enter')
                ->nestedRecursiveRules(['regex:/^[\pL\pN.\-]{1,25}$/u'])
                ->default([]),
            TextInput::make('precio_referencia')
                ->label('Precio con IVA')
                ->helperText('Solo se usa si este producto no tiene precio en SAINT.')
                ->integer()
                ->minValue(0)
                ->maxValue(999999999)
                ->prefix('$'),
            TextInput::make('unidad')
                ->helperText('Ejemplo: kg, metro, caja. Déjalo vacío si se vende por unidad.')
                ->maxLength(20)
                ->regex('/^[\pL\pN .\/]+$/u'),
            DatePicker::make('novedad_en')
                ->label('Marcar como novedad desde')
                ->native(false),
            TextInput::make('destacado_posicion')
                ->label('Posición en la portada')
                ->helperText('Déjalo vacío para que no salga destacado en el estante de su categoría.')
                ->integer()
                ->minValue(0)
                ->maxValue(999),
            Toggle::make('visible')
                ->label('Visible en la tienda')
                ->default(true),
            FileUpload::make('imagenes')
                ->label('Fotos')
                ->helperText('La primera es la principal. JPG, PNG o WEBP, máximo 5 MB cada una.')
                ->multiple()
                ->reorderable()
                ->disk('web')
                ->directory('media/productos')
                ->visibility('public')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(5120)
                ->maxFiles(8)
                ->minFiles(1)
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nombre')
            ->columns([
                ImageColumn::make('imagenes')->label('Foto')->disk('web')->limit(1)->square(),
                TextColumn::make('nombre')->searchable()->sortable()->wrap(),
                TextColumn::make('categoria.nombre')->label('Categoría')->sortable(),
                TextColumn::make('referencias')->searchable()->badge()->limitList(3),
                IconColumn::make('visible')->boolean(),
            ])
            ->filters([
                SelectFilter::make('categoria_id')->label('Categoría')->relationship('categoria', 'nombre'),
                SelectFilter::make('visible')->options([1 => 'Visibles', 0 => 'Ocultos']),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductos::route('/'),
            'create' => CreateProducto::route('/create'),
            'edit' => EditProducto::route('/{record}/edit'),
        ];
    }
}
