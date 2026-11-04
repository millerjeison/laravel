<?php

namespace Database\Factories;

use App\Models\SaintProducto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SaintProducto>
 */
class SaintProductoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => (string) fake()->unique()->numberBetween(1000, 99999),
            'huella' => fake()->sha256(),
            'descripcion' => strtoupper(fake()->words(3, true)),
            'instancia' => 7,
            'unidad' => 'UND',
            'precio1' => 1000,
            'precio2' => 1200,
            'precio3' => 1344.5378,
            'iva' => 19,
            'existencia' => 12,
            'activo' => true,
        ];
    }
}
