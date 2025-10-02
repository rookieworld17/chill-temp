<?php
/**
 * Die User-Klasse ist für die Verwaltung von Benutzerdaten in der Datenbank zuständig.
 * Sie enthält Methoden zum Abrufen und Aktualisieren von Benutzerinformationen.
 */
class User {
    /**
     * @var PDO Die PDO-Instanz für die Datenbankverbindung.
     */
    private PDO $pdo;

    /**
     * Konstruktor der User-Klasse.
     *
     * @param PDO $pdo Eine PDO-Instanz für die Datenbankverbindung.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Findet einen Benutzer anhand seines Anmeldenamens.
     *
     * @param string $loginName Der Anmeldename des Benutzers.
     * @return array|null Gibt die Benutzerdaten als assoziatives Array zurück oder null, wenn kein Benutzer gefunden wurde.
     */
    public function findByLogin(string $loginName): ?array {
        $sql = "SELECT * FROM benutzer WHERE loginName = :login";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['login' => $loginName]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Erhöht den Sperrzähler für einen Benutzer.
     * Dies wird typischerweise nach einem fehlgeschlagenen Anmeldeversuch verwendet.
     *
     * @param string $loginName Der Anmeldename des Benutzers.
     * @return bool Gibt true bei Erfolg zurück, andernfalls false.
     */
    public function increaseBlock(string $loginName): bool {
        $sql = "UPDATE benutzer SET gesperrt = gesperrt + 1 WHERE loginName = :login";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['login' => $loginName]);
    }
}
