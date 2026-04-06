<?php
/**
 * Admin Dashboard — Portfolio 2.0
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

startSecureSession();
requireAdmin();

$db = Database::getConnection();
$totalProjects = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$totalMessages = $db->query("SELECT COUNT(*) FROM messages")->fetchColumn();
$unreadMessages= $db->query("SELECT COUNT(*) FROM messages WHERE is_read=0")->fetchColumn();
$recentProjects= $db->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentMessages= $db->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

$adminUser = e($_SESSION['admin_username'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Dashboard — Admin Panel</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-canvas"></div>
<div class="admin-layout">
  <!-- Sidebar -->
  <?php include 'partials/sidebar.php'; ?>

  <!-- Main -->
  <main class="admin-main">
    <div class="admin-header">
      <div>
        <h1>Dashboard</h1>
        <p style="color:var(--text3);font-size:0.85rem">Welcome back, <?= $adminUser ?> 👋</p>
      </div>
      <a href="../index.php" class="btn btn-outline btn-sm" target="_blank">
        <i class="fas fa-external-link-alt"></i> View Site
      </a>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card blue">
        <div class="icon"><i class="fas fa-folder-open"></i></div>
        <div><div class="stat-val"><?= $totalProjects ?></div><div class="stat-lbl">Total Projects</div></div>
      </div>
      <div class="stat-card purple">
        <div class="icon"><i class="fas fa-envelope"></i></div>
        <div><div class="stat-val"><?= $totalMessages ?></div><div class="stat-lbl">Total Messages</div></div>
      </div>
      <div class="stat-card pink">
        <div class="icon"><i class="fas fa-bell"></i></div>
        <div><div class="stat-val"><?= $unreadMessages ?></div><div class="stat-lbl">Unread Messages</div></div>
      </div>
      <div class="stat-card green">
        <div class="icon"><i class="fas fa-check-circle"></i></div>
        <div><div class="stat-val"><?= $totalMessages - $unreadMessages ?></div><div class="stat-lbl">Read Messages</div></div>
      </div>
    </div>

    <!-- Recent Projects -->
    <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;margin-bottom:2rem;backdrop-filter:blur(8px)">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
        <h2 style="font-family:var(--font-head);font-size:1.1rem;font-weight:700">Recent Projects</h2>
        <a href="projects.php" class="btn btn-outline btn-sm">View All</a>
      </div>
      <?php if ($recentProjects): ?>
      <table class="admin-table">
        <thead><tr><th>Title</th><th>Category</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($recentProjects as $p): ?>
          <tr>
            <td><?= e($p['title']) ?></td>
            <td><span class="badge" style="background:rgba(79,143,255,0.1);color:var(--accent)"><?= e(strtoupper($p['category'])) ?></span></td>
            <td style="color:var(--text3);font-size:0.82rem"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
            <td>
              <a href="edit-project.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm"><i class="fas fa-edit"></i></a>
              <a href="delete-project.php?id=<?= $p['id'] ?>&csrf=<?= e(generateCSRF()) ?>" class="btn btn-danger btn-sm btn-delete-confirm" style="margin-left:0.4rem"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
      <p style="color:var(--text3);text-align:center;padding:2rem">No projects yet. <a href="add-project.php" style="color:var(--accent)">Add your first project →</a></p>
      <?php endif; ?>
    </div>

    <!-- Recent Messages -->
    <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;backdrop-filter:blur(8px)">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem">
        <h2 style="font-family:var(--font-head);font-size:1.1rem;font-weight:700">Recent Messages</h2>
        <a href="messages.php" class="btn btn-outline btn-sm">View All</a>
      </div>
      <?php if ($recentMessages): ?>
      <table class="admin-table">
        <thead><tr><th>Name</th><th>Email</th><th>Preview</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($recentMessages as $m): ?>
          <tr>
            <td><?= e($m['name']) ?></td>
            <td style="color:var(--text3);font-size:0.85rem"><?= e($m['email']) ?></td>
            <td style="color:var(--text2);font-size:0.85rem"><?= e(substr($m['message'], 0, 60)) ?>…</td>
            <td><span class="badge <?= $m['is_read'] ? 'badge-read' : 'badge-unread' ?>"><?= $m['is_read'] ? 'Read' : 'New' ?></span></td>
            <td style="color:var(--text3);font-size:0.82rem"><?= date('d M', strtotime($m['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
      <p style="color:var(--text3);text-align:center;padding:2rem">No messages yet.</p>
      <?php endif; ?>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
