<?php
#if ($_SERVER['HTTP_X_AUTHORIZATION'] != 'TooManyMLs') return;
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// get posted data
$team = isset($_GET['team']) ? $_GET['team'] : die();

include_once '../pss/Rotation.php';
include_once 'config.php';

$conf = new Config();
$config = json_encode($conf->config);
$year = isset($_GET['year']) ? $_GET['year'] : $conf->config['current_year'];

$rotation = new \Jhml\Rotation($year,$team);

print($rotation->getRotation());
?>
