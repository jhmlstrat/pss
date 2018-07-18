<?php
  namespace Scoring;
  require_once "MoveType.php";
  require_once "RosterItem.php";

  abstract class RosterBase {
    public $batters = array();
    public $pitchers = array();
    public $team = '';

    public abstract function writeRosterFile();
    public abstract function loadRosterFile();

    public function move($name, $moveType, $game) {
      for ($i=0; $i < count($this->batters); $i++) {
        if ($this->batters[$i]->player->name == $name) {
          RosterItem::processMove($this->batters[$i], $game, $moveType);
        }
      }
      for ($i=0; $i < count($this->pitchers); $i++) {
        if ($this->pitchers[$i]->player->name == $name) {
          RosterItem::processMove($this->pitchers[$i], $game, $moveType);
        }
      }
    }
    public function addHitter ($ri) {
      $index = -1;
      for ($i=0; $i < count($this->batters); $i++) {
        if ($this->batters[$i]->$player->name == $ri->player->name) {
          $index = $i;
        }
      }
      if ($index == -1) $this->batters[] = $ri;
    }
    public function addPitcher ($ri) {
      $index = -1;
      for ($i=0; $i < count($this->pitchers); $i++) {
        if ($this->pitchers[$i]->$player->name == $ri->player->name) {
          $index = $i;
        }
      }
      if ($index == -1) $this->pitchers[] = $ri;
    }
    public function getBatters() {usort($this->batters,array("\Scoring\RosterItem","cmp"));return $this->batters;}
    public function getPitchers() {return usort($this->pitchers,array("\Scoring\RosterItem","cmp"));return $this->pitchers;}

    public function getMajors($gameNo = 1) {
      $rtn = new self;
      for ($i=0; $i < count($this->batters); $i++) {
        if ($this->batters[$i]->isMajors($gameNo))
          $rtn->batters[] = $this->batters[$i];
      }
      for ($i=0; $i < count($this->pitchers); $i++) {
        if ($this->pitchers[$i]->isMajors($gameNo))
          $rtn->pitchers[] = $this->pitchers[$i];
      }
      usort($rtn->batters,array("\Scoring\RosterItem","cmp"));		
      usort($rtn->pitchers,array("\Scoring\RosterItem","cmp"));		
      return $rtn;		
    }
    public function getMinors() {
      $rtn = new self;
      for ($i=0; $i < count($this->batters); $i++) {
        if ($this->batters[$i]->isMinors($gameNo))
          $rtn->batters[] = $this->batters[$i];
      }
      for ($i=0; $i < count($this->pitchers); $i++) {
        if ($this->pitchers[$i]->isMinors($gameNo))
          $rtn->pitchers[] = $this->pitchers[$i];
      }
      usort($rtn->batters,array("\Scoring\RosterItem","cmp"));		
      usort($rtn->pitchers,array("\Scoring\RosterItem","cmp"));		
      return $rtn;		
      for ($i=0; $i < count($this->batters); $i++) {
        if (($this->batters[$i]->team_ == $team) and
            ($this->batters[$i]->moveType == MoveType::ToMinors or
             $this->batters[$i]->moveType == MoveType::OnDL)) {
          $rtn[] = $this->batters[$i];
        }
      }
      for ($i=0; $i < count($this->pitchers); $i++) {
        if (($this->pitchers[$i]->team_ == $team) and
            ($this->pitchers[$i]->moveType == MoveType::ToMinors or 
             $this->pitchers[$i]->moveType == MoveType::OnDL)) {
          $rtn[] = $this->pitchers[$i];
        }
      }
      usort($rtn,array("\Scoring\RosterItem","cmp"));		
      return $rtn;		
    }
  }
?>
