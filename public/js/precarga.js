(function(){ var p=8,f=document.getElementById('plFill');
  window.__plSet=function(v){ p=Math.max(p,Math.min(100,v)); f.style.width=p+'%'; };
  window.__plMsg=setInterval(function(){ if(p<85) window.__plSet(p+5); },500);
  window.__plDone=function(){ clearInterval(window.__plMsg); window.__plSet(100); setTimeout(function(){ var pl=document.getElementById('pl'); if(!pl) return; pl.classList.add('out'); setTimeout(function(){pl.remove()},550); },250); };
  setTimeout(function(){ if(document.getElementById('pl')) window.__plDone(); },8000);
})();
