    <div class="slides" id="slides">
      @foreach ($banners as $banner)
      <div class="slide {{ $banner->estilo }}"><div class="wrap">
        @if ($banner->imagen)
        <picture>@if ($banner->imagen_movil)<source media="(max-width:760px)" srcset="{{ asset($banner->imagen_movil) }}">@endif<img src="{{ asset($banner->imagen) }}" alt="{{ $banner->texto_alternativo }}" width="1600" height="600"></picture>
        <div class="cap">
        @else
        <div>
        @endif
          <span class="eyebrow">{{ $banner->etiqueta }}</span>
          <h2>{{ $banner->titulo_inicio }} <em>{{ $banner->titulo_resaltado }}</em> {{ $banner->titulo_fin }}</h2>
          <p>{!! nl2br(e($banner->texto)) !!}</p>
          <a class="cta" href="{{ $banner->boton_enlace }}">{{ $banner->boton_texto }}</a>
        </div>
        @if (! $banner->imagen && $banner->productos)
        <div class="fan" data-ids="{{ implode(',', $banner->productos) }}"></div>
        @endif
      </div><div class="tape"></div></div>
      @endforeach
    </div>
