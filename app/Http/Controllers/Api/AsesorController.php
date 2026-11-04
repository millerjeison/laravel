<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AsesorIa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AsesorController extends Controller
{
    public function asesor(Request $request, AsesorIa $asesor): JsonResponse
    {
        $datos = $request->validate([
            'mensaje' => ['required', 'string', 'max:1000'],
            'historial' => ['nullable', 'array', 'max:8'],
            'historial.*.rol' => ['required', 'string', 'in:cliente,asesor'],
            'historial.*.texto' => ['required', 'string', 'max:2000'],
            'categorias' => ['nullable', 'array', 'max:30'],
            'categorias.*' => ['string', 'max:80'],
        ]);

        return $this->responder($asesor->asesorar($datos));
    }

    public function recomendar(Request $request, AsesorIa $asesor): JsonResponse
    {
        $datos = $request->validate([
            'q' => ['required', 'string', 'max:200'],
            'items' => ['required', 'array', 'min:1', 'max:15'],
            'items.*.id' => ['required', 'string', 'max:60'],
            'items.*.n' => ['required', 'string', 'max:200'],
            'items.*.c' => ['nullable', 'string', 'max:80'],
        ]);

        return $this->responder($asesor->recomendar($datos));
    }

    /**
     * Sin respuesta del modelo, la tienda usa su búsqueda local.
     *
     * @param  array<string, mixed>|null  $resultado
     */
    private function responder(?array $resultado): JsonResponse
    {
        return $resultado === null
            ? response()->json(['mensaje' => 'Asesor no disponible'], Response::HTTP_SERVICE_UNAVAILABLE)
            : response()->json($resultado, options: JSON_UNESCAPED_UNICODE);
    }
}
