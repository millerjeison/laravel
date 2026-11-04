<?php

return [

    'panel_ruta' => env('JF_PANEL_RUTA', 'panel'),

    /*
    |--------------------------------------------------------------------------
    | Publicación de datos de SAINT
    |--------------------------------------------------------------------------
    |
    | Ambos arrancan apagados: la tienda muestra "Consultar precio" y
    | "Existencias por confirmar" hasta que JF revise los datos en vivo.
    |
    */

    'mostrar_precios' => (bool) env('JF_MOSTRAR_PRECIOS', false),

    'mostrar_existencias' => (bool) env('JF_MOSTRAR_EXISTENCIAS', false),

    'lista_precio' => (int) env('JF_LISTA_PRECIO', 3),

    'iva_defecto' => (float) env('JF_IVA_DEFECTO', 19),

    // Instancias internas de SAINT: 1 IMPUESTOS, 2 FLETES.
    'instancias_excluidas' => [1, 2],

    'puente' => [
        'secreto' => env('PUENTE_SECRETO'),
        'ventana_firma_segundos' => 300,
        'max_inactividad_minutos' => (int) env('PUENTE_MAX_INACTIVIDAD_MINUTOS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Novedades y asesor
    |--------------------------------------------------------------------------
    |
    | Sin credenciales propias, Laravel consulta el Worker actual de Cloudflare.
    | Con DROPBOX_* lee el Dropbox directamente y con ANTHROPIC_API_KEY responde
    | el asesor por su cuenta, sin depender del Worker.
    |
    */

    'worker_url' => env('JF_WORKER_URL', 'https://vitrina-jf.cristiansosa460.workers.dev'),

    'novedades_cache_segundos' => (int) env('JF_NOVEDADES_CACHE_SEGUNDOS', 600),

    'dropbox' => [
        'app_key' => env('DROPBOX_APP_KEY'),
        'app_secret' => env('DROPBOX_APP_SECRET'),
        'refresh_token' => env('DROPBOX_REFRESH_TOKEN'),
        'carpeta' => env('DROPBOX_CARPETA', ''),
    ],

    'ia' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'modelo' => env('JF_IA_MODELO', 'claude-opus-5-5'),
        // Tope de consultas al día entre todos los visitantes, para acotar el gasto.
        'limite_diario' => (int) env('JF_IA_LIMITE_DIARIO', 2000),
    ],

];
