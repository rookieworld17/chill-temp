<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION = [];
setcookie(session_name(), '', time()-3600, '/');
session_destroy();
header('Location: /index.php');
exit;