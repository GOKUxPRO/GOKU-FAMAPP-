<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Goku Pay</title>
<style>
:root{--bg:#0f1115;--card:#181b22;--tx:#e8eaf0;--mut:#8b91a1;--ac:#ff8a00;--bd:#2a2e38}
@media(prefers-color-scheme:light){:root{--bg:#f4f5f8;--card:#fff;--tx:#1b1e25;--mut:#666d7d;--bd:#dde0e8}}
*{box-sizing:border-box}
body{margin:0;font-family:system-ui,sans-serif;background:var(--bg);color:var(--tx);padding:16px}
.w{max-width:850px;margin:auto}
h1{color:var(--ac);margin:8px 0 4px}
.sub{color:var(--mut);font-size:13px;margin:0 0 16px}
.c{background:var(--card);border:1px solid var(--bd);border-radius:12px;padding:16px;margin-bottom:16px}
.c b{font-size:15px}
input{width:100%;padding:11px;margin:6px 0 10px;border-radius:8px;border:1px solid var(--bd);background:var(--bg);color:var(--tx);font-size:14px}
input:focus{outline:none;border-color:var(--ac)}
button{background:var(--ac);color:#000;border:0;padding:11px 20px;border-radius:8px;font-weight:700;cursor:pointer;font-size:14px}
button:active{opacity:.8}
button:disabled{opacity:.5;cursor:not-allowed}
.btn-danger{background:#ff5d5d;color:#fff;padding:8px 14px;font-size:12px;margin-left:10px}
.m{color:var(--mut);font-size:13px;margin:6px 0}
.k{word-break:break-all;background:var(--bg);padding:10px;border-radius:8px;font-family:monospace;font-size:13px;margin-top:8px}
table{width:100%;border-collapse:collapse;font-size:14px}
td,th{padding:9px 6px;border-bottom:1px solid var(--bd);text-align:left;white-space:nowrap}
th{color:var(--mut);font-size:12px;font-weight:600}
.t{overflow-x:auto}
.e{color:#ff5d5d}
.ok{color:#2ecc71}
.steps{background:var(--bg);border-radius:8px;padding:12px;margin:8px 0;font-size:13px;color:var(--mut)}
.steps li{margin:4px 0}
.loader{display:inline-block;width:14px;height:14px;border:2px solid var(--bd);border-top-color:var(--ac);border-radius:50%;animation:spin .7s linear infinite;vertical-align:middle;margin-right:6px}
@keyframes spin{to{transform:rotate(360deg)}}
</style>
</head>
<body><div class="w">

<h1>⚡ Goku Pay</h1>
<p class="sub">FamApp UPI Payment Gateway</p>

<!-- Register Section -->
<div class="c" id="regCard">
  <b>Register / Login</b>
  <p class="m">Gmail + Google App Password nakho — Gmail IMAP thi verify thashe, success toj API key malse.</p>

  <ul class="steps">
    <li>1. <a href="https://myaccount.google.com/apppasswords" target="_blank" style="color:var(--ac)">myaccount.google.com/apppasswords</a> par jaao</li>
    <li>2. App name: <b>GokuPay</b> → Create karo</li>
    <li>3. 16-char password copy karo (spaces vinaa)</li>
  </ul>

  <input id="email" type="email" placeholder="you@gmail.com" autocomplete="email">
  <input id="pass" type="password" placeholder="16-char App Password (no spaces)" maxlength="16" autocomplete="off">
  <button onclick="reg()" id="regBtn">Get API Key</button>
  <p id="rmsg" class="m"></p>
  
  <div id="keybox" style="display:none">
    <p class="ok">✅ Gmail verified! API Key ready — abhi j save karo:</p>
    <div class="k" id="keyVal"></div>
    <p class="m" style="margin-top:6px">⚠️ Aa key fari nahi dekhay — safe jagah save karo!</p>
  </div>
</div>

<!-- Payments Section -->
<div class="c">
  <b>Payments Dashboard</b>
  <input id="ak" placeholder="API Key (pay_...)" autocomplete="off">
  <button onclick="load()" id="loadBtn">Load Payments</button>
  <button onclick="deleteKey()" id="delBtn" class="btn-danger" style="display:none">Delete Key</button>
  <span class="m" style="margin-left:8px">auto-refresh 15s</span>
  <p id="pmsg" class="m"></p>
  
  <div class="t">
    <table id="tb" style="display:none">
      <thead>
        <tr>
          <th>Amount</th>
          <th>Sender</th>
          <th>Transaction ID</th>
          <th>UTR</th>
          <th>Time</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<!-- API Docs -->
<div class="c m">
  <b>API Usage</b><br>
  <code>GET api.php?action=get_payments</code><br>
  Header: <code>X-API-Key: pay_...</code>
</div>

</div>
<script>
const $=id=>document.getElementById(id);
const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
let timer;

// Saved key load
try{const k=localStorage.getItem('gk');if(k){$('ak').value=k;load();}}catch(e){}

async function reg(){
  const email=$('email').value.trim();
  const pass=$('pass').value.replace(/\s/g,'');

  if(!email){$('rmsg').className='e';$('rmsg').textContent='Email enter karo.';return;}
  if(pass.length!==16){$('rmsg').className='e';$('rmsg').textContent='App Password exactly 16 characters hovu joiye.';return;}

  $('regBtn').disabled=true;
  $('rmsg').className='m';
  $('rmsg').innerHTML='<span class="loader"></span>Gmail verify thai rahu che...';

  try{
    // Ahiya API.php call thay che
    const r=await fetch('api.php?action=register',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify({email,app_password:pass})
    });
    const d=await r.json();
    if(d.success){
      $('rmsg').textContent='';
      $('keybox').style.display='block';
      $('keyVal').textContent=d.api_key;
      $('ak').value=d.api_key;
      $('pass').value='';
      try{localStorage.setItem('gk',d.api_key);}catch(e){}
      load();
    }else{
      $('rmsg').className='e';
      $('rmsg').textContent=d.error||'Error occurred.';
    }
  }catch(e){
    $('rmsg').className='e';
    $('rmsg').textContent='Network error — internet check karo.';
  }
  $('regBtn').disabled=false;
}

async function load(){
  const k=$('ak').value.trim();
  if(!k)return;
  try{localStorage.setItem('gk',k);}catch(e){}

  $('loadBtn').disabled = true;

  try{
    // Ahiya pan API.php call thay che
    const r=await fetch('api.php?action=get_payments',{headers:{'X-API-Key':k}});
    const d=await r.json();
    
    if(d.error){
      $('pmsg').className='e';
      $('pmsg').textContent=d.error;
      $('tb').style.display='none';
      $('delBtn').style.display='none';
      return;
    }
    
    $('pmsg').className='m';
    $('pmsg').textContent=d.data.length?`${d.data.length} payment(s) found`:'No payments yet. Worker email check karti hase...';
    $('tb').style.display=d.data.length?'table':'none';
    $('delBtn').style.display='inline-block';
    
    $('tb').querySelector('tbody').innerHTML=d.data.map(p=>`
      <tr>
        <td><b>₹${esc(p.amount)}</b></td>
        <td>${esc(p.sender_name)}</td>
        <td style="font-family:monospace;font-size:12px">${esc(p.transaction_id)}</td>
        <td style="font-family:monospace;font-size:12px;color:var(--ac)">${esc(p.utr)}</td>
        <td style="font-size:12px">${esc(p.received_at)}</td>
      </tr>`).join('');
      
    clearInterval(timer);
    timer=setInterval(load,15000);
  }catch(e){
    $('pmsg').className='e';
    $('pmsg').textContent='Network error.';
  } finally {
    $('loadBtn').disabled = false;
  }
}

async function deleteKey(){
  const k = $('ak').value.trim();
  if(!k) return;
  if(!confirm('⚠️ Shu tame sachu ma API Key delete karvi che? (Worker aa account check karshe nahi)')) return;

  try {
    // Ahiya pan API.php call thay che
    const r = await fetch('api.php?action=delete_key', {
      method: 'POST',
      headers: {'X-API-Key': k, 'Content-Type': 'application/json'}
    });
    const d = await r.json();
    if(d.success) {
      alert('✅ API Key deleted!');
      localStorage.removeItem('gk');
      $('ak').value = '';
      $('tb').style.display = 'none';
      $('delBtn').style.display = 'none';
      $('pmsg').textContent = 'Key deleted.';
      clearInterval(timer);
    } else {
      alert('❌ ' + d.error);
    }
  } catch(e) {
    alert('❌ Network Error');
  }
}

document.addEventListener('keydown',e=>{
  if(e.key==='Enter'){
    if(document.activeElement===$('email')||document.activeElement===$('pass')) reg();
    if(document.activeElement===$('ak')) load();
  }
});
</script>
</body></html>
