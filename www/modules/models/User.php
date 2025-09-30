<?php
class User {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findByLogin(string $loginName): ?array {
        $sql = "SELECT * FROM benutzer WHERE loginName = :login";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['login' => $loginName]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function increaseBlock(string $loginName): bool {
        $sql = "UPDATE benutzer SET gesperrt = gesperrt + 1 WHERE loginName = :login";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['login' => $loginName]);
    }
}
