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
    $g = \Scoring\Game::findGameforTeam($year, $team, $gn);
    //error_log($g->toString().PHP_EOL,3,'error_log');

    if ($eBases != '0' and $play != 'E') {
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
            $g->cs1();
            break;
        case 'PO':
            $g->po1('1-3');
            break;
        case 'DI':
            $g->di();
            break;
        case 'RP-S1':
            if ($g->sit->first != '' and ($fielder == '3' or $fielder = '4')) {
                if ($g->sit->outs == 2) {
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
            if ($g->sit->first != '' or $g->sit->second != '' 
                or $g->sit->third != ''
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
            if ($g->sit->first != '') {
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
            if ($g->sit->first != '') {
                $first=1;
                if ($g->sit->second != '') {
                    $second=1;
                    if ($g->sit->third != '') {
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
            if ($g->sit->outs == 2) {
                $g->fo0($fielder);
            } else {
                if ($g->sit->third != '') {
                    $g->fo($fielder.'-5', -1, 0, 0, -1);
                } else {
                    $g->fo0($fielder);
                }
            }
            break;
        case 'RP-F3':
            if ($g->sit->outs == 2) {
                $g->fo0($fielder);
            } else {
                if ($g->sit->third != '') {
                    $g->fo($fielder.'-5', -1, 0, 0, -1);
                } elseif ($g->sit->second != '') {
                    $g->fo($fielder.'-6', 0, -1, 0, -1);
                } elseif ($g->sit->first != '') {
                    $g->fo($fielder.'-3', 0, 0, -1, -1);
                } else {
                    $g->fo0($fielder);
                }
            }
            break;
        case 'RP-W/S':
            if ($g->sit->first != '' or $g->sit->second != '' 
                or $g->sit->third != ''
            ) {
                $g->wp(1, 2, 2);
            } else {
                $g->kwp();
            }
            break;
        case 'RP-W/G':
            if ($g->sit->first != '' or $g->sit->second != '' 
                or $g->sit->third != ''
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
            if ($g->sit->first != '' or $g->sit->second != '' 
                or $g->sit->third != ''
            ) {
                $first=0;
                $second=0;
                $third=0;
                if ($g->sit->second != '') {
                    if ($g->sit->third == '') {
                        $second = 1;
                        if ($g->sit->first != '') {
                            $first = 1;
                        }
                    }
                } else {
                    if ($g->sit->first != '') {
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
            if ($g->sit->first != '') {
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
            if ($g->sit->outs == 2) {
                $first=0;
                $second=0;
                $third=0;
            } else {
                if ($infield == 'back') {
                    if ($g->sit->first != '' and $g->sit->second == '' 
                        and $g->sit->third == ''
                    ) {
                        if ($fielder == '3') {
                            $p = '3-6-1';
                        } elseif ($fielder == '4') {
                            $p = '4-6-3';
                        } else {
                            $p = $fielder.'-4-3';
                        }
                        $first=-1;
                    } elseif ($g->sit->first == '' and $g->sit->second != '' 
                        and $g->sit->third == ''
                    ) {
                        if ($fielder != '3' and $fielder != '4') {
                            $second = 0;
                        }
                    } elseif ($g->sit->first == '' and $g->sit->second == '' 
                        and $g->sit->third != ''
                    ) {
                        if ($fielder == '3') {
                            $third = 0;
                        } elseif ($fielder != '4' and $fielder != '6') {
                            $third = 0;
                        }
                    } elseif ($g->sit->first != '' and $g->sit->second != '' 
                        and $g->sit->third == ''
                    ) {
                        if ($fielder == '3') {
                            $p = '3-6-1';
                        } elseif ($fielder == '4') {
                            $p = '4-6-3';
                        } else {
                            $p = $fielder.'-4-3';
                        }
                        $first=-1;
                    } elseif ($g->sit->first != '' and $g->sit->second == '' 
                        and $g->sit->third != ''
                    ) {
                        if ($fielder == '3') {
                            $p = '3-6-1';
                        } elseif ($fielder == '4') {
                            $p = '4-6-3';
                        } else {
                            $p = $fielder.'-4-3';
                        }
                        $first=-1;
                        if ($g->sit->outs == 1) {
                            $third = 0;
                        }
                    } elseif ($g->sit->first == '' and $g->sit->second != '' 
                        and $g->sit->third != ''
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
                        if ($g->sit->outs == 1) {
                            $third = 0;
                            $second = 0;
                        }
                    }
                } else {
                    if ($g->sit->first == '' and $g->sit->second == '') {
                        $third = 0;
                    } elseif ($g->sit->first != '' and $g->sit->second == '') {
                        $third = 0;
                    } elseif ($g->sit->first == '' and $g->sit->second != '') {
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
            if ($g->sit->outs == 2) {
                $first=0;
                $second=0;
                $third=0;
            } else {
                if ($infield == 'back') {
                    if ($g->sit->first != '' and $g->sit->second == '' 
                        and $g->sit->third == ''
                    ) {
                        if ($fielder == '3' or $fielder == '4') {
                            $p = $fielder.'-6';
                        } else {
                            $p = $fielder.'-4';
                        }
                        $first=-1;
                        $bat=1;
                    } elseif ($g->sit->first == '' and $g->sit->second != '' 
                        and $g->sit->third == ''
                    ) {
                        if ($fielder != '3' and $fielder != '4') {
                            $second=0;
                        }
                    } elseif ($g->sit->first == '' and $g->sit->second == '' 
                        and $g->sit->third != ''
                    ) {
                        if ($fielder == '3') {
                            $third = 0;
                        } elseif ($fielder != '4' and $fielder != '6') {
                            $third = 0;
                        }
                    } elseif ($g->sit->first != '' and $g->sit->second != '' 
                        and $g->sit->third == ''
                    ) {
                        if ($fielder == '3' or $fielder == '4') {
                            $p = $fielder.'-6';
                        } else {
                            $p = $fielder.'-4';
                        }
                        $first=-1;
                        $bat=1;
                    } elseif ($g->sit->first != '' and $g->sit->second == '' 
                        and $g->sit->third != ''
                    ) {
                        if ($fielder == '3' or $fielder == '4') {
                            $p = $fielder.'-6';
                        } else {
                            $p = $fielder.'-4';
                        }
                        $first=-1;
                        $bat=1;
                    } elseif ($g->sit->first == '' and $g->sit->second != '' 
                        and $g->sit->third != ''
                    ) {
                        if ($fielder == '3') {
                            $third = 0;
                            $second = 0;
                        } elseif ($fielder != '4' and $fielder != '6') {
                            $third = 0;
                            $second = 0;
                        }
                    } else {
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
            if ($g->sit->outs == 2) {
                $first=0;
                $second=0;
                $third=0;
            } else {
                if ($infield != 'back') {
                    if ($g->sit->first == '' and $g->sit->second == '') {
                        $third=0;
                    } elseif ($g->sit->first != '' and $g->sit->second == '') {
                        $third=0;
                    } elseif ($g->sit->first == '' and $g->sit->second != '') {
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
            $g->sac1();
            break;
        case 'flyA':
            if ($g->sit->outs == 2) {
                $g->fo0($fielder);
            } else {
                $g->fo($fielder, 1, 1, 1, -1);
            }
            break;
        case 'flyB':
            if ($g->sit->outs == 2 or $g->sit->third == '') {
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
            if ($g->sit->outs == 2) {
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
    //  for ($i = 0; $i < 2; $i++) {
    //    if ($i == 0) $side = $game->visitor;
    //    else $side = $game->home;
    //error_log(json_encode($side).PHP_EOL,3,'error_log');
    //    $li = $g->lineup_[$i];
    //error_log($li->toString());
    //    for ($j=0; $j < count($side->lineup); $j++) {
    //error_log(print_r($li->getHitters($j),true));
    //error_log(print_r($side->lineup[$j],true));
    //      if (count($li->getHitters($j)) != count($side->lineup[$j])) {
    //error_log('Add ' . $j);
    //        $play = \ProjectScoresheet\Player::initial($side->lineup[$j][count($side->lineup[$j])-1]->player->name,null);
    //error_log('Add ' . $j);
    //        $g->battingOrder($i, $j, $play, \ProjectScoresheet\Position::position($side->lineup[$j][count($side->lineup[$j])-1]->player->positions[0]->position->pos));
    //error_log('Add ' . $j);
    //      } else {
    //        $o = count($li->getHitters($j)) - 1;
    //        if ($o >= 0) {
    //        }
    //      }
    //    }
    //error_log(count($li->getPitchers()));
    //error_log(count($side->rotation));
    //    if (count($li->getPitchers()) != count($side->rotation)) {
    //error_log(json_encode($side->rotation[count($side->rotation)-1]));
    //      $play = \ProjectScoresheet\Player::initial($side->rotation[count($side->rotation)-1]->player->name,null);
    //      $g->pitcher($i,$play);
    //    }
    //  }
    //$g->save();
    \Scoring\Game::save($year, json_decode($g->toString()));
    //error_log($g->toString());
    http_response_code(201);
}
?>

//$year = isset($_GET['year']) ? $_GET['year'] : die();
//if (isset($_GET['team'])) {
  //$team = $_GET['team'];
  //$game = isset($_GET['game']) ? $_GET['game'] : die();
  //if ($g == null) {
    //http_response_code(404);
  //} else {
    //http_response_code(200);
    //print($g->toString());
  //}
//}

