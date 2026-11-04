<?php

namespace App\Models;

use App\Services\AjustesSitio;
use Database\Factories\SaintProductoFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaintProducto extends Model
{
    /** @use HasFactory<SaintProductoFactory> */
    use HasFactory;

    protected $table = 'saint_productos';

    protected $primaryKey = 'codigo';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'precio1' => 'float',
            'precio2' => 'float',
            'precio3' => 'float',
            'iva' => 'float',
            'existencia' => 'float',
            'activo' => 'boolean',
        ];
    }

    /**
     * Productos que se pueden publicar: activos, sin códigos reciclados ("XX") ni instancias internas.
     *
     * @param  Builder<SaintProducto>  $query
     */
    #[Scope]
    protected function publicable(Builder $query): void
    {
        $query->where('activo', true)
            ->whereNotIn('instancia', config('tienda.instancias_excluidas'))
            ->whereRaw("UPPER(COALESCE(descripcion, '')) NOT LIKE 'XX%'")
            ->whereRaw("TRIM(COALESCE(descripcion, '')) <> ''");
    }

    /**
     * Precio al público en pesos: la lista configurada de SAINT más el IVA del producto.
     */
    public function precioPublico(): int
    {
        $lista = (float) $this->{'precio'.app(AjustesSitio::class)->listaPrecio()};

        return (int) round($lista * (1 + ($this->iva ?? config('tienda.iva_defecto')) / 100));
    }
}
