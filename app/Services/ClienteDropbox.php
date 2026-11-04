<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ClienteDropbox
{
    public function configurado(): bool
    {
        return filled(config('tienda.dropbox.refresh_token'));
    }

    /**
     * Todos los archivos de la carpeta de fotos, recorriendo la paginación de Dropbox.
     *
     * @return list<array<string, mixed>>
     *
     * @throws RequestException
     */
    public function listarArchivos(): array
    {
        $respuesta = $this->api()->post('https://api.dropboxapi.com/2/files/list_folder', [
            'path' => config('tienda.dropbox.carpeta'),
            'recursive' => true,
            'limit' => 2000,
        ])->throw()->json();

        $archivos = $respuesta['entries'];

        while ($respuesta['has_more']) {
            $respuesta = $this->api()->post('https://api.dropboxapi.com/2/files/list_folder/continue', [
                'cursor' => $respuesta['cursor'],
            ])->throw()->json();

            array_push($archivos, ...$respuesta['entries']);
        }

        return array_values(array_filter($archivos, fn (array $entrada): bool => $entrada['.tag'] === 'file'));
    }

    /**
     * Miniatura JPEG de una foto del Dropbox.
     *
     * @throws RequestException
     */
    public function miniatura(string $ruta): string
    {
        return Http::withToken($this->token())
            ->withHeaders([
                'Dropbox-API-Arg' => json_encode([
                    'resource' => ['.tag' => 'path', 'path' => $ruta],
                    'format' => 'jpeg',
                    'size' => 'w960h640',
                    'mode' => 'fitone_bestfit',
                ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG),
                'Content-Type' => 'text/plain',
            ])
            ->withBody('')
            ->post('https://content.dropboxapi.com/2/files/get_thumbnail_v2')
            ->throw()
            ->body();
    }

    private function api(): PendingRequest
    {
        return Http::withToken($this->token())->acceptJson();
    }

    /**
     * Los tokens de Dropbox duran cuatro horas; se renuevan con el refresh token.
     */
    private function token(): string
    {
        return Cache::remember('tienda:dropbox-token', now()->addHours(3), function (): string {
            return Http::asForm()
                ->withBasicAuth(config('tienda.dropbox.app_key'), config('tienda.dropbox.app_secret'))
                ->post('https://api.dropboxapi.com/oauth2/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => config('tienda.dropbox.refresh_token'),
                ])
                ->throw()
                ->json('access_token');
        });
    }
}
