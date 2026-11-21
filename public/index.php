<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard - GOKUxFAM</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script defer src="https://unpkg.com/lucide@latest"></script>
<style>
/* (CSS SAME AS BEFORE) */
:root{--bg:#f4f7fb;--card:#ffffff;--tx:#1e293b;--mut:#64748b;--ac:#2563eb;--ac-hover:#1d4ed8;--bd:#e2e8f0;--green:#10b981;--red:#ef4444;--shadow:0 10px 30px -10px rgba(0,0,0,0.08);}
*{box-sizing:border-box;margin:0;padding:0;font-family:'Plus Jakarta Sans',sans-serif}
body{background-color:var(--bg);color:var(--tx);min-height:100vh;display:flex;flex-direction:column;overflow-x:hidden;position:relative}
.bg-blob{position:fixed;border-radius:50%;filter:blur(80px);z-index:-1;opacity:0.6;animation:float 10s infinite ease-in-out alternate}
.blob-1{top:-10%;left:-10%;width:500px;height:500px;background:rgba(37,99,235,0.15)}
.blob-2{bottom:-10%;right:-10%;width:400px;height:400px;background:rgba(255,138,0,0.15);animation-delay:-5s}
@keyframes float{0%{transform:translate(0,0) scale(1)}100%{transform:translate(50px,50px) scale(1.1)}}
.public-nav{position:fixed;top:0;left:0;right:0;z-index:999;height:70px;background:rgba(255,255,255,0.8);backdrop-filter:blur(20px);border-bottom:1px solid rgba(0,0,0,0.05);display:flex;align-items:center;box-shadow:0 4px 20px rgba(0,0,0,0.02);animation:slideDown 0.5s ease-out}
.public-nav-container{display:flex;align-items:center;justify-content:space-between;width:100%;max-width:1200px;margin:0 auto;padding:0 20px}
.public-brand{display:inline-flex;align-items:center;text-decoration:none;color:var(--tx);font-weight:800;font-size:22px;letter-spacing:-0.5px}
.public-brand span{color:#ff8a00}
.public-nav-actions{display:flex;align-items:center;gap:14px}
.btn-logout{background:#fff;color:var(--tx);border:1px solid var(--bd);padding:8px 18px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;transition:all 0.3s;box-shadow:0 2px 5px rgba(0,0,0,0.02)}
.btn-logout:hover{background:#f8fafc;color:var(--red);border-color:#fecaca}
.main-wrapper{flex:1;max-width:850px;width:100%;margin:110px auto 50px;padding:0 20px;z-index:1}
.c{background:rgba(255,255,255,0.9);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.5);border-radius:24px;padding:35px;margin-bottom:24px;box-shadow:var(--shadow);transition:transform 0.3s ease,box-shadow 0.3s ease;animation:fadeUp 0.6s ease-out backwards}
.c:hover{transform:translateY(-3px);box-shadow:0 15px 35px -5px rgba(0,0,0,0.1)}
.c-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px}
.c-header h3{font-size:20px;font-weight:800;color:var(--tx);letter-spacing:-0.5px}
.input-group{margin-bottom:20px;animation:fadeUp 0.5s ease-out backwards}
.input-group label{display:block;font-size:13px;color:var(--tx);margin-bottom:8px;font-weight:700}
.input-group input{width:100%;padding:16px;border-radius:14px;border:2px solid var(--bd);background:#fff;color:var(--tx);font-size:15px;font-weight:500;outline:none;transition:all 0.3s}
.input-group input:focus{border-color:var(--ac);box-shadow:0 0 0 4px rgba(37,99,235,0.1);transform:translateY(-1px)}
.btn-primary{width:100%;padding:16px;border-radius:14px;border:none;background:linear-gradient(135deg,var(--ac),#3b82f6);color:#fff;font-weight:700;font-size:16px;cursor:pointer;transition:all 0.3s;box-shadow:0 8px 20px -6px rgba(37,99,235,0.4);position:relative;overflow:hidden}
.btn-primary::after{content:'';position:absolute;top:0;left:-100%;width:50%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.2),transparent);transition:0.5s}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 12px 25px -6px rgba(37,99,235,0.5)}
.btn-primary:hover::after{left:100%}
.btn-primary:disabled{opacity:0.6;cursor:not-allowed;transform:none;box-shadow:none}
.tutorial-link{display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:24px;color:var(--ac);text-decoration:none;font-size:14px;font-weight:700;padding:14px;border-radius:12px;background:#eff6ff;border:1px dashed #93c5fd;transition:all 0.3s}
.tutorial-link:hover{background:#dbeafe;transform:scale(1.02)}
.api-key-box{background:linear-gradient(to right,#f8fafc,#f1f5f9);padding:20px;border-radius:16px;font-family:'Courier New',monospace;font-size:16px;word-break:break-all;border:1px solid var(--bd);display:flex;justify-content:space-between;align-items:center;color:var(--ac);font-weight:700;box-shadow:inset 0 2px 4px rgba(0,0,0,0.02)}
.copy-btn{background:var(--ac);color:#fff;border:none;padding:10px 20px;border-radius:10px;font-weight:700;cursor:pointer;font-size:13px;transition:all 0.3s}
.copy-btn:hover{background:var(--ac-hover);transform:scale(1.05)}
.table-container{overflow-x:auto;border-radius:16px;border:1px solid var(--bd);background:#fff}
table{width:100%;border-collapse:collapse;font-size:14px}
th{text-align:left;padding:16px;color:var(--mut);font-size:12px;background:#f8fafc;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid var(--bd)}
td{padding:16px;border-bottom:1px solid #f1f5f9;color:var(--tx);font-weight:600}
tr:hover td{background:#f8fafc}
.danger-zone{border-color:#fecaca;background:linear-gradient(to bottom right,#fff,#fef2f2)}
.btn-danger{background:var(--red);color:#fff;border:none;padding:14px 24px;border-radius:12px;font-weight:700;cursor:pointer;transition:all 0.3s;box-shadow:0 4px 12px rgba(239,68,68,0.2)}
.btn-danger:hover{background:#dc2626;transform:translateY(-2px);box-shadow:0 8px 20px rgba(239,68,68,0.3)}
.msg{font-size:14px;margin-top:16px;padding:14px;border-radius:12px;text-align:center;display:none;font-weight:600;animation:fadeUp 0.3s ease}
.msg.error{background:#fef2f2;color:var(--red);border:1px solid #fecaca;display:block}
.msg.success{background:#f0fdf4;color:var(--green);border:1px solid #bbf7d0;display:block}
.blog-footer{background:#fff;border-top:1px solid var(--bd);padding:40px 0 20px;text-align:center;margin-top:auto}
.blog-footer p{font-size:13px;color:var(--mut)}
.blog-footer .links a{color:var(--ac);text-decoration:none;font-weight:700;margin:0 10px}
.fam-modal-backdrop{display:none;position:fixed;inset:0;background:rgba(15,23,42,0.4);backdrop-filter:blur(8px);z-index:9999;align-items:center;justify-content:center;padding:20px;opacity:0;transition:opacity 0.3s}
.fam-modal-backdrop.active{display:flex !important;opacity:1}
.fam-modal-box{background:#fff;border-radius:24px;max-width:420px;width:100%;padding:32px;text-align:center;box-shadow:0 25px 50px -12px rgba(15,23,42,0.3);transform:scale(0.9) translateY(20px);transition:all 0.3s cubic-bezier(0.175,0.885,0.32,1.275)}
.fam-modal-backdrop.active .fam-modal-box{transform:scale(1) translateY(0)}
.fam-modal-icon-wrap{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px}
.type-danger{background:#fef2f2;color:var(--red)}
.fam-modal-title{font-size:22px;font-weight:800;color:var(--tx);margin-bottom:8px}
.fam-modal-text{font-size:15px;color:var(--mut);line-height:1.6;margin-bottom:24px}
.fam-modal-actions{display:flex;gap:12px}
.fam-modal-btn{padding:14px;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;border:none;flex:1;transition:0.2s}
.fam-modal-btn-cancel{background:#f1f5f9;color:var(--tx)}
.fam-modal-btn-cancel:hover{background:#e2e8f0}
.fam-modal-btn-confirm{background:var(--red);color:#fff}
.fam-modal-btn-confirm:hover{background:#dc2626}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
@keyframes slideDown{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>

<div class="bg-blob blob-1"></div>
<div class="bg-blob blob-2"></div>

<header class="public-nav">
  <div class="public-nav-container">
    <a class="public-brand" href="index.php">⚡ GOKU<span>xFAM</span></a>
    <div class="public-nav-actions" id="navActions" style="display:none;">
      <button class="btn-logout" onclick="logout()">Logout</button>
    </div>
  </div>
</header>

<div class="main-wrapper">
  
  <!-- SETUP SCREEN -->
  <div id="setup-screen">
    <div class="c" style="max-width: 500px; margin: 0 auto; padding: 40px;">
      <div class="c-header" style="justify-content:center;">
        <h3 style="font-size: 24px;">Link Account 🚀</h3>
      </div>
      <p style="color:var(--mut);font-size:15px;margin-bottom:30px;text-align:center;line-height:1.6">Enter your FamApp Gmail, 16-character App Password, and the UPI ID where you want to receive money.</p>

      <div class="input-group" style="animation-delay: 0.1s;">
        <label>FamApp Gmail Address</label>
        <input type="email" id="email" placeholder="your-famapp-email@gmail.com">
      </div>

      <div class="input-group" style="animation-delay: 0.2s;">
        <label>16-Character App Password</label>
        <input type="password" id="app-pass" placeholder="xxxx xxxx xxxx xxxx" maxlength="16">
      </div>

      <!-- 🔥 NEW: UPI ID INPUT -->
      <div class="input-group" style="animation-delay: 0.25s;">
        <label>Your UPI ID (To receive payments)</label>
        <input type="text" id="upi_id" placeholder="e.g. 9876543210@ybl or name@okaxis">
      </div>

      <a href="https://t.me/HOWTOUSE69" target="_blank" class="tutorial-link" style="animation-delay: 0.3s;">
        ▶️ Watch Tutorial: How to get App Password?
      </a>

      <button class="btn-primary" onclick="doRegister()" id="regBtn" style="animation-delay: 0.4s;">Generate API Key</button>
      <p id="reg-msg" class="msg"></p>
    </div>
  </div>

  <!-- DASHBOARD SCREEN (Same as before) -->
  <div id="main-screen" style="display:none;">
    <div class="c" style="animation-delay: 0.1s;">
      <div class="c-header">
        <h3>Your API Key</h3>
        <span style="background:#dcfce7;color:#16a34a;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:800;box-shadow:0 2px 10px rgba(22,163,74,0.2);">● Active</span>
      </div>
      <p style="color:var(--mut);font-size:14px;margin-bottom:16px">Use this key in your Telegram bot to verify payments securely.</p>
      <div class="api-key-box">
        <span id="apiKeyText">pay_...</span>
        <button class="copy-btn" onclick="copyKey(event)">Copy</button>
      </div>
    </div>

    <div class="c" style="animation-delay: 0.2s;">
      <div class="c-header">
        <h3>Recent Payments</h3>
        <button class="copy-btn" onclick="loadPayments()" style="background:#f1f5f9;color:#0f172a;box-shadow:none;">Refresh ↻</button>
      </div>
      <div class="table-container">
        <table id="payTable">
          <thead><tr><th>Amount</th><th>Sender</th><th>UTR</th><th>Time</th></tr></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    <div class="c danger-zone" style="animation-delay: 0.3s;">
      <h3 style="color:var(--red);margin-bottom:8px;font-size:18px;">⚠️ Danger Zone</h3>
      <p style="color:#991b1b;font-size:14px;margin-bottom:20px;">Deleting your account will permanently remove your linked Gmail, API Key, and all payment history.</p>
      <button class="btn-danger" onclick="deleteAccount()">Delete Account</button>
    </div>
  </div>

</div>

<footer class="blog-footer">
  <div class="blog-footer-shell">
    <div class="links">
      <a href="index.php">Home</a>
      <a href="terms.html">Terms of Service</a>
      <a href="https://t.me/HOWTOUSE69" target="_blank">Support</a>
    </div>
    <p style="margin-top:15px;">&copy; 2026 GOKUxFAM. All rights reserved.</p>
  </div>
</footer>

<div class="fam-modal-backdrop" id="famGlobalModal" onclick="if(event.target===this)famCloseModal(false)">
  <div class="fam-modal-box">
    <div class="fam-modal-icon-wrap type-danger" id="famModalIconWrap"><i data-lucide="alert-triangle" style="width:32px;height:32px;"></i></div>
    <h3 class="fam-modal-title" id="famModalTitle">Confirm Action</h3>
    <p class="fam-modal-text" id="famModalText">Are you sure?</p>
    <div class="fam-modal-actions" id="famModalActions">
      <button type="button" class="fam-modal-btn fam-modal-btn-cancel" onclick="famCloseModal(false)">Cancel</button>
      <button type="button" class="fam-modal-btn fam-modal-btn-confirm" onclick="famCloseModal(true)">Confirm</button>
    </div>
  </div>
</div>

<script>
const $ = id => document.getElementById(id);
let currentApiKey = '';
let refreshTimer = null;

var _famConfirmResolver = null;
window.famConfirm = function(options) {
  return new Promise(function(resolve) {
    if (typeof options === 'string') options = { text: options };
    document.getElementById('famModalTitle').textContent = options.title || 'Confirm Action';
    document.getElementById('famModalText').textContent = options.text || '';
    document.getElementById('famModalConfirmBtn').textContent = options.confirmText || 'Confirm';
    _famConfirmResolver = resolve;
    document.getElementById('famGlobalModal').classList.add('active');
    document.body.style.overflow = 'hidden';
    if (window.lucide) lucide.createIcons();
  });
};
window.famCloseModal = function(result) {
  document.getElementById('famGlobalModal').classList.remove('active');
  document.body.style.overflow = '';
  if (_famConfirmResolver) { var res = _famConfirmResolver; _famConfirmResolver = null; res(!!result); }
};

function showMsg(el, text, type) {
  el.textContent = text;
  el.className = 'msg ' + type;
}

// 🔥 Update: Ahiya upi_id sathe data moklai rahyo che
async function doRegister() {
  const email = $('email').value.trim();
  const pass = $('app-pass').value.replace(/\s/g, '');
  const upi = $('upi_id').value.trim(); // UPI value
  const msg = $('reg-msg');

  if (!email.includes('@')) return showMsg(msg, 'Please enter a valid Gmail address.', 'error');
  if (pass.length !== 16) return showMsg(msg, 'App Password must be exactly 16 characters.', 'error');
  if (!upi.includes('@')) return showMsg(msg, 'Please enter a valid UPI ID (e.g. name@ybl).', 'error');

  $('regBtn').disabled = true; $('regBtn').textContent = 'Verifying Gmail...';
  msg.className = 'msg'; msg.style.display = 'none';

  try {
    const res = await fetch('api.php?action=register', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({email: email, app_password: pass, upi_id: upi}) // Ahiya upi_id moklyu
    });
    const data = await res.json();

    if (data.success) {
      localStorage.setItem('goku_api_key', data.api_key);
      currentApiKey = data.api_key;
      setTimeout(() => loginSuccess(currentApiKey), 800);
    } else {
      showMsg(msg, '❌ ' + data.error, 'error');
      $('regBtn').disabled = false; $('regBtn').textContent = 'Generate API Key';
    }
  } catch (e) {
    showMsg(msg, 'Network error. Check your internet.', 'error');
    $('regBtn').disabled = false; $('regBtn').textContent = 'Generate API Key';
  }
}

function loginSuccess(key) {
  currentApiKey = key;
  $('setup-screen').style.display = 'none';
  $('main-screen').style.display = 'block';
  $('navActions').style.display = 'flex';
  $('apiKeyText').textContent = key;
  loadPayments();
  clearInterval(refreshTimer);
  refreshTimer = setInterval(loadPayments, 15000);
}

function logout() {
  localStorage.removeItem('goku_api_key');
  location.reload();
}

function copyKey(e) {
  navigator.clipboard.writeText(currentApiKey);
  const btn = e.target;
  const old = btn.textContent;
  btn.textContent = 'Copied! ✓';
  btn.style.background = '#10b981';
  setTimeout(() => { btn.textContent = old; btn.style.background = 'var(--ac)'; }, 2000);
}

async function loadPayments() {
  if (!currentApiKey) return;
  try {
    const res = await fetch('api.php?action=get_payments', { headers: {'X-API-Key': currentApiKey} });
    const data = await res.json();
    const tbody = $('payTable').querySelector('tbody');
    
    if (!data.success || !data.data || !data.data.length) {
      tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:var(--mut);padding:40px;font-weight:500;">No payments found yet. Worker is checking emails...</td></tr>`;
      return;
    }
    tbody.innerHTML = data.data.map(p => `
      <tr>
        <td><b style="color:var(--ac);font-size:15px;">₹${p.amount}</b></td>
        <td>${p.sender_name}</td>
        <td style="font-family:monospace;font-size:13px;background:#f8fafc;padding:6px 10px;border-radius:6px;border:1px solid #e2e8f0;">${p.utr}</td>
        <td style="color:var(--mut);font-size:13px">${p.received_at}</td>
      </tr>
    `).join('');
  } catch (e) {}
}

async function deleteAccount() {
  const confirmed = await famConfirm({
    title: "Delete Account?",
    text: "WARNING: This will permanently remove your linked Gmail, API Key, and all payment history. This cannot be undone.",
    confirmText: "Yes, Delete",
    danger: true
  });
  if (!confirmed) return;

  try {
    const res = await fetch('api.php?action=delete_account', {
      method: 'POST',
      headers: {'X-API-Key': currentApiKey, 'Content-Type': 'application/json'}
    });
    const data = await res.json();
    if (data.success) {
      famAlert("✅ Account deleted successfully.");
      setTimeout(logout, 1500);
    } else {
      famAlert("❌ " + data.error);
    }
  } catch (e) {
    famAlert("❌ Network Error");
  }
}

window.famAlert = function(msg) {
  return new Promise((resolve) => {
    document.getElementById('famModalTitle').textContent = 'Notice';
    document.getElementById('famModalText').textContent = msg;
    document.getElementById('famModalCancelBtn').style.display = 'none';
    document.getElementById('famModalConfirmBtn').textContent = 'OK';
    document.getElementById('famModalConfirmBtn').style.background = '#2563eb';
    _famConfirmResolver = resolve;
    document.getElementById('famGlobalModal').classList.add('active');
  });
};

window.onload = () => {
  if (window.lucide) lucide.createIcons();
  const savedKey = localStorage.getItem('goku_api_key');
  if (savedKey) loginSuccess(savedKey);
};
</script>
</body>
</html>
