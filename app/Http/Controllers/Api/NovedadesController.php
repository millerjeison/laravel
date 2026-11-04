<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ClienteDropbox;
use App\Services\NovedadesTienda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class NovedadesController extends Controller
{
    public function indice(NovedadesTienda $novedades): JsonResponse
    {
        return response()->json($novedades->datos(), options: JSON_UNESCAPED_UNICODE)
            ->header('Cache-Control', 'public, max-age=60');
    }

    /**
     * Sirve una foto del Dropbox y la guarda en disco para no volver a pedirla.
     * La ruta va firmada: solo se aceptan enlaces que este mismo servidor publicó.
     */
    public function foto(Request $request, ClienteDropbox $dropbox): Response
    {
        abort_unless($dropbox->configurado(), Response::HTTP_NOT_FOUND);

        $datos = $request->validate([
            'p' => ['required', 'string', 'max:500'],
            'v' => ['nullable', 'string', 'max:64'],
        ]);

        $archivo = 'fotos/'.hash('sha256', $datos['p'].'|'.($datos['v'] ?? '')).'.jpg';
        $disco = Storage::disk('local');

        if (! $disco->exists($archivo)) {
            $disco->put($archivo, $dropbox->miniatura($datos['p']));
        }

        return response($disco->get($archivo), Response::HTTP_OK, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
