<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PuenteEstado;
use App\Models\SaintProducto;
use App\Services\AjustesSitio;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PreciosController extends Controller
{
    public const CLAVE_CACHE = 'tienda:precios';

    /**
     * Precios y existencias en vivo por código SAINT, según lo que JF decidió publicar.
     */
    public function __invoke(AjustesSitio $ajustes): JsonResponse
    {
        $mostrarPrecios = $ajustes->mostrarPrecios();
        $mostrarExistencias = $ajustes->mostrarExistencias();
        $estado = PuenteEstado::actual();

        if ((! $mostrarPrecios && ! $mostrarExistencias) || ! $estado->datosVigentes()) {
            return response()->json(['actualizado' => null, 'items' => (object) []]);
        }

        $items = Cache::remember(self::CLAVE_CACHE, now()->addMinute(), function () use ($mostrarPrecios, $mostrarExistencias): array {
            return SaintProducto::query()->publicable()->get()
                ->mapWithKeys(fn (SaintProducto $producto): array => [
                    $producto->codigo => array_filter([
                        'p' => $mostrarPrecios ? $producto->precioPublico() : null,
                        'e' => $mostrarExistencias ? max(0, (int) floor($producto->existencia)) : null,
                    ], fn (?int $valor): bool => $valor !== null),
                ])->all();
        });

        return response()->json([
            'actualizado' => $estado->ultimo_dato_en->toIso8601String(),
            'items' => (object) $items,
        ]);
    }
}
