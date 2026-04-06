<?php
/**
 * Admin — Add Project
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

startSecureSession();
requireAdmin();

$csrf   = generateCSRF();
$error  = '';
$success= '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $title       = sanitize($_POST['title']       ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $technologies= sanitize($_POST['technologies']?? '');
        $category    = sanitize($_POST['category']    ?? '');
        $live_url    = sanitizeUrl($_POST['live_url']    ?? '');
        $github_url  = sanitizeUrl($_POST['github_url']  ?? '');

        $allowedCats = ['all','php','js','css','fullstack'];

        if (!$title || !$description || !$technologies || !$category) {
            $error = 'Please fill in all required fields.';
        } elseif (!in_array($category, $allowedCats)) {
            $error = 'Invalid category.';
        } else {
            try {
                $db   = Database::getConnection();
                $stmt = $db->prepare(
                    "INSERT INTO projects (title, description, technologies, category, live_url, github_url)
                     VALUES (:title, :desc, :tech, :cat, :live, :git)"
                );
                $stmt->execute([
                    ':title' => $title, ':desc'  => $description,
                    ':tech'  => $technologies, ':cat' => $category,
                    ':live'  => $live_url,  ':git'  => $github_url,
                ]);
                $projectId = $db->lastInsertId();

                // Handle images
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                if (!empty($_FILES['images']['name'][0])) {
                    $files = $_FILES['images'];
                    $count = count($files['name']);
                    $imgStmt = $db->prepare("INSERT INTO project_images (project_id, image_path) VALUES (:pid, :path)");
                    for ($i = 0; $i < min($count, 5); $i++) {
                        $file = [
                            'name'     => $files['name'][$i],
                            'type'     => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error'    => $files['error'][$i],
                            'size'     => $files['size'][$i],
                        ];
                        $fname = uploadImage($file, $uploadDir);
                        if ($fname) {
                            $imgStmt->execute([':pid' => $projectId, ':path' => $fname]);
                        }
                    }
                }

                unset($_SESSION['csrf_token']);
                header('Location: projects.php?added=1');
                exit;
            } catch (PDOException $e) {
                error_log('Add project error: ' . $e->getMessage());
                $error = 'Database error. Please try again.';
            }
        }
    }
    $csrf = generateCSRF();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Add Project — Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-canvas"></div>
<div class="admin-layout">
  <?php include 'partials/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-header">
      <h1>Add New Project</h1>
      <a href="projects.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <?php if ($error): ?>
    <div class="form-feedback error-msg alert-auto" style="display:block"><?= e($error) ?></div>
    <?php endif; ?>

    <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius-lg);padding:2rem;backdrop-filter:blur(8px)">
      <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Project Title *</label>
            <input class="form-control" type="text" name="title" placeholder="My Awesome Project" required maxlength="150">
          </div>
          <div class="form-group">
            <label class="form-label">Category *</label>
            <select class="form-control" name="category" required>
              <option value="">Select category</option>
              <option value="php">PHP</option>
              <option value="js">JavaScript</option>
              <option value="css">CSS</option>
              <option value="fullstack">Full Stack</option>
              <option value="all">Other</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Description * <span style="color:var(--text3);font-size:0.75rem">(<span id="char-count">0</span>/2000)</span></label>
          <textarea class="form-control" name="description" rows="5" placeholder="Describe your project…" required maxlength="2000" data-maxlen="2000"></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Technologies * <span style="color:var(--text3);font-size:0.75rem">(comma separated, e.g. PHP, MySQL, JS)</span></label>
          <input class="form-control" type="text" name="technologies" placeholder="PHP, MySQL, JavaScript, CSS" required>
        </div>
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Live URL</label>
            <input class="form-control" type="url" name="live_url" placeholder="https://yourproject.com">
          </div>
          <div class="form-group">
            <label class="form-label">GitHub URL</label>
            <input class="form-control" type="url" name="github_url" placeholder="https://github.com/user/repo">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Project Images <span style="color:var(--text3);font-size:0.75rem">(max 5 images, jpg/png/webp, max 5MB each)</span></label>
          <input class="form-control" type="file" id="project-images" name="images[]" multiple accept="image/jpeg,image/png,image/webp">
          <div id="image-preview" style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-top:0.75rem"></div>
        </div>
        <div style="display:flex;gap:1rem;margin-top:0.5rem">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Project</button>
          <a href="projects.php" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
