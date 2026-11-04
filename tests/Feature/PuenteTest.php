<?php

namespace Tests\Feature;

use App\Models\PuenteEstado;
use App\Models\SaintProducto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PuenteTest extends TestCase
{
    use RefreshDatabase;

    private const SECRETO = 'secreto-de-prueba-con-mas-de-32-caracteres';

    protected function setUp(): void
    {
        parent::setUp();

        config(['tienda.puente.secreto' => self::SECRETO]);
    }

    public function test_rechaza_peticiones_sin_firma(): void
    {
        $this->postJson('/api/puente/cambios', $this->lote())->assertUnauthorized();

        $this->assertDatabaseCount('saint_productos', 0);
    }

    public function test_rechaza_firma_incorrecta(): void
    {
        $this->enviarFirmado('api/puente/cambios', $this->lote(), secreto: 'otro-secreto')->assertUnauthorized();

        $this->assertDatabaseCount('saint_productos', 0);
    }

    public function test_rechaza_firma_vencida(): void
    {
        $this->enviarFirmado('api/puente/cambios', $this->lote(), marca: time() - 301)->assertUnauthorized();
    }

    public function test_no_acepta_escrituras_si_no_hay_secreto_configurado(): void
    {
        config(['tienda.puente.secreto' => null]);

        $this->enviarFirmado('api/puente/cambios', $this->lote())->assertServiceUnavailable();
    }

    public function test_no_acepta_un_secreto_corto(): void
    {
        config(['tienda.puente.secreto' => 'corto']);

        $this->enviarFirmado('api/puente/cambios', $this->lote(), secreto: 'corto')->assertServiceUnavailable();
        $this->assertDatabaseCount('saint_productos', 0);
    }

    public function test_una_peticion_capturada_no_se_puede_repetir(): void
    {
        $marca = time();

        $this->enviarFirmado('api/puente/latido', ['ok' => true], marca: $marca)->assertOk();
        $this->enviarFirmado('api/puente/latido', ['ok' => true], marca: $marca)->assertConflict();
        $this->assertSame(0, PuenteEstado::actual()->filas);
    }

    public function test_las_huellas_exigen_firma(): void
    {
        SaintProducto::factory()->create();

        $this->getJson('/api/puente/huellas')->assertUnauthorized();
    }

    public function test_aplica_cambios_firmados_y_marca_los_datos_como_vigentes(): void
    {
        $this->enviarFirmado('api/puente/cambios', $this->lote())
            ->assertOk()
            ->assertJson(['aplicado' => true, 'items' => 1]);

        $this->assertDatabaseHas('saint_productos', ['codigo' => '1001', 'huella' => 'h1']);
        $this->assertTrue(PuenteEstado::actual()->datosVigentes());
    }

    public function test_actualiza_y_elimina_productos_existentes(): void
    {
        SaintProducto::factory()->create(['codigo' => '1001', 'huella' => 'vieja']);
        SaintProducto::factory()->create(['codigo' => '2002']);

        $lote = $this->lote();
        $lote['eliminados'] = ['2002'];

        $this->enviarFirmado('api/puente/cambios', $lote)->assertOk();

        $this->assertDatabaseHas('saint_productos', ['codigo' => '1001', 'huella' => 'h1']);
        $this->assertDatabaseMissing('saint_productos', ['codigo' => '2002']);
    }

    public function test_ignora_lotes_repetidos_o_viejos(): void
    {
        $this->enviarFirmado('api/puente/cambios', $this->lote(secuencia: 100))->assertOk();

        $viejo = $this->lote(secuencia: 100);
        $viejo['items'][0]['huella'] = 'antigua';

        $this->enviarFirmado('api/puente/cambios', $viejo)
            ->assertOk()
            ->assertJson(['aplicado' => false]);

        $this->assertDatabaseHas('saint_productos', ['codigo' => '1001', 'huella' => 'h1']);
    }

    public function test_entrega_las_huellas_conservando_ceros_a_la_izquierda(): void
    {
        SaintProducto::factory()->create(['codigo' => '001', 'huella' => 'h001']);
        SaintProducto::factory()->create(['codigo' => '8441', 'huella' => 'h8441']);

        $this->firmado('GET', 'api/puente/huellas')
            ->assertOk()
            ->assertExactJson(['huellas' => ['001' => 'h001', '8441' => 'h8441']]);
    }

    public function test_un_latido_con_error_no_renueva_la_vigencia_de_los_datos(): void
    {
        PuenteEstado::actual()->update(['ultimo_dato_en' => now()->subHours(2)]);

        $this->enviarFirmado('api/puente/latido', ['ok' => false, 'modo' => 'sondeo', 'error' => 'Sin conexión'])->assertOk();

        $estado = PuenteEstado::actual();

        $this->assertFalse($estado->datosVigentes());
        $this->assertSame('Sin conexión', $estado->error);
        $this->assertNotNull($estado->ultimo_latido_en);
    }

    public function test_un_latido_correcto_renueva_la_vigencia_y_limpia_el_error(): void
    {
        PuenteEstado::actual()->update(['ultimo_dato_en' => now()->subHours(2), 'error' => 'Antes']);

        $this->enviarFirmado('api/puente/latido', ['ok' => true, 'modo' => 'change_tracking', 'filas' => 7144])->assertOk();

        $estado = PuenteEstado::actual();

        $this->assertTrue($estado->datosVigentes());
        $this->assertNull($estado->error);
        $this->assertSame(7144, $estado->filas);
    }

    /**
     * @return array{secuencia: int, items: list<array<string, mixed>>, eliminados: list<string>}
     */
    private function lote(int $secuencia = 1): array
    {
        return [
            'secuencia' => $secuencia,
            'items' => [[
                'codigo' => '1001',
                'huella' => 'h1',
                'descripcion' => 'MARTILLO',
                'instancia' => 8,
                'unidad' => 'UND',
                'precio1' => '1000.0000',
                'precio2' => '1100.0000',
                'precio3' => '1200.0000',
                'iva' => '19.00',
                'existencia' => '8.0000',
                'activo' => true,
            ]],
            'eliminados' => [],
        ];
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    private function enviarFirmado(string $ruta, array $datos, ?int $marca = null, string $secreto = self::SECRETO): TestResponse
    {
        return $this->firmado('POST', $ruta, json_encode($datos), $marca, $secreto);
    }

    private function firmado(string $metodo, string $ruta, string $cuerpo = '', ?int $marca = null, string $secreto = self::SECRETO): TestResponse
    {
        $marca ??= time();
        $firma = hash_hmac('sha256', "{$marca}.{$metodo}.{$ruta}.{$cuerpo}", $secreto);

        return $this->call($metodo, "/{$ruta}", [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_PUENTE_MARCA' => (string) $marca,
            'HTTP_X_PUENTE_FIRMA' => $firma,
        ], $cuerpo);
    }
}
