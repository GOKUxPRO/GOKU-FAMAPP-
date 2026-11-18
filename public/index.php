<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Goku Pay</title>
<style>
:root{--bg:#0f1115;--card:#181b22;--tx:#e8eaf0;--mut:#8b91a1;--ac:#ff8a00;--bd:#2a2e38}
@media(prefers-color-scheme:light){:root{--bg:#f4f5f8;--card:#fff;--tx:#1b1e25;--mut:#666d7d;--bd:#dde0e8}}
*{box-sizing:border-box}body{margin:0;font-family:system-ui,sans-serif;background:var(--bg);color:var(--tx);padding:16px}
.w{max-width:720px;margin:auto}h1{color:var(--ac);margin:8px 0 16px}
.c{background:var(--card);border:1px solid var(--bd);border-radius:12px;padding:16px;margin-bottom:16px}
input{width:100%;padding:11px;margin:6px 0 10px;border-radius:8px;border:1px solid var(--bd);background:var(--bg);color:var(--tx)}
button{background:var(--ac);color:#000;border:0;padding:11px 16px;border-radius:8px;font-weight:600;cursor:pointer}
.m{color:var(--mut);font-size:13px}.k{word-break:break-all;background:var(--bg);padding:10px;border-radius:8px;font-family:monospace}
table{width:100%;border-collapse:collapse;font-size:14px}td,th{padding:8px 6px;border-bottom:1px solid var(--bd);text-align:left}
.t{overflow-x:auto}.e{color:#ff5d5d}.ok{color:#2ecc71}
</style>
</head>
<body><div class="w">
<h1>⚡ Goku Pay</h1>

<div class="c" id="reg">
  <b>Register</b>
  <p class="m">Gmail + 16-char App Password (Google Account → Security → App passwords). FamApp emails e inbox ma aavta hova joiye.</p>
  <input id="email" type="email" placeholder="you@gmail.com">
  <input id="pass" type="password" placeholder="16-char App Password">
  <button onclick="reg()">Get API Key</button>
  <p id="rmsg" class="m"></p>
  <div id="keybox" style="display:none">
    <p class="ok">Save this key now — fari nahi dekhay!</p><div class="k" id="key"></div>
  </div>
</div>

<div class="c">
  <b>Payments</b>
  <input id="ak" placeholder="API Key (pay_...)">
  <button onclick="load()">Load</button>
  <span class="m"> auto-refresh 15s</span>
  <p id="pmsg" class="m"></p>
  <div class="t"><table id="tb" style="display:none">
    <thead><tr><th>Amount</th><th>Sender</th><th>UTR</th><th>Txn ID</th><th>Time</th></tr></thead><tbody></tbody>
  </table></div>
</div>

<div class="c m"><b>API</b><br>GET <code>/api.php?action=get_payments</code> with header <code>X-API-Key: pay_...</code></div>
</div>
<script>
const $=id=>document.getElementById(id);
const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
let timer;
try{const k=localStorage.getItem('gk');if(k){$('ak').value=k;load();}}catch(e){}

async function reg(){
  $('rmsg').className='m';$('rmsg').textContent='Verifying Gmail...';
  try{
    const r=await fetch('api.php?action=register',{method:'POST',headers:{'Content-Type':'application/json'},
      body:JSON.stringify({email:$('email').value,app_password:$('pass').value})});
    const d=await r.json();
    if(d.success){
      $('rmsg').textContent='';$('keybox').style.display='block';$('key').textContent=d.api_key;
      $('ak').value=d.api_key;$('pass').value='';load();
    }else{$('rmsg').className='e';$('rmsg').textContent=d.error;}
  }catch(e){$('rmsg').className='e';$('rmsg').textContent='Network error';}
}

async function load(){
  const k=$('ak').value.trim();if(!k)return;
  try{localStorage.setItem('gk',k);}catch(e){}
  try{
    const r=await fetch('api.php?action=get_payments',{headers:{'X-API-Key':k}});
    const d=await r.json();
    if(!d.success){$('pmsg').className='e';$('pmsg').textContent=d.error;$('tb').style.display='none';return;}
    $('pmsg').className='m';$('pmsg').textContent=d.data.length?'':'No payments yet.';
    $('tb').style.display=d.data.length?'table':'none';
    $('tb').querySelector('tbody').innerHTML=d.data.map(p=>
      `<tr><td>₹${esc(p.amount)}</td><td>${esc(p.sender_name)}</td><td>${esc(p.utr)}</td><td>${esc(p.transaction_id)}</td><td>${esc(p.received_at)}</td></tr>`).join('');
    clearInterval(timer);timer=setInterval(load,15000);
  }catch(e){$('pmsg').className='e';$('pmsg').textContent='Network error';}
}
</script>
</body></html>
