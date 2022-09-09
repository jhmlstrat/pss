<?php
if ($_SERVER['HTTP_X_AUTHORIZATION'] != 'TooManyMLs') {
    return;
}
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// get posted data
//error_log('updateGame'.PHP_EOL,3,'error_log');
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    //error_log('Inside'.PHP_EOL,3,'error_log');
    $foo = json_decode(file_get_contents("php://input"));

    $data = isset($foo->data) ? $foo->data : die();
    //error_log(json_encode($data).PHP_EOL,3,'error_log');

    include_once '../pss/Game.php';
    include_once '../pss/Rosters.php';

    $year = isset($data->year) ? $data->year : die();
    //error_log($year.PHP_EOL,3,'error_log');
    $team = isset($data->team) ? $data->team : die();
    //error_log($team.PHP_EOL,3,'error_log');
    $gn = isset($data->game) ? $data->game : die();
    //error_log($gn.PHP_EOL,3,'error_log');
    $play = isset($data->play) ? $data->play : die();
    //error_log($play.PHP_EOL,3,'error_log');
    $third = isset($data->third) ? $data->third : '#';
    //error_log($third.PHP_EOL,3,'error_log');
    $second = isset($data->second) ? $data->second : '#';
    //error_log($second.PHP_EOL,3,'error_log');
    $first = isset($data->first) ? $data->first : '#';
    //error_log($first.PHP_EOL,3,'error_log');
    $batter = isset($data->batter) ? $data->batter : '#';
    //error_log($batter.PHP_EOL,3,'error_log');
    $fielder = isset($data->fielder) ? $data->fielder : '#';
    //error_log($fielder.PHP_EOL,3,'error_log');
    $infield = isset($data->infield) ? $data->infield : '#';
    //error_log($infield.PHP_EOL,3,'error_log');
    $eBases = isset($data->eBases) ? $data->eBases : '0';
    //error_log($eBases.PHP_EOL,3,'error_log');
    $injury = isset($data->injury) ? $data->injury : '-1';
    //error_log($injury.PHP_EOL,3,'error_log');
    $player = isset($data->player) ? $data->player : '#';
    //error_log($player.PHP_EOL,3,'error_log');
    $pitcher = isset($data->pitcher) ? $data->pitcher : false;
    //error_log(($pitcher?"true":"false").PHP_EOL,3,'error_log');
    $seriesComplete = isset($data->seriesComplete) ? $data->seriesComplete : false;
    //error_log(($seriesComplete?"true":"false").PHP_EOL,3,'error_log');
    $g = \Scoring\Game::findGameforTeam($year, $team, $gn);
    //error_log($g->toString().PHP_EOL,3,'error_log');
    $sit = $g->situation_;
    //error_log($sit->toString().PHP_EOL,3,'error_log');

    if ($injury > -1) {
      $rosters = new \Jhml\Rosters($year, false, false);
      $pt = '';
      if ($pitcher) {
          if ($g->lineup_[0]->getCurrentPitcher()->$name == $player) {
              $g->lineup_[0]->getCurrentPitcher()->injured = true;
              $pt = $g->teamName_[0];
          } else {
              $g->lineup_[1]->getCurrentPitcher()->injured = true;
              $pt = $g->teamName_[1];
          }
      } else {
          $found = false;
          //error_log($g->lineup_[0]->toString().PHP_EOL,3,'error_log');
          for ($i = 0; $i < 2 && ! $found; $i ++) {
              for ($j=0; $j < 9 && ! $found; $j ++) {
                  //error_log($g->lineup_[$i]->getHitter($j)->toString().PHP_EOL,3,'error_log');
                  if ($g->lineup_[$i]->getHitter($j)->name == $player) {
                      $found = true;
                      $g->lineup_[$i]->getHitter($j)->injured = true;
                      $pt = $g->teamName_[$i];
                      //error_log($g->teamName_[$i].PHP_EOL,3,'error_log');
                  }
              }
          }
      }
      $rosters->addInjury($pt, $player, $gn, $injury);
      //error_log($g->toString().PHP_EOL,3,'error_log');
      $rosters->writeRosterFile();
    }

    if ($seriesComplete) {
       $g->situation_->seriesComplete = $seriesComplete;  
    } else if ($batter != '#' || $first != '#' || $second != '#' || $third != '#') {
      if ($first > 0) $first = $first - 1; 
      if ($second > 0) $second = $second - 2; else $second = $second + 1;
      if ($third > 0) $third = $third - 3; else $third = $third + 2;
      if ($play == 'gb') {
        if ($fielder == '3') {
          $g->gb('3', $third, $second, $first, $batter);
        } else {
          $g->gb($fielder + '-3', $third, $second, $first, $batter);
        }
      } else if ($play == 'fly') {
        $g->fo($fielder, $third, $second, $first, $batter);
      } else if ($play == 'lo') {
        //error_log('g->fo:' . $third . '-'. $second . '-' . $first . '-' . $batter);
        $g->fo($fielder, $third, $second, $first, $batter);
      } else if ($play == 'S') {
        $g->play('S', $third, $second, $first, $batter);
      } else if ($play == 'D') {
        $g->play('D', $third, $second, $first, $batter);
      } else if ($play == 'BPHR-') {
        $g->play($fielder.'(bp)', $third, $second, $first, $batter);
      } else if ($play == 'SB') {
        //error_log($g->toString().PHP_EOL, 3, 'error_log');
        if ($eBases != '0') {
          $g->sbe($third + $eBases, $second + $eBases, $first + $eBases);
        } else {
          $g->sb($third, $second, $first);
        }
        //error_log($g->toString().PHP_EOL, 3, 'error_log');
      } else if ($play == 'CS') {
        if ($first < 0) {
          $g->cs('2-6', $third, $second, $first);
        } else if ($second < 0) {
          $g->cs('2-5', $third, $second, $first);
        } else {
          $g->cs('2-1', $third, $second, $first);
        }
      } else if ($play == 'PO') {
        if ($first < 0) {
          $g->po('1-3', $third, $second, $first);
        } else if ($second < 0) {
          $g->po('1-4', $third, $second, $first);
        } else {
          $g->po('1-5', $third, $second, $first);
        }
      } else {
        error_log('Unexpected play with bases: '.$play.PHP_EOL, 3, 'error_log');
      }
    } else if ($eBases != '0' and $play != 'E') {
        switch ($play) {
        case 'S1':
            $g->play('S;'.($eBases != '1' ? $eBases : '').'E'.$fielder, 1, 2, 1+$eBases, 1+$eBases);
            break;
        case 'S2':
            $e = $eBases;
            if ($eBases == 3) $e = 2;
            $g->play('S;'.($eBases != '1' ? $eBases : '').'E'.$fielder, 1, 2, 1+$e, 1+$e);
            break;
        case 'D2':
            $e = $eBases;
            if ($eBases == 2) $e = 1;
            $g->play('D;'.($eBases != '1' ? $eBases : '').'E'.$fielder, 1, 2, 3, 2+$e);
            break;
        case 'D3':
            $e = $eBases;
            if ($eBases == 2) $e = 1;
            $g->play('D;'.($eBases != '1' ? $eBases : '').'E'.$fielder, 1, 2, 3, 2+$e);
            break;
        case 'T3':
            $g->play('T;'.($eBases != '1' ? $eBases : '').'E'.$fielder, 1, 2, 3, 4);
            break;
        default:
            error_log('Unexpected error play: '.$play.PHP_EOL, 3, 'error_log');
        }
    } else {
        switch ($play) {
        case 'undo':
            $g->undo();
            break;
        case 'WP':
            $g->wp1();
            break;
        case 'B':
            $g->bk1();
            break;
        case 'PB':
            $g->pb1();
            break;
        case 'SB':
            $g->sb1();
            break;
        case 'CS':
            if ($sit->runner[1] != null) {
              $g->cs1('2-6');
            } else if ($sit->runner[2] != null) {
              $g->cs1('2-5');
            } else {
              $g->cs1('2-1');
            }
            break;
        case 'PO':
            if ($sit->runner[1] != null) {
              $g->po1('1-3');
            } else if ($sit->runner[2] != null) {
              $g->po1('1-4');
            } else {
              $g->po1('1-5');
            }
            break;
        case 'DI':
            $g->di();
            break;
        case 'RP-S1':
            if ($sit->runner[1] != null and ($fielder == '3' or $fielder = '4')) {
                if ($sit->outs == 2) {
                    $g->s1rp3();
                } else {
                    $g->s1rp();
                }
            } else {
                $g->s1();
            }
            break;
        case 'RP-S2':
            $g->s2rp();
            break;
        case 'RP-D2':
            $g->d2rp();
            break;
        case 'RP-D3':
            if ($sit->runner[1] != null or $sit->runner[2] != null 
                or $sit->runner[3] != null
            ) {
                $g->play('D', 1, 2, 3, 2);
            } else {
                $g->play($fielder.'-3', 0, 0, 0, -1);
            }
            // no break
        case 'RP-T3':
            $g->hr();
            break;
        case 'RP-G1':
            if ($sit->runner[1] != null) {
                if ($fielder == '3') {
                    $g->gb('3-6-1(DP)', 0, 0, -1, -1);
                } elseif ($fielder == '4') {
                    $g->gb('4-6-3(DP)', 0, 0, -1, -1);
                } else {
                    $g->gb($fielder + '-4-3(DP)', 0, 0, -1, -1);
                }
            } else {
                if ($fielder == '3') {
                    $g->gb('3', 0, 0, 0, -1);
                } else {
                    $g->gb($fielder + '-3', 0, 0, 0, -1);
                }
            }
            break;
        case 'RP-G2':
            if ($fielder == '1' or $fielder == '3') {
                $p='1-3';
            } else {
                $p='1-'.$fielder.'-3';
            }
            $first=0;
            $second=0;
            $third=0;
            if ($sit->runner[1] != null) {
                $first=1;
                if ($sit->runner[2] != null) {
                    $second=1;
                    if ($sit->runner[3] != null) {
                        $third=1;
                    }
                }
            }
            $g->gb($p, $third, $second, $first, -1);
            break;
        case 'RP-G3':
            if ($fielder == '3') {
                $g->gb1('3');
            } else {
                $g->gb1($fielder + '-3');
            }
            break;
        case 'RP-F1':
            $g->fo($fielder, 1, 2, 2, -1);
            break;
        case 'RP-F2':
            if ($sit->outs == 2) {
                $g->fo0($fielder);
            } else {
                if ($sit->runner[3] != null) {
                    $g->fo($fielder.'-5', -1, 0, 0, -1);
                } else {
                    $g->fo0($fielder);
                }
            }
            break;
        case 'RP-F3':
            if ($sit->outs == 2) {
                $g->fo0($fielder);
            } else {
                if ($sit->runner[3] != null) {
                    $g->fo($fielder.'-5', -1, 0, 0, -1);
                } elseif ($sit->runner[2] != null) {
                    $g->fo($fielder.'-6', 0, -1, 0, -1);
                } elseif ($sit->runner[1] != null) {
                    $g->fo($fielder.'-3', 0, 0, -1, -1);
                } else {
                    $g->fo0($fielder);
                }
            }
            break;
        case 'RP-W/S':
            if ($sit->runner[1] != null or $sit->runner[2] != null 
                or $sit->runner[3] != null
            ) {
                $g->wp(1, 2, 2);
            } else {
                $g->kwp();
            }
            break;
        case 'RP-W/G':
            if ($sit->runner[1] != null or $sit->runner[2] != null 
                or $sit->runner[3] != null
            ) {
                $g->wp1();
            } else {
                $g->kwp();
            }
            break;
        case 'RP-P/F':
            $g->ci();
            break;
        case 'RP-P/P':
            if ($sit->runner[1] != null or $sit->runner[2] != null 
                or $sit->runner[3] != null
            ) {
                $first=0;
                $second=0;
                $third=0;
                if ($sit->runner[2] != null) {
                    if ($sit->runner[3] == null) {
                        $second = 1;
                        if ($sit->runner[1] != null) {
                            $first = 1;
                        }
                    }
                } else {
                    if ($sit->runner[1] != null) {
                        $first = 1;
                    }
                }
                if ($first+$second+$third > 0) {
                    $g->pb($third, $second, $first);
                }
            } else {
                $g->kpb();
            }
            break;
        case 'RP-PO':
            $g->s1();
            break;
        case 'RP-FO':
            if ($sit->runner[1] != null) {
                $g->po('2-3', 0, 0, -1);
            } else {
                $g->fo0('2');
            }
            break;
        case 'gbA':
            if ($fielder == '3') {
                $p='3';
            } else {
                $p=$fielder.'-3';
            }
            $bat=-1;
            $first=1;
            $second=1;
            $third=1;
            if ($sit->outs == 2) {
                $first=0;
                $second=0;
                $third=0;
            } else {
                if ($infield == 'back') {
                    if ($sit->runner[1] != null and $sit->runner[2] == null 
                        and $sit->runner[3] == null
                    ) {
                        if ($fielder == '3') {
                            $p = '3-6-1';
                        } elseif ($fielder == '4') {
                            $p = '4-6-3';
                        } else {
                            $p = $fielder.'-4-3';
                        }
                        $first=-1;
                    } elseif ($sit->runner[1] == null and $sit->runner[2] != null 
                        and $sit->runner[3] == null
                    ) {
                        if ($fielder != '3' and $fielder != '4') {
                            $second = 0;
                        }
                    } elseif ($sit->runner[1] == null and $sit->runner[2] == null 
                        and $sit->runner[3] != null
                    ) {
                        if ($fielder == '3') {
                            $third = 0;
                        } elseif ($fielder != '4' and $fielder != '6') {
                            $third = 0;
                        }
                    } elseif ($sit->runner[1] != null and $sit->runner[2] != null 
                        and $sit->runner[3] == null
                    ) {
                        if ($fielder == '3') {
                            $p = '3-6-1';
                        } elseif ($fielder == '4') {
                            $p = '4-6-3';
                        } else {
                            $p = $fielder.'-4-3';
                        }
                        $first=-1;
                    } elseif ($sit->runner[1] != null and $sit->runner[2] == null 
                        and $sit->runner[3] != null
                    ) {
                        if ($fielder == '3') {
                            $p = '3-6-1';
                        } elseif ($fielder == '4') {
                            $p = '4-6-3';
                        } else {
                            $p = $fielder.'-4-3';
                        }
                        $first=-1;
                        if ($sit->outs == 1) {
                            $third = 0;
                        }
                    } elseif ($sit->runner[1] == null and $sit->runner[2] != null 
                        and $sit->runner[3] != null
                    ) {
                        if ($fielder == '3') {
                            $third = 0;
                            $second = 0;
                        } elseif ($fielder != '4' and $fielder != '6') {
                            $third = 0;
                            $second = 0;
                        }
                    } else {
                        if ($fielder == '3') {
                            $p = '3-6-1';
                        } elseif ($fielder == '4') {
                            $p = '4-6-3';
                        } else {
                            $p = $fielder.'-4-3';
                        }
                        $first=-1;
                        if ($sit->outs == 1) {
                            $third = 0;
                            $second = 0;
                        }
                    }
                } else {
                    if ($sit->runner[1] == null and $sit->runner[2] == null) {
                        $third = 0;
                    } elseif ($sit->runner[1] != null and $sit->runner[2] == null) {
                        $third = 0;
                    } elseif ($sit->runner[1] == null and $sit->runner[2] != null) {
                        $third = 0;
                        $second = 0;
                    } else {
                        if ($fielder == '2') {
                            $p='2;2-3';
                        } else {
                            $p = $fielder + '-2-3';
                        }
                        $third=-1;
                    }
                }
            }
            $g->gb($p, $third, $second, $first, $bat);
            break;
        case 'gbB':
            if ($fielder == '3') {
                $p='3';
            } else {
                $p=$fielder.'-3';
            }
            $bat=-1;
            $first=1;
            $second=1;
            $third=1;
            if ($sit->outs == 2) {
                $first=0;
                $second=0;
                $third=0;
            } else {
                if ($infield == 'back') {
                    if ($sit->runner[1] != null and $sit->runner[2] == null 
                        and $sit->runner[3] == null
                    ) {
                        if ($fielder == '3' or $fielder == '4') {
                            $p = $fielder.'-6';
                        } else {
                            $p = $fielder.'-4';
                        }
                        $first=-1;
                        $bat=1;
                    } elseif ($sit->runner[1] == null and $sit->runner[2] != null 
                        and $sit->runner[3] == null
                    ) {
                        if ($fielder != '3' and $fielder != '4') {
                            $second=0;
                        }
                    } elseif ($sit->runner[1] == null and $sit->runner[2] == null 
                        and $sit->runner[3] != null
                    ) {
                        if ($fielder == '3') {
                            $third = 0;
                        } elseif ($fielder != '4' and $fielder != '6') {
                            $third = 0;
                        }
                    } elseif ($sit->runner[1] != null and $sit->runner[2] != null 
                        and $sit->runner[3] == null
                    ) {
                        if ($fielder == '3' or $fielder == '4') {
                            $p = $fielder.'-6';
                        } else {
                            $p = $fielder.'-4';
                        }
                        $first=-1;
                        $bat=1;
                    } elseif ($sit->runner[1] != null and $sit->runner[2] == null 
                        and $sit->runner[3] != null
                    ) {
                        if ($fielder == '3' or $fielder == '4') {
                            $p = $fielder.'-6';
                        } else {
                            $p = $fielder.'-4';
                        }
                        $first=-1;
                        $bat=1;
                    } elseif ($sit->runner[1] == null and $sit->runner[2] != null 
                        and $sit->runner[3] != null
                    ) {
                        if ($fielder == '3') {
                            $third = 0;
                            $second = 0;
                        } elseif ($fielder != '4' and $fielder != '6') {
                            $third = 0;
                            $second = 0;
                        }
                    } elseif ($sit->runner[1] != null and $sit->runner[2] != null
                        and $sit->runner[3] != null
                    ) {
                        if ($fielder == '3' or $fielder == '4') {
                            $p = $fielder.'-6';
                        } else {
                            $p = $fielder.'-4';
                        }
                        $first=-1;
                        $bat=1;
                    }
                } else {
                    if ($fielder == '2') {
                        $p='2';
                    } else {
                        $p = $fielder.'-2';
                    }
                    $third=-1;
                    $bat=1;
                }
            }
            $g->gb($p, $third, $second, $first, $bat);
            break;
        case 'gbC':
            if ($fielder == '3') {
                $p='3-1';
            } else {
                $p=$fielder.'-3';
            }
            $bat=-1;
            $first=1;
            $second=1;
            $third=1;
            if ($sit->outs == 2) {
                $first=0;
                $second=0;
                $third=0;
            } else {
                if ($infield != 'back') {
                    if ($sit->runner[1] == null and $sit->runner[2] == null) {
                        $third=0;
                    } elseif ($sit->runner[1] != null and $sit->runner[2] == null) {
                        $third=0;
                    } elseif ($sit->runner[1] == null and $sit->runner[2] != null) {
                        $third=0;
                        $second=0;
                    } else {
                        if ($fielder == '2') {
                            $p='2';
                        } else {
                            $p = $fielder.'-2';
                        }
                        $third=-1;
                        $bat=1;
                    }
                }
            }
            $g->gb($p, $third, $second, $first, $bat);
            break;
        case 'SAC':
            if ($fielder == '3') {
                $p='SAC(3-4)';
            } else {
                $p='SAC('.$fielder.'-3)';
            }
            $g->sac1($p);
            break;
        case 'flyA':
            if ($sit->outs == 2) {
                $g->fo0($fielder);
            } else {
                $g->fo($fielder, 1, 1, 1, -1);
            }
            break;
        case 'flyB':
            if ($sit->outs == 2 or $sit->runner[3] == null) {
                $g->fo0($fielder);
            } else {
                $g->fo($fielder, 1, 0, 0, -1);
            }
            break;
        case 'flyC':
            $g->fo0($fielder);
            break;
        case 'lo':
            $g->fo0($fielder);
            break;
        case 'po':
            $g->fo0($fielder);
            break;
        case 'fo':
            $g->fo0($fielder);
            break;
        case 'K':
            $g->k();
            break;
        case 'S*':
            $g->s1();
            break;
        case 'S**':
            $g->s2();
            break;
        case 'S-dot':
            $g->play('S(dot)', 1, 2, 2, 1);
            break;
        case 'S+':
            $g->play('S(plus)', 1, 2, 2, 1);
            break;
        case 'S#':
            $g->play('S(hash)', 1, 2, 2, 1);
            break;
        case 'BPS+':
            $g->play('S(bp)', 1, 1, 1, 1);
            break;
        case 'BPS-':
            $g->play($fielder.'(bp)', 0, 0, 0, -1);
            break;
        case 'D**':
            $g->d2();
            break;
        case 'D***':
            $g->d3();
            break;
        case 'T':
            $g->t();
            break;
        case 'HR':
            $g->hr();
            break;
        case 'BPHR+':
            $g->play('HR(bp)', 1, 2, 3, 4);
            break;
        case 'BPHR-':
            if ($sit->outs == 2) {
                $g->play($fielder.'(bp)', 0, 0, 0, -1);
            } else {
                $g->play($fielder.'(bp)', 1, 0, 0, -1);
            }
            break;
        case 'BB':
            $g->bb();
            break;
        case 'HBP':
            $g->hbp();
            break;
        case 'E':
            $g->play(
                ($eBases != '1' ? $eBases : '').'E'.$fielder, 
                $eBases, $eBases, $eBases, $eBases
            );
            break;
        default:
            error_log('Unexpected play: '.$play.PHP_EOL, 3, 'error_log');
        }
    }
    //error_log($g->toString().PHP_EOL, 3, 'error_log');
    \Scoring\Game::save($year, json_decode($g->toString()));
    //error_log($g->toString());
    http_response_code(201);
    
}
?>

