<?php

namespace App\Services;

use App\Http\Controllers\Api\PreciosController;
use App\Models\Ajuste;
use Illuminate\Support\Facades\Cache;

class AjustesSitio
{
    private const CLAVE_CACHE = 'tienda:ajustes';

    public const CONTACTO = [
        'whatsapp_ventas' => '573232280597',
        'telefono_ventas' => '323 228 05 97',
        'whatsapp_compras' => '573016268860',
        'telefono_compras' => '301 626 88 60',
        'correo' => 'deposito-ferreteriajf@hotmail.com',
        'redes' => '@construmaterialesjf',
        'direccion' => 'Calle 55 # 58-17, Sector 14, Local 65, Centro Comercial Quincalla (Plaza Minorista), Medellín',
        'direccion_corta' => 'C.C. Quincalla, Sector 14, Local 65',
        'ubicacion' => 'Plaza Minorista · Sector 14',
        'zona' => 'Plaza Minorista',
        'lugar_mapa' => 'Construmateriales JF SAS, Cl. 55 #58-17, Plaza Minorista, Medellín',
        'eslogan' => '¡Todo en un solo lugar!',
        'descripcion_seo' => 'Ferretería en la Plaza Minorista de Medellín: herramientas, pinturas, eléctricos, plomería, drywall y más. Pide por WhatsApp con domicilio. 323 228 05 97.',
    ];

    /**
     * @return array<string, string>
     */
    public function contacto(): array
    {
        $contacto = array_merge(self::CONTACTO, array_intersect_key(array_filter($this->guardados(), filled(...)), self::CONTACTO));
        $lugar = urlencode($contacto['lugar_mapa']);

        return $contacto + [
            'maps_buscar' => "https://www.google.com/maps/search/?api=1&query={$lugar}",
            'maps_destino' => "https://www.google.com/maps/dir/?api=1&destination={$lugar}",
        ];
    }

    public function mostrarPrecios(): bool
    {
        return (bool) ($this->guardados()['mostrar_precios'] ?? config('tienda.mostrar_precios'));
    }

    public function mostrarExistencias(): bool
    {
        return (bool) ($this->guardados()['mostrar_existencias'] ?? config('tienda.mostrar_existencias'));
    }

    public function listaPrecio(): int
    {
        return (int) ($this->guardados()['lista_precio'] ?? config('tienda.lista_precio'));
    }

    /**
     * @param  array<string, mixed>  $valores
     */
    public function guardar(array $valores): void
    {
        foreach ($valores as $clave => $valor) {
            Ajuste::query()->updateOrCreate(['clave' => $clave], ['valor' => is_bool($valor) ? (string) (int) $valor : $valor]);
        }

        Cache::forget(self::CLAVE_CACHE);
        Cache::forget(PreciosController::CLAVE_CACHE);
    }

    /**
     * @return array<string, string|null>
     */
    private function guardados(): array
    {
        return Cache::rememberForever(self::CLAVE_CACHE, fn (): array => Ajuste::query()->pluck('valor', 'clave')->all());
    }
}
