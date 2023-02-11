<?php

  namespace ProjectScoresheet;

/*
 * Project Scoresheet was developed in the 1980s to better document
 * baseball games, attempting to eliminate some of the ambiguity
 * in traditional scorekeeping. It appears to have been all but
 * abandoned, but fits the need for what I am attempting to do.
 *
 * It was documented at http://dcortesi.home.mindspring.com/scoring/,
 * now gone
 *
 * I initially intend to use a modified subset of this scoring system.
 *
 * Two data structures will be kept for each team (visitor = 0, home = 1)
 * First is the lineup which consists of 10 elements separated by :. The
 * first 9 of these are the batting order and the 10th is the pitching order.
 * Each of the first 9 elements consist of ; separated group of players.
 * Each player is a comma separated group with the first element being the
 * player's name, followed by groups of 2 items which are the at bat number
 * that they entered the game at a position and the position
 */

  require_once 'Lineup.php';
  require_once 'Result.php';
  require_once 'Situation.php';
class ProjectScoresheet
{
    public $teamName_;
    public $gameNumber_;
    public $lineup_;
    public $roster_;
    public $results_;
    public $date_ = "";
    public $startTime_ = "";
    public $duration_ = "";
    public $weather_ = "";
    public $situation_;
    private $_result;
    private $_plus = false;
    public $debug_ = false;
    public function __construct()
    {
        $this->teamName_ = array();
        $this->gameNumber_ = array();
        $this->lineup_ = array();
        $this->roster_ = array();
        $this->results_ = array();
        foreach (Side::sides() as $key => $side) {
            $this->teamName_[$side] = "";
            $this->gameNumber_[$side] = 0;
            $this->lineup_[$side] = new Lineup();
            $this->roster_[$side] = array();
            $this->results_[$side] = array();
        }
        $this->situation_ = new Situation();
        $this->_result = new Result();
        $this->situation_->betweenInnings = true;
    }
    public static function fromString($str)
    {
        //error_log('PSS:fromString');
        //error_log($str);
        $json = json_decode($str);
        //error_log(JSON_ERROR_SYNTAX);
        //error_log(json_last_error());
        //error_log(print_r($json,true));
        $v = $json->visitor;
        //error_log(print_r($v,true));
        $h = $json->home;
        //error_log(print_r($h,true));
        $inst = new self();
        //$inst->debug_ = true;
        //var_dump($json->visitor);
        $inst->teamName_[0] = $v->name;
        $inst->teamName_[1] = $h->name;
        $inst->gameNumber_[0] = $v->gameNumber;
        $inst->gameNumber_[1] = $h->gameNumber;
        // Lineup
        $inst->lineup_[0] = Lineup::fromString(
            '"lineup":' . json_encode($v->lineup) . ',"rotation":' . 
            json_encode($v->rotation)
        );
        $inst->lineup_[1] = Lineup::fromString(
            '"lineup":' . json_encode($h->lineup) . ',"rotation":' . 
            json_encode($h->rotation)
        );
        if ($v->roster != null) {
            foreach ($v->roster as $r) {
                array_push($inst->roster_[0], $r);
            }
        }
        if ($h->roster != null) {
            foreach ($h->roster as $r) {
                array_push($inst->roster_[1], $r);
            }
        }
        if ($v->results != null) {
            foreach ($v->results as $rslt) {
                $r = Result::fromString(json_encode($rslt));
                if ($r->during == "") {
                  $inst->situation_->side = 0;
                  $inst->_result = $r;
                } else {
                  array_push($inst->results_[0], $r);
                }
            }
        }
        //error_log(print_r($inst->results_[0],true));
        if ($h->results != null) {
            foreach ($h->results as $rslt) {
                $r = Result::fromString(json_encode($rslt));
                if ($r->during == "") {
                  $inst->situation_->side = 1;
                  $inst->_result = $r;
                } else {
                  array_push($inst->results_[1], $r);
                }
            }
        }
        //error_log(print_r($inst->results_[1],true));
        if (! empty($json->date)) {
            $inst->date_ = $json->date;
        }
        if (! empty($json->startTime)) {
            $inst->startTime_ = $json->startTime;
        }
        if (! empty($json->duration)) {
            $inst->duration_ = $json->duration;
        }
        if (! empty($json->weather)) {
            $inst->weather_ = $json->weather;
        }
        //$inst->debugOn();
        //error_log(print_r($inst->situation_,true));
	$inst->_updateSituation();
	$inst->situation_->seriesComplete = $json->situation->seriesComplete;
        //error_log(print_r($inst->situation_,true));
        return $inst;
    }
    public function toString()
    {
        $sit = $this->situation_;
        $doneResult = ($this->_result->before == "") 
            && ($this->_result->during == "") && ($this->_result->after == "");
        $rtn = '{';
        foreach (Side::sides() as $key => $side) {
            if ($side == Side::VISITOR) {
                $rtn .= '"visitor":{';
            } else {
                $rtn .= ',"home":{';
            }
            $rtn .= '"name":"' .  $this->teamName_[$side] . '"';
            $rtn .= ',"gameNumber":"' . $this->gameNumber_[$side] . '"';
            $rtn .= ',' . $this->lineup_[$side]->toString();
            $rtn .= ',"roster":[';
            for ($i = 0; $i < count($this->roster_[$side]); $i++) {
                if ($i > 0) {
                    $rtn .= ",";
                }
                $rtn .= '"' . $this->roster_[$side][$i] . '"';
            }
            $rtn .= '],"results":[';
            for ($i = 0; $i < count($this->results_[$side]); $i++) {
                if ($i > 0) {
                    $rtn .= ",";
                }
                //$rtn .= '"' . $this->results_[$side][$i]->toString() . '"';
                $rtn .= $this->results_[$side][$i]->toString();
            }
            if (! $doneResult && $side == $sit->side) {
                $rtn .= "," . $this->_result->toString();
            }
            $rtn .= ']';
            $rtn .= '}';
        }
        if ($this->date_ != "") {
            $rtn .= ',"date":"' . $this->date_ . '"';
        }
        if ($this->startTime_ != "") {
            $rtn .= ',"startTime":"' . $this->startTime_ . '"';
        }
        if ($this->duration_ != "") {
            $rtn .= ',"duration":"' . $this->duration_ . '"';
        }
        if ($this->weather_ != "") {
            $rtn .= ',"weather":"' . $this->weather_ . '"';
        }
        $rtn .= ',"situation":' . $this->situation_->toString();
        $rtn .= '}';
        return $rtn;
    }
    public function debugOn()
    {
        $this->debug_ = true;
    }
    public function debugOff()
    {
        $this->debug_ = false;
    }
    public function assignTeam($side, $name, $gn)
    {
        $this->teamName_[$side] = $name;
        $this->gameNumber_[$side] = $gn;
    }
    public function dateOfGame($date)
    {
        $this->date_ = $date;
    }
    public function startTime($start)
    {
        $this->startTime_ = $start;
    }
    public function duration($duration)
    {
        $this->duration_ = $duration;
    }
    public function lineupValid($side)
    {
        return $this->lineup_[$side]->isValid();
    }
    public function curBattingOrder($spot, $play, $pos)
    {
        $this->battingOrder($this->situation_.side, $spot, $play, $pos);
    }
    public function battingOrder($side, $spot, $play, $posit)
    {
        if ($posit == Position::position("P")) {
            $this->pitcher($side, $play);
        }
        $pos = new Position();
        $pos->position = $posit;
        $pos->when(
            count($this->results_[Side::VISITOR]), 
            count($this->results_[Side::HOME])
        );
        $play->newPosition($pos);
        $this->lineup_[$side]->insertIntoBat($spot, $play);
        $this->_updateSituation();
    }
    public function curHitter($play)
    {
        $this->hitter($this->situation_->side, $play);
    }
    public function hitter($side, $play)
    {
        $pos = new Position();
        $pos->p(Position::position('PH'));
        $pos->when(count($this->results_[0]), count($this->results_[1]));
        $play->newPosition($pos);
        $this->lineup_[$side]->insertIntoBat(count($this->results_[$side])%9, $play);
        $this->_updateSituation();
    }
    public function curRunner($slot, $play)
    {
        runner($this->situation_->side, $slot, $play);
    }
    public function runner($side, $slot, $play)
    {
        $pos = new Position();
        $pos->p(Position::position('PR'));
        $pos->when(count($this->results_[0]), count($this->results_[1]));
        $play->newPosition($pos);
        $this->lineup_[$side]->insertIntoBat($slot, $play);
        $this->_updateSituation();
    }
    public function curMove($spot, $pos)
    {
        move($this->situation_->side, $spot, $pos);
    }
    public function move($side, $spot, $posi)
    {
        $pos = new Position();
        $pos->p($posi);
        $pos->when(count($this->results_[0]), count($this->results_[1]));
        //$play = $this->lineup_[$side]->getHitter($spot);
        //$play->newPosition($pos);
        $this->lineup_[$side]->movePlayer($spot, $pos);
        $this->_updateSituation();
    }
    public function curPitcher($play)
    {
        $this->pitcher($this->situation_.otherSide(), $play);
    }
    public function pitcher($side, $play)
    {
        $pos = new Position();
        $pos->position = Position::position("P");
        $pos->when(
            count($this->results_[Side::VISITOR]), 
            count($this->results_[Side::HOME])
        );
        $play->newPosition($pos);
        $this->lineup_[$side]->insertIntoPitch($play);
        $this->_updateSituation();
    }
    public function getSituation()
    {
        return $this->situation_;
    }
    private function _moveRunners($s)
    {
        $batter = $this->situation_->batter;
        $side = $this->situation_->side;
        $this->situation_->runs[$side] += substr_count($s, "-H");
        //error_log("mR: " . $s . ":" . $side . '-' . $this->situation_->inning . PHP_EOL,3,'error_log');
        $this->situation_->runsPerInning[$side][$this->situation_->inning] 
            += substr_count($s, "-H");
        $ses = explode(",", $s);
        foreach ($ses as $se) {
            if (substr_count($se, "3x") > 0) {
                $this->situation_->runner[3] = null;
            }
            if (substr_count($se, "3-H") > 0) {
                $this->situation_->runner[3] = null;
            }
            if (substr_count($se, "2x") > 0) {
                $this->situation_->runner[2] = null;
            }
            if (substr_count($se, "2-H") > 0) {
                $this->situation_->runner[2] = null;
            }
            if (substr_count($se, "2-3") > 0) {
                $this->situation_->runner[3] = $this->situation_->runner[2];
                $this->situation_->runner[2] = null;
            }
            if (substr_count($se, "1x") > 0) {
                $this->situation_->runner[1] = null;
            }
            if (substr_count($se, "1-H") > 0) {
                $this->situation_->runner[1] = null;
            }
            if (substr_count($se, "1-3") > 0) {
                $this->situation_->runner[3] = $this->situation_->runner[1];
                $this->situation_->runner[1] = null;
            }
            if (substr_count($se, "1-2") > 0) {
                $this->situation_->runner[2] = $this->situation_->runner[1];
                $this->situation_->runner[1] = null;
            }
            if (substr_count($se, "B-3") > 0) {
                $this->situation_->runner[3] = $batter;
            }
            if (substr_count($se, "B-2") > 0) {
                $this->situation_->runner[2] = $batter;
            }
            if (substr_count($se, "B-1") > 0) {
                $this->situation_->runner[1] = $batter;
            }
        }
        //if ($this->debug_) {
        //    print "3.mr: " . ($this->situation_->runner[3] == null ? "empty" : 
        //        $this->situation_->runner[3]->name) . ", 2: " .  
        //        ($this->situation_->runner[2] == null ? "empty" : 
        //        $this->situation_->runner[2]->name) . ", 1: ".  
        //        ($this->situation_->runner[1] == null ? "empty" : 
        //        $this->situation_->runner[1]->name) . "\n";
        //}
    }
    private function _updateSituation()
    {
        if ($this->debug_) {
            print "updateSituation\n";
            print "0: " . count($this->results_[0]) . " - " . count($this->results_[1]) . "\n";
        }
        $sit = $this->situation_;
        $doneResult = ($this->_result->before == "") 
            && ($this->_result->during == "") && ($this->_result->after == "");
        if ($this->debug_) {
            print $this->_result->toString() . "\n";
        }

        $this->situation_ = new Situation();
        $outs=array(0,0);
        $inn=array(1,1);
        $r=array(null,null,null,null);
        for ($side = 0; $side < 2; $side++) {
            $split = false;
            if (! $doneResult && $side == $sit->side) {
                array_push($this->results_[$side], $this->_result);
                if ($this->debug_) {
                    print "Added " . $side . " - " . count($this->results_[$side]) . "\n";
                }
            }
            $this->situation_->inning = $inn[$side];
            for ($i=0; $i < count($this->results_[$side]); $i++) {
                $rslt = $this->results_[$side][$i];
                $this->situation_->side = $side;
                $os = $this->situation_->otherSide();
                $this->situation_->batter 
                    = $this->lineup_[$side]->getHitter($i%9);
                if (! $split) {
                    $outs[$side] += substr_count($rslt->before, "x");
                    $this->situation_->errors[$os] 
                        += substr_count($rslt->before, "E");
                    foreach (explode(";",$rslt->before) as $b4) {
                        $this->_moveRunners($b4);
                    }
                    if ($side == 0) {
                        $r[3]=$this->situation_->runner[3];
                        $r[2]=$this->situation_->runner[2];
                        $r[1]=$this->situation_->runner[1];
                    }
                }
                if ($outs[$side] < 3) {
                    $split = false;
                    $outs[$side] += substr_count($rslt->during, "x");
                    if (strlen($rslt->during) != 0) {
                        if (substr_compare($rslt->during, "S", 0) == 0 ||
                            substr_compare($rslt->during, "S(", 0, 2) == 0 ||
                            substr_compare($rslt->during, "S;", 0, 2) == 0) {
                            $this->situation_->hits[$side] ++;
                        }
			if (substr_compare($rslt->during, "D", 0) == 0 ||
			    substr_compare($rslt->during, "D;", 0, 2) == 0) {
                            $this->situation_->hits[$side] ++;
                        }
			if (substr_compare($rslt->during, "T", 0) == 0 ||
			    substr_compare($rslt->during, "T;", 0, 2) == 0) {
                            $this->situation_->hits[$side] ++;
                        }
                        if (substr_compare($rslt->during, "HR", 0, 2) == 0) {
                            $this->situation_->hits[$side] ++;
                        }
                        if (preg_match('/.*E\d.*/',$rslt->during) === 1) {
                            $this->situation_->errors[$os] ++;
                        }
                    }
                    $outs[$side] += substr_count($rslt->after, "x");
                    $this->_moveRunners($rslt->after);
                    if ($side == 0) {
                        $r[3]=$this->situation_->runner[3];
                        $r[2]=$this->situation_->runner[2];
                        $r[1]=$this->situation_->runner[1];
                    }
                } else {
                    $split = true;
                    $i --;
                }
                if ($outs[$side] >= 3) {
                    $outs[$side] = 0;
                    $inn[$side] ++;
                    $this->situation_->switchSides();
                    $this->situation_->inning = $inn[$side];
                    if ($side == 0) {
                        $r[3]=null;
                        $r[2]=null;
                        $r[1]=null;
                    }
                }
            }
            if (! $doneResult && $side == $sit->side) {
                array_pop($this->results_[$side]);
            }
        }
        if ($outs[0] != 0) {
            $this->situation_->side = 0;
        } elseif ($outs[1] != 0) {
            $this->situation_->side = 1;
        } elseif ($inn[0] == $inn[1]) {
            $this->situation_->side = 0;
        } else {
            $this->situation_->side = 1;
        }
        if ($this->situation_->side == 0) {
            $this->situation_->runner[3]=$r[3];
            $this->situation_->runner[2]=$r[2];
            $this->situation_->runner[1]=$r[1];
        }
        $this->situation_->outs = $outs[$this->situation_->side];
        $this->situation_->inning = $inn[$this->situation_->side];
        $this->situation_->betweenInnings = ($outs[0] == 0) && ($outs[1] == 0) 
            && ($this->situation_->runner[3] == null) 
            && ($this->situation_->runner[2] == null) 
            && ($this->situation_->runner[1] == null) 
            && $this->situation_->runsPerInning
                [$this->situation_->side][$this->situation_->inning] == 0;

        if ($this->debug_) {
            print $this->situation_->side . ', ' . $outs[0] . ' - ' . 
                $outs[1] . ', ' . $inn[0] . ' - ' . $inn[1] . "\n";
        }
        if ($this->debug_) {
            print "3: " . ($this->situation_->runner[3] == null ? "empty" : 
                $this->situation_->runner[3]->name) . ", 2: " .  
                ($this->situation_->runner[2] == null ? "empty" : 
                $this->situation_->runner[2]->name) . ", 1: ".  
                ($this->situation_->runner[1] == null ? "empty" : 
                $this->situation_->runner[1]->name) . "\n";
        }
        if (! $this->situation_->gameOver()) {
            $tss = $this->situation_->side;
            if ($this->debug_) {
                print "4: " . count($this->results_[$tss]);
                if (count($this->results_[$tss] > 0)) {
                    print " - '" . $this->results_[$tss][count($this->results_[$tss])-1]->during . "'";
                }
                print "\n";
            }
            if (count($this->results_[$tss]) > 0
                && $this->results_[$tss][count($this->results_[$tss])-1]->during==""
            ) {
                $this->situation_->batter 
                    = $this->lineup_[$tss]->getHitter(
                        (count($this->results_[$tss])-1)%9
                    );
            } else {
                $this->situation_->batter 
                    = $this->lineup_[$tss]->getHitter(
                        count($this->results_[$tss])%9
                    );
            }
            $this->situation_->pitcher 
                = $this->lineup_[($tss+1)%2]->getCurrentPitcher();
        } else {
            $this->situation_->batter = "";
            $this->situation_->pitcher = "";
            //if ($this->situation_->runs[0] > $this->situation_->runs[1]) $this->situation_->inning --;
        }
    }
    private function _saveResult()
    {
        if ($this->_plus) {
            $this->_updateSituation();
        } else {
            $this->results_[$this->situation_->side]
                [count($this->results_[$this->situation_->side])] = $this->_result;
            $this->_result = new Result();
            $this->_updateSituation();
            if ($this->situation_->betweenInnings) {
                if ($this->situation_->gameOver()) {
                    return;
                };
                $c = count($this->results_[$this->situation_->side]);
                if (($c > 0) 
                    && ($this->results_[$this->situation_->side][$c-1]->during == "")
                ) {
                    $this->_result = $this->results_[$this->situation_->side][$c-1];
                    unset($this->results_[$this->situation_->side][$c-1]);
                }
            }
        }
        $this->_plus=false;
    }
    private function _batter($base)
    {
        if ($base == 0) {
            return;
        }
        if ($this->_result->after != "") {
            $this->_result->after .= ",";
        }
        if ($base > 0) {
            if ($base == 4) {
                $this->_result->after .= "B-H";
            } else {
                $this->_result->after .= "B-" . $base;
            }
        } else {
            $this->_result->after .= "Bx" . ($base * -1);
        }
    }
    private function _advanceSame($bases)
    {
        $this->_advanceSameB($bases, false);
    }
    private function _advanceSameB($bases, $before)
    {
        $this->_advance($bases, $bases, $bases, $before);
    }
    private function _advance($third, $second, $first, $before)
    {
        $bases = array($first, $second, $third);
        $first = true;
        for ($i=3; $i>0; $i--) {
            if ($this->situation_->base($i)) {
                if ($bases[$i-1] != 0) {
                    if ($before) {
                        if ($this->_result->before != "") {
                            if ($first) {
                                $this->_result->before .= ";";
                                $first = false;
                            } else $this->_result->before .= ",";
                        }
                    } else {
                        if ($this->_result->after != "") {
                            $this->_result->after .= ",";
                        }
                    }
                    if ($bases[$i-1] < 0) {
                        if ($i-$bases[$i-1] > 3) {
                            if ($before) {
                                $this->_result->before .= $i . "xH";
                            } else {
                                $this->_result->after .= $i . "xH";
                            }
                        } else {
                            if ($before) {
                                $this->_result->before 
                                    .= $i . "x" . ($i-$bases[$i-1]);
                            } else {
                                $this->_result->after 
                                    .= $i . "x" . ($i-$bases[$i-1]);
                            }
                        }
                    } else {
                        if ($i+$bases[$i-1] > 3) {
                            if ($before) {
                                $this->_result->before .= $i . "-H";
                            } else {
                                $this->_result->after .= $i . "-H";
                            }
                        } else {
                            if ($before) {
                                $this->_result->before 
                                    .= $i . "-" . ($i+$bases[$i-1]);
                            } else {
                                $this->_result->after 
                                    .= $i . "-" . ($i+$bases[$i-1]);
                            }
                        }
                    }
                }
            }
        }
    }
    private function _advanceForceA()
    {
        $this->_advanceForced(false);
    }
    private function _advanceForced($before)
    {
        if ($before) {
            if ($this->_result->before != "") {
                $this->_result->before .= ";";
            }
        } else {
            if ($this->_result->after != "") {
                $this->_result->after .= ",";
            }
        }
        if ($this->situation_->base(1)) {
            if ($this->situation_->base(2)) {
                if ($this->situation_->base(3)) {
                    if ($before) {
                        $this->_result->before = "3-H,";
                    } else {
                        $this->_result->after = "3-H,";
                    }
                }
                if ($before) {
                    $this->_result->before .= "2-3,";
                } else {
                    $this->_result->after .= "2-3,";
                }
            }
            if ($before) {
                $this->_result->before .= "1-2";
            } else {
                $this->_result->after .= "1-2";
            }
        }
    }
    public function play($p, $third, $second, $first, $bat)
    {
        $this->_result->during = $p;
        $this->_advance($third, $second, $first, false);
        $this->_batter($bat);
        $this->_saveResult();
    }
    public function k()
    {
        $this->_result->during = "K";
        $this->_result->after = "Bx";
        $this->_saveResult();
    }
    public function k23()
    {
        $this->_result->during = "K/23";
        $this->_result->after = "Bx1";
        $this->_saveResult();
    }
    public function kwp()
    {
        $this->_result->during = "K/WP";
        $this->_advanceSame(1);
        $this->_batter(1);
        $this->_saveResult();
    }
    public function kpb()
    {
        $this->_result->during = "K/PB";
        $this->_advanceSame(1);
        $this->_batter(1);
        $this->_saveResult();
    }
    public function ci()
    {
        $this->_result->during = "CI/E2";
        $this->_advanceForceA();
        $this->_batter(1);
        $this->_saveResult();
    }
    public function di()
    {
        $this->_advance(0, 1, 1, true);
        if ($this->_result->before != "") $this->_result->before .= "/";
        $this->_result->before .= "DI";
    }
    public function bb()
    {
        $this->_result->during = "BB";
        $this->_advanceForceA();
        $this->_batter(1);
        $this->_saveResult();
    }
    public function hbp()
    {
        $this->_result->during = "HBP";
        $this->_advanceForceA();
        $this->_batter(1);
        $this->_saveResult();
    }
    public function s1()
    {
        $this->_result->during = "S";
        $this->_advanceSame(1);
        $this->_batter(1);
        $this->_saveResult();
    }
    public function s1rp()
    {
        $this->_result->during = "S";
        $this->_advance(1, 1, -1);
        $this->_batter(1);
        $this->_saveResult();
    }
    public function s1rp3()
    {
        $this->_result->during = "S";
        $this->_advance(0, 0, -1);
        $this->_batter(1);
        $this->_saveResult();
    }
    public function s2()
    {
        $this->_result->during = "S";
        $this->_advanceSame(2);
        $this->_batter(1);
        $this->_saveResult();
    }
    public function s2rp()
    {
        $this->_result->during = "S/8-3";
        $this->_advanceSame(2);
        $this->_batter(1);
        $this->_result->after .= "Bx1";
        $this->_saveResult();
    }
    public function s($third, $second, $first, $bat)
    {
        $this->_result->during = "S";
        $this->_advance($third, $second, $first, false);
        $this->_batter($bat);
        $this->_saveResult();
    }
    public function d2()
    {
        $this->_result->during = "D";
        $this->_advanceSame(2);
        $this->_batter(2);
        $this->_saveResult();
    }
    public function d2rp()
    {
        $this->_result->during = "D/6";
        $this->_advanceSame(2);
        $this->_batter(2);
        $this->_result->after .= "Bx2";
        $this->_saveResult();
    }
    public function d3()
    {
        $this->_result->during = "D";
        $this->_advanceSame(3);
        $this->_batter(2);
        $this->_saveResult();
    }
    public function d($third, $second, $first, $bat)
    {
        $this->_result->during = "D";
        $this->_advance($third, $second, $first, false);
        $this->_batter($bat);
        $this->_saveResult();
    }
    public function t()
    {
        $this->_result->during = "T";
        $this->_advanceSame(3);
        $this->_batter(3);
        $this->_saveResult();
    }
    public function hr()
    {
        $this->_result->during = "HR";
        $this->_advanceSame(4);
        $this->_batter(4);
        $this->_saveResult();
    }
    public function gb1($result)
    {
        $this->gb($result, 1, 1, 1, -1);
    }
    public function gb($result, $third, $second, $first, $bat)
    {
        $this->_result->during = $result;
        $outs = 0;
        if ($third < 0) {
            $outs++;
        }
        if ($second < 0) {
            $outs++;
        }
        if ($first < 0) {
            $outs++;
        }
        if ($bat < 0) {
            $outs++;
        }
        if ($outs == 3) {
            $this->_result->during .= "/TP";
        }
        if ($outs == 2) {
            $this->_result->during .= "/DP";
        }
        if ($outs + $this->situation_->outs < 3) {
            $this->_advance($third, $second, $first, false);
        } else {
            $this->_advance(
                $third>0 ? 0 : $third, $second>0 ? 0 : $second, 
                $first>0 ? 0 : $first, false
            );
        }
        $this->_batter($bat);
        $this->_saveResult();
    }
    public function sac1($result)
    {
        $this->sac($result, 1, 1, 1, -1);
    }
    public function sac($result, $third, $second, $first, $bat)
    {
        $this->_result->during = $result . "/SAC";
        $this->_advance($third, $second, $first, false);
        $this->_batter($bat);
        $this->_saveResult();
    }
    public function fo0($result)
    {
        $this->fo($result, 0, 0, 0, -1);
    }
    public function fo($result, $third, $second, $first, $bat)
    {
        $this->_result->during = $result;
        $outs = 0;
        if ($third < 0) {
            $outs++;
        }
        if ($second < 0) {
            $outs++;
        }
        if ($first < 0) {
            $outs++;
        }
        if ($bat < 0) {
            $outs++;
        }
        if ($outs == 3) {
            $this->_result->during .= "/TP";
        }
        if ($outs == 2) {
            $this->_result->during .= "/DP";
        }
        if (($outs + $this->situation_->outs) < 3 && $third > 0 
            && $this->situation_->base(3)
        ) {
            $this->_result->during .= "/SF";
        }
        if ($outs + $this->situation_->outs < 3) {
            $this->_advance($third, $second, $first, false);
        } else {
            $this->_advance(
                $third>0 ? 0 : $third, $second>0 ? 0 : 
                $second, $first>0 ? 0 : $first, false
            );
        }
        if ($this->_result->after != "") {
            $this->_result->after .= ",";
        }
        $this->_result->after .= "Bx";
        $this->_saveResult();
    }
    public function e1($result)
    {
        $this->e($result, 1, 1, 1, 1, false);
    }
    public function e($result, $third, $second, $first, $bat, $before)
    {
        if ($before) {
            if ($this->_result->before != "") {
                $this->_result->before .= ";";
            }
            $this->_result->before = $result;
        } else {
            $this->_result->during = $result;
        }
        $this->_advance($third, $second, $first, $before);
        $this->_batter(bat);
        $this->_saveResult();
    }
    public function po1($result)
    {
        $this->po($result, 0, 0, -1);
    }
    public function po($result, $third, $second, $first)
    {
        //if ($this->_result->before != "") $this->_result->before .= ";";
        $this->_advance($third, $second, $first, true);
        if ($this->_result->before != "") $this->_result->before .= "/";
        $this->_result->before .= "PO-" . $result;
        if ($this->situation_->outs == 2) {
            $this->_saveResult();
        } else {
            $this->_updateSituation();
        }
    }
    public function wp1()
    {
        $this->wp(1, 1, 1);
    }
    public function wp($third, $second, $first)
    {
        $this->_advance($third, $second, $first, true);
        if ($this->_result->before != "") $this->_result->before .= "/";
        $this->_result->before .= "WP";
        $this->_updateSituation();
    }
    public function pb1()
    {
        $this->pb(1, 1, 1);
    }
    public function pb($third, $second, $first)
    {
        $this->_advance($third, $second, $first, true);
        if ($this->_result->before != "") $this->_result->before .= "/";
        $this->_result->before .= "PB";
        $this->_updateSituation();
    }
    public function pb3()
    {
        if (! $this->situation_->base(3)) {
            $this->pb();
        } else {
            if (! $this->situation_->base(2) && $this->situation_->base(1)) {
                $this->pb(0, 0, 1);
            }
        }
        $this->_updateSituation();
    }
    public function bk1()
    {
        $this->bk(1, 1, 1);
    }
    public function bk($third, $second, $first)
    {
        $this->_advance($third, $second, $first, true);
        if ($this->_result->before != "") $this->_result->before .= "/";
        $this->_result->before .= "BK";
        $this->_updateSituation();
    }
    public function sb1()
    {
        $this->sb(1, 1, 1);
    }
    public function sb($third, $second, $first)
    {
        $this->_advance($third, $second, $first, true);
        if ($this->_result->before != "") $this->_result->before .= "/";
        $this->_result->before .= "SB";
        $this->_updateSituation();
    }
    public function sbe($third, $second, $first)
    {
        $this->_advance($third, $second, $first, true);
        if ($this->_result->before != "") $this->_result->before .= "/";
        $this->_result->before .= "SB;E-2";
        $this->_updateSituation();
    }
    public function cs1($result)
    {
        $this->cs($result, -1, -1, -1);
    }
    public function cs($result, $third, $second, $first)
    {
        if ($this->_result->before != "") {
            $this->_result->before .= ";";
        }
        $this->_advance($third, $second, $first, true);
        if ($this->_result->before != "") $this->_result->before .= "/";
        $this->_result->before .= "CS" . $result;
        if ($this->situation_->outs == 2) {
            $this->_saveResult();
        } else {
            $this->_updateSituation();
        }
    }
    public function betweenInnings()
    {
        return $this->situation_->betweenInnings;
    }
    public function getLineup($side)
    {
        return $this->lineup_[$side];
    }
    public function undo()
    {
        if ($this->debug_) {
            print "undo\n";
        }
        if ($this->_result->before == "" && $this->_result->during == "" 
            && $this->_result->after == ""
        ) {
            if ($this->debug_) {
                print "Blank result\n";
            }
            if ($this->situation_->betweenInnings) {
                $s = $this->situation_->otherSide();
            } else {
                $s = $this->situation_->side;
            }
            $c = count($this->results_[$s]);
            $this->_result = $this->results_[$s][$c-1];
            unset($this->results_[$s][$c-1]);
            //$this->_updateSituation();
            $this->undo();
        } else {
            if ($this->_result->during != "" || $this->_result->after != "") {
                $outs = substr_count($this->_result->during, "x") + 
                    substr_count($this->_result->after, "x");
                $this->_result->during="";
                $this->_result->after="";
                //Handle split inning evil
                if ($outs >= $this->situation_->outs 
                    && $this->_result->before != ""
                ) {
                    $this->results_[$this->situation_->side]
                        [count($this->results_[$this->situation_->side])]
                            =$this->_result;
                    $this->_result = new Result();
                }
                $this->_updateSituation();
            } else {
                $this->_result->before="";
            }
        }
        $this->_updateSituation();
        //    //System.out.println("After: " + this.toString() + _result);
    }
    public function start()
    {
        $this->situation_->batter = $this->lineup_[0]->getHitter(0);
        $this->situation_->pitcher = $this->lineup_[1]->getCurrentPitcher();
    }
}
?>
