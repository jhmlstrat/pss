<?php
if ($_SERVER['HTTP_X_AUTHORIZATION'] != 'TooManyMLs') {
    return;
}
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// get posted data
require_once '../pss/Game.php';
$year = isset($_GET['year']) ? $_GET['year'] : die();
if (isset($_GET['team'])) {
    $team = $_GET['team'];
    $game = isset($_GET['game']) ? $_GET['game'] : die();
    $g = \Scoring\Game::findGameforTeam($year, $team, $game);
    if ($g == null) {
        http_response_code(404);
    } else {
        http_response_code(200);
        print($g->toString());
    }
    //} else if (isset($_GET['away'])) {
    //} else {
}
