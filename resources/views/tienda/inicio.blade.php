<main id="vHome">
  <section class="hero" aria-roledescription="carrusel" aria-label="Destacados">
    @include('tienda.banners')
    <button class="arrow prev" id="prev" aria-label="Anterior"><svg width="14" height="22" viewBox="0 0 14 22" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 2 3 11l8 9"/></svg></button>
    <button class="arrow next" id="next" aria-label="Siguiente"><svg width="14" height="22" viewBox="0 0 14 22" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 2 8 9-8 9"/></svg></button>
    <div class="dots" id="dots"></div>
  </section>

  <div class="wrap">
    <div class="shortcuts">
      <button class="sc" data-mega><h3>Categorías</h3><div class="ic"><svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.4"><rect x="8" y="8" width="20" height="20" rx="3" fill="#FFD200"/><rect x="36" y="8" width="20" height="20" rx="3" fill="#fff"/><rect x="8" y="36" width="20" height="20" rx="3" fill="#fff"/><rect x="36" y="36" width="20" height="20" rx="3" fill="#FFD200"/></svg></div><p>Nueve secciones con todo para tu obra.</p><span class="go">Ver categorías</span></button>
      <a class="sc" href="#visitanos"><h3>Pide por WhatsApp</h3><div class="ic"><svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.4" stroke-linejoin="round"><path d="M32 8a22 22 0 0 0-19 33l-3 13 13-3A22 22 0 1 0 32 8Z" fill="#FFD200"/><path d="M23 26h18M23 34h12" stroke-linecap="round"/></svg></div><p>Arma tu lista y te respondemos con precio.</p><span class="go">Cómo pedir</span></a>
      <a class="sc" href="#visitanos"><h3>Domicilios</h3><div class="ic"><svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.4" stroke-linejoin="round"><path d="M6 18h32v24H6z" fill="#FFD200"/><path d="M38 26h10l8 9v7H38z" fill="#fff"/><circle cx="16" cy="45" r="5" fill="#fff"/><circle cx="46" cy="45" r="5" fill="#fff"/></svg></div><p>Llevamos tu pedido en Medellín.</p><span class="go">Ver contacto</span></a>
      <a class="sc sc-new" href="#nuevos"><h3>Novedades</h3><div class="ic"><svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.4" stroke-linejoin="round"><path d="M32 6l6.5 15.5L55 24l-12.5 11L46 52 32 43.5 18 52l3.5-17L9 24l16.5-2.5z" fill="#FFD200"/><circle cx="52" cy="10" r="3" fill="#C8146F" stroke="none"/><circle cx="10" cy="48" r="2.5" fill="#C8146F" stroke="none"/></svg></div><p id="scNewTxt">Lo último que llegó a la ferretería.</p><span class="go">Ver novedades</span></a>
      <a class="sc" href="#colores"><h3>Carta de colores</h3><div class="ic"><svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.4" stroke-linejoin="round"><rect x="10" y="10" width="14" height="44" rx="3" fill="#FFD200"/><rect x="24" y="10" width="14" height="44" rx="3" fill="#C8146F" transform="rotate(14 31 54)"/><rect x="38" y="10" width="14" height="44" rx="3" fill="#fff" transform="rotate(28 45 54)"/></svg></div><p>Vinilos y esmaltes en todos los tonos.</p><span class="go">Ver colores</span></a>
      <a class="sc" href="{{ $contacto['maps_buscar'] }}" target="_blank" rel="noopener"><h3>Visítanos</h3><div class="ic"><svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.4" stroke-linejoin="round"><path d="M8 24 14 10h36l6 14z" fill="#FFD200"/><path d="M12 30v24h40V30" fill="#fff"/><path d="M8 24c0 4 3 6 6 6s6-2 6-6c0 4 3 6 6 6s6-2 6-6c0 4 3 6 6 6s6-2 6-6c0 4 3 6 6 6s6-2 6-6"/><path d="M27 54V40h10v14"/></svg></div><p>{{ $contacto['direccion_corta'] }}.</p><span class="go">Abrir en Google Maps</span></a>
    </div>

    <section class="news" id="novedades" aria-labelledby="newsT">
      <div class="news-h">
        <div>
          <span class="news-tag">✦ Recién llegados</span>
          <h2 id="newsT">Lo nuevo en JF</h2>
          <p id="newsSub">Productos que acaban de llegar a la ferretería.</p>
        </div>
        <a class="news-all" href="#nuevos">Ver todas las novedades →</a>
      </div>
      <div class="shelf"><button class="sbtn l" aria-label="Anterior"><svg width="10" height="16" viewBox="0 0 10 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 1 2 8l6 7"/></svg></button><div class="track" id="newsTrack"></div><button class="sbtn r" aria-label="Siguiente"><svg width="10" height="16" viewBox="0 0 10 16" fill="none" stroke="currentColor" stroke-width="2"><path d="m2 1 6 7-6 7"/></svg></button></div>
    </section>

    <section class="how" aria-labelledby="howT">
      <h2 id="howT">Comprar en JF es así de fácil</h2>
      <ol>
        <li><b>1</b><span><strong>Elige tus productos</strong>Busca o entra por categoría y toca “Agregar al carrito”.</span></li>
        <li><b>2</b><span><strong>Revisa tu carrito</strong>Elige si recoges en tienda o domicilio, y cómo pagas.</span></li>
        <li><b>3</b><span><strong>Envíalo por WhatsApp</strong>Te confirmamos precio, disponibilidad y domicilio.</span></li>
      </ol>
    </section>

    <section class="sec">
      <div class="sec-h"><h2>Compra por categoría</h2></div>
      <div class="tiles" id="tiles"></div>
    </section>

    <div id="shelves"></div>

    <section class="visit" id="visitanos">
      <div class="a">
        <h2>Visítanos o pide a domicilio</h2>
        <p>{{ $contacto['direccion'] }}.</p>
        <div class="maps"><a class="mbtn" href="{{ $contacto['maps_destino'] }}" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s7-6.3 7-12a7 7 0 0 0-14 0c0 5.7 7 12 7 12Z"/><circle cx="12" cy="10" r="2.5"/></svg>Cómo llegar</a><a class="mlink" href="{{ $contacto['maps_buscar'] }}" target="_blank" rel="noopener">Ver en Google Maps ↗</a></div>
        <p style="margin-top:14px;opacity:.9">Cómo pedir: agrega productos a <b>Mi carrito</b>, escribe tu nombre y toca <b>Enviar pedido por WhatsApp</b>. Te respondemos con precio, disponibilidad y costo del domicilio.</p>
      </div>
      <div class="b">
        <a class="ct" href="https://wa.me/{{ $contacto['whatsapp_ventas'] }}" target="_blank" rel="noopener"><span class="ci"><svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="3.2" stroke-linejoin="round"><path d="M6 18h32v24H6z" fill="#fff"/><path d="M38 26h10l8 9v7H38z" fill="#C8146F"/><circle cx="16" cy="45" r="5" fill="#fff"/><circle cx="46" cy="45" r="5" fill="#fff"/></svg><span class="wa"><svg viewBox="0 0 24 24" fill="#fff"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm5.3 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.5-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.8 0-1.4.7-2 1-2.3.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .5l-.4.6-.4.4c-.1.1-.3.3-.1.6.1.3.6 1.1 1.4 1.8 1 .9 1.8 1.1 2.1 1.3.3.1.4.1.6-.1l.8-1c.2-.3.4-.2.6-.1l1.9.9c.3.1.5.2.5.3.1.2.1.7-.1 1.3Z"/></svg></span></span><span><small>Despacho y ventas</small><span class="n">{{ $contacto['telefono_ventas'] }}</span></span></a>
        <a class="ct" href="https://wa.me/{{ $contacto['whatsapp_compras'] }}" target="_blank" rel="noopener"><span class="ci"><svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="3.2" stroke-linejoin="round"><path d="M10 22 32 12l22 10v24L32 56 10 46z" fill="#fff"/><path d="M10 22l22 10 22-10M32 32v24" /><path d="M21 17l22 10v8" stroke="#C8146F"/></svg><span class="wa"><svg viewBox="0 0 24 24" fill="#fff"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm5.3 14.1c-.2.6-1.3 1.2-1.8 1.2-.5.1-1 .2-3.3-.7-2.8-1.1-4.5-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.8 0-1.4.7-2 1-2.3.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 2c.1.2.1.4 0 .5l-.4.6-.4.4c-.1.1-.3.3-.1.6.1.3.6 1.1 1.4 1.8 1 .9 1.8 1.1 2.1 1.3.3.1.4.1.6-.1l.8-1c.2-.3.4-.2.6-.1l1.9.9c.3.1.5.2.5.3.1.2.1.7-.1 1.3Z"/></svg></span></span><span><small>Compras</small><span class="n">{{ $contacto['telefono_compras'] }}</span></span></a>
        <a class="ct" href="mailto:{{ $contacto['correo'] }}"><span class="ci"><svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="3.2" stroke-linejoin="round"><rect x="8" y="16" width="48" height="34" rx="4" fill="#fff"/><path d="M8 20l24 16 24-16" /><circle cx="50" cy="16" r="6" fill="#C8146F" stroke="#15140F" stroke-width="2.4"/></svg></span><span style="min-width:0"><small>Correo</small><span class="n em">{{ $contacto['correo'] }}</span></span></a>
        <small>Instagram y Facebook: {{ $contacto['redes'] }}</small>
      </div>
    </section>
  </div>
</main>
