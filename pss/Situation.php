<?php

  namespace ProjectScoresheet;

  require_once "Side.php";
class Situation
{
    const MAX_INNINGS=100;
    public $inning;
    public $side;
    public $outs;
    //    public int balls=0;
    //    public int strikes=0;
    public $runs;
    public $hits;
    public $errors;
    public $runsPerInning;
    public $batter;
    public $pitcher;
    public $runner;
    public $betweenInnings;

    public function __construct()
    {
        $this->inning=1;
        $this->side=Side::VISITOR;
        $this->outs=0;
        $this->runs=array(Side::VISITOR=>0,Side::HOME=>0);
        $this->hits=array(Side::VISITOR=>0,Side::HOME=>0);
        $this->errors=array(Side::VISITOR=>0,Side::HOME=>0);
        $this->runsPerInning=array(Side::VISITOR=>array(),Side::HOME=>array());
        for ($i = 1; $i <= Situation::MAX_INNINGS; $i ++) {
            $this->runsPerInning[Side::VISITOR][$i]=0;
            $this->runsPerInning[Side::HOME][$i]=0;
        }
        $this->batter=null;
        $this->pitcher=null;
        $this->runner=array(1=>null,2=>null,3=>null);
        $this->betweenInnings=true;
    }

    public function switchSides()
    {
        $this->outs=0;
        $this->runner[1] = null;
        $this->runner[2] = null;
        $this->runner[3] = null;
        if ($this->side == Side::VISITOR) {
            $this->side=Side::HOME;
        } else {
            $this->side = Side::VISITOR;
            $this->inning ++;
        }
    }

    public function addRun()
    {
        $this->runs[$this->side] ++;
        $this->runsPerInning[$this->side][$this->inning] ++;
    }
    public function addHit()
    {
        $this->hits[$this->side] ++;
    }
    public function addError()
    {
        $this->errors[$this->otherSide()] ++;
    }
    public function otherSide()
    {
        if ($this->side == Side::VISITOR) {
            return Side::HOME;
        }
        return Side::VISITOR;
    }
    public function base($b)
    {
        return $this->runner[$b] !== null;
    }
    public function gameOver()
    {
        // print 'Inning: ' . $this->inning . "\n";
        // print 'Side: ' . $this->side . "\n";
        // print 'Home: ' . $this->runs[Side::HOME] . "\n";
        // print 'Away: ' . $this->runs[Side::VISITOR] . "\n";
        // print 'Between: ' . $this->betweenInnings . "\n";
        if ($this->inning >= 9 && $this->side == Side::HOME 
            && $this->runs[Side::HOME] > $this->runs[Side::VISITOR]
        ) {
            return true;
        }
        if ($this->inning > 9 && $this->betweenInnings && $this->side==Side::VISITOR 
            && $this->runs[Side::HOME] < $this->runs[Side::VISITOR]
        ) {
            return true;
        }
        return false;
    }

    public function toString()
    {
        $rtn =  '{"situation":{';
        $rtn .= '"outs":"' . $this->outs . '",';
        $rtn .= '"inning":"' . $this->inning . '",';
        $rtn .= '"side":"' . $this->side . '",';
        $rtn .= '"first":"' . ($this->runner[1] == null ? "" : trim($this->runner[1]->name)) . '",';
        $rtn .= '"second":"' . ($this->runner[2] == null ? "" : trim($this->runner[2]->name)) . '",';
        $rtn .= '"third":"' . ($this->runner[3] == null ? "" : trim($this->runner[3]->name)) . '",';
        $rtn .= '"batter":"' . ($this->batter == null ? "" : trim($this->batter->name)) . '",';
        $rtn .= '"pitcher":"' . ($this->pitcher == null ? "" : trim($this->pitcher->name)) . '",';
        $rtn .= '"betweenInnings":' . ($this->betweenInnings ? "true" : "false") . ',';
        $rtn .= '"runs":["' . $this->runs[Side::VISITOR] . '","' . $this->runs[Side::HOME] . '"],';
        $rtn .= '"hits":["' . $this->hits[Side::VISITOR] . '","' . $this->hits[Side::HOME] . '"],';
        $rtn .= '"errors":["' . $this->errors[Side::VISITOR] . '","' . $this->errors[Side::HOME] . '"],';
        $rtn .= '"runsPerInning":[[';
        for ($i =0; $i < $this->inning; $i++) {
            if ($i > 0) $rtn .= ',';
            $rtn .= '"' .  $this->runsPerInning[0][$i+1] . '"';
        }
        $rtn .= '],[';
        for ($i =0; $i < ($this->side == 0 ? $this->inning - 1 : $this->inning); $i++) {
            if ($i > 0) $rtn .= ',';
            $rtn .= '"' .  $this->runsPerInning[1][$i+1] . '"';
        }
        $rtn .= ']],';
        $rtn .= '"gameOver":' . ($this->gameOver() ? "true" : "false") . '}}';
        return $rtn;
    }
    public function json()
    {
        return json_decode($this->toString());
    }
}
?>
