<?php
// Lädt die Bootstrap-Datei, die für die Initialisierung der Anwendung (DB-Verbindung, Autoloader etc.) notwendig ist.
require_once dirname(__DIR__,1) . '/../config/bootstrap.php';

// Stellt sicher, dass die Anfrage mit der POST-Methode gesendet wurde. Andere Methoden sind nicht erlaubt.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method Not Allowed', 405);

$loginName = trim($_POST['loginName'] ?? '');
$password  = trim($_POST['password'] ?? '');
// Überprüft, ob beide Parameter (Anmeldename und Passwort) vorhanden sind. Wenn nicht, wird ein Fehler zurückgegeben.
if (!$loginName || !$password) json_err('Missing parameters', 400);

try {
    // Erstellt eine neue Instanz des User-Models und übergibt die PDO-Datenbankverbindung.
    // Sucht den Benutzer in der Datenbank anhand des Anmeldenamens.
    $userModel = new User($pdo);
    $user = $userModel->findByLogin($loginName);
    if (!$user) json_ok(['success'=>false,'block'=>null]);

    // Überprüft, ob das eingegebene Passwort mit dem in der Datenbank übereinstimmt.
    // HINWEIS: Passwörter sollten immer gehasht gespeichert und mit password_verify() überprüft werden.
    if ($password === $user['password']) {
        // Startet eine neue Session, falls noch keine existiert.
        // Generiert eine neue Session-ID, um Session-Fixation-Angriffe zu verhindern.
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['benutzerId'];
        $_SESSION['login'] = $user['loginName'];
        $_SESSION['last_activity'] = time();

        json_ok(['success'=>true,'block'=>(int)$user['gesperrt']]);
    } else {
        // Bei falschem Passwort wird der Sperrzähler für den Benutzer erhöht.
        $userModel->increaseBlock($loginName);
        $current = isset($user['gesperrt']) ? (int)$user['gesperrt'] : 0;
        json_ok(['success'=>false,'block'=>$current + 1]);
    }
} catch (Throwable $e) {
    error_log($e->getMessage());
    json_err('Internal server error', 500);
}
