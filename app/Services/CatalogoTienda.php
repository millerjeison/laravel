<?php

namespace App\Services;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Support\Collection;

class CatalogoTienda
{
    public function __construct(private AjustesSitio $ajustes) {}

    /**
     * El catálogo con la forma que espera la tienda: cats, products y home.
     *
     * @return array{cats: list<array<string, string>>, products: list<array<string, mixed>>, home: array<string, mixed>}
     */
    public function datos(): array
    {
        $productos = Producto::query()->where('visible', true)->orderByRaw('categoria_id, LENGTH(id), id')->get();
        $porCategoria = $productos->groupBy('categoria_id');
        $categorias = Categoria::query()->orderBy('posicion')->get()
            ->filter(fn (Categoria $categoria): bool => $porCategoria->has($categoria->id))
            ->values();

        return [
            'cats' => $categorias->map(fn (Categoria $categoria): array => [
                'id' => $categoria->id,
                'label' => $categoria->nombre,
            ])->all(),
            'products' => $productos->map($this->producto(...))->all(),
            'home' => [
                'tiles' => $categorias->mapWithKeys(fn (Categoria $categoria): array => [
                    $categoria->id => $porCategoria[$categoria->id]->firstWhere('id', $categoria->portada_producto_id)?->id
                        ?? $porCategoria[$categoria->id]->first()->id,
                ])->all(),
                'feat' => $categorias->mapWithKeys(fn (Categoria $categoria): array => [
                    $categoria->id => $this->destacados($porCategoria[$categoria->id]),
                ])->all(),
            ],
        ];
    }

    /**
     * @param  Collection<int, Producto>  $productos
     * @return list<string>
     */
    private function destacados(Collection $productos): array
    {
        $elegidos = $productos->whereNotNull('destacado_posicion')->sortBy('destacado_posicion');

        return ($elegidos->isNotEmpty() ? $elegidos : $productos->take(12))->pluck('id')->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function producto(Producto $producto): array
    {
        return array_filter([
            'id' => $producto->id,
            'n' => $producto->nombre,
            'r' => $producto->referencias,
            'c' => $producto->categoria_id,
            'k' => count($producto->imagenes),
            '$' => $this->ajustes->mostrarPrecios() ? $producto->precio_referencia : null,
            'u' => $producto->unidad,
            'nw' => $producto->novedad_en?->format('Y-m-d'),
            'imgs' => array_map(asset(...), $producto->imagenes),
            't' => $producto->miniatura ? asset($producto->miniatura) : null,
            'panel' => $producto->gestionado ? 1 : null,
        ], fn (mixed $valor): bool => $valor !== null);
    }
}
