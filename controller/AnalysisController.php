<?php

require_once 'model/AnalysisTypeManager.php';
require_once 'model/AnalysisType.php';
require_once 'model/AnalysisManager.php';
require_once 'model/Analysis.php';
require_once 'model/UserManager.php';
require_once 'model/User.php';
require_once 'model/WebsiteManager.php';
require_once 'model/Website.php';

class AnalysisController{
    public static function analysis(){
        session_start();

        require_once 'view/analysisView.php';
    }

    public static function add(){
        //session_start();
        //$user = UserManager::getByEmail($_SESSION['email']);
        $website;

        //Création du website
        /*
        if(WebsiteManager::existsDomainName($_POST['domainName'])){
            $website = WebsiteManager::getByDomainName($_POST['domainName']);
        }else{
            $website = new Website(0, $_POST['domainName'], $user->getId());
            WebsiteManager::add($website);
        }*/

        //Création d'analyse
        //$listType = AnalysisType::getAll();

        $url = $_POST["domainName"];
        $resultHtml;
        $resultCrawler;
        $resultHtmlCrawler;
        $resultHtmlCrawlerParalel;

        echo $url;

        if(AnalysisController::domaineExiste($url)){
            $pythonPath = "C:\Users\Admin\AppData\Local\Programs\Python\Python312\python.exe";
            $scriptPathHtml = "scripts/first_analysis.py";

            $resultHtml = AnalysisController::displayJson(shell_exec($pythonPath." ".$scriptPathHtml." ".$url." 2>&1"));

            require_once 'view/resultView.php';    
        }else{
            $message = "Ce nom de domaine n'existe pas.";
            require_once 'view/errorView.php';  
        }  
    }

    static function domaineExiste($url) {
        // Retire le protocole (http:// ou https://) s'il existe
        $parse = parse_url($url);
        if (!isset($parse['host'])) {
            $domaine = $parse['path']; // Si l'URL est sans protocole
        } else {
            $domaine = $parse['host']; // Si l'URL a un protocole
        }
    
        // Vérifie le DNS
        if (checkdnsrr($domaine, 'A') || checkdnsrr($domaine, 'AAAA') || checkdnsrr($domaine, 'CNAME')) {
            return true; // Le domaine existe
        }
        return false; // Le domaine n'existe pas
    }
    
    private static function displayJson(array|string $json): void {
        // Si la chaîne JSON est passée, la convertir en tableau
        if (is_string($json)) {
            $json = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "<div style='color: red;'>Erreur de décodage JSON : " . json_last_error_msg() . "</div>";
                return;
            }
        }

        echo '<div style="font-family: Arial, sans-serif; padding: 10px; background: #f4f4f4; border-radius: 8px;">';
        echo '<h3 style="color: #333;">Résultat JSON :</h3>';
        echo '<ul style="list-style: none; padding-left: 20px;">';
        self::renderJson($json);
        echo '</ul>';
        echo '</div>';
    }

    private static function renderJson(array $json): void {
        foreach ($json as $key => $value) {
            echo '<li style="margin-bottom: 10px;">';
            echo '<strong style="color: #007BFF;">' . htmlspecialchars($key) . ':</strong> ';
            if (is_array($value)) {
                echo '<ul style="margin-top: 5px; padding-left: 20px;">';
                AnalysisController::renderJson($value);
                echo '</ul>';
            } elseif (is_bool($value)) {
                echo $value ? '<span style="color: green;">true</span>' : '<span style="color: red;">false</span>';
            } elseif (is_null($value)) {
                echo '<span style="color: gray;">null</span>';
            } else {
                echo htmlspecialchars((string)$value);
            }
            echo '</li>';
        }
    }
}