<?php
// Startet eine neue Session oder setzt eine bestehende fort, falls noch keine aktiv ist.
// Dies ist notwendig, um auf die $_SESSION-Variable zugreifen zu können.
if (session_status() === PHP_SESSION_NONE) session_start();

// Definiert die Timeout-Dauer in Sekunden (hier: 30 Minuten).
$timeout = 30 * 60;
// Überprüft, ob der Benutzer zu lange inaktiv war.
if (!empty($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    // Wenn die Inaktivitätszeit überschritten ist, wird die Session zurückgesetzt.
    $_SESSION = [];
    // Löscht das Session-Cookie im Browser.
    setcookie(session_name(), '', time()-3600, '/');
    // Zerstört alle Daten, die mit der aktuellen Session verbunden sind.
    session_destroy();
    // Leitet den Benutzer zur Login-Seite weiter.
    header('Location: /index.php');
    // Beendet die Skriptausführung, um sicherzustellen, dass keine weiteren Inhalte gesendet werden.
    exit;
}

// Überprüft, ob eine Benutzer-ID in der Session gesetzt ist.
// Wenn nicht, bedeutet das, dass der Benutzer nicht angemeldet ist.
if (empty($_SESSION['user_id'])) {
    // Leitet den nicht angemeldeten Benutzer zur Login-Seite weiter.
    header('Location: /index.php');
    // Beendet die Skriptausführung.
    exit;
}

// Setzt HTTP-Header, um das Caching der Seite im Browser zu verhindern.
// Dies stellt sicher, dass immer die aktuellste Version der Seite geladen wird und sensible Daten nicht im Cache verbleiben.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Aktualisiert den Zeitstempel der letzten Aktivität des Benutzers auf die aktuelle Zeit.
// Dies geschieht bei jedem Seitenaufruf, der diese Datei einbindet.
$_SESSION['last_activity'] = time();
