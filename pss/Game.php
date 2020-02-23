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
    }

    public function toString() {
      $results = '{';
      $results .= '"visitor":"' . $this->team_[0] . '"';
      $results .= ',"vGameNo":"' . intval($this->gameNumber_[0]) . '"';
      $results .= ',"vRun":"' . intval($this->runs_[0]) . '"';
      $results .= ',"vHit":"' . intval($this->hits_[0]) . '"';
      $results .= ',"vE":"' . intval($this->errors_[0]) . '"';
      $results .= ',"home":"' . $this->team_[1] . '"';
      $results .= ',"hGameNo":"' . intval($this->gameNumber_[1]) . '"';
      $results .= ',"hRun":"' . intval($this->runs_[1]) . '"';
      $results .= ',"hHit":"' . intval($this->hits_[1]) . '"';
      $results .= ',"hE":"' . intval($this->errors_[1]) . '"';
      $results .= ',"innings":"' . intval($this->innings_) . '"';
      $results .= ',"date":"' . $this->date_ . '"';
      $results .= ',"scoreSheet":' . ($this->hasScoreSheet() ? "true" : "false");
      if ($this->hasScoreSheet()) {
        $results .= ',"vStarter":"' . $this->starter_[0] . '"';
        $results .= ',"hStarter":"' . $this->starter_[1] . '"';
        $results .= ',"winner":"' . $this->winner_ . '"';
        $results .= ',"loser":"' . $this->loser_ . '"';
        $results .= ',"cwsavehStarter":"' . $this->save_ . '"';
        $results .= ',"day":' . ($this->hasScoreSheet() ? "true" : "false");
        $results .= ',"bpsL":"' . intval($this->bps_[0]) . '"';
        $results .= ',"bpsR":"' . intval($this->bps_[1]) . '"';
        $results .= ',"bphrL":"' . intval($this->bphr_[0]) . '"';
        $results .= ',"bphrR":"' . intval($this->bphr_[1]) . '"';
      }
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
