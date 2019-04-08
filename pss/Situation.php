<?php
  namespace ProjectScoresheet;
  require_once "Side.php";
  class Situation {
    const playerStub=".                             ";
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
   
    function __construct() {
      $this->inning=1;
      $this->side=Side::Visitor;
      $this->outs=0;
      $this->runs=array(Side::Visitor=>0,Side::Home=>0);
      $this->hits=array(Side::Visitor=>0,Side::Home=>0);
      $this->errors=array(Side::Visitor=>0,Side::Home=>0);
      $this->runsPerInning=array(Side::Visitor=>array(),Side::Home=>array());
      for ($i = 1; $i <= Situation::MAX_INNINGS; $i ++) {
        $this->runsPerInning[Side::Visitor][$i]=0;
        $this->runsPerInning[Side::Home][$i]=0;
      }
      $this->batter=null;
      $this->pitcher=null;
      $this->runner=array(1=>null,2=>null,3=>null);
      $this->betweenInnings=true;
    } 
    
    public function switchSides() {
      $this->outs=0;
      $this->runner[1] = null;
      $this->runner[2] = null;
      $this->runner[3] = null;
      if ($this->side == Side::Visitor) $this->side=Side::Home;
      else {
        $this->side = Side::Visitor;
        $this->inning ++;
      }
    }
//    public int whichSide() {return whichSide(side);}
//    public static int whichSide(Side side) {if (side == Side.Visitor) return 0; return 1;}
//    public static Side whichSide(int side) {if (side == 0) return Side.Visitor; return Side.Home;}
    public function addRun() {$this->runs[$this->side] ++; $this->runsPerInning[$this->side][$this->inning] ++;}
    public function addHit() {$this->hits[$this->side] ++;}
    public function addError() {$this->errors[$this->otherSide()] ++;}
    public function otherSide() {if ($this->side == Side::Visitor) return Side::Home; return Side::Visitor;}
    public function base($b) {return $this->runner[$b] !== null;}
    public function gameOver() { 
      #print 'Inning: ' . $this->inning . "\n";
      #print 'Side: ' . $this->side . "\n";
      #print 'Home: ' . $this->runs[Side::Home] . "\n";
      #print 'Away: ' . $this->runs[Side::Visitor] . "\n";
      #print 'Between: ' . $this->betweenInnings . "\n";
      if ($this->inning >= 9 && $this->side == Side::Home && $this->runs[Side::Home] > $this->runs[Side::Visitor]) return true;
      if ($this->inning > 9 && $this->betweenInnings && $this->side==Side::Visitor && $this->runs[Side::Home] < $this->runs[Side::Visitor]) return true;
      return false;
    }
    
    public function toString() {
      return '{"situation":{' .
               '"outs":"' . $this->outs . '",' .
               '"runsV":"' . $this->runs[Side::Visitor] . '",' .
               '"runsH":"' . $this->runs[Side::Home] . '",' .
               '"hitsV":"' . $this->hits[Side::Visitor] . '",' .
               '"hitsH":"' . $this->hits[Side::Home] . '",' .
               '"errorsV":"' . $this->errors[Side::Visitor] . '",' .
               '"errorsH":"' . $this->errors[Side::Home] . '",' .
               '"inning":"' . $this->inning . '",' .
               '"side":"' . $this->side . '",' .
               '"first":"' . ($this->runner[1] == null ? "" : trim($this->runner[1]->name)) . '",' .
               '"second":"' . ($this->runner[2] == null ? "" : trim($this->runner[2]->name)) . '",' .
               '"third":"' . ($this->runner[3] == null ? "" : trim($this->runner[3]->name)) . '",' .
               '"batter":"' . ($this->batter == null ? "" : trim($this->batter->name)) . '",' .
               '"pitcher":"' . ($this->pitcher == null ? "" : trim($this->pitcher->name)) . '",' .
               '"gameOver":"' . ($this->gameOver()?"true":"false") . '"}}';
    }
    public function json() {
      return json_decode($this->toString());
    }
  }
?>
