# 🚀 Portfolio 2.0 — Full Stack PHP Portfolio

A modern, premium, fully responsive portfolio website built with PHP, MySQL, HTML5, Advanced CSS3, and JavaScript (ES6+).

---

## ✨ Features

| Feature | Details |
|---|---|
| Design | Luxury dark theme with glassmorphism, gradient animations |
| Responsive | Mobile · Tablet · Desktop |
| Dark/Light Mode | Persistent toggle with localStorage |
| Animations | Custom cursor, preloader, typed text, scroll reveal, skill bars |
| Projects | CRUD with image carousel, category filter, skeleton loader |
| Admin Panel | Session auth, CSRF, PDO prepared statements |
| Security | XSS protection, SQL injection prevention, secure uploads |
| SEO | Dynamic meta tags, semantic HTML5, robots/sitemap ready |

---

## 📁 Folder Structure

```
/portfolio
├── /assets
│   ├── /css          → style.css (all styles)
│   └── /js           → main.js, admin.js
├── /uploads          → user-uploaded project images
├── /admin            → protected admin panel
│   ├── /partials     → sidebar.php
│   ├── index.php     → dashboard
│   ├── login.php     → admin login
│   ├── logout.php
│   ├── projects.php  → list all projects
│   ├── add-project.php
│   ├── edit-project.php
│   ├── delete-project.php
│   ├── messages.php
│   └── .htaccess
├── /config
│   ├── database.php  → PDO singleton
│   ├── security.php  → helpers & security functions
│   └── setup.sql     → raw SQL (manual setup)
├── index.php         → portfolio homepage
├── contact.php       → AJAX contact form handler
├── install.php       → one-time DB installer
├── 404.php           → custom 404 page
├── .htaccess         → routing, security headers, gzip
└── README.md
```

---

## ⚡ Quick Setup (5 minutes)

### Requirements
- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Apache with mod_rewrite enabled (or Nginx)
- mod_rewrite, mod_headers, mod_expires recommended

### Step 1 — Upload Files
Upload the entire `/portfolio` folder to your web server's public root (e.g., `public_html/` or `www/`).

### Step 2 — Configure Database
Open `config/database.php` and set your credentials:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio2.0');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
```

### Step 3 — Run Installer
Visit: `http://yourdomain.com/install.php`

This will:
- Create the `portfolio2.0` database
- Create all 4 tables (admin, projects, project_images, messages)
- Insert a default admin account
- Insert sample projects
- Create the uploads directory

### Step 4 — Login to Admin
URL: `http://yourdomain.com/admin/login.php`
- **Username:** admin
- **Password:** Admin@1234

> ⚠️ Change the password immediately after first login!

### Step 5 — Clean Up
After successful installation:
1. Delete `install.php` from your server
2. Remove the lock file `.installed` if you ever need to reinstall

---

## 🔒 Security Checklist

- [x] PDO prepared statements (SQL injection prevention)
- [x] `htmlspecialchars()` on all output (XSS prevention)
- [x] CSRF tokens on all forms
- [x] `password_hash()` / `password_verify()` for admin passwords
- [x] Session-based authentication with 30-min timeout
- [x] `session_regenerate_id()` on login
- [x] File type validation via `finfo` MIME check (not just extension)
- [x] Max file size: 5MB per image
- [x] PHP execution blocked in `/uploads/` via `.htaccess`
- [x] Directory listing disabled (`Options -Indexes`)
- [x] Security headers: X-Frame-Options, X-XSS-Protection, etc.
- [x] Rate limiting on contact form (session-based, 1 msg/min)

---

## 🎨 Customisation

### Personal Info
Edit `index.php`:
- Change `Mukesh Majhi` to your name
- Update typed words in the `data-words` attribute
- Replace hero description, social links, contact info
- Update the Google Maps embed URL (search your city on maps.google.com → Share → Embed)

### Profile Image
Replace the `hero-img-placeholder` div with:
```html
<img src="assets/images/profile.jpg" alt="Your Name" class="hero-img">
```

### Skills
Edit the skill arrays in `index.php` (search for `$front`, `$back`, `$db_skills`).

### Colors
Edit CSS variables at the top of `assets/css/style.css`:
```css
:root {
  --accent:  #4f8fff;   /* Primary blue */
  --accent2: #7b5ef8;   /* Purple */
  --accent3: #f04f8f;   /* Pink */
}
```

### Admin Password
In MySQL:
```sql
UPDATE admin SET password = '$2y$12$...' WHERE username = 'admin';
```
Generate hash in PHP:
```php
echo password_hash('YourNewPassword', PASSWORD_BCRYPT, ['cost' => 12]);
```

---

## 🗄 Database Schema

```sql
admin          (id, username, password, created_at)
projects       (id, title, description, technologies, category, live_url, github_url, created_at)
project_images (id, project_id → projects.id CASCADE, image_path)
messages       (id, name, email, message, is_read, created_at)
```

---

## 🤝 Credits

- **Fonts:** Google Fonts (Syne, DM Sans, Clash Display)
- **Icons:** Font Awesome 6
- **Maps:** Google Maps Embed API
- **Design & Code:** Portfolio 2.0 by Mukesh Majhi

---

## 📄 License

MIT — free to use, modify, and distribute.
