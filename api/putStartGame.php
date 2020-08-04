<?php
#if ($_SERVER['HTTP_X_AUTHORIZATION'] != 'TooManyMLs') return;
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

include_once 'config.php';
include_once '../pss/Game.php';
$conf = new Config();

// get posted data
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  //error_log(file_get_contents("php://input"),3,'error_log');
  //parse_str(file_get_contents("php://input"), $_PUT);
  $foo = json_decode(file_get_contents("php://input"));
  //error_log(json_encode($foo).PHP_EOL,3,'error_log');
  $data = isset($foo->data) ? $foo->data : die();
  //error_log(json_encode($data).PHP_EOL,3,'error_log');

  $year = isset($data->year) ? $data->year : die();
  $home = isset($data->home) ? $data->home : die();
  $hgame = isset($data->hgame) ? $data->hgame : die();
  $away = isset($data->away) ? $data->away : die();
  $agame = isset($data->agame) ? $data->agame : die();
  $date = isset($data->date) ? $data->date : die();
  $weather = isset($data->weather) ? $data->weather : die();
  if ($weather != 'good' && $weather != 'average' && $weather != 'bad') die();


  $gm = new \Scoring\Game($year);
  if ($gm->hasScoreSheet()) {
    http_response_code(204);
  } else {
    \Scoring\Game::createScoreSheet($year,$away,$agame,$home,$hgame,$date,$weather);
    http_response_code(201);
  }

}
?>
