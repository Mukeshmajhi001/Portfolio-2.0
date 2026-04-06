/**
 * Portfolio 2.0 — Main JS (ES6+)
 * Features: Cursor, Preloader, Navbar, Typed, Reveal, Carousel, Skills, Filter, Form, Scroll-top
 */

'use strict';

/* ── Custom Cursor ── */
const initCursor = () => {
  const dot  = document.getElementById('cursor-dot');
  const ring = document.getElementById('cursor-ring');
  if (!dot || !ring || window.matchMedia('(pointer: coarse)').matches) return;

  let mx = 0, my = 0, rx = 0, ry = 0;

  document.addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; });

  const loop = () => {
    rx += (mx - rx) * 0.12;
    ry += (my - ry) * 0.12;
    dot.style.left  = mx + 'px';
    dot.style.top   = my + 'px';
    ring.style.left = rx + 'px';
    ring.style.top  = ry + 'px';
    requestAnimationFrame(loop);
  };
  loop();

  document.querySelectorAll('a, button, .filter-btn, .carousel-btn, .carousel-dot').forEach(el => {
    el.addEventListener('mouseenter', () => document.body.classList.add('cursor-hover'));
    el.addEventListener('mouseleave', () => document.body.classList.remove('cursor-hover'));
  });
};

/* ── Preloader ── */
const initPreloader = () => {
  const pre = document.getElementById('preloader');
  if (!pre) return;
  window.addEventListener('load', () => {
    setTimeout(() => pre.classList.add('hide'), 1800);
  });
};

/* ── Navbar ── */
const initNavbar = () => {
  const nav   = document.getElementById('navbar');
  const ham   = document.querySelector('.hamburger');
  const mob   = document.querySelector('.nav-mobile');
  const links = document.querySelectorAll('.nav-links a, .nav-mobile a');
  if (!nav) return;

  // Scroll shadow
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 60);
    highlightActive();
  });

  // Hamburger
  ham?.addEventListener('click', () => {
    ham.classList.toggle('open');
    mob?.classList.toggle('open');
  });

  // Close mobile nav on link click
  mob?.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
      ham?.classList.remove('open');
      mob.classList.remove('open');
    });
  });

  // Active link highlight
  const sections = document.querySelectorAll('section[id]');
  const highlightActive = () => {
    const scrollPos = window.scrollY + 80;
    sections.forEach(sec => {
      const top = sec.offsetTop;
      const bot = top + sec.offsetHeight;
      const id  = sec.getAttribute('id');
      links.forEach(a => {
        if (a.getAttribute('href') === `#${id}`) {
          a.classList.toggle('active', scrollPos >= top && scrollPos < bot);
        }
      });
    });
  };

  // Smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
    });
  });
};

/* ── Theme Toggle ── */
const initTheme = () => {
  const btn   = document.getElementById('theme-toggle');
  const thumb = btn?.querySelector('.toggle-thumb');
  const saved = localStorage.getItem('theme');
  if (saved === 'light') document.body.classList.add('light-mode');

  const setIcon = () => {
    if (thumb) thumb.textContent = document.body.classList.contains('light-mode') ? '☀️' : '🌙';
  };
  setIcon();

  btn?.addEventListener('click', () => {
    document.body.classList.toggle('light-mode');
    localStorage.setItem('theme', document.body.classList.contains('light-mode') ? 'light' : 'dark');
    setIcon();
  });
};

/* ── Typed Animation ── */
const initTyped = () => {
  const el = document.getElementById('typed-text');
  if (!el) return;
  const words  = el.dataset.words ? JSON.parse(el.dataset.words) : ['Full Stack Developer'];
  let wi = 0, ci = 0, deleting = false;
  const cursor = document.createElement('span');
  cursor.className = 'typed-cursor';
  cursor.textContent = '|';
  el.after(cursor);

  const type = () => {
    const word = words[wi];
    if (!deleting) {
      el.textContent = word.substring(0, ci + 1);
      ci++;
      if (ci === word.length) { deleting = true; setTimeout(type, 1800); return; }
    } else {
      el.textContent = word.substring(0, ci - 1);
      ci--;
      if (ci === 0) { deleting = false; wi = (wi + 1) % words.length; }
    }
    setTimeout(type, deleting ? 60 : 110);
  };
  type();
};

/* ── Scroll Reveal ── */
const initReveal = () => {
  const els = document.querySelectorAll('.reveal');
  if (!els.length) return;
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); }
    });
  }, { threshold: 0.12 });
  els.forEach(el => obs.observe(el));
};

/* ── Skills Animation ── */
const initSkills = () => {
  const bars   = document.querySelectorAll('.skill-fill[data-width]');
  const circles = document.querySelectorAll('.circle-fill[data-pct]');
  if (!bars.length && !circles.length) return;

  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      // Bars
      bars.forEach(b => { b.style.width = b.dataset.width + '%'; });
      // Circles: circumference = 2πr = 283 for r=45
      circles.forEach(c => {
        const pct = parseFloat(c.dataset.pct);
        const offset = 283 - (283 * pct / 100);
        c.style.strokeDashoffset = offset;
        // Number counter
        const numEl = c.closest('.circular-ring')?.querySelector('.circle-num');
        if (numEl) {
          let cur = 0;
          const step = () => {
            cur = Math.min(cur + 2, pct);
            numEl.textContent = Math.round(cur) + '%';
            if (cur < pct) requestAnimationFrame(step);
          };
          step();
        }
      });
      obs.disconnect();
    });
  }, { threshold: 0.3 });

  const section = document.getElementById('skills');
  if (section) obs.observe(section);
};

