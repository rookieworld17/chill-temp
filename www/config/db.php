<?php
// Holt den Datenbank-Host aus den Umgebungsvariablen.
$host = getenv('DB_HOST');
// Holt den Datenbanknamen aus den Umgebungsvariablen.
$db   = getenv('DB_NAME');
// Holt den Datenbank-Benutzernamen aus den Umgebungsvariablen.
$user = getenv('DB_USER');
// Holt das Datenbank-Passwort aus den Umgebungsvariablen.
$pass = getenv('DB_PASS');
// Definiert den Zeichensatz für die Datenbankverbindung.
$charset = 'utf8mb4';

// Erstellt den Data Source Name (DSN) für die PDO-Verbindung.
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
// Definiert die Optionen für die PDO-Verbindung.
$options = [
    // Setzt den Fehlermodus auf "Exception", damit PDO-Fehler als Ausnahmen geworfen werden.
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // Setzt den Standard-Fetch-Modus auf assoziatives Array.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// Versucht, eine neue PDO-Instanz zu erstellen, um eine Verbindung zur Datenbank herzustellen.
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Gibt die PDO-Instanz bei erfolgreicher Verbindung zurück.
    return $pdo;
} catch (PDOException $e) {
    // Gibt im Fehlerfall die verwendeten Verbindungsdaten als JSON aus (nützlich für das Debugging).
    // ACHTUNG: In einer Produktionsumgebung sollten sensible Daten nicht ausgegeben werden.
    echo json_encode([
        'host' => getenv('DB_HOST'),
        'db' => getenv('DB_NAME'),
        'user' => getenv('DB_USER'),
        'pass' => getenv('DB_PASS')
    ]);
    // Wirft die ursprüngliche PDOException erneut, um den Fehler weiter zu behandeln.
    throw $e;
}
