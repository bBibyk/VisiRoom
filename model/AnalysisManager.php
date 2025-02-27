<?php
require_once 'Analysis.php';
require_once 'DbManager.php';

class AnalysisManager {
    private static ?PDO $cnx = null;

    private static function checkConnection(): void {
        if (self::$cnx === null) {
            self::$cnx = DbManager::getConnexion();
        }
    }

    public static function getByDomainName(string $domainName): array {
        self::checkConnection();

        $stmt = self::$cnx->prepare("
            SELECT a.* FROM analysis a 
            JOIN website w ON a.idWebsite = w.id 
            WHERE w.domainname = :domainname
        ");
        $stmt->bindValue(':domainname', $domainName, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLastByDomainNameAndAnalysisType(string $domainName, int $analysisTypeId): ?Analysis {
        self::checkConnection();

        $stmt = self::$cnx->prepare("
            SELECT a.* FROM analysis a 
            JOIN website w ON a.idWebsite = w.id 
            WHERE w.domainname = :domainname AND a.idAnalysisType = :idAnalysisType
            ORDER BY a.date DESC LIMIT 1
        ");
        $stmt->bindValue(':domainname', $domainName, PDO::PARAM_STR);
        $stmt->bindValue(':idAnalysisType', $analysisTypeId, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Analysis($data['date'], new Website($data['idWebsite']), new AnalysisType($data['idAnalysisType'])) : null;
    }

    public static function add(Analysis $analysis): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("INSERT INTO analysis (date, idWebsite, idAnalysisType, result) VALUES (:date, :idWebsite, :idAnalysisType, :result)");
        return $stmt->execute([
            ':date' => $analysis->getDate(),
            ':idWebsite' => $analysis->getWebsite()->getId(),
            ':idAnalysisType' => $analysis->getAnalysisType()->getId(),
            ':result' => $analysis->getResult()
        ]);
    }

    public static function delete(string $date, int $websiteId, int $analysisTypeId): bool {
        self::checkConnection();

        $stmt = self::$cnx->prepare("DELETE FROM analysis WHERE date = :date AND idWebsite = :idWebsite AND idAnalysisType = :idAnalysisType");
        return $stmt->execute([
            ':date' => $date,
            ':idWebsite' => $websiteId,
            ':idAnalysisType' => $analysisTypeId
        ]);
    }
}
