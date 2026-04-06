<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 — Page Not Found</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="bg-canvas"></div>
<div id="cursor-dot"></div>
<div id="cursor-ring"></div>
<div class="page-404">
  <div class="error-code">404</div>
  <h1 class="error-title">Page Not Found</h1>
  <p class="error-desc">The page you're looking for doesn't exist or has been moved.<br>Let's get you back on track.</p>
  <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
    <a href="/" class="btn btn-primary"><i class="fas fa-home"></i> Go Home</a>
    <a href="javascript:history.back()" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Go Back</a>
  </div>
  <div style="margin-top:3rem;font-size:5rem;opacity:0.1;animation:spin 8s linear infinite">⚙️</div>
</div>
<script src="/assets/js/main.js"></script>
</body>
</html>
