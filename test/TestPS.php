<?php
namespace ProjectScoresheet;

require_once '../pss/ProjectScoresheet.php';
class TestPS
{
    public static function checkAssert($bool, $number=0)
    {
        if (! $bool) {
            print "Error " . $number . " - Assertion failed\n";
            //exit;
            throw new \Exception("Error " . $number . " - Assertion failed");
        }
    }
    public static function checkSituation(
        $ps,
        // TBD Add batter and pitcher
        $inning,
        $side,
        $outs,
        $awayRuns,
        $homeRuns,
        $awayHits,
        $homeHits,
        $awayErrors,
        $homeErrors,
        $third,
        $second,
        $first,
        $betweenInnings,
        $number,
        $batter=null,
        $pitcher=null,
        $gameOver=false
    ) {
        $sit=$ps->getSituation();
        $rtn = true;
        if ($inning !== $sit->inning) {
            print "Error " . $number . ".0 - expected=" . $inning . 
                ",found=" . $sit->inning . "\n";
            $rtn = false;
        }
        if ($side !== $sit->side) {
            print "Error " . $number . ".1 - expected=" . $side . 
                ",found=" .$sit->side."\n";
            $rtn = false;
        }
        if ($outs !== $sit->outs) {
            print "Error " . $number . ".2 - expected=" . $outs . 
                ",found=" .$sit->outs."\n";
            $rtn = false;
        }
        if ($awayRuns !== $sit->runs[0]) {
            print "Error " . $number . ".3 - expected=" . $awayRuns . 
                ",found=" .$sit->runs[0]."\n";
            $rtn = false;
        }
        if ($homeRuns !== $sit->runs[1]) {
            print "Error " . $number . ".4 - expected=" . $homeRuns . 
                ",found=" .$sit->runs[1]."\n";
            $rtn = false;
        }
        if ($awayHits !== $sit->hits[0]) {
            print "Error " . $number . ".5 - expected=" . $awayHits . 
                ",found=" .$sit->hits[0]."\n";
            $rtn = false;
        }
        if ($homeHits !== $sit->hits[1]) {
            print "Error " . $number . ".6 - expected=" . $homeHits . 
                ",found=" .$sit->hits[1]."\n";
            $rtn = false;
        }
        if ($awayErrors !== $sit->errors[0]) {
            print "Error " . $number . ".7 - expected=" . $awayErrors . 
                ",found=" .$sit->errors[0]."\n";
            $rtn = false;
        }
        if ($homeErrors !== $sit->errors[1]) {
            print "Error " . $number . ".8 - expected=" . $homeErrors . 
                ",found=" .$sit->errors[1]."\n";
            $rtn = false;
        }
        if ($first == null) {
            if ($sit->runner[1] != null) {
                print "Error " . $number . ".9\n";
                $rtn = false;
            }
        } else {
            if ($sit->runner[1] == null) {
                print "Error " . $number . ".10\n";
                $rtn = false;
            } elseif ($first->name !== $sit->runner[1]->name) {
                print "Error " . $number . ".11\n";
                $rtn = false;
            }
        }
        if ($second == null) {
            if ($sit->runner[2] != null) {
                print "Error " . $number . ".12\n";
                $rtn = false;
            }
        } else {
            if ($sit->runner[2] == null) {
                print "Error " . $number . ".13\n";
                $rtn = false;
            } elseif ($second->name !== $sit->runner[2]->name) {
                print "Error " . $number . ".14\n";
                $rtn = false;
            }
        }
        if ($third == null) {
            if ($sit->runner[3] != null) {
                print "Error " . $number . ".15\n";
                $rtn = false;
            }
        } else {
            if ($sit->runner[3] == null) {
                print "Error " . $number . ".16\n";
                $rtn = false;
            } elseif ($third->name !== $sit->runner[3]->name) {
                print "Error " . $number . ".17\n";
                $rtn = false;
            }
        }
        if ($betweenInnings !== $sit->betweenInnings) {
            print "Error " . $number . ".18 - expected=" . $betweenInnings . 
                ",found=" .$sit->betweenInnings."\n";
            $rtn = false;
        }
        if ($batter == null) {
            if ($sit->batter != null) {
                print "Error " . $number . ".19\n";
                $rtn = false;
            }
        } else {
            //print $batter->name . "\t" . $sit->batter->name .  "\t" . 
            //  count($ps->results_[0]).  "\t" . count($ps->results_[1]). "\n";
            //print_r($ps->results_[0]);
            if ($sit->batter == null) {
                print "Error " . $number . ".20\n";
                $rtn = false;
            } elseif ($batter->name !== $sit->batter->name) {
                print "Error " . $number . ".21\n";
                $rtn = false;
            }
        }
        if ($pitcher == null) {
            if ($sit->pitcher != null) {
                //print_r($sit->pitcher,true);
                print "Error " . $number . ".22\n";
                $rtn = false;
            }
        } else {
            if ($sit->pitcher == null) {
                print "Error " . $number . ".23\n";
                $rtn = false;
            } elseif ($pitcher->name !== $sit->pitcher->name) {
                print "Error " . $number . ".24\n";
                $rtn = false;
            }
        }
 
        if ($gameOver !== $sit->gameOver()) {
            print "Error " . $number . ".25 - expected=" . $gameOver . 
                ",found=" .$sit->gameOver()."\n";
            $rtn = false;
        }
        if (! $rtn) {
            print_r($sit);
            exit;
        }
    }

