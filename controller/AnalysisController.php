<?php

require_once 'model/AnalysisTypeManager.php';
require_once 'model/AnalysisType.php';
require_once 'model/AnalysisManager.php';
require_once 'model/Analysis.php';
require_once 'model/UserManager.php';
require_once 'model/User.php';
require_once 'model/WebsiteManager.php';
require_once 'model/Website.php';
require_once 'controller/Parsedown.php';


class AnalysisController{
    public static function analysis(){
        require_once 'view/analysisView.php';
    }

    public static function all(){
        if(!isset($_SESSION["email"]) || empty($_SESSION["email"])){
            header("location: connection");
        }

        $user = UserManager::getByEmail($_SESSION['email']);
        $listAnalysis = AnalysisManager::getByUser($user);

        require_once 'view/dashboardView.php';
    }

    public static function get(){
        $analysis = AnalysisManager::getById($_GET['date'], $_GET['idWebsite'], $_GET['idAnalysisType']);
        $result = $analysis->getResult();
        
        require_once 'view/resultView.php';
    }

    public static function addTypeHtml(){ 
        if(!isset($_SESSION["email"]) || empty($_SESSION["email"])){
            header("location: connection");
        }

        $user = UserManager::getByEmail($_SESSION['email']);
        $website;
        $analysis;
        $result;

        if(isset($_POST["domainName"]) && !empty($_POST["domainName"])){
            $url = AnalysisController::completeDomainName($_POST['domainName']);

            //Création du website
            if(WebsiteManager::existsDomainName($url)){
                $website = WebsiteManager::getByDomainName($url);
            }else{
                $website = new Website(0, $url, $user);
                $website = WebsiteManager::add($website);
            }

            if(AnalysisController::domaineExiste($url)){
                //C:\Users\Admin\AppData\Local\Programs\Python\Python312\python.exe scripts/first_analysis.py https://seraphartgallery.com/ 2>&1
                //C:/wamp64/www/Visiboost/env/Scripts/python.exe scripts/first_analysis.py https://seraphartgallery.com/ 2>&1
    
                $scriptPathHtml = "scripts/first_analysis.py";
                $command = "C:/wamp64/www/Visiboost/env/Scripts/python.exe " . $scriptPathHtml . " " . $url . " 2>&1";

                //$command = "C:/wamp64/www/Visiboost/env/Scripts/python.exe " . $scriptPathHtml . " 1 2";
                $output = shell_exec($command);
    
                // On tente d'extraire du JSON (en cherchant la première accolade ouvrante)
                if($output != null){
                    $start = strpos($output, '{');
                    if ($start !== false) {
                        $jsonString = substr($output, $start);
                        $data = json_decode($jsonString, true);
                    } else {
                        $data = null;
                    }
                    
                    if(!isset($data['error'])){
                        if($data['type'] == 'SSR'){
                            $result = AnalysisController::renderJson($data);
                            $analysis = new Analysis("", $website, AnalysisTypeManager::getByLabel('html'), $result);
                    
                            AnalysisManager::add($analysis);
                            require_once 'view/resultView.php';
                        }elseif($data['type'] == 'CSR'){
                            $message = 'Les sites web développé avec le framework Angular ne peuvent pas être analysés. Il est recommandé d\'utiliser une autre architecture pour un meilleur référencement SEO.';
                            require_once 'view/errorView.php'; 
                        }
                    }else{
                        if($data['error'] == 'unavailable'){
                            $message = 'Chargement du site impossible.';
                            require_once 'view/errorView.php';                        
                        }else{
                            $message = "Erreur 500 : données indisponnibles.";
                            require_once 'view/errorView.php';
                        }
                    }
                }else{
                    $message = "Erreur 500 : données indisponnibles.";
                    require_once 'view/errorView.php';
                }   
            }else{
                $message = "Ce nom de domaine n'existe pas.";
                require_once 'view/errorView.php';
            }  
        }else{
            $message = 'Le nom de domaine est obligatoire';
            require_once 'view/errorView.php';
        }
    }

