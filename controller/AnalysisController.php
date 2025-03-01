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

        echo $url;

        if(AnalysisController::domaineExiste($url)){
            $pythonPath = "C:\Users\Admin\AppData\Local\Programs\Python\Python312\python.exe";
            $scriptPath = "scripts/test.py";
            $command = escapeshellcmd("$pythonPath $scriptPath $url 2>&1");
            $output = shell_exec($pythonPath." ".$scriptPath." ".$url." 2>&1");

            $resultHtml = AnalysisController::formatJsonResponse($output);
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
    

    static function formatJsonResponse($json) {
        $data = json_decode($json, true);
        $result = "";
    
        if (!$data) {
            return "<p>Aucune donnée disponible.</p>";
        }
    
        foreach ($data as $url => $sections) {
            $result .= "<h3>Analyse pour : $url</h3>";
            foreach ($sections as $section => $errors) {
                $result .= "<p><strong>" . ucfirst($section) . " :</strong></p>";
                if (empty($errors)) {
                    $result .= "<p>Aucune erreur détectée.</p>";
                } else {
                    $result .= "<ul>";
                    foreach ($errors as $error) {
                        // Encapsuler les balises <input> et <button> pour éviter l'exécution
                        $escapedError = str_replace('<input', '&lt;input', $error);
                        $escapedError = str_replace('</input>', '&lt;/input&gt;', $escapedError);
                        $escapedError = str_replace('<button', '&lt;button', $escapedError);
                        $escapedError = str_replace('</button>', '&lt;/button&gt;', $escapedError);
                        $result .= "<li>$escapedError</li>";
                    }
                    $result .= "</ul>";
                }
            }
            $result .= "<hr>";
        }
    
        return $result;
    }
}