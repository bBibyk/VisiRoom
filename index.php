<?php

require_once('controller/AnalysisController.php');
require_once('controller/ContactController.php');
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

session_start();

$controller::$action($param);
