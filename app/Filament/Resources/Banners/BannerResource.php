<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Resources\Banners\Pages\CreateBanner;
use App\Filament\Resources\Banners\Pages\EditBanner;
use App\Filament\Resources\Banners\Pages\ListBanners;
use App\Models\Banner;
use App\Models\Producto;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $modelLabel = 'banner';

    protected static ?string $pluralModelLabel = 'banners';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('etiqueta')->helperText('El texto pequeño sobre el título.')->required()->maxLength(60),
            Select::make('estilo')
                ->options([
                    's-img' => 'Con imagen grande',
                    's-dark' => 'Fondo oscuro',
                    's-mag' => 'Fondo magenta',
                    's-yel' => 'Fondo amarillo',
                    's-paper' => 'Fondo claro',
                ])
                ->required()
                ->default('s-dark'),
            TextInput::make('titulo_inicio')->label('Título: inicio')->required()->maxLength(60),
            TextInput::make('titulo_resaltado')->label('Título: parte resaltada')->maxLength(60),
            TextInput::make('titulo_fin')->label('Título: final')->maxLength(60),
            Textarea::make('texto')->maxLength(400)->rows(3),
            TextInput::make('boton_texto')->label('Texto del botón')->required()->maxLength(40),
            TextInput::make('boton_enlace')
                ->label('Enlace del botón')
                ->helperText('Una sección de la tienda, como #herramienta, o una dirección que empiece por https://')
                ->required()
                ->maxLength(200)
                ->regex('/^(#[\w\-]+|https:\/\/[^\s"\'<>]+)$/u'),
            Select::make('productos')
                ->label('Productos que se muestran')
                ->helperText('Hasta 3. No se usan en el estilo con imagen grande.')
                ->multiple()
                ->maxItems(3)
                ->searchable()
                ->options(fn (): array => Producto::query()->orderBy('nombre')->pluck('nombre', 'id')->all()),
            TextInput::make('posicion')->label('Orden')->integer()->minValue(0)->maxValue(999)->default(0)->required(),
            Toggle::make('activo')->default(true),
            FileUpload::make('imagen')
                ->label('Imagen para computador')
                ->disk('web')
                ->directory('media/banners')
                ->visibility('public')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(5120),
            FileUpload::make('imagen_movil')
                ->label('Imagen para celular')
                ->disk('web')
                ->directory('media/banners')
                ->visibility('public')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(5120),
            TextInput::make('texto_alternativo')
                ->helperText('Describe la imagen para buscadores y personas con lector de pantalla.')
                ->maxLength(300)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('posicion')
            ->columns([
                TextColumn::make('posicion')->label('Orden')->sortable(),
                TextColumn::make('etiqueta'),
                TextColumn::make('titulo_inicio')->label('Título'),
                IconColumn::make('activo')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'edit' => EditBanner::route('/{record}/edit'),
        ];
    }
}
