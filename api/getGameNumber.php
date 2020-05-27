<?php
if ($_SERVER['HTTP_X_AUTHORIZATION'] != 'TooManyMLs') return;
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// get posted data
$year = isset($_GET['year']) ? $_GET['year'] : die();
$team = isset($_GET['team']) ? $_GET['team'] : die();

include_once '../pss/Games.php';

$games = \Scoring\Games::getGames($year,$team);
print '{"year":"'. $year .'","team":"' . $team .'","gameNumber":"' . (count($games)+1) . '"}';
?>
