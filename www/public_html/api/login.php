<?php
require_once dirname(__DIR__,1) . '/../config/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method Not Allowed', 405);

$loginName = trim($_POST['loginName'] ?? '');
$password  = trim($_POST['password'] ?? '');
if (!$loginName || !$password) json_err('Missing parameters', 400);

try {
    $userModel = new User($pdo);
    $user = $userModel->findByLogin($loginName);
    if (!$user) json_ok(['success'=>false,'block'=>null]);

    if ($password === $user['password']) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['benutzerId'];
        $_SESSION['login'] = $user['loginName'];
        $_SESSION['last_activity'] = time();

        json_ok(['success'=>true,'block'=>(int)$user['gesperrt']]);
    } else {
        $userModel->increaseBlock($loginName);
        $current = isset($user['gesperrt']) ? (int)$user['gesperrt'] : 0;
        json_ok(['success'=>false,'block'=>$current + 1]);
    }
} catch (Throwable $e) {
    error_log($e->getMessage());
    json_err('Internal server error', 500);
}
