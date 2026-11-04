<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Services\AjustesSitio;
use Illuminate\Contracts\View\View;

class TiendaController extends Controller
{
    public function __invoke(AjustesSitio $ajustes): View
    {
        return view('tienda', [
            'contacto' => $ajustes->contacto(),
            'mostrarPrecios' => $ajustes->mostrarPrecios(),
            'banners' => Banner::query()->where('activo', true)->orderBy('posicion')->get(),
        ]);
    }
}
