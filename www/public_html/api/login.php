<?php
// Lädt die Bootstrap-Datei, die für die Initialisierung der Anwendung (DB-Verbindung, Autoloader etc.) notwendig ist.
require_once dirname(__DIR__,1) . '/../config/bootstrap.php';

// Stellt sicher, dass die Anfrage mit der POST-Methode gesendet wurde. Andere Methoden sind nicht erlaubt.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method Not Allowed', 405);

// Holt den Anmeldenamen aus den POST-Daten, entfernt Leerzeichen am Anfang und Ende.
$loginName = trim($_POST['loginName'] ?? '');
// Holt das Passwort aus den POST-Daten, entfernt Leerzeichen am Anfang und Ende.
$password  = trim($_POST['password'] ?? '');
// Überprüft, ob beide Parameter (Anmeldename und Passwort) vorhanden sind. Wenn nicht, wird ein Fehler zurückgegeben.
if (!$loginName || !$password) json_err('Missing parameters', 400);

// Ein try-catch-Block, um mögliche Fehler (z.B. Datenbankfehler) abzufangen und eine saubere Fehlermeldung auszugeben.
try {
    // Erstellt eine neue Instanz des User-Models und übergibt die PDO-Datenbankverbindung.
    $userModel = new User($pdo);
    // Sucht den Benutzer in der Datenbank anhand des Anmeldenamens.
    $user = $userModel->findByLogin($loginName);
    // Wenn kein Benutzer gefunden wird, wird eine erfolgreiche Antwort mit 'success: false' gesendet.
    if (!$user) json_ok(['success'=>false,'block'=>null]);

    // Überprüft, ob das eingegebene Passwort mit dem in der Datenbank übereinstimmt.
    // HINWEIS: Passwörter sollten immer gehasht gespeichert und mit password_verify() überprüft werden.
    if ($password === $user['password']) {
        // Startet eine neue Session, falls noch keine existiert.
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Generiert eine neue Session-ID, um Session-Fixation-Angriffe zu verhindern.
        session_regenerate_id(true);
        // Speichert Benutzerinformationen in der Session.
        $_SESSION['user_id'] = (int)$user['benutzerId'];
        $_SESSION['login'] = $user['loginName'];
        $_SESSION['last_activity'] = time();

        // Sendet eine erfolgreiche Antwort mit dem aktuellen Sperrstatus.
        json_ok(['success'=>true,'block'=>(int)$user['gesperrt']]);
    } else {
        // Bei falschem Passwort wird der Sperrzähler für den Benutzer erhöht.
        $userModel->increaseBlock($loginName);
        // Holt den aktuellen Sperrwert oder setzt ihn auf 0, falls nicht vorhanden.
        $current = isset($user['gesperrt']) ? (int)$user['gesperrt'] : 0;
        // Sendet eine Antwort, die den fehlgeschlagenen Versuch und den neuen Sperrwert anzeigt.
        json_ok(['success'=>false,'block'=>$current + 1]);
    }
} catch (Throwable $e) {
    // Protokolliert den Fehler für Debugging-Zwecke.
    error_log($e->getMessage());
    // Sendet eine generische Fehlermeldung an den Client.
    json_err('Internal server error', 500);
}
