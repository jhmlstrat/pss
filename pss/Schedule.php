<?php
  namespace Scoring;

  require_once "../pss/ScheduleItem.php";
  require_once "../pss/ResultsFile.php";

  class Schedule {
    private $config;

    private $schedules = [];

    private function addSI($t, $oppF, $numH, $numA) {
      $h = 'home';
      $a = 'away';
      $s = \Scoring\Seasons::Spring;
      $opp = '';
      foreach ($this->config['teams'] as $key => $team) {
        if ($team['franchise'] == $oppF) $opp = $team['team'];
      }
      if (($numH == 3 && $numA == 0) || ($numA == 3 && $numH == 0)) $s = \Scoring\Seasons::Fall;
      if ($numH == 7) {
        array_push($this->schedules[$t]['home'],\Scoring\ScheduleItem::newSI($t,$opp,4,$s));
        $numH = 3;
      }
      if ($numA == 7) {
        array_push($this->schedules[$t][$a],\Scoring\ScheduleItem::newSI($opp,$t,4,$s));
        $numA = 3;
      }
      if ($numH > 0) {
        array_push($this->schedules[$t][$h],\Scoring\ScheduleItem::newSI($t,$opp,$numH,$s));
      }
      if ($numA > 0) {
        array_push($this->schedules[$t][$a],\Scoring\ScheduleItem::newSI($opp,$t,$numA,$s));
      }
    }

    function __construct($year = 2017) {
      $rf = new ResultsFile($year);
//var_dump($rf);
      $previous = nil;
      $json = file_get_contents("../data/config.json");
      $confs = json_decode($json, true);
      foreach ($confs['years'] as $conf) {
        if ($conf['year'] == $year-1) $previous = $conf;
        if ($conf['year'] == $year) $this->config = $conf;
      }
      $w = [];
      foreach ($this->config['teams'] as $key => $team) {
        $w[$team['franchise']] = 0;
      }
      $yearLess = $year - 1;
      $results = file_get_contents("../../" . $yearLess . "/misc/rec_team.html");
      $entries = explode("\n",$results);
      $first = -1;
      for ($i = 0; $i < count($entries); $i++) {
        if (substr($entries[$i],0,8) == '--------') {
          $first = $i;
          $i = count($entries);
        }
      }
      $first --;
      if ($first < 0) { print "Error reading results<br/>"; return;}
      //print_r("<pre>" . $entries[$first] . "</pre><br/>");
      $columns = preg_replace('/<[^>]*> */','',$entries[$first]);
      $columns = preg_replace('/ [ \t]*/',' ',$columns);
      $pieces = explode(" ",$columns);
      if (count($pieces) != count($w)) {print "Mismatched number of teams<br/>"; return;}
      $foundCount = 0;
      $lines = [];
      $offset = [];
      for ($i = $first+1; $i < count($entries) && $foundCount <  count($this->config['teams']); $i++) {
        $line = preg_replace('/<[^>]*> */','',$entries[$i]);
        $line = preg_replace('/^ [ \t]*/',' ',$line);
        $line = preg_replace('/\./','',$line);
        if (strlen($line) <2) continue;
        if (substr($line,0,8) == '--------') continue;
        if ($entries[$i] == $entries[$first]) continue;
        $found = false;
        for ($j=0; $j < count($this->config['teams']); $j++) {
          $city = $previous['teams'][$j]['city'] . ' ' . $previous['teams'][$j]['team_name'];
          if (substr($line,0,strlen($city)) == $city) {
            $found = true;
            $foundCount ++;
            $line = preg_replace('/'.$city.' */','',$line);
            $line = preg_replace('/(\d)[ \t]*-[ \t]*(\d)/','$1+$2',$line);
            $line = preg_replace('/[ \t][ \t]*/',' ',$line);
            $line = preg_replace('/[ \t]*$/','',$line);
            $lines[$previous['teams'][$j]['franchise']] = $line;
            $against = explode(' ',$line);
            for ($k=0; $k < count($against)-1; $k++) {
              if (substr($against[$k],0,2) == '--') $offset[$previous['teams'][$j]['franchise']] = $k;
              else {
                $ws = explode('+',$against[$k]);
                $w[$previous['teams'][$j]['franchise']] += intval($ws[0]);
              }
            }
          }
        }
        if (! $found) {print "-" . $line . "- " . $foundCount . "<br/>"; return;}
      }
      $div = [];
      foreach ($this->config['teams'] as $team) {
        if (! array_key_exists($team['division'],$div)) $div[$team['division']] = [$team]; 
        else {
          $added = false;
          for ($i=0; $i < count($div[$team['division']]); $i++) {
            if ($w[$team['franchise']] > $w[$div[$team['division']][$i]['franchise']]) {
              array_splice($div[$team['division']],$i,0,[$team]);
              $added = true;
              $i = count($div[$team['division']]);
            }
            if ($w[$team['franchise']] == $w[$div[$team['division']][$i]['franchise']]) {
              $wls = explode(' ',$lines[$team['franchise']]);
              $wl = explode('+',$wls[$offset[$div[$team['division']][$i]['franchise']]]);
              if ($wl[0] > $wl[1]) {
                array_splice($div[$team['division']],$i,0,[$team]);
                $added = true;
                $i = count($div[$team['division']]);
              }
            }
          }
          if (! $added) array_push($div[$team['division']],$team);
        }
      }
      //print_r($div);
      //print "<br/>";
      $mult = 1;
      if ($year % 2 == 0) $mult = -1;
      $base = [];
      $divs = ['AMML','CBML','RCML'];
      $base[$divs[0]] = [4,14,2,9,6];
      $base[$divs[1]] = [10,11,12,7,5];
      $base[$divs[2]] = [13,8,1,3,15];
      if ($year >= 2019) {
        $divs = ['CBML','RCML','AMML'];
        $base[$divs[0]] = [12,6,15,7,5];
        $base[$divs[1]] = [10,8,9,11,2];
        $base[$divs[2]] = [4,1,13,14,3];
      }
      $f = \Scoring\Seasons::Fall;
      foreach ($this->config['teams'] as $key => $team) {
        $div_off = -1;
        $team_off = -1;
        $base_off = -1;
        $t = $team['team'];
        $tdiv = $team['division'];
        for ($i=0; $i <count($divs); $i++) {
          if ($divs[$i] == $tdiv) $div_off = $i;
        }
        $c = count($div[$tdiv]);
        for ($i=0; $i < $c; $i++) {
          if ($div[$tdiv][$i]['franchise'] == $team['franchise']) $team_off = $i;
          if ($base[$tdiv][$i] == $team['franchise']) $base_off = $i;
        }
        
        $this->schedules[$t] = [];
        $this->schedules[$t]['home'] = [];
        $this->schedules[$t]['away'] = [];
        //$this->addSI($t,$base[$tdiv][($base_off + $mult + $c)%$c]['team'],7,3);
        $this->addSI($t,$base[$tdiv][($base_off - $mult + $c)%$c],7,3);
        $this->addSI($t,$base[$tdiv][($base_off - $mult + $c)%$c],0,3);
        $this->addSI($t,$base[$tdiv][($base_off - $mult - $mult + $c)%$c],7,3);
        $this->addSI($t,$base[$tdiv][($base_off - $mult - $mult + $c)%$c],0,3);
        $this->addSI($t,$base[$tdiv][($base_off + $mult + $c)%$c],3,7);
        $this->addSI($t,$base[$tdiv][($base_off + $mult + $c)%$c],3,0);
        $this->addSI($t,$base[$tdiv][($base_off + $mult + $mult + $c)%$c],3,7);
        $this->addSI($t,$base[$tdiv][($base_off + $mult + $mult + $c)%$c],3,0);
        for ($i=0; $i <count($divs); $i++) {
          if ($i == $div_off) continue;
          for ($j=0; $j < $c; $j++) {
            if ($j != $team_off) {
              if ($i == ($div_off - $mult + count($divs))%count($divs)) {
                $this->addSI($t,$div[$divs[$i]][$j]['franchise'],3,2);
              } else {
                $this->addSI($t,$div[$divs[$i]][$j]['franchise'],2,3);
              }
            }
          }
        }
        $c = count($divs);
        $this->addSI($t,$div[$divs[($div_off - $mult + $c)%$c]][$team_off]['franchise'],0,2);
        $this->addSI($t,$div[$divs[($div_off - $mult + $c)%$c]][$team_off]['franchise'],3,0);
        $this->addSI($t,$div[$divs[($div_off + $mult + $c)%$c]][$team_off]['franchise'],2,0);
        $this->addSI($t,$div[$divs[($div_off + $mult + $c)%$c]][$team_off]['franchise'],0,3);
        $series = [];
        $count = 0;
        if ($rf->games[$t])
        foreach ($rf->games[$t] as $gm) {
//var_dump($this->schedules[$t]);          
//var_dump($gm);
          $count ++;
          if ($count == count($rf->games[$t])) array_push($series,$gm);
          if ($count == count($rf->games[$t]) || count($series) == 4 || (count($series) > 0 && ($series[0]->team_[0] != $gm->team_[0] || $series[0]->team_[1] != $gm->team_[1]))) {
            if ($series[0]->team_[0] == $t) {
              $side = "away";
              $oside = "home_";
              $off = 1;
            } else {
              $side="home";
              $oside = "away_";
              $off = 0;
            }
            $added=false;
            foreach ($this->schedules[$t][$side] as $sg) {
              if ($series[0]->team_[$off] == $sg->$oside && count($series) == $sg->games_ && count($sg->results_) == 0 && ! $added) {
                foreach ($series as $gmm) 
                  array_push($sg->results_,$gmm);
                $added=true;
              }
            }         
            if (! $added) {
              //var_dump($series);
              print "Shouldn't get here " . $series[0]->team_[0] . " at " .  $series[0]->team_[1] . "(" . $t . ") for " . count($series) . "\n";
            }
            $series = [];
          }
          array_push($series,$gm);
        }
      }  
      //print($this->getSchedule('pit')); print "<br/>";
    }

    public function getSchedule($team) {
      $rtn = '{"home":[';
      $fall = [];
      for ($i=0; $i <count($this->schedules[$team]['home']); $i++) {
        if ($this->schedules[$team]['home'][$i]->season_ == \Scoring\Seasons::Fall) {
          array_push($fall,$this->schedules[$team]['home'][$i]);
        } else {
          if ($i > 0) $rtn .= ',';
          $rtn .= $this->schedules[$team]['home'][$i]->toString(); 
        }
      }
      for ($i=0; $i <count($fall); $i++) {
         $rtn .= ',' . $fall[$i]->toString();
      }
      $rtn .= '],"away":[';
      $fall = [];
      for ($i=0; $i <count($this->schedules[$team]['away']); $i++) {
        if ($this->schedules[$team]['away'][$i]->season_ == \Scoring\Seasons::Fall) {
          array_push($fall,$this->schedules[$team]['away'][$i]);
        } else {
          if ($i > 0) $rtn .= ',';
          $rtn .= $this->schedules[$team]['away'][$i]->toString(); 
        }
      }
      for ($i=0; $i <count($fall); $i++) {
         $rtn .= ',' . $fall[$i]->toString();
      }
      $rtn .= ']}';
      return $rtn;
    }

    public static function generateSchedules($year = 2017) {
      $inst = new self($year);
      //$json = file_get_contents("../data/config.json");
      //$confs = json_decode($json, true);
      //foreach ($confs['years'] as $conf) {
      //  if ($conf['year'] == $year) $config = $conf;
      //}
      foreach ($inst->config['teams'] as $key => $team) {
//print_r($team); print "<br/>";
        $sf = fopen('../data/' . $year . '/' . strtolower($team['team']) . 'sched.html','w');
        fwrite($sf,"<HTML>\n<HEAD>\n<TITLE>" . $year . " " . $team['city'] . " " . $team['team_name'] . " Schedule</TITLE>\n</HEAD>\n");
        fwrite($sf,"<BODY background=3D\"../../gif/paper1.jpg\">\n");
        fwrite($sf,"<CENTER><H3>" . $year . " " . $team['city'] . " " . $team['team_name'] . " Schedule</H3></CENTER>\n");
        fwrite($sf,"<PRE><TT>\n");
        fwrite($sf,"    HOME (51)                           AWAY (51)\n\n");
        $fh=[];
        $fa=[];
        for ($h=0,$a=0;$h<count($inst->schedules[$team['team']]['home']) || $a<count($inst->schedules[$team['team']]['away']); $h++,$a++) {
          $home=nil;
          $away=nil;
          for ($i=0;$i<count($inst->config['teams']);$i++) {
            if ($inst->config['teams'][$i]['team'] == $inst->schedules[$team['team']]['home'][$h]->away_)  $home = $inst->config['teams'][$i];
            if ($inst->config['teams'][$i]['team'] == $inst->schedules[$team['team']]['away'][$a]->home_)  $away = $inst->config['teams'][$i];
          }
          if ($inst->schedules[$team['team']]['home'][$h]->season_ ==  \Scoring\Seasons::Fall) { $a--; array_push($fh,$home); continue;}
          if ($inst->schedules[$team['team']]['away'][$a]->season_ ==  \Scoring\Seasons::Fall) { $h--; array_push($fa,$away); continue;}
          $hg = $inst->schedules[$team['team']]['home'][$h]->games_;
          $ag = $inst->schedules[$team['team']]['away'][$a]->games_;
          if ($hg == 4) {$h++; $hg=7;};
          if ($ag == 4) {$a++; $ag=7;};
          fwrite($sf,sprintf("%-13s (%d)                    %-13s (%d) \n",$home['city'],$hg,$away['city'],$ag));
        }
       
        fwrite($sf,"\n");
        fwrite($sf,"                      SEPTEMBER\n\n");
        fwrite($sf,sprintf("%-13s (3)                    %-13s (3) \n",$fh[0]['city'],$fa[0]['city']));
        fwrite($sf,sprintf("%-13s (3)                    %-13s (3) \n",$fh[1]['city'],$fa[1]['city']));
        fwrite($sf,sprintf("%-13s (3)                    %-13s (3) \n",$fh[2]['city'],$fa[2]['city']));
 
        fwrite($sf,"\n\n");
        fwrite($sf,"</TT></PRE>\n");
        fwrite($sf,"<HR>\n");
        fwrite($sf,"</BODY>\n");
        fwrite($sf,"</HTML>\n");
        fclose($sf);
      }
    }
  }

?>
