<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$timeout = 30 * 60;
if (!empty($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    $_SESSION = [];
    setcookie(session_name(), '', time()-3600, '/');
    session_destroy();
    header('Location: /index.php');
    exit;
}

if (empty($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$_SESSION['last_activity'] = time();