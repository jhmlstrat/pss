<?php
  namespace Scoring;

  class Game {
    private $year;
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
    private $ssLoaded_;
		
    function __construct($year = 2017) {
      $this->year = $year;
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
      $this->bps_ = [];
      $this->bps_[0]=0;
      $this->bps_[1]=0;
      $this->bps_ = [];
      $this->bphr_[0]=0;
      $this->bphr_[1]=0;
      $this->final_ = false;
      $this->ssLoaded__ = false;
    }

    public function toString() {
      $results = '{';
      $results .= '"away":{';
        $results .= '"team":"' . $this->team_[0] . '"';
        $results .= ',"gameNumber":"' . intval($this->gameNumber_[0]) . '"';
        $results .= ',"runs":"' . intval($this->runs_[0]) . '"';
        $results .= ',"hits":"' . intval($this->hits_[0]) . '"';
        $results .= ',"errors":"' . intval($this->errors_[0]) . '"';
        $results .= ',"starter":"' . $this->starter_[0] . '"';
      $results .= '},"home":"{';
        $results .= '"team":"' . $this->team_[1] . '"';
        $results .= ',"gameNumber":"' . intval($this->gameNumber_[1]) . '"';
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
      $results .= ',"day":' . ($this->hasScoreSheet() ? "true" : "false");
      $results .= ',"bps":{"left":"' . intval($this->bps_[0]) . '","right":"' . intval($this->bps_[1]) . '"}';
      $results .= ',"bphr":"left":"' . intval($this->bphr_[0]) . '","right":"' . intval($this->bphr_[1]) . '"}';
      $results .= ',"final":' . ($this->final_ ? "true" : "false"); 
      $results .= '}';
      return $results;
    }
    public function json() {
      return json_decode($this->toString());
    }
    public function hasScoreSheet() {
      return file_exists('../data/' . $this->year . '/' . strtoupper($this->team_[0]) . sprintf('%03d', $this->gameNumber_[0]).  strtoupper($this->team_[1]) . sprintf('%03d', $this->gameNumber_[1]) . '.pss');
    }
    public function getScoreSheet() {
      // TBD
      return '{}';
    }
  }
?>
