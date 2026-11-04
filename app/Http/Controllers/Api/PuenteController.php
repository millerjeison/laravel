<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CambiosPuenteRequest;
use App\Models\PuenteEstado;
use App\Models\SaintProducto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PuenteController extends Controller
{
    /**
     * Lo que la web ya conoce, para que el puente envíe solo las diferencias.
     */
    public function huellas(): JsonResponse
    {
        return response()->json([
            'huellas' => (object) SaintProducto::query()->pluck('huella', 'codigo')->all(),
        ]);
    }

    /**
     * Aplica un lote de cambios. La secuencia descarta reintentos y lotes viejos.
     */
    public function cambios(CambiosPuenteRequest $request): JsonResponse
    {
        $datos = $request->validated();

        $aplicado = DB::transaction(function () use ($datos): bool {
            $estado = PuenteEstado::query()->lockForUpdate()->firstOrCreate(['id' => 1]);

            if ($datos['secuencia'] <= $estado->secuencia) {
                return false;
            }

            if ($datos['items'] !== []) {
                SaintProducto::query()->upsert(
                    $datos['items'],
                    ['codigo'],
                    ['huella', 'descripcion', 'instancia', 'unidad', 'precio1', 'precio2', 'precio3', 'iva', 'existencia', 'activo'],
                );
            }

            if ($datos['eliminados'] !== []) {
                SaintProducto::query()->whereIn('codigo', $datos['eliminados'])->delete();
            }

            $estado->update(['secuencia' => $datos['secuencia'], 'ultimo_dato_en' => now()]);

            return true;
        });

        Cache::forget(PreciosController::CLAVE_CACHE);

        return response()->json([
            'aplicado' => $aplicado,
            'items' => count($datos['items']),
            'eliminados' => count($datos['eliminados']),
        ]);
    }

    /**
     * Señal de vida. "ok" indica que el puente pudo leer SAINT, y es lo que mantiene vigentes los datos.
     */
    public function latido(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'ok' => ['required', 'boolean'],
            'modo' => ['nullable', 'string', 'max:20'],
            'filas' => ['nullable', 'integer', 'min:0'],
            'error' => ['nullable', 'string', 'max:500'],
        ]);

        $estado = PuenteEstado::actual();

        $estado->update([
            'modo' => $datos['modo'] ?? $estado->modo,
            'filas' => $datos['filas'] ?? $estado->filas,
            'error' => $datos['error'] ?? null,
            'ultimo_latido_en' => now(),
            'ultimo_dato_en' => $datos['ok'] ? now() : $estado->ultimo_dato_en,
        ]);

        return response()->json(['recibido' => true]);
    }
}
