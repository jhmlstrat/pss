<?php

  namespace ProjectScoresheet;

  require_once 'Player.php';
class Lineup
{
    private $_battingOrder;
    private $_pitchingOrder;
    public function __construct()
    {
        $this->_battingOrder=array();
        for ($i = 0; $i < 9; $i ++) {
            $this->_battingOrder[$i]=array();
        }
        $this->_pitchingOrder=array();
    }
    public static function fromString($str)
    {
        $inst = new self();
        // print $str . "\n";
        $lu = json_decode('{' . preg_replace('/,"rotation".*/', '', $str) . '}');
        $ro = json_decode(
            '{' .  preg_replace('/.*,"rotation"/', '"rotation"', $str) . '}'
        );
        foreach ($lu as $rows) {
            $i = 0;
            if ($rows != null) {
                foreach ($rows as $row) {
                    foreach ($row as $p) {
                        array_push(
                            $inst->_battingOrder[$i], 
                            Player::fromString(json_encode($p))
                        );
                    }
                    $i ++;
                }
            }
        }
        foreach ($ro as $row) {
            if ($row != null) {
                foreach ($row as $p) {
                    array_push(
                        $inst->_pitchingOrder, 
                        Player::fromString(json_encode($p))
                    );
                }
            }
        }
        return $inst;
    }
    public function toString()
    {
        $rtn = '"lineup":[[';
        for ($i=0; $i < count($this->_battingOrder); $i ++) {
            if ($i > 0) {
                $rtn .= '],[';
            }
            for ($j=0; $j < count($this->_battingOrder[$i]); $j ++) {
                if ($j > 0) {
                    $rtn .= ',';
                }
                $rtn .= $this->_battingOrder[$i][$j]->toString();
            }
        }
        $rtn .= ']],"rotation":[';
        for ($i=0; $i < count($this->_pitchingOrder); $i ++) {
            if ($i > 0) {
                $rtn .= ',';
            }
            $rtn .= $this->_pitchingOrder[$i]->toString();
        }
        $rtn .= ']';

        return $rtn;
    }
    public function isValid()
    {
        $rtn = (count($this->_pitchingOrder) !== 0);
        $tmp=array();
        for ($i = 0; $i < 9; $i ++) {
            $tmp[$i]=false;
        }
        for ($i = 0; $i < 9; $i ++) {
            $c = count($this->_battingOrder[$i]);
            if ($c > 0) {
                $play = $this->_battingOrder[$i][$c-1];
                $c = count($play->positions);
                if ($c > 0) {
                    $ord = $play->positions[$c - 1]->position;
                    if ($ord === 0) {
                        $ord = 1;
                    }
                    if ($ord <= count($tmp)) {
                        $tmp[$ord-1] = true;
                    }
                }
            }
        }
        for ($i=0; $i<9; $i ++) {
            $rtn = $rtn && $tmp[$i];
        }
        return $rtn;
    }
    public function getCurrentPitcher()
    {
        if (count($this->_pitchingOrder) == 0) {
            return null;
        }
        return $this->_pitchingOrder[count($this->_pitchingOrder)-1];
    }
    public function getHitter($spot)
    {
        if (count($this->_battingOrder[$spot]) == 0) {
            return null;
        }
        return $this->_battingOrder[$spot][count($this->_battingOrder[$spot])-1];
    }
    public function getHitters($spot)
    {
        return $this->_battingOrder[$spot];
    }
    public function getPitchers()
    {
        return $this->_pitchingOrder;
    }
    public function insertIntoBat($spot, $player)
    {
        $this->_battingOrder[$spot][] = $player;
    }
    public function insertIntoPitch($player)
    {
        $this->_pitchingOrder[] = $player;
    }
    public function movePlayer($spot, $pos)
    {
        $this->_battingOrder[$spot]
            [count($this->_battingOrder[$spot])-1]->newPosition($pos);
    }
}

?>
