<?php

require_once('controller/AnalysisController.php');
require_once('controller/InformationController.php');
require_once('controller/UserController.php');

define('ROOT',__dir__);
define('DEFAULT_CONTROLLER','analysis');
define('DEFAULT_ACTION','analysis');

$controller;
$action;

if(isset($_GET) && !empty($_GET))
{
    $controller = $_GET["controller"];
    $action = $_GET["action"];
}
else
{
    $controller = DEFAULT_CONTROLLER;
    $action = DEFAULT_ACTION;
}

$param = array();

foreach($_GET as $key=>$value)
{
    if(($key != 'controller') && ($key != 'action'))
    {
        $param[$key] = $value;
    }
}

ROOT.'/controller/'.$controller.'Controller.php';

$controller = $controller.'Controller';

$controller::$action($param);

/*// URL de la page à récupérer
$url = "https://seraphartgallery.com/";

// Initialiser une session cURL
$ch = curl_init();

// Configurer les options cURL
curl_setopt($ch, CURLOPT_URL, $url); // Définir l'URL à récupérer
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourner le résultat sous forme de chaîne
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Suivre les redirections, si elles existent
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Désactiver la vérification SSL (hôte)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Désactiver la vérification SSL (certificat)

// Exécuter la requête cURL et récupérer le contenu
$htmlContent = curl_exec($ch);

// Vérifier s'il y a une erreur
if (curl_errno($ch)) {
    echo "Erreur cURL : " . curl_error($ch);
} else {
     // Compter les balises <div>
     preg_match_all('/<div\b[^>]*>/i', $htmlContent, $matches);
     $divCount = count($matches[0]);
 
     // Afficher le code source et le nombre de balises <div>
     echo "<pre>" . htmlspecialchars($htmlContent) . "</pre>";
     echo "<p>Nombre de balises &lt;div&gt; : $divCount</p>";
}

// Fermer la session cURL
curl_close($ch);

// Le contenu HTML est maintenant dans la variable $htmlContent*/

