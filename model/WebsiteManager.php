<?php
require_once 'Website.php';
require_once 'DbManager.php';

class WebsiteManager {
    private static ?PDO $cnx = null;

    private static function checkConnection(): void {
        if (self::$cnx === null) {
            self::$cnx = DbManager::getConnexion();
        }
    }

    public static function getById(int $id): ?Website {
        self::checkConnection();

        $stmt = self::$cnx->prepare("SELECT * FROM website WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Website($data['id'], $data['domainname'], new User($data['idUser'])) : null;
    }

    public static function getByDomainName(string $domainName): ?Website {
        self::checkConnection();

        $stmt = self::$cnx->prepare("SELECT * FROM website WHERE domainname = :domainname");
        $stmt->bindValue(':domainname', $domainName, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Website($data['id'], $data['domainname'], new User($data['idUser'])) : null;
    }

    public static function getByUser(int $userId): array {
        self::checkConnection();

        $stmt = self::$cnx->prepare("SELECT * FROM website WHERE idUser = :idUser");
        $stmt->bindValue(':idUser', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function existsDomainName(string $domainName): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("SELECT COUNT(*) FROM website WHERE domainname = :domainname");
        $stmt->bindValue(':domainname', $domainName, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public static function add(Website $website): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("INSERT INTO website (domainname, idUser) VALUES (:domainname, :idUser)");
        return $stmt->execute([
            ':domainname' => $website->getDomainname(),
            ':idUser' => $website->getUser()->getId()
        ]);
    }

    public static function update(Website $website): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("UPDATE website SET domainname = :domainname, idUser = :idUser WHERE id = :id");
        return $stmt->execute([
            ':id' => $website->getId(),
            ':domainname' => $website->getDomainname(),
            ':idUser' => $website->getUser()->getId()
        ]);
    }

    public static function delete(int $id): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("DELETE FROM website WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
