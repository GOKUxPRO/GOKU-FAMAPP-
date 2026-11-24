<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FamGateway — Automated UPI Payment Verification</title>
  <meta name="description" content="Automated UPI payment verification. Reads FamApp payment emails via IMAP, extracts UTR, stores in Firebase, and fires webhooks.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

<!-- ================= NAV ================= -->
<header class="nav">
  <div class="shell nav-inner">
    <a class="brand" href="index.html">
      <span class="brand-mark">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
        </svg>
      </span>
      FamGateway
    </a>
    <ul class="links">
      <li><a href="#flow">How it works</a></li>
      <li><a href="#api">API</a></li>
      <li><a href="#checkout">Checkout</a></li>
      <li><a href="#features">Features</a></li>
    </ul>
    <div class="nav-actions">
      <a class="login" href="login.php">Sign in</a>
      <a class="nav-cta" href="register.php">Get API key <span>↗</span></a>
    </div>
    <button class="menu" aria-label="Menu" onclick="toggleMobileNav()">
      <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M4 7h16M4 12h16M4 17h16"/>
      </svg>
    </button>
  </div>
</header>

<nav id="mobile-nav" class="mobile-nav">
  <a href="#flow">How it works</a>
  <a href="#api">API</a>
  <a href="#checkout">Checkout</a>
  <a href="#features">Features</a>
  <div class="mobile-menu-actions">
    <a class="mobile-register" href="register.php">Register</a>
    <a class="mobile-login" href="login.php">Login</a>
  </div>
</nav>
<div id="mobile-overlay" class="mobile-overlay" onclick="closeMobileNav()"></div>

<main>

<!-- ================= HERO ================= -->
<section class="hero">
  <div class="shell hero-grid">
    <div>
      <div class="hero-eyebrow-wrap">
        <div class="eyebrow">
          <svg class="hero-fampay-mark" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
          </svg>
          Automated UPI Verification
        </div>
      </div>
      <div class="hero-h1-wrap">
        <h1 style="font-size:clamp(40px,5vw,68px);">Know when <em>UPI payments</em> arrive.</h1>
      </div>
      <div class="hero-copy-wrap">
        <p class="hero-copy">
          FamGateway reads your FamApp payment emails via IMAP, extracts the UTR and amount, stores everything in Firebase, and triggers webhooks to your server — automatically.
        </p>
      </div>
      <div class="hero-actions-wrap">
        <div class="hero-actions">
          <a class="primary" href="register.php">Create an account <span>→</span></a>
          <a class="secondary" href="#api">Read the API <span>↗</span></a>
        </div>
      </div>
      <div class="hero-proof-wrap">
        <div class="proof">
          <span><b>✓</b> Email-based verification</span>
          <span><b>✓</b> Firebase storage</span>
          <span><b>✓</b> Instant webhooks</span>
        </div>
      </div>
    </div>

    <div class="hero-visual">
      <div class="hero-card-wrap">
        <div class="product">
          <div class="product-head">
            <div class="dots"><i></i><i></i><i></i></div>
            <span>checkout</span>
            <span>LIVE</span>
          </div>
          <div class="product-main">
            <div class="gateway-brand">
              <div class="gateway-mark">
                <svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
              </div>
              <div>
                <b>FamGateway</b>
                <small>UPI Verification</small>
              </div>
            </div>
            <div class="product-label">Order total</div>
            <div class="money">₹499 <small>INR</small></div>
            <div class="pay-card">
              <div class="pay-row"><span>Order ID</span><b>order_8H2KQ</b></div>
              <div class="pay-row"><span>QR</span><b>Generated</b></div>
              <div class="pay-row">
                <span>Verification</span>
                <span class="status"><i></i>Payment received</span>
              </div>
            </div>
            <div class="flow">
              <div>QR created</div><span>→</span>
              <div>Pay with UPI</div><span>→</span>
              <div class="active">Confirmed</div>
            </div>
          </div>
          <div class="hero-note">Verified from email ✓</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= HOW IT WORKS ================= -->
