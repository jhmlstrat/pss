<?php
  namespace Scoring;

  class ResultsFile {
    private $config;
    public $games = [];    

    function __construct($year = 2017) {
      $json = file_get_contents("../data/config.json");
      $confs = json_decode($json, true);
      foreach ($confs['years'] as $conf) {
        if ($conf['year'] == $year) $this->config = $conf;
      }
      $mapping=[];
      foreach ($this->config['teams'] as $key => $team) {
        if (! file_exists('../data/' . $year . '/' . $team['team'] . 'game')) return;
        $this->games[$team['team']] = [];
        $mapping[$team['team_name']] = $team['team'];
      }
      #if (! file_exists('../../'. $year . '/misc/results.html')) return;
      #$fr = fopen('../../'. $year . '/misc/results.html','r');
      if (! file_exists('../data/'. $year . '/results.html')) return;
      $fr = fopen('../data/'. $year . '/results.html','r');
      $lines = [];
      while (($line = fgets($fr)) !== false)  {
        array_unshift($lines,$line);
      }
      for ($linei=0; $linei < count($lines); $linei++) {
        $line=$lines[$linei];
        if (strpos(strtoupper($line),"<LI>") !== false ) {
          $l = preg_replace('/<[^>]*>/','',rtrim($line));
          $d = preg_replace('/ -.*/','',$l);
          $s = preg_replace('/.*-- /','',$l);
          $r = preg_replace('/.* - /','',preg_replace("/ -- .*/",'',$l));
          $n1 = '';
          $n2 = '';
          $reversed = false;
          if (strpos($r,' win ')) {
            $n1 = preg_replace('/ win .*/','',$r);
            if (strpos($r,' at ')) {
              $n2 = preg_replace('/.* at /','',$r);
            } else {
              $n2 = preg_replace('/.* vs /','',$r);
              $reversed = true;
            }
          } else if (strpos($r,' sweep ')) {
            $n1 = preg_replace('/ sweep .*/','',$r);
            if (strpos($r,' at ')) {
              $n2 = preg_replace('/.* at /','',$r);
            } else {
              $n2 = preg_replace('/.* vs /','',$r);
              $reversed = true;
            }
          } else {
            $n1 = preg_replace('/ and .*/','',$r);
            $n2 = preg_replace('/.* and (.*) split .*/','\1',$r);
          }
          if ($reversed) {
            $tmp = $n1;
            $n1 = $n2;
            $n2 = $tmp;
          }
          $t1 = $mapping[$n1];
          $t2 = $mapping[$n2];

          $gs = explode(',',$s);
//print $gn . '<br />';
          foreach ($gs as $g) {
            $gm = new Game($year);
//    private $year;
//    public $team_;
//    public $gameNumber_;
//    public $runs_;
//    public $hits_;
//    public $errors_;
//    public $innings_;
//    public $date_;
		
//    function __construct($year = 2017) {
//      $this->year = $year;
//      $this->team_ = [];
//      $this->team_[0] = '';
//      $this->team_[1] = '';
//      $this->gameNumber_ = [];
//      $this->gameNumber_[0] = 0;
//      $this->gameNumber_[1] = 0;
//      $this->runs_ = [];
//      $this->runs_[0] = 0;
//      $this->runs_[1] = 0;
//      $this->hits_ = [];
//      $this->hits_[0] = 0;
//      $this->hits_[1] = 0;
//      $this->errors_ = [];
//      $this->errors_[0] = 0;
//      $this->errors_[1] = 0;
//      $this->innings_ = 9;
//      $this->date_ = '';
//    }
//print (103-count($this->game[$t1])-$gn) . ' - ' . (103-count($this->game[$t2])-$gn) . "<br />";
            $gm->team_[0] = $t1;
            $gm->team_[1] = $t2;
//TBD
            $gm->gameNumber_[0]=count($this->games[$t1])+1;
            $gm->gameNumber_[1]=count($this->games[$t2])+1;
            $gm->date_ = $d;
            $gm->innings_ = 9;
            if (strpos($g,'(') != false) {
              $is = explode('(',$g);
              $g = $is[0];
              $gm->innings_ = intval(explode(')',$is[1])[0]);
            }
            $rs = explode('-',$g);
            if ($reverse) {
              $gm->runs_[1]=$rs[0];
              $gm->runs_[0]=$rs[1];
            } else {
              $gm->runs_[0]=$rs[0];
              $gm->runs_[1]=$rs[1];
            }
            array_push($this->games[$t1],$gm);
            array_push($this->games[$t2],$gm);
          } 
        }// else print 'Hmm - ' . $line . "<br/>";
      } 
      fclose($fr);
      foreach ($this->config['teams'] as $key => $team) {
//print $team['team'] . ' ' . count($this->games[$team['team']]) . '<br/>';
      }

      //print 'Got Here<br/>'; 
    }
  }
?>
