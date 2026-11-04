const JF=JSON.parse(document.getElementById('jf-config').textContent);
const WA=JF.contacto.whatsapp;
const $=s=>document.querySelector(s), $$=s=>document.querySelectorAll(s);
let DATA, HOME={}, byId={}, catLabel={}, counts={}, view=[], shown=0;
const store={get(k,d){try{return JSON.parse(localStorage.getItem(k))??d}catch(e){return d}},set(k,v){try{localStorage.setItem(k,JSON.stringify(v))}catch(e){}}};
let cart=store.get('jf-cart',{});
const norm=s=>s.normalize('NFD').replace(/\p{M}/gu,'').toLowerCase();
const esc=s=>String(s).replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));

async function imgSrc(p,i=0,preferSmall){ if(preferSmall&&i===0&&p.t) return p.t; return p.imgs?(p.imgs[i]||p.imgs[0]):HOME[p.id+'.'+i]; }

const io=new IntersectionObserver(es=>{for(const e of es){ if(!e.isIntersecting) continue; io.unobserve(e.target);
  const p=byId[e.target.dataset.id]; imgSrc(p,0,e.target.dataset.small==='1').then(src=>{ if(src){ e.target.onload=()=>e.target.classList.add('on'); e.target.src=src; } }); }},{rootMargin:'500px'});

