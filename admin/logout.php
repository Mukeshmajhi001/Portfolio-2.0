<?php
require_once __DIR__ . '/../config/security.php';
startSecureSession();
session_unset();
session_destroy();
header('Location: login.php');
exit;
