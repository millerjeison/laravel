<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CabecerasSeguridad
{
    /**
     * La tienda no ejecuta scripts en línea. El panel necesita algo más de margen porque
     * Filament usa Alpine y Livewire, que evalúan expresiones.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $respuesta = $next($request);

        $respuesta->headers->set('X-Content-Type-Options', 'nosniff');
        $respuesta->headers->set('X-Frame-Options', 'DENY');
        $respuesta->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $respuesta->headers->set('Permissions-Policy', 'camera=(), geolocation=(), payment=(), microphone=(self)');
        $respuesta->headers->set('Content-Security-Policy', $this->politica($this->esPanel($request)));
        $respuesta->headers->remove('X-Powered-By');
        header_remove('X-Powered-By');

        if ($request->isSecure()) {
            $respuesta->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $respuesta;
    }

    private function esPanel(Request $request): bool
    {
        return $request->is(config('tienda.panel_ruta'), config('tienda.panel_ruta').'/*', 'livewire*');
    }

    private function politica(bool $panel): string
    {
        $scripts = $panel ? "'self' 'unsafe-inline' 'unsafe-eval'" : "'self'";
        $estilos = "'self' 'unsafe-inline' https://fonts.googleapis.com".($panel ? ' https://fonts.bunny.net' : '');
        $fuentes = "'self' data: https://fonts.gstatic.com".($panel ? ' https://fonts.bunny.net' : '');

        return implode('; ', [
            "default-src 'self'",
            "script-src {$scripts}",
            "style-src {$estilos}",
            "font-src {$fuentes}",
            "img-src 'self' data: blob: https:",
            "connect-src 'self'",
            'worker-src '.($panel ? "'self' blob:" : "'none'"),
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]);
    }
}
