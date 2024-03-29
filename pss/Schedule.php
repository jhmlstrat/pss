<?php

  namespace Scoring;

  require_once "../pss/ScheduleItem.php";
  require_once "../pss/ResultsFile.php";

class Schedule
{
    private $_config;
    private $_year;

    private $_schedules = [];

    private function _addSI($t, $oppF, $numH, $numA, $s)
    {
        $h = 'home';
        $a = 'away';
        $opp = '';
        foreach ($this->_config['teams'] as $key => $team) {
            if ($team['franchise'] == $oppF) {
                $opp = $team['team'];
            }
        }
        if ($numH == 7) {
            array_push(
                $this->_schedules[$t]['home'], 
                \Scoring\ScheduleItem::newSI($t, $opp, 4, $s)
            );
            $numH = 3;
        }
        if ($numA == 7) {
            array_push(
                $this->_schedules[$t][$a], 
                \Scoring\ScheduleItem::newSI($opp, $t, 4, $s)
            );
            $numA = 3;
        }
        if ($numH > 0) {
            array_push(
                $this->_schedules[$t][$h], 
                \Scoring\ScheduleItem::newSI($t, $opp, $numH, $s)
            );
        }
        if ($numA > 0) {
            array_push(
                $this->_schedules[$t][$a], 
                \Scoring\ScheduleItem::newSI($opp, $t, $numA, $s)
            );
        }
    }

