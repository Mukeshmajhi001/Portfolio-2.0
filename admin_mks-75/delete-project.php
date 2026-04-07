<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';
startSecureSession();
requireAdmin();

$id   = (int)($_GET['id']   ?? 0);
$csrf = $_GET['csrf'] ?? '';

if (!verifyCSRF($csrf) || !$id) {
    header('Location: projects.php');
    exit;
}

$db = Database::getConnection();

// Delete associated images from disk
$imgs = $db->prepare("SELECT image_path FROM project_images WHERE project_id = :id");
$imgs->execute([':id' => $id]);
foreach ($imgs->fetchAll() as $img) {
    @unlink(__DIR__ . '/../uploads/' . $img['image_path']);
}

$db->prepare("DELETE FROM projects WHERE id = :id")->execute([':id' => $id]);

unset($_SESSION['csrf_token']);
header('Location: projects.php?deleted=1');
exit;
