<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Goku Pay — FamApp UPI Payment Gateway</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #050505;
  --bg2: #0a0a0c;
  --card: rgba(20, 20, 25, 0.65);
  --card-solid: #121216;
  --tx: #ffffff;
  --mut: #8b91a1;
  --ac: #ff8a00;
  --ac2: #ff4d00;
  --ac-glow: rgba(255, 138, 0, 0.15);
  --bd: rgba(255, 255, 255, 0.08);
  --success: #2ecc71;
  --danger: #ff4d00;
  --radius: 16px;
  --radius-lg: 24px;
}

* { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
  background: var(--bg);
  color: var(--tx);
  font-family: 'Plus Jakarta Sans', sans-serif;
  min-height: 100vh;
  overflow-x: hidden;
  line-height: 1.5;
  background-image:
    radial-gradient(ellipse 80% 50% at 20% -10%, rgba(255, 138, 0, 0.12), transparent),
    radial-gradient(ellipse 60% 40% at 85% 10%, rgba(255, 77, 0, 0.08), transparent);
}

/* ===== NAV ===== */
.nav {
  position: sticky; top: 0; z-index: 100;
  backdrop-filter: blur(20px);
  background: rgba(5, 5, 5, 0.8);
  border-bottom: 1px solid var(--bd);
  padding: 14px 24px;
}
.nav-inner {
  max-width: 1100px; margin: 0 auto;
  display: flex; align-items: center; justify-content: space-between;
}
.logo { font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
.logo span { background: linear-gradient(135deg, var(--ac), var(--ac2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
.nav-links { display: flex; gap: 8px; align-items: center; }
.nav-links a {
  color: var(--mut); text-decoration: none; font-size: 14px; font-weight: 500;
  padding: 8px 14px; border-radius: 10px; transition: 0.2s;
}
.nav-links a:hover { color: var(--tx); background: rgba(255,255,255,0.05); }
.btn-nav {
  background: linear-gradient(135deg, var(--ac), var(--ac2));
  color: #000 !important; font-weight: 700 !important;
  padding: 10px 18px !important;
}
.btn-nav:hover { filter: brightness(1.1); }

/* ===== HERO ===== */
.hero {
  max-width: 1100px; margin: 0 auto;
  padding: 80px 24px 60px;
  text-align: center;
}
.badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(255,138,0,0.1); border: 1px solid rgba(255,138,0,0.3);
  color: var(--ac); font-size: 13px; font-weight: 600;
  padding: 8px 16px; border-radius: 999px; margin-bottom: 28px;
}
.hero h1 {
  font-size: clamp(36px, 6vw, 56px);
  font-weight: 800; letter-spacing: -1.5px; line-height: 1.15;
  margin-bottom: 20px;
}
.hero h1 .grad {
  background: linear-gradient(135deg, var(--ac), var(--ac2));
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
.hero p {
  color: var(--mut); font-size: 18px; max-width: 560px;
  margin: 0 auto 36px; line-height: 1.6;
}
.hero-cta { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
.btn-primary {
  display: inline-flex; align-items: center; gap: 8px;
  background: linear-gradient(135deg, var(--ac), var(--ac2));
  color: #000; font-weight: 700; font-size: 16px;
  padding: 16px 28px; border-radius: 14px; border: none;
  cursor: pointer; text-decoration: none; transition: 0.25s;
}
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 30px -8px var(--ac); }
.btn-secondary {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(255,255,255,0.05); color: var(--tx);
  font-weight: 600; font-size: 16px;
  padding: 16px 28px; border-radius: 14px;
  border: 1px solid var(--bd); cursor: pointer; text-decoration: none; transition: 0.25s;
}
.btn-secondary:hover { background: rgba(255,255,255,0.09); }

.stats {
  display: flex; justify-content: center; gap: 48px;
  margin-top: 56px; flex-wrap: wrap;
}
.stat { text-align: center; }
.stat .num { font-size: 28px; font-weight: 800; color: var(--ac); }
.stat .label { font-size: 13px; color: var(--mut); margin-top: 4px; }

/* ===== SECTIONS ===== */
.section {
  max-width: 1100px; margin: 0 auto;
  padding: 70px 24px;
}
.section-header { text-align: center; margin-bottom: 48px; }
.section-header h2 {
  font-size: 32px; font-weight: 800; letter-spacing: -0.8px; margin-bottom: 12px;
}
.section-header p { color: var(--mut); font-size: 16px; max-width: 480px; margin: 0 auto; }

/* ===== HOW IT WORKS ===== */
.steps {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}
.step {
  background: var(--card); border: 1px solid var(--bd);
  border-radius: var(--radius-lg); padding: 32px 28px;
  backdrop-filter: blur(16px); position: relative;
  transition: 0.3s;
}
.step:hover { border-color: rgba(255,138,0,0.3); transform: translateY(-4px); }
.step-num {
  width: 42px; height: 42px; border-radius: 12px;
  background: linear-gradient(135deg, var(--ac), var(--ac2));
  color: #000; font-weight: 800; font-size: 18px;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 20px;
}
.step h3 { font-size: 18px; font-weight: 700; margin-bottom: 10px; }
.step p { color: var(--mut); font-size: 14px; line-height: 1.6; }
.step a { color: var(--ac); text-decoration: none; font-weight: 600; }
.step a:hover { text-decoration: underline; }

/* ===== REGISTER / GET KEY ===== */
.get-key-grid {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 40px; align-items: start;
}
@media (max-width: 800px) { .get-key-grid { grid-template-columns: 1fr; } }

.auth-card {
  background: var(--card); border: 1px solid var(--bd);
  border-radius: var(--radius-lg); padding: 36px;
  backdrop-filter: blur(20px);
  box-shadow: 0 30px 60px -15px rgba(0,0,0,0.6);
}
.auth-card h3 { font-size: 22px; font-weight: 700; margin-bottom: 6px; }
.auth-card .sub { color: var(--mut); font-size: 14px; margin-bottom: 28px; }

.input-group { margin-bottom: 18px; }
.input-group label {
  display: block; font-size: 12px; color: var(--mut);
  margin-bottom: 8px; font-weight: 600; letter-spacing: 0.3px;
}
.input-group input {
  width: 100%; padding: 14px 16px; border-radius: 12px;
  border: 1px solid var(--bd); background: rgba(0,0,0,0.35);
  color: #fff; font-size: 15px; outline: none; transition: 0.25s;
  font-family: inherit;
}
.input-group input:focus {
  border-color: var(--ac); background: rgba(0,0,0,0.5);
  box-shadow: 0 0 0 3px var(--ac-glow);
}
.input-group .hint { font-size: 12px; color: var(--mut); margin-top: 6px; }

.tutorial-link {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  margin-bottom: 20px; color: var(--ac); text-decoration: none;
  font-size: 13px; font-weight: 600; padding: 12px;
  border-radius: 10px; background: rgba(255,138,0,0.06);
  border: 1px dashed rgba(255,138,0,0.4); transition: 0.25s;
}
.tutorial-link:hover { background: rgba(255,138,0,0.12); }

.btn-full { width: 100%; justify-content: center; margin-top: 6px; }
.btn-full:disabled { opacity: 0.6; cursor: not-allowed; transform: none !important; }

.msg {
  font-size: 13px; margin-top: 16px; padding: 12px 14px;
  border-radius: 10px; text-align: center; font-weight: 500; display: none;
}
.msg.error { background: rgba(255,77,0,0.1); color: #ff4d00; border: 1px solid rgba(255,77,0,0.3); display: block; }
.msg.success { background: rgba(46,204,113,0.1); color: #2ecc71; border: 1px solid rgba(46,204,113,0.3); display: block; }

.features-list { list-style: none; }
.features-list li {
  display: flex; gap: 14px; align-items: flex-start;
  padding: 16px 0; border-bottom: 1px solid var(--bd);
}
.features-list li:last-child { border-bottom: none; }
.features-list .icon {
  width: 36px; height: 36px; border-radius: 10px;
  background: rgba(255,138,0,0.12); color: var(--ac);
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; flex-shrink: 0;
}
.features-list h4 { font-size: 15px; font-weight: 600; margin-bottom: 3px; }
.features-list p { font-size: 13px; color: var(--mut); }

/* ===== DASHBOARD ===== */
#main-screen { display: none; }
.top-bar {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 28px; background: var(--card); backdrop-filter: blur(16px);
  padding: 16px 24px; border-radius: var(--radius); border: 1px solid var(--bd);
}
.btn-logout {
  background: rgba(255,77,0,0.1); color: #ff4d00;
  border: 1px solid rgba(255,77,0,0.3); padding: 10px 18px;
  border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 14px;
  transition: 0.25s; font-family: inherit;
}
.btn-logout:hover { background: rgba(255,77,0,0.2); }

.card {
  background: var(--card); border: 1px solid var(--bd);
  border-radius: var(--radius-lg); padding: 28px;
  margin-bottom: 20px; backdrop-filter: blur(16px);
}
.card-header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 18px;
}
.card-header h3 { font-size: 18px; font-weight: 700; }

.api-key-box {
  background: rgba(0,0,0,0.5); padding: 16px 18px; border-radius: 12px;
  font-family: 'JetBrains Mono', monospace; font-size: 14px;
  word-break: break-all; border: 1px solid var(--bd);
  display: flex; justify-content: space-between; align-items: center;
  color: var(--ac); gap: 16px;
}
.copy-btn {
  background: var(--ac); color: #000; border: none;
  padding: 8px 16px; border-radius: 8px; font-weight: 700;
  cursor: pointer; font-size: 12px; transition: 0.2s; white-space: nowrap;
  font-family: inherit;
}
.copy-btn:hover { background: #ffa640; }

.table-container { overflow-x: auto; margin-top: 8px; }
table { width: 100%; border-collapse: collapse; font-size: 14px; }
th {
  text-align: left; padding: 12px 14px; color: var(--mut);
  font-size: 11px; border-bottom: 1px solid var(--bd);
  font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px;
}
td { padding: 14px; border-bottom: 1px solid rgba(255,255,255,0.04); }
tr:hover td { background: rgba(255,255,255,0.02); }

.danger-zone { border-color: rgba(255,77,0,0.25); background: rgba(255,77,0,0.03); }
.btn-danger {
  background: #ff4d00; color: #fff; border: none;
  padding: 12px 24px; border-radius: 10px; font-weight: 600;
  cursor: pointer; transition: 0.25s; font-family: inherit; font-size: 14px;
}
.btn-danger:hover { background: #cc3e00; box-shadow: 0 5px 15px rgba(255,77,0,0.3); }

/* ===== DOCS ===== */
.docs-tabs {
  display: flex; gap: 6px; margin-bottom: 20px;
  background: rgba(0,0,0,0.3); padding: 6px; border-radius: 12px;
  width: fit-content;
}
.docs-tab {
  padding: 10px 20px; border-radius: 9px; border: none;
  background: transparent; color: var(--mut); font-weight: 600;
  font-size: 14px; cursor: pointer; transition: 0.2s; font-family: inherit;
}
.docs-tab.active {
  background: linear-gradient(135deg, var(--ac), var(--ac2));
  color: #000;
}
.docs-tab:hover:not(.active) { color: var(--tx); background: rgba(255,255,255,0.05); }

.code-block {
  background: #0d0d10; border: 1px solid var(--bd);
  border-radius: 14px; overflow: hidden; margin-bottom: 20px;
}
.code-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 12px 18px; background: rgba(255,255,255,0.03);
  border-bottom: 1px solid var(--bd); font-size: 13px; color: var(--mut);
}
.code-header .method {
  background: rgba(255,138,0,0.15); color: var(--ac);
  padding: 3px 10px; border-radius: 6px; font-weight: 700; font-size: 12px;
  margin-right: 10px;
}
.code-body {
  padding: 20px; font-family: 'JetBrains Mono', monospace;
  font-size: 13px; line-height: 1.7; overflow-x: auto;
  color: #e0e0e0; white-space: pre-wrap;
}
.code-body .comment { color: #6a737d; }
.code-body .string { color: #a5d6ff; }
.code-body .key { color: #ff8a00; }

.response-grid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px; margin-top: 24px;
}
.response-card {
  background: rgba(0,0,0,0.3); border: 1px solid var(--bd);
  border-radius: 12px; padding: 18px;
}
.response-card .tag {
  display: inline-block; font-size: 11px; font-weight: 700;
  padding: 4px 10px; border-radius: 6px; margin-bottom: 12px;
}
.tag.ok { background: rgba(46,204,113,0.15); color: #2ecc71; }
.tag.fail { background: rgba(255,77,0,0.15); color: #ff4d00; }
.tag.warn { background: rgba(255,193,7,0.15); color: #ffc107; }
.response-card pre {
  font-family: 'JetBrains Mono', monospace; font-size: 12px;
  color: var(--mut); white-space: pre-wrap; line-height: 1.6;
}

/* ===== FOOTER ===== */
.footer {
  border-top: 1px solid var(--bd);
  padding: 40px 24px; text-align: center;
  color: var(--mut); font-size: 13px;
}
.footer a { color: var(--ac); text-decoration: none; }
.footer .note { margin-top: 8px; font-size: 12px; opacity: 0.7; }

/* ===== UTILS ===== */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(16px); }
  to { opacity: 1; transform: translateY(0); }
}
.fade-in { animation: fadeIn 0.5s ease-out; }
.hidden { display: none !important; }

@media (max-width: 600px) {
  .hero { padding: 50px 20px 40px; }
  .stats { gap: 28px; }
  .section { padding: 50px 20px; }
  .auth-card { padding: 28px 22px; }
  .nav-links a:not(.btn-nav) { display: none; }
}
</style>
</head>
<body>

<!-- ===================== NAV ===================== -->
<nav class="nav">
  <div class="nav-inner">
    <div class="logo">⚡ Goku<span>Pay</span></div>
    <div class="nav-links">
      <a href="#how">How it works</a>
      <a href="#docs">API Docs</a>
      <a href="#get-key" class="btn-nav">Get Free API Key</a>
    </div>
  </div>
</nav>

<!-- ===================== LANDING (shown when not logged in) ===================== -->
<div id="landing">

  <!-- HERO -->
  <section class="hero">
    <div class="badge">🚀 FamApp UPI Payment Gateway</div>
    <h1>Verify FamApp Payments<br><span class="grad">Instantly & Automatically</span></h1>
    <p>Apne Telegram bot me FamApp payment auto-verify karo. Register karo, API key lo, integrate karo — bas 3 steps.</p>
    <div class="hero-cta">
      <a href="#get-key" class="btn-primary">Get Free API Key →</a>
      <a href="#docs" class="btn-secondary">View Docs</a>
    </div>
    <div class="stats">
      <div class="stat"><div class="num">~2s</div><div class="label">Verify Speed</div></div>
      <div class="stat"><div class="num">100%</div><div class="label">Auto Verified</div></div>
      <div class="stat"><div class="num">Free</div><div class="label">No Cost</div></div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section class="section" id="how">
    <div class="section-header">
      <h2>3 Steps Setup</h2>
      <p>Apne FamApp wali Gmail + App Password se register karo. API key milegi. Bas woh key apne bot me lagao.</p>
    </div>
    <div class="steps">
      <div class="step">
        <div class="step-num">01</div>
        <h3>Gmail App Password Banao</h3>
        <p>FamApp transaction emails jis Gmail pe aate hain, us Gmail ka App Password banao. Normal password kaam nahi karega — sirf App Password.</p>
        <p style="margin-top:12px"><a href="https://myaccount.google.com/apppasswords" target="_blank">Click here →</a></p>
      </div>
      <div class="step">
        <div class="step-num">02</div>
        <h3>Register Karo → API Key Lo</h3>
        <p>Neeche form me apna Gmail aur App Password daalo. Hum Gmail verify karenge aur tumhe ek unique API key denge. Yeh key tumhari identity hai.</p>
      </div>
      <div class="step">
        <div class="step-num">03</div>
        <h3>Bot Me Integrate Karo</h3>
        <p>API key aur docs ke saath apne Telegram bot me verify endpoint call karo. User UTR + amount dega, API instantly verify karega — auto balance add!</p>
      </div>
    </div>
  </section>

  <!-- GET API KEY -->
  <section class="section" id="get-key">
    <div class="section-header">
      <h2>Free API Key Lo</h2>
      <p>Sirf Gmail + App Password chahiye. Koi OTP nahi, koi signup nahi.</p>
    </div>
    <div class="get-key-grid">
      <div class="auth-card">
        <h3>⚡ Generate API Key</h3>
        <p class="sub">Gmail + 16-char App Password se register karo</p>

        <div class="input-group">
          <label>GMAIL ADDRESS</label>
          <input type="email" id="email" placeholder="tumhara@gmail.com">
          <div class="hint">FamApp wali Gmail — jis pe transaction emails aate hain</div>
        </div>

        <div class="input-group">
          <label>GMAIL APP PASSWORD</label>
          <input type="password" id="app-pass" placeholder="xxxxxxxxxxxxxxxx" maxlength="16">
          <div class="hint">Normal Gmail password nahi — App Password chahiye (16 chars, spaces hata do)</div>
        </div>

        <a href="https://t.me/HOWTOUSE69" target="_blank" class="tutorial-link">
          ▶️ Watch Tutorial: How to get 16-digit App Password?
        </a>

        <button class="btn-primary btn-full" onclick="doRegister()" id="regBtn">Get My API Key →</button>
        <p id="reg-msg" class="msg"></p>
      </div>

      <div>
        <ul class="features-list">
          <li>
            <div class="icon">⚡</div>
            <div>
              <h4>Instant Verification</h4>
              <p>Emails se payment details extract karke ~2 second me response</p>
            </div>
          </li>
          <li>
            <div class="icon">🔒</div>
            <div>
              <h4>Secure & Private</h4>
              <p>App Password encrypt karke store hota hai. API key hashed rakhi jaati hai</p>
            </div>
          </li>
          <li>
            <div class="icon">🤖</div>
            <div>
              <h4>Bot Ready</h4>
              <p>Telegram bot, website, ya koi bhi backend me easily integrate karo</p>
            </div>
          </li>
          <li>
            <div class="icon">🆓</div>
            <div>
              <h4>Completely Free</h4>
              <p>Koi limit nahi, koi hidden charges nahi. Free forever</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </section>

  <!-- API DOCS -->
  <section class="section" id="docs">
    <div class="section-header">
      <h2>API Documentation</h2>
      <p>Ek hi endpoint — payments fetch karo. Simple aur powerful.</p>
    </div>

    <div class="docs-tabs">
      <button class="docs-tab active" onclick="switchTab('python')">Python</button>
      <button class="docs-tab" onclick="switchTab('nodejs')">Node.js</button>
      <button class="docs-tab" onclick="switchTab('curl')">cURL</button>
    </div>

    <!-- PYTHON -->
    <div id="tab-python" class="code-block">
      <div class="code-header">
        <div><span class="method">GET</span> /api.php?action=get_payments</div>
        <span>Python</span>
      </div>
      <div class="code-body"><span class="comment"># Install: pip install requests</span>
import requests

API_KEY = <span class="string">"pay_tumhari_key_yahan"</span>
API_URL = <span class="string">"https://your-domain.com/api.php"</span>

def get_payments():
    res = requests.get(
        f<span class="string">"{API_URL}?action=get_payments"</span>,
        headers={<span class="string">"X-API-Key"</span>: API_KEY},
        timeout=15
    )
    data = res.json()
    if data.get(<span class="string">"success"</span>):
        for p in data[<span class="string">"data"</span>]:
            print(f<span class="string">"₹{p['amount']} from {p['sender_name']} | UTR: {p['utr']}"</span>)
        return data[<span class="string">"data"</span>]
    return []</div>
    </div>

    <!-- NODE.JS -->
    <div id="tab-nodejs" class="code-block hidden">
      <div class="code-header">
        <div><span class="method">GET</span> /api.php?action=get_payments</div>
        <span>Node.js</span>
      </div>
      <div class="code-body"><span class="comment">// Install: npm install axios</span>
const axios = require(<span class="string">'axios'</span>);

const API_KEY = <span class="string">'pay_tumhari_key_yahan'</span>;
const API_URL = <span class="string">'https://your-domain.com/api.php'</span>;

async function getPayments() {
  const res = await axios.get(<span class="string">`${API_URL}?action=get_payments`</span>, {
    headers: { <span class="string">'X-API-Key'</span>: API_KEY }
  });
  const data = res.data;
  if (data.success) {
    data.data.forEach(p => {
      console.log(<span class="string">`₹${p.amount} from ${p.sender_name} | UTR: ${p.utr}`</span>);
    });
    return data.data;
  }
  return [];
}</div>
    </div>

    <!-- CURL -->
    <div id="tab-curl" class="code-block hidden">
      <div class="code-header">
        <div><span class="method">GET</span> /api.php?action=get_payments</div>
        <span>cURL</span>
      </div>
      <div class="code-body">curl -X GET <span class="string">"https://your-domain.com/api.php?action=get_payments"</span> \
  -H <span class="string">"X-API-Key: pay_tumhari_key_yahan"</span>

<span class="comment"># Response example:</span>
{
  <span class="string">"success"</span>: true,
  <span class="string">"data"</span>: [
    {
      <span class="string">"amount"</span>: 500,
      <span class="string">"sender_name"</span>: <span class="string">"Rahul"</span>,
      <span class="string">"utr"</span>: <span class="string">"614205359807"</span>,
      <span class="string">"received_at"</span>: <span class="string">"2026-11-20 14:32:10"</span>
    }
  ]
}</div>
    </div>

    <!-- Response Fields -->
    <h3 style="font-size:18px;font-weight:700;margin:32px 0 16px">Response Fields</h3>
    <div class="response-grid">
      <div class="response-card">
        <span class="tag ok">✅ Success</span>
        <pre>{
  "success": true,
  "data": [
    {
      "amount": 500,
      "sender_name": "Rahul",
      "utr": "614205359807",
      "received_at": "2026-11-20 14:32:10"
    }
  ]
}</pre>
      </div>
      <div class="response-card">
        <span class="tag fail">❌ Invalid Key</span>
        <pre>{
  "error": "Invalid API Key."
}</pre>
      </div>
      <div class="response-card">
        <span class="tag warn">⚠️ Rate Limited</span>
        <pre>{
  "error": "Too many requests."
}</pre>
      </div>
    </div>
  </section>

</div><!-- end #landing -->


<!-- ===================== DASHBOARD (shown after login) ===================== -->
<div id="main-screen" class="section fade-in">
  <div class="top-bar">
    <h2 style="font-size:20px;font-weight:800">⚡ Goku<span style="background:linear-gradient(135deg,#ff8a00,#ff4d00);-webkit-background-clip:text;-webkit-text-fill-color:transparent">Pay</span></h2>
    <button class="btn-logout" onclick="logout()">Logout</button>
  </div>

  <!-- API KEY -->
  <div class="card">
    <div class="card-header">
      <h3>Your API Key</h3>
    </div>
    <p style="color:var(--mut);font-size:14px;margin-bottom:14px">Is key ko apne Telegram bot / backend me use karo payments fetch karne ke liye.</p>
    <div class="api-key-box">
      <span id="apiKeyText">pay_...</span>
      <button class="copy-btn" onclick="copyKey()">Copy</button>
    </div>
  </div>

  <!-- PAYMENTS -->
  <div class="card">
    <div class="card-header">
      <h3>Recent Payments</h3>
      <button class="copy-btn" onclick="loadPayments()" style="background:rgba(255,255,255,0.08);color:#fff">Refresh</button>
    </div>
    <div class="table-container">
      <table id="payTable">
        <thead>
          <tr>
            <th>Amount</th>
            <th>Sender</th>
            <th>UTR / Txn ID</th>
            <th>Time</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- DANGER ZONE -->
  <div class="card danger-zone">
    <h3 style="color:#ff4d00;margin-bottom:8px;font-size:16px">⚠️ Danger Zone</h3>
    <p style="color:var(--mut);font-size:14px;margin-bottom:18px">Deleting your account will permanently remove your linked Gmail, API Key, and all payment history.</p>
    <button class="btn-danger" onclick="deleteAccount()">Delete Email / Account</button>
  </div>
</div>


<!-- FOOTER -->
<footer class="footer">
  <div>⚡ GokuPay — Free FamApp UPI Payment Verification</div>
  <div class="note">Not affiliated with FamPay / FamApp. Made with ❤️</div>
</footer>


<script>
const $ = id => document.getElementById(id);
let currentApiKey = '';
let refreshTimer;

function showMsg(el, text, type) {
  el.textContent = text;
  el.className = 'msg ' + type;
}

// ================= REGISTER =================
async function doRegister() {
  const email = $('email').value.trim();
  const pass = $('app-pass').value.replace(/\s/g, '');
  const msg = $('reg-msg');

  if (!email.includes('@')) return showMsg(msg, 'Please enter a valid Gmail address.', 'error');
  if (pass.length !== 16) return showMsg(msg, 'App Password must be exactly 16 characters.', 'error');

  $('regBtn').disabled = true;
  $('regBtn').textContent = 'Verifying Gmail...';
  msg.className = 'msg'; msg.style.display = 'none';

  try {
    const res = await fetch('api.php?action=register', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({email, app_password: pass})
    });
    const data = await res.json();

    if (data.success) {
      localStorage.setItem('goku_api_key', data.api_key);
      currentApiKey = data.api_key;
      showMsg(msg, '✅ API Key generated successfully!', 'success');
      setTimeout(() => loginSuccess(currentApiKey), 900);
    } else {
      showMsg(msg, '❌ ' + (data.error || 'Registration failed'), 'error');
      $('regBtn').disabled = false;
      $('regBtn').textContent = 'Get My API Key →';
    }
  } catch (e) {
    showMsg(msg, 'Network error. Check your internet.', 'error');
    $('regBtn').disabled = false;
    $('regBtn').textContent = 'Get My API Key →';
  }
}

// ================= DASHBOARD =================
function loginSuccess(key) {
  currentApiKey = key;
  $('landing').classList.add('hidden');
  $('main-screen').style.display = 'block';
  $('apiKeyText').textContent = key;
  loadPayments();
  clearInterval(refreshTimer);
  refreshTimer = setInterval(loadPayments, 15000);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function logout() {
  localStorage.removeItem('goku_api_key');
  location.reload();
}

function copyKey() {
  navigator.clipboard.writeText(currentApiKey);
  const btn = document.querySelector('#main-screen .copy-btn');
  btn.textContent = 'Copied!';
  setTimeout(() => btn.textContent = 'Copy', 2000);
}

async function loadPayments() {
  if (!currentApiKey) return;
  try {
    const res = await fetch('api.php?action=get_payments', {
      headers: {'X-API-Key': currentApiKey}
    });
    const data = await res.json();
    const tbody = $('payTable').querySelector('tbody');

    if (!data.success || !data.data || !data.data.length) {
      tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:var(--mut);padding:28px">No payments found yet. Worker is checking emails...</td></tr>`;
      return;
    }

    tbody.innerHTML = data.data.map(p => `
      <tr>
        <td><b style="color:var(--ac)">₹${p.amount}</b></td>
        <td>${p.sender_name || '-'}</td>
        <td style="font-family:'JetBrains Mono',monospace;font-size:12px">${p.utr || p.transaction_id || '-'}</td>
        <td style="color:var(--mut);font-size:13px">${p.received_at}</td>
      </tr>
    `).join('');
  } catch (e) {}
}

async function deleteAccount() {
  if (!confirm('⚠️ WARNING: Are you sure you want to delete your account permanently?')) return;
  if (!confirm('🛑 REALLY DELETE? All your data will be lost.')) return;

  try {
    const res = await fetch('api.php?action=delete_account', {
      method: 'POST',
      headers: {'X-API-Key': currentApiKey, 'Content-Type': 'application/json'}
    });
    const data = await res.json();
    if (data.success) {
      alert('✅ Account deleted successfully.');
      logout();
    } else {
      alert('❌ ' + (data.error || 'Failed'));
    }
  } catch (e) {
    alert('❌ Network Error');
  }
}

// ================= DOCS TABS =================
function switchTab(name) {
  ['python', 'nodejs', 'curl'].forEach(t => {
    const el = $('tab-' + t);
    if (el) el.classList.toggle('hidden', t !== name);
  });
  document.querySelectorAll('.docs-tab').forEach(btn => {
    btn.classList.toggle('active', btn.textContent.toLowerCase().includes(name === 'nodejs' ? 'node' : name));
  });
}

// ================= INIT =================
window.onload = () => {
  const savedKey = localStorage.getItem('goku_api_key');
  if (savedKey) {
    loginSuccess(savedKey);
  }
};
</script>
</body>
</html>