<section id="flow" class="section">
  <div class="shell">
    <div class="flow-intro">
      <div class="anim-slide-left">
        <div class="kicker">How it works</div>
        <h2 style="font-size:clamp(30px,4vw,42px);">Three steps from email to webhook.</h2>
        <p class="section-intro">
          Connect Gmail once. Generate a QR. When the payment email arrives, FamGateway parses it and tells your server.
        </p>
      </div>
      <div class="api-flow-card anim-slide-right">
        <b>YOUR APP → FAMGATEWAY</b>
        <div class="api-flow-line">
          <div class="api-flow-node">API request<small>UPI + amount</small></div>
          <span class="api-flow-arrow">→</span>
          <div class="api-flow-node">QR + order<small>status: pending</small></div>
          <span class="api-flow-arrow">→</span>
          <div class="api-flow-node">Webhook<small>UTR received</small></div>
        </div>
        <div class="api-flow-result">{ "status": "success", "utr": "4023xxxx" }</div>
      </div>
    </div>

    <div class="steps anim-stagger">
      <article class="step">
        <div class="step-no">01 / CONNECT</div>
        <h3>Connect Gmail.</h3>
        <p>Add your FamApp Gmail credentials. FamGateway reads incoming payment emails via IMAP.</p>
      </article>
      <article class="step">
        <div class="step-no">02 / CREATE</div>
        <h3>Generate a QR.</h3>
        <p>Send a UPI ID and amount. Get back an order ID and a QR URL instantly.</p>
      </article>
      <article class="step">
        <div class="step-no">03 / VERIFY</div>
        <h3>Receive a webhook.</h3>
        <p>When the payment email arrives, we parse the UTR and POST it to your webhook URL.</p>
      </article>
    </div>
  </div>
</section>

<!-- ================= API ================= -->
<section id="api" class="section section-dark anim-fade-up">
  <div class="shell integration">
    <div class="anim-slide-left">
      <div class="kicker">API</div>
      <h2>Two endpoints. Clean JSON.</h2>
      <p class="section-intro">Create a payment, check its status. Every request is authenticated with your API key.</p>
      <div class="code">
        <div class="code-top"><span>create.sh</span><span>copy</span></div>
        <pre><span class="dim"># create a payment link</span>
<span class="green">GET</span> /api/qr.php
<span class="orange">upi</span>: "username@fam"
<span class="orange">amount</span>: 499
<span class="orange">api_key</span>: "gk_xxxxxxxx"

<span class="dim"># response</span>
{ "order_id": "...", "qr_url": "...", "status": "pending" }</pre>
      </div>
    </div>

    <div class="feature-list anim-stagger">
      <div class="feature">
        <span class="feature-num" style="color:#60a5fa;font-weight:800;">01</span>
        <div><h3>QR generation</h3><p>Create a UPI QR for any amount.</p></div>
        <span class="arrow">↗</span>
      </div>
      <div class="feature">
        <span class="feature-num" style="color:#60a5fa;font-weight:800;">02</span>
        <div><h3>Status check</h3><p>Check if an order is pending, paid, or expired.</p></div>
        <span class="arrow">↗</span>
      </div>
      <div class="feature">
        <span class="feature-num" style="color:#60a5fa;font-weight:800;">03</span>
        <div><h3>Webhook dispatch</h3><p>Automatically POST to your URL when a payment is verified.</p></div>
        <span class="arrow">↗</span>
      </div>
      <div class="feature">
        <span class="feature-num" style="color:#60a5fa;font-weight:800;">04</span>
        <div><h3>Duplicate prevention</h3><p>Processed emails are tracked, so each payment is credited once.</p></div>
        <span class="arrow">↗</span>
      </div>
    </div>
  </div>
</section>

<!-- ================= CHECKOUT PREVIEW ================= -->
<section id="checkout" class="section">
  <div class="shell">
    <div class="checkout-showcase">
      <div class="checkout-side anim-slide-left">
        <div>
          <div class="kicker">Checkout</div>
          <h2>Hosted checkout page included.</h2>
          <p class="section-intro">Share a link. Show the amount, UPI destination, QR, and live status — all on one page.</p>
        </div>
        <div class="checkout-card">
          <h3>What the user sees</h3>
          <p>Amount, UPI ID, QR, and waiting state at once. No extra steps, no hidden screens.</p>
        </div>
        <div class="checkout-metrics">
          <div class="checkout-metric"><b>Amount</b><span>₹100</span></div>
          <div class="checkout-metric"><b>Status</b><span>Waiting</span></div>
        </div>
      </div>

      <!-- CSS-only mockup (no external image needed) -->
      <div class="checkout-panel">
        <div style="width:100%; max-width:340px; background:#fff; border-radius:20px; padding:32px 24px; text-align:center; border:1px solid #e2e8f0;">
          <div style="width:52px;height:52px;margin:0 auto 16px;display:grid;place-items:center;border-radius:50%;background:#dbeafe;color:#2563eb;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
              <rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
            </svg>
          </div>
          <div style="display:inline-block;border:1px solid #bbf7d0;border-radius:99px;padding:4px 10px;color:#16a34a;background:#f0fdf4;font-size:10px;font-weight:700;letter-spacing:.5px;">WAITING FOR PAYMENT</div>
          <div style="display:block;margin:18px 0 6px;font-size:13px;color:#64748b;font-weight:600;">Amount to pay</div>
          <div style="font-size:32px;font-weight:800;letter-spacing:-1px;color:#0f172a;">₹100</div>
          <div style="margin-top:20px;padding:12px;background:#f8fafc;border-radius:10px;font-size:12px;color:#475569;">
            UPI: <b>username@fam</b>
          </div>
          <div style="margin-top:14px;font-size:10px;color:#94a3b8;letter-spacing:.7px;font-weight:700;">ORDER 8H2KQ · EXPIRES IN 10:00</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= FEATURES (only real) ================= -->
