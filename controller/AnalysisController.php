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
        require_once 'view/analysisView.php';
    }

    public static function add(){
        
        if(!isset($_SESSION["email"]) || empty($_SESSION["email"])){
            header("location: connection");
        }

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
    
    private static function displayJson(array|string $json): string {
        // Si la chaîne JSON est passée, la convertir en tableau
        if (is_string($json)) {
            $json = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "<div style='color: red;'>Erreur de décodage JSON : " . json_last_error_msg() . "</div>";
            }
        }
        return self::renderJson($json);
    }

    /*
    private static function renderJson(array $json, int $level = 1): void {
        foreach ($json as $key => $value) {
            // Décaler tous les titres d'un niveau vers le haut et masquer les plus hauts niveaux
            if ($level <= 6 && $level != 3) {
                echo '<h' . ($level - 1) . ' style="color: #007BFF; margin-top: 10px; padding-left: 30px; text-align: left;">' . htmlspecialchars($key) . '</h' . ($level - 1) . '>';
            }
            
            if (is_array($value)) {
                echo '<ul style="margin-top: 5px; padding-left: 20px; list-style-position: inside;">';
                AnalysisController::renderJson($value, $level + 1);
                echo '</ul>';
            } else {
                echo '<li style="display: block; text-align: left;">';
                if (is_bool($value)) {
                    echo '<span style="color: ' . ($value ? 'green' : 'red') . ';">' . ($value ? 'true' : 'false') . '</span>';
                } elseif (is_null($value)) {
                    echo '<span style="color: gray;">null</span>';
                } else {
                    echo '<span>' . htmlspecialchars((string)$value) . '</span>';
                }
                echo '</li>';
            }
        }
    }*/

    private static function renderJson(array $json): string {
        $result = '';

        foreach ($json as $k1 => $v1) {
            $result .= '<h1 class="result-h1">Page : '.htmlspecialchars($k1).'</h1>'; //Affichage des noms de pages

            if(is_array($v1)){
                foreach ($v1 as $k2 => $v2){
                    $result .= '<h2 class="result-h2">'.htmlspecialchars($k2).'</h2>'; //Affichage des types de correctifs

                    if(is_array($v2)){
                        foreach ($v2 as $k3 => $v3){
                            $result .= '<p class="result-text">'.htmlspecialchars((string)$v3).'</p>';
                        }
                    }
                }
            }else{
                
            }
        }

        return $result;
    }
}