<?php

namespace App\Filament\Pages;

use App\Models\DropboxAjuste;
use App\Services\NovedadesTienda;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;

class FotosDropbox extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCloud;

    protected static ?string $navigationLabel = 'Fotos de Dropbox';

    protected static ?string $title = 'Fotos de Dropbox';

    protected ?string $subheading = 'Lo que subes al Dropbox aparece solo en la tienda. Aquí puedes ocultar una foto o corregir su nombre sin tocar el Dropbox.';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.fotos-dropbox';

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (?string $search, int $page, int $recordsPerPage): LengthAwarePaginator => $this->fotos($search, $page, $recordsPerPage))
            ->searchable()
            ->columns([
                ImageColumn::make('foto')->square(),
                TextColumn::make('titulo')->label('Nombre')->wrap(),
                TextColumn::make('categoria')->label('Carpeta'),
                TextColumn::make('fecha')->date('d/m/Y'),
                IconColumn::make('oculto')->boolean()->trueColor('danger')->falseColor('gray'),
            ])
            ->recordActions([
                Action::make('renombrar')
                    ->label('Renombrar')
                    ->schema([
                        TextInput::make('titulo')
                            ->label('Nombre')
                            ->helperText('Conserva "ref" y el número: con eso se conecta el precio de SAINT.')
                            ->required()
                            ->maxLength(200),
                    ])
                    ->fillForm(fn (array $record): array => ['titulo' => $record['titulo']])
                    ->action(function (array $record, array $data): void {
                        DropboxAjuste::query()->updateOrCreate(['item_id' => $record['id']], ['titulo' => $data['titulo']]);
                    }),
                Action::make('alternar')
                    ->label(fn (array $record): string => $record['oculto'] ? 'Mostrar' : 'Ocultar')
                    ->color(fn (array $record): string => $record['oculto'] ? 'success' : 'danger')
                    ->action(function (array $record): void {
                        DropboxAjuste::query()->updateOrCreate(['item_id' => $record['id']], ['oculto' => ! $record['oculto']]);
                    }),
            ]);
    }

    private function fotos(?string $search, int $page, int $recordsPerPage): LengthAwarePaginator
    {
        $ajustes = DropboxAjuste::query()->get()->keyBy('item_id');

        $fotos = collect(app(NovedadesTienda::class)->originales()['items'])
            ->map(fn (array $item): array => [
                ...$item,
                'titulo' => $ajustes->get($item['id'])?->titulo ?: $item['titulo'],
                'oculto' => (bool) $ajustes->get($item['id'])?->oculto,
            ])
            ->when(filled($search), fn ($coleccion) => $coleccion->filter(
                fn (array $item): bool => str_contains(mb_strtolower($item['titulo']), mb_strtolower((string) $search)),
            ))
            ->keyBy('id');

        return new LengthAwarePaginator($fotos->forPage($page, $recordsPerPage), $fotos->count(), $recordsPerPage, $page);
    }
}
