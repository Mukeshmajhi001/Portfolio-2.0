<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
startSecureSession();
requireAdmin();

$db = Database::getConnection();

// Mark as read
if (isset($_GET['read']) && is_numeric($_GET['read'])) {
    $db->prepare("UPDATE messages SET is_read=1 WHERE id=:id")->execute([':id' => (int)$_GET['read']]);
    header('Location: messages.php');
    exit;
}

// Delete message
if (isset($_GET['delete']) && is_numeric($_GET['delete']) && verifyCSRF($_GET['csrf'] ?? '')) {
    $db->prepare("DELETE FROM messages WHERE id=:id")->execute([':id' => (int)$_GET['delete']]);
    unset($_SESSION['csrf_token']);
    header('Location: messages.php?deleted=1');
    exit;
}

$messages = $db->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
$csrf = generateCSRF();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Messages — Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-canvas"></div>
<div class="admin-layout">
  <?php include 'partials/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-header">
      <h1>Messages <span style="color:var(--text3);font-size:0.9rem;font-weight:400">(<?= count($messages) ?>)</span></h1>
    </div>
    <?php if (isset($_GET['deleted'])): ?>
    <div class="form-feedback success alert-auto" style="display:block">Message deleted.</div>
    <?php endif; ?>

    <?php if ($messages): ?>
    <div style="display:flex;flex-direction:column;gap:1rem">
      <?php foreach ($messages as $m): ?>
      <div style="background:var(--card-bg);border:1px solid <?= $m['is_read'] ? 'var(--border)' : 'rgba(79,143,255,0.3)' ?>;border-radius:var(--radius);padding:1.5rem;backdrop-filter:blur(8px)">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap">
          <div>
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.4rem">
              <strong><?= e($m['name']) ?></strong>
              <span class="badge <?= $m['is_read'] ? 'badge-read' : 'badge-unread' ?>"><?= $m['is_read'] ? 'Read' : 'New' ?></span>
            </div>
            <div style="font-size:0.82rem;color:var(--text3)">
              <i class="fas fa-envelope"></i> <?= e($m['email']) ?> &nbsp;
              <i class="fas fa-clock"></i> <?= date('d M Y H:i', strtotime($m['created_at'])) ?>
            </div>
          </div>
          <div style="display:flex;gap:0.5rem">
            <?php if (!$m['is_read']): ?>
            <a href="?read=<?= $m['id'] ?>" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Mark Read</a>
            <?php endif; ?>
            <a href="?delete=<?= $m['id'] ?>&csrf=<?= e($csrf) ?>" class="btn btn-danger btn-sm btn-delete-confirm"><i class="fas fa-trash"></i></a>
          </div>
        </div>
        <div style="margin-top:1rem;padding:1rem;background:var(--surface);border-radius:var(--radius-sm);color:var(--text2);font-size:0.9rem;line-height:1.7">
          <?= nl2br(e($m['message'])) ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:4rem;color:var(--text3)">
      <i class="fas fa-inbox" style="font-size:3rem;margin-bottom:1rem;display:block;color:var(--surface2)"></i>
      No messages yet.
    </div>
    <?php endif; ?>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
