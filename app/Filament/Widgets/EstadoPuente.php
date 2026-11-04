<?php

namespace App\Filament\Widgets;

use App\Models\PuenteEstado;
use App\Models\SaintProducto;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EstadoPuente extends StatsOverviewWidget
{
    protected ?string $heading = 'Puente con SAINT';

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        $estado = PuenteEstado::actual();
        $vigente = $estado->datosVigentes();

        return [
            Stat::make('Estado', $vigente ? 'Conectado' : 'Sin conexión')
                ->description($estado->error ?? 'Modo: '.($estado->modo ?? 'sin datos'))
                ->color($vigente ? 'success' : 'danger'),
            Stat::make('Última lectura de SAINT', $estado->ultimo_dato_en?->diffForHumans() ?? 'Nunca'),
            Stat::make('Productos sincronizados', number_format(SaintProducto::query()->count(), 0, ',', '.'))
                ->description(number_format(SaintProducto::query()->publicable()->count(), 0, ',', '.').' se pueden publicar'),
        ];
    }
}
