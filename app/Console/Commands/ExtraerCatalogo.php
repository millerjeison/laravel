<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('tienda:extraer-catalogo {origen : Carpeta del sitio estático original}')]
#[Description('Convierte las imágenes en base64 del sitio original en archivos y copia catalog.json')]
class ExtraerCatalogo extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $origen = rtrim($this->argument('origen'), '/');

        if (! File::exists("{$origen}/catalog.json")) {
            $this->error("No se encontró catalog.json en {$origen}");

            return self::FAILURE;
        }

        $destino = public_path('media/productos');
        File::ensureDirectoryExists($destino);

        $total = 0;

        foreach (File::glob("{$origen}/img/*.json") as $bloque) {
            foreach (File::json($bloque) as $clave => $imagen) {
                $total += $this->guardar($imagen, "{$destino}/".str_replace('.', '-', $clave).'.webp');
            }
        }

        foreach (File::json("{$origen}/home.json") as $clave => $imagen) {
            $total += $this->guardar($imagen, "{$destino}/".str_replace('.', '-', $clave).'-s.webp');
        }

        File::copy("{$origen}/catalog.json", database_path('data/catalog.json'));

        $this->info("Imágenes escritas: {$total}");

        return self::SUCCESS;
    }

    private function guardar(string $dataUri, string $ruta): int
    {
        $binario = base64_decode(substr($dataUri, strpos($dataUri, ',') + 1), true);

        if ($binario === false) {
            $this->warn("Imagen inválida: {$ruta}");

            return 0;
        }

        File::put($ruta, $binario);

        return 1;
    }
}
