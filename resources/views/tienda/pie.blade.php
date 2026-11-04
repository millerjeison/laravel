<footer><div class="foot-tape"></div><div class="wrap">
  <span><b>Construmateriales JF</b> · {{ $contacto['eslogan'] }}<br><a href="{{ $contacto['maps_buscar'] }}" target="_blank" rel="noopener" style="color:inherit">{{ $contacto['direccion'] }}</a><br><a href="mailto:{{ $contacto['correo'] }}" style="color:inherit">{{ $contacto['correo'] }}</a></span>
  <span><a class="fct" href="https://wa.me/{{ $contacto['whatsapp_ventas'] }}" style="color:inherit;text-decoration:none"><i></i><b>Despacho y ventas</b> {{ $contacto['telefono_ventas'] }}</a><br><a class="fct" href="https://wa.me/{{ $contacto['whatsapp_compras'] }}" style="color:inherit;text-decoration:none"><i></i><b>Compras</b> {{ $contacto['telefono_compras'] }}</a><br>{{ $contacto['redes'] }}</span>
</div></footer>
