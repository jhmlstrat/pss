<?php

  namespace Scoring;

class Injury
{
    public $name='';
    public $gameNumber=0;
    public $duration=0;
    public $replace='';

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
    public static function fromStatFileString($str) {
        $inst = new self();
	list($name,$duration,$replace) = explode("~",$str);
	if ($name != "none") $inst->name = $name;
	if ($duration != "REM") $inst->duration = $duration;
	if ($replace != "none") $inst->replace = $replace;
	return $inst;
    }
    public function toStatFileString() {
	if ($this->name == "") { $rtn = "none"; }
	else { $rtn = $this->name; }
	$rtn .= "~";
	if ($this->duration == "") { $rtn .= "REM"; }
	else { $rtn .= $this->duration; }
	$rtn .= "~";
	if ($this->replace == "") { $rtn .= "none"; }
	else { $rtn .= $this->replace; }
	return $rtn;
    }
}
?>
