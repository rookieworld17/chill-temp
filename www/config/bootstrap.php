<?php
// Strikten Session-Modus erzwingen, um Session-Fixation-Angriffe zu verhindern.
ini_set('session.use_strict_mode', 1);
// Setzt das HttpOnly-Flag für Session-Cookies, um den Zugriff über clientseitige Skripte (XSS) zu verhindern.
ini_set('session.cookie_httponly', 1);

// Wenn die Verbindung über HTTPS läuft, wird das Secure-Flag für das Session-Cookie gesetzt.
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

// Pfad zur Datenbank-Konfigurationsdatei.
$cfg = __DIR__ . '/db.php';
// Überprüft, ob die Datenbank-Konfigurationsdatei existiert und wirft eine Ausnahme, wenn nicht.
if (!file_exists($cfg)) {
    throw new Exception('Fehlende config/db.php');
}
// Lädt die Datenbankkonfiguration.
$maybe = include $cfg;
// Stellt sicher, dass eine gültige PDO-Instanz verfügbar ist,
// egal ob sie in der Datei deklariert oder von ihr zurückgegeben wird.
if (!isset($pdo) || !($pdo instanceof PDO)) {
    if ($maybe instanceof PDO) $pdo = $maybe;
}
// Wirft eine Ausnahme, wenn keine PDO-Verbindung hergestellt werden kann.
if (!isset($pdo) || !($pdo instanceof PDO)) {
    throw new Exception('Kein PDO verfügbar');
}

// Registriert eine Autoload-Funktion für Model-Klassen.
// Lädt automatisch Klassen aus dem Verzeichnis 'modules/models/'.
spl_autoload_register(function($class){
    $path = dirname(__DIR__) . '/modules/models/' . $class . '.php';
    if (file_exists($path)) require $path;
});

/**
 * Sendet eine erfolgreiche JSON-Antwort und beendet das Skript.
 * @param mixed $data Die zu kodierenden Daten.
 */
function json_ok($data){ header('Content-Type: application/json'); echo json_encode($data); exit; }

/**
 * Sendet eine JSON-Fehlerantwort und beendet das Skript.
 * @param string $msg Die Fehlermeldung.
 * @param int $code Der HTTP-Statuscode (Standard: 500).
 */
function json_err($msg, $code=500){ http_response_code($code); header('Content-Type: application/json'); echo json_encode(['error'=>$msg]); exit; }
