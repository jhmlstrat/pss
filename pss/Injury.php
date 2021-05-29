<?php

  namespace Scoring;

class Injury
{
    public $gameNumber=0;
    public $duration=0;

    public static function fromString($str)
    {
        $inst = new self();
        $js = json_decode($str);
        if (array_key_exists("gameNumber", $js->injury)) {
            intval($inst->gameNumber = $js->injury->gameNumber);
        }
        if (array_key_exists("duration", $js->injury)) {
            intval($inst->duration = $js->injury->duration);
        }
        return $inst;
    }
    public function toString()
    {
        return '{"injury":{"gameNumber":"' . $this->gameNumber . 
           '","duration":"' . $this->duration . '"}}';
    }
}
?>
