<?php

  namespace Scoring;

  require_once 'ProjectScoresheet.php';
  require_once 'Rosters.php';

class Game
{
    private $_year;
    public $team_;
    public $gameNumber_;
    public $runs_;
    public $hits_;
    public $errors_;
    public $innings_;
    public $date_;
    public $starter_;
    public $winner_;
    public $loser_;
    public $save_;
    public $day_;
    public $bps_;
    public $bphr_;
    public $final_;
    public $seriesComplete_;
    private $_ssLoaded_;
    private $_ss_;

    public function __construct($year = 2017)
    {
        $this->_year = $year;
        $this->team_ = [];
        $this->team_[0] = '';
        $this->team_[1] = '';
        $this->gameNumber_ = [];
        $this->gameNumber_[0] = 0;
        $this->gameNumber_[1] = 0;
        $this->runs_ = [];
        $this->runs_[0] = 0;
        $this->runs_[1] = 0;
        $this->hits_ = [];
        $this->hits_[0] = 0;
        $this->hits_[1] = 0;
        $this->errors_ = [];
        $this->errors_[0] = 0;
        $this->errors_[1] = 0;
        $this->innings_ = 9;
        $this->date_ = '';
        $this->starter_ = [];
        $this->starter_[0]='';
        $this->starter_[1]='';
        $this->winner_ = '';
        $this->loser_ = '';
        $this->save_ = '';
        $this->day_ = false;
        $this->bps_ = [];
        $this->bps_[0]=0;
        $this->bps_[1]=0;
        $this->bphr_ = [];
        $this->bphr_[0]=0;
        $this->bphr_[1]=0;
        $this->final_ = false;
        $this->seriesComplete_ = false;
        $this->_ssLoaded_ = false;
        $this->_ss_ = null;
    }

