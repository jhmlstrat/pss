<?php

if ($_SERVER['HTTP_X_AUTHORIZATION'] != 'TooManyMLs') {
    return;
}
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// get posted data
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    //error_log(file_get_contents("php://input"),3,'error_log');
    //parse_str(file_get_contents("php://input"), $_PUT);
    $foo = json_decode(file_get_contents("php://input"));

    $data = isset($foo->data) ? $foo->data : die();
    //error_log(json_encode($data).PHP_EOL,3,'error_log');

    include_once '../pss/Rosters.php';

    $year = isset($data->year) ? $data->year : die();
    //error_log($year.PHP_EOL,3,'error_log');
    $team = isset($data->team) ? $data->team : die();
    //error_log($team.PHP_EOL,3,'error_log');
    $game = isset($data->game) ? $data->game : die();
    //error_log($game.PHP_EOL,3,'error_log');
    $moves = isset($data->moves) ? $data->moves : die();

    $rosters = new \Jhml\Rosters($year, false, false);

    foreach ($moves as $move) {
        //error_log($move->name.":".$move->moveType.PHP_EOL,3,'error_log');
        $rosters->addMove($team, $move->name, $game, $move->moveType);
    }

    $rosters->writeRosterFile();
    http_response_code(201);
}
