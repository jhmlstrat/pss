<?php

  namespace Jhml;
//TODO: Trades

  require_once "Roster.php";

class Rosters
{
    public $year;
    public $rosters = [];

    private $_baseURL = "http://www.jhmlstrat.org/";
    private $_AgesFile = "/AGES.txt";
    private $_NewRosterFile = "/Rosters.json";
    private $_OldRosterFile = "/LEAGUE.JHML";
    private $_StratHittersFile = "/Hitters.txt";
    private $_StratPitchersFile = "/Pitchers.txt";
    public function __construct($year=2016, $old=false, $files=true)
    {
        $this->year = $year;
        $this->_AgesFile = '../data/' . $year . $this->_AgesFile;
        $this->_NewRosterFile = '../data/' . $year . $this->_NewRosterFile;
        $this->_OldRosterFile = '../data/' . $year . $this->_OldRosterFile;
        $this->_StratHittersFile = '../data/' . $year . $this->_StratHittersFile;
        $this->_StratPitchersFile = '../data/' . $year . $this->_StratPitchersFile;
        $this->_loadRosterFile($old, $files);
    }

    private function _initialize()
    {
    }

    public function writeRosterFile()
    {
        file_put_contents($this->_NewRosterFile, $this->toString(false));
    }
    public function writeOldMovesFiles()
    {
        foreach ($this->rosters as $team => $roster) {
            $mvU = array();
            $mvD = array();
            $inj = array();
            for ($i=0; $i<102; $i++) {
                $mvU[] = array();
                $mvD[] = array();
                $inj[] = array();
            }
            foreach ($roster->getBatters() as $batter) {
                $name = str_replace('.', '', $batter->player->name);
                foreach ($batter->moves as $move) {
                    if ($move->moveType == \Scoring\MoveType::TO_MINORS 
                        || $move->moveType == \Scoring\MoveType::ON_DL
                        || $move->moveType == \Scoring\MoveType::TRADED_AWAY
                    ) {
                        $mvD[count($mvD[$move->gameNumber-1]) < 10 ?
                            $move->gameNumber-1 :
                            $move->gameNumber][]
                                =$name.'~'.
                                    \Scoring\MoveType::toString($move->moveType);
                    }
                    if ($move->moveType == \Scoring\MoveType::TO_MAJORS 
                        || $move->moveType == \Scoring\MoveType::OFF_DL
                        || $move->moveType == \Scoring\MoveType::TRADED_FOR
                    ) {
                        $mvU[count($mvU[$move->gameNumber-1]) < 10 ?
                            $move->gameNumber-1 :
                            $move->gameNumber][]
                                =$name.'~'.
                                    \Scoring\MoveType::toString($move->moveType);
                    }
                }
                foreach ($batter->injuries as $injury) {
                    if ($injury->duration == 0) {
                        $inj[$injury->gameNumber-1][]
                            =$name.'~REM~none';
                    } else {
                        $inj[$injury->gameNumber-1][]
                            =$name.'~'.$injury->duration.'~none';
                    }
                }
            }
            foreach ($roster->getPitchers() as $pitcher) {
                $name = str_replace('.', '', $pitcher->player->name);
                foreach ($pitcher->moves as $move) {
                    if ($move->moveType == \Scoring\MoveType::TO_MINORS
                        || $move->moveType == \Scoring\MoveType::ON_DL
                        || $move->moveType == \Scoring\MoveType::TRADED_AWAY
                    ) {
                        $mvD[count($mvD[$move->gameNumber-1]) < 10 ?
                            $move->gameNumber-1 :
                            $move->gameNumber][]
                                =$name.'~'.
                                    \Scoring\MoveType::toString($move->moveType);
                    }
                    if ($move->moveType == \Scoring\MoveType::TO_MAJORS 
                        || $move->moveType == \Scoring\MoveType::OFF_DL
                        || $move->moveType == \Scoring\MoveType::TRADED_FOR
                    ) {
                        $mvU[count($mvU[$move->gameNumber-1]) < 10 ? 
                            $move->gameNumber-1 : 
                            $move->gameNumber][]
                                =$name.'~'.
                                    \Scoring\MoveType::toString($move->moveType);
                    }
                }
                foreach ($pitcher->injuries as $injury) {
                    if ($injury->duration == 0) {
                        $inj[$injury->gameNumber-1][]
                            =$name.'~REM~none';
                    } else {
                        $inj[$injury->gameNumber-1][]
                            =$name.'~'.$injury->duration.'~none';
                    }
                }
            }
            $omf = fopen(
                '../data/' . $this->year . '/' . strtolower($team) . 'moves', 'w'
            );
            for ($i=0; $i<102; $i++) {
                asort($mvU[$i]);
                asort($mvD[$i]);
                asort($inj[$i]);
                fwrite($omf, $i+1);
                for ($j=0; $j<3; $j++) {
                    if (count($inj[$i]) > $j) {
                        fwrite($omf, ":".$inj[$i][$j]);
                    } else {
                        fwrite($omf, ":none~REM~none");
                    }
                }
                for ($j=0; $j<10; $j++) {
                    if (count($mvD[$i]) > $j) {
                        fwrite($omf, ":".$mvD[$i][$j]);
                    } else {
                        fwrite($omf, ":none~To minors");
                    }
                    if (count($mvU[$i]) > $j) {
                        fwrite($omf, "~".$mvU[$i][$j]);
                    } else {
                        fwrite($omf, "~none~Fm minors");
                    }
                }
                fwrite($omf, "\n");
            }
            fclose($omf);
        }
    }
    private function _loadRosterFile($old, $files)
    {
        if (file_exists($this->_NewRosterFile) && !$old) {
            $str = file_get_contents($this->_NewRosterFile);
            $tmp = self::fromString($str);
            $this->year = $tmp->year;
            $this->rosters = $tmp->rosters;
            $this->_loadStratInfo();
        } elseif (file_exists($this->_OldRosterFile)) {
            $rf = fopen($this->_OldRosterFile, 'r');
            if ($rf) {
                while (($line = rtrim(fgets($rf))) !== false and strlen($line) > 2) {
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
                $this->_loadStratInfo();
                if ($files) {
                    $this->_processMoves();
                }
            }
        } else {
            $this->_initialize();
        }
    }
    private function _loadStratInfo()
    {
        if (! file_exists($this->_StratHittersFile)) {
            print "Missing Hitters file\n";
            exit;
        }
        $addI = 0;
        $add = 0;
        if ($this->year >= 2020) {
            $add ++;
            $addI = $addI + 2;
        }
        $hf = fopen($this->_StratHittersFile, 'r');
        $count = 0;
        while (($line = fgets($hf)) !== false) {
            if ($count > 0) {
                $pieces = explode("\t", $line);
                //foreach ($pieces as $value) { print $value . "\n"; }
                if ($pieces[$addI+2] < 100) {
                    continue;
                }
                $rindices = [];
                $bindices = [];
                $name = str_replace("\"", "", $pieces[$add+1]);
                $name = str_replace("+", "", $name);
                $name = str_replace("*", "", $name);
                $name = str_replace(",", ", ", $name);
                $name = str_replace(".", "", $name);
                foreach ($this->rosters as $team => $roster) {
                    for ($j = 0; $j < count($roster->batters); $j++) {
                        $test = str_replace(
                            ".", "", $roster->batters[$j]->player->name
                        );
                        if (strncmp($test, $name, strlen($name)) == 0) {
                            array_push($rindices, $team);
                            array_push($bindices, $j);
                            //$j=count($roster->batters);
                        }
                    }
                }
                if (count($rindices) == 0 || count($bindices) == 0) {
                    print "Batter not found " . $name . "\n";
                    exit;
                }
                for ($j = 0; $j < count($rindices); $j++) {
                    $rindex = $rindices[$j];
                    $bindex = $bindices[$j];
                    $this->rosters[$rindex]->batters[$bindex]->player->realTeam 
                        = $pieces[0];
                    if (strpos($pieces[$add+1], "+") !== false) {
                        $this->rosters[$rindex]->batters[$bindex]->player->hand="S";
                    }
                    if (strpos($pieces[$add+1], "*") !== false) {
                        $this->rosters[$rindex]->batters[$bindex]->player->hand="L";
                    }
                    $this->rosters[$rindex]->batters[$bindex]->player->ab 
                        = $pieces[$addI+2];
                    $this->rosters[$rindex]->batters[$bindex]->player->hit[0] 
                        = $pieces[$addI+5];
                    $this->rosters[$rindex]->batters[$bindex]->player->ob[0] 
                        = $pieces[$addI+6];
                    $this->rosters[$rindex]->batters[$bindex]->player->tb[0] 
                        = $pieces[$addI+7];
                    $this->rosters[$rindex]->batters[$bindex]->player->hr[0] 
                        = $pieces[$addI+8];
                    $this->rosters[$rindex]->batters[$bindex]->player->bps[0] 
                        = '0.0';
                    if (strpos($pieces[$addI+9], "*") === false) {
                        $this->rosters[$rindex]->batters[$bindex]->player->bps[0] 
                            = '5.0';
                    }
                    $this->rosters[$rindex]->batters[$bindex]->player->power[0] 
                        = 'W';
                    $this->rosters[$rindex]->batters[$bindex]->player->bphr[0] 
                        = '0.0';
                    if (strpos($pieces[$addI+9], "W") === false
                        and strpos($pieces[$addI+9], "w") === false
                    ) {
                        $this->rosters[$rindex]->batters[$bindex]->player->power[0]
                            = 'N';
                        $bphr=str_replace("w", "", $pieces[$addI+9]);
                        $bphr=str_replace("W", "", $bphr);
                        $bphr=str_replace("*", "", $bphr);
                        $this->rosters[$rindex]->batters[$bindex]->player->bphr[0]
                            = $bphr;
                    }
                    $this->rosters[$rindex]->batters[$bindex]->player->cl[0]
                        = $pieces[$addI+10];
                    $this->rosters[$rindex]->batters[$bindex]->player->dp[0]
                        = $pieces[$addI+11];
                    $this->rosters[$rindex]->batters[$bindex]->player->hit[1]
                        = $pieces[$addI+14];
                    $this->rosters[$rindex]->batters[$bindex]->player->ob[1]
                        = $pieces[$addI+15];
                    $this->rosters[$rindex]->batters[$bindex]->player->tb[1]
                        = $pieces[$addI+16];
                    $this->rosters[$rindex]->batters[$bindex]->player->hr[1]
                        = $pieces[$addI+17];
                    $this->rosters[$rindex]->batters[$bindex]->player->bps[1]
                        = '0.0';
                    if (strpos($pieces[$addI+18], "*") === false) {
                        $this->rosters[$rindex]->batters[$bindex]->player->bps[1]
                            = '5.0';
                    }
                    $this->rosters[$rindex]->batters[$bindex]->player->power[1]
                        = 'W';
                    $this->rosters[$rindex]->batters[$bindex]->player->bphr[1]
                        = '0.0';
                    if (strpos($pieces[$addI+18], "W") === false
                        and strpos($pieces[$addI+18], "w") === false
                    ) {
                        $this->rosters[$rindex]->batters[$bindex]->player->power[1]
                            = 'N';
                        $bphr=str_replace("w", "", $pieces[$addI+18]);
                        $bphr=str_replace("W", "", $bphr);
                        $bphr=str_replace("*", "", $bphr);
                        $this->rosters[$rindex]->batters[$bindex]->player->bphr[1]
                            = $bphr;
                    }
                    $this->rosters[$rindex]->batters[$bindex]->player->cl[1]
                        = $pieces[$addI+19];
                    $this->rosters[$rindex]->batters[$bindex]->player->dp[1]
                        = $pieces[$addI+20];
                    $steal = str_replace("\"", "", $pieces[$addI+21]);
                    $steal = str_replace('(', '', $steal);
                    $steal = str_replace(')', '', $steal);
                    $pos=strpos($steal, " ");
                    $first=substr($steal, 0, $pos);
                    $p1=strpos($first, '/');
                    $second=substr($steal, $pos+1);
                    $p2=strpos($second, '-');
                    $this->rosters[$rindex]->batters[$bindex]->player->lead
                        = substr($first, 0, $p1);
                    $this->rosters[$rindex]->batters[$bindex]->player->caught
                        = substr($first, $p1+1);
                    $this->rosters[$rindex]->batters[$bindex]->player->first
                        = strval(intval(substr($second, 0, $p2)));
                    $this->rosters[$rindex]->batters[$bindex]->player->second
                        = strval(intval(substr($second, $p2+1)));
                    $this->rosters[$rindex]->batters[$bindex]->player->running
                        = $pieces[$addI+23];
                    $this->rosters[$rindex]->batters[$bindex]->player->bunting
                        = $pieces[$addI+24];
                    $this->rosters[$rindex]->batters[$bindex]->player->hitAndRun
                        = $pieces[$addI+25];
                    $arm = 0;
                    for ($i = 26; $i < 34; $i++) {
                        if ($pieces[$addI+$i] !==  '') {
                            $pos = new \ProjectScoresheet\Position();
                            $pos->p(
                                \ProjectScoresheet\Position::position(
                                    \ProjectScoresheet\Position::positionString(
                                        $i-24
                                    )
                                )
                            );
                            if ($pos->position == 2) {
                                $cStr = preg_replace(
                                    '/ .*$/', "", 
                                    preg_replace(
                                        '/^.*c-/', "", 
                                        rtrim($pieces[$addI+34])
                                    )
                                );
                                $pos->arm = preg_replace(
                                    '/.*\(/', "", preg_replace('/\).*$/', "", $cStr)
                                );
                                $pos->t = preg_replace(
                                    '/\(.*$/', "", preg_replace('/.*T-/', "", $cStr)
                                );
                                $pos->pb = preg_replace(
                                    '/\).*$/', "", preg_replace('/.*pb-/', "", $cStr)
                                );
                            }
                            if ($pos->position == 7 || $pos->position == 8 
                                ||$pos->position == 9
                            ) {
                                $pos->arm = preg_replace(
                                    '/\).*$/', "", 
                                    preg_replace(
                                        '/^.*f-.\(/', "", 
                                        rtrim($pieces[$addI+34])
                                    )
                                );
                            }
                            $pos->rating = substr($pieces[$addI+$i], 0, 1);
                            $pos->e = substr($pieces[$addI+$i], 1);
                            array_push(
                                $this->rosters[$rindex]->batters[$bindex]
                                    ->player->positionsPlayed, $pos
                            );
                        }
                    }
                }
            }
            $count ++;
        }
        fclose($hf);
        $missing = false;
        foreach ($this->rosters as $team => $roster) {
            for ($j = 0; $j < count($roster->batters); $j++) {
                if (strcmp($roster->batters[$j]->player->realTeam, '') == 0) {
                    print "No team for " . $roster->batters[$j]->player->name . "\n";
                    $missing = true;
                }
            }
        }
        if ($missing) {
            exit;
        }
        if (! file_exists($this->_StratPitchersFile)) {
            print "Missing Pitchers file\n";
            exit;
        }
        $pf = fopen($this->_StratPitchersFile, 'r');
        $count = 0;
        while (($line = fgets($pf)) !== false) {
            if ($count > 0) {
                $pieces = explode("\t", $line);
                //foreach ($pieces as $value) { print $value . "\n"; }
                if ($pieces[$add+2] < 30) {
                    continue;
                }
                $rindices = [];
                $pindices = [];
                $name = str_replace("\"", "", $pieces[$add+1]);
                $name = str_replace("+", "", $name);
                $name = str_replace("*", "", $name);
                $name = str_replace(",", ", ", $name);
                $name = str_replace(".", "", $name);
                foreach ($this->rosters as $team => $roster) {
                    for ($j = 0; $j < count($roster->pitchers); $j++) {
                        $test = str_replace(
                            ".", "", $roster->pitchers[$j]->player->name
                        );
                        if (strncmp($test, $name, strlen($name)) == 0) {
                            array_push($rindices, $team);
                            array_push($pindices, $j);
                        }
                    }
                }
                if (count($pindices) == 0) {
                    print "Pitcher not found " . $name . "\n";
                    exit;
                }
                for ($j = 0; $j < count($rindices); $j++) {
                    $rindex = $rindices[$j];
                    $pindex = $pindices[$j];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->realTeam
                        = $pieces[0];
                    if (strpos($pieces[$add+1], "+") !== false) {
                        $this->rosters[$rindex]->pitchers[$pindex]->player->hand="S";
                    }
                    if (strpos($pieces[$add+1], "*") !== false) {
                        $this->rosters[$rindex]->pitchers[$pindex]->player->hand="L";
                    }
                    $this->rosters[$rindex]->pitchers[$pindex]->player->ip
                        = $pieces[$add+2];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->hit[0]
                        = $pieces[$add+5];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->ob[0]
                        = $pieces[$add+6];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->tb[0]
                        = $pieces[$add+7];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->hr[0]
                        = $pieces[$add+8];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->bps[0]
                        = '0.0';
                    if (strpos($pieces[$add+9], "*") === false) {
                        $this->rosters[$rindex]->pitchers[$pindex]->player->bps[0]
                            = '5.0';
                    }
                    $this->rosters[$rindex]->pitchers[$pindex]->player->power[0]
                        = 'W';
                    $this->rosters[$rindex]->pitchers[$pindex]->player->bphr[0]
                        = '0.0';
                    if (strpos($pieces[$add+9], "W") === false 
                        and strpos($pieces[$add+9], "w") === false
                    ) {
                        $this->rosters[$rindex]->pitchers[$pindex]->player->power[0]
                            = 'N';
                        $bphr=str_replace("w", "", $pieces[$add+9]);
                        $bphr=str_replace("W", "", $bphr);
                        $bphr=str_replace("*", "", $bphr);
                        $this->rosters[$rindex]->pitchers[$pindex]->player->bphr[0] 
                            = $bphr;
                    }
                    $this->rosters[$rindex]->pitchers[$pindex]->player->hit[1] 
                        = $pieces[$add+13];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->ob[1] 
                        = $pieces[$add+14];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->tb[1] 
                        = $pieces[$add+15];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->hr[1] 
                        = $pieces[$add+16];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->bps[1] 
                        = '0.0';
                    if (strpos($pieces[$add+17], "*") === false) {
                        $this->rosters[$rindex]->pitchers[$pindex]->player->bps[1] 
                            = '5.0';
                    }
                    $this->rosters[$rindex]->pitchers[$pindex]->player->power[1] 
                        = 'W';
                    $this->rosters[$rindex]->pitchers[$pindex]->player->bphr[1] 
                        = '0.0';
                    if (strpos($pieces[$add+17], "W") === false 
                        and strpos($pieces[$add+17], "w") === false
                    ) {
                        $this->rosters[$rindex]->pitchers[$pindex]->player->power[1]
                            = 'N';
                        $bphr=str_replace("w", "", $pieces[$add+17]);
                        $bphr=str_replace("W", "", $bphr);
                        $bphr=str_replace("*", "", $bphr);
                        $this->rosters[$rindex]->pitchers[$pindex]->player->bphr[1] 
                            = $bphr;
                    }
                    $this->rosters[$rindex]->pitchers[$pindex]->player->hold 
                        = $pieces[$add+19];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->endure 
                        = $pieces[$add+20];
                    $pos = new \ProjectScoresheet\Position();
                    $pos->p(\ProjectScoresheet\Position::position('P'));
                    $pos->rating = substr($pieces[$add+21], 0, 1);
                    $pos->e = substr($pieces[$add+21], 2);
                    array_push(
                        $this->rosters[$rindex]->pitchers[$pindex]
                            ->player->positionsPlayed, $pos
                    );
                    $this->rosters[$rindex]->pitchers[$pindex]->player->balk 
                        = $pieces[$add+22];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->wp 
                        = $pieces[$add+23];
                    $this->rosters[$rindex]->pitchers[$pindex]->player->bunting 
                        = preg_replace('/.*-/', "", $pieces[$add+24]);
                    $this->rosters[$rindex]->pitchers[$pindex]->player->batting 
                        = preg_replace('/-.*/', "", $pieces[$add+24]);
                    $this->rosters[$rindex]->pitchers[$pindex]->player->running 
                        = rtrim($pieces[$add+26]);
                }
            }
            $count ++;
        }
        fclose($pf);
        foreach ($this->rosters as $team => $roster) {
            for ($j = 0; $j < count($roster->pitchers); $j++) {
                if (strcmp($roster->pitchers[$j]->player->realTeam, '') == 0) {
                    print "No team for " . $roster->pitchers[$j]->player->name ."\n";
                    $missing = true;
                }
            }
        }
        if ($missing) {
            exit;
        }
    }
    private function _normalize($str)
    {
        $str = str_replace(" ", "", $str);
        $str = str_replace("-", "", $str);
        $str = str_replace(".", "", $str);
        $str = str_replace("'", "", $str);
        $str = str_replace("Castillo,Wellington", "Castillo,Welington", $str);
        $str = str_replace("Myers,Will", "Myers,Wil", $str);
        return str_replace(
            " ", "", 
            str_replace("-", "", str_replace(".", "", str_replace("'", "", $str)))
        );
    }
    private function _copyStrat($src, $dest)
    {
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
        $dest->endure = $src->endure;
        $dest->balk = $src->balk;
        $dest->wp = $src->wp;
        $dest->batting = $src->batting;
        $dest->age = $src->age;
    }
    private function _processMoves()
    {
        $teams = $this->getTeams();
        $dir = "../../cgi-bin/jhml/data/" . $this->year . "/";
        for ($i=0; $i < count($teams); $i++) {
            //print $teams[$i] . "<br/>";
            if ($teams[$i] == "DNQ") {
                continue;
            }
            if ($teams[$i] == "FRE") {
                continue;
            }
            if (! file_exists($dir . strtolower($teams[$i]) . "moves")) {
                print "Missing Moves file for " . $teams[$i] . "\n";
                exit;
            }
            $roster = $this->getRoster($teams[$i]);
            //print $dir . strtolower($teams[$i]) . "moves" . "<br/";
            $fp = fopen($dir . strtolower($teams[$i]) . "moves", 'r');
            while (($line = rtrim(fgets($fp))) !== false && strlen($line) > 2) {
                $pieces = explode(":", $line);
                if (count($pieces) == 2) {
                    continue;
                }
                if (strpos($line, "Traded for") !== false) {
                    $newbies = $roster->processTradedFor($line);
                    for ($j=0; $j < count($newbies); $j++) {
                        $newbies[$j]->team = $teams[$i];
                        $found = false;
                        foreach ($this->rosters as $team => $rosterI) {
                            for ($k=0; $k < count($rosterI->batters) 
                                && !$found; $k++
                            ) {
                                if ($this->_normalize(
                                    $rosterI->batters[$k]->player->name
                                ) == $this->_normalize($newbies[$j]->player->name)
                                ) {
                                    $this->_copyStrat(
                                        $rosterI->batters[$k]->player, 
                                        $newbies[$j]->player
                                    );
                                    $roster->batters[] = $newbies[$j];
                                    $found = true;
                                }
                            }
                            for ($k=0; $k < count($rosterI->pitchers) 
                                && !$found; $k++
                            ) {
                                if ($this->_normalize(
                                    $rosterI->pitchers[$k]->player->name
                                ) == $this->_normalize($newbies[$j]->player->name)
                                ) {
                                    $this->_copyStrat(
                                        $rosterI->pitchers[$k]->player, 
                                        $newbies[$j]->player
                                    );
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
    public function addMove($team, $name, $gameNumber, $type)
    {
        $roster = $this->getRoster($team);
        $roster->move($name, \Scoring\MoveType::fromString($type), $gameNumber);
    }
    public function addInjury($team, $name, $gameNumber, $duration)
    {
        $roster = $this->getRoster($team);
        $roster->addInjury($name, $gameNumber, $duration);
    }
    public function getTeams()
    {
        $rtn = [];
        foreach ($this->rosters as $team => $roster) {
            $rtn[] = $team;
        }
        return $rtn;
    }
    public function getRoster($team)
    {
        return $this->rosters[strtoupper($team)];
    }
    public static function fromString($str)
    {
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
    public function toString($includeStrat = false)
    {
        $rtn = '{"rosters":{"year":"' . $this->year . '"';
        $rtn .= ',"rosters":[';
        $first = true;
        foreach ($this->rosters as $r) {
            if (!$first) {
                $rtn .= ',';
            }
            $first = false;
            $rtn .= $r->toString($includeStrat);
        }
        $rtn .= ']';
        $rtn .= '}}';
        return $rtn;
    }
    public function json()
    {
        return json_decode($this->toString());
    }
}
?>
