<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
startSecureSession();
requireAdmin();

$db = Database::getConnection();
$projects = $db->query(
    "SELECT p.*, COUNT(pi.id) as img_count
     FROM projects p LEFT JOIN project_images pi ON pi.project_id = p.id
     GROUP BY p.id ORDER BY p.created_at DESC"
)->fetchAll();

$added   = isset($_GET['added']);
$deleted = isset($_GET['deleted']);
$csrf    = generateCSRF();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Projects — Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-canvas"></div>
<div class="admin-layout">
  <?php include 'partials/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-header">
      <h1>All Projects <span style="color:var(--text3);font-size:0.9rem;font-weight:400">(<?= count($projects) ?>)</span></h1>
      <a href="add-project.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Project</a>
    </div>
    <?php if ($added): ?>
    <div class="form-feedback success alert-auto" style="display:block">✅ Project added successfully!</div>
    <?php endif; ?>
    <?php if ($deleted): ?>
    <div class="form-feedback success alert-auto" style="display:block">🗑️ Project deleted.</div>
    <?php endif; ?>

    <?php if ($projects): ?>
    <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;backdrop-filter:blur(8px)">
      <table class="admin-table">
        <thead><tr><th>#</th><th>Title</th><th>Category</th><th>Technologies</th><th>Images</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
          <?php foreach ($projects as $i => $p): ?>
          <tr>
            <td style="color:var(--text3)"><?= $i + 1 ?></td>
            <td><strong><?= e($p['title']) ?></strong></td>
            <td><span class="badge" style="background:rgba(79,143,255,0.1);color:var(--accent)"><?= e(strtoupper($p['category'])) ?></span></td>
            <td style="color:var(--text2);font-size:0.82rem;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($p['technologies']) ?></td>
            <td style="text-align:center"><span class="badge" style="background:rgba(123,94,248,0.1);color:var(--accent2)"><?= $p['img_count'] ?></span></td>
            <td style="color:var(--text3);font-size:0.82rem"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
            <td style="display:flex;gap:0.4rem;flex-wrap:wrap">
              <a href="edit-project.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm"><i class="fas fa-edit"></i> Edit</a>
              <a href="delete-project.php?id=<?= $p['id'] ?>&csrf=<?= e($csrf) ?>"
                 class="btn btn-danger btn-sm btn-delete-confirm"><i class="fas fa-trash"></i> Del</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:4rem;color:var(--text3)">
      <i class="fas fa-folder-open" style="font-size:3rem;margin-bottom:1rem;display:block;color:var(--surface2)"></i>
      No projects yet. <a href="add-project.php" style="color:var(--accent)">Add your first project →</a>
    </div>
    <?php endif; ?>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
