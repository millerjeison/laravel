<?php

namespace Tests\Feature;

use App\Services\AjustesSitio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SeguridadTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_tienda_envia_cabeceras_de_seguridad(): void
    {
        $respuesta = $this->get('/')->assertOk();

        $respuesta->assertHeader('X-Content-Type-Options', 'nosniff');
        $respuesta->assertHeader('X-Frame-Options', 'DENY');
        $respuesta->assertHeaderMissing('X-Powered-By');

        $politica = $respuesta->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("script-src 'self';", $politica);
        $this->assertStringContainsString("frame-ancestors 'none'", $politica);
        $this->assertStringNotContainsString('unsafe-eval', $politica);
    }

    public function test_el_panel_tiene_una_politica_propia_sin_afectar_la_tienda(): void
    {
        $politica = $this->get('/panel/login')->assertOk()->headers->get('Content-Security-Policy');

        $this->assertStringContainsString("'unsafe-eval'", $politica);
        $this->assertStringContainsString("frame-ancestors 'none'", $politica);
    }

    public function test_exige_https_con_hsts_cuando_la_conexion_es_segura(): void
    {
        $this->get('https://localhost/')->assertHeader('Strict-Transport-Security');
    }

    public function test_la_tienda_no_ejecuta_scripts_en_linea(): void
    {
        $html = $this->get('/')->getContent();

        $this->assertDoesNotMatchRegularExpression('/<script(?![^>]*\bsrc=)(?![^>]*application\/json)[^>]*>/i', $html);
        $this->assertDoesNotMatchRegularExpression('/\son(click|load|error|submit)=/i', $html);
        $this->assertStringNotContainsString('onclick="', File::get(public_path('js/tienda.js')));
    }

    public function test_los_datos_del_sitio_no_pueden_romper_el_bloque_de_configuracion(): void
    {
        app(AjustesSitio::class)->guardar(['zona' => '</script><script>alert(1)</script>']);

        $html = $this->get('/')->getContent();

        $this->assertStringNotContainsString('<script>alert(1)', $html);
    }

    public function test_el_disco_local_no_se_sirve_por_ruta(): void
    {
        $this->get('/storage/fotos/cualquiera.jpg')->assertNotFound();
    }

    public function test_los_valores_de_ejemplo_son_seguros_para_produccion(): void
    {
        $ejemplo = File::get(base_path('.env.example'));

        $this->assertStringContainsString('APP_DEBUG=false', $ejemplo);
        $this->assertStringContainsString('APP_ENV=production', $ejemplo);
        $this->assertStringContainsString('SESSION_SECURE_COOKIE=true', $ejemplo);
        $this->assertMatchesRegularExpression('/^PUENTE_SECRETO=$/m', $ejemplo);
    }

    public function test_el_codigo_no_queda_expuesto_si_se_sube_todo_a_la_raiz_del_hosting(): void
    {
        $reglas = File::get(base_path('.htaccess'));

        $this->assertStringContainsString('RewriteRule ^(.*)$ public/$1 [L]', $reglas);
        $this->assertStringContainsString('Options -Indexes', $reglas);
        $this->assertMatchesRegularExpression('/<FilesMatch "[^"]*\\\\\.env[^"]*sqlite[^"]*">\s*Require all denied/', $reglas);
        $this->assertStringContainsString('RewriteCond %{THE_REQUEST} \s/public/ [NC]', File::get(public_path('.htaccess')));
    }

    public function test_nada_subido_a_media_se_puede_ejecutar(): void
    {
        $reglas = File::get(public_path('media/.htaccess'));

        $this->assertStringContainsString('php', $reglas);
        $this->assertStringContainsString('svg', $reglas);
        $this->assertStringContainsString('Require all denied', $reglas);
    }
}
