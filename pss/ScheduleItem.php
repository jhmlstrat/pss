<?php

  namespace Scoring;

  require_once "../pss/Seasons.php";
  require_once "../pss/Game.php";

class ScheduleItem
{
    public $home_;
    public $away_;
    public $games_;
    public $season_;
    public $results_ = [];

    public function toString()
    {
        $results = '[';
        foreach ($this->results_ as $result) {
            if ($results != '[') {
                $results .= ',';
            }
            $results .= $result->toString();
        }
        $results .= ']';
        return '{"scheduleItem":{"homeTeam":"' . $this->home_ . 
            '","awayTeam":"' . $this->away_ . '","numberOfGames":"' . $this->games_. 
            '","season":"' . $this->season_ . '","results":' . $results . '}}';
    }
    public function json()
    {
        return json_decode($this->toString());
    }
    public static function newSI($home, $away, $games, $season)
    {
        $inst = new self();
        $inst->home_ = $home;
        $inst->away_ = $away;
        $inst->games_ = $games;
        $inst->season_ = $season;
        return $inst;
    }
}
?>
