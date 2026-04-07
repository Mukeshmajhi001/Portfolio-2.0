<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
startSecureSession();
requireAdmin();

$db  = Database::getConnection();
$id  = (int)($_GET['id'] ?? 0);
$csrf= generateCSRF();

$stmt = $db->prepare("SELECT * FROM projects WHERE id = :id");
$stmt->execute([':id' => $id]);
$project = $stmt->fetch();

if (!$project) { header('Location: projects.php'); exit; }

$images = $db->prepare("SELECT * FROM project_images WHERE project_id = :id");
$images->execute([':id' => $id]);
$images = $images->fetchAll();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $title        = sanitize($_POST['title']        ?? '');
        $description  = sanitize($_POST['description']  ?? '');
        $technologies = sanitize($_POST['technologies'] ?? '');
        $category     = sanitize($_POST['category']     ?? '');
        $live_url     = sanitizeUrl($_POST['live_url']     ?? '');
        $github_url   = sanitizeUrl($_POST['github_url']   ?? '');
        $allowedCats  = ['all','php','js','css','fullstack'];

        if (!$title || !$description || !$technologies || !$category) {
            $error = 'Please fill in all required fields.';
        } elseif (!in_array($category, $allowedCats)) {
            $error = 'Invalid category.';
        } else {
            try {
                $upd = $db->prepare(
                    "UPDATE projects SET title=:t, description=:d, technologies=:tech,
                     category=:cat, live_url=:live, github_url=:git WHERE id=:id"
                );
                $upd->execute([
                    ':t' => $title, ':d' => $description, ':tech' => $technologies,
                    ':cat' => $category, ':live' => $live_url, ':git' => $github_url, ':id' => $id
                ]);

                // New images
                if (!empty($_FILES['images']['name'][0])) {
                    $uploadDir = __DIR__ . '/../uploads/';
                    $imgStmt = $db->prepare("INSERT INTO project_images (project_id, image_path) VALUES (:pid, :path)");
                    $files = $_FILES['images'];
                    for ($i = 0; $i < min(count($files['name']), 5); $i++) {
                        $file = ['name'=>$files['name'][$i],'type'=>$files['type'][$i],
                                 'tmp_name'=>$files['tmp_name'][$i],'error'=>$files['error'][$i],'size'=>$files['size'][$i]];
                        $fname = uploadImage($file, $uploadDir);
                        if ($fname) $imgStmt->execute([':pid' => $id, ':path' => $fname]);
                    }
                }

                // Delete specific images
                if (!empty($_POST['delete_images'])) {
                    foreach ($_POST['delete_images'] as $imgId) {
                        $imgId = (int)$imgId;
                        $imgRow = $db->prepare("SELECT image_path FROM project_images WHERE id=:id AND project_id=:pid");
                        $imgRow->execute([':id' => $imgId, ':pid' => $id]);
                        $imgRow = $imgRow->fetch();
                        if ($imgRow) {
                            @unlink(__DIR__ . '/../uploads/' . $imgRow['image_path']);
                            $db->prepare("DELETE FROM project_images WHERE id=:id")->execute([':id' => $imgId]);
                        }
                    }
                }

                unset($_SESSION['csrf_token']);
                header('Location: projects.php?updated=1');
                exit;
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    $csrf = generateCSRF();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">
<title>Edit Project — Admin</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="bg-canvas"></div>
<div class="admin-layout">
  <?php include 'partials/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-header">
      <h1>Edit Project</h1>
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
            <input class="form-control" type="text" name="title" value="<?= e($project['title']) ?>" required>
          </div>
          <div class="form-group">
            <label class="form-label">Category *</label>
            <select class="form-control" name="category" required>
              <?php foreach (['php'=>'PHP','js'=>'JavaScript','css'=>'CSS','fullstack'=>'Full Stack','all'=>'Other'] as $val=>$lbl): ?>
              <option value="<?= $val ?>" <?= $project['category'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Description *</label>
          <textarea class="form-control" name="description" rows="5" required><?= e($project['description']) ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Technologies *</label>
          <input class="form-control" type="text" name="technologies" value="<?= e($project['technologies']) ?>" required>
        </div>
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Live URL</label>
            <input class="form-control" type="url" name="live_url" value="<?= e($project['live_url'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">GitHub URL</label>
            <input class="form-control" type="url" name="github_url" value="<?= e($project['github_url'] ?? '') ?>">
          </div>
        </div>

        <?php if ($images): ?>
        <div class="form-group">
          <label class="form-label">Current Images <span style="color:var(--text3);font-size:0.75rem">(check to delete)</span></label>
          <div style="display:flex;gap:0.75rem;flex-wrap:wrap">
            <?php foreach ($images as $img): ?>
            <div style="position:relative;display:inline-block">
              <img src="../uploads/<?= e($img['image_path']) ?>" style="width:100px;height:75px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
              <label style="position:absolute;top:4px;right:4px;background:rgba(240,79,143,0.9);border-radius:4px;padding:2px 4px;cursor:pointer;font-size:0.7rem;color:#fff">
                <input type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" style="display:none"> ✕
              </label>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <div class="form-group">
          <label class="form-label">Add More Images</label>
          <input class="form-control" type="file" id="project-images" name="images[]" multiple accept="image/jpeg,image/png,image/webp">
          <div id="image-preview" style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-top:0.75rem"></div>
        </div>
        <div style="display:flex;gap:1rem">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Project</button>
          <a href="projects.php" class="btn btn-outline">Cancel</a>
        </div>
      </form>
    </div>
  </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
