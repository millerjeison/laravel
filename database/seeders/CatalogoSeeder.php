<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CatalogoSeeder extends Seeder
{
    /**
     * Carga las categorías y productos que venían en catalog.json, sin tocar lo que ya exista:
     * lo editado en el panel se conserva si se vuelve a ejecutar.
     */
    public function run(): void
    {
        $catalogo = File::json(database_path('data/catalog.json'));

        foreach ($catalogo['cats'] as $posicion => $categoria) {
            Categoria::query()->firstOrCreate(['id' => $categoria['id']], [
                'nombre' => $categoria['label'],
                'posicion' => $posicion,
                'portada_producto_id' => $catalogo['home']['tiles'][$categoria['id']] ?? null,
            ]);
        }

        $destacados = [];

        foreach ($catalogo['home']['feat'] as $ids) {
            foreach ($ids as $posicion => $id) {
                $destacados[$id] = $posicion;
            }
        }

        foreach ($catalogo['products'] as $producto) {
            $miniatura = "media/productos/{$producto['id']}-0-s.webp";

            Producto::query()->firstOrCreate(['id' => $producto['id']], [
                'nombre' => $producto['n'],
                'categoria_id' => $producto['c'],
                'precio_referencia' => $producto['$'] ?? null,
                'unidad' => $producto['u'] ?? null,
                'referencias' => $producto['r'],
                'imagenes' => array_map(
                    fn (int $indice): string => "media/productos/{$producto['id']}-{$indice}.webp",
                    range(0, max(0, $producto['k'] - 1)),
                ),
                'miniatura' => File::exists(public_path($miniatura)) ? $miniatura : null,
                'novedad_en' => $producto['nw'] ?? null,
                'destacado_posicion' => $destacados[$producto['id']] ?? null,
            ]);
        }
    }
}