    public function __construct($year = 2017)
    {
        $this->_year = $year;
        $rf = new ResultsFile($year);
        //var_dump($rf);
        unset($previous);
        $json = file_get_contents("../data/config.json");
        $confs = json_decode($json, true);
        foreach ($confs['years'] as $conf) {
            if ($conf['year'] == $year-1) {
                $previous = $conf;
            }
            if ($conf['year'] == $year) {
                $this->_config = $conf;
            }
        }
        # print(count($this->_config['teams']) . "\n");
        $pssG = [];
        if (count($this->_config['teams']) == 15) {
            $w = [];
            foreach ($this->_config['teams'] as $key => $team) {
                $w[$team['franchise']] = 0;
                $pssG[$team['franchise']] = [];
            }
            $yearLess = $year - 1;
            $results = file_get_contents("../../" . $yearLess . "/misc/rec_team.html");
            $entries = explode("\n", $results);
            $first = -1;
            for ($i = 0; $i < count($entries); $i++) {
                if (substr($entries[$i], 0, 8) == '--------') {
                    $first = $i;
                    $i = count($entries);
                }
            }
            $first --;
            if ($first < 0) {
                print "Error reading results<br/>";
                return;
            }
            //print_r("<pre>" . $entries[$first] . "</pre><br/>");
            $columns = preg_replace('/<[^>]*> */', '', $entries[$first]);
            $columns = preg_replace('/ [ \t]*/', ' ', $columns);
            $pieces = explode(" ", $columns);
            if (count($pieces) != count($w)) {
                print "Mismatched number of teams<br/>";
                return;
            }
            $foundCount = 0;
            $lines = [];
            $offset = [];
            for (
                $i = $first+1; 
                $i < count($entries) 
                && $foundCount < count($this->_config['teams']);
                $i++
            ) {
                $line = preg_replace('/<[^>]*> */', '', $entries[$i]);
                $line = preg_replace('/^ [ \t]*/', ' ', $line);
                $line = preg_replace('/\./', '', $line);
                if (strlen($line) <2) {
                    continue;
                }
                if (substr($line, 0, 8) == '--------') {
                    continue;
                }
                if ($entries[$i] == $entries[$first]) {
                    continue;
                }
                $found = false;
                for ($j=0; $j < count($this->_config['teams']); $j++) {
                    $city = $previous['teams'][$j]['city'] . ' ' . 
                        $previous['teams'][$j]['team_name'];
                    if (substr($line, 0, strlen($city)) == $city) {
                        $found = true;
                        $foundCount ++;
                        $line = preg_replace('/'.$city.' */', '', $line);
                        $line = preg_replace(
                            '/(\d)[ \t]*-[ \t]*(\d)/', '$1+$2', $line
                        );
                        $line = preg_replace('/[ \t][ \t]*/', ' ', $line);
                        $line = preg_replace('/[ \t]*$/', '', $line);
                        $lines[$previous['teams'][$j]['franchise']] = $line;
                        $against = explode(' ', $line);
                        for ($k=0; $k < count($against)-1; $k++) {
                            if (substr($against[$k], 0, 2) == '--') {
                                $offset[$previous['teams'][$j]['franchise']] = $k;
                            } else {
                                $ws = explode('+', $against[$k]);
                                $w[$previous['teams'][$j]['franchise']] += 
                                    intval($ws[0]);
                            }
                        }
                    }
                }
                if (! $found) {
                    print "-" . $line . "- " . $foundCount . "<br/>";
                    return;
                }
            }
            //print_r($w);
            $div = [];
            foreach ($this->_config['teams'] as $team) {
                if (! array_key_exists($team['division'], $div)) {
                    $div[$team['division']] = [$team];
                } else {
                    $added = false;
                    for ($i=0; $i < count($div[$team['division']]); $i++) {
                        $fr = $div[$team['division']][$i]['franchise'];
                        if ($w[$team['franchise']] > $w[$fr]) {
                            array_splice($div[$team['division']], $i, 0, [$team]);
                            $j = $i;
                            $added = true;
                            $i = count($div[$team['division']]);
                        }
                        if ($i < count($div[$team['division']]) ) {
                            $fr = $div[$team['division']][$i]['franchise'];
                            if ($w[$team['franchise']] == $w[$fr]) {
                                $wls = explode(' ', $lines[$team['franchise']]);
                                $wl = explode(
                                    '+', 
                                    $wls[$offset[$fr]]
                                );
                                if ($wl[0] > $wl[1]) {
                                    array_splice(
                                        $div[$team['division']], $i, 0, [$team]
                                    );
                                    $added = true;
                                    $i = count($div[$team['division']]);
                                }
                            }
                        }
                    }
                    if (! $added) {
                        array_push($div[$team['division']], $team);
                    }
                }
            }
            //print_r($div);
            //print "<br/>";
            $mult = 1;
            if ($year % 2 == 0) {
                $mult = -1;
            }
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
            foreach ($this->_config['teams'] as $key => $team) {
                $div_off = -1;
                $team_off = -1;
                $base_off = -1;
                $t = $team['team'];
                $tdiv = $team['division'];
                for ($i=0; $i <count($divs); $i++) {
                    if ($divs[$i] == $tdiv) {
                        $div_off = $i;
                    }
                }
                $c = count($div[$tdiv]);
                for ($i=0; $i < $c; $i++) {
                    if ($div[$tdiv][$i]['franchise'] == $team['franchise']) {
                        $team_off = $i;
                    }
                    if ($base[$tdiv][$i] == $team['franchise']) {
                        $base_off = $i;
                    }
                }
    
                $this->_schedules[$t] = [];
                $this->_schedules[$t]['home'] = [];
                $this->_schedules[$t]['away'] = [];
                $this->_addSI($t, $base[$tdiv][($base_off - $mult + $c)%$c], 7, 3, \Scoring\Seasons::SPRING);
                $this->_addSI($t, $base[$tdiv][($base_off - $mult + $c)%$c], 0, 3, \Scoring\Seasons::FALL);
                $this->_addSI(
                    $t, $base[$tdiv][($base_off - $mult - $mult + $c)%$c], 7, 3, \Scoring\Seasons::SPRING
                );
                $this->_addSI(
                    $t, $base[$tdiv][($base_off - $mult - $mult + $c)%$c], 0, 3, \Scoring\Seasons::FALL
                );
                $this->_addSI($t, $base[$tdiv][($base_off + $mult + $c)%$c], 3, 7, \Scoring\Seasons::SPRING);
                $this->_addSI($t, $base[$tdiv][($base_off + $mult + $c)%$c], 3, 0, \Scoring\Seasons::FALL);
                $this->_addSI(
                    $t, $base[$tdiv][($base_off + $mult + $mult + $c)%$c], 3, 7, \Scoring\Seasons::SPRING
                );
                $this->_addSI(
                    $t, $base[$tdiv][($base_off + $mult + $mult + $c)%$c], 3, 0, \Scoring\Seasons::FALL
                );
                for ($i=0; $i <count($divs); $i++) {
                    if ($i == $div_off) {
                        continue;
                    }
                    for ($j=0; $j < $c; $j++) {
                        if ($j != $team_off) {
                            if ($i == ($div_off - $mult + count($divs))%count($divs)) {
                                $this->_addSI(
                                    $t, $div[$divs[$i]][$j]['franchise'], 3, 2, \Scoring\Seasons::SPRING
                                );
                            } else {
                                $this->_addSI(
                                    $t, $div[$divs[$i]][$j]['franchise'], 2, 3, \Scoring\Seasons::SPRING
                                );
                            }
                        }
                    }
                }
                $c = count($divs);
                $this->_addSI(
                    $t, $div[$divs[($div_off - $mult + $c)%$c]][$team_off]['franchise'],
                    0, 2, \Scoring\Seasons::SPRING
                );
                $this->_addSI(
                    $t, $div[$divs[($div_off - $mult + $c)%$c]][$team_off]['franchise'],
                    3, 0, \Scoring\Seasons::FALL
                );
                $this->_addSI(
                    $t, $div[$divs[($div_off + $mult + $c)%$c]][$team_off]['franchise'],
                    2, 0, \Scoring\Seasons::SPRING
                );
                $this->_addSI(
                    $t, $div[$divs[($div_off + $mult + $c)%$c]][$team_off]['franchise'],
                    0, 3, \Scoring\Seasons::FALL
                );
                $series = [];
                $count = 0;
                if ($rf->games[$t]) {
                    foreach ($rf->games[$t] as $gm) {
                        //var_dump($this->_schedules[$t]);
                        //var_dump($gm);
                        $count ++;
                        if ($count == count($rf->games[$t])) {
                            array_push($series, $gm);
                        }
                        if ($count == count($rf->games[$t]) || count($series) == 4 
                            || (count($series) > 0 
                            && ($series[0]->team_[0] != $gm->team_[0] 
                            || $series[0]->team_[1] != $gm->team_[1]))
                        ) {
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
                            foreach ($this->_schedules[$t][$side] as $sg) {
                                if ($series[0]->team_[$off] == $sg->$oside 
                                    && count($series) == $sg->games_ 
                                    && count($sg->results_) == 0 && ! $added
                                ) {
                                    foreach ($series as $gmm) {
                                        array_push($sg->results_, $gmm);
                                    }
                                    $added=true;
                                }
                            }
                            //if (! $added) {
                            //var_dump($series);
                            //print "Shouldn't get here " . $series[0]->team_[0] . 
                            //    " at " .  $series[0]->team_[1] . 
                            //    "(" . $t . ") for " . count($series) . "\n";
                            //}
                            $series = [];
                        }
                        array_push($series, $gm);
                    }
                }
            }
        } else {
            print("Do 14\n");
        }
        $psss = glob('../data/'. $year . '/*.pss');
        foreach ($psss as $pss) {
            //error_log($pss.PHP_EOL,3,'error_log');
            $fn = str_replace('.pss', '', preg_replace('/.*\//', "", $pss));
            $a = substr($fn, 0, 3);
            $ag = intval(substr($fn, 3, 3));
            $h = substr($fn, 6, 3);
            $hg = intval(substr($fn, 9, 3));
            //error_log($a.'('.$ag.')@'.$h.'('.$hg.')'.PHP_EOL,3,'error_log');
            $g = Game::getGameFromScoreSheet($year, $a, $ag, $h, $hg);
            $pssG[strtolower($a)][$ag-1] = $g;
            $pssG[strtolower($h)][$hg-1] = $g;
        }
        foreach ($this->_schedules as $t => $sched) {
            if (!isset($pssG[$t]) or count($pssG[$t]) == 0) {
                continue;
            }
            //error_log($t.PHP_EOL,3,'error_log');
            //error_log(count($pssG[$t]).PHP_EOL,3,'error_log');
            //error_log(print_r($sched,true).PHP_EOL,3,'error_log');
            $series = [];
            $h='';
            $a='';
            $i=0;
            ksort($pssG[$t]);
            foreach ($pssG[$t] as $g) {
                $i++;
                //error_log($g->toString().PHP_EOL,3,'error_log');
                if (strtolower($g->team_[0]) != $t && strtolower($g->team_[1]) != $t) continue;
                //error_log($g->team_[0].PHP_EOL,3,'error_log');
                if (strtolower($g->team_[0]) == $t) {
                    $side = 'away';
                    $oside = 'home';
                } else {
                    $side = 'home';
                    $oside = 'away';
                }

// TBD - Needs to be based on seriesCompleted - which needs to be fixed/added

                if (($a != strtolower($g->team_[0]) || $h != $g->team_[1]
                    || count($series) == 4) && (count($series) != 0)
                ) {
                    $inserted = false;
                    foreach ($sched[$oside] as $s) {
                        if ($s->home_ == $h && $s->away_ == $a 
                            && $s->games_ == count($series) 
                            && (($s->season_ == 0 && $series[0]->gameNumber_[0] < 85)
                            || ($s->season_ == 1 && $series[0]->gameNumber_[0] > 84))
                        ) {
                            $inserted = true;
                            $series[count($series)-1]->seriesComplete_ = true;
                            $s->results_ = array_slice($series, 0);
                            $series = [];
                        }
                    }
                    //if (! $inserted) {
                    //    error_log('Failed to insert series - '. count($series).PHP_EOL, 3, 'error_log');
                        //error_log(print_r($series, true).PHP_EOL, 3, 'error_log');
                    //}
                }
                $oside = $side;
                $a = strtolower($g->team_[0]);
                $h = strtolower($g->team_[1]);
                array_push($series, $g);
                //error_log(print_r($series,true).PHP_EOL,3,'error_log');
                //error_log($a.'@'.$h.' - '.$oside.PHP_EOL,3,'error_log');
                if ($i == count($pssG[$t])) {
                    $inserted = false;
                    foreach ($sched[$oside] as $s) {
                        if ($s->home_ == $h && $s->away_ == $a 
                            && (($s->season_ == 0 
                            && $series[0]->gameNumber_[0] < 85) 
                            || ($s->season_ == 1 
                            && $series[0]->gameNumber_[0] > 84)) 
                            && ! $inserted
                        ) {
                            $inserted = true;
                            if ($s->games_ == count($series)) $series[count($series)-1]->seriesComplete_ = true;
                            $s->results_ = array_slice($series, 0);
                            //error_log('Here: '.$s->toString().PHP_EOL,3,'error_log');
                            //error_log('    : '.json_encode($series).PHP_EOL,3,'error_log');
                            $series = [];
                            //error_log('    : '.json_encode($series).PHP_EOL,3,'error_log');
                        }
                    }
                    if (! $inserted) {
                        error_log('Failed to insert last series'.PHP_EOL, 3, 'error_log');
                    //    error_log($series[0]->gameNumber_.PHP_EOL, 3, 'error_log');
                    //    error_log(print_r($series, true).PHP_EOL, 3, 'error_log');
                    }
                }
                //error_log(print_r($sched[$side],true).PHP_EOL,3,'error_log');
            }
        }
        //print($this->getSchedule('pit')); print "<br/>";
    }

