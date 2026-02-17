<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Voiles & Company | Accounting Firm in Knoxville, TN</title>
  <meta name="description" content="Voiles & Company is an accounting firm in Knoxville, TN providing tax preparation, financial statements, and bookkeeping services for businesses and individuals." />
  <meta name="theme-color" content="#0b2a3a" />
  <style>
    :root{
      --bg: #071a24;
      --panel: rgba(255,255,255,.06);
      --panel-2: rgba(255,255,255,.09);
      --text: rgba(255,255,255,.92);
      --muted: rgba(255,255,255,.72);
      --muted2: rgba(255,255,255,.6);
      --line: rgba(255,255,255,.12);
      --brand: #41d3ff;
      --brand2: #79ffa8;
      --shadow: 0 20px 60px rgba(0,0,0,.35);
      --radius: 18px;
      --radius2: 26px;
      --max: 1120px;
    }

    *{ box-sizing:border-box; }
    html,body{ min-height:100%; }
    body{
      margin:0;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      background:
        radial-gradient(1200px 800px at 20% -10%, rgba(65,211,255,.22), transparent 55%),
        radial-gradient(900px 700px at 100% 10%, rgba(121,255,168,.16), transparent 50%),
        radial-gradient(900px 700px at 40% 110%, rgba(65,211,255,.12), transparent 60%),
        var(--bg);
      color:var(--text);
      line-height:1.55;
      letter-spacing:.2px;
    }

    a{ color:inherit; text-decoration:none; }
    .wrap{ max-width:var(--max); margin:0 auto; padding: 0 20px; }

    /* Header */
    header{
      position: sticky;
      top: 0;
      z-index: 50;
      backdrop-filter: blur(10px);
      background: linear-gradient(to bottom, rgba(7,26,36,.85), rgba(7,26,36,.55));
      border-bottom: 1px solid rgba(255,255,255,.08);
    }
    .nav{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
      padding:14px 0;
    }
    .brand{
      display:flex;
      align-items:center;
      gap:12px;
      min-width: 240px;
    }
    .logo{
      width:40px;height:40px;border-radius:14px;
      background:
        radial-gradient(circle at 30% 30%, rgba(65,211,255,.95), rgba(65,211,255,.25) 60%, transparent 65%),
        radial-gradient(circle at 70% 70%, rgba(121,255,168,.8), rgba(121,255,168,.2) 60%, transparent 66%),
        rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.16);
      box-shadow: 0 14px 35px rgba(0,0,0,.25);
    }
    .brand strong{ display:block; font-size: 15px; letter-spacing:.3px; }
    .brand span{ display:block; font-size: 12px; color: var(--muted); margin-top:1px; }

    nav ul{
      list-style:none;
      display:flex;
      align-items:center;
      gap:18px;
      padding:0;
      margin:0;
    }
    nav a{
      font-size: 16px;
      color: var(--muted);
      padding:10px 10px;
      border-radius: 12px;
      transition: .2s ease;
    }
    nav a:hover{ color: var(--text); background: rgba(255,255,255,.06); }

    .cta{
      display:flex;
      align-items:center;
      gap:10px;
      white-space:nowrap;
    }
    .btn{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:10px;
      padding:10px 14px;
      border-radius: 14px;
      border:1px solid rgba(255,255,255,.14);
      background: rgba(255,255,255,.06);
      color: var(--text);
      font-weight: 600;
      font-size: 16px;
      transition: .2s ease;
      cursor: pointer;
    }
    .btn:hover{ transform: translateY(-1px); background: rgba(255,255,255,.09); }
    .btn.primary{
      background: linear-gradient(135deg, rgba(65,211,255,.35), rgba(121,255,168,.25));
      border-color: rgba(65,211,255,.35);
    }
    .btn.primary:hover{ background: linear-gradient(135deg, rgba(65,211,255,.45), rgba(121,255,168,.32)); }

    .hamburger{
      display:none;
      width:42px;height:42px;border-radius:14px;
      border:1px solid rgba(255,255,255,.14);
      background: rgba(255,255,255,.06);
      color: var(--text);
      cursor:pointer;
    }

    /* Hero */
    .hero{ padding: 64px 0 18px; }
    .hero-grid{
      display:grid;
      grid-template-columns: 1.25fr .95fr;
      gap: 24px;
      align-items: start;
    }
    .kicker{
      display:inline-flex;
      align-items:center;
      gap:10px;
      padding: 8px 12px;
      border:1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.05);
      border-radius: 999px;
      color: var(--muted);
      font-size: 12px;
    }
    .dot{
      width:9px;height:9px;border-radius:999px;
      background: var(--brand);
      box-shadow: 0 0 0 5px rgba(65,211,255,.14);
    }
    h1{
      margin: 16px 0 10px;
      font-size: clamp(34px, 4.2vw, 56px);
      line-height: 1.04;
      letter-spacing: -0.6px;
    }
    .subhead{
      margin: 0 0 18px;
      color: var(--muted);
      font-size: 16px;
      max-width: 60ch;
    }
    .hero-actions{
      display:flex;
      flex-wrap:wrap;
      gap:12px;
      margin: 18px 0 16px;
    }
    .mini{
      display:flex;
      flex-wrap:wrap;
      gap:12px;
      margin-top: 14px;
      color: var(--muted2);
      font-size: 12px;
    }
    .mini .pill{
      display:inline-flex;
      align-items:center;
      gap:8px;
      padding: 8px 10px;
      border-radius: 999px;
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.10);
    }

    /* Panels */
    .panel{
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.10);
      border-radius: var(--radius2);
      box-shadow: var(--shadow);
      overflow:hidden;
    }
    .panel-inner{ padding: 18px; }
    .panel h3{
      margin: 0 0 8px;
      font-size: 14px;
      letter-spacing: .3px;
    }
    .panel p{
      margin: 0 0 14px;
      color: var(--muted);
      font-size: 16px;
    }

    .quick{
      display:grid;
      gap:10px;
      margin-top: 10px;
    }
    .quick a{
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding: 12px 12px;
      border-radius: 16px;
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.10);
      transition:.2s ease;
      font-size: 16px;
      color: var(--text);
    }
    .quick a:hover{ background: rgba(255,255,255,.08); transform: translateY(-1px); }
    .arrow{ color: var(--muted); }

    /* Sections */
    section{ padding: 46px 0; }
    .section-title{
      display:flex;
      align-items:flex-end;
      justify-content:space-between;
      gap: 18px;
      margin-bottom: 18px;
    }
    .section-title h2{
      margin: 0;
      font-size: 22px;
      letter-spacing: -0.2px;
    }
    .section-title p{
      margin: 0;
      color: var(--muted);
      font-size: 16px;
      max-width: 62ch;
    }

    .cards{
      display:grid;
      grid-template-columns: repeat(12, 1fr);
      gap: 14px;
    }
    .card{
      grid-column: span 4;
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.10);
      border-radius: var(--radius);
      padding: 16px;
      transition: .2s ease;
    }
    .card:hover{ transform: translateY(-2px); background: rgba(255,255,255,.07); }
    .icon{
      width:42px;height:42px;border-radius: 16px;
      display:grid;place-items:center;
      background: rgba(65,211,255,.14);
      border: 1px solid rgba(65,211,255,.22);
      margin-bottom: 12px;
    }
    .card h3{ margin: 0 0 6px; font-size: 15px; }
    .card p{ margin: 0; color: var(--muted); font-size: 16px; }

    /* Process */
    .split{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      align-items: stretch;
    }
    .list{
      margin: 0;
      padding: 0;
      list-style:none;
      display:grid;
      gap: 10px;
    }
    .list li{
      display:flex;
      gap: 12px;
      padding: 12px 12px;
      border-radius: 16px;
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.10);
    }
    .num{
      width: 30px; height: 30px;
      border-radius: 12px;
      display:grid;place-items:center;
      background: rgba(121,255,168,.12);
      border: 1px solid rgba(121,255,168,.22);
      font-weight: 700;
      font-size: 12px;
      color: var(--text);
      flex: 0 0 auto;
    }
    .list strong{ display:block; font-size: 16px; }
    .list span{ display:block; font-size: 12px; color: var(--muted); margin-top:2px; }

    /* Testimonials */
    .quote{
      padding: 16px;
      border-radius: var(--radius);
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.10);
    }
    .quote p{ margin:0 0 10px; color: var(--text); font-size: 16px; }
    .quote .by{ color: var(--muted); font-size: 12px; }

    /* Contact */
    .contact{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      align-items:start;
    }
    form{ display:grid; gap: 10px; }
    label{ font-size: 12px; color: var(--muted); }
    input, textarea{
      width:100%;
      padding: 12px 12px;
      border-radius: 14px;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(0,0,0,.18);
      color: var(--text);
      outline: none;
      font-size: 16px;
    }
    textarea{ min-height: 120px; resize: vertical; }
    input:focus, textarea:focus{
      border-color: rgba(65,211,255,.45);
      box-shadow: 0 0 0 5px rgba(65,211,255,.10);
    }
    .fineprint{
      font-size: 12px;
      color: var(--muted2);
      line-height: 1.45;
      margin-top: 10px;
    }

    /* Footer */
    footer{
      padding: 26px 0 38px;
      border-top: 1px solid rgba(255,255,255,.10);
      color: var(--muted2);
      font-size: 12px;
    }
    .foot{
      display:flex;
      flex-wrap:wrap;
      gap: 12px;
      justify-content:space-between;
      align-items:center;
    }
    .foot a{ color: var(--muted); }
    .foot a:hover{ color: var(--text); }

    /* Mobile */
    @media (max-width: 980px){
      .hero-grid{ grid-template-columns: 1fr; }
      .cards .card{ grid-column: span 6; }
      .split{ grid-template-columns: 1fr; }
      .contact{ grid-template-columns: 1fr; }
      nav ul{ display:none; }
      .hamburger{ display:inline-grid; place-items:center; }
      .cta{ display:none; }
      header.open nav ul{
        display:flex;
        flex-direction:column;
        align-items:flex-start;
        gap: 0;
        position:absolute;
        left:0; right:0;
        top: 64px;
        margin: 0;
        padding: 10px 20px 16px;
        background: rgba(7,26,36,.94);
        border-bottom: 1px solid rgba(255,255,255,.10);
      }
      header.open nav a{ width: 100%; padding: 12px 10px; }
    }

    @media (max-width: 640px){
      .cards .card{ grid-column: span 12; }
      .brand{ min-width: auto; }
    }

    @media (prefers-reduced-motion: reduce){
      *{ scroll-behavior:auto !important; transition:none !important; }
    }
  </style>
