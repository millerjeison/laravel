<?php

namespace App\Filament\Resources\Productos\Pages;

use App\Filament\Resources\Productos\ProductoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProducto extends EditRecord
{
    protected static string $resource = ProductoResource::class;

    /**
     * Al editarlo pasa a ser del panel: el Dropbox ya no le cambia fotos ni categoría.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return ['gestionado' => true] + $data;
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
