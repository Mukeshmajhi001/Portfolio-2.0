<?php
/**
 * Portfolio 2.0 — Main Page
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

startSecureSession();
$db = Database::getConnection();

// Fetch projects
$projects = $db->query("SELECT p.*, GROUP_CONCAT(pi.image_path ORDER BY pi.id SEPARATOR '|') AS images
    FROM projects p LEFT JOIN project_images pi ON pi.project_id = p.id
    GROUP BY p.id ORDER BY p.created_at DESC")->fetchAll();

// CSRF token
$csrf = generateCSRF();

// SEO
$siteTitle = "Mukesh Majhi — Full Stack Developer";
$siteDesc  = "Creative full stack developer crafting modern web experiences with PHP, JavaScript, and cutting-edge technologies.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?= e($siteDesc) ?>">
<meta name="keywords" content="Full Stack Developer, PHP, JavaScript, Portfolio, Web Developer">
<meta name="robots" content="index, follow">
<meta property="og:title"       content="<?= e($siteTitle) ?>">
<meta property="og:description" content="<?= e($siteDesc) ?>">
<meta property="og:type"        content="website">
<title><?= e($siteTitle) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Custom Cursor -->
<div id="cursor-dot"></div>
<div id="cursor-ring"></div>

<!-- Page Transition -->
<div class="page-transition" id="page-transition"></div>

<!-- Preloader -->
<div id="preloader">
  <div class="preloader-logo">Mks-75.</div>
  <div class="preloader-bar"><div class="preloader-fill"></div></div>
</div>

<!-- Animated Background -->
<div class="bg-canvas"></div>

<!-- Navbar -->
<nav id="navbar">
  <div class="nav-inner">
    <a class="nav-logo" href="#home">Mks-75.</a>
    <div class="nav-links">
      <a href="#home">Home</a>
      <a href="#projects">Projects</a>
      <a href="#skills">Skills</a>
      <a href="#about">About</a>
      <a href="#contact">Contact</a>
    </div>
    <div class="nav-actions">
      <button id="theme-toggle" aria-label="Toggle dark/light mode">
        <span class="toggle-thumb">🌙</span>
      </button>
      <button class="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

<!-- Mobile Nav -->
<div class="nav-mobile">
  <a href="#home">Home</a>
  <a href="#projects">Projects</a>
  <a href="#skills">Skills</a>
  <a href="#about">About</a>
  <a href="#contact">Contact</a>
</div>

<div id="app">

<!-- ══════════════ HOME ══════════════ -->
<div id="home" style="min-height:100vh; display:flex; align-items:center;">
  <div class="hero-inner">
    <div class="hero-content">
      <div class="hero-badge reveal">Available for hire</div>
      <h1 class="hero-name reveal delay-1">
        <span class="first">Hey, I'm<br></span>
        <span class="last">Mukesh Majhi</span>
      </h1>
      <p class="hero-title reveal delay-2">
        <span id="typed-text" data-words='["Full Stack Developer","PHP Expert","JavaScript Ninja","UI/UX Enthusiast","Open Source Contributor"]'></span>
      </p>
      <p class="hero-desc reveal delay-3">
        I craft fast, beautiful, and scalable web applications. Passionate about clean code,
        creative design, and bringing ideas to life on the web.
      </p>
      <div class="hero-actions reveal delay-4">
        <a href="resume.pdf" class="btn btn-primary" download>
          <i class="fas fa-download"></i> Download CV
        </a>
        <a href="#contact" class="btn btn-outline">
          <i class="fas fa-paper-plane"></i> Hire Me
        </a>
      </div>
      <div class="hero-socials reveal delay-4">
        <a href="https://github.com" target="_blank" rel="noopener" class="social-link"><i class="fab fa-github"></i></a>
        <a href="https://linkedin.com" target="_blank" rel="noopener" class="social-link"><i class="fab fa-linkedin-in"></i></a>
        <a href="https://instagram.com" target="_blank" rel="noopener" class="social-link"><i class="fab fa-instagram"></i></a>
        <a href="https://twitter.com" target="_blank" rel="noopener" class="social-link"><i class="fab fa-twitter"></i></a>
      </div>
    </div>
    <div class="hero-img-wrap reveal right">
      <div class="hero-img-ring">
        
        <img src="assets/images/profile.jpg" alt="Mukesh Majhi" class="hero-img">
      </div>
    </div>
  </div>
</div>

<!-- ══════════════ PROJECTS ══════════════ -->
<section id="projects">
  <div class="section-header">
    <div class="section-tag">Portfolio</div>
    <h2 class="section-title reveal">My <span>Projects</span></h2>
    <div class="section-line"></div>
  </div>

  <!-- Filter -->
  <div class="filter-tabs reveal">
    <button class="filter-btn active" data-filter="all">All</button>
    <button class="filter-btn" data-filter="php">PHP</button>
    <button class="filter-btn" data-filter="js">JavaScript</button>
    <button class="filter-btn" data-filter="css">CSS</button>
    <button class="filter-btn" data-filter="fullstack">Full Stack</button>
  </div>

  <!-- Skeleton Loader -->
  <div class="projects-grid" id="projects-grid">
    <?php if (empty($projects)): ?>
    <!-- Skeleton placeholders -->
    <?php for ($s = 0; $s < 3; $s++): ?>
    <div class="skeleton-card">
      <div class="skeleton sk-img"></div>
      <div class="sk-body">
        <div class="skeleton sk-line w-40"></div>
        <div class="skeleton sk-line w-80 mt-1"></div>
        <div class="skeleton sk-line w-60 mt-1"></div>
        <div class="skeleton sk-line w-80 mt-1"></div>
      </div>
    </div>
    <?php endfor; ?>

    <!-- Demo project cards (remove when DB has data) -->
    <?php
    $demos = [
      ['title'=>'E-Commerce Platform','desc'=>'Full-featured online store with cart, payments, and admin dashboard.','tech'=>'PHP,MySQL,JS,CSS','cat'=>'fullstack','live'=>'','git'=>'','img'=>''],
      ['title'=>'Task Manager App','desc'=>'Kanban-style project management tool with drag-and-drop and real-time updates.','tech'=>'JavaScript,CSS,PHP','cat'=>'js','live'=>'','git'=>'','img'=>''],
      ['title'=>'Portfolio Generator','desc'=>'Dynamic portfolio builder with custom themes and one-click export.','tech'=>'PHP,MySQL,CSS','cat'=>'php','live'=>'','git'=>'','img'=>''],
    ];
    foreach ($demos as $d):
    ?>
    <div class="project-card" data-category="<?= e($d['cat']) ?>">
      <div class="project-carousel">
        <div class="carousel-track">
          <div class="carousel-slide">
            <div class="img-placeholder"><i class="fas fa-code"></i></div>
          </div>
        </div>
      </div>
      <div class="project-body">
        <div class="project-category"><?= e(strtoupper($d['cat'])) ?></div>
        <h3 class="project-title"><?= e($d['title']) ?></h3>
        <p class="project-desc"><?= e($d['desc']) ?></p>
        <div class="project-tech">
          <?php foreach (explode(',', $d['tech']) as $t): ?>
          <span class="tech-tag"><?= e(trim($t)) ?></span>
          <?php endforeach; ?>
        </div>
        <div class="project-links">
          <?php if ($d['live']): ?>
          <a href="<?= e($d['live']) ?>" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
            <i class="fas fa-external-link-alt"></i> Live Demo
          </a>
          <?php endif; ?>
          <?php if ($d['git']): ?>
          <a href="<?= e($d['git']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
            <i class="fab fa-github"></i> GitHub
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>

    <?php else: ?>
    <!-- Real DB projects -->
    <?php foreach ($projects as $p):
      $images = $p['images'] ? explode('|', $p['images']) : [];
    ?>
    <div class="project-card reveal" data-category="<?= e($p['category']) ?>">
      <div class="project-carousel">
        <div class="carousel-track">
          <?php if ($images): foreach ($images as $img): ?>
          <div class="carousel-slide">
            <img src="uploads/<?= e($img) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
          </div>
          <?php endforeach; else: ?>
          <div class="carousel-slide">
            <div class="img-placeholder"><i class="fas fa-code"></i></div>
          </div>
          <?php endif; ?>
        </div>
        <?php if (count($images) > 1): ?>
        <button class="carousel-btn carousel-prev"><i class="fas fa-chevron-left"></i></button>
        <button class="carousel-btn carousel-next"><i class="fas fa-chevron-right"></i></button>
        <div class="carousel-dots">
          <?php for ($i = 0; $i < count($images); $i++): ?>
          <div class="carousel-dot <?= $i === 0 ? 'active' : '' ?>"></div>
          <?php endfor; ?>
        </div>
        <?php endif; ?>
      </div>
      <div class="project-body">
        <div class="project-category"><?= e(strtoupper($p['category'])) ?></div>
        <h3 class="project-title"><?= e($p['title']) ?></h3>
        <p class="project-desc"><?= e(substr($p['description'], 0, 120)) ?>…</p>
        <div class="project-tech">
          <?php foreach (explode(',', $p['technologies']) as $t): ?>
          <span class="tech-tag"><?= e(trim($t)) ?></span>
          <?php endforeach; ?>
        </div>
        <div class="project-links">
          <?php if ($p['live_url']): ?>
          <a href="<?= e($p['live_url']) ?>" class="btn btn-primary btn-sm" target="_blank" rel="noopener">
            <i class="fas fa-external-link-alt"></i> Live Demo
          </a>
          <?php endif; ?>
          <?php if ($p['github_url']): ?>
          <a href="<?= e($p['github_url']) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">
            <i class="fab fa-github"></i> GitHub
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<!-- ══════════════ SKILLS ══════════════ -->
<section id="skills">
  <div class="section-header">
    <div class="section-tag">Expertise</div>
    <h2 class="section-title reveal">My <span>Skills</span></h2>
    <div class="section-line"></div>
  </div>

  <div class="skills-grid">
    <!-- Frontend -->
    <div class="skills-card frontend reveal left">
      <div class="skills-card-head">
        <div class="skills-icon frontend"><i class="fas fa-palette"></i></div>
        <h3 class="skills-cat">Frontend</h3>
      </div>
      <?php $front = [['HTML5',95,'🌐'],['CSS3 / Sass',90,'🎨'],['JavaScript ES6+',88,'⚡'],['React.js',80,'⚛️'],['Vue.js',72,'💚']]; ?>
      <?php foreach ($front as [$name,$pct,$icon]): ?>
      <div class="skill-item">
        <div class="skill-info">
          <span class="skill-name"><span class="skill-icon-sm"><?= $icon ?></span><?= $name ?></span>
          <span class="skill-pct"><?= $pct ?>%</span>
        </div>
        <div class="skill-bar"><div class="skill-fill" data-width="<?= $pct ?>"></div></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Backend -->
    <div class="skills-card backend reveal">
      <div class="skills-card-head">
        <div class="skills-icon backend"><i class="fas fa-server"></i></div>
        <h3 class="skills-cat">Backend</h3>
      </div>
      <?php $back = [['PHP 8+',92,'🐘'],['Laravel',85,'🔴'],['Node.js',78,'🟢'],['REST APIs',88,'🔌'],['Python',65,'🐍']]; ?>
      <?php foreach ($back as [$name,$pct,$icon]): ?>
      <div class="skill-item">
        <div class="skill-info">
          <span class="skill-name"><span class="skill-icon-sm"><?= $icon ?></span><?= $name ?></span>
          <span class="skill-pct"><?= $pct ?>%</span>
        </div>
        <div class="skill-bar"><div class="skill-fill" data-width="<?= $pct ?>"></div></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Database -->
    <div class="skills-card database reveal right">
      <div class="skills-card-head">
        <div class="skills-icon database"><i class="fas fa-database"></i></div>
        <h3 class="skills-cat">Database & Tools</h3>
      </div>
      <?php $db_skills = [['MySQL',90,'🗄️'],['PostgreSQL',75,'🐘'],['MongoDB',70,'🍃'],['Redis',65,'🔴'],['Git / GitHub',92,'🐙']]; ?>
      <?php foreach ($db_skills as [$name,$pct,$icon]): ?>
      <div class="skill-item">
        <div class="skill-info">
          <span class="skill-name"><span class="skill-icon-sm"><?= $icon ?></span><?= $name ?></span>
          <span class="skill-pct"><?= $pct ?>%</span>
        </div>
        <div class="skill-bar"><div class="skill-fill" data-width="<?= $pct ?>"></div></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Circular Progress -->
  <div class="circular-skills">
    <?php $circs = [['Problem Solving',94],['Team Work',88],['Creativity',90],['Communication',85],['Adaptability',92]];
    foreach ($circs as [$label,$pct]):
      $offset = 283 - (283 * $pct / 100);
    ?>
    <div class="circular-item reveal">
      <div class="circular-ring">
        <svg viewBox="0 0 100 100" width="100" height="100">
          <circle class="circle-bg" cx="50" cy="50" r="45"/>
          <circle class="circle-fill" cx="50" cy="50" r="45" data-pct="<?= $pct ?>" style="stroke-dashoffset:283"/>
        </svg>
        <div class="circle-num">0%</div>
      </div>
      <div class="circle-label"><?= e($label) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ══════════════ ABOUT ══════════════ -->
<section id="about">
  <div class="section-header">
    <div class="section-tag">Who I Am</div>
    <h2 class="section-title reveal">About <span>Me</span></h2>
    <div class="section-line"></div>
  </div>

  <div class="about-grid">
    <div class="about-img-wrap reveal left">
      <div class="about-img">Mks-75</div>
    </div>
    <div class="about-text">
      <h3 class="reveal">Passionate Developer &amp; Creative Thinker</h3>
      <p class="reveal delay-1">
        I'm Mukesh Majhi, a full stack developer with 5+ years of experience building scalable
        web applications. I specialize in PHP, JavaScript, and modern frameworks, with a deep
        passion for creating elegant solutions to complex problems.
      </p>
      <p class="reveal delay-2">
        When I'm not coding, you'll find me contributing to open source, writing tech articles,
        or exploring the latest in web technology. I believe in continuous learning and pushing
        the boundaries of what's possible on the web.
      </p>

      <div class="about-stats reveal delay-3">
        <div class="stat-box"><div class="stat-num">5+</div><div class="stat-label">Years Experience</div></div>
        <div class="stat-box"><div class="stat-num">80+</div><div class="stat-label">Projects Done</div></div>
        <div class="stat-box"><div class="stat-num">50+</div><div class="stat-label">Happy Clients</div></div>
      </div>

      <!-- Education Timeline -->
      <h3 class="reveal" style="margin-bottom:1.5rem">Education &amp; Experience</h3>
      <div class="timeline reveal">
        <div class="timeline-item">
          <div class="timeline-date">2024 — Present</div>
          <div class="timeline-title">Senior Full Stack Developer</div>
          <div class="timeline-sub">TechCorp Solutions, Remote</div>
          <p class="timeline-desc">Leading development of enterprise-scale web applications serving 100k+ users.</p>
        </div>
        <div class="timeline-item">
          <div class="timeline-date">2021 — 2024</div>
          <div class="timeline-title">Full Stack Developer</div>
          <div class="timeline-sub">Digital Agency Co.</div>
          <p class="timeline-desc">Built 30+ client websites and web apps using PHP, Laravel, and React.</p>
        </div>
        <div class="timeline-item">
          <div class="timeline-date">2019 — 2021</div>
          <div class="timeline-title">B.Tech in Computer Science</div>
          <div class="timeline-sub">State University of Technology</div>
          <p class="timeline-desc">Graduated with First Class Honours. Specialized in Web Technologies.</p>
        </div>
        <div class="timeline-item">
          <div class="timeline-date">2017 — 2019</div>
          <div class="timeline-title">Junior Web Developer</div>
          <div class="timeline-sub">Startup Studio (Internship)</div>
          <p class="timeline-desc">Kickstarted career building responsive websites and REST APIs.</p>
        </div>
      </div>

      <!-- Achievements -->
      <h3 class="reveal mt-3" style="margin-top:2rem; margin-bottom:1rem">Achievements</h3>
      <div class="achievements reveal">
        <div class="achievement-item">
          <div class="ach-icon">🏆</div>
          <div><div class="ach-title">Best Developer Award 2023</div><div class="ach-desc">Awarded by TechCorp for exceptional performance</div></div>
        </div>
        <div class="achievement-item">
          <div class="ach-icon">🌟</div>
          <div><div class="ach-title">Open Source Contributor</div><div class="ach-desc">500+ GitHub stars across projects</div></div>
        </div>
        <div class="achievement-item">
          <div class="ach-icon">📝</div>
          <div><div class="ach-title">Tech Blogger</div><div class="ach-desc">10k+ monthly readers on dev.to</div></div>
        </div>
        <div class="achievement-item">
          <div class="ach-icon">🎓</div>
          <div><div class="ach-title">AWS Certified</div><div class="ach-desc">AWS Solutions Architect Associate</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════ CONTACT ══════════════ -->
<section id="contact">
  <div class="section-header">
    <div class="section-tag">Get In Touch</div>
    <h2 class="section-title reveal">Contact <span>Me</span></h2>
    <div class="section-line"></div>
  </div>

  <div class="contact-grid">
    <div class="contact-info reveal left">
      <h3>Let's Work Together</h3>
      <p>Have a project in mind or want to discuss opportunities? I'd love to hear from you. Drop me a message and I'll get back to you within 24 hours.</p>
      <div class="contact-items">
        <div class="contact-item">
          <div class="ci-icon"><i class="fas fa-envelope"></i></div>
          <div><div class="ci-label">Email</div><div class="ci-val">hello@alexsharma.dev</div></div>
        </div>
        <div class="contact-item">
          <div class="ci-icon"><i class="fas fa-phone"></i></div>
          <div><div class="ci-label">Phone</div><div class="ci-val">+1 (555) 123-4567</div></div>
        </div>
        <div class="contact-item">
          <div class="ci-icon"><i class="fas fa-map-marker-alt"></i></div>
          <div><div class="ci-label">Location</div><div class="ci-val">New York, USA</div></div>
        </div>
      </div>
      <div class="hero-socials">
        <a href="https://github.com" target="_blank" rel="noopener" class="social-link"><i class="fab fa-github"></i></a>
        <a href="https://linkedin.com" target="_blank" rel="noopener" class="social-link"><i class="fab fa-linkedin-in"></i></a>
        <a href="https://instagram.com" target="_blank" rel="noopener" class="social-link"><i class="fab fa-instagram"></i></a>
      </div>
      <div class="map-embed">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.25280949758!2d-74.11976373946228!3d40.697403441901016!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY!5e0!3m2!1sen!2sus!4v1703000000000!5m2!1sen!2sus"
          style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </div>

    <div class="contact-form-wrap reveal right">
      <div class="form-feedback"></div>
      <form id="contact-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <div class="form-group">
          <label class="form-label" for="name">Your Name *</label>
          <input class="form-control" type="text" id="name" name="name" placeholder="John Doe" autocomplete="name">
          <div class="field-error" id="name-error"></div>
        </div>
        <div class="form-group">
          <label class="form-label" for="email">Email Address *</label>
          <input class="form-control" type="email" id="email" name="email" placeholder="john@example.com" autocomplete="email">
          <div class="field-error" id="email-error"></div>
        </div>
        <div class="form-group">
          <label class="form-label" for="message">Message *</label>
          <textarea class="form-control" id="message" name="message" rows="5" placeholder="Tell me about your project…"></textarea>
          <div class="field-error" id="message-error"></div>
        </div>
        <button type="submit" class="btn btn-primary w-full" style="justify-content:center;">
          <i class="fas fa-paper-plane"></i> Send Message
        </button>
      </form>
    </div>
  </div>
</section>

</div><!-- #app -->

<!-- Footer -->
<footer>
  <p>Designed &amp; Developed with <span>❤</span> by <span>Mukesh Majhi</span> &copy; <?= date('Y') ?></p>
</footer>

<!-- Scroll to Top -->
<button id="scroll-top" aria-label="Scroll to top"><i class="fas fa-arrow-up"></i></button>

<script src="assets/js/main.js"></script>
</body>
</html>