    static function addTypeSearch(){
        if(!isset($_SESSION["email"]) || empty($_SESSION["email"])){
            header("location: connection");
        }

        $user = UserManager::getByEmail($_SESSION['email']);
        $website;
        $analysis;
        $sentence = $_POST["sentence"];
        $result;

        if(isset($_POST["domainName"]) && !empty($_POST["domainName"])){
            $url = AnalysisController::completeDomainName($_POST['domainName']);

            //Création du website
            if(WebsiteManager::existsDomainName($url)){
                $website = WebsiteManager::getByDomainName($url);
            }else{
                $website = new Website(0, $url, $user);
                $website = WebsiteManager::add($website);
            }

            if(isset($_POST["sentence"]) && !empty($_POST["sentence"])){
                if(AnalysisController::domaineExiste($url)){
                    //C:\wamp64\www\Visiboost\env\Scripts\python.exe scripts/request_page_analysis.py "https://www.exemple.com" "exemple"
                    //C:\wamp64\www\Visiboost\env\Scripts\python.exe scripts/request_page_analysis.py "https://www.seraphartgallery.com" "oeuvre d'art"
        
                    $scriptPath = "scripts/request_page_analysis.py";
                    $command = 'C:/wamp64/www/Visiboost/env/Scripts/python.exe scripts/request_page_analysis.py "'.$url.'" "'.$sentence.'"';
                    
                    echo $command;

                    $output = shell_exec($command);

                    var_dump($output);
        
                    if($output != null){
                        // On tente d'extraire du JSON (en cherchant la première accolade ouvrante)
                        $data;
                        $start = strpos($output, '{');
                        if ($start !== false) {
                            $jsonString = substr($output, $start);
                            $data = json_decode($jsonString, true);
                        } else {
                            $data = null;
                        }
                    
                            // Si tout va bien, affiche
                            $result = AnalysisController::renderJsonSearch($data);

                            if($result != 'error:unavailable'){
                                $analysis = new Analysis("", $website, AnalysisTypeManager::getByLabel('search'), $result);
                
                                AnalysisManager::add($analysis);
                                require_once 'view/resultView.php';
                            }else{
                                $message = 'Données indisponible';
                                require_once 'view/errorView.php';
                            }
                    }else{
                        $message = "Erreur 500 : données indisponnibles.";
                        require_once 'view/errorView.php';
                    } 
                }else{
                    $message = "Ce nom de domaine n'existe pas.";
                    require_once 'view/errorView.php';
                }
            }else{
                $message = "La recherche type est obligatoire.";
                require_once 'view/errorView.php';
            }
        }else{
            $message = "Le nom de domaine est obligatoire.";
            require_once 'view/errorView.php';
        }
    }

    static function completeDomainName(string $domainName): string {
        if (!preg_match('#^[a-z]+://#i', $domainName)) {
            $domainName = 'https://' . $domainName;
        }

        // Ajouter www. si le domaine ne l’a pas déjà
        /*$parsed = parse_url($domainName);
        if (isset($parsed['host']) && !preg_match('/^www\./', $parsed['host'])) {
            $domainName = str_replace($parsed['host'], 'www.' . $parsed['host'], $domainName);
        }*/

        return $domainName;
    }


    static function domaineExiste(string $url): bool {
        // Extraire le host
        $parse = parse_url($url);
        $host = $parse['host'] ?? $parse['path'];

        // Si vide, on ne peut rien faire
        if (empty($host)) {
            return false;
        }

        // 1. Test direct
        if (checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA') || checkdnsrr($host, 'CNAME')) {
            return true;
        }

        // 2. Remonter les sous-domaines (eu.shop.battle.net → shop.battle.net → battle.net)
        $parts = explode('.', $host);
        while (count($parts) > 2) {
            array_shift($parts); // retire le sous-domaine le plus à gauche
            $testHost = implode('.', $parts);

            if (checkdnsrr($testHost, 'A') || checkdnsrr($testHost, 'AAAA') || checkdnsrr($testHost, 'CNAME')) {
                return true;
            }
        }

        // 3. Fallback optionnel (résolution DNS par IP)
        $resolved = gethostbyname($host);
        if ($resolved !== $host) {
            return true;
        }

        return false;
    }
    
    private static function displayJson(array|string $json): string {
        // Si la chaîne JSON est passée, la convertir en tableau
        if (is_string($json)) {
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "<div style='color: red;'>Erreur de décodage JSON : " . json_last_error_msg() . "</div>";
            }
        }

