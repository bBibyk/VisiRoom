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

    public static function getById(string $date, int $idWebsite, int $idAnalysisType): Analysis {
        self::checkConnection();

        $stmt = self::$cnx->prepare("
            SELECT a.* FROM analysis a
            WHERE a.date = :date AND a.idWebsite = :idWebsite AND a.idAnalysisType = :idAnalysisType
        ");
        $stmt->bindValue(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':idWebsite', $idWebsite, PDO::PARAM_INT);
        $stmt->bindValue(':idAnalysisType', $idAnalysisType, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetch();

        $analysis = new Analysis($data["date"], WebsiteManager::getById($data["idWebsite"]), AnalysisTypeManager::getById($data['idAnalysisType']), $data["result"]);

        return $analysis;
    }

    public static function getByUser(User $user): array {
        self::checkConnection();

        $stmt = self::$cnx->prepare("
            SELECT a.* FROM analysis a
            JOIN website w ON a.idWebsite = w.id 
            JOIN user u ON w.idUser = u.id 
            WHERE u.email = :email
            ORDER BY date DESC;
        ");
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->execute();
        
        $list = array();

        while($row=$stmt->fetch())
        {
            $analysis = new Analysis($row["date"], WebsiteManager::getById($row["idWebsite"]), AnalysisTypeManager::getById($row['idAnalysisType']), $row["result"]);
            $list[]= $analysis;
        }

        return $list;
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

    public static function add(Analysis $analysis): ?Analysis {
        self::checkConnection();

        $stmt = self::$cnx->prepare("INSERT INTO analysis (date, idWebsite, idAnalysisType, result) VALUES (NOW(), :idWebsite, :idAnalysisType, :result)");
        $stmt->bindValue(':idWebsite', $analysis->getWebsite()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':idAnalysisType', $analysis->getAnalysisType()->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':result', $analysis->getResult(), PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Analysis($data['date'], new Website($data['idWebsite']), new AnalysisType($data['idAnalysisType'])) : null;
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
