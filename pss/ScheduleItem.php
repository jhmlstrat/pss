<?php
  namespace Scoring;
  require_once "../pss/Seasons.php";

  class ScheduleItem {
    public $home_;
    public $away_;
    public $games_;
    public $season_;
    public $results_ = [];
		
    public function toString() {
      $results = '[';
      foreach ($this->results_ as $result) {
        if ($results != '[') $results .= ',';
        $results .= '{"vRun":';
        $results .= (isset($result['vRun']) ? $result['vRun'] : 0);
        $results .= ',"vHit":';
        $results .= isset($result['vHit']) ? $result['vHit'] : 0;
        $results .= ',"vE":';
        $results .= isset($result['vE']) ? $result['vE'] : 0;
        $results .= ',"hRun":';
        $results .= isset($result['hRun']) ? $result['hRun'] : 0;
        $results .= ',"hHit":';
        $results .= isset($result['hHit']) ? $result['hHit'] : 0;
        $results .= ',"hE":';
        $results .= isset($result['hE']) ? $result['hE'] : 0;
        $results .= '}';
      }
      $results .= ']';
      return '{"scheduleItem":{"homeTeam":"' . $this->home_ . '","awayTeam":"' . $this->away_ . '","numberOfGames":"' . $this->games_ . '","season":"' . $this->season_ . '","results":' . $results . '}}';
    }
    public function json() {
      return json_decode($this->toString());
    }
  }
?>
