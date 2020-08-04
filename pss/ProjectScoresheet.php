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
  class ProjectScoresheet {
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
    private $result_;
    private $plus_ = false;
    private $debug_ = false;
    function __construct() {
      $this->teamName_ = array();
      $this->gameNumber_ = array();
      $this->lineup_ = array();
      $this->roster_ = array();
      $this->results_ = array();
      foreach (Side::sides() as $key => $side) {
        $this->teamName_[$side] = "";
        $this->gameNumber_[$side] = 0;
        $this->lineup_[$side] = new Lineup;
        $this->roster_[$side] = array();
        $this->results_[$side] = array();
      }
      $this->situation_ = new Situation;
      $this->result_ = new Result;
      $this->situation_->betweenInnings = true;
    }
    public static function fromString($str) {
      $json = json_decode($str);
      $v = $json->visitor;
      $h = $json->home;
      $inst = new self();
//var_dump($json->visitor);
      $inst->teamName_[0] = $v->name;
      $inst->teamName_[1] = $h->name;
      $inst->gameNumber_[0] = $v->gameNumber;
      $inst->gameNumber_[1] = $h->gameNumber;
      #Lineup
      $inst->lineup_[0] = Lineup::fromString('"lineup":' . json_encode($v->lineup) . ',"rotation":' . json_encode($v->rotation));
      $inst->lineup_[1] = Lineup::fromString('"lineup":' . json_encode($h->lineup) . ',"rotation":' . json_encode($h->rotation));
      if ($v->roster != null) {
        foreach($v->roster as $r) {
          array_push($inst->roster_[0],$r);
        }
      }
      if ($h->roster != null) {
        foreach($h->roster as $r) {
          array_push($inst->roster_[1],$r);
        }
      }
      if ($v->results != null) {
        foreach($v->results as $rslt) {
          $r = Result::fromString(json_encode($rslt));
          array_push($inst->results_[0],$r);
        }
      }
      if ($h->results != null) {
        foreach($h->results as $rslt) {
          $r = Result::fromString(json_encode($rslt));
          array_push($inst->results_[1],$r);
        }
      }
      if ($json->date) $inst->date_ = $json->date;
      if ($json->startTime) $inst->startTime_ = $json->startTime;
      if ($json->duration) $inst->duration_ = $json->duration;
      if ($json->weather) $inst->weather_ = $json->weather;
      //$inst->debugOn();
      $inst->updateSituation();
      return $inst;
    }
    public function toString() {
      $rtn = '{';
      foreach (Side::sides() as $key => $side) {
        if ($side == Side::Visitor) $rtn .= '"visitor":{'; else $rtn .= ',"home":{';
        $rtn .= '"name":"' .  $this->teamName_[$side] . '"';
        $rtn .= ',"gameNumber":"' . $this->gameNumber_[$side] . '"';
        $rtn .= ',' . $this->lineup_[$side]->toString();
        $rtn .= ',"roster":[';
        for ($i = 0; $i < count($this->roster_[$side]); $i++) {
          if ($i > 0) $rtn .= ",";
          $rtn .= '"' . $this->roster_[$side][$i] . '"';
        }
        $rtn .= '],"results":[';
        for ($i = 0; $i < count($this->results_[$side]); $i++) {
          if ($i > 0) $rtn .= ",";
          $rtn .= '"' . $this->results_[$side][$i]->toString() . '"';
        }
        $rtn .= ']';
        $rtn .= '}';
      }
      if ($this->date_ != "") $rtn .= ',"date":"' . $this->date_ . '"';
      if ($this->startTime_ != "") $rtn .= ',"startTime":"' . $this->startTime_ . '"';
      if ($this->duration_ != "") $rtn .= ',"duration":"' . $this->duration_ . '"';
      if ($this->weather_ != "") $rtn .= ',"weather":"' . $this->weather_ . '"';
      $rtn .= '}';
      return $rtn;
    }
    public function debugOn() { $this->debug_ = true; }
    public function debugOff() { $this->debug_ = false; }
    public function assignTeam($side, $name, $gn) {
      $this->teamName_[$side] = $name;
      $this->gameNumber_[$side] = $gn;
    }
    public function dateOfGame($date) {$this->date_ = $date; }
    public function startTime($start) {$this->startTime_ = $start; }
    public function duration($duration) { $this->duration_ = $duration; }
    public function lineupValid($side) { return $this->lineup_[$side]->isValid(); }
    public function curBattingOrder($spot, $play, $pos) { $this->battingOrder($this->situation_.side, $spot, $play, $pos); }
    public function battingOrder($side, $spot, $play, $posit) {
      if ($posit == Position::position("P")) $this->pitcher($side, $play);
      $pos = new Position;
      $pos->position = $posit;
      $pos->when(count($this->results_[Side::Visitor]),count($this->results_[Side::Home]));
      $play->newPosition($pos);
      $this->lineup_[$side]->insertIntoBat($spot, $play);
      $this->updateSituation();
    }
    public function curHitter($play) { $this->hitter($this->situation_->side, $play); }
    public function hitter ($side, $play) {
      $pos = new Position;
      $pos->p(Position::position('PH'));
      $pos->when(count($this->results_[0]),count($this->results_[1]));
      $play->newPosition($pos);
      $this->lineup_[$side]->insertIntoBat(count($this->results_[$side])%9, $play);
      $this->updateSituation();
    }
    public function curRunner($slot, $play) { runner($this->situation_->side, $slot, $play); }
    public function runner($side, $slot, $play) {
      $pos = new Position;
      $pos->p(Position::position('PR'));
      $pos->when(count($this->results_[0]),count($this->results_[1]));
      $play->newPosition($pos);
      $this->lineup_[$side]->insertIntoBat($slot, $play);
      $this->updateSituation();
    }
    public function curMove($spot, $pos) {move($this->situation_->side, $spot, $pos); }
    public function move($side, $spot, $posi) {
      $pos = new Position;
      $pos->p($posi);
      $pos->when(count($this->results_[0]),count($this->results_[1]));
      //$play = $this->lineup_[$side]->getHitter($spot);
      //$play->newPosition($pos);
      $this->lineup_[$side]->movePlayer($spot, $pos);
      $this->updateSituation();
    }
    public function curPitcher ($play) {$this->pitcher ($this->situation_.otherSide(), $play); }
    public function pitcher ($side, $play) {
      $pos = new Position;
      $pos->position = Position::position("P");
      $pos->when(count($this->results_[Side::Visitor]),count($this->results_[Side::Home]));
      $play->newPosition($pos);
      $this->lineup_[$side]->insertIntoPitch($play);
      $this->updateSituation();
    }
    public function getSituation() { return $this->situation_; }
    private function moveRunners($s) {
      $batter = $this->situation_->batter;
      $side = $this->situation_->side;
      $this->situation_->runs[$side] += substr_count($s,"-H");
      $this->situation_->runsPerInning[$side][$this->situation_->inning] += substr_count($s,"-H");
      if (substr_count($s,"3x") > 0) $this->situation_->runner[3] = null; 
      if (substr_count($s,"3-H") > 0) $this->situation_->runner[3] = null; 
      if (substr_count($s,"2x") > 0) $this->situation_->runner[2] = null; 
      if (substr_count($s,"2-H") > 0) $this->situation_->runner[2] = null; 
      if (substr_count($s,"2-3") > 0) {$this->situation_->runner[3] = $this->situation_->runner[2]; $this->situation_->runner[2] = null;} 
      if (substr_count($s,"1x") > 0) $this->situation_->runner[1] = null; 
      if (substr_count($s,"1-H") > 0) $this->situation_->runner[1] = null; 
      if (substr_count($s,"1-3") > 0) {$this->situation_->runner[3] = $this->situation_->runner[1]; $this->situation_->runner[1] = null;} 
      if (substr_count($s,"1-2") > 0) {$this->situation_->runner[2] = $this->situation_->runner[1]; $this->situation_->runner[1] = null;} 
      if (substr_count($s,"B-3") > 0) $this->situation_->runner[3] = $batter; 
      if (substr_count($s,"B-2") > 0) $this->situation_->runner[2] = $batter; 
      if (substr_count($s,"B-1") > 0) $this->situation_->runner[1] = $batter; 
      if ($this->debug_) print "3: " . ($this->situation_->runner[3] == null ? "empty" : $this->situation_->runner[3]->name) . ", 2: " .  ($this->situation_->runner[2] == null ? "empty" : $this->situation_->runner[2]->name) . ", 1: ".  ($this->situation_->runner[1] == null ? "empty" : $this->situation_->runner[1]->name) . "\n";
    }
    private function updateSituation() {
      if ($this->debug_) print "updateSituation\n";
      $sit = $this->situation_;
      $doneResult = ($this->result_->before == "") && ($this->result_->during == "") && ($this->result_->after == "");

      #$outs = 0;
      #if ($this->situation_ == null) $origBatter = null; else $origBatter = $this->situation_->batter;
      #$this->situation_ = new Situation;
      #$splitInning= array(false,false);
      #$it=array(0,0);
      #$side=0;
      #while ($it[0] < count($this->results_[0]) || $it[1] < count($this->results_[1]) || 
      #      ($splitInning[0] && $this->results_[0][count($this->results_[0])-1]->during != '') || 
      #      ($splitInning[1] && $this->results_[1][count($this->results_[1])-1]->during != '') || 
      #      (! $doneResult)) {
      #  if ($this->debug_) print "side=" . $side . ",it0=" . $it[0] . ",#results0=" . count($this->results_[0]) . ",it1=" . $it[1] . ",#results1=" . count($this->results_[1]) . ",split0=" .  ($splitInning[0]?"true":"false") . ",during0=" . $this->results_[0][count($this->results_[0])-1]->during . ",split1=" .  ($splitInning[1]?"true":"false") . ",'during1=" . $this->results_[1][count($this->results_[1])-1]->during . ",doneR=" .  ($doneResult?"true":"false") . "\n";
      #  $this->situation_->betweenInnings=false;
      #  if ($splitInning[$side]) {$it[$side]--;}
      #  if ($it[$side] < count($this->results_[$side])) {
      #    $rslt = $this->results_[$side][$it[$side]];
      #    $this->situation_->batter = $this->lineup_[$side]->getHitter($it[$side]%9);
      #    $it[$side] ++;
      #  } else {
      #    $rslt = $this->result_;
      #    $this->situation_->batter = $origBatter;
      #    $doneResult = true;
      #  }
      #  $this->situation_->side = $side;
      #  if (! $splitInning[$side]) {
      #    $outs += substr_count($rslt->before,"x");
      #    $this->moveRunners($rslt->before);
      #  }
      #  if ($outs < 3) {
      #    $splitInning[$side] = false;
      #    $outs += substr_count($rslt->during,"x");
      #    if (strlen($rslt->during) != 0) {
      #      if (substr_compare($rslt->during,"S",0) == 0) $this->situation_->hits[$side] ++;
      #      if (substr_compare($rslt->during,"D",0) == 0) $this->situation_->hits[$side] ++;
      #      if (substr_compare($rslt->during,"T",0) == 0) $this->situation_->hits[$side] ++;
      #      if (substr_compare($rslt->during,"HR",0) == 0) $this->situation_->hits[$side] ++;          
      #    }
      #    $outs += substr_count($rslt->after,"x");
      #    $this->moveRunners($rslt->after);
      #  } else {
      #    $splitInning[$side] = true;
      #  }
      #  if ($outs >= 3) {
      #    $outs = 0;
      #    $this->situation_->switchSides();
      #    $this->situation_->betweenInnings = true;
      #    $side = $this->situation_->side;
      #  }
      #  $this->situation_->outs = $outs;
      #}
      #$ws = $this->situation_->side;
      #$os = $this->situation_->otherSide();
      #$this->situation_->batter = $this->lineup_[$ws]->getHitter(count($this->results_[$ws])%9);
      #$this->situation_->pitcher = $this->lineup_[$os]->getCurrentPitcher();

      $this->situation_ = new Situation;
      $outs=array(0,0);
      $inn=array(1,1);
      $r=array(null,null,null,null);
      for ($side = 0; $side < 2; $side++) {
        $split = false;
        if (! $doneResult && $side == $sit->side) array_push($this->results_[$side],$this->result_);
        for ($i=0; $i < count($this->results_[$side]); $i++) {
          $rslt = $this->results_[$side][$i];
          $this->situation_->side = $side;
          $os = $this->situation_->otherSide();
          $this->situation_->batter = $this->lineup_[$side]->getHitter($i%9);
          if (! $split) {
            $outs[$side] += substr_count($rslt->before,"x");
            $this->situation_->errors[$os] += substr_count($rslt->before,"E");          
            $this->moveRunners($rslt->before);
            if ($side == 0) { $r[3]=$this->situation_->runner[3]; $r[2]=$this->situation_->runner[2]; $r[1]=$this->situation_->runner[1];}
          }
          if ($outs[$side] < 3) {
            $split = false;
            $outs[$side] += substr_count($rslt->during,"x");
            if (strlen($rslt->during) != 0) {
              if (substr_compare($rslt->during,"S",0) == 0) $this->situation_->hits[$side] ++;
              if (substr_compare($rslt->during,"D",0) == 0) $this->situation_->hits[$side] ++;
              if (substr_compare($rslt->during,"T",0) == 0) $this->situation_->hits[$side] ++;
              if (substr_compare($rslt->during,"HR",0) == 0) $this->situation_->hits[$side] ++;          
              if (substr_compare($rslt->during,"E",0) == 0) $this->situation_->errors[$os] ++;          
            }
            $outs[$side] += substr_count($rslt->after,"x");
            $this->moveRunners($rslt->after);
            if ($side == 0) { $r[3]=$this->situation_->runner[3]; $r[2]=$this->situation_->runner[2]; $r[1]=$this->situation_->runner[1];}
          } else {
            $split = true;
            $i --;
          }
          if ($outs[$side] >= 3) {
            $outs[$side] = 0;
            $inn[$side] ++;
            $this->situation_->switchSides();
            if ($side == 0) { $r[3]=null; $r[2]=null; $r[1]=null;}
          }
        }
        if (! $doneResult && $side == $sit->side) array_pop($this->results_[$side]);
        if ($this->debug_ && $side == 0) print "3: " . ($this->situation_->runner[3] == null ? "empty" : $this->situation_->runner[3]->name) . ", 2: " .  ($this->situation_->runner[2] == null ? "empty" : $this->situation_->runner[2]->name) . ", 1: ".  ($this->situation_->runner[1] == null ? "empty" : $this->situation_->runner[1]->name) . "\n";
      }
      if ($outs[0] != 0) $this->situation_->side = 0;
      else if ($outs[1] != 0) $this->situation_->side = 1;
      else if ($inn[0] == $inn[1]) $this->situation_->side = 0;
      else $this->situation_->side = 1;
      if ($this->situation_->side == 0) { $this->situation_->runner[3]=$r[3]; $this->situation_->runner[2]=$r[2]; $this->situation_->runner[1]=$r[1];}
      $this->situation_->outs = $outs[$this->situation_->side];
      $this->situation_->inning = $inn[$this->situation_->side];
      $this->situation_->betweenInnings = ($outs[0] == 0) && ($outs[1] == 0) && ($this->situation_->runner[3] == null) && ($this->situation_->runner[2] == null) && ($this->situation_->runner[1] == null) & $this->situation_->runsPerInning[$this->situation_->side][$this->situation_->inning] == 0;
      
      if ($this->debug_) print $this->situation_->side . ', ' . $outs[0] . ' - ' . $outs[1] . ', ' . $inn[0] . ' - ' . $inn[1] . "\n";
      if ($this->debug_) print "3: " . ($this->situation_->runner[3] == null ? "empty" : $this->situation_->runner[3]->name) . ", 2: " .  ($this->situation_->runner[2] == null ? "empty" : $this->situation_->runner[2]->name) . ", 1: ".  ($this->situation_->runner[1] == null ? "empty" : $this->situation_->runner[1]->name) . "\n";
      
 
//sleep(1);
//print_r($this->results_);
//print ($splitInning[0]?"true":"false") . "\t" . ($splitInning[0]?"true":"false") . "\n";
    }
    private function saveResult () {
      if ($this->plus_) {
        $this->updateSituation();
      } else {
        $this->results_[$this->situation_->side][count($this->results_[$this->situation_->side])] = $this->result_;
        $this->result_ = new Result;
        $this->updateSituation();
        if ($this->situation_->betweenInnings) {
          if ($this->situation_->gameOver()) { return; };
          $c = count($this->results_[$this->situation_->side]);
          if (($c > 0) && ($this->results_[$this->situation_->side][$c-1]->during == "")) {
            $this->result_ = $this->results_[$this->situation_->side][$c-1];
            unset($this->results_[$this->situation_->side][$c-1]);
          }
        }
      }
      $this->plus_=false;
    }
    private function batter($base) {
      if ($base == 0) return;
      if ($this->result_->after != "") $this->result_->after .= ",";
      if ($base > 0) {
        if ($base == 4) {
          $this->result_->after .= "B-H";
        } else {
          $this->result_->after .= "B-" . $base;
        }
      } else {
        $this->result_->after .= "Bx" . $base;
      }
    }
    private function advanceSame ($bases) {$this->advanceSameB($bases,false);}
    private function advanceSameB ($bases, $before) {$this->advance($bases, $bases, $bases, $before); }
    private function advance ($third, $second, $first, $before) {
      $bases = array($first, $second, $third);
      for ($i=3; $i>0; $i--) {
        if ($this->situation_->base($i)) {
          if ($bases[$i-1] != 0) {
            if ($before) {
              if ($this->result_->before != "") $this->result_->before .= ",";
            } else {
              if ($this->result_->after != "") $this->result_->after .= ",";
            }
            if ($bases[$i-1] < 0) {
              if ($i-$bases[$i-1] > 3) {
                if ($before) $this->result_->before .= $i . "xH";
                else $this->result_->after .= $i . "xH";    
              } else {
                if ($before) $this->result_->before .= $i . "x" . ($i-$bases[$i-1]);
                else $this->result_->after .= $i . "x" . ($i-$bases[$i-1]);            
              }
            } else {
              if ($i+$bases[$i-1] > 3) {
                if ($before) $this->result_->before .= $i . "-H";
                else $this->result_->after .= $i . "-H";
              } else {
                if ($before) $this->result_->before .= $i . "-" . ($i+$bases[$i-1]);
                else $this->result_->after .= $i . "-" . ($i+$bases[$i-1]);
              }
            }
          }
        }
      }
    }
    private function advanceForceA() {$this->advanceForced(false);}
    private function advanceForced($before) {
      if ($before) {
        if ($this->result_->before != "") $this->result_->before .= ",";
      } else {
        if ($this->result_->after != "") $this->result_->after .= ",";
      }
      if ($this->situation_->base(1)) {
        if ($this->situation_->base(2)) {
          if ($this->situation_->base(3)) {
            if ($before) $this->result_->before = "3-H,"; 
            else $this->result_->after = "3-H,";
          }
          if ($before) $this->result_->before .= "2-3,"; 
          else $this->result_->after .= "2-3,";
        }
        if ($before) $this->result_->before .= "1-2"; 
        else $this->result_->after .= "1-2";
      }
    }
    public function k() {$this->result_->during = "K"; $this->result_->after = "Bx"; $this->saveResult(); }
    public function k23() {$this->result_->during = "K/23"; $this->result_->after = "Bx1"; $this->saveResult(); }
    public function kwp() {$this->result_->during = "K/WP"; $this->advanceSame(1); $this->batter(1); $this->saveResult(); }
    public function ci() {$this->result_->during = "CI/E2"; $this->advanceForceA(); $this->batter(1); $this->saveResult(); }
    public function di() {$this->result_->before .= "/DI"; $this->advance(0,1,1,true);}
    public function bb() {$this->result_->during = "BB"; $this->advanceForceA(); $this->batter(1); $this->saveResult(); }
    public function hbp() {$this->result_->during = "HBP"; $this->advanceForceA(); $this->batter(1); $this->saveResult(); }
    public function s1() {$this->result_->during = "S"; $this->advanceSame(1); $this->batter(1); $this->saveResult(); }
    public function s2() {$this->result_->during = "S"; $this->advanceSame(2); $this->batter(1); $this->saveResult(); }
    public function s($third, $second, $first, $bat) {
      $this->result_->during = "S"; 
      $this->advance($third, $second, $first, false); 
      $this->batter($bat); 
      $this->saveResult(); 
    }
    public function d2() {$this->result_->during = "D"; $this->advanceSame(2); $this->batter(2); $this->saveResult(); }
    public function d3() {$this->result_->during = "D"; $this->advanceSame(3); $this->batter(2); $this->saveResult(); }
    public function d($third, $second, $first, $bat) {
      $this->result_->during = "D"; 
      $this->advance($third, $second, $first, false); 
      $this->batter($bat); 
      $this->saveResult(); 
    }
    public function t() {$this->result_->during = "T"; $this->advanceSame(3); $this->batter(3); $this->saveResult(); }
    public function hr() {$this->result_->during = "HR"; $this->advanceSame(4); $this->batter(4); $this->saveResult(); }
    public function gb1($result) { $this->gb($result,1,1,1,-1); }
    public function gb($result, $third, $second, $first, $bat) {
      $this->result_->during = $result;
      $outs = 0;
      if ($third < 0) $outs++;
      if ($second < 0) $outs++;
      if ($first < 0) $outs++;
      if ($bat < 0) $outs++;
      if ($outs == 3) $this->result_->during .= "/TP";
      if ($outs == 2) $this->result_->during .= "/DP";
      if ($outs + $this->situation_->outs < 3) $this->advance($third, $second, $first, false); 
      else $this->advance($third>0?0:$third, $second>0?0:$second, $first>0?0:$first, false);
      $this->batter($bat);
      $this->saveResult();
    }
    public function sac1($result) { $this->sac($result,1,1,1,-1);}
    public function sac($result, $third, $second, $first, $bat) {
      $this->result_->during = $result + "/SAC";
      $this->advance($third, $second, $first, false);
      $this->batter($bat);
      $this->saveResult();
    }
    public function fo0($result) {$this->fo ($result,0,0,0,-1); }
    public function fo($result, $third, $second, $first, $bat) {
      $this->result_->during = $result;
      $outs = 0;
      if ($third < 0) $outs++;
      if ($second < 0) $outs++;
      if ($first < 0) $outs++;
      if ($bat < 0) $outs++;
      if ($outs == 3) $this->result_->during += "/TP";
      if ($outs == 2) $this->result_->during += "/DP";
      if (($outs + $this->situation_->outs) < 3 && $third > 0 && $this->situation_->base(2) ) $this->result_->during .= "/SF";
      if ($outs + $this->situation_->outs < 3) $this->advance($third, $second, $first, false); 
      else $this->advance($third>0?0:$third, $second>0?0:$second, $first>0?0:$first, false);
      if ($this->result_->after != "") $this->result_->after .= ",";
      $this->result_->after .= "Bx";
      $this->saveResult();
    }
    public function e1($result) {$this->e($result,1,1,1,1,false);}
    public function e($result, $third, $second, $first, $bat, $before) {
      if ($before) {
        if ($this->result_->before != "") $this->result_->before .= ",";
        $this->result_->before = $result;
      } else {
        $this->result_->during = $result;
      }
      $this->advance($third, $second, $first, $before);
      $this->batter(bat);
      $this->saveResult();
    }
    public function po1($result) { $this->po($result,-1,-1,-1); }
    public function po($result, $third, $second, $first) {
      if ($this->result_->before != "") $this->result_->before .= ",";
      $this->advance($third, $second, $first, true);
      $this->result_->before .= "/PO-" . $result;
      if ($this->situation_->outs == 2) $this->saveResult(); else $this->updateSituation();
    }
    public function wp1() {$this->wp(1,1,1);}
    public function wp($third, $second, $first) {
      $this->advance($third, $second, $first, true);
      $this->result_->before .= "/WP";
      $this->updateSituation();
    }
    public function pb1() {$this->pb(1,1,1);}
    public function pb($third, $second, $first) {
      $this->advance($third, $second, $first, true);
      $this->result_->before .= "/PB";    
      $this->updateSituation();
    }
    public function pb3() {
      if (! $this->situation_->base(3)) {
        $this->pb();
      } else {
        if (! $this->situation_->base(2) && $this->situation_->base(1)) {
          $this->pb(0,0,1);
        }
      }
      $this->updateSituation();
    }
    public function bk1() {$this->bk(1,1,1);}
    public function bk($third, $second, $first) {
      $this->advance($third, $second, $first, true);
      $this->result_->before .= "/BK";    
      $this->updateSituation();
    }
    public function sb1() {$this->sb(1,1,1);}
    public function sb($third, $second, $first) {
      $this->advance($third, $second, $first, true);
      $this->result_->before .= "/SB";    
      $this->updateSituation();
    }
    public function cs1($result) {$this->cs($result,-1,-1,-1); }
    public function cs($result, $third, $second, $first) {
      if ($this->result_->before != "") $this->result_->before += ",";
      $this->advance($third, $second, $first, true);
      $this->result_->before .= "/CS" . $result;
      if ($this->situation_->outs == 2) $this->saveResult(); else $this->updateSituation();
    }
    public function betweenInnings() {return $this->situation_->betweenInnings;}
    public function getLineup($side) {return $this->lineup_[$side];}
    public function undo() {
      if ($this->debug_) print "undo\n";
      if ($this->result_->before == "" && $this->result_->during == "" && $this->result_->after == "") {
        if ($this->debug_) print "Blank result\n";
        if ($this->situation_->betweenInnings) {
          $s = $this->situation_->otherSide();
        } else {
          $s = $this->situation_->side;
        }
        $c = count($this->results_[$s]);
        $this->result_ = $this->results_[$s][$c-1];
        unset($this->results_[$s][$c-1]);
        //$this->updateSituation();
        $this->undo();
      } else {
        if ($this->result_->during != "" || $this->result_->after != "") {
        $outs = substr_count($this->result_->during,"x") + substr_count($this->result_->after,"x");
          $this->result_->during=""; 
          $this->result_->after="";
          //Handle split inning evil
          if ($outs >= $this->situation_->outs && $this->result_->before != "") {
            $this->results_[$this->situation_->side][count($this->results_[$this->situation_->side])]=$this->result_;
            $this->result_ = new Result;
          }
          $this->updateSituation();
        } else {
          $this->result_->before="";
        }
      }
      $this->updateSituation();
//    //System.out.println("After: " + this.toString() + result_);
    }
    public function start() {
      $this->situation_->batter = $this->lineup_[0]->getHitter(0);
      $this->situation_->pitcher = $this->lineup_[1]->getCurrentPitcher();
    }
  }
