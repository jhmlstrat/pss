<?php
  namespace Jhml;
  require_once "MoveType.php";
  require_once "RosterItem.php";

  class Roster  {

    public $batters = array();
    public $pitchers = array();
    public $team = '';

    public function addBatter ($ri) {
      $index = -1;
      for ($i=0; $i < count($this->batters); $i++) {
        if ($this->batters[$i]->player->name == $ri->player->name) {
          $index = $i;
        }
      }
      if ($index == -1) $this->batters[] = $ri;
      usort($this->batters,array("\Scoring\RosterItem","cmp"));	
    }

    public function addPitcher ($ri) {
      $index = -1;
      for ($i=0; $i < count($this->pitchers); $i++) {
        if ($this->pitchers[$i]->player->name == $ri->player->name) {
          $index = $i;
        }
      }
      if ($index == -1) $this->pitchers[] = $ri;
      usort($this->pitchers,array("\Scoring\RosterItem","cmp"));
    }
    public function getBatters() {return $this->batters;}
    public function getPitchers() {return $this->pitchers;}

    public function isValid($gameNo = 1) {
      if ($gameNo > 84) return true;
      $majors = $this->getMajors($gameNo);
      if ((count($majors->batters) + count($majors->pitchers)) <= 25) return true;
      return false;
    }
    public function getMajors($gameNo = 1) {
      $rtn = new self;
      $rtn->team = $this->team;
      for ($i=0; $i < count($this->batters); $i++) {
        if ($this->batters[$i]->isMajors($gameNo))
          $rtn->batters[] = $this->batters[$i];
      }
      for ($i=0; $i < count($this->pitchers); $i++) {
        if ($this->pitchers[$i]->isMajors($gameNo))
          $rtn->pitchers[] = $this->pitchers[$i];
      }
      return $rtn;		
    }
    public function getMinors($gameNo = 1) {
      $rtn = new self;
      $rtn->team = $this->team;
      for ($i=0; $i < count($this->batters); $i++) {
        if ($this->batters[$i]->isMinors($gameNo))
          $rtn->batters[] = $this->batters[$i];
      }
      for ($i=0; $i < count($this->pitchers); $i++) {
        if ($this->pitchers[$i]->isMinors($gameNo))
          $rtn->pitchers[] = $this->pitchers[$i];
      }
      return $rtn;		
    }
    public function processTradedFor($str) {
      return \Scoring\RosterItem::processTradedFor($str);
    }
    public function processMoveFile($str) {
      for ($i=0; $i < count($this->batters); $i++) {
        \Scoring\RosterItem::addMoveFileString($this->batters[$i],$str);
      }
      for ($i=0; $i < count($this->pitchers); $i++) {
        \Scoring\RosterItem::addMoveFileString($this->pitchers[$i],$str);
      }

    }
    public function move($name, $moveType, $game) {
      for ($i=0; $i < count($this->batters); $i++) {
        if ($this->batters[$i]->player->name == $name) {
          \Scoring\RosterItem::processMove($this->batters[$i], $game, $moveType);
        }
      }
      for ($i=0; $i < count($this->pitchers); $i++) {
        if ($this->pitchers[$i]->player->name == $name) {
          \Scoring\RosterItem::processMove($this->pitchers[$i], $game, $moveType);
        }
      }
    }
    public static function fromString($str) {
      $inst = new self();
      $js = json_decode($str);
      $inst->team = $js->roster->team;
      $bs =  $js->roster->batters;
      for ($i =0; $i < count($bs); $i++) {
        $ri = \Scoring\RosterItem::fromString(json_encode($bs[$i]));
        $ri->team = $js->roster->team;
        $inst->addBatter($ri);
      }
      $ps =  $js->roster->pitchers;
      for ($i =0; $i < count($ps); $i++) {
        $ri = \Scoring\RosterItem::fromString(json_encode($ps[$i]));
        $ri->team = $js->roster->team;
        $inst->addPitcher($ri);
      }
      return $inst;
    }
    public function toString($includeStrat = false) {
      usort($this->batters,array("\Scoring\RosterItem","cmp"));	
      usort($this->pitchers,array("\Scoring\RosterItem","cmp"));	
      $rtn = '{"roster":{"team":"' . $this->team . '"';
      $rtn .= ',"batters":[';
      for ($i =0; $i < count($this->batters); $i++) {
        if ($i > 0) $rtn .= ',';
        $rtn .= $this->batters[$i]->toString($includeStrat);
      }
      $rtn .= ']';
      $rtn .= ',"pitchers":[';
      for ($i =0; $i < count($this->pitchers); $i++) {
        if ($i > 0) $rtn .= ',';
        $rtn .= $this->pitchers[$i]->toString($includeStrat);
      }
      $rtn .= ']';
      $rtn .= '}}';
      return $rtn;
    }
    public function json() {
      return json_decode($this->toString());
    }
  }
?>
