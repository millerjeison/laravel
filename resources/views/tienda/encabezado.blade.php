<header class="hd" id="hd">
  <div class="wrap r1">
    <a class="logo" href="#inicio">
      <img src="{{ asset('logo.webp') }}" alt="Logo Construmateriales JF" width="60" height="60">
      <span><b>Construmateriales<br>JF</b><small>{{ $contacto['eslogan'] }}</small></span>
    </a>
    <form class="search" id="sform" role="search">
      <input id="q" type="search" placeholder="¿Qué necesitas? Ej: machete, cable, 8225…" autocomplete="off" aria-label="Buscar productos" aria-controls="sugg" aria-autocomplete="list">
      <button type="button" class="qx" id="qx" aria-label="Borrar búsqueda" hidden>✕</button>
      <div class="sugg" id="sugg" hidden role="listbox"></div>
      <button aria-label="Buscar"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg></button>
    </form>
    <a class="promo" href="#visitanos"><span class="pill">Domicilios</span>en Medellín · <em>{{ $contacto['telefono_ventas'] }}</em></a>
  </div>
  <div class="wrap r2">
    <div class="loc">
      <svg width="20" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s7-6.3 7-12a7 7 0 0 0-14 0c0 5.7 7 12 7 12Z"/><circle cx="12" cy="10" r="2.5"/></svg>
      <div style="min-width:0"><small>Visítanos en</small><span>{{ $contacto['ubicacion'] }}</span></div>
    </div>
    <nav class="nav" aria-label="Menú principal">
      <button class="cat-btn" id="catBtn" aria-expanded="false" aria-controls="mega">Categorías <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m2.5 4.5 3.5 3.5 3.5-3.5"/></svg></button>
      <a href="#nuevos" class="nvl"><i aria-hidden="true"></i>Novedades</a>
      <a href="#herramienta">Herramienta eléctrica</a>
      <a href="#deposito">Pinturas</a>
      <a href="#electricos">Eléctricos</a>
      <a href="#agricola">Agrícola</a>
      <a href="#colores">Carta de colores</a>
      <a href="#visitanos" class="new">Pedidos</a>
    </nav>
    <button class="cart" id="openCart" aria-label="Ver mi carrito">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/><circle cx="10" cy="20" r="1.3"/><circle cx="17" cy="20" r="1.3"/></svg>
      <span class="n" id="cartN">0</span><span class="lbl">Mi carrito</span>
    </button>
  </div>
  <div class="mega" id="mega" hidden><div class="wrap" id="megaList"></div></div>
</header>
