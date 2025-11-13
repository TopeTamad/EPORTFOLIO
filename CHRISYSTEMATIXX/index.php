<?php
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/inc/auth.php';
$p = get_profile();
$skills = get_skills();
$projects = get_projects();
$certs = get_certificates();
$css_ver = @filemtime(__DIR__ . '/assets/css/style.css') ?: time();
$js_ver = @filemtime(__DIR__ . '/assets/js/main.js') ?: time();
$contact_ok = null;
$contact_msg = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['contact_submit'])) {
  $honeypot = trim($_POST['website'] ?? '');
  $token = $_POST['csrf_token'] ?? '';
  if ($honeypot !== '') {
    $contact_ok = false; $contact_msg = 'Spam detected.';
  } elseif (!verify_csrf($token)) {
    $contact_ok = false; $contact_msg = 'Invalid form token. Please reload the page and try again.';
  } else {
    [$ok, $msg] = save_contact_message($_POST['name'] ?? '', $_POST['email'] ?? '', $_POST['message'] ?? '');
    $contact_ok = $ok;
    $contact_msg = $msg;
    regenerate_csrf();
  }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CHRISYSTEMATIXX — <?php echo htmlspecialchars($p['full_name'] ?? 'Portfolio'); ?></title>
  <meta name="description" content="Professional e-Portfolio of <?php echo htmlspecialchars($p['full_name'] ?? ''); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <script>
    (function(){
      try {
        var saved = localStorage.getItem('theme');
        var light = window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches;
        var mode = (saved === 'light' || saved === 'dark') ? saved : (light ? 'light' : 'dark');
        var root = document.documentElement;
        root.classList.remove('theme-light','theme-dark');
        if (mode === 'light') root.classList.add('theme-light'); else root.classList.add('theme-dark');
      } catch(e) {}
    })();
  </script>
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo (int)$css_ver; ?>">
</head>
  <body>
  <div class="bg-ambient" aria-hidden="true"></div>
  <canvas id="code-canvas" aria-hidden="true"></canvas>
  <header class="navbar">
    <div class="container navbar-inner">
      <div class="brand">CHRI<span>SYSTEMATIXX</span></div>
      <nav class="nav">
        <a href="#about">About</a>
        <a href="#skills">Skills</a>
        <a href="#projects">Projects</a>
        <a href="#certificates">Certificates</a>
        <a href="#contact">Contact</a>
      </nav>
      <button id="theme-toggle" class="button secondary" type="button" aria-label="Toggle theme"></button>
    </div>
  </header>

  <main class="container">
    <section class="hero" id="about">
      <div>
        <div class="badge">Purpose. Precision. Progress.</div>
        <h1 class="title">Hi, I'm <span id="typed-name" class="gradient-text" data-text="<?php echo htmlspecialchars($p['full_name'] ?? ''); ?>"></span>.</h1>
        <p class="subtitle"><?php echo htmlspecialchars($p['headline'] ?? ''); ?></p>
        <p style="margin:0 0 20px; color:#c7cfdb;">I build with clarity and craft—with systems that feel elegant and work flawlessly. Clean code. Human-centered design. Relentless iteration.</p>
        <div class="cta">
          <a class="button" href="#projects">View Projects</a>
          <a class="button secondary" href="#contact">Contact Me</a>
        </div>
      </div>
      <div style="text-align:center;">
        <div class="avatar-circle">
          <img src="<?php echo htmlspecialchars($p['avatar'] ?: 'https://i.pravatar.cc/420?u=chris'); ?>" alt="Profile">
        </div>
        <div style="margin-top:14px; color:#9aa4b2;">
          <?php echo htmlspecialchars($p['location'] ?? ''); ?> · <a href="mailto:<?php echo htmlspecialchars($p['email'] ?? ''); ?>" title="Just message me." style="color:#22d3ee; text-decoration:none;"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2zm0 2l8 5 8-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>Email</a>
        </div>
      </div>
    </section>

    <section class="section" id="skills">
      <h2>Skills</h2>
      <p class="lead">Fluent in foundations. Growing every day. Focused on building resilient, production-ready web solutions.</p>
      <div class="card pad">
        <div class="grid cols-2" style="gap:18px;">
          <?php foreach ($skills as $s): ?>
            <div class="skill">
              <div style="opacity:.9;"><?php echo htmlspecialchars($s['name']); ?></div>
              <div class="bar"><span data-pct="<?php echo (int)$s['level']; ?>"></span></div>
              <div class="lvl"><?php echo (int)$s['level']; ?>%</div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section" id="projects">
      <h2>Projects</h2>
      <p class="lead">Selected works crafted with care—where practicality meets polish.</p>
      <div class="grid cols-3">
        <?php foreach ($projects as $pr): ?>
          <div class="card pad project">
            <img src="<?php echo htmlspecialchars($pr['image_url'] ?: 'https://picsum.photos/seed/p/800/600'); ?>" alt="<?php echo htmlspecialchars($pr['title']); ?>">
            <div class="meta">
              <h4><?php echo htmlspecialchars($pr['title']); ?></h4>
              <p><?php echo htmlspecialchars($pr['description']); ?></p>
              <?php if (!empty($pr['project_url'])): ?>
                <div style="margin-top:8px;"><a href="<?php echo htmlspecialchars($pr['project_url']); ?>" target="_blank" rel="noopener" style="color:#22d3ee; text-decoration:none;">View project</a></div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section" id="certificates">
      <h2>Certificates</h2>
      <p class="lead">Milestones that mark my learning journey—evidence of consistent growth.</p>
      <div class="card pad certs">
        <div class="grid" style="gap:16px;">
          <?php foreach ($certs as $c): ?>
            <div class="item">
              <img src="<?php echo htmlspecialchars($c['image_url'] ?: 'https://picsum.photos/seed/c/400/300'); ?>" alt="<?php echo htmlspecialchars($c['title']); ?>">
              <div>
                <h4><?php echo htmlspecialchars($c['title']); ?></h4>
                <p><?php echo htmlspecialchars(($c['issuer'] ?? '') . (!empty($c['issue_date']) ? ' • ' . date('M Y', strtotime($c['issue_date'])) : '')); ?></p>
                <?php if (!empty($c['credential_url'])): ?>
                  <div style="margin-top:6px;"><a href="<?php echo htmlspecialchars($c['credential_url']); ?>" target="_blank" rel="noopener" style="color:#22d3ee; text-decoration:none;">View credential</a></div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section" id="contact">
      <h2>Contact</h2>
      <p class="lead">Open to collaboration, internships, and junior developer roles.</p>
      <div class="card pad">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
          <div>
            <div style="font-weight:600; margin-bottom:8px;">Send a message</div>
            <?php if ($contact_ok !== null): ?>
              <div style="margin:10px 0; padding:10px 12px; border-radius:10px; border:1px solid var(--border); background: <?php echo $contact_ok ? '#0e1f17' : '#2a1111'; ?>; color: <?php echo $contact_ok ? '#34d399' : '#fda4af'; ?>;">
                <?php echo htmlspecialchars($contact_msg); ?>
              </div>
            <?php endif; ?>
            <form method="post" action="#contact" style="display:grid; gap:12px;">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
              <input type="text" name="website" value="" style="display:none !important" tabindex="-1" autocomplete="off">
              <input type="text" name="name" placeholder="Your name" required style="background:#0b1222; border:1px solid var(--border); color:var(--text); padding:12px 14px; border-radius:10px;">
              <input type="email" name="email" placeholder="Your email" required style="background:#0b1222; border:1px solid var(--border); color:var(--text); padding:12px 14px; border-radius:10px;">
              <textarea name="message" placeholder="Your message" rows="5" required style="background:#0b1222; border:1px solid var(--border); color:var(--text); padding:12px 14px; border-radius:10px; resize:vertical;"></textarea>
              <button class="button" type="submit" name="contact_submit" value="1">Send</button>
            </form>
          </div>
          <div>
            <div style="font-weight:600;">Email</div>
            <div><a href="mailto:<?php echo htmlspecialchars($p['email'] ?? ''); ?>" title="Just message me." style="color:#22d3ee; text-decoration:none;"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2zm0 2l8 5 8-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><?php echo htmlspecialchars($p['email'] ?? ''); ?></a></div>
            <div style="height:12px;"></div>
            <div style="font-weight:600;">Location</div>
            <div style="color:#9aa4b2;"><?php echo htmlspecialchars($p['location'] ?? ''); ?></div>
            <?php if (!empty($p['facebook_url']) || !empty($p['instagram_url'])): ?>
            <div style="height:12px;"></div>
            <div style="font-weight:600;">Social</div>
            <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
              <?php if (!empty($p['facebook_url'])): ?><a href="<?php echo htmlspecialchars($p['facebook_url']); ?>" title="Just message me." target="_blank" rel="noopener" style="color:#22d3ee; text-decoration:none; display:inline-flex; align-items:center; gap:6px;"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7h-2.4V12h2.4V9.8c0-2.4 1.4-3.7 3.6-3.7c1 0 2 .2 2 .2v2.3h-1.1c-1.1 0-1.5 .7-1.5 1.4V12h2.6l-.4 2.9h-2.2v7A10 10 0 0 0 22 12" fill="currentColor"/></svg>Facebook</a><?php endif; ?>
              <?php if (!empty($p['instagram_url'])): ?><a href="<?php echo htmlspecialchars($p['instagram_url']); ?>" title="Just message me." target="_blank" rel="noopener" style="color:#22d3ee; text-decoration:none; display:inline-flex; align-items:center; gap:6px;"><svg class="ico" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3.5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor"/></svg>Instagram</a><?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="container">© <?php echo date('Y'); ?> CHRISYSTEMATIXX • Built with PHP and care</div>
  </footer>

  <script type="application/ld+json">
  <?php
    $siteUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . dirname($_SERVER['REQUEST_URI'] ?? '/EPORT/CHRISYSTEMATIXX/') . '/';
    $sameAs = [];
    if (!empty($p['facebook_url'])) { $sameAs[] = $p['facebook_url']; }
    if (!empty($p['instagram_url'])) { $sameAs[] = $p['instagram_url']; }
    $projectsLd = [];
    foreach ($projects as $pr) {
      $projectsLd[] = [
        '@type' => 'CreativeWork',
        'name' => (string)$pr['title'],
        'description' => (string)$pr['description'],
        'url' => !empty($pr['project_url']) ? (string)$pr['project_url'] : $siteUrl.'#projects',
        'image' => !empty($pr['image_url']) ? $siteUrl . ltrim((string)$pr['image_url'], '/') : null,
      ];
    }
    $jsonLd = [
      '@context' => 'https://schema.org',
      '@graph' => [
        [
          '@type' => 'Person',
          'name' => (string)($p['full_name'] ?? 'CHRISYSTEMATIXX'),
          'jobTitle' => (string)($p['headline'] ?? ''),
          'url' => $siteUrl,
          'email' => !empty($p['email']) ? 'mailto:'.$p['email'] : null,
          'image' => !empty($p['avatar']) ? $siteUrl . ltrim((string)$p['avatar'], '/') : null,
          'address' => !empty($p['location']) ? (string)$p['location'] : null,
          'sameAs' => $sameAs,
        ],
        [
          '@type' => 'ItemList',
          'name' => 'Projects',
          'itemListElement' => array_map(function($i, $proj){
            return [
              '@type' => 'ListItem',
              'position' => $i+1,
              'item' => $proj,
            ];
          }, array_keys($projectsLd), $projectsLd)
        ]
      ]
    ];
    echo json_encode($jsonLd, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
  ?>
  </script>
  <script src="assets/js/main.js?v=<?php echo (int)$js_ver; ?>"></script>
</body>
</html>
