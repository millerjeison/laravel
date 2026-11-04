<?php

namespace Tests\Feature;

use App\Filament\Pages\ContenidoSitio;
use App\Filament\Pages\FotosDropbox;
use App\Filament\Resources\Banners\Pages\CreateBanner;
use App\Filament\Resources\Categorias\Pages\CreateCategoria;
use App\Filament\Resources\Productos\Pages\CreateProducto;
use App\Filament\Resources\Productos\Pages\EditProducto;
use App\Filament\Resources\Productos\Pages\ListProductos;
use App\Filament\Resources\Usuarios\Pages\CreateUsuario;
use App\Models\Banner;
use App\Models\Categoria;
use App\Models\DropboxAjuste;
use App\Models\Producto;
use App\Models\User;
use App\Services\AjustesSitio;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Categoria::query()->create(['id' => 'gas', 'nombre' => 'Gas', 'posicion' => 0]);
    }

    public function test_sin_sesion_no_se_entra_al_panel(): void
    {
        $this->get('/panel')->assertRedirect('/panel/login');
        $this->get('/panel/productos')->assertRedirect('/panel/login');
    }

    public function test_un_usuario_desactivado_no_puede_entrar(): void
    {
        $this->actingAs(User::factory()->create(['activo' => false]))->get('/panel')->assertForbidden();
    }

    public function test_un_usuario_activo_ve_todas_las_secciones(): void
    {
        $this->entrar();

        foreach (['', '/productos', '/categorias', '/banners', '/usuarios', '/contenido-sitio'] as $seccion) {
            $this->get("/panel{$seccion}")->assertOk();
        }
    }

    public function test_crea_un_producto_que_aparece_en_la_tienda_con_su_foto(): void
    {
        Storage::fake('web');
        $this->entrar();

        Livewire::test(CreateProducto::class)
            ->fillForm([
                'nombre' => 'Taladro nuevo',
                'categoria_id' => 'gas',
                'referencias' => ['8441'],
                'visible' => true,
                'imagenes' => [UploadedFile::fake()->image('taladro.jpg')],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $producto = Producto::query()->where('nombre', 'Taladro nuevo')->firstOrFail();

        $this->assertTrue($producto->gestionado);
        $this->assertStringStartsWith('panel-', $producto->id);
        Storage::disk('web')->assertExists($producto->imagenes[0]);
        $this->assertStringStartsWith('media/productos/', $producto->imagenes[0]);

        $this->getJson('/api/catalogo')
            ->assertJsonPath('products.0.n', 'Taladro nuevo')
            ->assertJsonPath('products.0.panel', 1);
    }

    public function test_rechaza_archivos_que_no_son_imagenes_y_svg(): void
    {
        Storage::fake('web');
        $this->entrar();

        foreach ([UploadedFile::fake()->create('virus.php', 10, 'application/x-php'), UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml')] as $archivo) {
            Livewire::test(CreateProducto::class)
                ->fillForm(['nombre' => 'X', 'categoria_id' => 'gas', 'imagenes' => [$archivo]])
                ->call('create')
                ->assertHasFormErrors(['imagenes']);
        }

        $this->assertDatabaseCount('productos', 0);
    }

    public function test_rechaza_referencias_y_unidades_con_html(): void
    {
        Storage::fake('web');
        $this->entrar();

        Livewire::test(CreateProducto::class)
            ->fillForm([
                'nombre' => 'X',
                'categoria_id' => 'gas',
                'unidad' => '<img src=x onerror=alert(1)>',
                'referencias' => ['<script>'],
                'imagenes' => [UploadedFile::fake()->image('a.jpg')],
            ])
            ->call('create')
            ->assertHasFormErrors(['unidad', 'referencias.0']);
    }

    public function test_al_editar_un_producto_pasa_a_ser_del_panel_y_ocultarlo_lo_saca_de_la_tienda(): void
    {
        Storage::fake('web');
        Storage::disk('web')->put('media/productos/prueba.webp', 'x');
        $this->entrar();
        $producto = $this->producto();

        Livewire::test(EditProducto::class, ['record' => $producto->id])
            ->fillForm(['visible' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($producto->fresh()->gestionado);
        $this->assertFalse($producto->fresh()->visible);
        $this->getJson('/api/catalogo')->assertJsonCount(0, 'products');
    }

    public function test_cambiar_las_fotos_quita_la_miniatura_vieja(): void
    {
        $producto = $this->producto(['miniatura' => 'media/productos/vieja-s.webp']);

        $producto->update(['imagenes' => ['media/productos/nueva.webp']]);

        $this->assertNull($producto->fresh()->miniatura);
    }

    public function test_borrar_un_producto_solo_borra_archivos_de_su_carpeta(): void
    {
        Storage::fake('web');
        Storage::disk('web')->put('media/productos/a.webp', 'x');
        Storage::disk('web')->put('logo.webp', 'x');

        $this->producto(['imagenes' => ['media/productos/a.webp', 'logo.webp']])->delete();

        Storage::disk('web')->assertMissing('media/productos/a.webp');
        Storage::disk('web')->assertExists('logo.webp');
    }

    public function test_la_busqueda_de_productos_funciona_por_nombre(): void
    {
        $this->entrar();
        $uno = $this->producto(['id' => 'uno', 'nombre' => 'Martillo']);
        $dos = $this->producto(['id' => 'dos', 'nombre' => 'Cemento']);

        Livewire::test(ListProductos::class)
            ->searchTable('Martillo')
            ->assertCanSeeTableRecords([$uno])
            ->assertCanNotSeeTableRecords([$dos]);
    }

    public function test_el_enlace_de_un_banner_no_acepta_javascript(): void
    {
        $this->entrar();

        Livewire::test(CreateBanner::class)
            ->fillForm([
                'etiqueta' => 'A', 'estilo' => 's-dark', 'titulo_inicio' => 'B', 'boton_texto' => 'C',
                'boton_enlace' => 'javascript:alert(1)', 'posicion' => 0, 'activo' => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['boton_enlace']);

        $this->assertDatabaseCount('banners', 0);
    }

    public function test_un_banner_nuevo_sale_en_la_tienda_con_el_texto_escapado(): void
    {
        $this->entrar();

        Livewire::test(CreateBanner::class)
            ->fillForm([
                'etiqueta' => 'Oferta', 'estilo' => 's-dark', 'titulo_inicio' => '<b>Gran</b> venta', 'boton_texto' => 'Ver',
                'boton_enlace' => '#gas', 'posicion' => 0, 'activo' => true, 'texto' => "Línea 1\nLínea 2",
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame(1, Banner::query()->count());

        $this->get('/')
            ->assertSee('Oferta')
            ->assertSee('&lt;b&gt;Gran&lt;/b&gt; venta', false)
            ->assertSee('Línea 1<br />', false);
    }

    public function test_el_nombre_de_una_categoria_no_acepta_html(): void
    {
        $this->entrar();

        Livewire::test(CreateCategoria::class)
            ->fillForm(['nombre' => '<img src=x>', 'id' => 'nueva', 'posicion' => 1])
            ->call('create')
            ->assertHasFormErrors(['nombre']);
    }

    public function test_los_datos_del_sitio_cambian_la_tienda_y_la_publicacion_de_precios(): void
    {
        $this->entrar();

        Livewire::test(ContenidoSitio::class)
            ->fillForm([
                'whatsapp_ventas' => '573001112233',
                'telefono_ventas' => '300 111 22 33',
                'mostrar_precios' => true,
                'lista_precio' => 2,
            ])
            ->call('guardar')
            ->assertHasNoFormErrors();

        $ajustes = app(AjustesSitio::class);

        $this->assertTrue($ajustes->mostrarPrecios());
        $this->assertSame(2, $ajustes->listaPrecio());

        $this->get('/')
            ->assertSee('300 111 22 33')
            ->assertSee('https://wa.me/573001112233', false)
            ->assertDontSee('https://wa.me/573232280597', false);
    }

    public function test_el_whatsapp_solo_acepta_numeros(): void
    {
        $this->entrar();

        Livewire::test(ContenidoSitio::class)
            ->fillForm(['whatsapp_ventas' => '"><script>'])
            ->call('guardar')
            ->assertHasFormErrors(['whatsapp_ventas']);
    }

    public function test_oculta_y_renombra_fotos_de_dropbox(): void
    {
        config(['tienda.worker_url' => 'https://worker.test']);
        Http::fake(['worker.test/novedades' => Http::response(['items' => [[
            'id' => 'id:abc', 'titulo' => 'Machete ref100', 'categoria' => '1 AGRICOLA',
            'fecha' => '2026-10-28T03:59:15Z', 'foto' => 'https://worker.test/foto?p=a.jpg',
        ]]])]);
        $this->entrar();

        Livewire::test(FotosDropbox::class)
            ->assertSee('Machete ref100')
            ->callAction(TestAction::make('alternar')->table('id:abc'));

        $this->assertTrue(DropboxAjuste::query()->findOrFail('id:abc')->oculto);
        $this->getJson('/api/novedades')->assertJsonCount(0, 'items');
    }

    public function test_las_claves_del_panel_deben_ser_fuertes(): void
    {
        $this->entrar();

        Livewire::test(CreateUsuario::class)
            ->fillForm(['name' => 'Otro', 'email' => 'otro@jf.test', 'password' => '12345678', 'activo' => true])
            ->call('create')
            ->assertHasFormErrors(['password']);

        $this->assertDatabaseMissing('users', ['email' => 'otro@jf.test']);
    }

    public function test_el_secreto_de_dos_pasos_no_se_guarda_en_claro(): void
    {
        $usuario = User::factory()->create();

        $usuario->saveAppAuthenticationSecret('SECRETO123');

        $this->assertSame('SECRETO123', $usuario->fresh()->getAppAuthenticationSecret());
        $this->assertDatabaseMissing('users', ['app_authentication_secret' => 'SECRETO123']);
        $this->assertArrayNotHasKey('app_authentication_secret', $usuario->fresh()->toArray());
    }

    private function entrar(): User
    {
        $usuario = User::factory()->create();
        $this->actingAs($usuario);

        return $usuario;
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    private function producto(array $datos = []): Producto
    {
        return Producto::query()->create($datos + [
            'id' => 'prueba',
            'nombre' => 'Producto',
            'categoria_id' => 'gas',
            'referencias' => [],
            'imagenes' => ['media/productos/prueba.webp'],
        ]);
    }
}
