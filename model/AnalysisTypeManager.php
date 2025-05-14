<?php
require_once 'AnalysisType.php';
require_once 'DbManager.php';

class AnalysisTypeManager {
    private static ?PDO $cnx = null;

    private static function checkConnection(): void {
        if (self::$cnx === null) {
            self::$cnx = DbManager::getConnexion();
        }
    }

    public static function getAll(): array {
        self::checkConnection();

        $stmt = self::$cnx->prepare("SELECT * FROM analysisType");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById(int $id): ?AnalysisType {
        self::checkConnection();

        $stmt = self::$cnx->prepare("SELECT * FROM analysisType WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new AnalysisType($data['id'], $data['label']) : null;
    }

    public static function getByLabel(string $label): ?AnalysisType {
        self::checkConnection();

        $stmt = self::$cnx->prepare("SELECT * FROM analysisType WHERE label = :label");
        $stmt->bindValue(':label', $label, PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new AnalysisType($data['id'], $data['label']) : null;
    }

    public static function add(AnalysisType $analysisType): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("INSERT INTO analysisType (label) VALUES (:label)");
        return $stmt->execute([
            ':label' => $analysisType->getLabel()
        ]);
    }

    public static function update(AnalysisType $analysisType): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("UPDATE analysisType SET label = :label WHERE id = :id");
        return $stmt->execute([
            ':id' => $analysisType->getId(),
            ':label' => $analysisType->getLabel()
        ]);
    }

    public static function delete(int $id): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("DELETE FROM analysisType WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
