<?php
  namespace Scoring;

  class ScheduleItem {
    public $home_;
    public $away_;
    public $games_;
    public $season_;
    public $results_;
		
    public function toString() {
	    return '{"scheduleItem":{"homeTeam":"' . $this->home_ . '","awayTeam":"' . $this->away_ . '","numberOfGames":"' . $this->games_ . '","season":"' . $this->season_ . '","results":"' . $this->results_ . '"}}';
    }
    public function json() {
      return json_decode($this->toString());
    }
  }
?>