const COP=new Intl.NumberFormat('es-CO',{style:'currency',currency:'COP',maximumFractionDigits:0});
const money=v=>COP.format(v).replace(/\s/g,' ');
const many=p=>(p.pr||p.r).length>1;
const MESES=['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
const fmtDate=iso=>{const [y,m,d]=iso.split('-');return `${+d} ${MESES[+m-1]}`;};
function priceHTML(p){ if(!p.$) return '<span class="pr"><small>Consultar precio</small></span>'; return `<span class="pr">${many(p)?'<small>Desde</small>':''}${money(p.$)}${p.u?`<small>/ ${p.u}</small>`:''}</span>`; }
const refsText=p=>p.r.length?'REF. '+p.r.join(' · '):'';
function actHTML(id){ const q=cart[id]||0;
  return q?`<div class="step"><button data-dec="${id}" aria-label="Quitar uno">−</button><output>${q}</output><button data-inc="${id}" aria-label="Agregar uno">+</button></div>`
          :`<button class="add" data-inc="${id}">Agregar al carrito</button>`; }
function card(p,small){
  const el=document.createElement('article'); el.className='card'; el.dataset.id=p.id;
  el.innerHTML=`<button class="thumb" data-open="${p.id}" aria-label="Ver ${esc(p.n)}">${p.nw?'<span class="newb">✦ Nuevo</span>':''}<img data-id="${p.id}" data-small="${small?1:0}" alt="${esc(p.n)}">${p.k>1?`<span class="more">${p.k} fotos</span>`:''}</button>
  <div class="info"><button class="name nm" data-open="${p.id}">${esc(p.n)}</button><div class="refs">${refsText(p)}</div>${p.nw?`<span class="arrived">Llegó el ${fmtDate(p.nw)}</span>`:''}${priceHTML(p)}<div class="act">${actHTML(p.id)}</div></div>`;
  io.observe(el.querySelector('img')); return el; }

let slide=0, timer;
function go(n){ const s=$$('.slide'); slide=(n+s.length)%s.length; $('#slides').style.transform=`translateX(-${slide*100}%)`;
  $$('#dots button').forEach((b,i)=>b.setAttribute('aria-current',i===slide)); }
let paused=false;
function auto(){ clearInterval(timer); if(paused) return; if(!matchMedia('(prefers-reduced-motion: reduce)').matches) timer=setInterval(()=>go(slide+1),6000); }
const CAT_ICO={nuevos:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M32 8l6 14 15 2-11 10 3 15-13-8-13 8 3-15-11-10 15-2z" fill="#FFD200"/><circle cx="52" cy="12" r="3" fill="#C8146F" stroke="none"/></svg>',agricola:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M32 54V30" /><path d="M32 34c-10 0-16-6-16-16 10 0 16 6 16 16Z" fill="#FFD200"/><path d="M32 30c0-9 5-14 14-14 0 9-5 14-14 14Z" fill="#fff"/><path d="M16 54h32"/></svg>',deposito:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 22h32v26a6 6 0 0 1-6 6H20a6 6 0 0 1-6-6z" fill="#FFD200"/><ellipse cx="30" cy="22" rx="16" ry="5" fill="#fff"/><path d="M46 26h4a4 4 0 0 1 4 4v8" /><path d="M14 34h32" stroke="#C8146F"/></svg>',electricos:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M24 40c-5-4-8-9-8-15a16 16 0 0 1 32 0c0 6-3 11-8 15v6H24z" fill="#FFD200"/><path d="M26 52h12M28 57h8"/><path d="M33 22l-5 8h8l-5 8" stroke="#C8146F"/></svg>',ferreteria:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 44c10 0 10-12 20-12s10 12 20 12" /><circle cx="12" cy="44" r="5" fill="#FFD200"/><circle cx="52" cy="44" r="5" fill="#FFD200"/><path d="M22 12l8 8-4 4-8-8a6 6 0 0 1 4-4Z" fill="#fff"/><path d="M28 22l10 10" stroke="#C8146F"/></svg>',herramienta:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 20h30l6 6v6H10z" fill="#FFD200"/><path d="M46 26h10" /><path d="M20 32l-4 20h12l2-20" fill="#fff"/><path d="M30 38h6" stroke="#C8146F"/></svg>',manual:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18h26l6 6-6 6H14z" fill="#FFD200"/><path d="M28 30v24" stroke-width="5"/><path d="M28 30v24" stroke="#C8146F" stroke-width="1.5"/></svg>',colores:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><rect x="10" y="10" width="14" height="44" rx="3" fill="#FFD200"/><rect x="24" y="10" width="14" height="44" rx="3" fill="#C8146F" transform="rotate(14 31 54)"/><rect x="38" y="10" width="14" height="44" rx="3" fill="#fff" transform="rotate(28 45 54)"/></svg>',tornilleria:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10h24l-4 8H24z" fill="#FFD200"/><path d="M26 18v30l6 8 6-8V18" fill="#fff"/><path d="M26 24l12 4M26 32l12 4M26 40l12 4" stroke="#C8146F"/></svg>',gas:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><rect x="18" y="16" width="28" height="40" rx="12" fill="#FFD200"/><path d="M26 16V9h12v7"/><path d="M18 36h28" stroke="#C8146F"/></svg>',drywall:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><rect x="10" y="12" width="44" height="40" rx="2" fill="#fff"/><path d="M32 12v40M10 32h44"/><circle cx="18" cy="20" r="2" fill="#C8146F" stroke="none"/><circle cx="46" cy="44" r="2" fill="#C8146F" stroke="none"/><rect x="12" y="34" width="18" height="16" fill="#FFD200" stroke="none"/></svg>',lubricantes:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 26h26l8 8v18H14z" fill="#FFD200"/><path d="M40 26l12-10 4 4-10 10" fill="#fff"/><path d="M22 18h10v8H22z" fill="#fff"/><path d="M58 30v6" stroke="#C8146F"/></svg>',plomeria:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 22h22v10H10z" fill="#fff"/><path d="M32 22h8a10 10 0 0 1 10 10v6H40v-6H32" fill="#FFD200"/><path d="M18 14h6M21 14v8"/><path d="M45 44v4M45 52v3" stroke="#C8146F"/></svg>',seguridad:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 42a22 22 0 0 1 44 0z" fill="#FFD200"/><path d="M8 42h48v6H8z" fill="#fff"/><path d="M26 22v-6h12v6" /><path d="M32 16v26" stroke="#C8146F"/></svg>',dropbox:'<svg viewBox="0 0 64 64" fill="none" stroke="#15140F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 22 32 12l22 10v24L32 56 10 46z" fill="#fff"/><path d="M10 22l22 10 22-10M32 32v24"/><path d="M21 17l22 10v8" stroke="#C8146F"/></svg>'};
const catIco=id=>`<span class="mico">${CAT_ICO[id]||CAT_ICO.dropbox}</span>`;
function buildHome(){
  $$('.fan').forEach(f=>{ f.innerHTML=f.dataset.ids.split(',').map(id=>`<img alt="${esc(byId[id]?.n||'')}" src="${HOME[id+'.0']||''}">`).join(''); });
  $('#dots').innerHTML=[...$$('.slide')].map((_,i)=>`<button aria-label="Ir al destacado ${i+1}"></button>`).join('');
  $$('#dots button').forEach((b,i)=>b.onclick=()=>{go(i);auto()});
  const hero=$('.hero'); hero.addEventListener('mouseenter',()=>{paused=true;clearInterval(timer)}); hero.addEventListener('mouseleave',()=>{paused=false;auto()});
  let tx=null; hero.addEventListener('touchstart',e=>{tx=e.touches[0].clientX},{passive:true}); hero.addEventListener('touchend',e=>{ if(tx==null) return; const dx=e.changedTouches[0].clientX-tx; if(Math.abs(dx)>45){ go(slide+(dx<0?1:-1)); auto(); } tx=null; });
  $('#prev').onclick=()=>{go(slide-1);auto()}; $('#next').onclick=()=>{go(slide+1);auto()}; go(0); auto();
  $('#tiles').innerHTML=DATA.cats.map(c=>`<a class="tile" href="#${c.id}"><img alt="" src="${HOME[DATA.home.tiles[c.id]+'.0']||''}">${c.label}<small>${counts[c.id]} productos</small></a>`).join('');
  const NEW=DATA.products.filter(p=>p.nw).sort((a,b)=>b.nw.localeCompare(a.nw));
  $('#newsSub').textContent=`${NEW.length} productos que acaban de llegar a la ferretería. Sé el primero en tenerlos.`;
  const nt=$('#newsTrack'); NEW.slice(0,16).forEach(p=>nt.appendChild(card(p,true)));
  const ns=$('#novedades'); ns.querySelector('.l').onclick=()=>nt.scrollBy({left:-nt.clientWidth,behavior:'smooth'}); ns.querySelector('.r').onclick=()=>nt.scrollBy({left:nt.clientWidth,behavior:'smooth'});
  $('#scNewTxt').textContent=`${NEW.length} productos recién llegados.`;
  const sh=$('#shelves');
  DATA.cats.forEach(c=>{
    const s=document.createElement('section'); s.className='sec';
    s.innerHTML=`<div class="sec-h"><h2>${c.label}</h2><a href="#${c.id}">Ver todo (${counts[c.id]})</a></div><div class="shelf"><button class="sbtn l" aria-label="Anterior"><svg width="10" height="16" viewBox="0 0 10 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 1 2 8l6 7"/></svg></button><div class="track"></div><button class="sbtn r" aria-label="Siguiente"><svg width="10" height="16" viewBox="0 0 10 16" fill="none" stroke="currentColor" stroke-width="2"><path d="m2 1 6 7-6 7"/></svg></button></div>`;
    const t=s.querySelector('.track'); DATA.home.feat[c.id].forEach(id=>t.appendChild(card(byId[id],true)));
    s.querySelector('.l').onclick=()=>t.scrollBy({left:-t.clientWidth,behavior:'smooth'});
    s.querySelector('.r').onclick=()=>t.scrollBy({left:t.clientWidth,behavior:'smooth'});
    sh.appendChild(s);
  });
  const nw0=DATA.products.filter(p=>p.nw).sort((a,b)=>b.nw.localeCompare(a.nw))[0];
  $('#megaList').innerHTML=`<a href="#nuevos"><img alt="" src="${HOME[nw0.id+'.0']||''}">${catIco('nuevos')}<span><b style="color:#C8146F">✦ Novedades</b><small>${DATA.products.filter(p=>p.nw).length} recién llegados</small></span></a>`+DATA.cats.map(c=>`<a href="#${c.id}"><img alt="" src="${HOME[DATA.home.tiles[c.id]+'.0']||''}">${catIco(c.id)}<span><b>${c.label}</b><small>${counts[c.id]} productos</small></span></a>`).join('');
}

function renderMore(){ const g=$('#grid'); view.slice(shown,shown+48).forEach(p=>g.appendChild(card(p))); shown=Math.min(view.length,shown+48); }
let LAST=[null,''];
function showList(cat,q,keepScroll){
  $('#vHome').hidden=true; $('#vList').hidden=false;
  const isNew=cat==='nuevos';
  view=q?buscar(q,cat).res:DATA.products.filter(p=>isNew?p.nw:(!cat||p.c===cat));
  if(isNew) view.sort((a,b)=>b.nw.localeCompare(a.nw));
  LAST=[cat,q]; const so=((isNew||q)&&!SORT_TOCADO&&$('#sort').value==='az')?'rel':$('#sort').value;
  if(so==='asc') view.sort((a,b)=>(a.$||1e12)-(b.$||1e12)); else if(so==='desc') view.sort((a,b)=>(b.$||0)-(a.$||0)); else if(so==='az') view.sort((a,b)=>a.n.localeCompare(b.n,'es',{sensitivity:'base',numeric:true}));
  const lab=cat==='nuevos'?'Novedades':(cat?catLabel[cat]:'Todos los productos');
  $('#lTitle').textContent=q?`Resultados para “${q}”`:lab;
  $('#crumb').textContent=q?'Búsqueda':lab;
  $('#lCount').textContent=view.length+(view.length===1?' producto':' productos');
  const links=[['nuevos','✦ Novedades',DATA.products.filter(p=>p.nw).length],[null,'Todo',DATA.products.length],...DATA.cats.map(c=>[c.id,c.label,counts[c.id]])];
  const lk=([id,l,n])=>`<a href="#${id||'todo'}" aria-current="${!q&&(id||null)===(cat||null)}">${l}${n!=null?` <span>${n}</span>`:''}</a>`;
  $('#sideCats').innerHTML=links.map(x=>`<li>${lk(x)}</li>`).join('');
  $('#chipsm').innerHTML=links.map(([id,l])=>lk([id,l,null])).join('');
  const g=$('#grid'); g.innerHTML=''; shown=0;
  if(!view.length) g.innerHTML=`<div class="empty"><b style="display:block;font-size:17px;color:var(--fg);margin-bottom:6px">No encontramos “${esc(q)}”</b>Revisa la ortografía, prueba con una palabra más corta o busca por referencia.<br>También puedes preguntarnos por WhatsApp al ${JF.contacto.telefono}: puede que lo tengamos.<div style="display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin-top:16px">${DATA.cats.map(c=>`<a href="#${c.id}" class="pill2">${c.label}</a>`).join("")}</div></div>`;
  renderMore(); if(!keepScroll) scrollTo({top:0});
}
let SORT_TOCADO=false;
$('#sort').addEventListener('change',()=>{ SORT_TOCADO=true; showList(LAST[0],LAST[1],true); });
new IntersectionObserver(es=>{ if(es[0].isIntersecting&&!$('#vList').hidden&&shown<view.length) renderMore(); },{rootMargin:'800px'}).observe($('#sentinel'));

function showHome(anchor){ $('#vList').hidden=true; $('#vHome').hidden=false;
  if(anchor){ const el=document.getElementById(anchor); const y=el.getBoundingClientRect().top+scrollY-$('#hd').offsetHeight-12; scrollTo({top:y,behavior:'smooth'}); } else scrollTo({top:0}); }
function route(){
  closeMega(); closeSugg(); const hh=(location.hash||'#inicio'); $$('.nav a').forEach(a=>a.getAttribute('href')===hh&&hh!=='#visitanos'?a.setAttribute('aria-current','page'):a.removeAttribute('aria-current')); const h=decodeURIComponent((location.hash||'').slice(1));
  if(catLabel[h]||h==='nuevos') showList(h); else if(h==='todo') showList(null);
  else if(h==='visitanos') showHome('visitanos'); else showHome();
}
addEventListener('hashchange',route);
document.addEventListener('click',e=>{ const a=e.target.closest('a[href^="#"]'); if(a&&a.getAttribute('href')===location.hash){ route(); } });
$('#sform').addEventListener('submit',e=>{ e.preventDefault(); const q=$('#q').value.trim(); if(!q) return; clearTimeout(qt); closeSugg(); showList(null,q); try{history.replaceState(null,'','#buscar')}catch(_){}; $('#q').blur(); });
/* ---------- Buscador inteligente: entiende frases como "necesito un machete para la finca" ---------- */
const STOP=new Set('necesito necesita necesitamos quiero quisiera queria busco buscando buscar estoy tengo tienen tiene venden vende hay me mi mis su sus un una unos unas el la los las lo de del al a para por pa con sin en y o que como cual cuales donde casa hogar apartamento algo pintar pinto pared paredes fachada finca campo potrero lote obra fuga gotea goteando taller arreglo reparar alguna alguno algun favor porfa porfavor hola buenas buenos dias tardes noches gracias se le les es son ser esta este estos estas ese esa eso muy mas mejor bueno buena barato barata economico economica precio cuanto cuesta vale valen sirve sirva usar uso hacer arreglar poner cambiar comprar compra ustedes usted tu yo nos'.split(' '));
const SINON={bombilla:'bombillo',bombillas:'bombillo',foco:'bombillo',focos:'bombillo',enchufe:'toma',enchufes:'toma',tomacorriente:'toma',interruptor:'suiche',interruptores:'suiche',switch:'suiche',destornillador:'destornillador',atornillador:'destornillador',desarmador:'destornillador',
  martilo:'martillo',serrucho:'serrucho',segueta:'segueta',lija:'lija',lijas:'lija',candado:'candado',cerradura:'chapa',chapas:'chapa',grifo:'llave',canilla:'llave',regadera:'ducha',teflon:'teflon',pegante:'pegante',pegamento:'pegante',silicon:'silicona',
  escoba:'escoba',azadon:'azadon',pica:'pica',pico:'pica',palin:'pala',palas:'pala',guantes:'guante',botas:'bota',gafas:'gafa',lentes:'gafa',tapabocas:'tapaboca',mascarilla:'tapaboca',extension:'extension',alargue:'extension',linterna:'linterna',
  pulidora:'pulidora',esmeril:'pulidora',taladro:'taladro',broca:'broca',brocas:'broca',tornillos:'tornillo',puntillas:'puntilla',clavos:'puntilla',clavo:'puntilla',chazos:'chazo',tarugo:'chazo',tarugos:'chazo'};
const CONTEXTO=[
  {re:/finca|campo|potrero|cultivo|siembra|sembrar|jardin|huerta|ganado|monte|rozar|cerca|lote|maleza|pasto/,cat:'agricola',txt:'para la finca'},
  {re:/pintar|pintura|pared|fachada|estuco|resanar|impermeabil/,cat:'deposito',txt:'pintura y obra'},
  {re:/luz|electric|corriente|cableado|bombill|lampara|ilumin|energia/,cat:'electricos',txt:'eléctricos'},
  {re:/bano|ducha|agua|fuga|gotea|tuberia|tubo|lavamanos|lavaplatos|sanitario|inodoro|cocina/,cat:'plomeria',txt:'plomería'},
  {re:/seguridad|proteccion|proteger|obra|construccion|casco|soldar/,cat:'seguridad',txt:'seguridad'},
  {re:/gas|estufa|calentador|pipeta|cilindro|fogon/,cat:'gas',txt:'gas'},
  {re:/drywall|cielo ?raso|superboard|panel yeso/,cat:'drywall',txt:'drywall'},
  {re:/aceite|lubric|grasa|oxido|aflojar/,cat:'lubricantes',txt:'lubricantes'},
  {re:/carro|moto|taller|mecanic/,cat:'manual',txt:'taller'} ];
const raiz=w=>w.length>5&&/es$/.test(w)?w.slice(0,-2):(w.length>4&&/s$/.test(w)?w.slice(0,-1):w);
let VOCAB=null;
function vocab(){ if(VOCAB) return VOCAB; VOCAB=new Set(); DATA.products.forEach(p=>norm(p.n).split(/[^a-z0-9]+/).forEach(w=>{ if(w.length>3) VOCAB.add(w); })); return VOCAB; }
function lev1(a,b){ if(Math.abs(a.length-b.length)>1) return false; let i=0,j=0,d=0; while(i<a.length&&j<b.length){ if(a[i]===b[j]){i++;j++;continue;} if(++d>1) return false; if(a.length>b.length) i++; else if(b.length>a.length) j++; else {i++;j++;} } return d+(a.length-i)+(b.length-j)<=1; }
function entender(q){
  const n=norm(q||'').replace(/[^a-z0-9ñ ]+/g,' ').replace(/\s+/g,' ').trim();
  const ctx=CONTEXTO.filter(c=>c.re.test(n)&&catLabel[c.cat]);
  DATA.products.forEach(p=>{ if(!p._n) p._n=' '+norm(p.n+' '+p.r.join(' ')).replace(/[^a-z0-9ñ]+/g,' ')+' '; });
  let words=n.split(' ').filter(w=>w&&!STOP.has(w)).map(w=>SINON[w]||w);
  const ctxOnly=w=>ctx.some(c=>c.re.test(w))&&!vocab().has(w)&&!vocab().has(raiz(w));
  let terms=words.filter(w=>!ctxOnly(w)).map(raiz);
  const V=vocab(); const corr=[];
  terms=terms.map(t=>{ if(/^\d+$/.test(t)||t.length<5||DATA.products.some(p=>p._n.includes(t))) return t;
    for(const v of V){ if(lev1(t,v)){ corr.push([t,v]); return v; } } return t; });
  return {terms,ctx,corr};
}
function buscar(q,cat){
  const E=entender(q); const T=E.terms; const isNew=cat==='nuevos';
  const base=DATA.products.filter(p=>isNew?p.nw:(!cat||p.c===cat));
  if(!T.length) return {res:E.ctx.length?base.filter(p=>E.ctx.some(c=>c.cat===p.c)):base,E};
  const score=p=>{ let s=0,hits=0; const nn=p._n;
    T.forEach(t=>{ if(nn.includes(t)){ hits++; s+=nn.includes(' '+t)?4:2; if(nn.startsWith(' '+t)) s+=2; } });
    if(E.ctx.some(c=>c.cat===p.c)) s+=1; return [hits,s]; };
  let sc=base.map(p=>[p,...score(p)]);
  let all=sc.filter(x=>x[1]===T.length);
  if(!all.length) all=sc.filter(x=>x[1]>0&&x[1]>=Math.ceil(T.length/2));
  if(!all.length&&E.ctx.length) all=sc.filter(x=>E.ctx.some(c=>c.cat===x[0].c));
  all.sort((a,b)=>b[1]-a[1]||b[2]-a[2]||a[0].n.localeCompare(b[0].n,'es'));
  return {res:all.map(x=>x[0]),E};
}
function search(q){ return buscar(q).res; }

let sIdx=-1;
function renderSugg(){ const q=$('#q').value.trim(), box=$('#sugg'); $('#qx').hidden=!q; if(document.activeElement!==$('#q')){ box.hidden=true; return; }
  if(q.length<2){ box.hidden=true; return; }
  const B=buscar(q), res=B.res, E=B.E; const cats=DATA.cats.filter(c=>norm(c.label).includes(norm(q))||E.ctx.some(x=>x.cat===c.id));
  let h='';
  if(E.terms.length||E.ctx.length){ const chips=[...E.terms.map(t=>(E.corr.find(c=>c[1]===t)?'¿'+t+'?':t)),...E.ctx.map(c=>catLabel[c.cat])];
    h+=`<div class="ia"><span class="ia-i" aria-hidden="true">✦</span><span>Buscando: ${chips.map(c=>`<b>${esc(c)}</b>`).join(' · ')}</span></div>`; }
  if(cats.length) h+=`<div class="lbl">Categorías</div>`+cats.map(c=>`<a href="#${c.id}" class="si"><img alt="" src="${HOME[DATA.home.tiles[c.id]+'.0']||''}"><span>${c.label}<small>${counts[c.id]} productos</small></span></a>`).join('');
  if(res.length) h+=`<div class="lbl">Productos</div>`+res.slice(0,6).map(p=>`<button type="button" class="si" data-open="${p.id}"><img alt="" data-sid="${p.id}"><span>${esc(p.n)}<small>${catLabel[p.c]}${p.r.length?' · REF. '+p.r[0]:''}${p.$?' · <b style="color:var(--fg)">'+(many(p)?'desde ':'')+money(p.$)+'</b>':''}</small></span></button>`).join('')+`<button type="submit" class="all si">Ver los ${res.length} resultados →</button>`;
  if(!h) h=`<div class="none">No encontramos “${esc(q)}”. Prueba con otra palabra o pregúntanos por WhatsApp al ${JF.contacto.telefono}.</div>`;
  box.innerHTML=h; box.hidden=false; sIdx=-1; pedirReco(q,E,res);
  box.querySelectorAll('img[data-sid]').forEach(im=>imgSrc(byId[im.dataset.sid],0,true).then(s=>{if(s) im.src=s})); }

/* ---------- Asesor IA: recomienda varias opciones para lo que pidió el cliente ---------- */
const recoUrl=()=>JF.api.recomendar; const RECO=new Map(); let recoT;
function pedirReco(q,E,res){ clearTimeout(recoT);
  const frase=norm(q).split(/\s+/).filter(Boolean).length>=3||E.ctx.length;
  if(!E.terms.length&&!E.ctx.length||!frase||res.length<2) return;
  const key=norm(q).trim(); if(RECO.has(key)){ pintarReco(q,RECO.get(key)); return; }
  recoT=setTimeout(()=>{ const box=$('#sugg'); const ia=box.querySelector('.ia');
    if(ia&&!box.querySelector('.reco')) ia.insertAdjacentHTML('afterend','<div class="reco wait"><span class="dots" aria-hidden="true"><i></i><i></i><i></i></span> El asesor está eligiendo las mejores opciones…</div>');
    fetch(recoUrl(),{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({q,items:res.slice(0,15).map(p=>({id:p.id,n:p.n,c:catLabel[p.c]}))})})
      .then(r=>r.ok?r.json():null).then(d=>{ const ok=d&&Array.isArray(d.picks)&&d.picks.filter(x=>byId[x.id]).length>=1?d:null; RECO.set(key,ok); pintarReco(q,ok); })
      .catch(()=>{ RECO.set(key,null); pintarReco(q,null); }); },650); }
function pintarReco(q,d){ const box=$('#sugg'); if(box.hidden||$('#q').value.trim()!==q) return; box.querySelector('.reco')?.remove(); if(!d) return;
  const picks=d.picks.filter(x=>byId[x.id]); if(!picks.length) return;
  const html=`<div class="reco"><div class="reco-h"><span class="ia-i" aria-hidden="true">✦</span><b>${esc(d.mensaje||'Te recomendamos')}</b></div>`+
    picks.map((x,i)=>{ const p=byId[x.id]; return `<button type="button" class="si rc" data-open="${p.id}"><img alt="" data-sid="${p.id}"><span>${i===0?'<em class="best">Mejor opción</em>':''}${esc(p.n)}<small>${esc(x.razon||catLabel[p.c])}</small></span></button>`; }).join('')+`<div class="reco-f">Recomendación automática. Confírmala con nosotros por WhatsApp.</div></div>`;
  const ia=box.querySelector('.ia'); if(ia) ia.insertAdjacentHTML('afterend',html); else box.insertAdjacentHTML('afterbegin',html);
  box.querySelectorAll('.reco img[data-sid]').forEach(im=>imgSrc(byId[im.dataset.sid],0,true).then(s=>{if(s) im.src=s})); }
function closeSugg(){ $('#sugg').hidden=true; }
let qt; $('#q').addEventListener('input',()=>{ clearTimeout(qt); qt=setTimeout(renderSugg,120); });
$('#q').addEventListener('focus',renderSugg);
$('#q').addEventListener('keydown',e=>{ const it=[...$$('#sugg .si')]; if(!it.length||$('#sugg').hidden) return;
  if(e.key==='ArrowDown'||e.key==='ArrowUp'){ e.preventDefault(); sIdx=(sIdx+(e.key==='ArrowDown'?1:-1)+it.length)%it.length; it.forEach((x,i)=>x.classList.toggle('on',i===sIdx)); }
  else if(e.key==='Enter'&&sIdx>=0){ e.preventDefault(); it[sIdx].click(); } });
$('#qx').onclick=()=>{ $('#q').value=''; closeSugg(); $('#qx').hidden=true; $('#q').focus(); };
/* ---------- López: chat con opciones, texto y voz que arma la lista de materiales ---------- */
(function(){
  const fab=$('#asFab'), panel=$('#asPanel'), log=$('#asLog'), inp=$('#asIn'); if(!fab) return;
  const asesorUrl=()=>JF.api.asesor;
  const WA_V=JF.contacto.whatsapp, MAPS=JF.contacto.mapsDestino;
  const HIST=[]; let ocupado=false, iniciado=false;
  const ss={get:k=>{try{return sessionStorage.getItem(k)}catch(_){return null}},set:(k,v)=>{try{sessionStorage.setItem(k,v)}catch(_){}}};
  const hora=()=>new Date().toLocaleTimeString('es-CO',{hour:'numeric',minute:'2-digit'});
  const abajo=()=>{ log.scrollTop=log.scrollHeight; };
  function fila(quien,html){ const r=document.createElement('div'); r.className='lp-row '+quien;
    r.innerHTML=(quien==='bot'?'<img class="lp-av" src="'+JF.lopez+'" alt="">':'')+`<div class="lp-col"><div class="as-m ${quien}">${html}</div><span class="lp-t">${hora()}</span></div>`;
    log.appendChild(r); abajo(); return r; }
  const bot=(t,html)=>{ HIST.push({rol:'asesor',texto:t}); return fila('bot',html??esc(t)); };
  function opciones(lista){ const d=document.createElement('div'); d.className='lp-ops';
    lista.forEach(([txt,fn])=>{ const b=document.createElement(typeof fn==='string'?'a':'button');
      if(typeof fn==='string'){ b.href=fn; b.target='_blank'; b.rel='noopener'; } else { b.type='button'; b.onclick=()=>{ d.remove(); fila('yo',esc(txt.replace(/^\S+\s/,''))); fn(); }; }
      b.textContent=txt; d.appendChild(b); }); log.appendChild(d); abajo(); }
  const escribiendo=()=>fila('bot','<span class="as-typing" aria-label="Escribiendo"><i></i><i></i><i></i></span>');
  const luego=(fn,ms=650)=>{ const e=escribiendo(); setTimeout(()=>{ e.remove(); fn(); },ms); };

  const nuevos=()=>(DATA?DATA.products:[]).filter(p=>p.nw).sort((a,b)=>b.nw.localeCompare(a.nw));
  function menu(){ opciones([
    ['✦ Productos nuevos',verNuevos],['🧰 Armar mi lista de materiales',armarLista],['📦 Ver categorías',verCategorias],
    ['🚚 Domicilios',verDomicilios],['📍 Cómo llegar',verUbicacion],['💬 Hablar con un asesor','https://wa.me/'+WA_V+'?text='+encodeURIComponent('Hola Construmateriales JF, quiero hablar con un asesor.')]]); }
  function saludo(){ iniciado=true;
    bot('¡Bienvenido a Construmateriales JF! 👋 Soy López, tu asesor digital 24/7. Te ayudo a encontrar lo que necesitas para tu obra, tu casa o tu finca.');
    setTimeout(()=>{ const n=nuevos().length; bot(n?`Esta semana tenemos ${n} productos recién llegados. Para comenzar, elige una opción o cuéntame qué vas a hacer hoy:`:'Para comenzar, elige una opción o cuéntame qué vas a hacer hoy:'); menu(); },500); }
  function carrusel(ps){ const c=document.createElement('div'); c.className='lp-car';
    ps.forEach(p=>{ const b=document.createElement('button'); b.type='button'; b.dataset.open=p.id;
      b.innerHTML=`<img alt="" loading="lazy">${p.nw?'<em>✦ Nuevo</em>':''}<span>${esc(p.n)}</span>`; c.appendChild(b);
      imgSrc(p,0,true).then(s=>{ if(s) b.querySelector('img').src=s; }); });
    log.appendChild(c); abajo(); }
  function verNuevos(){ luego(()=>{ const ns=nuevos(); bot(ns.length?`¡Mira lo último que llegó a la ferretería! Toca uno para ver los detalles:`:'Por ahora no hay novedades. Te muestro los productos de la tienda.'); carrusel((ns.length?ns:DATA.products).slice(0,10));
    opciones([['Ver todas las novedades →',()=>{ cerrar(); location.hash='nuevos'; }],['↩ Volver al menú',()=>luego(()=>{ bot('¿En qué más te ayudo?'); menu(); })]]); }); }
  function armarLista(){ luego(()=>bot('¡Listo! Cuéntame qué vas a hacer y yo te armo la lista de materiales. Por ejemplo: "voy a pintar una habitación" o "quiero hacer una cerca en la finca". También puedes mandarme una nota de voz 🎤.')); setTimeout(()=>inp.focus(),700); }
  function verCategorias(){ luego(()=>{ bot('Estas son nuestras categorías. ¿Cuál quieres ver?');
    opciones(DATA.cats.map(c=>[c.label,()=>{ cerrar(); location.hash=c.id; }])); }); }
  function verDomicilios(){ luego(()=>{ bot('Hacemos domicilios en Medellín 🚚. Arma tu carrito en la página, toca "Enviar pedido por WhatsApp" y te confirmamos precio, disponibilidad y costo del domicilio.');
    opciones([['💬 Pedir por WhatsApp','https://wa.me/'+WA_V+'?text='+encodeURIComponent('Hola Construmateriales JF, quiero hacer un pedido a domicilio.')],['↩ Volver al menú',()=>luego(()=>{ bot('¿En qué más te ayudo?'); menu(); })]]); }); }
  function verUbicacion(){ luego(()=>{ bot(`Estamos en ${JF.contacto.direccion} 📍`);
    opciones([['📍 Abrir en Google Maps',MAPS],['↩ Volver al menú',()=>luego(()=>{ bot('¿En qué más te ayudo?'); menu(); })]]); }); }

  const abrir=()=>{ panel.hidden=false; fab.setAttribute('aria-expanded','true'); $('#lpPromo').hidden=true; ss.set('lp-visto','1');
    if(!iniciado) saludo(); setTimeout(()=>inp.focus(),60); };
  const cerrar=()=>{ panel.hidden=true; fab.setAttribute('aria-expanded','false'); };
  fab.onclick=()=>panel.hidden?abrir():cerrar(); $('#asX').onclick=cerrar;
  addEventListener('keydown',e=>{ if(e.key==='Escape'){ if(!$('#asList').hidden) cerrarLista(); else if(!panel.hidden) cerrar(); } });
  log.addEventListener('click',e=>{ if(e.target.closest('.lp-car [data-open]')) cerrar(); });

  /* aviso junto a López */
  if(ss.get('lp-visto')) $('#lpBadge').hidden=true;
  else setTimeout(()=>{ if(!panel.hidden||ss.get('lp-promo-x')) return; const n=nuevos().length; $('#lpPromoT').textContent=n?`¡${n} productos nuevos!`:'¿Qué vas a construir hoy?'; $('#lpPromo').hidden=false; },2500);
  $('#lpPx').onclick=()=>{ $('#lpPromo').hidden=true; ss.set('lp-promo-x','1'); };
  $('#lpPgo').onclick=()=>{ abrir(); setTimeout(()=>{ fila('yo','Productos nuevos'); verNuevos(); },1200); };

  /* lista de materiales con IA */
  const productosDe=m=>{ let r=buscar(m.buscar).res; if(!r.length&&m.nombre) r=buscar(m.nombre).res; return r.slice(0,12); };
  function planLocal(texto){ const partes=norm(texto).split(/,|;|\by\b|\btambien\b|\bademas\b|\. /).map(x=>x.trim()).filter(Boolean);
    const vistos=new Set(), mats=[];
    partes.forEach(pz=>entender(pz).terms.forEach(w=>{ if(w.length<3||/^\d+$/.test(w)||vistos.has(w)||!buscar(w).res.length) return; vistos.add(w); mats.push({nombre:w.charAt(0).toUpperCase()+w.slice(1),buscar:w,para:''}); }));
    const E=entender(texto);
    return {respuesta: mats.length?'Esto es lo que encontré en la tienda para lo que me contaste:':(E.ctx.length?'Te muestro lo que tenemos en '+E.ctx.map(c=>catLabel[c.cat]).join(' y ')+'. Si me dices qué materiales necesitas, te armo la lista.':'Cuéntame un poco más: ¿qué vas a hacer y qué materiales tienes en mente? Por ejemplo: "voy a pintar una pared" o "necesito varilla, alambre y puntillas".'),
            materiales: mats.length?mats:E.ctx.map(c=>({nombre:catLabel[c.cat],buscar:'',cat:c.cat,para:''}))}; }
  function mostrarPlan(plan){
    const mats=(plan.materiales||[]).map(m=>({...m,prods:m.cat?DATA.products.filter(p=>p.c===m.cat).slice(0,12):productosDe(m)}));
    let h=esc(plan.respuesta||'');
    if(mats.length){ h+='<ul>'+mats.map(m=>`<li><i class="${m.prods.length?'':'no'}">${m.prods.length?'✓':'○'}</i><span><b>${esc(m.nombre)}</b>${m.para?` <small>· ${esc(m.para)}</small>`:''}<br><small>${m.prods.length?m.prods.length+(m.prods.length===1?' opción':' opciones')+' en la tienda':'Pregúntanos por WhatsApp'}</small></span></li>`).join('')+'</ul>';
      if(mats.some(m=>m.prods.length)) h+='<button type="button" class="as-go">Ver todos los materiales →</button>'; }
    const r=bot((plan.respuesta||'')+(mats.length?' Materiales: '+mats.map(m=>m.nombre).join(', '):''),h);
    const go=r.querySelector('.as-go'); if(go){ const pl={...plan,mats}; go.onclick=()=>abrirLista(pl); }
  }
  async function enviar(texto){ texto=String(texto||'').trim(); if(!texto||ocupado) return; ocupado=true; inp.value='';
    log.querySelectorAll('.lp-ops').forEach(o=>o.remove()); fila('yo',esc(texto)); HIST.push({rol:'cliente',texto});
    const espera=escribiendo(); let plan=null;
    try{ const r=await fetch(asesorUrl(),{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({mensaje:texto,historial:HIST.slice(-7,-1),categorias:DATA.cats.map(c=>c.label)})});
      if(r.ok){ const d=await r.json(); if(d&&(d.respuesta||(d.materiales||[]).length)) plan=d; } }catch(_){}
    if(!plan) plan=planLocal(texto);
    espera.remove(); mostrarPlan(plan); ocupado=false; }
  $('#asForm').addEventListener('submit',e=>{ e.preventDefault(); enviar(inp.value); });

  /* nota de voz */
  const SR=window.SpeechRecognition||window.webkitSpeechRecognition, mic=$('#asMic');
  if(SR){ mic.hidden=false; let rec=null,on=false;
    const parar=()=>{ on=false; mic.classList.remove('on'); mic.setAttribute('aria-label','Enviar nota de voz'); inp.placeholder='Escribe tu mensaje…'; try{rec&&rec.stop()}catch(_){} };
    mic.onclick=()=>{ if(on){ parar(); return; } rec=new SR(); rec.lang='es-CO'; rec.interimResults=true; rec.continuous=false; let final='';
      rec.onresult=e=>{ let t=''; final=''; for(const r of e.results){ t+=r[0].transcript; if(r.isFinal) final+=r[0].transcript; } inp.value=t; };
      rec.onerror=e=>{ parar(); if(e.error==='not-allowed'||e.error==='service-not-allowed') bot('Para mandarme una nota de voz, permite el uso del micrófono en tu navegador. También puedes escribirme 🙂'); };
      rec.onend=()=>{ const t=(final||inp.value).trim(); parar(); if(t) enviar(t); };
      try{ rec.start(); on=true; mic.classList.add('on'); mic.setAttribute('aria-label','Dejar de grabar'); inp.value=''; inp.placeholder='Te escucho… habla ahora'; }catch(_){ parar(); } }; }

  /* ventana con todos los materiales */
  function abrirLista(plan){ const L=$('#asList'); $('#asGuide').textContent=plan.respuesta||''; const secs=$('#asSecs'); secs.innerHTML='';
    plan.mats.forEach((m,i)=>{ const sec=document.createElement('section'); sec.className='as-sec';
      sec.innerHTML=`<h3><span class="num">${i+1}</span>${esc(m.nombre)}${m.para?`<span>${esc(m.para)}</span>`:''}</h3>`;
      if(m.prods.length){ const t=document.createElement('div'); t.className='track'; m.prods.forEach(p=>t.appendChild(card(p,true))); sec.appendChild(t); }
      else sec.insertAdjacentHTML('beforeend','<div class="as-none">No lo encontramos en la página. Escríbenos y te confirmamos si lo tenemos.</div>');
      secs.appendChild(sec); });
    const msg='Hola Construmateriales JF, López me sugirió esta lista:\n'+plan.mats.map((m,i)=>`${i+1}. ${m.nombre}`).join('\n')+'\n¿Me ayudan con precios y disponibilidad?';
    $('#asWa').href='https://wa.me/'+WA_V+'?text='+encodeURIComponent(msg);
    L.hidden=false; L.scrollTop=0; document.body.style.overflow='hidden'; cerrar(); $('#asListX').focus(); }
  function cerrarLista(){ $('#asList').hidden=true; document.body.style.overflow=''; }
  $('#asListX').onclick=()=>{ cerrarLista(); abrir(); };
  $('#asCart').onclick=()=>{ cerrarLista(); openCart(); };
})();

/* ---------- Búsqueda por voz ---------- */
(function(){ const SR=window.SpeechRecognition||window.webkitSpeechRecognition, btn=$('#mic'); if(!SR||!btn) return; btn.hidden=false;
  let rec=null, on=false;
  const stop=()=>{ on=false; btn.classList.remove('on'); btn.setAttribute('aria-label','Buscar por voz'); try{rec&&rec.stop()}catch(_){} };
  btn.onclick=()=>{ if(on){ stop(); return; }
    rec=new SR(); rec.lang='es-CO'; rec.interimResults=true; rec.continuous=false; rec.maxAlternatives=1;
    rec.onresult=e=>{ let t=''; for(const r of e.results) t+=r[0].transcript; $('#q').value=t.replace(/[.¿?¡!]+$/,'').trim(); $('#q').focus(); renderSugg(); };
    rec.onerror=e=>{ stop(); if(e.error==='not-allowed'||e.error==='service-not-allowed'){ $('#sugg').innerHTML='<div class="none">Para buscar por voz, permite el uso del micrófono en tu navegador.</div>'; $('#sugg').hidden=false; } };
    rec.onend=()=>{ stop(); if($('#q').value.trim()) { $('#q').focus(); renderSugg(); } };
    try{ rec.start(); on=true; btn.classList.add('on'); btn.setAttribute('aria-label','Dejar de escuchar'); $('#q').value=''; $('#q').focus();
      $('#sugg').innerHTML='<div class="listen">Te escucho… dime qué necesitas</div>'; $('#sugg').hidden=false; }catch(_){ stop(); } };
})();

document.addEventListener('click',e=>{ if(!e.target.closest('#sform')) closeSugg(); else if(e.target.closest('.si:not(.all)')) closeSugg(); });

function closeMega(){ $('#mega').hidden=true; $('#catBtn').setAttribute('aria-expanded','false'); }
function toggleMega(){ const o=$('#mega').hidden; try{ document.documentElement.style.setProperty('--hdh',Math.round($('#hd').getBoundingClientRect().height)+'px'); }catch(_){} $('#mega').hidden=!o; $('#catBtn').setAttribute('aria-expanded',o); if(o) scrollTo({top:0,behavior:'smooth'}); }
$('#catBtn').onclick=e=>{e.stopPropagation();toggleMega()};
document.addEventListener('click',e=>{ if(!$('#mega').hidden&&!e.target.closest('#mega,#catBtn,[data-mega]')) closeMega(); });

function saveCart(){ store.set('jf-cart',cart); const tot=Object.values(cart).reduce((a,b)=>a+b,0); $('#cartN').textContent=tot; $('#cbN').textContent=tot; $('#cartBar').hidden=!tot; document.body.classList.toggle('has-cart',tot>0); $('.cb-t').textContent=(tot===1?'producto':'productos')+(cartTotal()?' · '+money(cartTotal()):'');
  $$('.card .act').forEach(a=>a.innerHTML=actHTML(a.closest('.card').dataset.id));
  if(!$('#modal').hidden) mState();
  if(!$('#drawer').hidden) renderCart(); updateSend(); }
function inc(id){ const first=!cart[id]; cart[id]=(cart[id]||0)+1; saveCart(); if(first) toast('✓ Agregado al carrito: '+byId[id].n); }
function dec(id){ cart[id]=(cart[id]||0)-1; if(cart[id]<=0) delete cart[id]; saveCart(); }
let tt; function toast(t){ const el=$('#toast'); el.textContent=t; el.hidden=false; clearTimeout(tt); tt=setTimeout(()=>el.hidden=true,1800); }
function renderCart(){ const ids=Object.keys(cart).filter(id=>byId[id]), box=$('#items'); $('#cform').hidden=!ids.length; $('#dfoot').hidden=!ids.length;
  if(!ids.length){ box.innerHTML=`<div class="empty"><b style="display:block;font-size:17px;color:var(--fg);margin-bottom:6px">Tu carrito está vacío</b>Toca “Agregar al carrito” en los productos que necesites.<div style="margin-top:16px"><a href="#todo" class="cta" data-cerrar-carrito>Explorar productos</a></div></div>`; return; }
  box.innerHTML=ids.map(id=>{const p=byId[id]; return `<div class="it"><img alt="" data-cid="${id}"><div style="min-width:0"><div class="name">${esc(p.n)}</div><div class="refs">${refsText(p)}</div>${p.$?`<div class="lp">${money(p.$*cart[id])} <small>(${money(p.$)} c/u)</small></div>`:''}</div>${actHTML(id)}</div>`}).join('');
  box.querySelectorAll('img[data-cid]').forEach(im=>imgSrc(byId[im.dataset.cid],0,true).then(s=>{if(s) im.src=s})); }
/* ---------- precios: IVA y costos por medio de pago (editar aquí) ---------- */
const IVA=0.19;            // Los precios publicados YA incluyen IVA (Ley 1480, precio al público con impuestos)
const FEES={               // Recargo que cobra JF según medio de pago: pct = porcentaje (0.03 = 3%), fix = valor fijo
  efectivo:{pct:0,fix:0},
  transferencia:{pct:0,fix:0},
  online:{pct:0,fix:0}     // cuando se active la pasarela (Wompi/Shopify) se define aquí si se traslada la comisión
};
function breakdown(){ const prod=cartTotal(); const base=Math.round(prod/(1+IVA)); const pay=coVal('pay')||'efectivo'; const f=FEES[pay]||{pct:0,fix:0};
  const fee=prod?Math.round(prod*f.pct+f.fix):0; return {prod,base,tax:prod-base,fee,total:prod+fee,pay,dom:coVal('dl')==='domicilio'}; }
function renderSummary(){ const b=breakdown(), units=Object.keys(cart).filter(id=>byId[id]).reduce((t,id)=>t+cart[id],0);
  $('#sUnits').textContent=`Subtotal ${units} ${units===1?'producto':'productos'} (sin IVA)`; $('#sBase').textContent=money(b.base); $('#sTax').textContent=money(b.tax); $('#sProd').textContent=money(b.prod);
  $('#sShip').innerHTML=b.dom?'<span class="muted">Por confirmar</span>':'<span class="ok">Gratis · recoges en tienda</span>';
  $('#sFeeL').textContent=b.pay==='transferencia'?'Costo por transferencia':b.pay==='online'?'Costo pago en línea':'Costo por pago en efectivo';
  $('#sFee').innerHTML=b.fee?money(b.fee):'<span class="ok">$ 0</span>';
  $('#sTotal').textContent=money(b.total)+(b.dom?' + domicilio':'');
  $('#sFine').textContent=b.pay==='transferencia'?'JF no cobra recargo por transferencia. Tu banco podría cobrarte por enviar a otro banco (entre $4.600 y $14.500 aprox.); con Bre-B, Nequi o desde una cuenta del mismo banco normalmente es gratis.':(LIVE?'Precios actualizados con IVA incluido. Te confirmamos disponibilidad':'Precios de referencia con IVA incluido. Te confirmamos valor final, disponibilidad')+(b.dom?' y costo del domicilio':'')+' por WhatsApp.';
  $('#cTot').textContent=money(b.total); $('#cTotSub').textContent='IVA incluido'+(b.dom?' · domicilio por confirmar':''); }
function cartTotal(){ return Object.keys(cart).filter(id=>byId[id]).reduce((t,id)=>t+(byId[id].$||0)*cart[id],0); }
function message(){ const ids=Object.keys(cart).filter(id=>byId[id]), name=$('#cname').value.trim(), note=$('#cnote').value.trim();
  let m=`Hola Construmateriales JF, ${name?'soy '+name+', ':''}quiero cotizar este pedido:\n\n`;
  ids.forEach(id=>{const p=byId[id]; m+=`• ${cart[id]} x ${p.n}${p.r.length?' (REF. '+p.r.join(', ')+')':''}${p.$?' — ref. '+money(p.$*cart[id]):''}\n`});
  const B=breakdown(); if(B.prod){ m+=`\nSubtotal sin IVA: ${money(B.base)}\nIVA (19%): ${money(B.tax)}\nTotal productos (IVA incluido): ${money(B.prod)}\n`; if(B.fee) m+=`Costo medio de pago: ${money(B.fee)}\n`; m+=`*Total a pagar: ${money(B.total)}*${B.dom?' + domicilio (por confirmar)':''}\n${LIVE?'(según precios de la página)':'(precios de referencia, quedo atento a la confirmación)'}\n`; }
  const dom=coVal('dl')==='domicilio';
  m+= dom?`\n🚚 Entrega: Domicilio\n📍 ${$('#cdir').value.trim()}${$('#cbar').value.trim()?', '+$('#cbar').value.trim():''}\n`:`\n🏬 Entrega: Recojo en la tienda (${JF.contacto.zona})\n`;
  const pay={efectivo:'Efectivo',transferencia:'Transferencia (Nequi / bancaria) — por favor envíenme los datos'}[coVal('pay')]||'';
  if(pay) m+=`💳 Pago: ${pay}\n`;
  if(note) m+=`\nNota: ${note}\n`; return m+'\nGracias.'; }
function updateSend(){ const ok0=Object.keys(cart).some(id=>byId[id]), a=$('#send'); renderSummary();
  let why=''; if(ok0&&coVal('dl')==='domicilio'&&!$('#cdir').value.trim()) why='Escribe la dirección de entrega para enviar el pedido.'; else if(ok0&&!$('#cname').value.trim()) why='Escribe tu nombre para enviar el pedido.';
  $('#sendHint').hidden=!why; $('#sendHint').textContent=why; const has=ok0&&!why; a.setAttribute('aria-disabled',!has); a.href=has?`https://wa.me/${WA}?text=${encodeURIComponent(message())}`:'#'; }
function openCart(){ $('#scrim').hidden=false; $('#drawer').hidden=false; renderCart(); updateSend(); $('#closeCart').focus(); }
function closeCart(){ $('#scrim').hidden=true; $('#drawer').hidden=true; }

/* ---------- stock (desde SAINT, solo si JF decide publicarlo) ---------- */
let STOCK={};
function stockOf(p){ if(p.id in STOCK) return +STOCK[p.id]; const v=p.r.filter(r=>r in STOCK).map(r=>+STOCK[r]); return v.length?v.reduce((a,b)=>a+b,0):null; }
function stockHTML(n){
  if(n==null) return ['', `<i></i><span><b>Existencias por confirmar</b><small>Te confirmamos disponibilidad por WhatsApp al enviar el carrito.</small></span>`];
  if(n<=0) return ['out', `<i></i><span><b>Agotado</b><small>Escríbenos y te avisamos cuando llegue.</small></span>`];
  if(n<=5) return ['low', `<i></i><span><b>¡Últimas ${n} unidades!</b><small>Disponibles en bodega</small></span>`];
  return ['ok', `<i></i><span><b>${n} unidades disponibles</b><small>En bodega · ${JF.contacto.zona}</small></span>`];
}
/* ---------- quick view ---------- */
let mPic=0;
function mState(){ const p=byId[$('#modal').dataset.id]; const st=stockOf(p); const max=st==null?999:Math.max(0,st-(cart[p.id]||0));
  const q=$('#mQty'); let v=Math.round(+q.value||1); v=Math.max(1,Math.min(v,Math.max(1,max))); q.value=v; q.max=max;
  $('#qMinus').disabled=v<=1; $('#qPlus').disabled=v>=max;
  $('#mMax').textContent=st==null?'':(max>0?`máx. ${max}`:'');
  const inC=cart[p.id]||0; $('#mIn').hidden=!inC; $('#mIn').textContent=`✓ Ya tienes ${inC} en tu carrito`;
  $('#mAdd').disabled=max<=0; $('#mAddT').textContent=max<=0?(st<=0?'Agotado':'Ya tienes todo el disponible'):`Agregar ${v>1?v+' ':''}al carrito${p.$?' · '+money(p.$*v):''}`;
  $('#mPrice').innerHTML=priceHTML(p); $('#mSub').textContent=p.$?(v>1?`${v} × ${money(p.$)} = ${money(p.$*v)}`:(many(p)?'El precio puede variar según medida o referencia':'Precio por '+(p.u||'unidad'))):''; }
async function setPic(i){ const p=byId[$('#modal').dataset.id]; mPic=i; $('#mImg').src=await imgSrc(p,i)||HOME[p.id+'.0']||''; $$('#mPics button').forEach((b,j)=>b.setAttribute('aria-current',j===i)); }
async function openModal(id){ const p=byId[id], m=$('#modal'); m.dataset.id=id; m.hidden=false; document.body.style.overflow='hidden';
  $('#mCat').textContent=catLabel[p.c]+(p.nw?' · ✦ Nuevo, llegó el '+fmtDate(p.nw):''); $('#mTitle').textContent=p.n; $('#mRefs').textContent=refsText(p);
  const [cls,html]=stockHTML(stockOf(p)); $('#mStock').className='stock '+cls; $('#mStock').innerHTML=html;
  $('#mQty').value=1; mState();
  $('#mImg').src=HOME[id+'.0']||''; $('#mImg').alt=p.n; $('#mZoom').classList.remove('lens');
  $('#mPics').innerHTML=p.k>1?Array.from({length:p.k},(_,j)=>`<button data-pic="${j}" aria-label="Foto ${j+1}" aria-current="${j===0}"><img alt=""></button>`).join(''):'';
  $('#mClose').focus(); setPic(0);
  $$('#mPics button').forEach(async b=>{ b.querySelector('img').src=await imgSrc(p,+b.dataset.pic)||''; }); }
function closeModal(){ $('#modal').hidden=true; document.body.style.overflow=''; }
$('#qMinus').onclick=()=>{ $('#mQty').value=+$('#mQty').value-1; mState(); };
$('#qPlus').onclick=()=>{ $('#mQty').value=+$('#mQty').value+1; mState(); };
$('#mQty').addEventListener('change',mState);
$('#mAdd').onclick=()=>{ const id=$('#modal').dataset.id, q=+$('#mQty').value||1; cart[id]=(cart[id]||0)+q; saveCart();
  const n=$('#cartN'); n.classList.remove('bump'); void n.offsetWidth; n.classList.add('bump');
  closeModal(); toast(`✓ ${q} × ${byId[id].n} agregado al carrito`); };
$('#mKeep').onclick=closeModal;
$('#mGoCart').onclick=()=>{ closeModal(); openCart(); };
/* hover lens */
const mz=$('#mZoom');
mz.addEventListener('mousemove',e=>{ if(matchMedia('(hover:none)').matches) return; const r=mz.getBoundingClientRect(); $('#mImg').style.transformOrigin=`${(e.clientX-r.left)/r.width*100}% ${(e.clientY-r.top)/r.height*100}%`; mz.classList.add('lens'); });
mz.addEventListener('mouseleave',()=>mz.classList.remove('lens'));
mz.addEventListener('click',e=>{ if(!e.target.closest('#mFull')) openLB(); });
$('#mFull').onclick=e=>{ e.stopPropagation(); openLB(); };
/* fullscreen zoom with pan & pinch */
let Z={s:1,x:0,y:0}, ptrs=new Map(), pinch0=null, drag0=null, lastTap=0;
function lbApply(){ $('#lbImg').style.transform=`translate(calc(-50% + ${Z.x}px), calc(-50% + ${Z.y}px)) scale(${Z.s})`; $('#lbPct').textContent=Math.round(Z.s*100)+'%'; }
function lbZoom(s,cx,cy){ const st=$('#lbStage').getBoundingClientRect(); s=Math.max(1,Math.min(5,s));
  if(cx!=null){ const ox=cx-st.left-st.width/2, oy=cy-st.top-st.height/2; Z.x=ox-(ox-Z.x)*(s/Z.s); Z.y=oy-(oy-Z.y)*(s/Z.s); }
  Z.s=s; if(s===1){Z.x=0;Z.y=0;} lbApply(); }
async function lbShow(i){ const p=byId[$('#modal').dataset.id]; mPic=(i+p.k)%p.k; Z={s:1,x:0,y:0}; lbApply(); $('#lbImg').src=await imgSrc(p,mPic)||''; $('#lbT').textContent=p.n+(p.k>1?` · foto ${mPic+1} de ${p.k}`:''); }
function openLB(){ const p=byId[$('#modal').dataset.id]; $('#lb').hidden=false; $('#lbPrev').hidden=$('#lbNext').hidden=p.k<2; lbShow(mPic); $('#lbX').focus(); }
function closeLB(){ $('#lb').hidden=true; }
$('#lbX').onclick=closeLB; $('#lbIn').onclick=()=>lbZoom(Z.s*1.5); $('#lbOut').onclick=()=>lbZoom(Z.s/1.5);
$('#lbPrev').onclick=e=>{e.stopPropagation();lbShow(mPic-1)}; $('#lbNext').onclick=e=>{e.stopPropagation();lbShow(mPic+1)};
const stg=$('#lbStage');
stg.addEventListener('wheel',e=>{ e.preventDefault(); lbZoom(Z.s*(e.deltaY<0?1.15:1/1.15),e.clientX,e.clientY); },{passive:false});
stg.addEventListener('pointerdown',e=>{ if(e.target.closest('.lbnav')) return; stg.setPointerCapture(e.pointerId); ptrs.set(e.pointerId,{x:e.clientX,y:e.clientY});
  if(ptrs.size===2){ const [a,b]=[...ptrs.values()]; pinch0={d:Math.hypot(a.x-b.x,a.y-b.y),s:Z.s}; drag0=null; }
  else { drag0={x:e.clientX,y:e.clientY,zx:Z.x,zy:Z.y}; stg.classList.add('drag');
    const now=Date.now(); if(now-lastTap<300){ lbZoom(Z.s>1?1:2.5,e.clientX,e.clientY); drag0=null; } lastTap=now; } });
stg.addEventListener('pointermove',e=>{ if(!ptrs.has(e.pointerId)) return; ptrs.set(e.pointerId,{x:e.clientX,y:e.clientY});
  if(pinch0&&ptrs.size===2){ const [a,b]=[...ptrs.values()]; lbZoom(pinch0.s*Math.hypot(a.x-b.x,a.y-b.y)/pinch0.d,(a.x+b.x)/2,(a.y+b.y)/2); }
  else if(drag0&&Z.s>1){ Z.x=drag0.zx+e.clientX-drag0.x; Z.y=drag0.zy+e.clientY-drag0.y; lbApply(); } });
const up=e=>{ ptrs.delete(e.pointerId); if(ptrs.size<2) pinch0=null; if(!ptrs.size){ drag0=null; stg.classList.remove('drag'); } };
stg.addEventListener('pointerup',up); stg.addEventListener('pointercancel',up);

document.addEventListener('click',e=>{ if(e.target.closest('[data-cerrar-carrito]')) closeCart(); const t=e.target.closest('[data-inc],[data-dec],[data-open],[data-pic],[data-mega]'); if(!t) return;
  if(t.dataset.inc) inc(t.dataset.inc); else if(t.dataset.dec) dec(t.dataset.dec);
  else if(t.dataset.open) openModal(t.dataset.open);
  else if(t.hasAttribute('data-mega')){ e.stopPropagation(); toggleMega(); }
  else if(t.dataset.pic){ setPic(+t.dataset.pic); } });
$('#openCart').onclick=openCart; $('#cartBar').onclick=openCart;
$('#toTop').onclick=()=>scrollTo({top:0,behavior:'smooth'});
addEventListener('scroll',()=>{ $('#toTop').hidden=scrollY<900; },{passive:true}); $('#closeCart').onclick=closeCart; $('#scrim').onclick=closeCart;
$('#mClose').onclick=closeModal; $('#mScrim').onclick=closeModal;
$('#clear').onclick=()=>{cart={};saveCart()};

/* ---------- checkout options ---------- */
const CO=store.get('jf-co',{});
function coVal(n){ const el=document.querySelector(`input[name="${n}"]:checked`); return el?el.value:''; }
function coSync(){ const dom=coVal('dl')==='domicilio'; $('#addr').hidden=!dom;
  $('#payCashTxt').textContent=dom?'Pagas en efectivo al recibir el domicilio.':'Pagas al recoger en la tienda.';
  store.set('jf-co',{dl:coVal('dl'),pay:coVal('pay'),dir:$('#cdir').value,bar:$('#cbar').value,name:$('#cname').value}); updateSend(); }
(function(){ if(CO.dl) document.querySelector(`input[name="dl"][value="${CO.dl}"]`)?.click(); if(CO.pay) document.querySelector(`input[name="pay"][value="${CO.pay}"]:not(:disabled)`)?.click();
  if(CO.dir) $('#cdir').value=CO.dir; if(CO.bar) $('#cbar').value=CO.bar; if(CO.name) $('#cname').value=CO.name; })();
$$('input[name="dl"],input[name="pay"]').forEach(i=>i.addEventListener('change',coSync));
coSync();
['cdir','cbar','cname','cnote'].forEach(id=>$('#'+id).addEventListener('input',coSync));

$('#send').addEventListener('click',()=>toast('Abriendo WhatsApp…'));
document.addEventListener('keydown',e=>{ if(e.key==='Escape'){ if(!$('#lb').hidden){closeLB();return;} closeModal();closeCart();closeMega();} if(!$('#lb').hidden&&e.key==='ArrowRight') lbShow(mPic+1); if(!$('#lb').hidden&&e.key==='ArrowLeft') lbShow(mPic-1); });

const T0=Date.now(); const step=v=>x=>{ window.__plSet&&window.__plSet(v); return x; };
/* ---------- Precios apagados en .env = toda la tienda muestra "Consultar precio" ---------- */
function ocultarPrecios(){ if(JF.mostrarPrecios) return;
  DATA.products.forEach(p=>{ if(p.$){ p._$=p.$; delete p.$; } });
  document.documentElement.classList.add('sin-precios'); document.querySelectorAll('#sort .op-precio').forEach(o=>o.remove());
  document.querySelectorAll('.prnote').forEach(el=>{ el.innerHTML='<span>💬</span><span><b>Consulta precios por WhatsApp.</b> Arma tu carrito y te respondemos con valor y disponibilidad.</span>'; });
  const pp=document.querySelector('.mside .price'); if(pp) pp.innerHTML='<b>Consulta el precio</b> agregando el producto al carrito y enviándolo por WhatsApp. Te respondemos con valor, disponibilidad y domicilio.';
  const ct=document.getElementById('cTotSub'); if(ct) ct.textContent='Te confirmamos el valor por WhatsApp'; }
/* ---------- Novedades desde el Dropbox de JF ---------- */
/* JF_MATCH: detecta si una foto del Dropbox ya es un producto existente */
function jfMatcher(products,norm){
  const SYN={cert:'certificado',certif:'certificado',plas:'plastico',plast:'plastico',mm:'multimarca',galv:'galvanizado',galvanizada:'galvanizado',inalambrico:'inalambrico',parrila:'parrilla',parilla:'parrilla',isopo:'hisopo',cocedora:'cosedora'};
  const ST=new Set(['de','para','x','y','en','el','la','los','las','a','por','tipo','und','unidad','par']);
  const toks=t=>{ let s=norm(String(t||'')).replace(/ref\s*\.?\s*\d+/g,' ').replace(/\bwa\d+\b/g,' ').replace(/[^a-z0-9]+/g,' ').trim();
    const w=s.split(' ').filter(Boolean); const words=[],dig=[];
    w.forEach(x=>{ if(/^\d+$/.test(x)) dig.push(x); else { x=SYN[x]||x; if(!ST.has(x)) words.push(x); } });
    const out=new Set(words); if(dig.length) out.add('#'+dig.join('')); return out; };
  const jac=(A,B)=>{ let i=0; A.forEach(x=>{ if(B.has(x)) i++; }); return i/(A.size+B.size-i||1); };
  const strong=(A,B)=>{ for(const x of A) if(x.length>=4&&x[0]!=='#'&&B.has(x)) return true; return false; };
  const numOf=A=>[...A].filter(x=>x[0]==='#'||/\d/.test(x)).sort().join('|');
  const refsOf=t=>[...String(t).matchAll(/ref\s*\.?\s*([0-9]{3,})/gi)].map(m=>m[1]);
  const idx=products.map(p=>({p,T:toks(p.n),N:null})); idx.forEach(o=>o.N=numOf(o.T));
  const byRef=new Map(); idx.forEach(o=>o.p.r.forEach(r=>{ if(!byRef.has(r)) byRef.set(r,[]); byRef.get(r).push(o); }));
  const byWord=new Map(); idx.forEach(o=>o.T.forEach(w=>{ if(!byWord.has(w)) byWord.set(w,[]); byWord.get(w).push(o); }));
  function find(title,excl){
    const A=toks(title), r=refsOf(title), NA=numOf(A); if(A.size===0) return null;
    let best=null,bs=0,how='';
    r.forEach(x=>(byRef.get(x)||[]).forEach(o=>{ if(o.p===excl) return; const j=jac(A,o.T); if((j>=0.2||strong(A,o.T))&&j+1>bs){ bs=j+1; best=o; how='misma referencia'; } }));
    if(best) return {p:best.p,how,score:bs-1};
    const cand=new Set(); A.forEach(w=>(byWord.get(w)||[]).forEach(o=>cand.add(o)));
    cand.forEach(o=>{ if(o.p===excl) return; const j=jac(A,o.T); if(j>bs){ bs=j; best=o; } });
    if(!best) return null;
    if(bs===1){ const rb=best.p.r||[]; if(r.length&&rb.length&&!r.some(x=>rb.includes(x))) return null; return {p:best.p,how:'mismo nombre',score:1}; }
    return null; }
  return {find,toks,jac,numOf,refsOf};
}
function mergeDropbox(N){ if(!N||!Array.isArray(N.items)||!N.items.length) return;
  const nz=t=>norm(String(t||'')).replace(/^\s*\d+\s*/,'').replace(/[^a-z0-9 ]/g,' ').replace(/\s+/g,' ').trim();
  const EXTRA=[ {id:'ferreteria',label:'Ferretería, perfilería y mangueras',m:'ferreteria',after:'electricos'},
                 {id:'manual',label:'Herramienta manual',m:'herramienta manual',after:'herramienta'},
                 {id:'plomeria',label:'Plomería, PVC y griferías',m:'plomeria'},
                 {id:'seguridad',label:'Seguridad industrial',m:'seguridad'} ];
  const catOf=f=>{ const x=nz(f); if(!x||x.startsWith('nuevo')) return 'dropbox';
    const e=EXTRA.find(e=>x.startsWith(e.m)); if(e) return e.id;
    const c=DATA.cats.find(c=>nz(c.id)===x||nz(c.label)===x)||DATA.cats.find(c=>nz(c.label).split(' ').some(w=>w.length>3&&x.split(' ').includes(w))||x.split(' ').includes(nz(c.id)));
    return c?c.id:'dropbox'; };
  const refsOf=t=>[...String(t).matchAll(/ref\s*\.?\s*([0-9]{3,})/gi)].map(m=>m[1]);
  const limpia=t=>{ let x=String(t||'').replace(/\s*WA\d{3,5}\s*$/i,'').replace(/\s*ref\s*\.?\s*[0-9][\w]*/gi,' ').replace(/\s+/g,' ').trim();
    if(x===x.toUpperCase()) x=x.toLowerCase(); x=x.charAt(0).toUpperCase()+x.slice(1); return x||'Nuevo en JF'; };
  /* ---- evitar repetidos: misma referencia o mismo nombre = mismo producto ---- */
  const limite=Date.now()-30*864e5;
  catLabel.dropbox='Más productos';
  { /* productos repetidos dentro del catálogo inicial: se deja uno solo */
    const K=jfMatcher([],norm), vis=new Map();
    DATA.products=DATA.products.filter(p=>{ const k=[...K.toks(p.n)].sort().join(' '); const o=vis.get(k);
      if(o&&(!o.r.length||!p.r.length||p.r.some(x=>o.r.includes(x)))){ p.r.forEach(x=>{ if(!o.r.includes(x)) o.r.push(x); }); return false; }
      if(!o) vis.set(k,p); return true; }); }
  const M=jfMatcher(DATA.products,norm);
  const generico=t=>/^nuevo en jf$/i.test(String(t||'').trim());
  const pend=[]; let fusionados=0;
  N.items.forEach(it=>{ const f=String(it.fecha||'').slice(0,10); const nw=(f&&new Date(f)>=limite)?f:undefined; const cat=catOf(it.categoria);
    const m=generico(it.titulo)?null:M.find(it.titulo);
    if(m){ if(m.p.panel) return; fusionados++; const ya=m.p;
      if(!ya._dbx){ ya._dbx=1; ya.imgs=[]; delete ya.t; }              // la foto sale del Dropbox
      ya.imgs.push(it.foto); ya.img=ya.imgs[0]; ya.k=ya.imgs.length; ya.h=ya.imgs.map(()=>0);
      if(cat!=='dropbox') ya.c=cat;                       // queda en la carpeta del Dropbox
      if(nw&&(!ya.nw||ya.nw<nw)) ya.nw=nw;               // si llegó hace poco, sale como nuevo
      return; }
    pend.push({it,nw,cat,r:refsOf(it.titulo),n:it.titulo}); });
  /* fotos repetidas dentro del mismo Dropbox: un solo producto con varias fotos */
  const M2=jfMatcher(pend,norm), par=pend.map((_,i)=>i), raiz=i=>{ while(par[i]!==i) i=par[i]=par[par[i]]; return i; };
  pend.forEach((o,i)=>{ if(generico(o.n)) return; const m=M2.find(o.n,o); if(m){ const j=pend.indexOf(m.p); const a=raiz(i),b=raiz(j); if(a!==b) par[a]=b; } });
  const grupos=new Map(); pend.forEach((o,i)=>{ const k=raiz(i); if(!grupos.has(k)) grupos.set(k,[]); grupos.get(k).push(o); });
  const nuevos=[];
  grupos.forEach(g=>{ g.sort((x,y)=>(y.it.fecha||'').localeCompare(x.it.fecha||''));
    const main=g.reduce((a,o)=>M2.toks(o.n).size>M2.toks(a.n).size?o:a,g[0]);
    const r=[...new Set(g.flatMap(o=>o.r))]; const cat=(g.find(o=>o.cat!=='dropbox')||g[0]).cat;
    const nw=g.map(o=>o.nw).filter(Boolean).sort().pop(); const imgs=g.map(o=>o.it.foto);
    if(g.length>1) fusionados+=g.length-1;
    nuevos.push({ id:'dbx-'+String(main.it.id||main.it.foto).replace(/[^A-Za-z0-9]/g,'').slice(-24), n:limpia(main.n)+(r.length?' · Ref. '+r.join(' / '):''), r:[], pr:r, c:cat, k:imgs.length, h:imgs.map(()=>0), img:imgs[0], imgs, nw }); });
  nuevos.forEach(p=>{ HOME[p.id+'.0']=p.img; });
  /* el Dropbox manda: solo se muestran los productos que tienen foto en el Dropbox */
  const conFoto=DATA.products.filter(p=>p._dbx||p.panel); conFoto.forEach(p=>{ HOME[p.id+'.0']=p.img; });
  console.info('Catálogo inicial: se muestran',conFoto.length,'· se ocultan (no están en el Dropbox):',DATA.products.length-conFoto.length);
  DATA.products=nuevos.concat(conFoto);
  EXTRA.forEach(e=>{ const ps=DATA.products.filter(p=>p.c===e.id); if(!ps.length||DATA.cats.some(c=>c.id===e.id)) return;
    const i=e.after?DATA.cats.findIndex(c=>c.id===e.after):-1;
    DATA.cats.splice(i>=0?i+1:DATA.cats.length,0,{id:e.id,label:e.label}); catLabel[e.id]=e.label;
    DATA.home.tiles[e.id]=ps[0].id; DATA.home.feat[e.id]=ps.slice(0,12).map(p=>p.id); });
  const pid=new Map(DATA.products.map(p=>[p.id,p]));
  DATA.cats.forEach(c=>{ let f=(DATA.home.feat[c.id]||[]).filter(id=>pid.get(id)&&pid.get(id).c===c.id);
    if(f.length<12) DATA.products.forEach(p=>{ if(f.length<12&&p.c===c.id&&!f.includes(p.id)) f.push(p.id); });
    DATA.home.feat[c.id]=f; const t=pid.get(DATA.home.tiles[c.id]); if(!t||t.c!==c.id) DATA.home.tiles[c.id]=f[0]; });
  console.info('Productos desde Dropbox:',nuevos.length,'· fotos repetidas unificadas:',fusionados); }
var LIVE=null;
function applyLive(L){ if(!L||!L.items||!Object.keys(L.items).length) return; LIVE=L; const IT=L.items; let n=0;
  DATA.products.forEach(p=>{ const m=(p.pr||p.r).map(r=>IT[r]).filter(Boolean); const ps=m.map(x=>x.p).filter(v=>v>0);
    if(ps.length){ p.$=Math.min(...ps); p.live=1; n++; } else if(!p.panel) delete p.$; /* sin precio en SAINT: Consultar precio */ const es=m.filter(x=>x.e!=null); if(es.length) STOCK[p.id]=es.reduce((t,x)=>t+x.e,0); });
  const t=L.actualizado?new Date(L.actualizado):null; const hh=t?t.toLocaleString('es-CO',{weekday:'short',hour:'numeric',minute:'2-digit'}):'';
  document.querySelectorAll('.prnote').forEach(el=>{ el.innerHTML='<span>✅</span><span><b>Precios actualizados</b>'+(hh?' ('+hh+')':'')+'. Directo de nuestro inventario.</span>'; });
  const pp=document.querySelector('.mside .price'); if(pp) pp.innerHTML='<b>Precio actualizado</b> desde el inventario de la ferretería. Te confirmamos disponibilidad y domicilio por WhatsApp.';
  console.info('Precios en línea aplicados a',n,'productos'); }
Promise.all([fetch(JF.api.catalogo).then(r=>{ if(!r.ok) throw new Error('catalogo '+r.status); return r.json(); }).then(step(60)), fetch(JF.api.precios,{cache:'no-store'}).then(r=>r.ok?r.json():null).catch(()=>null), fetch(JF.api.novedades).then(r=>r.ok?r.json():null).catch(()=>null).then(step(85))]).then(([d,live,nov])=>{
  DATA=d; d.products.forEach(p=>{ HOME[p.id+'.0']=p.t||p.imgs[0]; }); d.cats.forEach(c=>catLabel[c.id]=c.label); mergeDropbox(nov); applyLive(live); ocultarPrecios();
  d.products.forEach(p=>{ byId[p.id]=p; counts[p.c]=(counts[p.c]||0)+1; p._s=norm(p.n+' '+p.r.join(' ')+' '+catLabel[p.c]+(p.nw?' nuevo novedad novedades':'')); });
  buildHome(); route(); saveCart();
  setTimeout(()=>window.__plDone&&window.__plDone(), Math.max(0,1600-(Date.now()-T0)));
}).catch(()=>window.__plDone&&window.__plDone());
