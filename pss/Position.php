<?php

  namespace ProjectScoresheet;

  require_once "When.php";
class Position
{
    public $position;
    private $_when;
    public $rating;
    public $e;
    public $arm;
    public $t;
    public $pb;
    public function __construct()
    {
        $this->position = 0;
        $this->_when = new When();
        $this->rating = 0;
        $this->e = 0;
        $this->arm = 0;
        $this->t = 0;
        $this->pb = 0;
    }
    public static function fromString($str)
    {
        $inst = new self();
        $pos = json_decode($str);
        $inst->position = Position::position($pos->position->pos);
        $whenStr = '{"when":' . json_encode($pos->position->when) . '}';
        $inst->_when = When::fromString($whenStr);
        return $inst;
    }
    public function p($position)
    {
        $this->position = $position;
    }
    public function when($away, $home)
    {
        $this->_when->away($away);
        $this->_when->home($home);
    }
    public function toString()
    {
        return '{"position":{"pos":"' . $this->positionString($this->position) . 
            '",' . substr($this->_when->toString(), 1, -1) . '}}';
    }
    public function json()
    {
        return json_decode($this->toString());
    }
    public static function fromStratString($str)
    {
        $inst = new self();
        $pos = json_decode($str);
        $inst->position = Position::position($pos->position->pos);
        $inst->rating = $pos->position->rating;
        $inst->e = $pos->position->e;
        $inst->arm = $pos->position->arm;
        $inst->t = $pos->position->t;
        $inst->pb = $pos->position->pb;
        return $inst;
    }
    public function stratString()
    {
        return '{"position":{"pos":"' . $this->positionString($this->position) . 
            '","rating":"' . $this->rating . '","e":"' . $this->e . '","arm":"' . 
            $this->arm . '","t":"' . $this->t . '","pb":"' . $this->pb . '"}}';
    }
    public function stratJson()
    {
        return json_decode($this->stratString());
    }
    public static function positionArray()
    {
        return array(
            "DH","P","C","B1","B2","B3","SS","LF","CF","RF","PH","PR","OOG"
        );
    }
    public static function position($positionString)
    {
        foreach (Position::positionArray() as $i => $p) {
            if ($p == $positionString) {
                return $i;
            }
        }
        print "Position::position - bad parameter -{$positionString}-\n";
        exit -1;
    }
    public static function positionString($pos)
    {
        foreach (Position::positionArray() as $i => $p) {
            if ($i == $pos) {
                return $p;
            }
        }
        print "Position::positionString - bad parameter {$pos}\n";
        exit -1;
    }
    public function getWhen()
    {
	return $this->_when;
    }
}
?>
