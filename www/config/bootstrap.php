<?php
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

$cfg = __DIR__ . '/db.php';
if (!file_exists($cfg)) {
    throw new Exception('Missing config/db.php');
}
$maybe = include $cfg;
if (!isset($pdo) || !($pdo instanceof PDO)) {
    if ($maybe instanceof PDO) $pdo = $maybe;
}
if (!isset($pdo) || !($pdo instanceof PDO)) {
    throw new Exception('No PDO available');
}

spl_autoload_register(function($class){
    $path = dirname(__DIR__) . '/modules/models/' . $class . '.php';
    if (file_exists($path)) require $path;
});

function json_ok($data){ header('Content-Type: application/json'); echo json_encode($data); exit; }
function json_err($msg, $code=500){ http_response_code($code); header('Content-Type: application/json'); echo json_encode(['error'=>$msg]); exit; }
