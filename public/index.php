<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Goku Pay - Secure Gateway</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--bg:#050505;--card:rgba(20,20,25,0.6);--tx:#ffffff;--mut:#8b91a1;--ac:#ff8a00;--ac2:#ff4d00;--bd:rgba(255,255,255,0.08)}
*{box-sizing:border-box;margin:0;padding:0;font-family:'Plus Jakarta Sans',sans-serif}
body{background:var(--bg);color:var(--tx);min-height:100vh;display:flex;justify-content:center;align-items:center;overflow-x:hidden;background-image:radial-gradient(circle at 15% 50%, rgba(255,138,0,0.08), transparent 25%),radial-gradient(circle at 85% 30%, rgba(255,77,0,0.08), transparent 25%);}
.w{width:100%;max-width:900px;padding:20px;position:relative;z-index:1}
h1{font-weight:700;letter-spacing:-0.5px}
.accent{background:linear-gradient(135deg,var(--ac),var(--ac2));-webkit-background-clip:text;-webkit-text-fill-color:transparent}

/* Glass Auth Card */
#auth-screen{display:flex;flex-direction:column;gap:20px;animation:fadeIn 0.6s ease-out}
.auth-card{background:var(--card);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid var(--bd);border-radius:24px;padding:40px;box-shadow:0 30px 60px -10px rgba(0,0,0,0.8), inset 0 1px 0 rgba(255,255,255,0.1);max-width:440px;margin:auto;width:100%}
.auth-header{text-align:center;margin-bottom:30px}
.auth-header h1{font-size:36px;margin-bottom:8px}
.auth-header p{color:var(--mut);font-size:14px}
.tabs{display:flex;background:rgba(0,0,0,0.4);border-radius:14px;padding:4px;margin-bottom:24px;border:1px solid var(--bd)}
.tab{flex:1;text-align:center;padding:12px;border-radius:10px;cursor:pointer;font-weight:600;font-size:14px;color:var(--mut);transition:all 0.3s}
.tab.active{background:var(--ac);color:#000;box-shadow:0 4px 15px rgba(255,138,0,0.3)}
.input-group{margin-bottom:18px}
.input-group label{display:block;font-size:12px;color:var(--mut);margin-bottom:8px;margin-left:4px;font-weight:600}
.input-group input{width:100%;padding:14px 16px;border-radius:12px;border:1px solid var(--bd);background:rgba(0,0,0,0.3);color:#fff;font-size:15px;transition:all 0.3s;outline:none}
.input-group input:focus{border-color:var(--ac);background:rgba(0,0,0,0.5);box-shadow:0 0 0 3px rgba(255,138,0,0.15)}
.btn-primary{width:100%;padding:16px;border-radius:12px;border:none;background:linear-gradient(135deg,var(--ac),var(--ac2));color:#000;font-weight:700;font-size:16px;cursor:pointer;transition:transform 0.2s, box-shadow 0.2s;margin-top:10px}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 10px 25px -5px var(--ac)}
.btn-primary:active{transform:scale(0.98)}

/* Dashboard */
#main-screen{display:none;animation:fadeIn 0.6s ease-out;width:100%}
.top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;background:var(--card);backdrop-filter:blur(16px);padding:16px 24px;border-radius:16px;border:1px solid var(--bd)}
.btn-logout{background:rgba(255,77,0,0.1);color:#ff4d00;border:1px solid rgba(255,77,0,0.3);padding:10px 18px;border-radius:10px;cursor:pointer;font-weight:600;transition:0.3s}
.btn-logout:hover{background:rgba(255,77,0,0.2)}
.c{background:var(--card);backdrop-filter:blur(16px);border:1px solid var(--bd);border-radius:20px;padding:30px;margin-bottom:20px;box-shadow:0 15px 35px rgba(0,0,0,0.2)}
.c-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.api-key-box{background:rgba(0,0,0,0.5);padding:16px;border-radius:12px;font-family:monospace;font-size:14px;word-break:break-all;border:1px solid var(--bd);display:flex;justify-content:space-between;align-items:center;color:var(--ac)}
.copy-btn{background:var(--ac);color:#000;border:none;padding:8px 16px;border-radius:8px;font-weight:700;cursor:pointer;font-size:12px;transition:0.2s}
.copy-btn:hover{background:#ffa640}
.table-container{overflow-x:auto;margin-top:16px}
table{width:100%;border-collapse:collapse;font-size:14px}
th{text-align:left;padding:14px 12px;color:var(--mut);font-size:12px;border-bottom:1px solid var(--bd);font-weight:600;text-transform:uppercase;letter-spacing:0.5px}
td{padding:14px 12px;border-bottom:1px solid rgba(255,255,255,0.04)}
tr:hover td{background:rgba(255,255,255,0.02)}
.danger-zone{border-color:rgba(255,77,0,0.3);background:rgba(255,77,0,0.03)}
.btn-danger{background:#ff4d00;color:#fff;border:none;padding:12px 24px;border-radius:10px;font-weight:600;cursor:pointer;transition:0.3s}
.btn-danger:hover{background:#cc3e00;box-shadow:0 5px 15px rgba(255,77,0,0.3)}
.msg{font-size:13px;margin-top:16px;padding:12px;border-radius:10px;text-align:center;display:none;font-weight:500}
.msg.error{background:rgba(255,77,0,0.1);color:#ff4d00;border:1px solid rgba(255,77,0,0.3);display:block}
.msg.success{background:rgba(46,204,113,0.1);color:#2ecc71;border:1px solid rgba(46,204,113,0.3);display:block}
.tutorial-link{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:20px;color:var(--ac);text-decoration:none;font-size:14px;font-weight:600;padding:12px;border-radius:10px;background:rgba(255,138,0,0.05);border:1px dashed rgba(255,138,0,0.5);transition:0.3s}
.tutorial-link:hover{background:rgba(255,138,0,0.1)}
@keyframes fadeIn{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>
<div class="w">

  <!-- AUTH SCREEN (Login/Register) -->
  <div id="auth-screen">
    <div class="auth-card">
      <div class="auth-header">
        <h1>⚡ Goku<span class="accent">Pay</span></h1>
        <p>Next-Gen Payment Gateway</p>
      </div>

      <div class="tabs">
        <div class="tab active" onclick="switchTab('login')">Login</div>
        <div class="tab" onclick="switchTab('register')">Register</div>
      </div>

      <!-- Login Form -->
      <div id="form-login">
        <div class="input-group">
          <label>Email Address</label>
          <input type="email" id="log-email" placeholder="you@gmail.com">
        </div>
        <div class="input-group">
          <label>Account Password</label>
          <input type="password" id="log-pass" placeholder="Enter your password">
        </div>
        <button class="btn-primary" onclick="doLogin()" id="logBtn">Login</button>
        <p id="log-msg" class="msg"></p>
      </div>

      <!-- Register Form -->
      <div id="form-register" style="display:none">
        <div class="input-group">
          <label>Email Address</label>
          <input type="email" id="reg-email" placeholder="you@gmail.com">
        </div>
        <div class="input-group">
          <label>Create Password</label>
          <input type="password" id="reg-pass" placeholder="Min 6 characters">
        </div>
        <div class="input-group">
          <label>Confirm Password</label>
          <input type="password" id="reg-confirm" placeholder="Repeat password">
        </div>
        <button class="btn-primary" onclick="doRegister()" id="regBtn">Create Account</button>
        <p id="reg-msg" class="msg"></p>
      </div>
    </div>
  </div>

  <!-- MAIN DASHBOARD -->
  <div id="main-screen">
    <div class="top-bar">
      <h2>⚡ Goku<span class="accent">Pay</span></h1></h2>
      <button class="btn-logout" onclick="logout()">Logout</button>
    </div>

    <!-- SETUP GMAIL (Aave tyare j dekhase jyare API Key na hoy) -->
    <div id="setup-card" class="c" style="display:none">
      <div class="c-header">
        <h3>Connect your Gmail 🚀</h3>
      </div>
      <p style="color:var(--mut);font-size:14px;margin-bottom:20px;line-height:1.6">
        To start auto-verifying FamApp payments, you need to link your Gmail. We need a <b>16-character App Password</b>. 
      </p>
      <div class="input-group">
        <label>16-Character App Password</label>
        <input type="password" id="app-pass" placeholder="xxxx xxxx xxxx xxxx" maxlength="16">
      </div>
      <button class="btn-primary" onclick="linkGmail()" id="linkBtn">Generate API Key</button>
      <a href="https://t.me/HOWTOUSE69" target="_blank" class="tutorial-link">
        ▶️ Watch Tutorial: How to get App Password?
      </a>
      <p id="setup-msg" class="msg"></p>
    </div>

    <!-- API KEY DISPLAY -->
    <div id="key-card" class="c" style="display:none">
      <div class="c-header">
        <h3>Your API Key</h3>
      </div>
      <p style="color:var(--mut);font-size:14px;margin-bottom:12px">Use this key in your Telegram bot to verify payments securely.</p>
      <div class="api-key-box">
        <span id="apiKeyText">pay_...</span>
        <button class="copy-btn" onclick="copyKey()">Copy</button>
      </div>
    </div>

    <!-- PAYMENTS TABLE -->
    <div id="payments-card" class="c" style="display:none">
      <div class="c-header">
        <h3>Recent Payments</h3>
        <button class="copy-btn" onclick="loadPayments()">Refresh</button>
      </div>
      <div class="table-container">
        <table id="payTable">
          <thead>
            <tr>
              <th>Amount</th>
              <th>Sender</th>
              <th>UTR</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <!-- DANGER ZONE -->
    <div id="danger-card" class="c danger-zone" style="display:none">
      <h3 style="color:#ff4d00;margin-bottom:8px">⚠️ Danger Zone</h3>
      <p style="color:var(--mut);font-size:14px;margin-bottom:20px">Deleting your account will permanently remove your linked Gmail, API Key, and all payment history.</p>
      <button class="btn-danger" onclick="deleteAccount()">Delete Account</button>
    </div>
  </div>

</div>

<script>
const $ = id => document.getElementById(id);
let currentToken = '';
let currentApiKey = '';
let refreshTimer;

// ================= AUTH =================
function switchTab(tab) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  if(tab === 'login'){
    document.querySelectorAll('.tab')[0].classList.add('active');
    $('form-login').style.display = 'block';
    $('form-register').style.display = 'none';
  } else {
    document.querySelectorAll('.tab')[1].classList.add('active');
    $('form-login').style.display = 'none';
    $('form-register').style.display = 'block';
  }
}

async function doRegister() {
  const email = $('reg-email').value.trim();
  const pass = $('reg-pass').value;
  const confirm = $('reg-confirm').value;
  const msg = $('reg-msg');
  
  if(pass.length < 6) return showMsg(msg, 'Password must be at least 6 characters.', 'error');
  if(pass !== confirm) return showMsg(msg, 'Passwords do not match.', 'error');

  $('regBtn').disabled = true; $('regBtn').textContent = 'Creating Account...';
  
  try {
    const res = await fetch('api.php?action=register', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({email, password: pass})
    });
    const data = await res.json();
    
    if(data.success) {
      showMsg(msg, '✅ Account created!', 'success');
      setTimeout(() => loginSuccess(data.session_token, data.api_key), 1000);
    } else {
      showMsg(msg, '❌ ' + data.error, 'error');
      $('regBtn').disabled = false; $('regBtn').textContent = 'Create Account';
    }
  } catch(e) {
    showMsg(msg, 'Network error.', 'error');
    $('regBtn').disabled = false; $('regBtn').textContent = 'Create Account';
  }
}

async function doLogin() {
  const email = $('log-email').value.trim();
  const pass = $('log-pass').value;
  const msg = $('log-msg');

  $('logBtn').disabled = true; $('logBtn').textContent = 'Verifying...';
  
  try {
    const res = await fetch('api.php?action=login', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({email, password: pass})
    });
    const data = await res.json();
    
    if(data.success) {
      showMsg(msg, '✅ Login successful!', 'success');
      setTimeout(() => loginSuccess(data.session_token, data.api_key), 800);
    } else {
      showMsg(msg, '❌ ' + data.error, 'error');
      $('logBtn').disabled = false; $('logBtn').textContent = 'Login';
    }
  } catch(e) {
    showMsg(msg, 'Network error.', 'error');
    $('logBtn').disabled = false; $('logBtn').textContent = 'Login';
  }
}

function showMsg(el, text, type) {
  el.textContent = text;
  el.className = 'msg ' + type;
}

// ================= DASHBOARD =================
function loginSuccess(token, apiKey) {
  currentToken = token;
  currentApiKey = apiKey;
  
  localStorage.setItem('goku_token', token);
  localStorage.setItem('goku_api_key', apiKey || '');
  
  $('auth-screen').style.display = 'none';
  $('main-screen').style.display = 'block';
  
  if (apiKey) {
    setupDashboardUI(apiKey);
  } else {
    // Show Gmail setup card
    $('setup-card').style.display = 'block';
    $('key-card').style.display = 'none';
    $('payments-card').style.display = 'none';
    $('danger-card').style.display = 'none';
  }
}

async function linkGmail() {
  const appPass = $('app-pass').value.replace(/\s/g, '');
  const msg = $('setup-msg');

  if (appPass.length !== 16) {
    return showMsg(msg, 'App Password must be exactly 16 characters.', 'error');
  }

  $('linkBtn').disabled = true; $('linkBtn').textContent = 'Verifying Gmail...';

  try {
    const res = await fetch('api.php?action=link_gmail', {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'X-Session-Token': currentToken},
      body: JSON.stringify({app_password: appPass})
    });
    const data = await res.json();

    if (data.success) {
      currentApiKey = data.api_key;
      localStorage.setItem('goku_api_key', currentApiKey);
      setupDashboardUI(currentApiKey);
    } else {
      showMsg(msg, '❌ ' + data.error, 'error');
      $('linkBtn').disabled = false; $('linkBtn').textContent = 'Generate API Key';
    }
  } catch (e) {
    showMsg(msg, 'Network error.', 'error');
    $('linkBtn').disabled = false; $('linkBtn').textContent = 'Generate API Key';
  }
}

function setupDashboardUI(apiKey) {
  $('setup-card').style.display = 'none';
  $('key-card').style.display = 'block';
  $('payments-card').style.display = 'block';
  $('danger-card').style.display = 'block';
  
  $('apiKeyText').textContent = apiKey;
  
  loadPayments();
  clearInterval(refreshTimer);
  refreshTimer = setInterval(loadPayments, 15000);
}

function logout() {
  localStorage.removeItem('goku_token');
  localStorage.removeItem('goku_api_key');
  location.reload();
}

function copyKey() {
  navigator.clipboard.writeText(currentApiKey);
  const btn = document.querySelector('.copy-btn');
  btn.textContent = 'Copied!';
  setTimeout(() => btn.textContent = 'Copy', 2000);
}

async function loadPayments() {
  if(!currentApiKey) return;
  try {
    const res = await fetch('api.php?action=get_payments', {
      headers: {'X-API-Key': currentApiKey}
    });
    const data = await res.json();
    
    const tbody = $('payTable').querySelector('tbody');
    
    if(!data.success || !data.data || !data.data.length) {
      tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:var(--mut)">No payments found yet. Worker is checking emails...</td></tr>`;
      return;
    }
    
    tbody.innerHTML = data.data.map(p => `
      <tr>
        <td><b style="color:var(--ac)">₹${p.amount}</b></td>
        <td>${p.sender_name}</td>
        <td style="font-family:monospace;font-size:12px">${p.utr}</td>
        <td style="color:var(--mut);font-size:12px">${p.received_at}</td>
      </tr>
    `).join('');
  } catch(e) {}
}

async function deleteAccount() {
  if(!confirm('⚠️ WARNING: Are you sure you want to delete your account permanently?')) return;
  if(!confirm('🛑 REALLY DELETE? All your data will be lost.')) return;

  try {
    const res = await fetch('api.php?action=delete_account', {
      method: 'POST',
      headers: {'X-API-Key': currentApiKey, 'Content-Type': 'application/json'}
    });
    const data = await res.json();
    
    if(data.success) {
      alert('✅ Account deleted successfully.');
      logout();
    } else {
      alert('❌ ' + data.error);
    }
  } catch(e) {
    alert('❌ Network Error');
  }
}

// Check if already logged in
window.onload = () => {
  const savedToken = localStorage.getItem('goku_token');
  const savedKey = localStorage.getItem('goku_api_key');
  if(savedToken) {
    loginSuccess(savedToken, savedKey);
  }
};
</script>
</body></html>
