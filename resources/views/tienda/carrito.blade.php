<div class="scrim" id="scrim" hidden></div>
<aside class="drawer" id="drawer" hidden aria-label="Mi carrito">
  <div class="dh"><div><h3>Mi carrito</h3><small class="dsub">Revisa tu pedido, elige entrega y pago, y envíalo por WhatsApp.</small></div><button class="x" id="closeCart" aria-label="Cerrar">✕</button></div>
  <div class="dbody">
    <div class="items" id="items"></div>
    <div class="df" id="cform">
      <fieldset class="opt">
        <legend><b>1</b> ¿Cómo quieres recibirlo?</legend>
        <label class="ch"><input type="radio" name="dl" value="tienda" checked><span><strong>Recoger en la tienda</strong><small>{{ $contacto['direccion_corta'] }}</small></span></label>
        <label class="ch"><input type="radio" name="dl" value="domicilio"><span><strong>Domicilio</strong><small>En Medellín y el área metropolitana. El costo se confirma por WhatsApp.</small></span></label>
        <div class="addr" id="addr" hidden>
          <label for="cdir">Dirección de entrega</label>
          <input id="cdir" placeholder="Ej. Cra 50 # 45-20, apto 301" autocomplete="street-address">
          <label for="cbar">Barrio y municipio</label>
          <input id="cbar" placeholder="Ej. Prado Centro, Medellín">
        </div>
      </fieldset>
      <fieldset class="opt">
        <legend><b>2</b> ¿Cómo vas a pagar?</legend>
        <label class="ch"><input type="radio" name="pay" value="efectivo" checked><span><strong>Efectivo</strong><small id="payCashTxt">Pagas al recoger en la tienda.</small></span></label>
        <label class="ch"><input type="radio" name="pay" value="transferencia"><span><strong>Transferencia</strong><small>Nequi o transferencia bancaria. Te enviamos los datos por WhatsApp.</small></span></label>
        <label class="ch off"><input type="radio" name="pay" value="online" disabled><span><strong>Tarjeta o PSE en línea <em class="soon">Próximamente</em></strong><small>Pago seguro con crédito, débito o PSE.</small></span></label>
      </fieldset>
      <section class="sum" aria-labelledby="sumT">
        <h4 id="sumT">Resumen de tu pedido</h4>
        <div class="row"><span id="sUnits">Productos</span><span id="sBase"></span></div>
        <div class="row"><span>IVA (19%)</span><span id="sTax"></span></div>
        <div class="row strong"><span>Total productos <small>(IVA incluido)</small></span><span id="sProd"></span></div>
        <div class="row"><span>Domicilio</span><span id="sShip"></span></div>
        <div class="row"><span id="sFeeL">Costo del medio de pago</span><span id="sFee"></span></div>
        <div class="row total"><span>Total a pagar</span><span id="sTotal"></span></div>
        <p class="fine" id="sFine"></p>
      </section>
      <fieldset class="opt">
        <legend><b>3</b> Tus datos</legend>
        <label for="cname">Nombre</label>
        <input id="cname" placeholder="Ej. Carlos Gómez" autocomplete="name">
        <label for="cnote">Nota (opcional)</label>
        <textarea id="cnote" placeholder="Medidas, colores, horario para recoger…"></textarea>
      </fieldset>
      <button class="clear" id="clear">Vaciar carrito</button>
    </div>
  </div>
  <div class="dfoot" id="dfoot">
    <div class="ctot"><span>Total a pagar<small id="cTotSub">IVA incluido · domicilio por confirmar</small></span><b id="cTot">$0</b></div>
    <span class="hint" id="sendHint" hidden></span>
    <a class="send" id="send" target="_blank" rel="noopener" aria-disabled="true">
      <svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3Z"/></svg>
      Enviar pedido por WhatsApp
    </a>
    <p class="note">Precios de referencia: te confirmamos valor final, disponibilidad y domicilio por WhatsApp ({{ $contacto['telefono_ventas'] }}).</p>
  </div>
</aside>
