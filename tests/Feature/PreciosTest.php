<?php

namespace Tests\Feature;

use App\Models\PuenteEstado;
use App\Models\SaintProducto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PreciosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        PuenteEstado::actual()->update(['ultimo_dato_en' => now()]);
        config(['tienda.mostrar_precios' => true, 'tienda.mostrar_existencias' => true, 'tienda.lista_precio' => 3]);
    }

    public function test_no_publica_nada_con_los_valores_por_defecto(): void
    {
        config(['tienda.mostrar_precios' => false, 'tienda.mostrar_existencias' => false]);
        SaintProducto::factory()->create(['codigo' => '1001']);

        $this->getJson('/api/precios')->assertOk()->assertExactJson(['actualizado' => null, 'items' => []]);
    }

    public function test_el_precio_publico_suma_el_iva_de_cada_producto_a_la_lista_configurada(): void
    {
        SaintProducto::factory()->create(['codigo' => '1001', 'precio3' => 1344.5378, 'iva' => 19]);
        SaintProducto::factory()->create(['codigo' => '1002', 'precio3' => 1000, 'iva' => 5]);
        SaintProducto::factory()->create(['codigo' => '1003', 'precio3' => 1000, 'iva' => 0]);
        SaintProducto::factory()->create(['codigo' => '1004', 'precio3' => 1000, 'iva' => null]);

        $this->getJson('/api/precios')
            ->assertJsonPath('items.1001.p', 1600)
            ->assertJsonPath('items.1002.p', 1050)
            ->assertJsonPath('items.1003.p', 1000)
            ->assertJsonPath('items.1004.p', 1190);
    }

    public function test_respeta_la_lista_de_precios_elegida(): void
    {
        config(['tienda.lista_precio' => 1]);
        SaintProducto::factory()->create(['codigo' => '1001', 'precio1' => 1000, 'precio3' => 5000, 'iva' => 0]);

        $this->getJson('/api/precios')->assertJsonPath('items.1001.p', 1000);
    }

    public function test_excluye_codigos_reciclados_inactivos_e_instancias_internas(): void
    {
        SaintProducto::factory()->create(['codigo' => '1000']);
        SaintProducto::factory()->create(['codigo' => '2137', 'descripcion' => 'XX']);
        SaintProducto::factory()->create(['codigo' => '2138', 'descripcion' => 'xx viejo']);
        SaintProducto::factory()->create(['codigo' => '2139', 'descripcion' => '  ']);
        SaintProducto::factory()->create(['codigo' => '3000', 'activo' => false]);
        SaintProducto::factory()->create(['codigo' => '4000', 'instancia' => 1]);
        SaintProducto::factory()->create(['codigo' => '4001', 'instancia' => 2]);

        $items = $this->getJson('/api/precios')->json('items');

        $this->assertSame(['1000'], array_map('strval', array_keys($items)));
    }

    public function test_sin_existencias_publicadas_no_envia_la_clave_e(): void
    {
        config(['tienda.mostrar_existencias' => false]);
        SaintProducto::factory()->create(['codigo' => '1001']);

        $item = $this->getJson('/api/precios')->json('items.1001');

        $this->assertArrayHasKey('p', $item);
        $this->assertArrayNotHasKey('e', $item);
    }

    public function test_las_existencias_negativas_se_publican_como_cero(): void
    {
        SaintProducto::factory()->create(['codigo' => '1001', 'existencia' => -43]);

        $this->getJson('/api/precios')->assertJsonPath('items.1001.e', 0);
    }

    public function test_deja_de_publicar_si_el_puente_lleva_mucho_sin_leer_saint(): void
    {
        SaintProducto::factory()->create(['codigo' => '1001']);
        PuenteEstado::actual()->update(['ultimo_dato_en' => now()->subMinutes(31)]);

        $this->getJson('/api/precios')->assertExactJson(['actualizado' => null, 'items' => []]);
    }
}
