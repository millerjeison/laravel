<x-filament-panels::page>
    <form wire:submit="guardar" class="grid gap-6">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit">Guardar cambios</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
