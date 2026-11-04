<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuenteEstado extends Model
{
    protected $table = 'puente_estados';

    protected $guarded = [];

    protected $attributes = ['secuencia' => 0, 'filas' => 0];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ultimo_latido_en' => 'datetime',
            'ultimo_dato_en' => 'datetime',
        ];
    }

    public static function actual(): self
    {
        return self::query()->firstOrCreate(['id' => 1]);
    }

    /**
     * Los datos de SAINT solo se publican si el puente leyó la base hace poco.
     */
    public function datosVigentes(): bool
    {
        return $this->ultimo_dato_en?->gte(now()->subMinutes(config('tienda.puente.max_inactividad_minutos'))) ?? false;
    }
}