//public class ProjectScoresheet {
//    
//  public ProjectScoresheet(String s) throws InvalidProjectScoresheetException {
//    if (s.contains("~DH~")) init(Lineup.LineupType.AL);
//    else init(Lineup.LineupType.NL);
//    if (s.startsWith("---\n")) {
//      StringBuilder sb;
//      BufferedReader br = new BufferedReader(new StringReader(s));
//      String line = "";
//      try {
//        br.readLine();
//        br.readLine();
//        for (int i=0; i<2; i++) {
//          line = br.readLine();
//          if (! line.contains(" name: ")) throw new InvalidProjectScoresheetException("Expected name:, found " + line);
//          teamName_[i] = line.replaceFirst(".* name: ", "");
//          line=br.readLine();
//          if (! line.contains(" gameNumber: ")) throw new InvalidProjectScoresheetException("Expected gameNumber:, found "  + line);
//          gameNumber_[i] = line.replaceFirst(".* gameNumber: ", "");
//          line=br.readLine();
//          if (! line.contains(" lineup:")) throw new InvalidProjectScoresheetException("Expected lineup:, found " + line);
//          sb = new StringBuilder("---\n" + line + "\n");
//          line=br.readLine();
//          while (! line.contains(" results:")) {
//            sb.append(line + "\n");
//            line=br.readLine();
//          }
//          lineup_[i]=new Lineup(sb.toString());
//          line=br.readLine();
//          String b="";
//          String d="";
//          String a="";
//          while (line !=null && line.startsWith(" ")) {
//            if (line.startsWith("   -")) {
//              if (!d.equals("")) results_[i].addLast(new Result(b+"~"+d+"~"+a));
//              b="";d="";a="";
//            }
//            if (line.contains(" before: ")) b=line.replaceFirst(".* before: ", "");
//            if (line.contains(" during: ")) d=line.replaceFirst(".* during: ", "");
//            if (line.contains(" after: ")) a=line.replaceFirst(".* after: ", "");
//            line=br.readLine();
//          }
//          if (!d.equals("")) results_[i].addLast(new Result(b+"~"+d+"~"+a));
//        }
//        while(line != null) {
//          if (line.startsWith("date: ")) date_=line.replaceFirst("date: ", ""); 
//          if (line.startsWith("startTime: ")) startTime_=line.replaceFirst("startTime: ", ""); 
//          if (line.startsWith("duration: ")) duration_=line.replaceFirst("duration: ", ""); 
//          line=br.readLine();
//        }
//      } catch (Exception ex) {ex.printStackTrace();throw new InvalidProjectScoresheetException(ex.getMessage()); }
//    } else {
//      String[] sp = s.split("\n",0);
//      int next = 0;
////      System.out.println(sp.length);
//      if (sp.length != 7) throw new InvalidProjectScoresheetException("Not enough fields in input String:" + sp.length);
//      Iterator<Situation.Side> it = EnumSet.allOf(Situation.Side.class).iterator();
//      while (it.hasNext()) {
//        int side = Situation.whichSide(it.next());
//        String team = sp[next++];
//        teamName_[side] = team.substring(0, team.indexOf("("));
//        gameNumber_[side] = team.substring(team.indexOf("(")+ 1, team.indexOf(")"));
////        System.out.println(teamName_[side]);
//        lineup_[side] = new Lineup(sp[next++]);
////        System.out.println(lineup_[side]);
//        String[] rs = sp[next++].split(";",-1);
////        System.out.println(rs.length);
//        for (int i=0; i<rs.length; i++) {
//          if (rs[i].length() > 0) {
////            System.out.println(i);
//            Result rslt = new Result(rs[i]);
//            results_[side].addLast(rslt);
//          }
//        }
//      }
//      String[] dtInfo = sp[next].split("~",-1);
//      if (dtInfo.length != 3) throw new InvalidProjectScoresheetException("Invalid Date/Time Info");
//      date_ = dtInfo[0];
//      startTime_ = dtInfo[1];
//      duration_ = dtInfo[2];
//    }
//    updateSituation();
//  }
?>
