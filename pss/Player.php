<?php
  namespace ProjectScoresheet;
  require_once 'Position.php';
  class Player {
    public $name;
    public $realTeam;
    public $hand;
    //public $positions;
    public $positionsPlayed;
    public $ab;
    public $bb;
    public $hit;
    public $ob;
    public $tb;
    public $hr;
    public $bps;
    public $bphr;
    public $cl;
    public $power;
    public $dp;
    public $running;
    public $hitAndRun;
    public $bunting;
    public $lead;
    public $caught;
    public $first;
    public $second;
    public $ip;
    public $hold;
    public $endure;
    public $balk;
    public $wp;
    public $batting;
    public $age;
    function __construct() {
      $this->name='';
      $this->realTeam='';
      $this->hand = "R";
      //$this->positions=array();
      $this->positionsPlayed=array();
      $this->ab = 0;
      $this->bb = '';
      $this->hit=array();
      $this->ob=array();
      $this->tb=array();
      $this->hr=array();
      $this->bps=array();
      $this->bphr=array();
      $this->cl=array();
      $this->power=array();
      $this->dp=array();
      $this->hit[0]=0.0;
      $this->hit[1]=0.0;
      $this->ob[0]=0.0;
      $this->ob[1]=0.0;
      $this->tb[0]=0.0;
      $this->tb[1]=0.0;
      $this->hr[0]=0.0;
      $this->hr[1]=0.0;
      $this->bps[0]=0.0;
      $this->bps[1]=0.0;
      $this->bphr[0]=0.0;
      $this->bphr[1]=0.0;
      $this->cl[0]=0.0;
      $this->cl[1]=0.0;
      $this->power[0]='';
      $this->power[1]='';
      $this->dp[0]=0.0;
      $this->dp[1]=0.0;
      $this->running = "";
      $this->hitAndRun = "";
      $this->bunting = "";
      $this->lead = "";
      $this->caught = "";
      $this->first = 0;
      $this->second = 0;
      $this->ip=0.0;
      $this->hold=0;
      $this->endure='';
      $this->balk=0;
      $this->wp=0;
      $this->batting='';
      $this->age = 99;
    }
    public static function initial($name) {
      $inst = new self();
      $inst->name = $name;
      //if (! is_null($position))$inst->positions[]=$position;
      return $inst;
    }
    public static function fromString($str) {
      $inst = new self();
      $play = json_decode($str);
      $inst->name = $play->player->name;
      //$pos = $play->player->positions;
      //for ($i = 0; $i < count($pos); $i ++) {
      //  array_push($inst->positions,Position::fromString(json_encode($pos[$i])));
      //}
      if (array_key_exists("strat",$play->player)) {
        $strat = $play->player->strat;
        $inst->realTeam=$strat->realTeam;
        $inst->hand=$strat->hand;
        $pos = $strat->positionsPlayed;
        for ($i = 0; $i < count($pos); $i ++) {
          array_push($inst->positionsPlayed,Position::fromStratString(json_encode($pos[$i])));
        }
        $inst->ab=intval($strat->ab);
        $inst->bb=$strat->bb;
        $inst->hit[0]=floatval($strat->hitL);
        $inst->hit[1]=floatval($strat->hitR);
        $inst->ob[0]=floatval($strat->obL);
        $inst->ob[1]=floatval($strat->obR);
        $inst->tb[0]=floatval($strat->tbL);
        $inst->tb[1]=floatval($strat->tbR);
        $inst->hr[0]=floatval($strat->hrL);
        $inst->hr[1]=floatval($strat->hrR);
        $inst->bps[0]=floatval($strat->bpsL);
        $inst->bps[1]=floatval($strat->bpsR);
        $inst->bphr[0]=floatval($strat->bphrL);
        $inst->bphr[1]=floatval($strat->bphrR);
        $inst->cl[0]=floatval($strat->clL);
        $inst->cl[1]=floatval($strat->clR);
        $inst->power[0]=$strat->powerL;
        $inst->power[1]=$strat->powerR;
        $inst->dp[0]=floatval($strat->dpL);
        $inst->dp[1]=floatval($strat->dpR);
        $inst->running=$strat->running;
        $inst->hitAndRun=$strat->hitAndRun;
        $inst->bunting=$strat->bunting;
        $inst->lead=$strat->lead;
        $inst->caught=$strat->caught;
        $inst->first=intval($strat->first);
        $inst->second=intval($strat->second);
        $inst->ip=floatval($strat->ip);
        $inst->hold=intval($strat->hold);
        $inst->endure=$strat->endurance;
        $inst->balk=intval($strat->balk);
        $inst->wp=intval($strat->wp);
        $inst->batting=$strat->batting;
        $inst->age=intval($strat->age);
      }
      return $inst;
    }
    //public function newPosition($position) {
    //  $this->positions[]=$position;
    //}
    //public function clearposition() {
    //  $this->positions=array();
    //}
    public function toString($includeStrat = false) {
      //$rtn = '{"player":{"name":"' . $this->name . '",positions":[';
      $rtn = '{"player":{"name":"' . $this->name . '"';
      //$count = count($this->positions);
      //$oog = false;
      //for ($i = 0; $i < $count; $i ++) {
      //  $posit=$this->positions[$i]->json()->pos;
      //  if ($posit == 'OOG') {
      //    $oog = true;
      //  } else {
      //   if (!$oog) {
      //      if ($i != 0) $rtn .= ',';
      //      $rtn .= $this->positions[$i]->toString();
      //    }
      //  }
      //}
      //$rtn .= ']';
      if ($includeStrat) {
        $rtn .= ',"strat":{';
        $rtn .= '"realTeam":"' . $this->realTeam . '"';
        $rtn .= ',"hand":"' . $this->hand  . '"';
        $count = count($this->positionsPlayed);
        $rtn .= ',"positionsPlayed":[';
        for ($i = 0; $i < $count; $i ++) {
          if ($i != 0) $rtn .= ',';
          $rtn.=$this->positionsPlayed[$i]->stratString();
        }
        $rtn .= ']';
        $rtn .= ',"ab":"' . $this->ab  . '"';
        $rtn .= ',"bb":"' . $this->bb  . '"';
        $rtn .= ',"hitL":"' . number_format($this->hit[0],1) . '"';
        $rtn .= ',"hitR":"' . number_format($this->hit[1],1) . '"';
        $rtn .= ',"obL":"' . number_format($this->ob[0],1) . '"';
        $rtn .= ',"obR":"' . number_format($this->ob[1],1) . '"';
        $rtn .= ',"tbL":"' . number_format($this->tb[0],1) . '"';
        $rtn .= ',"tbR":"' . number_format($this->tb[1],1) . '"';
        $rtn .= ',"hrL":"' . number_format($this->hr[0],1) . '"';
        $rtn .= ',"hrR":"' . number_format($this->hr[1],1) . '"';
        $rtn .= ',"bpsL":"' . number_format($this->bps[0],1) . '"';
        $rtn .= ',"bpsR":"' . number_format($this->bps[1],1) . '"';
        $rtn .= ',"bphrL":"' . number_format($this->bphr[0],1) . '"';
        $rtn .= ',"bphrR":"' . number_format($this->bphr[1],1) . '"';
        $rtn .= ',"clL":"' . number_format($this->cl[0],1) . '"';
        $rtn .= ',"clR":"' . number_format($this->cl[1],1) . '"';
        $rtn .= ',"powerL":"' . $this->power[0] . '"';
        $rtn .= ',"powerR":"' . $this->power[1] . '"';
        $rtn .= ',"dpL":"' . number_format($this->dp[0],1) . '"';
        $rtn .= ',"dpR":"' . number_format($this->dp[1],1) . '"';
        $rtn .= ',"running":"' . $this->running  . '"';
        $rtn .= ',"hitAndRun":"' . $this->hitAndRun  . '"';
        $rtn .= ',"bunting":"' . $this->bunting  . '"';
        $rtn .= ',"lead":"' . $this->lead  . '"';
        $rtn .= ',"caught":"' . $this->caught  . '"';
        $rtn .= ',"first":"' . $this->first  . '"';
        $rtn .= ',"second":"' . $this->second  . '"';
        $rtn .= ',"ip":"' . number_format($this->ip,1) . '"';
        $rtn .= ',"hold":"' . $this->hold . '"';
        $rtn .= ',"endurance":"' . $this->endure . '"';
        $rtn .= ',"balk":"' . $this->balk . '"';
        $rtn .= ',"wp":"' . $this->wp . '"';
        $rtn .= ',"batting":"' . $this->batting . '"';
        $rtn .= ',"age":"' . $this->age  . '"';
        $rtn .= '}';
      }
      $rtn .= '}}';
      return $rtn;
    }
    public function json() {
      return json_decode($this->toString());
    }
  }
?>
