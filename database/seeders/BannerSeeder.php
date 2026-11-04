<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Los banners que traía el sitio original. Solo se cargan si aún no hay ninguno.
     */
    public function run(): void
    {
        if (Banner::query()->exists()) {
            return;
        }

        $banners = [
            [
                'estilo' => 's-img', 'etiqueta' => 'Servicio a domicilio', 'titulo_inicio' => 'Todo en', 'titulo_resaltado' => 'un solo', 'titulo_fin' => 'lugar',
                'texto' => "WhatsApp 323 228 05 97\nTel. 604 605 27 29\nC.C. Quincalla, Sector 14, Local 65",
                'boton_texto' => 'Ver todos los productos →', 'boton_enlace' => '#todo',
                'imagen' => 'banner-jf-1600.webp', 'imagen_movil' => 'banner-jf-900.webp',
                'texto_alternativo' => 'Equipo de JF Construmateriales con productos de plomería, pinturas, grifería, eléctricos, herramientas, ferretería, drywall y repuestos. Servicio a domicilio, WhatsApp 323 228 05 97, teléfono 604 605 27 29, Calle 55 # 58-17, Sector 14, Local 65, Centro Comercial Quincalla, Plaza Minorista',
            ],
            [
                'estilo' => 's-dark', 'etiqueta' => 'Herramienta eléctrica', 'titulo_inicio' => 'Potencia', 'titulo_resaltado' => 'que hace', 'titulo_fin' => 'el trabajo',
                'texto' => 'Taladros, pulidoras, compresores y motosierras de DeWalt, Makita, Ranger, Black+Decker y más.',
                'boton_texto' => 'Ver herramientas →', 'boton_enlace' => '#herramienta',
                'productos' => ['herramienta-79', 'herramienta-35', 'herramienta-9'],
            ],
            [
                'estilo' => 's-mag', 'etiqueta' => 'Depósito y pinturas', 'titulo_inicio' => 'Pinta, sella', 'titulo_resaltado' => 'y protege', 'titulo_fin' => null,
                'texto' => 'Viniltex, Koraza, Sikaflex, estucos, cemento y todo lo de obra en un solo lugar.',
                'boton_texto' => 'Ver depósito →', 'boton_enlace' => '#deposito',
                'productos' => ['deposito-143', 'deposito-146', 'deposito-120'],
            ],
            [
                'estilo' => 's-yel', 'etiqueta' => 'Eléctricos', 'titulo_inicio' => 'Ilumina', 'titulo_resaltado' => 'tu obra', 'titulo_fin' => null,
                'texto' => 'Paneles LED, reflectores, protectores de voltaje, cable, tableros y suichería.',
                'boton_texto' => 'Ver eléctricos →', 'boton_enlace' => '#electricos',
                'productos' => ['electricos-159', 'electricos-174', 'electricos-180'],
            ],
            [
                'estilo' => 's-paper', 'etiqueta' => 'Carta de colores', 'titulo_inicio' => 'Encuentra', 'titulo_resaltado' => 'tu color', 'titulo_fin' => null,
                'texto' => 'Vinilos y esmaltes JF en todos los tonos. Pide la referencia y te la preparamos.',
                'boton_texto' => 'Ver carta de colores →', 'boton_enlace' => '#colores',
                'productos' => ['colores-3', 'colores-2', 'colores-7'],
            ],
        ];

        foreach ($banners as $posicion => $banner) {
            Banner::query()->create($banner + ['posicion' => $posicion]);
        }
    }
}
