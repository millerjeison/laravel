<?php

namespace Tests\Feature;

use App\Models\DropboxAjuste;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class NovedadesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        config(['tienda.worker_url' => 'https://worker.test']);
    }

    public function test_sin_dropbox_propio_consulta_el_worker_y_guarda_el_resultado(): void
    {
        Http::fake(['worker.test/novedades' => Http::response(['items' => [$this->item('a', 'Uno')]])]);

        $this->getJson('/api/novedades')->assertOk()->assertJsonPath('items.0.titulo', 'Uno');
        $this->getJson('/api/novedades')->assertOk();

        Http::assertSentCount(1);
    }

    public function test_si_el_origen_falla_sirve_la_ultima_lista_buena(): void
    {
        Http::fakeSequence('worker.test/*')
            ->push(['items' => [$this->item('a', 'Uno')]])
            ->push('caído', 500);

        $this->getJson('/api/novedades')->assertJsonPath('items.0.id', 'a');

        Cache::forget('tienda:novedades');

        $this->getJson('/api/novedades')->assertOk()->assertJsonPath('items.0.id', 'a');
    }

    public function test_si_el_origen_falla_sin_respaldo_devuelve_lista_vacia(): void
    {
        Http::fake(['worker.test/*' => Http::response('caído', 500)]);

        $this->getJson('/api/novedades')->assertOk()->assertExactJson(['items' => []]);
    }

    public function test_con_dropbox_propio_arma_las_novedades_con_la_misma_forma_del_worker(): void
    {
        $this->configurarDropbox();

        Http::fake([
            'api.dropboxapi.com/oauth2/token' => Http::response(['access_token' => 'tok']),
            'api.dropboxapi.com/2/files/list_folder' => Http::response([
                'entries' => [
                    $this->archivo('id:1', '/5 HERRAMIENTA ELÉCTRICA/polichadora blackdecker ref2610-WA0138.jpg', '2026-10-27T00:00:00Z'),
                    ['.tag' => 'folder', 'name' => '1 AGRICOLA', 'path_display' => '/1 AGRICOLA', 'id' => 'id:c'],
                ],
                'has_more' => true,
                'cursor' => 'c1',
            ]),
            'api.dropboxapi.com/2/files/list_folder/continue' => Http::response([
                'entries' => [
                    $this->archivo('id:2', '/1 AGRICOLA/machete ref100.png', '2026-10-28T00:00:00Z'),
                    $this->archivo('id:3', '/1 AGRICOLA/lista.pdf', '2026-10-28T00:00:00Z'),
                ],
                'has_more' => false,
                'cursor' => 'c2',
            ]),
        ]);

        $respuesta = $this->getJson('/api/novedades')->assertOk();

        $respuesta->assertJsonCount(2, 'items')
            ->assertJsonPath('items.0.titulo', 'Machete ref100')
            ->assertJsonPath('items.0.categoria', '1 AGRICOLA')
            ->assertJsonPath('items.1.titulo', 'Polichadora blackdecker ref2610 WA0138')
            ->assertJsonPath('items.1.categoria', '5 HERRAMIENTA ELÉCTRICA');

        $this->assertStringContainsString('/api/foto?', $respuesta->json('items.0.foto'));
        Http::assertNotSent(fn (Request $peticion): bool => str_contains($peticion->url(), 'worker.test'));
    }

    public function test_la_foto_se_descarga_una_vez_y_queda_en_disco(): void
    {
        Storage::fake('local');
        $this->configurarDropbox();

        Http::fake([
            'api.dropboxapi.com/oauth2/token' => Http::response(['access_token' => 'tok']),
            'content.dropboxapi.com/*' => Http::response('jpeg-binario'),
        ]);

        $ruta = URL::signedRoute('api.foto', ['p' => '/1 AGRICOLA/machete.jpg', 'v' => 'r1'], absolute: false);

        $this->get($ruta)->assertOk()->assertHeader('Content-Type', 'image/jpeg');
        $this->assertSame('jpeg-binario', $this->get($ruta)->getContent());

        Http::assertSentCount(2);
    }

    public function test_la_foto_solo_acepta_enlaces_firmados_por_el_servidor(): void
    {
        Storage::fake('local');
        $this->configurarDropbox();
        Http::fake();

        $this->get('/api/foto?'.http_build_query(['p' => '/Privado/contrato.jpg']))->assertForbidden();

        $firmada = URL::signedRoute('api.foto', ['p' => '/1 AGRICOLA/machete.jpg', 'v' => 'r1'], absolute: false);
        $alterada = str_replace('machete', 'contrato', $firmada);

        $this->get($alterada)->assertForbidden();
        $this->get(str_replace('v=r1', 'v=r2', $firmada))->assertForbidden();

        Http::assertNothingSent();
    }

    public function test_descarta_novedades_con_datos_que_podrian_inyectar_html(): void
    {
        Http::fake(['worker.test/novedades' => Http::response(['items' => [
            $this->item('bueno', 'Bueno'),
            ['foto' => 'https://x.test/a.jpg" onerror="alert(1)'] + $this->item('foto-mala', 'Mala'),
            ['fecha' => '<img src=x>'] + $this->item('fecha-mala', 'Mala'),
            ['foto' => 'javascript:alert(1)'] + $this->item('esquema-malo', 'Malo'),
            'no-es-un-item',
        ]])]);

        $ids = array_column($this->getJson('/api/novedades')->assertOk()->json('items'), 'id');

        $this->assertSame(['bueno'], $ids);
    }

    public function test_aplica_lo_que_jf_oculto_o_renombro_en_el_panel(): void
    {
        DropboxAjuste::query()->create(['item_id' => 'oculta', 'oculto' => true]);
        DropboxAjuste::query()->create(['item_id' => 'renombrada', 'titulo' => 'Nombre corregido']);

        Http::fake(['worker.test/novedades' => Http::response(['items' => [
            $this->item('oculta', 'Se va'),
            $this->item('renombrada', 'nombre malo'),
        ]])]);

        $this->getJson('/api/novedades')
            ->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.titulo', 'Nombre corregido');
    }

    /**
     * @return array<string, string>
     */
    private function item(string $id, string $titulo): array
    {
        return [
            'id' => $id,
            'titulo' => $titulo,
            'categoria' => '1 AGRICOLA',
            'fecha' => '2026-10-28T03:59:15Z',
            'foto' => 'https://worker.test/foto?p=a.jpg&v=1',
        ];
    }

    private function configurarDropbox(string $carpeta = ''): void
    {
        config(['tienda.dropbox' => [
            'app_key' => 'k',
            'app_secret' => 's',
            'refresh_token' => 'r',
            'carpeta' => $carpeta,
        ]]);
    }

    /**
     * @return array<string, string>
     */
    private function archivo(string $id, string $ruta, string $fecha): array
    {
        return [
            '.tag' => 'file',
            'id' => $id,
            'name' => basename($ruta),
            'path_display' => $ruta,
            'server_modified' => $fecha,
            'rev' => 'abc123',
        ];
    }
}
