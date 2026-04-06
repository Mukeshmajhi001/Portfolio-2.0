<?php
/**
 * Contact Form Handler — Portfolio 2.0
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

startSecureSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// CSRF check
$token = $_POST['csrf_token'] ?? '';
if (!verifyCSRF($token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Security token expired. Please refresh the page.']);
    exit;
}

// Sanitize
$name    = sanitize($_POST['name']    ?? '');
$email   = sanitize($_POST['email']   ?? '');
$message = sanitize($_POST['message'] ?? '');

// Validate
$errors = [];
if (strlen($name) < 2)                  $errors[] = 'Name must be at least 2 characters.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
if (strlen($message) < 10)             $errors[] = 'Message must be at least 10 characters.';
if (strlen($name) > 100)               $errors[] = 'Name is too long.';
if (strlen($message) > 5000)           $errors[] = 'Message is too long.';

if ($errors) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Rate limiting (simple session-based)
if (!isset($_SESSION['last_contact'])) $_SESSION['last_contact'] = 0;
if ((time() - $_SESSION['last_contact']) < 60) {
    echo json_encode(['success' => false, 'message' => 'Please wait a minute before sending another message.']);
    exit;
}

try {
    $db   = Database::getConnection();
    $stmt = $db->prepare("INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)");
    $stmt->execute([':name' => $name, ':email' => $email, ':message' => $message]);
    $_SESSION['last_contact'] = time();
    // Regenerate CSRF
    unset($_SESSION['csrf_token']);
    echo json_encode(['success' => true, 'message' => '✅ Message sent! I\'ll reply within 24 hours.']);
} catch (PDOException $e) {
    error_log('Contact form DB error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to save message. Please try again.']);
}
