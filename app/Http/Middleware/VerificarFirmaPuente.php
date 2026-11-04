<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class VerificarFirmaPuente
{
    private const LARGO_MINIMO_SECRETO = 32;

    /**
     * Solo el puente de la tienda puede hablar con estas rutas: HMAC-SHA256 de "marca.MÉTODO.ruta.cuerpo".
     * Cada firma vale una sola vez, así una petición capturada no se puede repetir.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secreto = config('tienda.puente.secreto');
        $marca = $request->header('X-Puente-Marca');
        $firma = $request->header('X-Puente-Firma');

        abort_if(strlen((string) $secreto) < self::LARGO_MINIMO_SECRETO, Response::HTTP_SERVICE_UNAVAILABLE, 'Puente sin configurar');
        abort_unless(is_string($marca) && is_string($firma) && ctype_digit($marca), Response::HTTP_UNAUTHORIZED, 'Firma ausente');
        abort_if(
            abs(time() - (int) $marca) > config('tienda.puente.ventana_firma_segundos'),
            Response::HTTP_UNAUTHORIZED,
            'Firma vencida',
        );

        $esperada = hash_hmac('sha256', implode('.', [
            $marca,
            $request->method(),
            $request->route()->uri(),
            $request->getContent(),
        ]), $secreto);

        abort_unless(hash_equals($esperada, $firma), Response::HTTP_UNAUTHORIZED, 'Firma inválida');

        abort_unless(
            Cache::add("puente:firma:{$firma}", true, config('tienda.puente.ventana_firma_segundos') * 2),
            Response::HTTP_CONFLICT,
            'Firma repetida',
        );

        return $next($request);
    }
}