<section id="features" class="section section-dark">
  <div class="shell">
    <div style="text-align:center; max-width:600px; margin:0 auto 60px;">
      <div class="kicker" style="margin-bottom:12px; display:inline-block;">What FamGateway does</div>
      <h2 style="font-size:clamp(32px,5vw,48px); margin-bottom:20px; letter-spacing:-1px;">Only the features that actually work.</h2>
      <p class="section-intro" style="font-size:17px; line-height:1.6; margin:0 auto;">No fake metrics. No buzzwords. Every feature below is running in production.</p>
    </div>

    <div class="anim-stagger" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:24px;">
      <div style="background:var(--ink-2); padding:28px; border-radius:20px; border:1px solid rgba(255,255,255,.1);">
        <div style="width:44px;height:44px;background:rgba(59,130,246,.15);color:#60a5fa;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <h3 style="font-size:17px;color:#fff;margin:0 0 8px;font-weight:700;">Gmail IMAP reading</h3>
        <p style="color:#afc0b4;font-size:14px;line-height:1.6;margin:0;">Reads FamApp payment confirmation emails from your Gmail account.</p>
      </div>

      <div style="background:var(--ink-2); padding:28px; border-radius:20px; border:1px solid rgba(255,255,255,.1);">
        <div style="width:44px;height:44px;background:rgba(34,197,94,.15);color:#4ade80;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/></svg>
        </div>
        <h3 style="font-size:17px;color:#fff;margin:0 0 8px;font-weight:700;">Email parsing</h3>
        <p style="color:#afc0b4;font-size:14px;line-height:1.6;margin:0;">Extracts amount, UTR, and sender name from FamApp emails using regex.</p>
      </div>

      <div style="background:var(--ink-2); padding:28px; border-radius:20px; border:1px solid rgba(255,255,255,.1);">
        <div style="width:44px;height:44px;background:rgba(244,63,94,.15);color:#fb7185;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
        </div>
        <h3 style="font-size:17px;color:#fff;margin:0 0 8px;font-weight:700;">Firebase storage</h3>
        <p style="color:#afc0b4;font-size:14px;line-height:1.6;margin:0;">Every verified payment is written to Firebase Realtime Database.</p>
      </div>

      <div style="background:var(--ink-2); padding:28px; border-radius:20px; border:1px solid rgba(255,255,255,.1);">
        <div style="width:44px;height:44px;background:rgba(168,85,247,.15);color:#c084fc;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
        </div>
        <h3 style="font-size:17px;color:#fff;margin:0 0 8px;font-weight:700;">Duplicate prevention</h3>
        <p style="color:#afc0b4;font-size:14px;line-height:1.6;margin:0;">Processed email IDs are tracked in Firebase, so each payment is credited once.</p>
      </div>

      <div style="background:var(--ink-2); padding:28px; border-radius:20px; border:1px solid rgba(255,255,255,.1);">
        <div style="width:44px;height:44px;background:rgba(59,130,246,.15);color:#60a5fa;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <h3 style="font-size:17px;color:#fff;margin:0 0 8px;font-weight:700;">Webhook callbacks</h3>
        <p style="color:#afc0b4;font-size:14px;line-height:1.6;margin:0;">POSTs to your server the moment a payment is verified, with the UTR.</p>
      </div>

      <div style="background:var(--ink-2); padding:28px; border-radius:20px; border:1px solid rgba(255,255,255,.1);">
        <div style="width:44px;height:44px;background:rgba(234,179,8,.15);color:#facc15;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <h3 style="font-size:17px;color:#fff;margin:0 0 8px;font-weight:700;">AES-256 encryption</h3>
        <p style="color:#afc0b4;font-size:14px;line-height:1.6;margin:0;">Gmail app passwords are encrypted at rest using AES-256-CBC.</p>
      </div>

      <div style="background:var(--ink-2); padding:28px; border-radius:20px; border:1px solid rgba(255,255,255,.1);">
        <div style="width:44px;height:44px;background:rgba(20,184,166,.15);color:#2dd4bf;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <h3 style="font-size:17px;color:#fff;margin:0 0 8px;font-weight:700;">Background worker</h3>
        <p style="color:#afc0b4;font-size:14px;line-height:1.6;margin:0;">Runs on cron, checks emails, writes to Firebase, and fires webhooks.</p>
      </div>

      <div style="background:var(--ink-2); padding:28px; border-radius:20px; border:1px solid rgba(255,255,255,.1);">
        <div style="width:44px;height:44px;background:rgba(59,130,246,.15);color:#60a5fa;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
        </div>
        <h3 style="font-size:17px;color:#fff;margin:0 0 8px;font-weight:700;">API key auth</h3>
        <p style="color:#afc0b4;font-size:14px;line-height:1.6;margin:0;">Every request is authenticated with a per-user API key.</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= CTA ================= -->
