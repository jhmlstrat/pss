<?php

  namespace Scoring;

  require_once 'ProjectScoresheet.php';
  require_once 'Rosters.php';
  require_once 'BatterStats.php';
  require_once 'FielderStats.php';
  require_once 'PitcherStats.php';

class RunnerInfo {
    public $runner;
    public $responsible;
    public $earned;
    public $advancedOnError;

    public function __construct($runner, $responsible, $earned = true, $advancedOnError = 0) {
        $this->runner = $runner;
        $this->responsible = $responsible;
        $this->earned = $earned;
        $this->advancedOnError = $advancedOnError;
    }
}

class Game
{
    private $_year;
    public $team_;
    public $fullTeamName_;
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
    public $_ss_;

    public function __construct($year = 2017)
    {
        $this->_year = $year;
        $this->team_ = [];
        $this->team_[0] = '';
        $this->team_[1] = '';
        $this->fullTeamName_ = [];
        $this->fullTeamName_[0] = '';
        $this->fullTeamName_[1] = '';
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
        //print($this->_scoreSheetName() . "\n");
        $this->_ss_ = \ProjectScoresheet\ProjectScoresheet::fromString(
            file_get_contents($this->_scoreSheetName())
        );
        $this->_ssLoaded_ = true;
        if (isset($this->_ss_->away->rotation) and count($this->_ss_->away->rotation) != 0) {
            $this->starter_[0] = $this->_ss_->away->rotation[0]->name;
        }
        if (isset($this->_ss_->home->rotation) and count($this->_ss_->home->rotation) != 0) {
            $this->starter_[1] = $this->_ss_->home->rotation[0]->name;
        }
        if ($this->_ss_->date_ != "") {
            $this->date_ = $this->_ss_->date_;
        }
        $sit = $this->_ss_->getSituation();
        $this->runs_[0] = $sit->runs[0];
        $this->runs_[1] = $sit->runs[1];
        $this->hits_[0] = $sit->hits[0];
        $this->hits_[1] = $sit->hits[1];
        $this->errors_[0] = $sit->errors[0];
        $this->errors_[1] = $sit->errors[1];
        $this->innings_ = $sit->inning;
        $this->final_ = $sit->gameOver();
        $this->seriesComplete_ = $sit->seriesComplete;
        $this->starter_[0] = $this->_ss_->lineup_[0]->getPitchers()[0]->name;
        $this->starter_[1] = $this->_ss_->lineup_[1]->getPitchers()[0]->name;
        $config = [];
        $json = file_get_contents("../data/config.json");
        $confs = json_decode($json, true);
        foreach ($confs['years'] as $conf) {
            if ($conf['year'] == $this->_year) {
                $config = $conf;
            }
        }
        foreach ($config['teams'] as $key => $team) {
            if (strtolower($team['team']) == strtolower($this->team_[0])) {
                $this->fullTeamName_[0] = $team['city'] . ' ' . $team['team_name'];
            }
            if (strtolower($team['team']) == strtolower($this->team_[1])) {
                $this->fullTeamName_[1] = $team['city'] . ' ' . $team['team_name'];
                //error_log(print_r($team,true).PHP_EOL,3,'error_log');
                $tw = $team['weather']['values'][strtolower($this->_ss_->weather_)];
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
        if (! $this->final_) return;
        // print("Pitchers\n");
        // Winner, loser & saves
        $curr = []; $curr[0] = 0; $curr[1] = 0;
        $runs = []; $runs[0] = 0; $runs[1] = 0;
        $b4Done = []; $b4Done[0] = false; $b4Done[1] = false;
        $outs = 0;
        $side = 0;
        $wSide = 0; if ($this->runs_[0] < $this->runs_[1]) $wSide = 1;
        $lSide = ($wSide + 1)%2;
        $lastOuts = 0;
        $last = null;
        $runners = [null, null, null];
        $wps = $this->_ss_->lineup_[$wSide]->getPitchers();
        if (count($wps) > 1) {
            $last = $wps[count($wps) - 1]->positions[0]->getWhen()->when();
        }
        $lastLead = 0;
        $saveSit = false;
        $lastP = false;
        $junk = 0;
        // print_r($last);
        while ($curr[0] <= count($this->_ss_->results_[0]) && $curr[1] <= count($this->_ss_->results_[1])) {
            $found_outs = false;
            // print ($side . ": " . $curr[0] . ' - ' . $curr[1] . "\n");
            if ($curr[$wSide] == $last[$wSide] && $curr[$lSide] == $last[$lSide]) {
                $lastP = true;
                $lastLead = $runs[$wSide] - $runs[$lSide];
                $noRun = 0;
                if ($runners[2] != null) $noRun ++;
                if ($runners[1] != null) $noRun ++;
                if ($runners[0] != null) $noRun ++;
                if (($lastLead - $noRun - 2) <= 0) $saveSit = true;
            }
            $oside = ($side + 1)%2;
            foreach ($this->_ss_->lineup_[$side]->getHitters($curr[$side]%9) as $sh) {
                $entered = $sh->positions[0]->getWhen()->when();
                if ($entered[0] <= $curr[0] && $entered[1] <= $curr[1]) {
                    $cb = new BatterStats($sh->name);
                }
            }
            foreach ($this->_ss_->lineup_[$oside]->getPitchers() as $op) {
                $entered = $op->positions[0]->getWhen()->when();
                if ($entered[0] <= $curr[0] && $entered[1] <= $curr[1]) {
                    $cp = new PitcherStats($op->name);
                }
            }
            // print($cb->name . ' vs ' . $cp->name . "\n");
            if (! $b4Done[$side]) {
                $b4Done[$side] = true;
                $before = $this->_ss_->results_[$side][$curr[$side]]->before;
                foreach (explode(";",$before) as $b4) {
                    if ($b4 != "") {
                        if (substr_count($b4,'x') > 0) $found_outs = true;
                        list ($movement, $reason) = explode("/",$b4);
                        if (substr_count($b4,'-H') > 0) {
                            $runsb4 = $runs[$side];
                            $runs[$side] += substr_count($b4,'-H');
                            if ($runsb4 <= $runs[$oside] && $runs[$side] > $runs[$oside]) {
                                foreach ($this->_ss_->lineup_[$side]->getPitchers() as $p) {
                                    $w = $p->positions[0]->getWhen()->when();
                                    if ($w[$side] <= $curr[$side] && $w[$oside] <= $curr[$oside]) $this->winner_ = $p->name;
                                }
                                $scorers = array();
                                if (substr_count($b4,'3-H') > 0) array_push($scorers, $runners[2]);
                                if (substr_count($b4,'2-H') > 0) array_push($scorers, $runners[1]);
                                if (substr_count($b4,'1-H') > 0) array_push($scorers, $runners[0]);
                                $runDiff = $runs[$oside] - $runsb4;
                                if (count($scorers) >= $runDiff + 1) {
                                    $this->loser_ = $scorers[$runDiff]->responsible->name;
                                } else {
                                    print("loadScoresheet - scoring from an unoccupied base - " . $b4 . "\n");
                                }
                            }
                        }
                        $this->handleRunners($runners, $movement, $cb, $cp, $outs, true, 0, $junk);
                        if ($lastP && $side == $lSide) $lastOuts += substr_count($b4,'x');   //  OK to add 0, so removed the extra check
                    }
                }
            }
            if (! ($found_outs && $outs == 0)) {
                $b4Done[$side] = false;
                $after = $this->_ss_->results_[$side][$curr[$side]]->after;
                if ($after != "") {
                    if (substr_count($after,'x') > 0) $found_outs = true;
                    if (substr_count($after,'-H') > 0) {
                        $runsb4 = $runs[$side];
                        $runs[$side] += substr_count($after,'-H');
                        if ($runsb4 <= $runs[$oside] && $runs[$side] > $runs[$oside]) {
                                foreach ($this->_ss_->lineup_[$side]->getPitchers() as $p) {
                                    $w = $p->positions[0]->getWhen()->when();
                                    if ($w[$side] <= $curr[$side] && $w[$oside] <= $curr[$oside]) $this->winner_ = $p->name;
                                }
                                $scorers = array();
                                if (substr_count($after,'3-H') > 0) array_push($scorers, $runners[2]);
                                if (substr_count($after,'2-H') > 0) array_push($scorers, $runners[1]);
                                if (substr_count($after,'1-H') > 0) array_push($scorers, $runners[0]);
                                if (substr_count($after,'B-H') > 0) array_push($scorers, new RunnerInfo($cb, $cp));
                                $runDiff = $runs[$oside] - $runsb4;
                                if (count($scorers) >= $runDiff + 1) {
                                    $this->loser_ = $scorers[$runDiff]->responsible->name;
                                } else {
                                    print("loadScoresheet - scoring from an unoccupied base - " . $after . "\n");
                                }
                        }
                    }
                    $this->handleRunners($runners, $after, $cb, $cp, $outs, false, 0, $junk);
                    if ($lastP && $side == $lSide) $lastOuts += substr_count($after,'x');   //  OK to add 0, so removed the extra check
                }
                $curr[$side]++;
            }
            if ($runs[$side] == $runs[$oside]) {
                $this->winner_ = ''; $this->loser = ''; $this->save_ = '';
            }
            if ($found_outs && $outs == 0) {
                $side = $oside;
            }
        }
        // print("saveSit: " . ($saveSit ? "true" : "false") . ", lastLead: " . $lastLead . ", lastOuts: " . $lastOuts .  "\n");
        if ($saveSit || ($lastLead <= 3 && $lastLead > 0 && $lastOuts >= 3) || ($lastLead > 0 && $lastOuts >= 9)) {
            if ($this->winner_ != $wps[count($wps) - 1]->name) {
                $this->save_ = $wps[count($wps) - 1]->name;
            }
        }
        // print ("Winner - " . $this->winner_ . "\n");
        // print ("Loser - " . $this->loser_ . "\n");
        // print ("Save - " . $this->save_ . "\n");
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
    public static function fromStatFileString($str1,$str2) {
        $pieces1 = explode("~", $str1);
        $pieces2 = explode("~", $str2);
        $inst = new self();
        $inst->fullTeamName_[0] = $pieces1[0];
        $inst->fullTeamName_[1] = $pieces2[0];
        $inst->runs_[0] = $pieces1[1];
        $inst->runs_[1] = $pieces2[1];
        $inst->hits_[0] = $pieces1[2];
        $inst->hits_[1] = $pieces2[2];
        $inst->errors_[0] = $pieces1[3];
        $inst->errors_[1] = $pieces2[3];
        $inst->innings_ = $pieces1[4];
        $inst->final_ = true;
        return $inst;
    }
    public function toStatFileString() {
        if ($this->final_) {
            $rtn = $this->fullTeamName_[0] . "~";
            $rtn .= $this->runs_[0] . "~";
            $rtn .= $this->hits_[0] . "~";
            $rtn .= $this->errors_[0] . "~";
            if ($this->_ss_ == null) {
                $rtn .= $this->innings_ . ":";
            } else {
                $sit = $this->_ss_->getSituation();
                if ($sit->side == 0) $rtn .= strval($this->innings_-1) . ":";
                else $rtn .= $this->innings_ . ":";
            }
            $rtn .= $this->fullTeamName_[1] . "~";
            $rtn .= $this->runs_[1] . "~";
            $rtn .= $this->hits_[1] . "~";
            $rtn .= $this->errors_[1];
        } else $rtn='';
        return $rtn;
    }
    public static function parseStatFileLine($line) {
        $pieces = explode(":", trim($line));
        $game = $pieces[0];
        $g = Game::fromStatFileString($pieces[1],$pieces[2]);
        return [$game, $g];
    }
    private function print_runners($cb, $cp, $runners, $outs) {
        return;
        print "Batter - " . $cb->name . " ,3 - ";
        if ($runners[2] == null) {
            print("empty");
        } else {
            print($runners[2]->runner->name);
        }
        print " ,2 - ";
        if ($runners[1] == null) {
            print("empty");
        } else {
            print($runners[1]->runner->name);
        }
        print " ,1 - ";
        if ($runners[0] == null) {
            print("empty");
        } else {
            print($runners[0]->runner->name);
        }
        print(", outs = " . $outs . "\n");
    }
    private function handleRunners(&$runners, $movement, $cb, $cp, &$outs, $error, $advancedOnError, &$errorsThisInning) {
        $this->print_runners($cb, $cp, $runners, $outs);
        //print($movement . "\n");
        //print($cb->name . " vs " . $cp->name . "\n");
        //print_r($cb);
        //print_r($cp);
        foreach (explode(",",$movement) as $move) {
            //print("-- " . $move . " --\n");
            $base = substr($move,0,1);
            if (preg_match("/-H$/",$move)) {
                // print("RUN: -- " . $move . " --\n");
                // Figure out runner, add to his runs scored, and clear the base
                // FIX ER HERE
                if ($base == "B") {
                    $cb->run ++;
                    $cb->rbi ++;
                    $cp->er ++;
                    $cp->r ++;
                } else {
                    $cb->rbi ++;
                    $bn = intval($base) - 1;
                    if ($bn < 0 or $runners[$bn] == null ) {
                        print("handle_runners - scoring from an unoccupied base - " . $move . "\n");
                    } else {
                        $runners[$bn]->runner->run++;
                        $runners[$bn]->responsible->r++;
                        if ($runners[$bn]->earned && ($outs + $errorsThisInning) < 3) {
                            $runners[$bn]->responsible->er++;
                        }
                        $runners[$bn] = null;
                    }
                }
            } elseif (substr($move,1,1) == '-') {
                $newBase = intval(substr($move,2,1)) - 1;
                if ($runners[$newBase] != null ){
                    print("handle_runners - moving to an occupied base - " . $move . "\n");
                } else {
                    if ($base == "B") {
                        $br = new RunnerInfo($cb, $cp, ! $error, $advancedOnError);
                        $runners[$newBase] = $br;
                    } else {
                        $bn = intval($base) - 1;
                        if ($bn < 0 or $runners[$bn] == null ) {
                            print("handle_runners - moving from an occupied base - " . $move . "\n");
                        } else {
                            $runners[$newBase] = $runners[$bn];
                            $runners[$bn] = null;
                        }
                    }
                }
            } elseif (substr($move,1,1) == 'x') {
                $outs ++;
                if ($base != "B") {
                    $bn = intval($base) - 1;
                    $runners[$bn] = null;
                }
            } else {
                print("UNHANDLED handle_runner: " . $move . "\n");
            }
        }
        if ($outs >= 3) {
            $outs = 0;
	    $runners[0] = null; $runners[1] = null; $runners[2] = null;
	    $errorsThisInning = 0;
        }

        $this->print_runners($cb, $cp, $runners, $outs);
        // print("-------------------------------\n");
    }

    private function addError($pos, $lineup, &$e) {
    }

    public function gameStats() {
        // print($this->toString() . "\n"); 
        print($this->_scoreSheetName() . "\n"); 
        $bs = array();
        $bs[0] = array();
        $bs[1] = array();
        $es = array();
        $es[0] = array();
        $es[1] = array();
        $is = array();
        $is[0] = array();
        $is[1] = array();
        $ms = array();
        $ms[0] = array();
        $ms[1] = array();
        $ps = array();
        $ps[0] = array();
        $ps[1] = array();

        $tb = array();
        $tb[0] = array();
        $tb[1] = array();
        for ($i=0; $i < 2; $i++) {
            for ($j=0; $j < 9; $j++) {
                $tb[$i][$j] = array();
                foreach ($this->_ss_->lineup_[$i]->getHitters($j) as $h) {
                    $bn = new BatterStats($h->name);
                    $bn->beginPlay = $h->positions[0]->getWhen()->when();
                    array_push($tb[$i][$j], $bn);
                }
            }
            $starter = true;
            foreach ($this->_ss_->lineup_[$i]->getPitchers() as $p) {
                $pn = new PitcherStats($p->name);
                $pn->beginPlay = $p->positions[0]->getWhen()->when();
                if ($starter) {
                    $starter = false;
                    $pn->start = 1;
                }
                if ($p->name == $this->winner_) $pn->w = 1;
                if ($p->name == $this->loser_) $pn->l = 1;
                if ($p->name == $this->save_) $pn->s = 1;
                array_push($ps[$i], $pn);
            }
        }
        for ($i=0; $i < 2; $i++) {
            $outs = 0;
            $runners = [null, null, null];
            $errorsThisInning = 0;
            foreach ($this->_ss_->results_[$i] as $j => $r) {
                $cb = null; $cp = null;
                $found_outs = false;
                // Figure out current batter, current pitcher and runners
                foreach ($tb[$i][$j%9] as $b) {
                    if ($b->beginPlay[$i] <= $j) $cb = $b;
                }
                foreach ($ps[($i+1)%2] as $p) {
                    if ($p->beginPlay[$i] <= $j) $cp = $p;
                }
                if ($r->before != "") {
                    // print("Before: " . $r->before . ", outs - " . $outs . "\n");
                    foreach (explode(";",$r->before) as $b4) {
                        $error = false;
                        $advancedOnError = 0;
                        list ($movement, $reason) = explode("/",$b4);
                        if ($reason == "WP") {
                            $cp->wildp ++;
                        } else if ($reason == "BK") {
                            $cp->balk ++;
                        } else if ($reason == "PB") {
                            // Nada to do
                        } else if ($reason == "SB") {
                            // print($b4 . "\n");
                            foreach (explode(",",$movement) as $move) {
                                $base = intval(substr($move,0,1)) - 1;
                                if ($runners[$base] == null) {
                                    print("SB from an empty base\n");
                                } else {
                                    $runners[$base]->runner->sb++;
                                }
                            }
                        } else if ($reason == "CS" || preg_match("/PO-/", $reason)) {
                            // print($b4 . "\n");
                            foreach (explode(",",$movement) as $move) {
                                $base = intval(substr($move,0,1)) - 1;
                                $x = substr($move,1,1);
                                if ($x == 'x') {
                                    if ($runners[$base] == null) {
                                        print("CS from an empty base\n");
                                    } else {
                                        $runners[$base]->runner->cs++;
                                        $cp->addOut();
                                    }
                                }
                            }
                            // TBD: Runner(s)?
                        } else {
                            print("UNHANDLED B4: " . $b4 . "--\n");
                        }
                        // print($movement . ", outs - " . $outs . "\n");
                        if (substr_count($movement,'x') > 0) $found_outs = true;
                        $this->handleRunners($runners, $movement, $cb, $cp, $outs, $error, $advancedOnError, $errorsThisInning);
                    }
                    if (preg_match("/E\d/",$r->before)) {
                        print(" Error b4: " . $r->before . "\n");
                    }
                }
                $error = false;
                $advancedOnError = 0;
                if (substr_compare($r->during, "S", 0) == 0 ||
                    substr_compare($r->during, "S(", 0, 2) == 0 ||
                    substr_compare($r->during, "S;", 0, 2) == 0) {
                    $cb->ab ++;
                    $cb->hit ++;
                    $cp->h ++;
                } else if (substr_compare($r->during, "D", 0) == 0 ||
                    substr_compare($r->during, "D;", 0, 2) == 0) {
                    $cb->ab ++;
                    $cb->hit ++;
                    $cb->d ++;
                    $cp->h ++;
                } else if (substr_compare($r->during, "T", 0) == 0 ||
                    substr_compare($r->during, "T;", 0, 2) == 0) {
                    $cb->ab ++;
                    $cb->hit ++;
                    $cb->t ++;
                    $cp->h ++;
                } else if (substr_compare($r->during, "HR", 0, 2) == 0) {
                    $cb->ab ++;
                    $cb->hit ++;
                    $cb->hr ++;
                    $cp->h ++;
                    $cp->hr ++;
                } else if (preg_match('/^K/',$r->during)) {
                    $cb->ab ++;
                    $cb->k ++;
                    $cp->addOut();
                    $cp->k ++;
                } else if ($r->during == "BB" ) {
                    $cb->bb ++;
                    $cp->bb ++;
                } else if ($r->during == "HBP" ) {
                    $cb->hbp ++;
                    $cp->hbp ++;
                } else if (preg_match("/\/DP$/",$r->during)) {
                    $cb->ab ++;
                    $cb->dp ++;
                    $cp->addOut();
                    $cp->addOut();
                } else if (preg_match("/\/SAC$/",$r->during)) {
                    $cb->sac ++;
                    $cp->addOut();
                } else if (preg_match("/\/SF$/",$r->during)) {
                    $cb->sf ++;
                    $cp->addOut();
                } else if (preg_match("/^[46]\(bp\)$/",$r->during)) {
                    $cb->ab ++;
                    $cb->bpsMissed ++;
                    $cp->addOut();
                    $cp->bpsMissed ++;
                } else if (preg_match("/^[789]\(bp\)$/",$r->during)) {
                    $cb->ab ++;
                    $cb->bphrMissed ++;
                    $cp->addOut();
                    $cp->bphrMissed ++;
                } else if (preg_match("/^\d$/",$r->during) or preg_match("/^\d-\d$/",$r->during)) {
                    $cb->ab ++;
                    $cp->addOut();
                } else if (preg_match("/E\d/",$r->during)) {
                    $cb->ab ++;
                } else {
                    print("UNHANDLED During - ");
                    print_r($r);
                }
                if (preg_match("/E\d/",$r->during)) {
                    // print("----------------\n" . $r->during . ": " . strpos($r->during,"E") . "\n");
                    if (preg_match("/^\d?E\d/",$r->during)) {
                        $errorsThisInning ++;
                    }
                    if (strpos($r->during,"E") == 0) {
                        $error = true;
                    }
                    $pos = strpos($r->during,"E") + 1;
                    $position = substr($r->during, $pos, 1);
                    // print("   - " . $position .  " - " . $j . "\n");
                    $oside = ($i + 1)%2;
                    // print_r($tb[$oside]);
                    // foreach ($tb[$oside] as $slot) {
                    //     print_r($slot);
                    // }
                    $guilty = null;
                    if ($position == '1') {
                        $guilty = "PITCHERS";
                    } else {
                        $player = [];
                        for ($k=0; $k < 9; $k++) {
                            foreach ($this->_ss_->lineup_[$oside]->getHitters($k) as $h) {
                                foreach ($h->positions as $p) {
                                    if ($p->position == $position) {
                                        $w = $p->getWhen()->when();
                                        // print($h->name . " - " . $p->position . " - " . $j . ":" . $w[$i] . "\n");
                                        if ($j > $w[$i]) {
                                            // print_r($h->name . "\n");
                                            // print_r($w);
                                            $play = (object)[
                                                "name" => $h->name,
                                                "when" => $w[$i],
                                            ];
                                            // print_r($play);
                                            array_push($player, $play);
                                        }
                                    }
                                }
                            }
                        }
                        usort($player, function($a,$b) {return $a->when <=> $b->when;});
                        $guilty = $player[count($player)-1]->name;
                        // print("----" . $player[count($player)-1]->name . "\n");
                    }
                    if ($guilty != null) {
                        // Loop through es and add one if found. Create if not
                        $found = false;
                        foreach ($es[$oside] as $field) {
                            if ($field->name == $guilty) {
                                $found = true;
                                $field->e ++;
                            }
                        }
                        if (! $found) {
                            $field = new FielderStats($guilty);
                            $field->e = 1;
                            array_push($es[$oside], $field);
                            if ($guilty == "PITCHERS") {
                                $pFound = false;
                                foreach ($bs[$oside] as $batter) {
                                    if ($batter->name == "PITCHERS") $pFound = true;
                                }
                                if (! $pfound) {
                                   $place = new BatterStats($guilty);
                                   array_push($bs[$oside], $place);
                                }
                            }
                        }
                    } else {
                            print("unassigned error " . $r->during . "\n");
                    }
                }
                if ($r->after != "") {
                    // print_r("After: " . $r->after . "\n");
                    if (substr_count($after,'x') > 0) $found_outs = true;
                    $this->handleRunners($runners, $r->after, $cb, $cp, $outs, $error, $advancedOnError, $errorsThisInning);
                }
            }
        }
        for ($i=0; $i < 2; $i++) {
            for ($j=0; $j < 9; $j++) {
                foreach ($tb[$i][$j] as $h) {
                    array_push($bs[$i],$h);
                }
            }
        }


        return [$bs[0], $es[0], $is[0], $ms[0], $ps[0], $bs[1], $es[1], $is[1], $ms[1], $ps[1]];
    }
}

?>