/* ── Project Carousel ── */
const initCarousels = () => {
  document.querySelectorAll('.project-carousel').forEach(car => {
    const track = car.querySelector('.carousel-track');
    const slides = car.querySelectorAll('.carousel-slide');
    const dots   = car.querySelectorAll('.carousel-dot');
    const prev   = car.querySelector('.carousel-prev');
    const next   = car.querySelector('.carousel-next');
    if (!track || slides.length < 2) { prev?.remove(); next?.remove(); return; }

    let cur = 0;
    const go = (i) => {
      cur = (i + slides.length) % slides.length;
      track.style.transform = `translateX(-${cur * 100}%)`;
      dots.forEach((d, j) => d.classList.toggle('active', j === cur));
    };

    prev?.addEventListener('click', () => go(cur - 1));
    next?.addEventListener('click', () => go(cur + 1));
    dots.forEach((d, i) => d.addEventListener('click', () => go(i)));
    go(0);

    // Auto-play
    let interval = setInterval(() => go(cur + 1), 3500);
    car.addEventListener('mouseenter', () => clearInterval(interval));
    car.addEventListener('mouseleave', () => { interval = setInterval(() => go(cur + 1), 3500); });
  });
};

/* ── Project Filter ── */
const initFilter = () => {
  const btns = document.querySelectorAll('.filter-btn[data-filter]');
  const cards = document.querySelectorAll('.project-card[data-category]');
  if (!btns.length) return;

  btns.forEach(btn => {
    btn.addEventListener('click', () => {
      btns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const filter = btn.dataset.filter;
      cards.forEach(card => {
        const show = filter === 'all' || card.dataset.category === filter;
        card.style.transition = 'opacity 0.3s, transform 0.3s';
        if (show) {
          card.style.opacity = '1'; card.style.transform = 'scale(1)'; card.style.display = '';
        } else {
          card.style.opacity = '0'; card.style.transform = 'scale(0.95)';
          setTimeout(() => { if (card.style.opacity === '0') card.style.display = 'none'; }, 300);
        }
      });
    });
  });
};

/* ── Contact Form Validation ── */
const initContactForm = () => {
  const form = document.getElementById('contact-form');
  if (!form) return;

  const showError = (id, msg) => {
    const el = document.getElementById(id);
    if (el) { el.textContent = msg; el.style.display = msg ? 'block' : 'none'; }
    const input = document.getElementById(id.replace('-error', ''));
    if (input) input.classList.toggle('error', !!msg);
  };

  const validate = () => {
    let ok = true;
    const name  = form.querySelector('[name="name"]').value.trim();
    const email = form.querySelector('[name="email"]').value.trim();
    const msg   = form.querySelector('[name="message"]').value.trim();
    const emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!name || name.length < 2)         { showError('name-error', 'Please enter your name (min 2 chars)'); ok = false; }
    else showError('name-error', '');
    if (!email || !emailRx.test(email))   { showError('email-error', 'Please enter a valid email address'); ok = false; }
    else showError('email-error', '');
    if (!msg || msg.length < 10)          { showError('message-error', 'Message too short (min 10 chars)'); ok = false; }
    else showError('message-error', '');
    return ok;
  };

  form.addEventListener('submit', async e => {
    e.preventDefault();
    if (!validate()) return;

    const btn      = form.querySelector('[type="submit"]');
    const feedback = form.querySelector('.form-feedback');
    btn.disabled   = true;
    btn.textContent = 'Sending…';

    try {
      const res  = await fetch('contact.php', { method: 'POST', body: new FormData(form) });
      const data = await res.json();
      feedback.className = 'form-feedback ' + (data.success ? 'success' : 'error-msg');
      feedback.textContent = data.message;
      if (data.success) form.reset();
    } catch {
      feedback.className = 'form-feedback error-msg';
      feedback.textContent = 'Server error. Please try again.';
    } finally {
      btn.disabled = false;
      btn.textContent = 'Send Message';
    }
  });
};

/* ── Scroll to Top ── */
const initScrollTop = () => {
  const btn = document.getElementById('scroll-top');
  if (!btn) return;
  window.addEventListener('scroll', () => btn.classList.toggle('show', window.scrollY > 500));
  btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
};

/* ── Loading Skeleton → Fade Cards ── */
const initSkeletonLoader = () => {
  const grid = document.getElementById('projects-grid');
  if (!grid) return;
  const skeletons = grid.querySelectorAll('.skeleton-card');
  // Skeletons visible by default; actual cards hidden until loaded
  // Called by PHP after render
  window.showProjects = () => {
    skeletons.forEach(s => s.remove());
    grid.querySelectorAll('.project-card').forEach((c, i) => {
      c.style.opacity = '0';
      c.style.transform = 'translateY(20px)';
      setTimeout(() => {
        c.style.transition = 'opacity 0.5s, transform 0.5s';
        c.style.opacity = '1';
        c.style.transform = 'none';
      }, i * 80);
    });
  };
};

/* ── Init ── */
document.addEventListener('DOMContentLoaded', () => {
  initCursor();
  initPreloader();
  initNavbar();
  initTheme();
  initTyped();
  initReveal();
  initSkills();
  initCarousels();
  initFilter();
  initContactForm();
  initScrollTop();
  initSkeletonLoader();
  if (window.showProjects) window.showProjects();
});