<section class="closing">
  <div class="shell">
    <div class="closing-grid anim-fade-up">
      <div>
        <h2>Start accepting UPI payments.</h2>
        <p>Create an account, connect Gmail, and get your first webhook in minutes. No KYC, no monthly fees.</p>
      </div>
      <a class="primary" href="register.php">Create an account <span>→</span></a>
    </div>
  </div>
</section>

</main>

<!-- ================= FOOTER ================= -->
<footer>
  <div class="shell">
    <div class="foot-grid">
      <div class="foot-brand">
        <a class="brand" href="index.html" style="color:#fff;font-size:15px;">
          <span class="brand-mark">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          </span>
          FamGateway
        </a>
        <p>Automated UPI payment verification for developers. Reads FamApp emails, verifies UTRs, fires webhooks.</p>
      </div>
      <div class="foot-col">
        <b>Product</b>
        <a href="#flow">How it works</a>
        <a href="#api">API</a>
        <a href="#features">Features</a>
      </div>
      <div class="foot-col">
        <b>Account</b>
        <a href="register.php">Register</a>
        <a href="login.php">Sign in</a>
      </div>
      <div class="foot-col">
        <b>Legal</b>
        <a href="terms.html">Terms</a>
        <a href="privacy.html">Privacy</a>
      </div>
    </div>
    <div class="legal">
      <span>© 2025 FamGateway</span>
      <span>Built for developers.</span>
    </div>
  </div>
</footer>

<!-- ================= Back to top ================= -->
<a href="#" class="back-to-top" id="backToTop" aria-label="Back to top">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
</a>

<script>
  // ===== Mobile nav =====
  function toggleMobileNav() {
    const nav = document.getElementById('mobile-nav');
    const overlay = document.getElementById('mobile-overlay');
    const open = nav.classList.toggle('open');
    overlay.classList.toggle('open', open);
    document.body.style.overflow = open ? 'hidden' : '';
  }
  function closeMobileNav() {
    document.getElementById('mobile-nav').classList.remove('open');
    document.getElementById('mobile-overlay').classList.remove('open');
    document.body.style.overflow = '';
  }
  document.querySelectorAll('#mobile-nav a').forEach(a => a.addEventListener('click', closeMobileNav));

  // ===== Sticky nav =====
  const navEl = document.querySelector('.nav');
  const onScroll = () => navEl.classList.toggle('nav-sticky', window.scrollY > 40);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // ===== Back to top =====
  const backBtn = document.getElementById('backToTop');
  window.addEventListener('scroll', () => backBtn.classList.toggle('visible', window.scrollY > 400), { passive: true });
  backBtn.addEventListener('click', e => { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); });

  // ===== Scroll animations =====
  const animObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        animObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
  document.querySelectorAll('.anim-fade-up, .anim-fade-in, .anim-slide-left, .anim-slide-right, .anim-stagger').forEach(el => animObserver.observe(el));
</script>

</body>
</html>
