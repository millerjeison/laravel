<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use Database\Seeders\CatalogoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TiendaTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_portada_carga_la_tienda_con_la_configuracion_para_el_navegador(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Construmateriales JF')
            ->assertSee('@construmaterialesjf', false)
            ->assertSee('id="jf-config"', false)
            ->assertSee(asset('logo.webp'), false);
    }

    public function test_el_catalogo_conserva_todo_lo_que_traia_el_sitio_original(): void
    {
        $this->seed(CatalogoSeeder::class);

        $original = File::json(database_path('data/catalog.json'));
        $catalogo = $this->getJson('/api/catalogo')->assertOk()->json();

        $this->assertSame($original['cats'], $catalogo['cats']);
        $this->assertCount(count($original['products']), $catalogo['products']);
        $this->assertEquals($original['home']['tiles'], $catalogo['home']['tiles']);
        $this->assertEquals($original['home']['feat'], $catalogo['home']['feat']);

        $porId = array_column($catalogo['products'], null, 'id');

        foreach ($original['products'] as $producto) {
            $portado = $porId[$producto['id']];

            $this->assertSame($producto['n'], $portado['n']);
            $this->assertSame($producto['r'], $portado['r']);
            $this->assertSame($producto['c'], $portado['c']);
            $this->assertSame($producto['k'], $portado['k']);
            $this->assertSame($producto['nw'] ?? null, $portado['nw'] ?? null);
            $this->assertSame($producto['u'] ?? null, $portado['u'] ?? null);

            foreach ($portado['imgs'] as $imagen) {
                $this->assertFileExists(public_path(parse_url($imagen, PHP_URL_PATH)));
            }
        }
    }

    public function test_el_catalogo_oculta_o_muestra_el_precio_de_referencia_segun_la_configuracion(): void
    {
        $this->seed(CatalogoSeeder::class);

        $this->assertArrayNotHasKey('$', $this->getJson('/api/catalogo')->json('products.0'));

        config(['tienda.mostrar_precios' => true]);

        $this->assertArrayHasKey('$', $this->getJson('/api/catalogo')->json('products.0'));
    }

    public function test_el_catalogo_no_incluye_productos_ocultos_ni_categorias_vacias(): void
    {
        $categoria = Categoria::query()->create(['id' => 'solo-ocultos', 'nombre' => 'Solo ocultos', 'posicion' => 1]);
        Categoria::query()->create(['id' => 'visible', 'nombre' => 'Visible', 'posicion' => 2]);

        Producto::query()->create(['id' => 'a', 'nombre' => 'Oculto', 'categoria_id' => $categoria->id, 'referencias' => [], 'imagenes' => ['media/a.webp'], 'visible' => false]);
        Producto::query()->create(['id' => 'b', 'nombre' => 'Visible', 'categoria_id' => 'visible', 'referencias' => [], 'imagenes' => ['media/b.webp']]);

        $catalogo = $this->getJson('/api/catalogo')->assertOk()->json();

        $this->assertSame(['visible'], array_column($catalogo['cats'], 'id'));
        $this->assertSame(['b'], array_column($catalogo['products'], 'id'));
        $this->assertSame(['visible' => 'b'], $catalogo['home']['tiles']);
    }
}