    public static function checkExpected($ps, $expected, $visitor, $home, $number)
    {
        if ($ps->toString() !== $expected) {
            print "Error " . $number . ".0\n";
            print $ps->toString() . "\n";
            print $expected . "\n";
            exit;
        }
        if ($ps->lineupValid(Side::Visitor) != $visitor) {
            print "Error " . $number . ".1\n";
            exit;
        }
        if ($ps->lineupValid(Side::Home) != $home) {
            print "Error " . $number . ".2\n";
            exit;
        }
    }
}
$ps = new ProjectScoresheet;
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"","gameNumber":"0","lineup":[[],[],[],[],[],[],[],' .
         '[],[]],"rotation":[],"roster":[],"results":[]},"home":{"name":"",' .
         '"gameNumber":"0","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"situation":{"situation":{"outs":"0","runsV":' .
         '"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0",' .
         '"inning":"1","side":"0","first":"","second":"","third":"","batter":""' .
         ',"pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 1
);
$ps->assignTeam(Side::Visitor, 'Pittsburgh', '001');
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[],[],[],' .
         '[],[],[],[],[],[]],"rotation":[],"roster":[],"results":[]},"home":{' .
         '"name":"","gameNumber":"0","lineup":[[],[],[],[],[],[],[],[],[]],' .
         '"rotation":[],"roster":[],"results":[]},"situation":{"situation":{' .
         '"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":' .
         '"0","errorsH":"0","inning":"1","side":"0","first":"","second":"",' .
         '"third":"","batter":"","pitcher":"","betweenInnings":true,"gameOver":' .
         'false}}}', 
    false, false, 2
);
$ps->assignTeam(Side::Home, 'San Diego', '001');
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[],[],[],' .
         '[],[],[],[],[],[]],"rotation":[],"roster":[],"results":[]},"home":{' .
         '"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[]' .
         ',[]],"rotation":[],"roster":[],"results":[]},"situation":{"situation":{' .
         '"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0"' .
         ',"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":""' .
         ',"batter":"","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 3
);
$play = array();
$play[Side::Visitor] = array();
$play[Side::Visitor][0] = Player::initial('Morgan', null);
$ps->battingOrder(
    Side::Visitor, 0, $play[Side::Visitor][0], Position::position('RF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001",' .
         '"lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"roster":[],' .
         '"results":[]},"situation":{"situation":{"outs":"0","runsV":"0","runsH":' .
         '"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0","inning":"1",' .
         '"side":"0","first":"","second":"","third":"","batter":"Morgan","pitcher"' .
         ':"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 4
);
$play[Side::Visitor][1] = Player::initial('McCutchen', null);
$ps->battingOrder(
    Side::Visitor, 1, $play[Side::Visitor][1], Position::position('CF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[],[],[],[],[],[],[]],"rotation":[],"roster":[],"results":[]}' .
         ',"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],' .
         '[],[],[],[]],"rotation":[],"roster":[],"results":[]},"situation":{' .
         '"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0",' .
         '"errorsV":"0","errorsH":"0","inning":"1","side":"0","first":"","second":' .
         '"","third":"","batter":"Morgan","pitcher":"","betweenInnings":true,' .
         '"gameOver":false}}}', 
    false, false, 5
);
$play[Side::Visitor][2] = Player::initial('Betemit', null);
$ps->battingOrder(
    Side::Visitor, 2, $play[Side::Visitor][2], Position::position('B3')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],' .
         '[],[],[]],"rotation":[],"roster":[],"results":[]},"home":{"name":' .
         '"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],' .
         '"rotation":[],"roster":[],"results":[]},"situation":{"situation":{"outs"' .
         ':"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0",' .
         '"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":"",' .
         '"batter":"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 6
);
$play[Side::Visitor][3] = Player::initial('Duda', null);
$ps->battingOrder(
    Side::Visitor, 3, $play[Side::Visitor][3], Position::position('B1')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1"' .
         ',"when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001"' .
         ',"lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"roster":[],' .
         '"results":[]},"situation":{"situation":{"outs":"0","runsV":"0","runsH"' .
         ':"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0","inning":"1",' .
         '"side":"0","first":"","second":"","third":"","batter":"Morgan","pitcher"' .
         ':"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 7
);
$play[Side::Visitor][4] = Player::initial('Willingham', null);
$ps->battingOrder(
    Side::Visitor, 4, $play[Side::Visitor][4], Position::position('DH')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home"' .
         ':"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[],[],[],[]],"rotation":[],"roster":[],"results":[]},' .
         '"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],' .
         '[],[],[],[]],"rotation":[],"roster":[],"results":[]},"situation":{' .
         '"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0",' .
         '"errorsV":"0","errorsH":"0","inning":"1","side":"0","first":"","second":' .
         '"","third":"","batter":"Morgan","pitcher":"","betweenInnings":true,' .
         '"gameOver":false}}}', 
    false, false, 8
);
$play[Side::Visitor][5] = Player::initial('Tabata', null);
$ps->battingOrder(
    Side::Visitor, 5, $play[Side::Visitor][5], Position::position('LF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1"' .
         ',"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham"' .
         ',"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[]]' .
         ',"rotation":[],"roster":[],"results":[]},"home":{"name":"San Diego",' .
         '"gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"situation":{"situation":{"outs":"0","runsV":' .
         '"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0",' .
         '"inning":"1","side":"0","first":"","second":"","third":"","batter":' .
         '"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 9
);
$play[Side::Visitor][6] = Player::initial('Scutaro', null);
$ps->battingOrder(
    Side::Visitor, 6, $play[Side::Visitor][6], Position::position('SS')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1"' .
         ',"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham"' .
         ',"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001"' .
         ',"lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"roster":[],' .
         '"results":[]},"situation":{"situation":{"outs":"0","runsV":"0","runsH"' .
         ':"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0","inning":"1",' .
         '"side":"0","first":"","second":"","third":"","batter":"Morgan","pitcher"' .
         ':"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 10
);
$play[Side::Visitor][7] = Player::initial('Thole', null);
$ps->battingOrder(
    Side::Visitor, 7, $play[Side::Visitor][7], Position::position('C')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1"' .
         ',"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham"' .
         ',"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[]],"rotation":[],"roster":[],"results":[]},"home":{' .
         '"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],' .
         '[]],"rotation":[],"roster":[],"results":[]},"situation":{"situation":{' .
         '"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0"' .
         ',"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":""' .
         ',"batter":"Morgan","pitcher":"","betweenInnings":true,"gameOver":' .
         'false}}}', 
    false, false, 11
);
$play[Side::Visitor][8] = Player::initial('Walker', null);
$ps->battingOrder(
    Side::Visitor, 8, $play[Side::Visitor][8], Position::position('B2')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole"' .
         ',"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[],"roster":[],"results":[]},"home":{"name":"San Diego",' .
         '"gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"situation":{"situation":{"outs":"0","runsV":' .
         '"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0",' .
         '"inning":"1","side":"0","first":"","second":"","third":"","batter":' .
         '"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 12
);
$play[Side::Visitor][9] = Player::initial('Hudson', null);
$ps->pitcher(Side::Visitor, $play[Side::Visitor][9]);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[],[],[],[],[],[],[],[],[]],"rotation":[],"roster":[],"results":[]},' .
         '"situation":{"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0"' .
         ',"hitsH":"0","errorsV":"0","errorsH":"0","inning":"1","side":"0","first"' .
         ':"","second":"","third":"","batter":"Morgan","pitcher":"",' .
         '"betweenInnings":true,"gameOver":false}}}', 
    true, false, 13
);

