<?php
  namespace Scoring;

  require_once "../pss/ScheduleItem.php";

  class Schedule {
    private $config;

    private $year;

    function __construct($year = 2017) {
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
print_r($offset);print "<br/>";
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
      print_r($div);
      print "<br/>";
    }

  }

?>
