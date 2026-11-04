<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Producto extends Model
{
    protected $table = 'productos';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(function (Producto $producto): void {
            if ($producto->exists && $producto->isDirty('imagenes')) {
                $producto->miniatura = null;
            }
        });

        static::deleted(function (Producto $producto): void {
            Storage::disk('web')->delete(array_filter(
                [...$producto->imagenes, $producto->miniatura],
                fn (?string $ruta): bool => str_starts_with((string) $ruta, 'media/productos/'),
            ));
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'referencias' => 'array',
            'imagenes' => 'array',
            'novedad_en' => 'date:Y-m-d',
            'visible' => 'boolean',
            'gestionado' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Categoria, $this>
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}