    public function getSchedule($team)
    {
        $resultsA = [];
        $rtn = '{"home":[';
        $fall = [];
        for ($i=0; $i <count($this->_schedules[$team]['home']); $i++) {
            $sea = $this->_schedules[$team]['home'][$i]->season_;
            if ($sea == \Scoring\Seasons::FALL) {
                array_push($fall, $this->_schedules[$team]['home'][$i]);
            } else {
                if ($i > 0) {
                    $rtn .= ',';
                }
                $rtn .= $this->_schedules[$team]['home'][$i]->toString();
            }
            for (
                $j=0; $j < count($this->_schedules[$team]['home'][$i]->results_);
                $j++
            ) {
                //error_log(print_r($this->_schedules[$team]['home'][$i]->results_,true).PHP_EOL,3,'error_log');
                $gn = $this->_schedules[$team]['home'][$i]->results_[$j]
                    ->gameNumber_[1];
                //error_log(print_r($gn,true).PHP_EOL,3,'error_log');

                $found = false;
                //for ($k=0; (! $found) && ($k < count($resultsA)); $k++) {
                //    if ($resultsA[$k]->team_[0] == $team) {
                //        $cg=$resultsA[$k]->gameNumber_[0];
                //    } else {
                //        $cg=$resultsA[$k]->gameNumber_[1];
                //    }
                //    if ($cg > $gn) {
                //        array_splice(
                //            $resultsA, $k, 0, 
                //            [$this->_schedules[$team]['home'][$i]->results_[$j]]
                //        );
                //        $found = true;
                //    }
               // }
                if (! $found) {
                    $resultsA[$gn - 1] = $this->_schedules[$team]['home'][$i]->results_[$j];
                    //array_push(
                    //    $resultsA, $this->_schedules[$team]['home'][$i]->results_[$j]
                    //);
                }
            }
        }
        for ($i=0; $i <count($fall); $i++) {
            $rtn .= ',' . $fall[$i]->toString();
        }
        $rtn .= '],"away":[';
        $fall = [];
        for ($i=0; $i <count($this->_schedules[$team]['away']); $i++) {
            $sea = $this->_schedules[$team]['away'][$i]->season_;
            if ($sea == \Scoring\Seasons::FALL) {
                array_push($fall, $this->_schedules[$team]['away'][$i]);
            } else {
                if ($i > 0) {
                    $rtn .= ',';
                }
                $rtn .= $this->_schedules[$team]['away'][$i]->toString();
            }
            for ($j=0; $j < count($this->_schedules[$team]['away'][$i]->results_); 
                 $j++
            ) {
                $gn = $this->_schedules[$team]['away'][$i]
                    ->results_[$j]->gameNumber_[0];
                $found = false;
                //for ($k=0; (! $found) && ($k < count($resultsA)); $k++) {
                //    if ($resultsA[$k]->team_[0] == $team) {
                //        $cg=$resultsA[$k]->gameNumber_[0];
                //    } else {
                //        $cg=$resultsA[$k]->gameNumber_[1];
                //    }
                //    if ($cg > $gn) {
                //        array_splice(
                //            $resultsA, $k, 0, 
                //            [$this->_schedules[$team]['away'][$i]->results_[$j]]
                //        );
                //        $found = true;
                //    }
               // }
                if (! $found) {
                    $resultsA[$gn - 1] = $this->_schedules[$team]['away'][$i]->results_[$j];
                    //array_push(
                    //    $resultsA, $this->_schedules[$team]['away'][$i]->results_[$j]
                    //);
                }
            }
        }
        for ($i=0; $i <count($fall); $i++) {
            $rtn .= ',' . $fall[$i]->toString();
        }
        $days = 0;
        // error_log(print_r($resultsA,true).PHP_EOL,3,'error_log');
        ksort($resultsA);
        for ($i=0; $i < count($resultsA); $i++) {
            if ($resultsA[$i]->seriesComplete_) {
                $days++;
            }
            if ($days == 3) {
                $dayO = new Game($this->_year);
                $dayO->gameNumber_[1] = -1;
                //print($dayO->toString() . "\n");
                array_splice($resultsA, $i+1, 0, array($dayO));
                $days = 0;
            }
        }
        // error_log(print_r($resultsA,true).PHP_EOL,3,'error_log');
        $results = '[';
        foreach ($resultsA as $result) {
            if ($results != '[') {
                $results .= ',';
            }
            $results .= $result->toString();
        }
        $results .= ']';
        $rtn .= '],"results":' . $results;

        $rtn .= '}';
        return $rtn;
    }

