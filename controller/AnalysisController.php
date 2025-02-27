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
        session_start();
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

        $pythonPath = "C:\Users\Admin\AppData\Local\Programs\Python\Python312\python.exe";
        $scriptPath = "scripts/test.py";
        var_dump(shell_exec($pythonPath." ".$scriptPath." 2>&1"));
        $command = escapeshellcmd("$pythonPath $scriptPath 2>&1");
        $output = shell_exec($command);
        echo "<pre>$output</pre>";

        //require_once 'view/resultView.php';      
    }
}