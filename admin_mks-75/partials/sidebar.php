<aside class="admin-sidebar">
  <div class="sidebar-logo">Mks-75 Admin</div>
  <nav class="sidebar-nav">
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
      <i class="fas fa-chart-pie"></i> Dashboard
    </a>
    <a href="projects.php" class="<?= in_array(basename($_SERVER['PHP_SELF']), ['projects.php','add-project.php','edit-project.php']) ? 'active' : '' ?>">
      <i class="fas fa-folder"></i> Projects
    </a>
    <a href="add-project.php" class="<?= basename($_SERVER['PHP_SELF']) === 'add-project.php' ? 'active' : '' ?>">
      <i class="fas fa-plus-circle"></i> Add Project
    </a>
    <a href="messages.php" class="<?= basename($_SERVER['PHP_SELF']) === 'messages.php' ? 'active' : '' ?>">
      <i class="fas fa-envelope"></i> Messages
      <?php
      $unread = (Database::getConnection())->query("SELECT COUNT(*) FROM messages WHERE is_read=0")->fetchColumn();
      if ($unread > 0):
      ?>
      <span style="margin-left:auto;background:var(--accent3);color:#fff;font-size:0.7rem;padding:0.15rem 0.5rem;border-radius:50px;"><?= $unread ?></span>
      <?php endif; ?>
    </a>
    <a href="../index.php" target="_blank"><i class="fas fa-eye"></i> View Site</a>
    <a href="logout.php" style="margin-top:auto; color:var(--accent3)"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>
</aside>
