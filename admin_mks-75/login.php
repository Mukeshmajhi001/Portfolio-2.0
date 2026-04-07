<?php
/**
 * Admin Login — Portfolio 2.0 — Mukesh Majhi
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

startSecureSession();

if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$csrf  = generateCSRF();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid. Please refresh.';
    } else {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username && $password) {
            try {
                $db   = Database::getConnection();
                $stmt = $db->prepare("SELECT id, password FROM admin WHERE username = :u LIMIT 1");
                $stmt->execute([':u' => $username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_id']       = $admin['id'];
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['last_activity']  = time();
                    header('Location: index.php');
                    exit;
                } else {
                    sleep(1); // Slow brute-force
                    $error = 'Invalid username or password.';
                }
            } catch (PDOException $e) {
                $error = 'Database error. Please try again.';
            }
        } else {
            $error = 'Please fill in all fields.';
        }
    }
    unset($_SESSION['csrf_token']);
    $csrf = generateCSRF();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin Login — Portfolio 2.0 — Mukesh Majhi</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-canvas"></div>
<div class="login-page">
  <div class="login-card">
    <h1>Admin Panel</h1>
    <p>Sign in to manage your portfolio</p>
    <?php if ($error): ?>
    <div class="form-feedback error-msg" style="display:block"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
      <div class="form-group">
        <label class="form-label" for="username">Username</label>
        <input class="form-control" type="text" id="username" name="username"
          placeholder="Enter username" autocomplete="username" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input class="form-control" type="password" id="password" name="password"
          placeholder="Enter password" autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn btn-primary w-full" style="justify-content:center;margin-top:0.5rem">
        <i class="fas fa-sign-in-alt"></i> Login
      </button>
    </form>
    <p style="margin-top:1.5rem; font-size:0.78rem; color:var(--text3); text-align:center">
      Default: admin / Admin@1234
    </p>
    <div style="text-align:center; margin-top:1rem">
      <a href="../index.php" style="font-size:0.82rem; color:var(--accent)">← Back to Portfolio</a>
    </div>
  </div>
</div>
</body>
</html>
