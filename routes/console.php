<?php

use App\Models\PuenteEstado;
use App\Models\SaintProducto;
use Illuminate\Support\Facades\Artisan;

Artisan::command('puente:estado', function () {
    $estado = PuenteEstado::actual();

    $this->table(['Dato', 'Valor'], [
        ['Modo', $estado->modo ?? '—'],
        ['Último latido', $estado->ultimo_latido_en?->toDateTimeString() ?? 'nunca'],
        ['Último dato de SAINT', $estado->ultimo_dato_en?->toDateTimeString() ?? 'nunca'],
        ['Datos vigentes', $estado->datosVigentes() ? 'sí' : 'no'],
        ['Filas en SAINT', $estado->filas ?? 0],
        ['Productos sincronizados', SaintProducto::query()->count()],
        ['Publicables', SaintProducto::query()->publicable()->count()],
        ['Último error', $estado->error ?? '—'],
    ]);
})->purpose('Muestra el estado del puente con SAINT');