$play[Side::Home] = array();
$play[Side::Home][0] = Player::initial('Bourne', null);
$ps->battingOrder(
    Side::Home, 0, $play[Side::Home][0], Position::position('RF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1"' .
         ',"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham"' .
         ',"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[],[]],' .
         '"rotation":[],"roster":[],"results":[]},"situation":{"situation":{"outs"' .
         ':"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0",' .
         '"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":"",' .
         '"batter":"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    true, false, 14
);
$play[Side::Home][1] = Player::initial('Jay', null);
$ps->battingOrder(
    Side::Home, 1, $play[Side::Home][1], Position::position('CF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[],[],[],[],[],[],[]],"rotation":[],"roster":[],' .
         '"results":[]},"situation":{"situation":{"outs":"0","runsV":"0","runsH":' .
         '"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0","inning":"1",' .
         '"side":"0","first":"","second":"","third":"","batter":"Morgan","pitcher"' .
         ':"","betweenInnings":true,"gameOver":false}}}', 
    true, false, 15
);
$play[Side::Home][2] = Player::initial('Murphy', null);
$ps->battingOrder(
    Side::Home, 2, $play[Side::Home][2], Position::position('B3')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],' .
         '[],[],[]],"rotation":[],"roster":[],"results":[]},"situation":{' .
         '"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0",' .
         '"errorsV":"0","errorsH":"0","inning":"1","side":"0","first":"","second":' .
         '"","third":"","batter":"Morgan","pitcher":"","betweenInnings":true,' .
         '"gameOver":false}}}', 
    true, false, 16
);
$play[Side::Home][3] = Player::initial('Holliday', null);
$ps->battingOrder(
    Side::Home, 3, $play[Side::Home][3], Position::position('B1')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[]],"rotation":' .
         '[],"roster":[],"results":[]},"situation":{"situation":{"outs":"0",' .
         '"runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":' .
         '"0","inning":"1","side":"0","first":"","second":"","third":"","batter":' .
         '"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    true, false, 17
);
$play[Side::Home][4] = Player::initial('Young', null);
$ps->battingOrder(
    Side::Home, 4, $play[Side::Home][4], Position::position('LF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age"' .
         ':"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[],[],[],[]],"rotation":[],"roster":[],"results":[]},' .
         '"situation":{"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0"' .
         ',"hitsH":"0","errorsV":"0","errorsH":"0","inning":"1","side":"0","first"' .
         ':"","second":"","third":"","batter":"Morgan","pitcher":"",' .
         '"betweenInnings":true,"gameOver":false}}}', 
    true, false, 18
);
$play[Side::Home][5] = Player::initial('Abreu', null);
$ps->battingOrder(
    Side::Home, 5, $play[Side::Home][5], Position::position('DH')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Abreu","age":"99","positions":[{' .
         '"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[],[],[]]' .
         ',"rotation":[],"roster":[],"results":[]},"situation":{"situation":{' .
         '"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0"' .
         ',"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":""' .
         ',"batter":"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}',
    true, false, 19
);
$play[Side::Home][6] = Player::initial('Escobar', null);
$ps->battingOrder(
    Side::Home, 6, $play[Side::Home][6], Position::position('SS')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Abreu","age":"99","positions":[{' .
         '"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Escobar","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"situation":{"situation":{"outs":"0","runsV":' .
         '"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0",' .
         '"inning":"1","side":"0","first":"","second":"","third":"","batter":' .
         '"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    true, false, 20
);
$play[Side::Home][7] = Player::initial('Buck', null);
$ps->battingOrder(
    Side::Home, 7, $play[Side::Home][7], Position::position('C')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Abreu","age":"99","positions":[{' .
         '"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Escobar","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[]],"rotation":[],"roster":[],"results":[]},' .
         '"situation":{"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0"' .
         ',"hitsH":"0","errorsV":"0","errorsH":"0","inning":"1","side":"0","first"' .
         ':"","second":"","third":"","batter":"Morgan","pitcher":"",' .
         '"betweenInnings":true,"gameOver":false}}}', 
    true, false, 21
);
$play[Side::Home][8] = Player::initial('Carroll', null);
$ps->battingOrder(
    Side::Home, 8, $play[Side::Home][8], Position::position('B2')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Abreu","age":"99","positions":[{' .
         '"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Escobar","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Carroll","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[],"roster":[],"results":[]},"situation":{"situation":{"outs"' .
         ':"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0",' .
         '"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":"",' .
         '"batter":"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    true, false, 22
);
$play[Side::Home][9] = Player::initial('Vazquez', null);
$ps->pitcher(Side::Home, $play[Side::Home][9]);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Abreu","age":"99","positions":[{' .
         '"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Escobar","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Carroll","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Vazquez","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"situation":{"situation":{"outs":"0","runsV":"0",' .
         '"runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0",' .
         '"inning":"1","side":"0","first":"","second":"","third":"","batter":' .
         '"Morgan","pitcher":"Vazquez","betweenInnings":true,"gameOver":false}}}', 
    true, true, 23
);
$ps->start();
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 0, 0, 0, 0, 0, 0, 0, 
    null, null, null, true, 24, $play[Side::Visitor][0], $play[Side::Home][9]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 0, 0, 0, 1, 0, 0, 0, 
    null, null, $play[Side::Visitor][0], false, 25, 
    $play[Side::Visitor][1], $play[Side::Home][9]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 0, 0, 0, 2, 0, 0, 0, 
    null, $play[Side::Visitor][0], $play[Side::Visitor][1], 
    false, 26, $play[Side::Visitor][2], $play[Side::Home][9]
);
$ps->k();
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 1, 0, 0, 2, 0, 0, 0, 
    null, $play[Side::Visitor][0], $play[Side::Visitor][1], 
    false, 27, $play[Side::Visitor][3], $play[Side::Home][9]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 1, 0, 0, 3, 0, 0, 0, 
    $play[Side::Visitor][0], $play[Side::Visitor][1], $play[Side::Visitor][3], 
    false, 28, $play[Side::Visitor][4], $play[Side::Home][9]
);
$ps->fo('9', 1, 0, 0, -1);
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 2, 1, 0, 3, 0, 0, 0, 
    null, $play[Side::Visitor][1], $play[Side::Visitor][3], false, 29, 
    $play[Side::Visitor][5], $play[Side::Home][9]
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 1, Side::Home, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 30, $play[Side::Home][0], $play[Side::Visitor][9]
);
$ps->gb1('43');
TestPS::checkSituation(
    $ps, 1, Side::Home, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 31, $play[Side::Home][1], $play[Side::Visitor][9]
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 1, Side::Home, 2, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 32, $play[Side::Home][2], $play[Side::Visitor][9]
);
$ps->bb();
TestPS::checkSituation(
    $ps, 1, Side::Home, 2, 1, 0, 3, 0, 0, 0, 
    null, null, $play[Side::Home][2], false, 33, $play[Side::Home][3], 
    $play[Side::Visitor][9]
);
$ps->po1('13');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 34, $play[Side::Visitor][6], $play[Side::Home][9]
);
// 2
$ps->fo0('5');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 35, $play[Side::Visitor][7], $play[Side::Home][9]
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 2, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 36, $play[Side::Visitor][8], $play[Side::Home][9]
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 2, Side::Home, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 37, $play[Side::Home][3], $play[Side::Visitor][9]
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 38, $play[Side::Home][4], $play[Side::Visitor][9]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 1, 0, 0, 
    null, null, $play[Side::Home][4], false, 39, $play[Side::Home][5], 
    $play[Side::Visitor][9]
);
$ps->cs1('2-6');
TestPS::checkSituation(
    $ps, 2, Side::Home, 2, 1, 0, 3, 1, 0, 0, 
    null, null, null, false, 40, $play[Side::Home][5], $play[Side::Visitor][9]
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 1, 0, 0, 
    null, null, $play[Side::Home][4], false, 41, $play[Side::Home][5], 
    $play[Side::Visitor][9]
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 42, $play[Side::Home][4], $play[Side::Visitor][9]
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Home, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 43, $play[Side::Home][3], $play[Side::Visitor][9]
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 2, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 44, $play[Side::Visitor][8], $play[Side::Home][9]
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 45, $play[Side::Visitor][7], $play[Side::Home][9]
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 46, $play[Side::Visitor][6], $play[Side::Home][9]
);
$ps->undo();
TestPS::checkSituation(
    $ps, 1, Side::Home, 2, 1, 0, 3, 0, 0, 0, 
    null, null, $play[Side::Home][2], false, 47, $play[Side::Home][3], 
    $play[Side::Visitor][9]
);
$ps->undo();
TestPS::checkSituation(
    $ps, 1, Side::Home, 2, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 48, $play[Side::Home][2], $play[Side::Visitor][9]
);
$ps->bb();
TestPS::checkSituation(
    $ps, 1, Side::Home, 2, 1, 0, 3, 0, 0, 0, 
    null, null, $play[Side::Home][2], false, 49, $play[Side::Home][3], 
    $play[Side::Visitor][9]
);
$ps->po1('13');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 50, $play[Side::Visitor][6], $play[Side::Home][9]
);
// 2 again
$ps->fo0('5');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 51, $play[Side::Visitor][7], $play[Side::Home][9]
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 2, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 52, $play[Side::Visitor][8], $play[Side::Home][9]
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 2, Side::Home, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 53, $play[Side::Home][3], $play[Side::Visitor][9]
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 54, $play[Side::Home][4], $play[Side::Visitor][9]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 1, 0, 0, 
    null, null, $play[Side::Home][4], false, 55, $play[Side::Home][5], 
    $play[Side::Visitor][9]
);
$ps->cs1('2-6');
TestPS::checkSituation(
    $ps, 2, Side::Home, 2, 1, 0, 3, 1, 0, 0, 
    null, null, null, false, 56, $play[Side::Home][5], $play[Side::Visitor][9]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 2, Side::Home, 2, 1, 0, 3, 2, 0, 0, 
    null, null, $play[Side::Home][5], false, 57, $play[Side::Home][6], 
    $play[Side::Visitor][9]
);
$ps->s(0, 0, 2, 2);
TestPS::checkSituation(
    $ps, 2, Side::Home, 2, 1, 0, 3, 3, 0, 0, 
    $play[Side::Home][5], $play[Side::Home][6], null, false, 58, 
    $play[Side::Home][7], $play[Side::Visitor][9]
);
$ps->k();
TestPS::checkSituation(
    $ps, 3, Side::Visitor, 0, 1, 0, 3, 3, 0, 0, 
    null, null, null, true, 59, $play[Side::Visitor][0], $play[Side::Home][9]
);
// 3
$ps->k();
TestPS::checkSituation(
    $ps, 3, Side::Visitor, 1, 1, 0, 3, 3, 0, 0, 
    null, null, null, false, 60, $play[Side::Visitor][1], $play[Side::Home][9]
);
$ps->bb();
TestPS::checkSituation(
    $ps, 3, Side::Visitor, 1, 1, 0, 3, 3, 0, 0, 
    null, null, $play[Side::Visitor][1], false, 61, $play[Side::Visitor][2], 
    $play[Side::Home][9]
);
$ps->fo0('4');
TestPS::checkSituation(
    $ps, 3, Side::Visitor, 2, 1, 0, 3, 3, 0, 0, 
    null, null, $play[Side::Visitor][1], false, 62, 
    $play[Side::Visitor][3], $play[Side::Home][9]
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 3, Side::Home, 0, 1, 0, 3, 3, 0, 0, 
    null, null, null, true, 63, $play[Side::Home][8], $play[Side::Visitor][9]
);
$ps->bb();
TestPS::checkSituation(
    $ps, 3, Side::Home, 0, 1, 0, 3, 3, 0, 0, 
    null, null, $play[Side::Home][8], false, 64, 
    $play[Side::Home][0], $play[Side::Visitor][9]
);
$ps->gb1('31');
TestPS::checkSituation(
    $ps, 3, Side::Home, 1, 1, 0, 3, 3, 0, 0, 
    null, $play[Side::Home][8], null, false, 65, 
    $play[Side::Home][1], $play[Side::Visitor][9]
);
$ps->k();
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 0, 3, 3, 0, 0, 
    null, $play[Side::Home][8], null, false, 66, 
    $play[Side::Home][2], $play[Side::Visitor][9]
);
$ps->t();
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 1, 3, 4, 0, 0, 
    $play[Side::Home][2], null, null, false, 67, 
    $play[Side::Home][3], $play[Side::Visitor][9]
);
$ps->bb();
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 1, 3, 4, 0, 0, 
    $play[Side::Home][2], null, $play[Side::Home][3], 
    false, 68, $play[Side::Home][4], $play[Side::Visitor][9]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 2, 3, 5, 0, 0, 
    null, $play[Side::Home][3], $play[Side::Home][4], 
    false, 69, $play[Side::Home][5], $play[Side::Visitor][9]
);
$ps->d(0, 2, 3, 3);
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 4, 3, 6, 0, 0, 
    $play[Side::Home][5], null, null, false, 70, 
    $play[Side::Home][6], $play[Side::Visitor][9]
);
$ps->wp1();
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 5, 3, 6, 0, 0, 
    null, null, null, false, 71, $play[Side::Home][6], $play[Side::Visitor][9]
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 4, Side::Visitor, 0, 1, 5, 3, 6, 0, 0, 
    null, null, null, true, 72, $play[Side::Visitor][4], $play[Side::Home][9]
);
// 4
$ps->k();
TestPS::checkSituation(
    $ps, 4, Side::Visitor, 1, 1, 5, 3, 6, 0, 0, 
    null, null, null, false, 73, $play[Side::Visitor][5], $play[Side::Home][9]
);
$ps->k();
TestPS::checkSituation(
    $ps, 4, Side::Visitor, 2, 1, 5, 3, 6, 0, 0, 
    null, null, null, false, 74, $play[Side::Visitor][6], $play[Side::Home][9]
);
$ps->fo0('2');
TestPS::checkSituation(
    $ps, 4, Side::Home, 0, 1, 5, 3, 6, 0, 0, 
    null, null, null, true, 75, $play[Side::Home][7], $play[Side::Visitor][9]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 4, Side::Home, 0, 1, 5, 3, 7, 0, 0, 
    null, null, $play[Side::Home][7], false, 76, $play[Side::Home][8], 
    $play[Side::Visitor][9]
);
$ps->sac1('31');
TestPS::checkSituation(
    $ps, 4, Side::Home, 1, 1, 5, 3, 7, 0, 0, 
    null, $play[Side::Home][7], null, false, 77, $play[Side::Home][0], 
    $play[Side::Visitor][9]
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 4, Side::Home, 2, 1, 5, 3, 7, 0, 0, 
    null, $play[Side::Home][7], null, false, 78, $play[Side::Home][1], 
    $play[Side::Visitor][9]
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 0, 1, 5, 3, 7, 0, 0, 
    null, null, null, true, 79, $play[Side::Visitor][7], $play[Side::Home][9]
);
// 5
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 1, 1, 5, 3, 7, 0, 0, 
    null, null, null, false, 80, $play[Side::Visitor][8], $play[Side::Home][9]
);
$ps->bb();
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 1, 1, 5, 3, 7, 0, 0, 
    null, null, $play[Side::Visitor][8], false, 81, $play[Side::Visitor][0], 
    $play[Side::Home][9]
);
$ps->s(0, 0, 2, 2);
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 1, 1, 5, 4, 7, 0, 0, 
    $play[Side::Visitor][8], $play[Side::Visitor][0], null, false, 82, 
    $play[Side::Visitor][1], $play[Side::Home][9]
);
$ps->gb('43', 1, 1, 0, -1);
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 2, 2, 5, 4, 7, 0, 0, 
    $play[Side::Visitor][0], null, null, false, 83, 
    $play[Side::Visitor][2], $play[Side::Home][9]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 2, 3, 5, 5, 7, 0, 0, 
    null, null, $play[Side::Visitor][2], false, 84, 
    $play[Side::Visitor][3], $play[Side::Home][9]
);
$ps->fo0('4');
TestPS::checkSituation(
    $ps, 5, Side::Home, 0, 3, 5, 5, 7, 0, 0, 
    null, null, null, true, 85, $play[Side::Home][2], $play[Side::Visitor][9]
);
$ps->gb1('43');
TestPS::checkSituation(
    $ps, 5, Side::Home, 1, 3, 5, 5, 7, 0, 0, 
    null, null, null, false, 86, $play[Side::Home][3], $play[Side::Visitor][9]
);
$ps->k();
TestPS::checkSituation(
    $ps, 5, Side::Home, 2, 3, 5, 5, 7, 0, 0, 
    null, null, null, false, 87, $play[Side::Home][4], $play[Side::Visitor][9]
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 6, Side::Visitor, 0, 3, 5, 5, 7, 0, 0, 
    null, null, null, true, 88, $play[Side::Visitor][4], $play[Side::Home][9]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 6, Side::Visitor, 0, 3, 5, 6, 7, 0, 0, 
    null, null, $play[Side::Visitor][4], false, 89, 
    $play[Side::Visitor][5], $play[Side::Home][9]
);
$ps->k();
TestPS::checkSituation(
    $ps, 6, Side::Visitor, 1, 3, 5, 6, 7, 0, 0, 
    null, null, $play[Side::Visitor][4], false, 90, 
    $play[Side::Visitor][6], $play[Side::Home][9]
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 6, Side::Visitor, 2, 3, 5, 6, 7, 0, 0, 
    null, null, $play[Side::Visitor][4], false, 91, 
    $play[Side::Visitor][7], $play[Side::Home][9]
);
$ps->fo0('4');
$play[Side::Visitor][10] = Player::initial('Villanueva', null);
$ps->pitcher(Side::Visitor, $play[Side::Visitor][10]);
TestPS::checkSituation(
    $ps, 6, Side::Home, 0, 3, 5, 6, 7, 0, 0, 
    null, null, null, true, 92, $play[Side::Home][5], $play[Side::Visitor][10]
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 6, Side::Home, 1, 3, 5, 6, 7, 0, 0, 
    null, null, null, false, 93, $play[Side::Home][6], $play[Side::Visitor][10]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 6, Side::Home, 1, 3, 5, 6, 8, 0, 0, 
    null, null, $play[Side::Home][6], false, 94, 
    $play[Side::Home][7], $play[Side::Visitor][10]
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 6, Side::Home, 2, 3, 5, 6, 8, 0, 0, 
    null, null, $play[Side::Home][6], false, 95, 
    $play[Side::Home][8], $play[Side::Visitor][10]
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 7, Side::Visitor, 0, 3, 5, 6, 8, 0, 0, 
    null, null, null, true, 96, $play[Side::Visitor][8], $play[Side::Home][9]
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 7, Side::Visitor, 1, 3, 5, 6, 8, 0, 0, 
    null, null, null, false, 97, $play[Side::Visitor][0], $play[Side::Home][9]
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 7, Side::Visitor, 2, 3, 5, 6, 8, 0, 0, 
    null, null, null, false, 98, $play[Side::Visitor][1], $play[Side::Home][9]
);
$ps->k();
TestPS::checkSituation(
    $ps, 7, Side::Home, 0, 3, 5, 6, 8, 0, 0, 
    null, null, null, true, 99, $play[Side::Home][0], $play[Side::Visitor][10]
);
$ps->gb1('53');
TestPS::checkSituation(
    $ps, 7, Side::Home, 1, 3, 5, 6, 8, 0, 0, 
    null, null, null, false, 100, $play[Side::Home][1], $play[Side::Visitor][10]
);
$ps->gb1('43');
TestPS::checkSituation(
    $ps, 7, Side::Home, 2, 3, 5, 6, 8, 0, 0, 
    null, null, null, false, 101, $play[Side::Home][2], $play[Side::Visitor][10]
);
$ps->s1();
TestPS::checkSituation(
    $ps, 7, Side::Home, 2, 3, 5, 6, 9, 0, 0, 
    null, null, $play[Side::Home][2], false, 102, 
    $play[Side::Home][3], $play[Side::Visitor][10]
);
$ps->sb1();
TestPS::checkSituation(
    $ps, 7, Side::Home, 2, 3, 5, 6, 9, 0, 0, 
    null, $play[Side::Home][2], null, false, 103, 
    $play[Side::Home][3], $play[Side::Visitor][10]
);
$ps->k();
$play[Side::Home][10] = Player::initial('Johnson', null);
$ps->battingOrder(
    Side::Home, 3, $play[Side::Home][10], Position::position('B1')
);
//$ps->pitcher('Adams');
$play[Side::Home][11] = Player::initial('Adams', null);
$ps->pitcher(Side::Home, $play[Side::Home][11]);
TestPS::checkSituation(
    $ps, 8, Side::Visitor, 0, 3, 5, 6, 9, 0, 0, 
    null, null, null, true, 104, $play[Side::Visitor][2], $play[Side::Home][11]
);
$ps->fo0('4');
TestPS::checkSituation(
    $ps, 8, Side::Visitor, 1, 3, 5, 6, 9, 0, 0, 
    null, null, null, false, 105, $play[Side::Visitor][3], $play[Side::Home][11]
);
$ps->hr();
TestPS::checkSituation(
    $ps, 8, Side::Visitor, 1, 4, 5, 7, 9, 0, 0, 
    null, null, null, false, 106, $play[Side::Visitor][4], $play[Side::Home][11]
);
$ps->k();
TestPS::checkSituation(
    $ps, 8, Side::Visitor, 2, 4, 5, 7, 9, 0, 0, 
    null, null, null, false, 107, $play[Side::Visitor][5], $play[Side::Home][11]
);
$ps->k();
$play[Side::Visitor][11] = Player::initial('Mujica', null);
//$ps->pitcher(Side::Visitor, $play[Side::Visitor][11]);
$ps->pitcher(Side::Visitor, $play[Side::Visitor][11]);
TestPS::checkSituation(
    $ps, 8, Side::Home, 0, 4, 5, 7, 9, 0, 0, 
    null, null, null, true, 108, $play[Side::Home][4], $play[Side::Visitor][11]
);
$ps->hr();
$play[Side::Home][12] = Player::initial('Cruz', null);
//$ps->battingOrder(
//    Side::Home, 6, $play[Side::Home][12], Position::position('PH')
//);
$ps->hitter(Side::Home, $play[Side::Home][12]);
TestPS::checkSituation(
    $ps, 8, Side::Home, 0, 4, 6, 7, 10, 0, 0, 
    null, null, null, false, 109, $play[Side::Home][12], $play[Side::Visitor][11]
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 8, Side::Home, 1, 4, 6, 7, 10, 0, 0, 
    null, null, null, false, 110, $play[Side::Home][6], $play[Side::Visitor][11]
);
$ps->k();
TestPS::checkSituation(
    $ps, 8, Side::Home, 2, 4, 6, 7, 10, 0, 0, 
    null, null, null, false, 111, $play[Side::Home][7], $play[Side::Visitor][11]
);
$ps->k();
TestPS::checkAssert(! $ps->lineupValid(Side::Home), 113);
try {
    $ps->move(Side::Home, 5, Position::position('DH'));
} catch (Exception $ex) {
    print "Should not get here - 114: " . $ex->getMessage() . "\n";
    exit;
}
TestPS::checkAssert($ps->lineupValid(Side::Home), 115);
$play[Side::Home][13] = Player::initial('Rivera', null);
$ps->pitcher(Side::Home, $play[Side::Home][13]);
$play[Side::Visitor][12] = Player::initial('Presley', null);
$ps->hitter(Side::Visitor, $play[Side::Visitor][12]);
TestPS::checkSituation(
    $ps, 9, Side::Visitor, 0, 4, 6, 7, 10, 0, 0, 
    null, null, null, true, 1112, $play[Side::Visitor][12], $play[Side::Home][13]
);
$ps->gb1('31');
TestPS::checkSituation(
    $ps, 9, Side::Visitor, 1, 4, 6, 7, 10, 0, 0, 
    null, null, null, false, 116, $play[Side::Visitor][7], $play[Side::Home][13]
);
$ps->d2();
TestPS::checkSituation(
    $ps, 9, Side::Visitor, 1, 4, 6, 8, 10, 0, 0, 
    null, $play[Side::Visitor][7], null, false, 117, 
    $play[Side::Visitor][8], $play[Side::Home][13]
);
$ps->fo0('4');
TestPS::checkSituation(
    $ps, 9, Side::Visitor, 2, 4, 6, 8, 10, 0, 0, 
    null, $play[Side::Visitor][7], null, false, 118, 
    $play[Side::Visitor][0], $play[Side::Home][13]
);
$ps->wp1();
TestPS::checkSituation(
    $ps, 9, Side::Visitor, 2, 4, 6, 8, 10, 0, 0, 
    $play[Side::Visitor][7], null, null, false, 119, 
    $play[Side::Visitor][0], $play[Side::Home][13]
);
TestPS::checkAssert(! $ps->getSituation()->gameOver(), 120);
//$ps->debugOn();
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 9, Side::Home, 0, 4, 6, 8, 10, 0, 0, 
    null, null, null, true, 121, null, null, true, null, null
);
try {
    $g1 = $ps->toString();
    $ps = ProjectScoresheet::fromString($g1);
    $g2 = $ps->toString();
    TestPS::checkAssert($g1 == $g2, 122);
} catch (\Exception $ex) {
    print('Should not get here 123: ' . $ex->getMessage() . "\n");
    for ($i=0; $i < min(strlen($g1), strlen($g2)); $i++) {
        if ($g1[$i] != $g2[$i]) {
            print $i . "<br/>";
            $i =  min(strlen($g1), strlen($g2)) + 1;
        }
    }
 
    print $g1 . "\n";
    print $g2 . "\n";
    exit;
}
TestPS::checkSituation(
    $ps, 9, Side::Home, 0, 4, 6, 8, 10, 0, 0, 
    null, null, null, true, 124, null, null, true
);

