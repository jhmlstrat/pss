<?php
#if ($_SERVER['HTTP_X_AUTHORIZATION'] != 'TooManyMLs') return;
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

include_once 'config.php';

$conf = new Config();
print(json_encode($conf->config));
?>