    public function toString()
    {
        $results = '{';
        $results .= '"away":{';
        $results .= '"team":"' . $this->team_[0] . '"';
        $results .= ',"gameNumber":"' . intval($this->gameNumber_[0]) . '"';
        $results .= ',"runs":"' . intval($this->runs_[0]) . '"';
        $results .= ',"hits":"' . intval($this->hits_[0]) . '"';
        $results .= ',"errors":"' . intval($this->errors_[0]) . '"';
        $results .= ',"starter":"' . $this->starter_[0] . '"';
        $results .= '},"home":{';
        $results .= '"team":"' . $this->team_[1] . '"';
        $results .= ',"gameNumber":"' . ($this->gameNumber_[1] == -1 ? 'DO' : 
            intval($this->gameNumber_[1])) . '"';
        $results .= ',"runs":"' . intval($this->runs_[1]) . '"';
        $results .= ',"hits":"' . intval($this->hits_[1]) . '"';
        $results .= ',"errors":"' . intval($this->errors_[1]) . '"';
        $results .= ',"starter":"' . $this->starter_[1] . '"';
        $results .= '}';
        $results .= ',"innings":"' . intval($this->innings_) . '"';
        $results .= ',"date":"' . $this->date_ . '"';
        $results .= ',"scoreSheet":' . ($this->hasScoreSheet() ? "true" : "false");
        $results .= ',"winner":"' . $this->winner_ . '"';
        $results .= ',"loser":"' . $this->loser_ . '"';
        $results .= ',"save":"' . $this->save_ . '"';
        $results .= ',"day":' . ($this->day_ ? "true" : "false");
        $results .= ',"bps":{"left":"' . $this->bps_[0] . '","right":"' . 
            $this->bps_[1] . '"}';
        $results .= ',"bphr":{"left":"' . $this->bphr_[0] . '","right":"' . 
            $this->bphr_[1] . '"}';
        $results .= ',"seriesComplete":' . ($this->seriesComplete_ ? "true" : 
            "false");
        $results .= ',"final":' . ($this->final_ ? "true" : "false");
        $results .= '}';
        return $results;
    }
    public function json()
    {
        return json_decode($this->toString());
    }
    private function _scoreSheetName()
    {
        return '../data/' . $this->_year . '/' . strtoupper($this->team_[0]) . 
            sprintf('%03d', $this->gameNumber_[0]).  strtoupper($this->team_[1]) . 
            sprintf('%03d', $this->gameNumber_[1]) . '.pss';
    }
    public function hasScoreSheet()
    {
        return file_exists($this->_scoreSheetName());
    }
    public function getScoreSheet()
    {
        // TBD
        return '{}';
    }
    public function loadScoreSheet()
    {
        if (! $this->hasScoreSheet()) {
            return;
        }
        $inst = \ProjectScoresheet\ProjectScoresheet::fromString(
            file_get_contents($this->_scoreSheetName())
        );
        if (isset($inst->away->rotation) and count($inst->away->rotation) != 0) {
            $this->starter_[0] = $inst->away->rotation[0];
        }
        if (isset($inst->home->rotation) and count($inst->home->rotation) != 0) {
            $this->starter_[1] = $inst->home->rotation[0];
        }
        if ($inst->date_ != "") {
            $this->date_ = $inst->date_;
        }
        $sit = $inst->getSituation();
        $this->runs_[0] = $sit->runs[0];
        $this->runs_[1] = $sit->runs[1];
        $this->hits_[0] = $sit->hits[0];
        $this->hits_[1] = $sit->hits[1];
        $this->errors_[0] = $sit->errors[0];
        $this->errors_[1] = $sit->errors[1];
        $this->innings_ = $sit->inning;
        $this->final_ = $sit->gameOver();
        $config = [];
        $json = file_get_contents("../data/config.json");
        $confs = json_decode($json, true);
        foreach ($confs['years'] as $conf) {
            if ($conf['year'] == $this->_year) {
                $config = $conf;
            }
        }
        foreach ($config['teams'] as $key => $team) {
            if (strtolower($team['team']) == strtolower($this->team_[1])) {
                //error_log(print_r($team,true).PHP_EOL,3,'error_log');
                $tw = $team['weather']['values'][strtolower($inst->weather_)];
                $this->bps_[0]=strval($tw['bps']['left']);
                $this->bps_[1]=strval($tw['bps']['right']);
                $this->bphr_[0]=strval($tw['bphr']['left']);
                $this->bphr_[1]=strval($tw['bphr']['right']);
                if ($team['weather']['robbing']['left'] == true) {
                    $this->bphr_[0] .= '*';
                }
                if ($team['weather']['robbing']['right'] == 1) {
                    $this->bphr_[1] .= '*';
                }
            }
        }
    }
    public static function createScoreSheet($year, $away, $agame, 
        $home, $hgame, $date, $weather
    ) {
        $g = new Game($year);
        $g->team_[0] = $away;
        $g->team_[1] = $home;
        $g->gameNumber_[0] = $agame;
        $g->gameNumber_[1] = $hgame;
        $inst = new \ProjectScoresheet\ProjectScoresheet();
        $inst->teamName_[0] = $away;
        $inst->teamName_[1] = $home;
        $inst->gameNumber_[0] = $agame;
        $inst->gameNumber_[1] = $hgame;
        $inst->date_ = $date;
        $inst->weather_ = $weather;
        $rosters = new \Jhml\Rosters($year, false, false);
        $ar = $rosters->getRoster($away);
        foreach ($ar->batters as $batter) {
            $tmt = NULL;
            foreach ($batter->moves as $move) {
                 if ($move->gameNumber <= $agame) {
                     $tmt = $move->moveType;
                 }
            }
            if (is_null($tmt)
                || $tmt == \Scoring\MoveType::TO_MAJORS 
                || $tmt == \Scoring\MoveType::OFF_DL
            ) {
                array_push($inst->roster_[0], $batter->player->name);
            }
        }
        foreach ($ar->pitchers as $pitcher) {
            $tmt = NULL;
            foreach ($pitcher->moves as $move) {
                 if ($move->gameNumber <= $agame) {
                     $tmt = $move->moveType;
                 }
            }
            if (is_null($tmt)
                || $tmt == \Scoring\MoveType::TO_MAJORS 
                || $tmt == \Scoring\MoveType::OFF_DL
            ) {
                array_push($inst->roster_[0], $pitcher->player->name);
            }
        }
        $hr = $rosters->getRoster($home);
        foreach ($hr->batters as $batter) {
            $tmt = NULL;
            foreach ($batter->moves as $move) {
                 if ($move->gameNumber <= $hgame) {
                     $tmt = $move->moveType;
                 }
            }
            if (is_null($tmt)
                || $tmt == \Scoring\MoveType::TO_MAJORS 
                || $tmt == \Scoring\MoveType::OFF_DL
            ) {
                array_push($inst->roster_[1], $batter->player->name);
            }
        }
        foreach ($hr->pitchers as $pitcher) {
            $tmt = NULL;
            foreach ($pitcher->moves as $move) {
                 if ($move->gameNumber <= $hgame) {
                     $tmt = $move->moveType;
                 }
            }
            if (is_null($tmt)
                || $tmt == \Scoring\MoveType::TO_MAJORS 
                || $tmt == \Scoring\MoveType::OFF_DL
            ) {
                array_push($inst->roster_[1], $pitcher->player->name);
            }
        }
        //error_log($inst->toString().PHP_EOL,3,'error_log');
        file_put_contents($g->_scoreSheetName(), $inst->toString());
    }
    public static function getGameFromScoreSheet($year, $away, $agame, $home, $hgame)
    {
        $g = new Game($year);
        $g->team_[0] = $away;
        $g->team_[1] = $home;
        $g->gameNumber_[0] = $agame;
        $g->gameNumber_[1] = $hgame;
        $g->loadScoreSheet();
        return $g;
    }
    public static function findGameforTeam($year, $team, $game)
    {
        $pss = glob(
            '../data/'. $year . '/*' . strtoupper($team) . 
            sprintf('%03d', $game). '*.pss'
        );
        if (count($pss) != 1) {
            return null;
        }
        $g = new Game($year);
        $fn = str_replace('.pss', '', preg_replace('/.*\//', "", $pss[0]));
        $g->team_[0] = substr($fn, 0, 3);
        $g->gameNumber_[0] = intval(substr($fn, 3, 3));
        $g->team_[1] = substr($fn, 6, 3);
        $g->gameNumber_[1] = intval(substr($fn, 9, 3));
        $inst = \ProjectScoresheet\ProjectScoresheet::fromString(
            file_get_contents($g->_scoreSheetName())
        );
        return $inst;
    }
    public static function save($year, $game)
    {
        $g = Game::getGameFromScoreSheet(
            $year, $game->visitor->name, $game->visitor->gameNumber, 
            $game->home->name, $game->home->gameNumber
        );
        $inst = \ProjectScoresheet\ProjectScoresheet::fromString(json_encode($game));
        file_put_contents($g->_scoreSheetName(), $inst->toString());
    }
}

?>
