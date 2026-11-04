<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CatalogoTienda;
use Illuminate\Http\JsonResponse;

class CatalogoController extends Controller
{
    public function __invoke(CatalogoTienda $catalogo): JsonResponse
    {
        return response()->json($catalogo->datos(), options: JSON_UNESCAPED_UNICODE);
    }
}
