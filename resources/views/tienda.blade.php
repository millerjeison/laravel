<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title>Construmateriales JF</title>
<meta name="description" content="{{ $contacto['descripcion_seo'] }}">
<meta name="theme-color" content="#FEED01">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icon-192.png') }}">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Construmateriales JF">
<meta property="og:title" content="Construmateriales JF · {{ $contacto['eslogan'] }}">
<meta property="og:description" content="{{ $contacto['descripcion_seo'] }}">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:image" content="{{ asset('og-jf.jpg') }}">
<meta property="og:image:secure_url" content="{{ asset('og-jf.jpg') }}">
<meta property="og:image:type" content="image/jpeg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="Construmateriales JF, {{ $contacto['eslogan'] }}">
<meta property="og:locale" content="es_CO">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Construmateriales JF · {{ $contacto['eslogan'] }}">
<meta name="twitter:image" content="{{ asset('og-jf.jpg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@700;800;900&family=Archivo:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500&display=swap">
<link rel="stylesheet" href="{{ asset('css/tienda.css') }}?v={{ filemtime(public_path('css/tienda.css')) }}">
</head>
<body>
@include('tienda.precarga')
@include('tienda.encabezado')

@include('tienda.inicio')

@include('tienda.listado')

@include('tienda.pie')

@include('tienda.carrito')

@include('tienda.producto')

@include('tienda.lopez')

@php
    $configuracion = [
        'mostrarPrecios' => $mostrarPrecios,
        'lopez' => asset('lopez.webp'),
        'contacto' => [
            'whatsapp' => $contacto['whatsapp_ventas'],
            'telefono' => $contacto['telefono_ventas'],
            'direccion' => $contacto['direccion'],
            'zona' => $contacto['zona'],
            'mapsDestino' => $contacto['maps_destino'],
        ],
        'api' => [
            'catalogo' => route('api.catalogo'),
            'precios' => route('api.precios'),
            'novedades' => route('api.novedades'),
            'asesor' => route('api.asesor'),
            'recomendar' => route('api.recomendar'),
        ],
    ];
@endphp
<script type="application/json" id="jf-config">@json($configuracion)</script>
<script src="{{ asset('js/tienda.js') }}?v={{ filemtime(public_path('js/tienda.js')) }}"></script>
</body>
</html>