print "Test successful\n";
exit;


// Second test needs work.
$ps = new ProjectScoresheet;
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"","gameNumber":"0","lineup":[[],[],[],[],[],[],[],' .
         '[],[]],"rotation":[],"roster":[],"results":[]},"home":{"name":"",' .
         '"gameNumber":"0","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"situation":{"situation":{"outs":"0","runsV":' .
         '"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0",' .
         '"inning":"1","side":"0","first":"","second":"","third":"","batter":""' .
         ',"pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 1
);
$ps->assignTeam(Side::Visitor, 'Pittsburgh', '001');
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[],[],[],' .
         '[],[],[],[],[],[]],"rotation":[],"roster":[],"results":[]},"home":{' .
         '"name":"","gameNumber":"0","lineup":[[],[],[],[],[],[],[],[],[]],' .
         '"rotation":[],"roster":[],"results":[]},"situation":{"situation":{' .
         '"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":' .
         '"0","errorsH":"0","inning":"1","side":"0","first":"","second":"",' .
         '"third":"","batter":"","pitcher":"","betweenInnings":true,"gameOver":' .
         'false}}}', 
    false, false, 2
);
$ps->assignTeam(Side::Home, 'San Diego', '001');
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[],[],[],' .
         '[],[],[],[],[],[]],"rotation":[],"roster":[],"results":[]},"home":{' .
         '"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[]' .
         ',[]],"rotation":[],"roster":[],"results":[]},"situation":{"situation":{' .
         '"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0"' .
         ',"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":""' .
         ',"batter":"","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 3
);
$play = array();
$play[Side::Visitor] = array();
$play[Side::Visitor][0] = Player::initial('Morgan', null);
$ps->battingOrder(
    Side::Visitor, 0, $play[Side::Visitor][0], Position::position('RF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001",' .
         '"lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"roster":[],' .
         '"results":[]},"situation":{"situation":{"outs":"0","runsV":"0","runsH":' .
         '"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0","inning":"1",' .
         '"side":"0","first":"","second":"","third":"","batter":"Morgan","pitcher"' .
         ':"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 4
);
$play[Side::Visitor][1] = Player::initial('McCutchen', null);
$ps->battingOrder(
    Side::Visitor, 1, $play[Side::Visitor][1], Position::position('CF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[],[],[],[],[],[],[]],"rotation":[],"roster":[],"results":[]}' .
         ',"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],' .
         '[],[],[],[]],"rotation":[],"roster":[],"results":[]},"situation":{' .
         '"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0",' .
         '"errorsV":"0","errorsH":"0","inning":"1","side":"0","first":"","second":' .
         '"","third":"","batter":"Morgan","pitcher":"","betweenInnings":true,' .
         '"gameOver":false}}}', 
    false, false, 5
);
$play[Side::Visitor][2] = Player::initial('Betemit', null);
$ps->battingOrder(
    Side::Visitor, 2, $play[Side::Visitor][2], Position::position('B3')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],' .
         '[],[],[]],"rotation":[],"roster":[],"results":[]},"home":{"name":' .
         '"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],' .
         '"rotation":[],"roster":[],"results":[]},"situation":{"situation":{"outs"' .
         ':"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0",' .
         '"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":"",' .
         '"batter":"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 6
);
$play[Side::Visitor][3] = Player::initial('Duda', null);
$ps->battingOrder(
    Side::Visitor, 3, $play[Side::Visitor][3], Position::position('B1')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1"' .
         ',"when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001"' .
         ',"lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"roster":[],' .
         '"results":[]},"situation":{"situation":{"outs":"0","runsV":"0","runsH"' .
         ':"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0","inning":"1",' .
         '"side":"0","first":"","second":"","third":"","batter":"Morgan","pitcher"' .
         ':"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 7
);
$play[Side::Visitor][4] = Player::initial('Willingham', null);
$ps->battingOrder(
    Side::Visitor, 4, $play[Side::Visitor][4], Position::position('DH')
);
TestPs::checkExpected(
);
$play[Side::Visitor][4] = Player::initial('Willingham', null);
$ps->battingOrder(
    Side::Visitor, 4, $play[Side::Visitor][4], Position::position('DH')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home"' .
         ':"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[],[],[],[]],"rotation":[],"roster":[],"results":[]},' .
         '"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],' .
         '[],[],[],[]],"rotation":[],"roster":[],"results":[]},"situation":{' .
         '"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0",' .
         '"errorsV":"0","errorsH":"0","inning":"1","side":"0","first":"","second":' .
         '"","third":"","batter":"Morgan","pitcher":"","betweenInnings":true,' .
         '"gameOver":false}}}', 
    false, false, 8
);
$play[Side::Visitor][5] = Player::initial('Tabata', null);
$ps->battingOrder(
    Side::Visitor, 5, $play[Side::Visitor][5], Position::position('LF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1"' .
         ',"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham"' .
         ',"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[]]' .
         ',"rotation":[],"roster":[],"results":[]},"home":{"name":"San Diego",' .
         '"gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"situation":{"situation":{"outs":"0","runsV":' .
         '"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0",' .
         '"inning":"1","side":"0","first":"","second":"","third":"","batter":' .
         '"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 9
);
$play[Side::Visitor][6] = Player::initial('Scutaro', null);
$ps->battingOrder(
    Side::Visitor, 6, $play[Side::Visitor][6], Position::position('SS')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1"' .
         ',"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham"' .
         ',"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001"' .
         ',"lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"roster":[],' .
         '"results":[]},"situation":{"situation":{"outs":"0","runsV":"0","runsH"' .
         ':"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0","inning":"1",' .
         '"side":"0","first":"","second":"","third":"","batter":"Morgan","pitcher"' .
         ':"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 10
);
$play[Side::Visitor][7] = Player::initial('Thole', null);
$ps->battingOrder(
    Side::Visitor, 7, $play[Side::Visitor][7], Position::position('C')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1"' .
         ',"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham"' .
         ',"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[]],"rotation":[],"roster":[],"results":[]},"home":{' .
         '"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],' .
         '[]],"rotation":[],"roster":[],"results":[]},"situation":{"situation":{' .
         '"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0"' .
         ',"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":""' .
         ',"batter":"Morgan","pitcher":"","betweenInnings":true,"gameOver":' .
         'false}}}', 
    false, false, 11
);
$play[Side::Visitor][8] = Player::initial('Walker', null);
$ps->battingOrder(
    Side::Visitor, 8, $play[Side::Visitor][8], Position::position('B2')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole"' .
         ',"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[],"roster":[],"results":[]},"home":{"name":"San Diego",' .
         '"gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"situation":{"situation":{"outs":"0","runsV":' .
         '"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0",' .
         '"inning":"1","side":"0","first":"","second":"","third":"","batter":' .
         '"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    false, false, 12
);
$play[Side::Visitor][9] = Player::initial('Hudson', null);
$ps->pitcher(Side::Visitor, $play[Side::Visitor][9]);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[],[],[],[],[],[],[],[],[]],"rotation":[],"roster":[],"results":[]},' .
         '"situation":{"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0"' .
         ',"hitsH":"0","errorsV":"0","errorsH":"0","inning":"1","side":"0","first"' .
         ':"","second":"","third":"","batter":"Morgan","pitcher":"",' .
         '"betweenInnings":true,"gameOver":false}}}', 
    true, false, 13
);

