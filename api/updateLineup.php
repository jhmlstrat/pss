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

    include_once '../pss/Game.php';

    $year = isset($data->year) ? $data->year : die();
    //error_log($year.PHP_EOL,3,'error_log');
    $game = isset($data->game) ? $data->game : die();
    //error_log(json_encode($game).PHP_EOL,3,'error_log');
    $team = $game->home->name;
    //error_log($team.PHP_EOL,3,'error_log');
    $gn = $game->home->gameNumber;
    //error_log($gn.PHP_EOL,3,'error_log');
    $g = \Scoring\Game::findGameforTeam($year, $team, $gn);
    //error_log($g->toString());

    for ($i = 0; $i < 2; $i++) {
        if ($i == 0) {
            $side = $game->visitor;
        } else {
            $side = $game->home;
        }
        //error_log(json_encode($side).PHP_EOL,3,'error_log');
        $li = $g->lineup_[$i];
        //error_log($li->toString());
        for ($j=0; $j < count($side->lineup); $j++) {
            //error_log(print_r($li->getHitters($j),true));
            //error_log(print_r($side->lineup[$j],true));
            $gh = $li->getHitters($j);
            //error_log(":" . $j . ":" . count($gh) . " - " . count($side->lineup[$j]));
            if (count($gh) != count($side->lineup[$j])) {
                //error_log('Add ' . $j);
                $play = \ProjectScoresheet\Player::initial(
                    $side->lineup[$j][count($side->lineup[$j])-1]->player->name,
                    null
                );
                //error_log('Add ' . $i . ":" . $j . " - " . $play->toString() . " + " . $side->lineup[$j][count($side->lineup[$j])-1]->player->positions[0]->position->pos);
                $g->battingOrder(
                    $i,
                    $j,
                    $play,
                    \ProjectScoresheet\Position::position(
                        $side->lineup[$j][count(
                            $side->lineup[$j]
                        )-1]->player->positions[0]->position->pos
                    )
                );
                //error_log('Add ' . $g->toString());
            } else {
                $o = count($gh) - 1;
                if ($o >= 0) {
                  $hitter = $gh[$o];
                  //error_log(print_r($hitter,true));
                  $pc = count($hitter->positions);
                  $pl = $side->lineup[$j][count($side->lineup[$j])-1]->player;
                  //error_log(print_r($hitter->positions[count($hitter->positions)-1]->position,true));
                  //error_log(print_r(\ProjectScoresheet\Position::position($pl->positions[count($pl->positions)-1]->position->pos),true));
                  if ($hitter->positions[count($hitter->positions)-1]->position != \ProjectScoresheet\Position::position($pl->positions[count($pl->positions)-1]->position->pos)) {
                    //error_log(print_r($hitter->name,true));
                    $g->move($i, $j, \ProjectScoresheet\Position::position($pl->positions[count($pl->positions)-1]->position->pos));
                  }
                }
            }
        }
        //error_log(count($li->getPitchers()));
        //error_log(count($side->rotation));
        if (count($li->getPitchers()) != count($side->rotation)) {
            //error_log(json_encode($side->rotation[count($side->rotation)-1]));
            $play = \ProjectScoresheet\Player::initial(
                $side->rotation[count($side->rotation)-1]->player->name,
                null
            );
            $g->pitcher($i, $play);
        }
    }
    //$g->save();
    \Scoring\Game::save($year, json_decode($g->toString()));
    //error_log($g->toString());
    http_response_code(201);
}
?>

//$year = isset($_GET['year']) ? $_GET['year'] : die();
//if (isset($_GET['team'])) {
  //$team = $_GET['team'];
  //$game = isset($_GET['game']) ? $_GET['game'] : die();
  //if ($g == null) {
    //http_response_code(404);
  //} else {
    //http_response_code(200);
    //print($g->toString());
  //}
//}

