<?php
//if ($_SERVER['HTTP_X_AUTHORIZATION'] != 'TooManyMLs') return;
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// get posted data
$team = isset($_GET['team']) ? $_GET['team'] : die();
//$team='pit';

include_once '../pss/Schedule.php';
include_once 'config.php';

$conf = new Config();
$config = json_encode($conf->config);
$year = $conf->config['current_year'];
if (isset($_GET['year'])) $year = $_GET['year'];
//$year=2018;

$schedule = new \Scoring\Schedule($year);
print($schedule->getSchedule($team));
?>
