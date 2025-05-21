<?php
require_once 'User.php';
require_once 'DbManager.php';

class UserManager {
    private static ?PDO $cnx = null;

    // Vérification de la connexion à la base de données
    private static function checkConnection(): void {
        if (self::$cnx === null) {
            self::$cnx = DbManager::getConnexion();
        }
    }

    // Récupérer tous les utilisateurs
    public static function getAll(): array {
        self::checkConnection();

        $stmt = self::$cnx->prepare("SELECT * FROM user");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par son ID
    public static function getById(int $id): ?User {
        self::checkConnection();

        $stmt = self::$cnx->prepare("SELECT * FROM user WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new User($data['id'], $data['firstname'], $data['surname'], $data['sub'], $data['email'], $data['password']) : null;
    }

    // Récupérer un utilisateur par son email
    public static function getByEmail(string $email): ?User {
        self::checkConnection();

        $stmt = self::$cnx->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new User($data['id'], $data['firstname'], $data['surname'], $data['sub'], $data['email'], $data['password']) : null;
    }

    // Vérifier si un email existe déjà en BDD
    public static function existsEmail(string $email): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Ajouter un utilisateur
    public static function add(User $user): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("INSERT INTO user (firstname, surname, sub, email, password) VALUES (:firstname, :surname, :sub, :email, :password)");
        return $stmt->execute([
            ':firstname' => $user->getFirstname(),
            ':surname' => $user->getSurname(),
            ':sub' => $user->getSub(),
            ':email' => $user->getEmail(),
            ':password' => password_hash($user->getPassword(), PASSWORD_DEFAULT)
        ]);
    }

    // Mettre à jour un utilisateur
    public static function update(User $user): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("UPDATE user SET firstname = :firstname, surname = :surname, sub = :sub, email = :email, password = :password WHERE id = :id");
        return $stmt->execute([
            ':id' => $user->getId(),
            ':firstname' => $user->getFirstname(),
            ':surname' => $user->getSurname(),
            ':sub' => $user->getSub(),
            ':email' => $user->getEmail(),
            ':password' => password_hash($user->getPassword(), PASSWORD_DEFAULT)
        ]);
    }

    // Supprimer un utilisateur
    public static function delete(int $id): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("DELETE FROM user WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
