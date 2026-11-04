<div class="modal" id="modal" hidden role="dialog" aria-modal="true" aria-labelledby="mTitle">
  <div class="scrim" id="mScrim" style="position:absolute;z-index:0"></div>
  <div class="mbox">
    <div class="mgal">
      <div class="mimg" id="mZoom">
        <img id="mImg" alt="">
        <button class="zfull" id="mFull" aria-label="Ver en pantalla completa"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg></button>
        <span class="zhint"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5M11 8v6M8 11h6"/></svg><span class="d">Pasa el mouse para ampliar · </span>Toca para zoom</span>
      </div>
      <div class="pics" id="mPics"></div>
    </div>
    <div class="mside">
      <button class="x" id="mClose" aria-label="Cerrar">✕</button>
      <span class="cat" id="mCat"></span>
      <h3 id="mTitle"></h3>
      <span class="refs" id="mRefs"></span>
      <div class="mprice"><span id="mPrice"></span><span class="sub" id="mSub"></span></div>
      <div class="stock" id="mStock"></div>
      <div class="qtyrow">
        <label for="mQty">Cantidad</label>
        <div class="qty"><button id="qMinus" aria-label="Menos">−</button><input id="mQty" type="number" inputmode="numeric" min="1" value="1"><button id="qPlus" aria-label="Más">+</button></div>
        <span class="max" id="mMax"></span>
      </div>
      <p class="incart" id="mIn" hidden></p>
      <button class="addcart" id="mAdd"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/><circle cx="10" cy="20" r="1.3"/><circle cx="17" cy="20" r="1.3"/></svg><span id="mAddT">Agregar al carrito</span></button>
      <div class="mlinks"><button id="mKeep">Seguir comprando</button><button id="mGoCart">Ver carrito</button></div>
      <p class="price"><b>Precio de referencia</b> en el mercado colombiano. Al enviar tu carrito por WhatsApp te confirmamos el valor final y la disponibilidad.</p>
    </div>
  </div>
</div>
<div class="lb" id="lb" hidden role="dialog" aria-modal="true" aria-label="Zoom del producto">
  <div class="lbbar"><span class="t" id="lbT"></span><button id="lbOut" aria-label="Alejar">−</button><output id="lbPct">100%</output><button id="lbIn" aria-label="Acercar">+</button><button id="lbX" aria-label="Cerrar zoom">✕</button></div>
  <div class="lbstage" id="lbStage"><img id="lbImg" alt=""><button class="lbnav p" id="lbPrev" aria-label="Foto anterior">‹</button><button class="lbnav n" id="lbNext" aria-label="Foto siguiente">›</button></div>
  <div class="lbhint">Rueda del mouse o pellizca para acercar · arrastra para mover · doble toque para alternar</div>
</div>
<div class="toast" id="toast" hidden></div>
