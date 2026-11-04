<?php

namespace App\Filament\Pages;

use App\Services\AjustesSitio;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * @property-read Schema $form
 */
class ContenidoSitio extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = 'Datos del sitio';

    protected static ?string $title = 'Datos del sitio';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.contenido-sitio';

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(AjustesSitio $ajustes): void
    {
        $this->form->fill([
            ...array_intersect_key($ajustes->contacto(), AjustesSitio::CONTACTO),
            'mostrar_precios' => $ajustes->mostrarPrecios(),
            'mostrar_existencias' => $ajustes->mostrarExistencias(),
            'lista_precio' => $ajustes->listaPrecio(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Precios y existencias')
                    ->description('Vienen de SAINT a través del puente. El IVA de cada producto se suma automáticamente.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('mostrar_precios')
                            ->label('Mostrar precios en la tienda')
                            ->helperText('Apagado, los clientes ven "Consultar precio".'),
                        Toggle::make('mostrar_existencias')
                            ->label('Mostrar existencias en la tienda')
                            ->helperText('Enciéndelo solo si las existencias de SAINT son reales.'),
                        Select::make('lista_precio')
                            ->label('Lista de precios al público')
                            ->options([1 => 'Precio 1', 2 => 'Precio 2', 3 => 'Precio 3'])
                            ->required(),
                    ]),
                Section::make('Contacto')
                    ->columns(2)
                    ->schema([
                        TextInput::make('whatsapp_ventas')
                            ->label('WhatsApp de ventas')
                            ->helperText('Solo números, con el 57 adelante. Aquí llegan los pedidos.')
                            ->required()
                            ->regex('/^\d{8,15}$/'),
                        TextInput::make('telefono_ventas')
                            ->label('Teléfono de ventas, como se muestra')
                            ->required()
                            ->regex('/^[\d +()\-]{7,20}$/'),
                        TextInput::make('whatsapp_compras')
                            ->label('WhatsApp de compras')
                            ->helperText('Solo números, con el 57 adelante.')
                            ->required()
                            ->regex('/^\d{8,15}$/'),
                        TextInput::make('telefono_compras')
                            ->label('Teléfono de compras, como se muestra')
                            ->required()
                            ->regex('/^[\d +()\-]{7,20}$/'),
                        TextInput::make('correo')->email()->required()->maxLength(120),
                        TextInput::make('redes')
                            ->label('Instagram y Facebook')
                            ->required()
                            ->maxLength(60)
                            ->regex('/^[@\pL\pN ._\-]+$/u'),
                    ]),
                Section::make('Ubicación')
                    ->columns(2)
                    ->schema([
                        TextInput::make('direccion')->label('Dirección completa')->required()->maxLength(200)->columnSpanFull(),
                        TextInput::make('direccion_corta')->label('Dirección corta')->required()->maxLength(100),
                        TextInput::make('ubicacion')->label('Ubicación en el encabezado')->required()->maxLength(60),
                        TextInput::make('zona')->label('Zona')->required()->maxLength(60),
                        TextInput::make('lugar_mapa')
                            ->label('Búsqueda en Google Maps')
                            ->helperText('El texto con el que Google Maps encuentra el local.')
                            ->required()
                            ->maxLength(200),
                    ]),
                Section::make('Textos generales')
                    ->schema([
                        TextInput::make('eslogan')->required()->maxLength(80),
                        Textarea::make('descripcion_seo')
                            ->label('Descripción para Google y redes sociales')
                            ->required()
                            ->maxLength(300)
                            ->rows(3),
                    ]),
            ]);
    }

    public function guardar(AjustesSitio $ajustes): void
    {
        $ajustes->guardar($this->form->getState());

        Notification::make()->title('Cambios guardados')->success()->send();
    }
}