</head>

<body>
  <header id="siteHeader">
    <div class="wrap">
      <div class="nav">
        <a class="brand" href="#top" aria-label="Voiles & Company home">
          <div class="logo" aria-hidden="true"></div>
          <div>
            <strong>Voiles &amp; Company</strong>
            <span>Knoxville, TN • Accounting • Tax</span>
          </div>
        </a>

        <nav aria-label="Primary navigation">
          <ul>
            <li><a href="#services">Services</a></li>
            <li><a href="#process">How We Work</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
        </nav>

        <div class="cta">
          <a class="btn" href="tel:+1-865-588-1757" aria-label="Call Voiles and Company">Call</a>
          <a class="btn primary" href="#contact">Request a Consultation</a>
        </div>

        <button class="hamburger" id="hamburger" aria-label="Open menu" aria-expanded="false">
          ☰
        </button>
      </div>
    </div>
  </header>

  <main id="top">
    <section class="hero">
      <div class="wrap">
        <div class="hero-grid">
          <div>
            <div class="kicker"><span class="dot" aria-hidden="true"></span> Accounting support for East Tennessee businesses and individuals</div>
            <h1>Reliable accounting.<br/>Straightforward guidance.</h1>
            <p class="subhead">
              Voiles &amp; Company is a Knoxville, Tennessee accounting firm providing tax preparation,
              financial statements, and bookkeeping services—built around accuracy, responsiveness, and clarity. We serve clients throughout East Tennessee.
            </p>

            <div class="hero-actions">
              <a class="btn primary" href="#contact">Request a Consultation</a>
              <a class="btn" href="#services">View Services</a>
            </div>

            <div class="mini" aria-label="Highlights">
              <div class="pill">✔ Tax preparation</div>
              <div class="pill">✔ Financial statements</div>
              <div class="pill">✔ Monthly bookkeeping</div>
              <div class="pill">✔ Knoxville, TN</div>
            </div>
          </div>

          <aside class="panel" aria-label="Quick actions">
            <div class="panel-inner">
              <h3>Need help quickly?</h3>
              <p>Send a message and we’ll get back to you with next steps.</p>

              <div class="quick">
                <a href="#contact"><span>New client inquiry</span><span class="arrow">→</span></a>
                <a href="#services"><span>Tax preparation</span><span class="arrow">→</span></a>
                <a href="#services"><span>Bookkeeping services</span><span class="arrow">→</span></a>
                <a href="#services"><span>Financial statements</span><span class="arrow">→</span></a>
              </div>

            </div>
          </aside>
        </div>
      </div>
    </section>

    <section id="services">
      <div class="wrap">
        <div class="section-title">
          <h2>Services</h2>
          <p>Focused accounting services designed for clarity and compliance—without unnecessary complexity.</p>
        </div>

        <div class="cards" role="list">
          <article class="card" role="listitem">
            <div class="icon" aria-hidden="true">🧾</div>
            <h3>Tax Preparation</h3>
            <p>Accurate filings for individuals and businesses, with clear guidance on what to provide and what to expect.</p>
          </article>

          <article class="card" role="listitem">
            <div class="icon" aria-hidden="true">📊</div>
            <h3>Financial Statements</h3>
            <p>Clean, readable statements that help you understand performance and support lending, reporting, or planning needs.</p>
          </article>

          <article class="card" role="listitem">
            <div class="icon" aria-hidden="true">📒</div>
            <h3>Bookkeeping Services</h3>
            <p>Monthly reconciliations and organized books so you’re not playing catch-up—especially at tax time.</p>
          </article>
        </div>
      </div>
    </section>

    <section id="process">
      <div class="wrap">
        <div class="section-title">
          <h2>How we work</h2>
          <p>A simple, professional process that respects your time.</p>
        </div>

        <div class="split">
          <div class="panel">
            <div class="panel-inner">
              <h3>Our process</h3>
              <ul class="list">
                <li><div class="num">1</div><div><strong>Reach out</strong><span>Send a message or call with what you need and your timeline.</span></div></li>
                <li><div class="num">2</div><div><strong>Get a clear plan</strong><span>We outline scope, required documents, and a realistic turnaround.</span></div></li>
                <li><div class="num">3</div><div><strong>We handle the details</strong><span>Accurate work, clear communication, and minimal back-and-forth.</span></div></li>
                <li><div class="num">4</div><div><strong>Ongoing support</strong><span>For bookkeeping clients, we keep things clean month to month.</span></div></li>
              </ul>
            </div>
          </div>

          <div class="panel">
            <div class="panel-inner">
              <h3>What to expect</h3>
              <ul class="list">
                <li><div class="num">✓</div><div><strong>Responsive communication</strong><span>Timely replies and clear next steps.</span></div></li>
                <li><div class="num">✓</div><div><strong>Clear deliverables</strong><span>You’ll know exactly what you’re getting and when.</span></div></li>
                <li><div class="num">✓</div><div><strong>Plain English</strong><span>We translate the numbers into what they mean.</span></div></li>
                <li><div class="num">✓</div><div><strong>Local + practical</strong><span>Knoxville-based support with a real-world approach.</span></div></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="about">
      <div class="wrap">
        <div class="section-title">
          <h2>About</h2>
          <p>Voiles &amp; Company serves Knoxville, TN and surrounding communities with professional accounting services.</p>
        </div>

        <div class="splitA">
          <div class="panel">
            <div class="panel-inner">
              <h3>Who we help</h3>
              <p>
                We work with businesses that want dependable bookkeeping and financial reporting, and individuals who want
                tax preparation handled accurately and efficiently.
              </p>
            </div>
          </div>

          <!--<div class="panel">
            <div class="panel-inner">
              <h3>Client notes</h3>
              <div class="quote">
                <p>“Clear communication, accurate work, and a smooth process from start to finish.”</p>
                <div class="by">— Client (placeholder)</div>
              </div>
              <div style="height:10px"></div>
              <div class="quote">
                <p>“Our books are finally organized—and tax time stopped being stressful.”</p>
                <div class="by">— Client (placeholder)</div>
              </div>
              <p class="fineprint">Swap these placeholders for real testimonials when available.</p>
            </div>
          </div>-->
        </div>
      </div>
    </section>

    <section id="contact">
      <div class="wrap">
        <div class="section-title">
          <h2>Contact</h2>
          <p>Send a message and we’ll coordinate a time to talk.</p>
        </div>

        <div class="contact">
          <div class="panel">
            <div class="panel-inner">
              <h3>Send a message</h3>
              <form onsubmit="return handleSubmit(event)">
                <div>
                  <label for="name">Name</label>
                  <input id="name" name="name" autocomplete="name" required />
                </div>
                <div>
                  <label for="email">Email</label>
                  <input id="email" name="email" type="email" autocomplete="email" required />
                </div>
                <div>
                  <label for="service">Service needed</label>
                  <input id="service" name="service" placeholder="Tax preparation, financial statements, bookkeeping..." />
                </div>
                <div>
                  <label for="message">Message</label>
                  <textarea id="message" name="message" placeholder="A few details helps us route you correctly."></textarea>
                </div>
                <button class="btn primary" type="submit">Send Inquiry</button>

              </form>
            </div>
          </div>

          <div class="panel">
            <div class="panel-inner">
              <h3>Firm details</h3>
              <p>
                <strong>Voiles &amp; Company</strong><br/>
                5401 Kingston Pike, Suite 280<br/>
                Knoxville, TN
              </p>

              <p>
                <strong>Phone:</strong> <a href="tel:+1-865-588-1757">(865) 588-1757</a><br/>
                <strong>Email:</strong> <a href="mailto:dvoiles@voilesco.com">dvoiles@voilesco.com</a><br/>
                <strong>Office:</strong> Knoxville, Tennessee
              </p>

              <div style="height:14px"></div>
              <a class="btn" href="#top">Back to top ↑</a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="wrap">
      <div class="foot">
        <div>© <span id="year"></span> Voiles &amp; Company. All rights reserved.</div>
        <div style="display:flex; gap:14px; flex-wrap:wrap;">
          <a href="#services">Services</a>
          <a href="#process">How We Work</a>
          <a href="#contact">Contact</a>
          <!--<a href="#" onclick="alert('Add your privacy policy URL here.'); return false;">Privacy</a>-->
        </div>
      </div>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    (function () {
      const header = document.getElementById('siteHeader');
      const btn = document.getElementById('hamburger');

      btn.addEventListener('click', () => {
        const open = header.classList.toggle('open');
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      });

      // Close menu on nav click (mobile)
      document.querySelectorAll('nav a').forEach(a => {
        a.addEventListener('click', () => {
          header.classList.remove('open');
          btn.setAttribute('aria-expanded', 'false');
        });
      });

      document.getElementById('year').textContent = new Date().getFullYear();
    })();

    // Demo form handler (front-end only)
    function handleSubmit(e){
      e.preventDefault();
      const note = document.getElementById('formNote');
      note.textContent = "Thanks! This is a demo form—hook it to your backend or form service to receive submissions.";
      note.style.color = "rgba(121,255,168,.9)";
      return false;
    }
  </script>
</body>
</html>
