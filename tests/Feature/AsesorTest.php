<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AsesorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['tienda.worker_url' => 'https://worker.test', 'tienda.ia.api_key' => null]);
    }

    public function test_sin_clave_propia_reenvia_la_consulta_al_worker(): void
    {
        Http::fake(['worker.test/asesor' => Http::response(['respuesta' => 'Listo', 'materiales' => []])]);

        $this->postJson('/api/asesor', ['mensaje' => 'voy a pintar', 'historial' => [], 'categorias' => ['Gas']])
            ->assertOk()
            ->assertJsonPath('respuesta', 'Listo');

        Http::assertSent(fn (Request $peticion): bool => $peticion['mensaje'] === 'voy a pintar');
    }

    public function test_si_el_asesor_no_responde_devuelve_503_para_que_la_tienda_use_la_busqueda_local(): void
    {
        Http::fake(['worker.test/*' => Http::response('caído', 500)]);

        $this->postJson('/api/asesor', ['mensaje' => 'hola'])->assertServiceUnavailable();
        $this->postJson('/api/recomendar', ['q' => 'cable', 'items' => [['id' => 'a', 'n' => 'Cable']]])->assertServiceUnavailable();
    }

    public function test_valida_el_tamano_de_lo_que_llega_del_navegador(): void
    {
        Http::fake();

        $this->postJson('/api/asesor', ['mensaje' => str_repeat('a', 1001)])->assertUnprocessable();
        $this->postJson('/api/asesor', [])->assertUnprocessable();
        $this->postJson('/api/recomendar', ['q' => 'x', 'items' => array_fill(0, 16, ['id' => 'a', 'n' => 'b'])])->assertUnprocessable();

        Http::assertNothingSent();
    }

    public function test_limita_las_consultas_por_minuto(): void
    {
        Http::fake(['worker.test/*' => Http::response(['respuesta' => 'ok'])]);

        foreach (range(1, 20) as $intento) {
            $this->postJson('/api/asesor', ['mensaje' => 'hola'])->assertOk();
        }

        $this->postJson('/api/asesor', ['mensaje' => 'hola'])->assertTooManyRequests();
    }
}
