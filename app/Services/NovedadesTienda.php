<?php

namespace App\Services;

use App\Models\DropboxAjuste;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;

class NovedadesTienda
{
    private const CLAVE_RESPALDO = 'tienda:novedades:ultimo';

    public function __construct(private ClienteDropbox $dropbox) {}

    /**
     * Las fotos del Dropbox con lo que JF ocultó o renombró en el panel.
     *
     * @return array{actualizado?: string, categorias?: array<int, string>, items: list<array<string, mixed>>}
     */
    public function datos(): array
    {
        $datos = $this->originales();
        $ajustes = DropboxAjuste::query()->get()->keyBy('item_id');

        $datos['items'] = collect($datos['items'])
            ->reject(fn (array $item): bool => (bool) $ajustes->get($item['id'])?->oculto)
            ->map(fn (array $item): array => ['titulo' => $ajustes->get($item['id'])?->titulo ?: $item['titulo']] + $item)
            ->values()
            ->all();

        return $datos;
    }

    /**
     * Las fotos tal como vienen del origen. Si falla se sirve la última lista buena.
     *
     * @return array{actualizado?: string, categorias?: array<int, string>, items: list<array<string, mixed>>}
     */
    public function originales(): array
    {
        return Cache::remember('tienda:novedades', config('tienda.novedades_cache_segundos'), function (): array {
            try {
                $datos = $this->dropbox->configurado() ? $this->desdeDropbox() : $this->desdeWorker();
                $datos['items'] = $this->sanear($datos['items'] ?? []);
                Cache::forever(self::CLAVE_RESPALDO, $datos);

                return $datos;
            } catch (Throwable $error) {
                report($error);

                return Cache::get(self::CLAVE_RESPALDO, ['items' => []]);
            }
        });
    }

    /**
     * La tienda inserta estos valores en su HTML: se descarta lo que no tenga la forma esperada.
     *
     * @param  array<int, mixed>  $items
     * @return list<array<string, mixed>>
     */
    private function sanear(array $items): array
    {
        return array_values(array_filter($items, fn (mixed $item): bool => is_array($item)
            && is_string($item['id'] ?? null)
            && is_string($item['titulo'] ?? null)
            && is_string($item['categoria'] ?? null)
            && preg_match('/^\d{4}-\d{2}-\d{2}T[\d:.]+Z?$/', (string) ($item['fecha'] ?? ''))
            && preg_match('/^(https?:\/\/|\/)[^\s"\'<>]+$/', (string) ($item['foto'] ?? ''))));
    }

    /**
     * @return array{items: list<array<string, mixed>>}
     */
    private function desdeWorker(): array
    {
        return Http::timeout(20)
            ->get(rtrim(config('tienda.worker_url'), '/').'/novedades')
            ->throw()
            ->json();
    }

    /**
     * @return array{actualizado: string, categorias: list<string>, items: list<array<string, mixed>>}
     */
    private function desdeDropbox(): array
    {
        $raiz = rtrim(config('tienda.dropbox.carpeta'), '/');

        $items = collect($this->dropbox->listarArchivos())
            ->filter(fn (array $archivo): bool => (bool) preg_match('/\.(jpe?g|png|webp|heic)$/i', $archivo['name']))
            ->map(fn (array $archivo): array => [
                'id' => $archivo['id'],
                'titulo' => $this->titulo($archivo['name']),
                'categoria' => Str::before(ltrim(Str::after($archivo['path_display'], $raiz), '/'), '/'),
                'fecha' => $archivo['server_modified'],
                'foto' => URL::signedRoute('api.foto', ['p' => $archivo['path_display'], 'v' => $archivo['rev']], absolute: false),
            ])
            ->sortByDesc('fecha')
            ->values();

        return [
            'actualizado' => now()->toIso8601String(),
            'categorias' => $items->pluck('categoria')->unique()->values()->all(),
            'items' => $items->all(),
        ];
    }

    /**
     * "polichadora blackdecker ref2610-WA0138.jpg" => "Polichadora blackdecker ref2610 WA0138"
     */
    private function titulo(string $nombreArchivo): string
    {
        return Str::ucfirst(trim(str_replace(['-', '_'], ' ', pathinfo($nombreArchivo, PATHINFO_FILENAME))));
    }
}