$play[Side::Home] = array();
$play[Side::Home][0] = Player::initial('Bourne', null);
$ps->battingOrder(
    Side::Home, 0, $play[Side::Home][0], Position::position('RF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1"' .
         ',"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham"' .
         ',"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[],[]],' .
         '"rotation":[],"roster":[],"results":[]},"situation":{"situation":{"outs"' .
         ':"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0",' .
         '"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":"",' .
         '"batter":"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    true, false, 14
);
$play[Side::Home][1] = Player::initial('Jay', null);
$ps->battingOrder(
    Side::Home, 1, $play[Side::Home][1], Position::position('CF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[],[],[],[],[],[],[]],"rotation":[],"roster":[],' .
         '"results":[]},"situation":{"situation":{"outs":"0","runsV":"0","runsH":' .
         '"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0","inning":"1",' .
         '"side":"0","first":"","second":"","third":"","batter":"Morgan","pitcher"' .
         ':"","betweenInnings":true,"gameOver":false}}}', 
    true, false, 15
);
$play[Side::Home][2] = Player::initial('Murphy', null);
$ps->battingOrder(
    Side::Home, 2, $play[Side::Home][2], Position::position('B3')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],' .
         '[],[],[]],"rotation":[],"roster":[],"results":[]},"situation":{' .
         '"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0",' .
         '"errorsV":"0","errorsH":"0","inning":"1","side":"0","first":"","second":' .
         '"","third":"","batter":"Morgan","pitcher":"","betweenInnings":true,' .
         '"gameOver":false}}}', 
    true, false, 16
);
$play[Side::Home][3] = Player::initial('Holliday', null);
$ps->battingOrder(
    Side::Home, 3, $play[Side::Home][3], Position::position('B1')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[]],"rotation":' .
         '[],"roster":[],"results":[]},"situation":{"situation":{"outs":"0",' .
         '"runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":' .
         '"0","inning":"1","side":"0","first":"","second":"","third":"","batter":' .
         '"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    true, false, 17
);
$play[Side::Home][4] = Player::initial('Young', null);
$ps->battingOrder(
    Side::Home, 4, $play[Side::Home][4], Position::position('LF')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age"' .
         ':"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[],[],[],[]],"rotation":[],"roster":[],"results":[]},' .
         '"situation":{"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0"' .
         ',"hitsH":"0","errorsV":"0","errorsH":"0","inning":"1","side":"0","first"' .
         ':"","second":"","third":"","batter":"Morgan","pitcher":"",' .
         '"betweenInnings":true,"gameOver":false}}}', 
    true, false, 18
);
$play[Side::Home][5] = Player::initial('Abreu', null);
$ps->battingOrder(
    Side::Home, 5, $play[Side::Home][5], Position::position('DH')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Abreu","age":"99","positions":[{' .
         '"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[],[],[]]' .
         ',"rotation":[],"roster":[],"results":[]},"situation":{"situation":{' .
         '"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0"' .
         ',"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":""' .
         ',"batter":"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}',
    true, false, 19
);
$play[Side::Home][6] = Player::initial('Escobar', null);
$ps->battingOrder(
    Side::Home, 6, $play[Side::Home][6], Position::position('SS')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Abreu","age":"99","positions":[{' .
         '"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Escobar","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[],[]],"rotation":[],' .
         '"roster":[],"results":[]},"situation":{"situation":{"outs":"0","runsV":' .
         '"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0",' .
         '"inning":"1","side":"0","first":"","second":"","third":"","batter":' .
         '"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    true, false, 20
);
$play[Side::Home][7] = Player::initial('Buck', null);
$ps->battingOrder(
    Side::Home, 7, $play[Side::Home][7], Position::position('C')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Abreu","age":"99","positions":[{' .
         '"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Escobar","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[]],"rotation":[],"roster":[],"results":[]},' .
         '"situation":{"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0"' .
         ',"hitsH":"0","errorsV":"0","errorsH":"0","inning":"1","side":"0","first"' .
         ':"","second":"","third":"","batter":"Morgan","pitcher":"",' .
         '"betweenInnings":true,"gameOver":false}}}', 
    true, false, 21
);
$play[Side::Home][8] = Player::initial('Carroll', null);
$ps->battingOrder(
    Side::Home, 8, $play[Side::Home][8], Position::position('B2')
);
TestPs::checkExpected(
);
$play[Side::Home][7] = Player::initial('Buck', null);
$ps->battingOrder(
    Side::Home, 7, $play[Side::Home][7], Position::position('C')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Abreu","age":"99","positions":[{' .
         '"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Escobar","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[]],"rotation":[],"roster":[],"results":[]},' .
         '"situation":{"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0"' .
         ',"hitsH":"0","errorsV":"0","errorsH":"0","inning":"1","side":"0","first"' .
         ':"","second":"","third":"","batter":"Morgan","pitcher":"",' .
         '"betweenInnings":true,"gameOver":false}}}', 
    true, false, 21
);
$play[Side::Home][8] = Player::initial('Carroll', null);
$ps->battingOrder(
    Side::Home, 8, $play[Side::Home][8], Position::position('B2')
);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Abreu","age":"99","positions":[{' .
         '"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Escobar","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Carroll","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[],"roster":[],"results":[]},"situation":{"situation":{"outs"' .
         ':"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0",' .
         '"errorsH":"0","inning":"1","side":"0","first":"","second":"","third":"",' .
         '"batter":"Morgan","pitcher":"","betweenInnings":true,"gameOver":false}}}', 
    true, false, 22
);
$play[Side::Home][9] = Player::initial('Vazquez', null);
$ps->pitcher(Side::Home, $play[Side::Home][9]);
TestPs::checkExpected(
    $ps, '{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":' .
         '{"name":"Morgan","age":"99","positions":[{"position":{"pos":"RF","when":' .
         '{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","age":' .
         '"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":' .
         '"0"}}}]}}],[{"player":{"name":"Betemit","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Duda","age":"99","positions":[{"position":{"pos":"B1",' .
         '"when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham",' .
         '"age":"99","positions":[{"position":{"pos":"DH","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Tabata","age":"99","positions":[{' .
         '"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Scutaro","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Walker","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Hudson","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":' .
         '[[{"player":{"name":"Bourne","age":"99","positions":[{"position":{"pos":' .
         '"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay",' .
         '"age":"99","positions":[{"position":{"pos":"CF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Murphy","age":"99","positions":[{' .
         '"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Holliday","age":"99","positions":[{"position":{"pos":' .
         '"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young",' .
         '"age":"99","positions":[{"position":{"pos":"LF","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Abreu","age":"99","positions":[{' .
         '"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{' .
         '"player":{"name":"Escobar","age":"99","positions":[{"position":{"pos":' .
         '"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck",' .
         '"age":"99","positions":[{"position":{"pos":"C","when":{"visitor":"0",' .
         '"home":"0"}}}]}}],[{"player":{"name":"Carroll","age":"99","positions":[{' .
         '"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],' .
         '"rotation":[{"player":{"name":"Vazquez","age":"99","positions":[{' .
         '"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"roster":' .
         '[],"results":[]},"situation":{"situation":{"outs":"0","runsV":"0",' .
         '"runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0",' .
         '"inning":"1","side":"0","first":"","second":"","third":"","batter":' .
         '"Morgan","pitcher":"Vazquez","betweenInnings":true,"gameOver":false}}}', 
    true, true, 23
);
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 0, 0, 0, 0, 0, 0, 0, 
    null, null, null, true, 24
);
$ps->s1();
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 0, 0, 0, 1, 0, 0, 0, 
    null, null, $play[Side::Visitor][0], false, 25
);
$ps->s1();
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 0, 0, 0, 2, 0, 0, 0, 
    null, $play[Side::Visitor][0], $play[Side::Visitor][1], false, 26
);
$ps->k();
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 1, 0, 0, 2, 0, 0, 0, 
    null, $play[Side::Visitor][0], $play[Side::Visitor][1], false, 27
);
$ps->s1();
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 1, 0, 0, 3, 0, 0, 0, 
    $play[Side::Visitor][0], $play[Side::Visitor][1], 
    $play[Side::Visitor][3], false, 28
);
$ps->fo('9', 1, 0, 0, -1);
TestPS::checkSituation(
    $ps, 1, Side::Visitor, 2, 1, 0, 3, 0, 0, 0, 
    null, $play[Side::Visitor][1], $play[Side::Visitor][3], false, 29
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 1, Side::Home, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 30
);
$ps->gb1('43');
TestPS::checkSituation(
    $ps, 1, Side::Home, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 31
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 1, Side::Home, 2, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 32
);
$ps->bb();
TestPS::checkSituation(
    $ps, 1, Side::Home, 2, 1, 0, 3, 0, 0, 0, 
    null, null, $play[Side::Home][2], false, 33
);
$ps->po1('13');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 34
);
// 2
$ps->fo0('5');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 35
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 2, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 36
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 2, Side::Home, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 37
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 38
);
$ps->s1();
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 1, 0, 0, 
    null, null, $play[Side::Home][4], false, 39
);
$ps->cs1('2-6');
TestPS::checkSituation(
    $ps, 2, Side::Home, 2, 1, 0, 3, 1, 0, 0, 
    null, null, null, false, 40
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 1, 0, 0, 
    null, null, $play[Side::Home][4], false, 41
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 42
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Home, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 43
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 2, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 44
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 45
);
$ps->undo();
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 46
);
$ps->undo();
TestPS::checkSituation(
    $ps, 1, Side::Home, 2, 1, 0, 3, 0, 0, 0, 
    null, null, $play[Side::Home][2], false, 47
);
$ps->undo();
TestPS::checkSituation(
    $ps, 1, Side::Home, 2, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 48
);
$ps->bb();
TestPS::checkSituation(
    $ps, 1, Side::Home, 2, 1, 0, 3, 0, 0, 0, 
    null, null, $play[Side::Home][2], false, 49
);
$ps->po1('13');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 50
);
// 2 again
$ps->fo0('5');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 51
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 2, Side::Visitor, 2, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 52
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 2, Side::Home, 0, 1, 0, 3, 0, 0, 0, 
    null, null, null, true, 53
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 0, 0, 0, 
    null, null, null, false, 54
);
$ps->s1();
TestPS::checkSituation(
    $ps, 2, Side::Home, 1, 1, 0, 3, 1, 0, 0, 
    null, null, $play[Side::Home][4], false, 55
);
$ps->cs1('2-6');
TestPS::checkSituation(
    $ps, 2, Side::Home, 2, 1, 0, 3, 1, 0, 0, 
    null, null, null, false, 56
);
$ps->s1();
TestPS::checkSituation(
    $ps, 2, Side::Home, 2, 1, 0, 3, 2, 0, 0, 
    null, null, $play[Side::Home][5], false, 57
);
$ps->s(0, 0, 2, 2);
TestPS::checkSituation(
    $ps, 2, Side::Home, 2, 1, 0, 3, 3, 0, 0, 
    $play[Side::Home][5], $play[Side::Home][6], null, false, 58
);
$ps->k();
TestPS::checkSituation(
    $ps, 3, Side::Visitor, 0, 1, 0, 3, 3, 0, 0, 
    null, null, null, true, 59
);
// 3
$ps->k();
TestPS::checkSituation(
    $ps, 3, Side::Visitor, 1, 1, 0, 3, 3, 0, 0, 
    null, null, null, false, 60
);
$ps->bb();
TestPS::checkSituation(
    $ps, 3, Side::Visitor, 1, 1, 0, 3, 3, 0, 0, 
    null, null, $play[Side::Visitor][1], false, 61
);
$ps->fo0('4');
TestPS::checkSituation(
    $ps, 3, Side::Visitor, 2, 1, 0, 3, 3, 0, 0, 
    null, null, $play[Side::Visitor][1], false, 62
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 3, Side::Home, 0, 1, 0, 3, 3, 0, 0, 
    null, null, null, true, 63
);
$ps->bb();
TestPS::checkSituation(
    $ps, 3, Side::Home, 0, 1, 0, 3, 3, 0, 0, 
    null, null, $play[Side::Home][8], false, 64
);
$ps->gb1('31');
TestPS::checkSituation(
    $ps, 3, Side::Home, 1, 1, 0, 3, 3, 0, 0, 
    null, $play[Side::Home][8], null, false, 65
);
$ps->k();
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 0, 3, 3, 0, 0, 
    null, $play[Side::Home][8], null, false, 66
);
$ps->t();
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 1, 3, 4, 0, 0, 
    $play[Side::Home][2], null, null, false, 67
);
$ps->bb();
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 1, 3, 4, 0, 0, 
    $play[Side::Home][2], null, $play[Side::Home][3], false, 68
);
$ps->s1();
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 2, 3, 5, 0, 0, 
    null, $play[Side::Home][3], $play[Side::Home][4], false, 69
);
$ps->d(0, 2, 3, 3);
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 4, 3, 6, 0, 0, 
    $play[Side::Home][5], null, null, false, 70
);
$ps->wp1();
TestPS::checkSituation(
    $ps, 3, Side::Home, 2, 1, 5, 3, 6, 0, 0, 
    null, null, null, false, 71
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 4, Side::Visitor, 0, 1, 5, 3, 6, 0, 0, 
    null, null, null, true, 72
);
// 4
$ps->k();
TestPS::checkSituation(
    $ps, 4, Side::Visitor, 1, 1, 5, 3, 6, 0, 0, 
    null, null, null, false, 73
);
$ps->k();
TestPS::checkSituation(
    $ps, 4, Side::Visitor, 2, 1, 5, 3, 6, 0, 0, 
    null, null, null, false, 74
);
$ps->fo0('2');
TestPS::checkSituation(
    $ps, 4, Side::Home, 0, 1, 5, 3, 6, 0, 0, 
    null, null, null, true, 75
);
$ps->s1();
TestPS::checkSituation(
    $ps, 4, Side::Home, 0, 1, 5, 3, 7, 0, 0, 
    null, null, $play[Side::Home][7], false, 76
);
$ps->sac1('31');
TestPS::checkSituation(
    $ps, 4, Side::Home, 1, 1, 5, 3, 7, 0, 0, 
    null, $play[Side::Home][7], null, false, 77
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 4, Side::Home, 2, 1, 5, 3, 7, 0, 0, 
    null, $play[Side::Home][7], null, false, 78
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 0, 1, 5, 3, 7, 0, 0, 
    null, null, null, true, 79
);
// 5
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 1, 1, 5, 3, 7, 0, 0, 
    null, null, null, false, 80
);
$ps->bb();
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 1, 1, 5, 3, 7, 0, 0, 
    null, null, $play[Side::Visitor][8], false, 81
);
$ps->s(0, 0, 2, 2);
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 1, 1, 5, 4, 7, 0, 0, 
    $play[Side::Visitor][8], $play[Side::Visitor][0], null, false, 82
);
$ps->gb('43', 1, 1, 0, -1);
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 2, 2, 5, 4, 7, 0, 0, 
    $play[Side::Visitor][0], null, null, false, 83
);
$ps->s1();
TestPS::checkSituation(
    $ps, 5, Side::Visitor, 2, 3, 5, 5, 7, 0, 0, 
    null, null, $play[Side::Visitor][2], false, 84
);
$ps->fo0('4');
TestPS::checkSituation(
    $ps, 5, Side::Home, 0, 3, 5, 5, 7, 0, 0, 
    null, null, null, true, 85
);
$ps->gb1('43');
TestPS::checkSituation(
    $ps, 5, Side::Home, 1, 3, 5, 5, 7, 0, 0, 
    null, null, null, false, 86
);
$ps->k();
TestPS::checkSituation(
    $ps, 5, Side::Home, 2, 3, 5, 5, 7, 0, 0, 
    null, null, null, false, 87
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 6, Side::Visitor, 0, 3, 5, 5, 7, 0, 0, 
    null, null, null, true, 88
);
$ps->s1();
TestPS::checkSituation(
    $ps, 6, Side::Visitor, 0, 3, 5, 6, 7, 0, 0, 
    null, null, $play[Side::Visitor][4], false, 89
);
$ps->k();
TestPS::checkSituation(
    $ps, 6, Side::Visitor, 1, 3, 5, 6, 7, 0, 0, 
    null, null, $play[Side::Visitor][4], false, 90
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 6, Side::Visitor, 2, 3, 5, 6, 7, 0, 0, 
    null, null, $play[Side::Visitor][4], false, 91
);
$ps->fo0('4');
TestPS::checkSituation(
    $ps, 6, Side::Home, 0, 3, 5, 6, 7, 0, 0, 
    null, null, null, true, 92
);
$play[Side::Visitor][10] = Player::initial('Villanueva', null);
$ps->pitcher(Side::Visitor, $play[Side::Visitor][10]);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 6, Side::Home, 1, 3, 5, 6, 7, 0, 0, 
    null, null, null, false, 93
);
$ps->s1();
TestPS::checkSituation(
    $ps, 6, Side::Home, 1, 3, 5, 6, 8, 0, 0, 
    null, null, $play[Side::Home][6], false, 94
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 6, Side::Home, 2, 3, 5, 6, 8, 0, 0, 
    null, null, $play[Side::Home][6], false, 95
);
$ps->fo0('7');
TestPS::checkSituation(
    $ps, 7, Side::Visitor, 0, 3, 5, 6, 8, 0, 0, 
    null, null, null, true, 96
);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 7, Side::Visitor, 1, 3, 5, 6, 8, 0, 0, 
    null, null, null, false, 97
);
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 7, Side::Visitor, 2, 3, 5, 6, 8, 0, 0, 
    null, null, null, false, 98
);
$ps->k();
TestPS::checkSituation(
    $ps, 7, Side::Home, 0, 3, 5, 6, 8, 0, 0, 
    null, null, null, true, 99
);
$ps->gb1('53');
TestPS::checkSituation(
    $ps, 7, Side::Home, 1, 3, 5, 6, 8, 0, 0, 
    null, null, null, false, 100
);
$ps->gb1('43');
TestPS::checkSituation(
    $ps, 7, Side::Home, 2, 3, 5, 6, 8, 0, 0, 
    null, null, null, false, 101
);
$ps->s1();
TestPS::checkSituation(
    $ps, 7, Side::Home, 2, 3, 5, 6, 9, 0, 0, 
    null, null, $play[Side::Home][2], false, 102
);
$ps->sb1();
TestPS::checkSituation(
    $ps, 7, Side::Home, 2, 3, 5, 6, 9, 0, 0, 
    null, $play[Side::Home][2], null, false, 103
);
$ps->k();
TestPS::checkSituation(
    $ps, 8, Side::Visitor, 0, 3, 5, 6, 9, 0, 0, 
    null, null, null, true, 104
);
$play[Side::Home][10] = Player::initial('Johnson', null);
$ps->battingOrder(
    Side::Home, 3, $play[Side::Home][10], Position::position('B1')
);
//$ps->pitcher('Adams');
$play[Side::Home][11] = Player::initial('Adams', null);
$ps->pitcher(Side::Home, $play[Side::Home][11]);
$ps->fo0('4');
TestPS::checkSituation(
    $ps, 8, Side::Visitor, 1, 3, 5, 6, 9, 0, 0, 
    null, null, null, false, 105
);
$ps->hr();
TestPS::checkSituation(
    $ps, 8, Side::Visitor, 1, 4, 5, 7, 9, 0, 0, 
    null, null, null, false, 106
);
$ps->k();
TestPS::checkSituation(
    $ps, 8, Side::Visitor, 2, 4, 5, 7, 9, 0, 0, 
    null, null, null, false, 107
);
$ps->k();
TestPS::checkSituation(
    $ps, 8, Side::Home, 0, 4, 5, 7, 9, 0, 0, 
    null, null, null, true, 108
);
$play[Side::Visitor][11] = Player::initial('Mujica', null);
//$ps->pitcher(Side::Visitor, $play[Side::Visitor][11]);
$ps->pitcher(Side::Visitor, $play[Side::Visitor][11]);
$ps->hr();
TestPS::checkSituation(
    $ps, 8, Side::Home, 0, 4, 6, 7, 10, 0, 0, 
    null, null, null, false, 109
);
$play[Side::Home][12] = Player::initial('Cruz', null);
//$ps->battingOrder(
//    Side::Home, 6, $play[Side::Home][12], Position::position('PH')
//);
$ps->hitter(Side::Home, $play[Side::Home][12]);
$ps->gb1('63');
TestPS::checkSituation(
    $ps, 8, Side::Home, 1, 4, 6, 7, 10, 0, 0, 
    null, null, null, false, 110
);
$ps->k();
TestPS::checkSituation(
    $ps, 8, Side::Home, 2, 4, 6, 7, 10, 0, 0, 
    null, null, null, false, 111
);
$ps->k();
TestPS::checkSituation(
    $ps, 9, Side::Visitor, 0, 4, 6, 7, 10, 0, 0, 
    null, null, null, true, 112
);
TestPS::checkAssert(! $ps->lineupValid(Side::Home), 113);
try {
    $ps->move(Side::Home, 5, Position::position('DH'));
} catch (Exception $ex) {
    print "Should not get here - 114: " . $ex->getMessage() . "\n";
    exit;
}
TestPS::checkAssert($ps->lineupValid(Side::Home), 115);
$play[Side::Home][13] = Player::initial('Rivera', null);
$ps->pitcher(Side::Home, $play[Side::Home][13]);
$play[Side::Visitor][12] = Player::initial('Presley', null);
$ps->hitter(Side::Visitor, $play[Side::Visitor][12]);
$ps->gb1('31');
TestPS::checkSituation(
    $ps, 9, Side::Visitor, 1, 4, 6, 7, 10, 0, 0, 
    null, null, null, false, 116
);
$ps->d2();
TestPS::checkSituation(
    $ps, 9, Side::Visitor, 1, 4, 6, 8, 10, 0, 0, 
    null, $play[Side::Visitor][7], null, false, 117
);
$ps->fo0('4');
TestPS::checkSituation(
    $ps, 9, Side::Visitor, 2, 4, 6, 8, 10, 0, 0, 
    null, $play[Side::Visitor][7], null, false, 118
);
$ps->wp1();
TestPS::checkSituation(
    $ps, 9, Side::Visitor, 2, 4, 6, 8, 10, 0, 0, 
    $play[Side::Visitor][7], null, null, false, 119
);
TestPS::checkAssert(! $ps->getSituation()->gameOver(), 120);
//$ps->debugOn();
$ps->fo0('8');
TestPS::checkSituation(
    $ps, 9, Side::Home, 0, 4, 6, 8, 10, 0, 0, 
    null, null, null, true, 121, null, null, true
);
try {
    $g1 = $ps->toString();
    $ps = ProjectScoresheet::fromString($g1);
    $g2 = $ps->toString();
    TestPS::checkAssert($g1 == $g2, 122);
} catch (Exception $ex) {
    print('Should not get here 123: ' + $ex->getMessage() . "\n");
    print $g1 . "\n";
    print $g2 . "\n";
    exit;
}
TestPS::checkSituation(
    $ps, 9, Side::Home, 0, 4, 6, 8, 10, 0, 0,
    null, null, null, true, 124, null, null, true
);
print "Test successful\n";
?>