    public static function generateSchedules($year = 2017)
    {
        $inst = new self($year);
        //$json = file_get_contents("../data/config.json");
        //$confs = json_decode($json, true);
        //foreach ($confs['years'] as $conf) {
        //  if ($conf['year'] == $year) $_config = $conf;
        //}
        foreach ($inst->_config['teams'] as $key => $team) {
            //print_r($team); print "<br/>";
            $sf = fopen(
                '../data/' . $year . '/' . strtolower($team['team']) . 
                'sched.html', 'w'
            );
            fwrite(
                $sf, "<HTML>\n<HEAD>\n<TITLE>" . $year . " " . $team['city'] . "
                 " . $team['team_name'] . " Schedule</TITLE>\n</HEAD>\n"
            );
            fwrite($sf, "<BODY background=3D\"../../gif/paper1.jpg\">\n");
            fwrite(
                $sf, "<CENTER><H3>" . $year . " " . $team['city'] . " " .
                $team['team_name'] . " Schedule</H3></CENTER>\n"
            );
            fwrite($sf, "<PRE><TT>\n");
            fwrite($sf, "    HOME (51)                           AWAY (51)\n\n");
            $fh=[];
            $fa=[];
            for ($h=0,$a=0;$h<count($inst->_schedules[$team['team']]['home']) 
                || $a<count($inst->_schedules[$team['team']]['away']); $h++,$a++
            ) {
                $home=null;
                $away=null;
                $haway=null;
                $ahome=null;
                $hseason=null;
                $aseason=null;
                if ($h<count($inst->_schedules[$team['team']]['home'])) {
                    $haway = $inst->_schedules[$team['team']]['home'][$h]->away_;
                    $hseason = $inst->_schedules[$team['team']]['home'][$h]->season_;
                }
                if ($a<count($inst->_schedules[$team['team']]['away'])) {
                    $ahome = $inst->_schedules[$team['team']]['away'][$a]->home_;
                    $aseason = $inst->_schedules[$team['team']]['away'][$a]->season_;
                }
                for ($i=0;$i<count($inst->_config['teams']);$i++) {
                    if ($inst->_config['teams'][$i]['team'] == $haway) {
                        $home = $inst->_config['teams'][$i];
                    }
                    if ($inst->_config['teams'][$i]['team'] == $ahome) {
                        $away = $inst->_config['teams'][$i];
                    }
                }
                $fall = false;
                if ($hseason ==  \Scoring\Seasons::FALL) {
                    $a--;
                    array_push($fh, $home);
                    $fall = true;
                }
                if ($aseason ==  \Scoring\Seasons::FALL) {
                    $h--;
                    array_push($fa, $away);
                    $fall = true;
                }
                if ($fall) {
                    continue;
                }
                if ($h<count($inst->_schedules[$team['team']]['home']) 
                    && $a<count($inst->_schedules[$team['team']]['away'])
                ) {
                    $hg = $inst->_schedules[$team['team']]['home'][$h]->games_;
                    $ag = $inst->_schedules[$team['team']]['away'][$a]->games_;
                    if ($hg == 4) {
                        $h++;
                        $hg=7;
                    };
                    if ($ag == 4) {
                        $a++;
                        $ag=7;
                    };
                    fwrite(
                        $sf, sprintf(
                            "%-13s (%d)                    %-13s (%d) \n", 
                            $home['city'], $hg, $away['city'], $ag
                        )
                    );
                }
            }
            fwrite($sf, "\n");
            fwrite($sf, "                      SEPTEMBER\n\n");
            fwrite(
                $sf, sprintf(
                    "%-13s (3)                    %-13s (3) \n",
                    $fh[0]['city'], $fa[0]['city']
                )
            );
            fwrite(
                $sf, sprintf(
                    "%-13s (3)                    %-13s (3) \n",
                    $fh[1]['city'], $fa[1]['city']
                )
            );
            fwrite(
                $sf, sprintf(
                    "%-13s (3)                    %-13s (3) \n", 
                    $fh[2]['city'], $fa[2]['city']
                )
            );

            fwrite($sf, "\n\n");
            fwrite($sf, "</TT></PRE>\n");
            fwrite($sf, "<HR>\n");
            fwrite($sf, "</BODY>\n");
            fwrite($sf, "</HTML>\n");
            fclose($sf);
        }
    }
}
?>
