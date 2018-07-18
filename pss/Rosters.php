<?php
  namespace Jhml;
  require_once "Roster.php";
//TODO: Trades

  class Rosters {
    public $year;
    public $rosters = [];

//    private $yearString = "2017";
    private $baseURL = "http://www.jhmlstrat.org/";
//    private $initialized = false;
    private $AgesFile = "/AGES.txt";
    private $RosterFile = "/LEAGUE.JHML";
    private $StratHittersFile = "/Hitters.txt";
    private $StratPitchersFile = "/Pitchers.txt";
    function __construct($year=2016) {
      $this->year = $year;
      $this->AgesFile = '../data/' . $year . $this->AgesFile;
      $this->RosterFile = '../data/' . $year . $this->RosterFile;
      $this->StratHittersFile = '../data/' . $year . $this->StratHittersFile;
      $this->StratPitchersFile = '../data/' . $year . $this->StratPitchersFile;
      $this->loadRosterFile();
    }

    
//
//  int noMatch = 0;
//
    private function initialize() {
      //$basePage = get(baseURL + "ros" + $this->year + ".html");
//    java.io.StringReader sr = new java.io.StringReader(basePage);
//    java.io.BufferedReader br = new java.io.BufferedReader(sr);
//    String inputLine;
//    try
//    {
//      while ((inputLine = br.readLine()) != null)
//      {
////        System.out.println(inputLine);
//        if (inputLine.contains("rost.html") && ( ! inputLine.contains("t16")))
//        {
//          boolean pitchers = true;
//          boolean batters = true;
//          inputLine = inputLine.substring(0,inputLine.indexOf("\">"));
////          System.out.println(inputLine);
//          String fileURL = inputLine.substring(inputLine.lastIndexOf("\"")+1);
////          System.out.println(fileURL);
//          String team = fileURL.substring(fileURL.indexOf("/")+1, fileURL.indexOf("rost.html")).toUpperCase();
////          System.out.println(team);
//          if (team.equals("FREB"))
//          {
//            break;
//          }
//          if (team.equals("FREP"))
//          {
//            break;
//          }          
//          addTeam(team);
////          System.out.println(baseURL + "/" + fileURL + "\t" + team);
//          String teamPage = W.get( baseURL + "/" + fileURL);
////          System.out.println(teamPage);
//          java.io.StringReader isr = new java.io.StringReader(teamPage);
//          java.io.BufferedReader ibr = new java.io.BufferedReader(isr);
//          String innerLine;
//          while ((innerLine = ibr.readLine()) != null)
//          {
////            System.out.println(innerLine);
//            if (batters)
//            {
//              if (innerLine.indexOf("</OL>") >= 0)
//              {
//                batters = false;
////                System.out.println("End of Batters");
//              }
//              if (innerLine.indexOf("<LI>") >= 0 && innerLine.indexOf("<!--") == -1)
//              {
//                String Batter = innerLine.substring(4,innerLine.indexOf("  "));
////                Batter = Batter.replace(" Texas Rangers" , "");
//                RosterItem ri = new RosterItem(Batter, team);
//                addHitter(ri);
////                System.out.println("Adding "+ Batter);
//              }
//            }
//            else if (pitchers)
//            {
//              if (innerLine.indexOf("</OL>") >= 0)
//              {
//                pitchers = false;
////                System.out.println("End of Pitchers");
//              }              
//              if (innerLine.indexOf("<LI>") >= 0 && innerLine.indexOf("<!--") == -1)
//              {
//                String Pitcher = innerLine.substring(4,innerLine.indexOf("  "));
//                RosterItem ri = new RosterItem(Pitcher, team);
//                addPitcher(ri);
////                System.out.println("Adding "+ Pitcher);
//              }
//            }
//          }
//        }
//      }
////      System.out.println(batterList);
////      System.out.println();
////      System.out.println(pitcherList);
//    }
//    catch (Exception ex)
//    {
//      ex.printStackTrace();
//
//    }
//    initialized = true;
    }
//

//

//WRONG 
    public function writeRosterFile() {
      $rf = fopen(Roster::RosterFile,'w');
      usort($this->batters,array("\Scoring\RosterItem","cmp"));
      usort($this->pitchers,array("\Scoring\RosterItem","cmp"));
      foreach ($this->batters as $value) {
        fwrite($rf,$value->toString() . "\n");
      }
      fwrite($rf,"\n");
      foreach ($this->pitchers as $value) {
        fwrite($rf,$value->toString() . "\n");
      }
      fclose($rf);
    }

    private function loadRosterFile() {
      if (file_exists($this->RosterFile)) {
        $rf = fopen($this->RosterFile,'r');
        if ($rf) {
          while (($line = rtrim(fgets($rf))) !== false and strlen($line) > 2)  {
            $batter = \Scoring\RosterItem::fromRosterFileString($line);
            if (! array_key_exists($batter->team, $this->rosters)) {
              $this->rosters[$batter->team] = new \Jhml\Roster();
              $this->rosters[$batter->team]->team = $batter->team;
            }
            $this->rosters[$batter->team]->addBatter($batter);
          }
          while (($line = fgets($rf)) !== false) {
            $pitcher = \Scoring\RosterItem::fromRosterFileString($line);
            $this->rosters[$pitcher->team]->addPitcher($pitcher);
          }
          fclose($rf);
          ksort($this->rosters);
          $this->loadStratInfo();
          $this->processMoves();
        }       
      } else {
        $this->initialize();
      }
    }
    private function loadStratInfo() {
      if (! file_exists($this->StratHittersFile)) {
        print "Missing Hitters file\n";
        exit;
      }
      $hf = fopen($this->StratHittersFile,'r');
      $count = 0;
      while (($line = fgets($hf)) !== false)  {
        if ($count > 0) {
          $pieces = explode("\t",$line);
          //foreach ($pieces as $value) { print $value . "\n"; }
          $rindex = '';
          $bindex = -1;
          $name = str_replace("\"","",$pieces[1]);
          $name = str_replace("+","",$name);
          $name = str_replace("*","",$name);
          $name = str_replace(",",", ",$name);
          $name = str_replace(".","",$name);
          foreach($this->rosters as $team => $roster) {
            for ($j = 0; $j < count($roster->batters); $j++) {
              $test = str_replace(".","",$roster->batters[$j]->player->name);
              if (strncmp($test,$name,strlen($name)) == 0) {
                $rindex = $team;
                $bindex = $j;
                $j=count($roster->batters);
              }
            }
          }
          if ($rindex == -1 || $bindex == -1) {
            print "Batter not found " . $name . "\n";
            exit;
          }
          $this->rosters[$rindex]->batters[$bindex]->player->realTeam = $pieces[0];
          $this->rosters[$rindex]->batters[$bindex]->player->realTeam = $pieces[0];
          if (strpos($pieces[1],"+") !== false) $this->rosters[$rindex]->batters[$bindex]->player->hand="S";
          if (strpos($pieces[1],"*") !== false) $this->rosters[$rindex]->batters[$bindex]->player->hand="L";
          $this->rosters[$rindex]->batters[$bindex]->player->ab = $pieces[2];
          $this->rosters[$rindex]->batters[$bindex]->player->hit[0] = $pieces[5];
          $this->rosters[$rindex]->batters[$bindex]->player->ob[0] = $pieces[6];
          $this->rosters[$rindex]->batters[$bindex]->player->tb[0] = $pieces[7];
          $this->rosters[$rindex]->batters[$bindex]->player->bps[0] = '0.0';
          if (strpos($pieces[9],"*") === false) $this->rosters[$rindex]->batters[$bindex]->player->bps[0] = '5.0';
          $this->rosters[$rindex]->batters[$bindex]->player->power[0] = 'W';
          $this->rosters[$rindex]->batters[$bindex]->player->bphr[0] = '0.0';
          if (strpos($pieces[9],"W") === false and strpos($pieces[9],"w") === false) {
            $this->rosters[$rindex]->batters[$bindex]->player->power[0] = 'N';
            $bphr=str_replace("w","",$pieces[9]);
            $bphr=str_replace("W","",$bphr);
            $bphr=str_replace("*","",$bphr);
            $this->rosters[$rindex]->batters[$bindex]->player->bphr[0] = $bphr;
          }
          $this->rosters[$rindex]->batters[$bindex]->player->cl[0] = $pieces[10];
          $this->rosters[$rindex]->batters[$bindex]->player->dp[0] = $pieces[11];
          $this->rosters[$rindex]->batters[$bindex]->player->hit[1] = $pieces[14];
          $this->rosters[$rindex]->batters[$bindex]->player->ob[1] = $pieces[15];
          $this->rosters[$rindex]->batters[$bindex]->player->tb[1] = $pieces[16];
          $this->rosters[$rindex]->batters[$bindex]->player->bps[1] = '0.0';
          if (strpos($pieces[18],"*") === false) $this->rosters[$rindex]->batters[$bindex]->player->bps[1] = '5.0';
          $this->rosters[$rindex]->batters[$bindex]->player->power[1] = 'W';
          $this->rosters[$rindex]->batters[$bindex]->player->bphr[1] = '0.0';
          if (strpos($pieces[18],"W") === false and strpos($pieces[18],"w") === false) {
            $this->rosters[$rindex]->batters[$bindex]->player->power[1] = 'N';
            $bphr=str_replace("w","",$pieces[18]);
            $bphr=str_replace("W","",$bphr);
            $bphr=str_replace("*","",$bphr);
            $this->rosters[$rindex]->batters[$bindex]->player->bphr[1] = $bphr;
          }
          $this->rosters[$rindex]->batters[$bindex]->player->cl[1] = $pieces[19];
          $this->rosters[$rindex]->batters[$bindex]->player->dp[1] = $pieces[20];
          $steal = str_replace("\"","",$pieces[21]);
          $steal = str_replace('(','',$steal);
          $steal = str_replace(')','',$steal);
          $pos=strpos($steal," ");
          $first=substr($steal,0,$pos);
          $p1=strpos($first,'/');
          $second=substr($steal,$pos+1);
          $p2=strpos($second,'-');
          $this->rosters[$rindex]->batters[$bindex]->player->lead = substr($first,0,$p1);
          $this->rosters[$rindex]->batters[$bindex]->player->pickoff = substr($first,$p1+1);
          $this->rosters[$rindex]->batters[$bindex]->player->steal[0] = substr($second,0,$p2);
          $this->rosters[$rindex]->batters[$bindex]->player->steal[1] = substr($second,$p2+1);
          $this->rosters[$rindex]->batters[$bindex]->player->run = $pieces[23];
          $this->rosters[$rindex]->batters[$bindex]->player->bunt = $pieces[24];
          $this->rosters[$rindex]->batters[$bindex]->player->hitAndRun = $pieces[25];
          for ($i = 26; $i < 34; $i++) {
            if ($pieces[$i] !==  '') {
              $pos = new \ProjectScoresheet\Position;
              $pos->p(\ProjectScoresheet\Position::position(\ProjectScoresheet\Position::positionString($i-24)));
              $pos->rating = substr($pieces[$i],0,1);
              $pos->e = substr($pieces[$i],1);
              array_push( $this->rosters[$rindex]->batters[$bindex]->player->positionsPlayed,$pos);
            }
          }
        }
        $count ++;
      }
      fclose($hf);
      $missing = false;
      foreach($this->rosters as $team => $roster) {
        for ($j = 0; $j < count($roster->batters); $j++) {
          if (strcmp($roster->batters[$j]->player->realTeam,'') == 0) {
            print "No team for " . $roster->batters[$j]->player->name . "\n";
            $missing = true;
          }
        }
      }
      if ($missing) exit;
      if (! file_exists($this->StratPitchersFile)) {
        print "Missing Pitchers file\n";
        exit;
      }
      $pf = fopen($this->StratPitchersFile,'r');
      $count = 0;
      while (($line = fgets($pf)) !== false)  {
        if ($count > 0) {
          $pieces = explode("\t",$line);
          //foreach ($pieces as $value) { print $value . "\n"; }
          $rindex = "";
          $pindex = -1;
          $name = str_replace("\"","",$pieces[1]);
          $name = str_replace("+","",$name);
          $name = str_replace("*","",$name);
          $name = str_replace(",",", ",$name);
          $name = str_replace(".","",$name);
          foreach($this->rosters as $team => $roster) {
            for ($j = 0; $j < count($roster->pitchers); $j++) {
              $test = str_replace(".","",$roster->pitchers[$j]->player->name);
              if (strncmp($test,$name,strlen($name)) == 0) {
                $rindex = $team;
                $pindex = $j;
                $j=count($roster->pitchers);
              }
            }
          }
          if ($pindex == -1) {
            print "Pitcher not found " . $name . "\n";
            exit;
          }
          $this->rosters[$rindex]->pitchers[$pindex]->player->realTeam = $pieces[0];
          if (strpos($pieces[1],"+") !== false) $this->rosters[$rindex]->pitchers[$pindex]->player->hand="S";
          if (strpos($pieces[1],"*") !== false) $this->rosters[$rindex]->pitchers[$pindex]->player->hand="L";
          $this->rosters[$rindex]->pitchers[$pindex]->player->ip = $pieces[2];
          $this->rosters[$rindex]->pitchers[$pindex]->player->hit[0] = $pieces[5];
          $this->rosters[$rindex]->pitchers[$pindex]->player->ob[0] = $pieces[6];
          $this->rosters[$rindex]->pitchers[$pindex]->player->tb[0] = $pieces[7];
          $this->rosters[$rindex]->pitchers[$pindex]->player->bps[0] = '0.0';
          if (strpos($pieces[9],"*") === false) $this->rosters[$rindex]->pitchers[$pindex]->player->bps[0] = '5.0';
          $this->rosters[$rindex]->pitchers[$pindex]->player->power[0] = 'W';
          $this->rosters[$rindex]->pitchers[$pindex]->player->bphr[0] = '0.0';
          if (strpos($pieces[9],"W") === false and strpos($pieces[9],"w") === false) {
            $this->rosters[$rindex]->pitchers[$pindex]->player->power[0] = 'N';
            $bphr=str_replace("w","",$pieces[9]);
            $bphr=str_replace("W","",$bphr);
            $bphr=str_replace("*","",$bphr);
            $this->rosters[$rindex]->pitchers[$pindex]->player->bphr[0] = $bphr;
          }
          $this->rosters[$rindex]->pitchers[$pindex]->player->hit[1] = $pieces[13];
          $this->rosters[$rindex]->pitchers[$pindex]->player->ob[1] = $pieces[14];
          $this->rosters[$rindex]->pitchers[$pindex]->player->tb[1] = $pieces[15];
          $this->rosters[$rindex]->pitchers[$pindex]->player->bps[1] = '0.0';
          if (strpos($pieces[17],"*") === false) $this->rosters[$rindex]->pitchers[$pindex]->player->bps[1] = '5.0';
          $this->rosters[$rindex]->pitchers[$pindex]->player->power[1] = 'W';
          $this->rosters[$rindex]->pitchers[$pindex]->player->bphr[1] = '0.0';
          if (strpos($pieces[17],"W") === false and strpos($pieces[17],"w") === false) {
            $this->rosters[$rindex]->pitchers[$pindex]->player->power[1] = 'N';
            $bphr=str_replace("w","",$pieces[17]);
            $bphr=str_replace("W","",$bphr);
            $bphr=str_replace("*","",$bphr);
            $this->rosters[$rindex]->pitchers[$pindex]->player->bphr[1] = $bphr;
          }
          $this->rosters[$rindex]->pitchers[$pindex]->player->hold = $pieces[19];
          $this->rosters[$rindex]->pitchers[$pindex]->player->endure = $pieces[20];
          $pos = new \ProjectScoresheet\Position;
          $pos->p(\ProjectScoresheet\Position::position('P'));
          $pos->rating = substr($pieces[21],0,1);
          $pos->e = substr($pieces[21],2);
          array_push( $this->rosters[$rindex]->pitchers[$pindex]->player->positionsPlayed,$pos);
          $this->rosters[$rindex]->pitchers[$pindex]->player->balk = $pieces[22];
          $this->rosters[$rindex]->pitchers[$pindex]->player->wp = $pieces[23];
          $this->rosters[$rindex]->pitchers[$pindex]->player->batting = $pieces[24];
          $this->rosters[$rindex]->pitchers[$pindex]->player->run = $pieces[26];
        }
        $count ++;
      }
      fclose($pf);
      foreach($this->rosters as $team => $roster) {
        for ($j = 0; $j < count($roster->pitchers); $j++) {
          if (strcmp($roster->pitchers[$j]->player->realTeam,'') == 0) {
            print "No team for " . $roster->pitchers[$j]->player->name . "\n";
            $missing = true;
          }
        }
      }
      if ($missing) exit;
    }
    //TBD
    private function normalize($str) {
      return str_replace("-","",str_replace(".","",str_replace("'","",$str)));
    }
    private function copyStrat($src,$dest) {
      $dest->name = $src->name;
      $dest->realTeam = $src->realTeam;
      $dest->hand = $src->hand;
//      $dest->positionsPlayed = $src->positionsPlayed;
      $dest->ab = $src->ab;
      $dest->bb = $src->bb;
      $dest->hit[0] = $src->hit[0];
      $dest->hit[1] = $src->hit[1];
      $dest->ob[0] = $src->ob[0];
      $dest->ob[1] = $src->ob[1];
      $dest->tb[0] = $src->tb[0];
      $dest->tb[1] = $src->tb[1];
      $dest->hr[0] = $src->hr[0];
      $dest->hr[1] = $src->hr[1];
      $dest->bps[0] = $src->bps[0];
      $dest->bps[1] = $src->bps[1];
      $dest->bphr[0] = $src->bphr[0];
      $dest->bphr[1] = $src->bphr[1];
      $dest->cl[0] = $src->cl[0];
      $dest->cl[1] = $src->cl[1];
      $dest->power[0] = $src->power[0];
      $dest->power[1] = $src->power[1];
      $dest->dp[0] = $src->dp[0];
      $dest->dp[1] = $src->dp[1];
      $dest->running = $src->running;
      $dest->hitAndRun = $src->hitAndRun;
      $dest->bunting = $src->bunting;
      $dest->lead = $src->lead;
      $dest->caught = $src->caught;
      $dest->first = $src->first;
      $dest->second = $src->second;
      $dest->ip = $src->ip;
      $dest->hold = $src->hold;
      $dest->endurance = $src->endurance;
      $dest->balk = $src->balk;
      $dest->wp = $src->wp;
      $dest->batting = $src->batting;
      $dest->age = $src->age;
    }
    private function processMoves() {
      $teams = $this->getTeams();
      $dir = "../../cgi-bin/jhml/data/" . $this->year . "/";
      for ($i=0; $i < count($teams); $i++) {
//print $teams[$i] . "<br/>";
        if ($teams[$i] == "DNQ") continue;
        if ($teams[$i] == "FRE") continue;
        if (! file_exists($dir . strtolower($teams[$i]) . "moves")) {
          print "Missing Moves file for " . $teams[$i] . "\n";
          exit;
        }
        $roster = $this->getRoster($teams[$i]);
//print $dir . strtolower($teams[$i]) . "moves" . "<br/";
        $fp = fopen($dir . strtolower($teams[$i]) . "moves",'r');
        while (($line = rtrim(fgets($fp))) !== false && strlen($line) > 2)  {
          $pieces = explode(":",$line);
          if (count($pieces) == 2) continue;
          if (strpos($line,"Traded for") !== false) {
            $newbies = $roster->processTradedFor($line);
            for ($j=0; $j < count($newbies); $j++) {
              $newbies[$j]->team = $teams[$i];
//print ">processTradedFor: " .  $newbies[$j]->player->name . "    " . $teams[$i] . "<br/>";
              $found = false;
              foreach($this->rosters as $team => $rosterI) {
                for ($k=0; $k < count($rosterI->batters) && !$found; $k++) {
                  if ($this->normalize($rosterI->batters[$k]->player->name) == $newbies[$j]->player->name) {
                    $this->copyStrat($rosterI->batters[$k]->player,$newbies[$j]->player);
                    $roster->batters[] = $newbies[$j];
                    $found = true;
                  }
                }
                for ($k=0; $k < count($rosterI->pitchers) && !$found; $k++) {
                  if ($this->normalize($rosterI->pitchers[$k]->player->name) == $newbies[$j]->player->name) {
                    $this->copyStrat($rosterI->pitchers[$k]->player,$newbies[$j]->player);
                    $roster->pitchers[] = $newbies[$j];
                    $found = true;
                  }
                }
              }
              if (! $found) {
                print 'Did not find ' . $newbies[$j]->player->name;
                exit;
              } else {
                //print $newbies[$j]->toString(true) . "<br/>";
              }
            }
          }
          $roster->processMoveFile($line);
        }
        fclose($fp);
      }
    }
    public function getTeams() {
      $rtn = [];
      foreach($this->rosters as $team => $roster) {
        $rtn[] = $team;
      }
      return $rtn;
    }
    public function getRoster($team) {
      return $this->rosters[strtoupper($team)];
    }
    //TODO
    public static function fromString($str) {
      $inst = new self();
      $js = json_decode($str);
      $inst->year = $js->rosters->year;
      $rs =  $js->rosters->rosters;
      for ($i =0; $i < count($rs); $i++) {
        $r = \Jhml\Roster::fromString(json_encode($rs[$i]));
        $inst->rosters[$r->team] = $r;
      }
      return $inst;
    }
    //TODO
    public function toString($includeStrat = false) {
      $rtn = '{"rosters":{"year":"' . $this->year . '"';
      $rtn .= ',"rosters":[';
      $first = true;
      foreach ($this->rosters as $r) {
        if (!$first) $rtn .= ',';
        $first = false;
        $rtn .= $r->toString($includeStrat);
      }
      $rtn .= ']';
      $rtn .= '}}';
      return $rtn;
    }
    public function json() {
      return json_decode($this->toString());
    }
  }


?>
