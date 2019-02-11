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
        if (! file_exists('../../cgi-bin/jhml/data/' . $year . '/' . $team['team'] . 'game')) return;
        $this->games[$team['team']] = [];
        $mapping[$team['team_name']] = $team['team'];
      }
      if (! file_exists('../../'. $year . '/misc/results.html')) return;
      $fr = fopen('../../'. $year . '/misc/results.html','r');
      while (($line = fgets($fr)) !== false)  {
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
          $t1 = $mapping[$n1];
          $t2 = $mapping[$n2];

          $gs = explode(',',$s);
          $gn = count($gs);
//print $gn . '<br />';
          foreach ($gs as $g) {
            $gm = new Game($year);
//  namespace Scoring;
//
//  class Game {
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
            $gm->date_ = $d;
            $gm->innings = 9;
            if (strpos($g,'(') != false) {
              $is = explode('(',$g);
              $g = $is[0];
              $gm->innings = explode(')',$is[1])[0];
            }     
            $this->game[$t1][103-count($this->game[$t1])-$gn] = $gm;
            $this->game[$t2][103-count($this->game[$t2])-$gn] = $gm;
            $gn = $gn - 2;
          } 
        }// else print 'Hmm - ' . $line . "<br/>";
      } 
      fclose($fr);
      foreach ($this->config['teams'] as $key => $team) {
//print $team['team'] . ' ' . count($this->game[$team['team']]) . '<br/>';
      }

      //print 'Got Here<br/>'; 
    }
  }
?>

