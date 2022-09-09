<?php

  namespace Scoring;

  require_once 'MoveType.php';

class Move
{
    public $name="";
    public $moveType=0;
    public $gameNumber=0;

    public static function fromString($str)
    {
        $inst = new self();
        $js = json_decode($str);
        if (property_exists($js->move, "moveType")) {
            $inst->moveType = MoveType::fromString($js->move->moveType);
        }
        if (property_exists($js->move, "gameNumber")) {
            $inst->gameNumber = intval($js->move->gameNumber);
        }
        return $inst;
    }
    public function toString()
    {
        return '{"move":{"moveType":"' . MoveType::toString($this->moveType) .
            '","gameNumber":"' . $this->gameNumber . '"}}';
    }
    public static function fromStatFileString($str) {
        $instU = new self();
        $instD = new self();
        list($nameU,$moveU,$nameD, $moveD) = explode("~",$str);
        if ($nameU != "none") $instU->name = $nameU;
        $instU->moveType = MoveType::fromString($moveU);
        if ($nameD != "none") $instD->name = $nameD;
        $instD->moveType = MoveType::fromString($moveD);
        return [$instU, $instD];
    }
    public function toStatFileString() {
        if ($this->name == "") { $rtn = "none"; }
        else { $rtn = $this->name; }
	$rtn .= "~";
	$rtn .= MoveType::toString($this->moveType);
	return $rtn;
    }
}
?>
