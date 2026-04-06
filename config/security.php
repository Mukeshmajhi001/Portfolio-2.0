<?php
/**
 * Security & Helper Functions
 */

// Start secure session
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
        ini_set('session.use_strict_mode', 1);
        session_start();
    }
}

// Generate CSRF token
function generateCSRF(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRF(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitize output
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Sanitize input
function sanitize(string $input): string {
    return trim(strip_tags($input));
}

// Sanitize & validate URL — returns clean URL string or empty string
function sanitizeUrl(string $input): string {
    $url = trim(strip_tags($input));
    if ($url === '') return '';
    // Accept only http:// or https:// URLs for security
    if (!preg_match('#^https?://#i', $url)) return '';
    $validated = filter_var($url, FILTER_VALIDATE_URL);
    return $validated !== false ? $validated : '';
}

// Check admin login
function isAdminLoggedIn(): bool {
    startSecureSession();
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['last_activity'])) return false;
    if ((time() - $_SESSION['last_activity']) > 1800) { // 30 min timeout
        session_unset();
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// Require admin login
function requireAdmin(): void {
    if (!isAdminLoggedIn()) {
        header('Location: ../admin/login.php');
        exit;
    }
}

// Allowed image types
const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

// Validate and upload image
function uploadImage(array $file, string $uploadDir): string|false {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > MAX_FILE_SIZE) return false;
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ALLOWED_IMAGE_TYPES, true)) return false;
    $ext = match($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        default      => false
    };
    if (!$ext) return false;
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = rtrim($uploadDir, '/') . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;
    return $filename;
}