        return self::renderJson($json);
    }

    private static function renderJson(array $json): string {
        $html = '';
    
        foreach ($json as $url => $sections) {
            $html .= "<h2>Résultats pour : <em>$url</em></h2>";

            if(is_array($sections)){
                foreach ($sections as $sectionName => $sectionData) {
                $html .= "<h3>".ucfirst($sectionName)."</h3>";
                $html .= "<ul>";
    
                foreach ($sectionData as $key => $value) {
                    if(is_array($value)) {
                        if($sectionName != 'text'){
                            $html .= "<li><strong>$key:</strong><ul>";
                        }

                        foreach ($value as $subKey => $subValue) {
                            if(is_array($subValue)){
                                $html.= '<table><thead><tr><th>Type d\'analyse</th><th>Explication</th><th>Valeur idéal</th><th>Valeur réelle</th></thead><tbody>';
                                
                                foreach($subValue as $k => $v){
                                    if($k == 'flesch_reading_ease'){
                                        $html.='<tr><td>Indice de lisibilité de Flesch</td><td>Plus il est élevé, plus le texte est facile</td><td>> 60 (bon) — > 80 (très facile)</td><td>'.$v.'</td></tr>';
                                    }elseif($k == 'flesch_kincaid_grade'){
                                        $html.='<tr><td>Niveau scolaire Flesch-Kincaid</td><td>Niveau d’étude nécessaire pour comprendre</td><td>6-8 (bon pour un public large)</td><td>'.$v.'</td></tr>';
                                    }elseif($k == 'smog_index'){
                                        $html.='<tr><td>Indice SMOG</td><td>Complexité liée au vocabulaire difficile</td><td>< 10 (préférable)</td><td>'.$v.'</td></tr>';
                                    }elseif($k == 'automated_readability_index'){
                                        $html.='<tr><td>Indice de lisibilité automatisé</td><td>Âge scolaire estimé du lecteur</td><td>7-9 (idéal pour public général)</td><td>'.$v.'</td></tr>';
                                    }elseif($k == 'dale_chall_score'){
                                        $html.='<tr><td>Score de Dale-Chall</td><td>Mesure des mots complexes non familiers</td><td>< 7.5 (plus c’est bas, mieux c’est)</td><td>'.$v.'</td></tr>';
                                    }elseif($k == 'difficult_words'){
                                        $html.='<tr><td>Nombre de mots difficiles</td><td>Nombre de mots considérés « difficiles »</td><td>Moins, c’est mieux</td><td>'.$v.'</td></tr>';
                                    }elseif($k == 'linsear_write_formula'){
                                        $html.='<tr><td>Formule Linsear Write</td><td>Complexité grammaticale</td><td>Entre 6 et 9</td><td>'.$v.'</td></tr>';
                                    }elseif($k == 'gunning_fog'){
                                        $html.='<tr><td>Indice Gunning Fog</td><td>Longueur des phrases et vocabulaire complexe</td><td>< 12 (bon), idéalement < 10</td><td>'.$v.'</td></tr>';
                                    }elseif($k == 'text_standard'){
                                        $html.='<tr><td>Niveau scolaire moyen estimé</td><td>Moyenne des niveaux scolaires estimés</td><td>"7th and 8th grade" (bon niveau)</td><td>'.$v.'</td></tr>';
                                    }
                                }

                                $html.='</tbody></table>';
                            }elseif($subKey == 'global_score'){
                                $html.= '<h3>Socre global : '.$subValue.'</h3>';
                            }else{
                                $html .= "<li>$subKey : " . (is_array($subValue) ? json_encode($subValue, JSON_UNESCAPED_UNICODE) : $subValue) . "</li>";
                            }
                        }

                        $html .= "</ul></li>";
                    } else {
                        $html .= "<li><strong>$key:</strong> $value</li>";
                    }
                }
    
                $html .= "</ul>";
            }
            }
    
            
        }
    
        return $html;
    }

    private static function renderJsonSearch(array $json): string {
        $parsedown = new Parsedown();

        $markdown = $json['advise']['changes'];

        $html = $parsedown->text($markdown);

        return $html;
    }  
}