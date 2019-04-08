<?php
  namespace ProjectScoresheet;
  require_once '../pss/ProjectScoresheet.php';
  class TestPS {
    public static function checkAssert($bool, $number=0) {
      if (! $bool) {
        print "Error " . $number . " - Assertion failed\n";
        exit;
      }
    }
    public static function checkSituation($ps,
      $inning, $side, $outs, 
      $awayRuns, $homeRuns, 
      $awayHits, $homeHits, 
      $awayErrors, $homeErrors,
      $third, $second, $first, $betweenInnings, $number, $gameOver=false) {
      $sit=$ps->getSituation();
      $rtn = true;
      if ($inning !== $sit->inning) {
        print "Error " . $number . ".0 - expected=" . $inning . ",found=" . $sit->inning . "\n";
        $rtn = false;
      }
      if ($side !== $sit->side) {
        print "Error " . $number . ".1 - expected=" . $side . ",found=" .$sit->side."\n";
        $rtn = false;
      }
      if ($outs !== $sit->outs) {
        print "Error " . $number . ".2 - expected=" . $outs . ",found=" .$sit->outs."\n";
        $rtn = false;
      }
      if ($awayRuns !== $sit->runs[0]) {
        print "Error " . $number . ".3 - expected=" . $awayRuns . ",found=" .$sit->runs[0]."\n";
        $rtn = false;
      }
      if ($homeRuns !== $sit->runs[1]) {
        print "Error " . $number . ".4 - expected=" . $homeRuns . ",found=" .$sit->runs[1]."\n";
        $rtn = false;
      }
      if ($awayHits !== $sit->hits[0]) {
        print "Error " . $number . ".5 - expected=" . $awayHits . ",found=" .$sit->hits[0]."\n";
        $rtn = false;
      }
      if ($homeHits !== $sit->hits[1]) {
        print "Error " . $number . ".6 - expected=" . $homeHits . ",found=" .$sit->hits[1]."\n";
        $rtn = false;
      }
      if ($awayErrors !== $sit->errors[0]) {
        print "Error " . $number . ".7 - expected=" . $awayErrors . ",found=" .$sit->errors[0]."\n";
        $rtn = false;
      }
      if ($homeErrors !== $sit->errors[1]) {
        print "Error " . $number . ".8 - expected=" . $homeErrors . ",found=" .$sit->errors[1]."\n";
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
        } else if ($first->name !== $sit->runner[1]->name) {
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
        } else if ($second->name !== $sit->runner[2]->name) {
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
        } else if ($third->name !== $sit->runner[3]->name) {
          print "Error " . $number . ".17\n";
          $rtn = false;
        }
      }
      if ($betweenInnings !== $sit->betweenInnings) {
        print "Error " . $number . ".18 - expected=" . $betweenInnings . ",found=" .$sit->betweenInnings."\n";
        $rtn = false;
      }
      if ($gameOver !== $sit->gameOver()) {
        print "Error " . $number . ".19 - expected=" . $gameOver . ",found=" .$sit->gameOver()."\n";
        $rtn = false;
      }
      if (! $rtn) {
        print_r($sit);
        exit;
      }
    }
//  private static void  compareStrings(String s1, String s2) {
//    if (s1.equals(s2)) return;
//    String[] split1 = s1.split("\n",0);
//    String[] split2 = s2.split("\n",0);
//    //assert(split1.length==split2.length);
//    for (int i=0; i<split2.length; i++) {
//      if (i>=split1.length) {
//        i--;
//        out.println(split1[i]);
//        out.println(split2[i]);      
//        out.println(split1.length);
//        out.println(split2.length);
//        assert(split1.length==split2.length);
//      }
//      if (! split1[i].equals(split2[i])) {
//        out.println(split1[i]);
////        out.println("----------------------------");
//        out.println(split2[i]);
//        out.println(i);
//      }
//      assert(split1[i].equals(split2[i]));
//    }
//  }   
    public static function checkExpected($ps, $expected, $visitor, $home, $number) {
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
  TestPs::checkExpected($ps,'{"visitor":{"name":"","gameNumber":"0","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"","gameNumber":"0","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,1);
  $ps->assignTeam(Side::Visitor,'Pittsburgh','001');
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"","gameNumber":"0","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,1);
  $ps->assignTeam(Side::Home,'San Diego','001');
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,3);
  $play = array();
  $play[Side::Visitor] = array();
  $play[Side::Visitor][0] = Player::initial('Morgan',null);
  $ps->battingOrder(Side::Visitor, 0, $play[Side::Visitor][0], Position::position('RF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,4);
  $play[Side::Visitor][1] = Player::initial('McCutchen',null);
  $ps->battingOrder(Side::Visitor, 1, $play[Side::Visitor][1], Position::position('CF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,5);
  $play[Side::Visitor][2] = Player::initial('Betemit',null);
  $ps->battingOrder(Side::Visitor, 2, $play[Side::Visitor][2], Position::position('B3'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,6);
  $play[Side::Visitor][3] = Player::initial('Duda',null);
  $ps->battingOrder(Side::Visitor, 3, $play[Side::Visitor][3], Position::position('B1'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,7);
  $play[Side::Visitor][4] = Player::initial('Willingham',null);
  $ps->battingOrder(Side::Visitor, 4, $play[Side::Visitor][4], Position::position('DH'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,8);
  $play[Side::Visitor][5] = Player::initial('Tabata',null);
  $ps->battingOrder(Side::Visitor, 5, $play[Side::Visitor][5], Position::position('LF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,9);
  $play[Side::Visitor][6] = Player::initial('Scutaro',null);
  $ps->battingOrder(Side::Visitor, 6, $play[Side::Visitor][6], Position::position('SS'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,10);
  $play[Side::Visitor][7] = Player::initial('Thole',null);
  $ps->battingOrder(Side::Visitor, 7, $play[Side::Visitor][7], Position::position('C'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,11);
  $play[Side::Visitor][8] = Player::initial('Walker',null);
  $ps->battingOrder(Side::Visitor, 8, $play[Side::Visitor][8], Position::position('B2'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,12);
  $play[Side::Visitor][9] = Player::initial('Hudson',null);
  $ps->pitcher(Side::Visitor, $play[Side::Visitor][9]);
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',true,false,13);

  $play[Side::Home] = array();
  $play[Side::Home][0] = Player::initial('Bourne',null);
  $ps->battingOrder(Side::Home, 0, $play[Side::Home][0], Position::position('RF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',true,false,14);
  $play[Side::Home][1] = Player::initial('Jay',null);
  $ps->battingOrder(Side::Home, 1, $play[Side::Home][1], Position::position('CF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',true,false,15);
  $play[Side::Home][2] = Player::initial('Murphy',null);
  $ps->battingOrder(Side::Home, 2, $play[Side::Home][2], Position::position('B3'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[]],"rotation":[],"results":[]}}',true,false,16);
  $play[Side::Home][3] = Player::initial('Holliday',null);
  $ps->battingOrder(Side::Home, 3, $play[Side::Home][3], Position::position('B1'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[]],"rotation":[],"results":[]}}',true,false,17);
  $play[Side::Home][4] = Player::initial('Young',null);
  $ps->battingOrder(Side::Home, 4, $play[Side::Home][4], Position::position('LF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[]],"rotation":[],"results":[]}}',true,false,18);
  $play[Side::Home][5] = Player::initial('Abreu',null);
  $ps->battingOrder(Side::Home, 5, $play[Side::Home][5], Position::position('DH'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Abreu","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[],[],[]],"rotation":[],"results":[]}}',true,false,19);
  $play[Side::Home][6] = Player::initial('Escobar',null);
  $ps->battingOrder(Side::Home, 6, $play[Side::Home][6], Position::position('SS'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Abreu","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Escobar","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[],[]],"rotation":[],"results":[]}}',true,false,20);
  $play[Side::Home][7] = Player::initial('Buck',null);
  $ps->battingOrder(Side::Home, 7, $play[Side::Home][7], Position::position('C'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Abreu","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Escobar","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[]],"rotation":[],"results":[]}}',true,false,21);
  $play[Side::Home][8] = Player::initial('Carroll',null);
  $ps->battingOrder(Side::Home, 8, $play[Side::Home][8], Position::position('B2'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Abreu","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Escobar","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Carroll","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[],"results":[]}}',true,false,22);
  $play[Side::Home][9] = Player::initial('Vazquez',null);
  $ps->pitcher(Side::Home, $play[Side::Home][9]);
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Abreu","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Escobar","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Carroll","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Vazquez","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]}}',true,true,23);
  TestPS::checkSituation($ps,1,Side::Visitor,0,0,0,0,0,0,0,null,null,null,true,24);
  $ps->s1();
  TestPS::checkSituation($ps,1,Side::Visitor,0,0,0,1,0,0,0,null,null,$play[Side::Visitor][0],false,25);
  $ps->s1();
  TestPS::checkSituation($ps,1,Side::Visitor,0,0,0,2,0,0,0,null,$play[Side::Visitor][0],$play[Side::Visitor][1],false,26);
  $ps->k();
  TestPS::checkSituation($ps,1,Side::Visitor,1,0,0,2,0,0,0,null,$play[Side::Visitor][0],$play[Side::Visitor][1],false,27);
  $ps->s1();
  TestPS::checkSituation($ps,1,Side::Visitor,1,0,0,3,0,0,0,$play[Side::Visitor][0],$play[Side::Visitor][1],$play[Side::Visitor][3],false,28);
  $ps->fo('9',1,0,0,-1);
  TestPS::checkSituation($ps,1,Side::Visitor,2,1,0,3,0,0,0,null,$play[Side::Visitor][1],$play[Side::Visitor][3],false,29);
  $ps->fo0('7');
  TestPS::checkSituation($ps,1,Side::Home,0,1,0,3,0,0,0,null,null,null,true,30);
  $ps->gb1('43');
  TestPS::checkSituation($ps,1,Side::Home,1,1,0,3,0,0,0,null,null,null,false,31);
  $ps->gb1('63');
  TestPS::checkSituation($ps,1,Side::Home,2,1,0,3,0,0,0,null,null,null,false,32);
  $ps->bb();
  TestPS::checkSituation($ps,1,Side::Home,2,1,0,3,0,0,0,null,null,$play[Side::Home][2],false,33);
  $ps->po1('13');
  TestPS::checkSituation($ps,2,Side::Visitor,0,1,0,3,0,0,0,null,null,null,true,34);
  // 2
  $ps->fo0('5');
  TestPS::checkSituation($ps,2,Side::Visitor,1,1,0,3,0,0,0,null,null,null,false,35);
  $ps->fo0('8');
  TestPS::checkSituation($ps,2,Side::Visitor,2,1,0,3,0,0,0,null,null,null,false,36);
  $ps->gb1('63');
  TestPS::checkSituation($ps,2,Side::Home,0,1,0,3,0,0,0,null,null,null,true,37);
  $ps->fo0('8');
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,0,0,0,null,null,null,false,38);
  $ps->s1();
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,1,0,0,null,null,$play[Side::Home][4],false,39);
  $ps->cs1('2-6');
  TestPS::checkSituation($ps,2,Side::Home,2,1,0,3,1,0,0,null,null,null,false,40);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,1,0,0,null,null,$play[Side::Home][4],false,41);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,0,0,0,null,null,null,false,42);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Home,0,1,0,3,0,0,0,null,null,null,true,43);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Visitor,2,1,0,3,0,0,0,null,null,null,false,44);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Visitor,1,1,0,3,0,0,0,null,null,null,false,45);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Visitor,0,1,0,3,0,0,0,null,null,null,true,46);
  $ps->undo();
  TestPS::checkSituation($ps,1,Side::Home,2,1,0,3,0,0,0,null,null,$play[Side::Home][2],false,47);
  $ps->undo();
  TestPS::checkSituation($ps,1,Side::Home,2,1,0,3,0,0,0,null,null,null,false,48);
  $ps->bb();
  TestPS::checkSituation($ps,1,Side::Home,2,1,0,3,0,0,0,null,null,$play[Side::Home][2],false,49);
  $ps->po1('13');
  TestPS::checkSituation($ps,2,Side::Visitor,0,1,0,3,0,0,0,null,null,null,true,50);
  // 2 again
  $ps->fo0('5');
  TestPS::checkSituation($ps,2,Side::Visitor,1,1,0,3,0,0,0,null,null,null,false,51);
  $ps->fo0('8');
  TestPS::checkSituation($ps,2,Side::Visitor,2,1,0,3,0,0,0,null,null,null,false,52);
  $ps->gb1('63');
  TestPS::checkSituation($ps,2,Side::Home,0,1,0,3,0,0,0,null,null,null,true,53);
  $ps->fo0('8');
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,0,0,0,null,null,null,false,54);
  $ps->s1();
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,1,0,0,null,null,$play[Side::Home][4],false,55);
  $ps->cs1('2-6');
  TestPS::checkSituation($ps,2,Side::Home,2,1,0,3,1,0,0,null,null,null,false,56);
  $ps->s1();
  TestPS::checkSituation($ps,2,Side::Home,2,1,0,3,2,0,0,null,null,$play[Side::Home][5],false,57);
  $ps->s(0,0,2,2);
  TestPS::checkSituation($ps,2,Side::Home,2,1,0,3,3,0,0,$play[Side::Home][5],$play[Side::Home][6],null,false,58);
  $ps->k();
  TestPS::checkSituation($ps,3,Side::Visitor,0,1,0,3,3,0,0,null,null,null,true,59);
  // 3
  $ps->k();
  TestPS::checkSituation($ps,3,Side::Visitor,1,1,0,3,3,0,0,null,null,null,false,60);
  $ps->bb();
  TestPS::checkSituation($ps,3,Side::Visitor,1,1,0,3,3,0,0,null,null,$play[Side::Visitor][1],false,61);
  $ps->fo0('4');
  TestPS::checkSituation($ps,3,Side::Visitor,2,1,0,3,3,0,0,null,null,$play[Side::Visitor][1],false,62);
  $ps->fo0('7');
  TestPS::checkSituation($ps,3,Side::Home,0,1,0,3,3,0,0,null,null,null,true,63);
  $ps->bb();
  TestPS::checkSituation($ps,3,Side::Home,0,1,0,3,3,0,0,null,null,$play[Side::Home][8],false,64);
  $ps->gb1('31');
  TestPS::checkSituation($ps,3,Side::Home,1,1,0,3,3,0,0,null,$play[Side::Home][8],null,false,65);
  $ps->k();
  TestPS::checkSituation($ps,3,Side::Home,2,1,0,3,3,0,0,null,$play[Side::Home][8],null,false,66);
  $ps->t();
  TestPS::checkSituation($ps,3,Side::Home,2,1,1,3,4,0,0,$play[Side::Home][2],null,null,false,67);
  $ps->bb();
  TestPS::checkSituation($ps,3,Side::Home,2,1,1,3,4,0,0,$play[Side::Home][2],null,$play[Side::Home][3],false,68);
  $ps->s1();
  TestPS::checkSituation($ps,3,Side::Home,2,1,2,3,5,0,0,null,$play[Side::Home][3],$play[Side::Home][4],false,69);
  $ps->d(0,2,3,3);
  TestPS::checkSituation($ps,3,Side::Home,2,1,4,3,6,0,0,$play[Side::Home][5],null,null,false,70);
  $ps->wp1();
  TestPS::checkSituation($ps,3,Side::Home,2,1,5,3,6,0,0,null,null,null,false,71);
  $ps->gb1('63');
  TestPS::checkSituation($ps,4,Side::Visitor,0,1,5,3,6,0,0,null,null,null,true,72);
  // 4
  $ps->k();
  TestPS::checkSituation($ps,4,Side::Visitor,1,1,5,3,6,0,0,null,null,null,false,73);
  $ps->k();
  TestPS::checkSituation($ps,4,Side::Visitor,2,1,5,3,6,0,0,null,null,null,false,74);
  $ps->fo0('2');
  TestPS::checkSituation($ps,4,Side::Home,0,1,5,3,6,0,0,null,null,null,true,75);
  $ps->s1();
  TestPS::checkSituation($ps,4,Side::Home,0,1,5,3,7,0,0,null,null,$play[Side::Home][7],false,76);
  $ps->sac1('31');
  TestPS::checkSituation($ps,4,Side::Home,1,1,5,3,7,0,0,null,$play[Side::Home][7],null,false,77);
  $ps->fo0('8');
  TestPS::checkSituation($ps,4,Side::Home,2,1,5,3,7,0,0,null,$play[Side::Home][7],null,false,78);
  $ps->fo0('8');
  TestPS::checkSituation($ps,5,Side::Visitor,0,1,5,3,7,0,0,null,null,null,true,79);
  // 5
  $ps->gb1('63');
  TestPS::checkSituation($ps,5,Side::Visitor,1,1,5,3,7,0,0,null,null,null,false,80);
  $ps->bb();
  TestPS::checkSituation($ps,5,Side::Visitor,1,1,5,3,7,0,0,null,null,$play[Side::Visitor][8],false,81);
  $ps->s(0,0,2,2);
  TestPS::checkSituation($ps,5,Side::Visitor,1,1,5,4,7,0,0,$play[Side::Visitor][8],$play[Side::Visitor][0],null,false,82);
  $ps->gb('43',1,1,0,-1);
  TestPS::checkSituation($ps,5,Side::Visitor,2,2,5,4,7,0,0,$play[Side::Visitor][0],null,null,false,83);
  $ps->s1();
  TestPS::checkSituation($ps,5,Side::Visitor,2,3,5,5,7,0,0,null,null,$play[Side::Visitor][2],false,84);
  $ps->fo0('4');
  TestPS::checkSituation($ps,5,Side::Home,0,3,5,5,7,0,0,null,null,null,true,85);
  $ps->gb1('43');
  TestPS::checkSituation($ps,5,Side::Home,1,3,5,5,7,0,0,null,null,null,false,86);
  $ps->k();
  TestPS::checkSituation($ps,5,Side::Home,2,3,5,5,7,0,0,null,null,null,false,87);
  $ps->fo0('7');
  TestPS::checkSituation($ps,6,Side::Visitor,0,3,5,5,7,0,0,null,null,null,true,88);
  $ps->s1();
  TestPS::checkSituation($ps,6,Side::Visitor,0,3,5,6,7,0,0,null,null,$play[Side::Visitor][4],false,89);
  $ps->k();
  TestPS::checkSituation($ps,6,Side::Visitor,1,3,5,6,7,0,0,null,null,$play[Side::Visitor][4],false,90);
  $ps->fo0('7');
  TestPS::checkSituation($ps,6,Side::Visitor,2,3,5,6,7,0,0,null,null,$play[Side::Visitor][4],false,91);
  $ps->fo0('4');
  TestPS::checkSituation($ps,6,Side::Home,0,3,5,6,7,0,0,null,null,null,true,92);
  $play[Side::Visitor][10] = Player::initial('Villanueva',null);
  $ps->pitcher(Side::Visitor, $play[Side::Visitor][10]);
  $ps->gb1('63');
  TestPS::checkSituation($ps,6,Side::Home,1,3,5,6,7,0,0,null,null,null,false,93);
  $ps->s1();
  TestPS::checkSituation($ps,6,Side::Home,1,3,5,6,8,0,0,null,null,$play[Side::Home][6],false,94);
  $ps->fo0('7');
  TestPS::checkSituation($ps,6,Side::Home,2,3,5,6,8,0,0,null,null,$play[Side::Home][6],false,95);
  $ps->fo0('7');
  TestPS::checkSituation($ps,7,Side::Visitor,0,3,5,6,8,0,0,null,null,null,true,96);
  $ps->gb1('63');
  TestPS::checkSituation($ps,7,Side::Visitor,1,3,5,6,8,0,0,null,null,null,false,97);
  $ps->fo0('8');
  TestPS::checkSituation($ps,7,Side::Visitor,2,3,5,6,8,0,0,null,null,null,false,98);
  $ps->k();
  TestPS::checkSituation($ps,7,Side::Home,0,3,5,6,8,0,0,null,null,null,true,99);
  $ps->gb1('53');
  TestPS::checkSituation($ps,7,Side::Home,1,3,5,6,8,0,0,null,null,null,false,100);
  $ps->gb1('43');
  TestPS::checkSituation($ps,7,Side::Home,2,3,5,6,8,0,0,null,null,null,false,101);
  $ps->s1();
  TestPS::checkSituation($ps,7,Side::Home,2,3,5,6,9,0,0,null,null,$play[Side::Home][2],false,102);
  $ps->sb1();
  TestPS::checkSituation($ps,7,Side::Home,2,3,5,6,9,0,0,null,$play[Side::Home][2],null,false,103);
  $ps->k();
  TestPS::checkSituation($ps,8,Side::Visitor,0,3,5,6,9,0,0,null,null,null,true,104);
  $play[Side::Home][10] = Player::initial('Johnson',null);
  $ps->battingOrder(Side::Home, 3, $play[Side::Home][10], Position::position('B1'));
  #$ps->pitcher('Adams');
  $play[Side::Home][11] = Player::initial('Adams',null);
  $ps->pitcher(Side::Home, $play[Side::Home][11]);
  $ps->fo0('4');
  TestPS::checkSituation($ps,8,Side::Visitor,1,3,5,6,9,0,0,null,null,null,false,105);
  $ps->hr();
  TestPS::checkSituation($ps,8,Side::Visitor,1,4,5,7,9,0,0,null,null,null,false,106);
  $ps->k();
  TestPS::checkSituation($ps,8,Side::Visitor,2,4,5,7,9,0,0,null,null,null,false,107);
  $ps->k();
  TestPS::checkSituation($ps,8,Side::Home,0,4,5,7,9,0,0,null,null,null,true,108);
  $play[Side::Visitor][11] = Player::initial('Mujica',null);
  #$ps->pitcher(Side::Visitor, $play[Side::Visitor][11]);
  $ps->pitcher(Side::Visitor, $play[Side::Visitor][11]);
  $ps->hr();
  TestPS::checkSituation($ps,8,Side::Home,0,4,6,7,10,0,0,null,null,null,false,109);
  $play[Side::Home][12] = Player::initial('Cruz',null);
  #$ps->battingOrder(Side::Home, 6, $play[Side::Home][12], Position::position('PH'));
  $ps->hitter(Side::Home,$play[Side::Home][12]);
  $ps->gb1('63');
  TestPS::checkSituation($ps,8,Side::Home,1,4,6,7,10,0,0,null,null,null,false,110);
  $ps->k();
  TestPS::checkSituation($ps,8,Side::Home,2,4,6,7,10,0,0,null,null,null,false,111);
  $ps->k();
  TestPS::checkSituation($ps,9,Side::Visitor,0,4,6,7,10,0,0,null,null,null,true,112);
  TestPS::checkAssert(! $ps->lineupValid(Side::Home),113);
    try {
      $ps->move(Side::Home,5,Position::position('DH'));
    } catch (Exception $ex) {
      print "Should not get here - 114: " . $ex->getMessage() . "\n";
      exit;
    }
  TestPS::checkAssert($ps->lineupValid(Side::Home),115);
  $play[Side::Home][13] = Player::initial('Rivera',null);
  $ps->pitcher(Side::Home,$play[Side::Home][13]);
  $play[Side::Visitor][12] = Player::initial('Presley',null);
  $ps->hitter(Side::Visitor,$play[Side::Visitor][12]);
  $ps->gb1('31');
  TestPS::checkSituation($ps,9,Side::Visitor,1,4,6,7,10,0,0,null,null,null,false,116);
  $ps->d2();
  TestPS::checkSituation($ps,9,Side::Visitor,1,4,6,8,10,0,0,null,$play[Side::Visitor][7],null,false,117);
  $ps->fo0('4');
  TestPS::checkSituation($ps,9,Side::Visitor,2,4,6,8,10,0,0,null,$play[Side::Visitor][7],null,false,118);
  $ps->wp1();
  TestPS::checkSituation($ps,9,Side::Visitor,2,4,6,8,10,0,0,$play[Side::Visitor][7],null,null,false,119);
  TestPS::checkAssert(! $ps->getSituation()->gameOver(),120);
  #$ps->debugOn();
  $ps->fo0('8');
  TestPS::checkSituation($ps,9,Side::Home,0,4,6,8,10,0,0,null,null,null,true,121,true);
  try {
    $g1 = $ps->toString();
    $ps = ProjectScoresheet::fromString($g1);
    $g2 = $ps->toString();
    TestPS::checkAssert($g1 == $g2,122);
  } catch (Exception $ex) {
    print('Should not get here 123: ' + $ex->getMessage() . "\n");
    print $g1 . "\n";
    print $g2 . "\n";
    exit;
  }
  TestPS::checkSituation($ps,9,Side::Home,0,4,6,8,10,0,0,null,null,null,true,124,true);
////      out.println(g2);
////    ProjectScoresheetUtilities.generateOds(ps);
//
//    ps = new ProjectScoresheet();
//    ps.assignTeam(Situation.Side.Visitor,'Seattle','WS1');
//    ps.assignTeam(Situation.Side.Home,'Pittsburgh','WS1');
//    ps.dateOfGame('20140122');
//    ps.battingOrder(Situation.Side.Visitor, 1, 'Santana,Carlos', Position.Positions.C);
//    ps.battingOrder(Situation.Side.Visitor, 2, 'Fowler,Dexter', Position.Positions.CF);
//    ps.battingOrder(Situation.Side.Visitor, 3, 'Gonzalez,Adrian', Position.Positions.B1);
//    ps.battingOrder(Situation.Side.Visitor, 4, 'Braun,Ryan', Position.Positions.LF);
//    ps.battingOrder(Situation.Side.Visitor, 5, 'Craig,Allen', Position.Positions.DH);
//    ps.battingOrder(Situation.Side.Visitor, 6, 'Zobrist,Ben', Position.Positions.SS);
//    ps.battingOrder(Situation.Side.Visitor, 7, 'Zimmerman,Ryan', Position.Positions.B3);
//    ps.battingOrder(Situation.Side.Visitor, 8, 'Ellis,Mark', Position.Positions.B2);
//    ps.battingOrder(Situation.Side.Visitor, 9, 'Rios,Alex', Position.Positions.RF);
//    ps.pitcher(Situation.Side.Visitor, 'Lohse,Kyle');
//    assert(ps.lineupValid(Situation.Side.Visitor));
//    ps.battingOrder(Situation.Side.Home, 1, 'Bernadina,Roger', Position.Positions.LF);
//    ps.battingOrder(Situation.Side.Home, 2, 'Venable,Max', Position.Positions.RF);
//    ps.battingOrder(Situation.Side.Home, 3, 'McCutcheon,Andrew', Position.Positions.CF);
//    ps.battingOrder(Situation.Side.Home, 4, 'Morneau,Justin', Position.Positions.B1);
//    ps.battingOrder(Situation.Side.Home, 5, 'Willingham,Josh', Position.Positions.DH);
//    ps.battingOrder(Situation.Side.Home, 6, 'Betemit,Wilson', Position.Positions.B3);
//    ps.battingOrder(Situation.Side.Home, 7, 'Walker,Neil', Position.Positions.B2);
//    ps.battingOrder(Situation.Side.Home, 8, 'Thole,Josh', Position.Positions.C);
//    ps.battingOrder(Situation.Side.Home, 9, 'Hardy,J.J.', Position.Positions.SS);
//    ps.pitcher(Situation.Side.Home, 'Gonzalez,Gio');
//    assert(ps.lineupValid(Situation.Side.Home));
//    checkSituation(ps,1,Side.Visitor,0,0,0,0,0,0,0,stub,stub,stub,true);
//    ps.fo('6');
//    checkSituation(ps,1,Side.Visitor,1,0,0,0,0,0,0,stub,stub,stub,false);
//    ps.bb();
//    checkSituation(ps,1,Side.Visitor,1,0,0,0,0,0,0,stub,stub,'Fowler,Dexter',false);
//    ps.bb();
//    checkSituation(ps,1,Side.Visitor,1,0,0,0,0,0,0,stub,'Fowler,Dexter','Gonzalez,Adrian',false);
//    ps.k();
//    checkSituation(ps,1,Side.Visitor,2,0,0,0,0,0,0,stub,'Fowler,Dexter','Gonzalez,Adrian',false);
//    ps.fo('5');
//    checkSituation(ps,1,Side.Home,0,0,0,0,0,0,0,stub,stub,stub,true);
//    ps.s1();
//    checkSituation(ps,1,Side.Home,0,0,0,0,1,0,0,stub,stub,'Bernadina,Roger',false);
//    ps.gb('3-6', 0, 0, -1, 1);
//    checkSituation(ps,1,Side.Home,1,0,0,0,1,0,0,stub,stub,'Venable,Max',false);
//    ps.fo('6');
//    checkSituation(ps,1,Side.Home,2,0,0,0,1,0,0,stub,stub,'Venable,Max',false);
//    ps.bb();
//    checkSituation(ps,1,Side.Home,2,0,0,0,1,0,0,stub,'Venable,Max','Morneau,Justin',false);
//    ps.fo('9');
//    checkSituation(ps,2,Side.Visitor,0,0,0,0,1,0,0,stub,stub,stub,true);
//    ps.bb();
//    checkSituation(ps,2,Side.Visitor,0,0,0,0,1,0,0,stub,stub,'Zobrist,Ben',false);
//    ps.k();
//    checkSituation(ps,2,Side.Visitor,1,0,0,0,1,0,0,stub,stub,'Zobrist,Ben',false);
//    ps.fo('7');
//    checkSituation(ps,2,Side.Visitor,2,0,0,0,1,0,0,stub,stub,'Zobrist,Ben',false);
//    ps.d2();
//    checkSituation(ps,2,Side.Visitor,2,0,0,1,1,0,0,'Zobrist,Ben','Rios,Alex',stub,false);
//    ps.gb('5-3',0,0,0,-1);
//    checkSituation(ps,2,Side.Home,0,0,0,1,1,0,0,stub,stub,stub,true);
//    ps.fo('6');
//    checkSituation(ps,2,Side.Home,1,0,0,1,1,0,0,stub,stub,stub,false);
//    ps.fo('9');
//    checkSituation(ps,2,Side.Home,2,0,0,1,1,0,0,stub,stub,stub,false);
//    ps.s1();
//    checkSituation(ps,2,Side.Home,2,0,0,1,2,0,0,stub,stub,'Thole,Josh',false);
//    ps.gb('6-3');
//    checkSituation(ps,3,Side.Visitor,0,0,0,1,2,0,0,stub,stub,stub,true);
//    ps.s1();
//    checkSituation(ps,3,Side.Visitor,0,0,0,2,2,0,0,stub,stub,'Fowler,Dexter',false);
//    ps.fo('8');
//    checkSituation(ps,3,Side.Visitor,1,0,0,2,2,0,0,stub,stub,'Fowler,Dexter',false);
//    ps.d3();
//    checkSituation(ps,3,Side.Visitor,1,1,0,3,2,0,0,stub,'Braun,Ryan',stub,false);
//    ps.k();
//    checkSituation(ps,3,Side.Visitor,2,1,0,3,2,0,0,stub,'Braun,Ryan',stub,false);
//    ps.d2();
//    checkSituation(ps,3,Side.Visitor,2,2,0,4,2,0,0,stub,'Zobrist,Ben',stub,false);
//    ps.hbp();
//    ps.runner(7, 'Chisenhall,Lonnie');
//    checkSituation(ps,3,Side.Visitor,2,2,0,4,2,0,0,stub,'Zobrist,Ben','Chisenhall,Lonnie',false);
//    ps.pb();
//    checkSituation(ps,3,Side.Visitor,2,2,0,4,2,0,0,'Zobrist,Ben','Chisenhall,Lonnie',stub,false);
//    ps.fo('6');
//    try{ps.move(Side.Visitor, 7, Position.Positions.B3);}catch (Exception ex) {ex.printStackTrace();System.exit(-1);}
//    checkSituation(ps,3,Side.Home,0,2,0,4,2,0,0,stub,stub,stub,true);
//    ps.s1();
//    checkSituation(ps,3,Side.Home,0,2,0,4,3,0,0,stub,stub,'Bernadina,Roger',false);
//    ps.fo('6');
//    checkSituation(ps,3,Side.Home,1,2,0,4,3,0,0,stub,stub,'Bernadina,Roger',false);
//    ps.fo('5');
//    checkSituation(ps,3,Side.Home,2,2,0,4,3,0,0,stub,stub,'Bernadina,Roger',false);
//    ps.k();
//    checkSituation(ps,4,Side.Visitor,0,2,0,4,3,0,0,stub,stub,stub,true);
//    ps.fo('7');
//    checkSituation(ps,4,Side.Visitor,1,2,0,4,3,0,0,stub,stub,stub,false);
//    ps.k();
//    checkSituation(ps,4,Side.Visitor,2,2,0,4,3,0,0,stub,stub,stub,false);
//    ps.bb();
//    checkSituation(ps,4,Side.Visitor,2,2,0,4,3,0,0,stub,stub,'Fowler,Dexter',false);
//    ps.d2();
//    checkSituation(ps,4,Side.Visitor,2,2,0,5,3,0,0,'Fowler,Dexter','Gonzalez,Adrian',stub,false);
//    ps.k();
//    checkSituation(ps,4,Side.Home,0,2,0,5,3,0,0,stub,stub,stub,true);
//    ps.k();
//    checkSituation(ps,4,Side.Home,1,2,0,5,3,0,0,stub,stub,stub,false);
//    ps.fo('7');
//    checkSituation(ps,4,Side.Home,2,2,0,5,3,0,0,stub,stub,stub,false);
//    ps.fo('7');
//    checkSituation(ps,5,Side.Visitor,0,2,0,5,3,0,0,stub,stub,stub,true);
//    ps.fo('8');
//    checkSituation(ps,5,Side.Visitor,1,2,0,5,3,0,0,stub,stub,stub,false);
//    ps.gb('4-3');
//    checkSituation(ps,5,Side.Visitor,2,2,0,5,3,0,0,stub,stub,stub,false);
//    ps.fo('4');
//    checkSituation(ps,5,Side.Home,0,2,0,5,3,0,0,stub,stub,stub,true);
//    ps.bb();
//    checkSituation(ps,5,Side.Home,0,2,0,5,3,0,0,stub,stub,'Thole,Josh',false);
//    ps.fo('6');
//    checkSituation(ps,5,Side.Home,1,2,0,5,3,0,0,stub,stub,'Thole,Josh',false);
//    ps.s1();
//    checkSituation(ps,5,Side.Home,1,2,0,5,4,0,0,stub,'Thole,Josh','Bernadina,Roger',false);
//    ps.bb();
//    checkSituation(ps,5,Side.Home,1,2,0,5,4,0,0,'Thole,Josh','Bernadina,Roger','Venable,Max',false);
//    ps.fo('8');
//    checkSituation(ps,5,Side.Home,2,2,0,5,4,0,0,'Thole,Josh','Bernadina,Roger','Venable,Max',false);
//    ps.d3();
//    checkSituation(ps,5,Side.Home,2,2,3,5,5,0,0,stub,'Morneau,Justin',stub,false);
//    ps.bb();
//    checkSituation(ps,5,Side.Home,2,2,3,5,5,0,0,stub,'Morneau,Justin','Willingham,Josh',false);
//    ps.fo('6');
//    checkSituation(ps,6,Side.Visitor,0,2,3,5,5,0,0,stub,stub,stub,true);
//    ps.fo('7');
//    checkSituation(ps,6,Side.Visitor,1,2,3,5,5,0,0,stub,stub,stub,false);
//    ps.fo('8');
//    checkSituation(ps,6,Side.Visitor,2,2,3,5,5,0,0,stub,stub,stub,false);
//    ps.s1();
//    checkSituation(ps,6,Side.Visitor,2,2,3,6,5,0,0,stub,stub,'Santana,Carlos',false);
//    ps.k();
//    checkSituation(ps,6,Side.Home,0,2,3,6,5,0,0,stub,stub,stub,true);
//    ps.gb('6-3');
//    checkSituation(ps,6,Side.Home,1,2,3,6,5,0,0,stub,stub,stub,false);
//    ps.bb();
//    checkSituation(ps,6,Side.Home,1,2,3,6,5,0,0,stub,stub,'Thole,Josh',false);
//    ps.fo('8');
//    checkSituation(ps,6,Side.Home,2,2,3,6,5,0,0,stub,stub,'Thole,Josh',false);
//    ps.battingOrder(1, 'Rodriguez,Alex', Position.Positions.PH);
//    ps.s2();
//    checkSituation(ps,6,Side.Home,2,2,3,6,6,0,0,'Thole,Josh',stub,'Rodriguez,Alex',false);
//    ps.e('E4');
//    checkSituation(ps,6,Side.Home,2,2,4,6,6,0,0,stub,'Rodriguez,Alex','Venable,Max',false);
//    ps.k();
//    try {ps.move(Side.Home, 1, Position.Positions.B3);} catch (Exception ex) {ex.printStackTrace();System.exit(-2);}
//    ps.battingOrder(Side.Home, 6, 'Mastroianni,Darin', Position.Positions.LF);
//    assert(ps.lineupValid(Side.Home));
//    checkSituation(ps,7,Side.Visitor,0,2,4,6,6,0,0,stub,stub,stub,true);
//    ps.k();
//    checkSituation(ps,7,Side.Visitor,1,2,4,6,6,0,0,stub,stub,stub,false);
//    ps.s1();
//    checkSituation(ps,7,Side.Visitor,1,2,4,7,6,0,0,stub,stub,'Braun,Ryan',false);
//    ps.gb('5-4-3',0,0,-1,-1);
//    checkSituation(ps,7,Side.Home,0,2,4,7,6,0,0,stub,stub,stub,true);
//    ps.k();
//    checkSituation(ps,7,Side.Home,1,2,4,7,6,0,0,stub,stub,stub,false);
//    ps.fo('7');
//    checkSituation(ps,7,Side.Home,2,2,4,7,6,0,0,stub,stub,stub,false);
//    ps.k();
//    checkSituation(ps,8,Side.Visitor,0,2,4,7,6,0,0,stub,stub,stub,true);
//    ps.pitcher('Mujica,Edward');
//    ps.fo('9');
//    checkSituation(ps,8,Side.Visitor,1,2,4,7,6,0,0,stub,stub,stub,false);
//    ps.fo('9');
//    checkSituation(ps,8,Side.Visitor,2,2,4,7,6,0,0,stub,stub,stub,false);
//    ps.gb('6-3');
//    checkSituation(ps,8,Side.Home,0,2,4,7,6,0,0,stub,stub,stub,true);
//    ps.pitcher('Clippard,Tyler');
//    ps.fo('5');
//    checkSituation(ps,8,Side.Home,1,2,4,7,6,0,0,stub,stub,stub,false);
//    ps.d2();
//    checkSituation(ps,8,Side.Home,1,2,4,7,7,0,0,stub,'Thole,Josh',stub,false);
//    ps.k();
//    checkSituation(ps,8,Side.Home,2,2,4,7,7,0,0,stub,'Thole,Josh',stub,false);
//    ps.gb('6-3');
//    checkSituation(ps,9,Side.Visitor,0,2,4,7,7,0,0,stub,stub,stub,true);
//    ps.pitcher('Betancourt,Rafael');
//    ps.battingOrder(9, 'Jaso,John', Position.Positions.PH);
//    ps.fo('8');
//    checkSituation(ps,9,Side.Visitor,1,2,4,7,7,0,0,stub,stub,stub,false);
//    ps.battingOrder(1, 'DeJesus,David', Position.Positions.PH);
//    ps.gb('6-3');
//    checkSituation(ps,9,Side.Visitor,2,2,4,7,7,0,0,stub,stub,stub,false);
//    assert(!ps.getSituation().gameOver());
//    ps.fo('6');
//    checkSituation(ps,9,Side.Home,0,2,4,7,7,0,0,stub,stub,stub,true);
//    assert(ps.getSituation().gameOver());
//    //out.println(ps);
//    String ws1i_to_s = ps.toString();
//    ps.setOutputMode('new');
//    String ws1i_to_y = ps.toString();
//    //out.println(ws1i_to_y);
//    String ws1s = 'Seattle(WS1)\n' +
//        'Santana,Carlos~C~0.0~OOG~36.37;DeJesus,David~PH~36.37:Fowler,Dexter~CF~0.0:Gonzalez,Adrian~B1~0.0:Braun,Ryan~LF~0.0:Craig,Allen~DH~0.0:Zobrist,Ben~SS~0.0:Zimmerman,Ryan~B3~0.0~OOG~16.9;Chisenhall,Lonnie~PR~16.9~B3~17.9:Ellis,Mark~B2~0.0:Rios,Alex~RF~0.0~OOG~35.37;Jaso,John~PH~35.37:Lohse,Kyle~P~0.0~OOG~35.33;Clippard,Tyler~P~35.33\n' +
//        '~6~;~BB~B-1;~BB~1-2,B-1;~K~;~5~;~BB~B-1;~K~;~7~;~D~1-3,B-2;~5-3~;~S~B-1;~8~;~D~1-H,B-2;~K~;~D~2-H,B-2;~HBP~B-1;2-3,1-2/PB~6~;~7~;~K~;~BB~B-1;~D~1-3,B-2;~K~;~8~;~4-3~;~4~;~7~;~8~;~S~B-1;~K~;~K~;~S~B-1;~5-4-3/DP~1x2,Bx1;~9~;~9~;~6-3~;~8~;~6-3~;~6~\n' +
//        'Pittsburgh(WS1)\n' +
//        'Bernadina,Roger~LF~0.0~OOG~29.27;Rodriguez,Alex~PH~29.27~B3~29.30:Venable,Max~RF~0.0:McCutcheon,Andrew~CF~0.0:Morneau,Justin~B1~0.0:Willingham,Josh~DH~0.0:Betemit,Wilson~B3~0.0~OOG~29.30;Mastroianni,Darin~LF~29.30:Walker,Neil~B2~0.0:Thole,Josh~C~0.0:Hardy,J.J.~SS~0.0:Gonzalez,Gio~P~0.0~OOG~32.33;Mujica,Edward~P~32.33~OOG~35.37;Betancourt,Rafael~P~35.37\n' +
//        '~S~B-1;~3-6~1x2,B-1;~6~;~BB~1-2,B-1;~9~;~6~;~9~;~S~B-1;~6-3~;~S~B-1;~6~;~5~;~K~;~K~;~7~;~7~;~BB~B-1;~6~;~S~1-2,B-1;~BB~2-3,1-2,B-1;~8~;~D~3-H,2-H,1-H,B-2;~BB~B-1;~6~;~6-3~;~BB~B-1;~8~;~S~1-3,B-1;~E4~3-H,1-2,B-1;~K~;~K~;~7~;~K~;~5~;~D~B-2;~K~;~6-3~\n' +
//        '20140122~~';
//    try {
//      ps = new ProjectScoresheet(ws1s);
//    } catch (InvalidProjectScoresheetException e) {
//      e.printStackTrace();
//      System.exit(-1);
//    }
//    String ws1s_to_s = ps.toString();
//    ps.setOutputMode('new');
//    String ws1s_to_y = ps.toString();
//    compareStrings(ws1i_to_s, ws1s);
//    compareStrings(ws1i_to_s, ws1s_to_s);
//    compareStrings(ws1i_to_y, ws1s_to_y);
//    //out.println(ws1s_to_y);
//    String ws1y = '{"visitor":{"name": Seattle,"gameNumber": WS1,"lineup":\n' +
//        '   -\n      - name: Santana,Carlos\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n      - name: DeJesus,David\n        positions:\n         - position: PH\n           visitor: 36\n           home: 37\n' +
//        '   -\n      - name: Fowler,Dexter\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: Gonzalez,Adrian\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: Braun,Ryan\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: Craig,Allen\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: Zobrist,Ben\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: Zimmerman,Ryan\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n      - name: Chisenhall,Lonnie\n        positions:\n         - position: PR\n           visitor: 16\n           home: 9\n         - position: B3\n           visitor: 17\n           home: 9\n' +
//        '   -\n      - name: Ellis,Mark\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: Rios,Alex\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n      - name: Jaso,John\n        positions:\n         - position: PH\n           visitor: 35\n           home: 37\n' +
//        '   rotation:\n      - name: Lohse,Kyle\n        visitor: 0\n        home: 0\n      - name: Clippard,Tyler\n        visitor: 35\n        home: 33\n' +
//        '   results:\n   - during: 6\n   - during: BB\n     after: B-1\n   - during: BB\n     after: 1-2,B-1\n   - during: K\n   - during: 5\n   - during: BB\n     after: B-1\n' +
//        '   - during: K\n   - during: 7\n   - during: D\n     after: 1-3,B-2\n   - during: 5-3\n   - during: S\n     after: B-1\n   - during: 8\n   - during: D\n     after: 1-H,B-2\n' +
//        '   - during: K\n   - during: D\n     after: 2-H,B-2\n   - during: HBP\n     after: B-1\n   - before: 2-3,1-2/PB\n     during: 6\n   - during: 7\n   - during: K\n   - during: BB\n     after: B-1\n' +
//        '   - during: D\n     after: 1-3,B-2\n   - during: K\n   - during: 8\n   - during: 4-3\n   - during: 4\n   - during: 7\n   - during: 8\n   - during: S\n     after: B-1\n' +
//        '   - during: K\n   - during: K\n   - during: S\n     after: B-1\n   - during: 5-4-3/DP\n     after: 1x2,Bx1\n   - during: 9\n   - during: 9\n   - during: 6-3\n   - during: 8\n   - during: 6-3\n   - during: 6\n' +
//        'home:{"name": Pittsburgh,"gameNumber": WS1,"lineup":\n'+
//        '   -\n      - name: Bernadina,Roger\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n      - name: Rodriguez,Alex\n        positions:\n         - position: PH\n           visitor: 29\n           home: 27\n         - position: B3\n           visitor: 29\n           home: 30\n' +
//        '   -\n      - name: Venable,Max\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: McCutcheon,Andrew\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: Morneau,Justin\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: Willingham,Josh\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: Betemit,Wilson\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n      - name: Mastroianni,Darin\n        positions:\n         - position: LF\n           visitor: 29\n           home: 30\n' +
//        '   -\n      - name: Walker,Neil\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: Thole,Josh\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n' +
//        '   -\n      - name: Hardy,J.J.\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
//        '   rotation:\n      - name: Gonzalez,Gio\n        visitor: 0\n        home: 0\n      - name: Mujica,Edward\n        visitor: 32\n        home: 33\n      - name: Betancourt,Rafael\n        visitor: 35\n        home: 37\n' +
//        '   results:\n   - during: S\n     after: B-1\n   - during: 3-6\n     after: 1x2,B-1\n   - during: 6\n   - during: BB\n     after: 1-2,B-1\n   - during: 9\n   - during: 6\n   - during: 9\n   - during: S\n     after: B-1\n' +
//        '   - during: 6-3\n   - during: S\n     after: B-1\n   - during: 6\n   - during: 5\n   - during: K\n   - during: K\n   - during: 7\n   - during: 7\n   - during: BB\n     after: B-1\n' +
//        '   - during: 6\n   - during: S\n     after: 1-2,B-1\n   - during: BB\n     after: 2-3,1-2,B-1\n   - during: 8\n   - during: D\n     after: 3-H,2-H,1-H,B-2\n   - during: BB\n     after: B-1\n   - during: 6\n   - during: 6-3\n' +
//        '   - during: BB\n     after: B-1\n   - during: 8\n   - during: S\n     after: 1-3,B-1\n   - during: E4\n     after: 3-H,1-2,B-1\n   - during: K\n   - during: K\n   - during: 7\n   - during: K\n   - during: 5\n   - during: D\n     after: B-2\n   - during: K\n   - during: 6-3\n' +
//        'date: 20140122\n';
////    String ws2y = '{"visitor":{"name": Seattle,"gameNumber": WS2,"lineup":\n' +
////        '   -\n      - name: Fowler,Dexter\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
////        '   -\n      - name: Jaso,John\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n      - name: Kratz,Erik\n        positions:\n         - position: PH\n           visitor: 38\n           home: 26\n         - position: C\n           visitor: 43\n           home: 26\n' +
////        '   -\n      - name: Moss,Brandon\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n      - name: Santana,Carlos\n        positions:\n         - position: DH\n           visitor: 39\n           home:26\n' +
////        '   -\n      - name: Zobrist,Ben\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
////        '   -\n      - name: Craig,Allen\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n      - name: Gonzalez,Adrian\n        positions:\n         - position: B1\n           visitor: 43\n           home: 26\n' +
////        '   -\n      - name: Braun,Ryan\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n      - name: DeJesus,David\n        positions:\n         - position: LF\n           visitor: 43\n           home: 26\n' +
////        '   -\n      - name: Rollins,Jimmy\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
////        '   -\n      - name: Rios,Alex\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n' +
////        '   -\n      - name: Chisenhall,Lonnie\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n' +
//////        '   rotation:\n      - name: Lohse,Kyle\n        visitor: 0\n        home: 0\n      - name: Clippard,Tyler\n        visitor: 35\n        home: 33\n' +
//////        '   results:\n   - during: 6\n   - during: BB\n     after: B-1\n   - during: BB\n     after: 1-2,B-1\n   - during: K\n   - during: 5\n   - during: BB\n     after: B-1\n' +
//////        '   - during: K\n   - during: 7\n   - during: D\n     after: 1-3,B-2\n   - during: 5-3\n   - during: S\n     after: B-1\n   - during: 8\n   - during: D\n     after: 1-H,B-2\n' +
//////        '   - during: K\n   - during: D\n     after: 2-H,B-2\n   - during: HBP\n     after: B-1\n   - before: 2-3,1-2/PB\n     during: 6\n   - during: 7\n   - during: K\n   - during: BB\n     after: B-1\n' +
//////        '   - during: D\n     after: 1-3,B-2\n   - during: K\n   - during: 8\n   - during: 4-3\n   - during: 4\n   - during: 7\n   - during: 8\n   - during: S\n     after: B-1\n' +
//////        '   - during: K\n   - during: K\n   - during: S\n     after: B-1\n   - during: 5-4-3/DP\n     after: 1x2,Bx1\n   - during: 9\n   - during: 9\n   - during: 6-3\n   - during: 8\n   - during: 6-3\n   - during: 6\n' +
//////        'home:{"name": Pittsburgh,"gameNumber": WS1,"lineup":\n'+
//////        '   -\n      - name: Bernadina,Roger\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n      - name: Rodriguez,Alex\n        positions:\n         - position: PH\n           visitor: 27\n           home: 29\n         - position: B3\n           visitor: 30\n           home: 29\n' +
//////        '   -\n      - name: Venable,Max\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: McCutcheon,Andrew\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Morneau,Justin\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Willingham,Josh\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Betemit,Wilson\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n      - name: Mastroianni,Darin\n        positions:\n         - position: LF\n           visitor: 30\n           home: 29\n' +
//////        '   -\n      - name: Walker,Neil\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Thole,Josh\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Hardy,J.J.\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
//////        '   rotation:\n      - name: Gonzalez,Gio\n        visitor: 0\n        home: 0\n      - name: Mujica,Edward\n        visitor: 33\n        home: 32\n      - name: Betancourt,Rafael\n        visitor: 37\n        home: 35\n' +
//////        '   results:\n   - during: S\n     after: B-1\n   - during: 3-6\n     after: 1x2,B-1\n   - during: 6\n   - during: BB\n     after: 1-2,B-1\n   - during: 9\n   - during: 6\n   - during: 9\n   - during: S\n     after: B-1\n' +
//////        '   - during: 6-3\n   - during: S\n     after: B-1\n   - during: 6\n   - during: 5\n   - during: K\n   - during: K\n   - during: 7\n   - during: 7\n   - during: BB\n     after: B-1\n' +
//////        '   - during: 6\n   - during: S\n     after: 1-2,B-1\n   - during: BB\n     after: 2-3,1-2,B-1\n   - during: 8\n   - during: D\n     after: 3-H,2-H,1-H,B-2\n   - during: BB\n     after: B-1\n   - during: 6\n   - during: 6-3\n' +
//////        '   - during: BB\n     after: B-1\n   - during: 8\n   - during: S\n     after: 1-3,B-1\n   - during: E4\n     after: 3-H,1-2,B-1\n   - during: K\n   - during: K\n   - during: 7\n   - during: K\n   - during: 5\n   - during: D\n     after: B-2\n   - during: K\n   - during: 6-3\n' +
//////        'date: 20140122\n';
//////    String ws3y = '{"visitor":{"name": Seattle,"gameNumber": WS1,"lineup":\n' +
//////        '   -\n      - name: Santana,Carlos\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n      - name: DeJesus,David\n        positions:\n         - position: PH\n           visitor: 36\n           home: 37\n' +
//////        '   -\n      - name: Fowler,Dexter\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Gonzalez,Adrian\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Braun,Ryan\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Craig,Allen\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Zobrist,Ben\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Zimmerman,Ryan\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n      - name: Chisenhall,Lonnie\n        positions:\n         - position: PR\n           visitor: 16\n           home: 9\n         - position: B3\n           visitor: 17\n           home: 9\n' +
//////        '   -\n      - name: Ellis,Mark\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Rios,Alex\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n      - name: Jaso,John\n        positions:\n         - position: PH\n           visitor: 35\n           home: 37\n' +
//////        '   rotation:\n      - name: Lohse,Kyle\n        visitor: 0\n        home: 0\n      - name: Clippard,Tyler\n        visitor: 35\n        home: 33\n' +
//////        '   results:\n   - during: 6\n   - during: BB\n     after: B-1\n   - during: BB\n     after: 1-2,B-1\n   - during: K\n   - during: 5\n   - during: BB\n     after: B-1\n' +
//////        '   - during: K\n   - during: 7\n   - during: D\n     after: 1-3,B-2\n   - during: 5-3\n   - during: S\n     after: B-1\n   - during: 8\n   - during: D\n     after: 1-H,B-2\n' +
//////        '   - during: K\n   - during: D\n     after: 2-H,B-2\n   - during: HBP\n     after: B-1\n   - before: 2-3,1-2/PB\n     during: 6\n   - during: 7\n   - during: K\n   - during: BB\n     after: B-1\n' +
//////        '   - during: D\n     after: 1-3,B-2\n   - during: K\n   - during: 8\n   - during: 4-3\n   - during: 4\n   - during: 7\n   - during: 8\n   - during: S\n     after: B-1\n' +
//////        '   - during: K\n   - during: K\n   - during: S\n     after: B-1\n   - during: 5-4-3/DP\n     after: 1x2,Bx1\n   - during: 9\n   - during: 9\n   - during: 6-3\n   - during: 8\n   - during: 6-3\n   - during: 6\n' +
//////        'home:{"name": Pittsburgh,"gameNumber": WS1,"lineup":\n'+
//////        '   -\n      - name: Bernadina,Roger\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n      - name: Rodriguez,Alex\n        positions:\n         - position: PH\n           visitor: 27\n           home: 29\n         - position: B3\n           visitor: 30\n           home: 29\n' +
//////        '   -\n      - name: Venable,Max\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: McCutcheon,Andrew\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Morneau,Justin\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Willingham,Josh\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Betemit,Wilson\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n      - name: Mastroianni,Darin\n        positions:\n         - position: LF\n           visitor: 30\n           home: 29\n' +
//////        '   -\n      - name: Walker,Neil\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Thole,Josh\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Hardy,J.J.\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
//////        '   rotation:\n      - name: Gonzalez,Gio\n        visitor: 0\n        home: 0\n      - name: Mujica,Edward\n        visitor: 33\n        home: 32\n      - name: Betancourt,Rafael\n        visitor: 37\n        home: 35\n' +
//////        '   results:\n   - during: S\n     after: B-1\n   - during: 3-6\n     after: 1x2,B-1\n   - during: 6\n   - during: BB\n     after: 1-2,B-1\n   - during: 9\n   - during: 6\n   - during: 9\n   - during: S\n     after: B-1\n' +
//////        '   - during: 6-3\n   - during: S\n     after: B-1\n   - during: 6\n   - during: 5\n   - during: K\n   - during: K\n   - during: 7\n   - during: 7\n   - during: BB\n     after: B-1\n' +
//////        '   - during: 6\n   - during: S\n     after: 1-2,B-1\n   - during: BB\n     after: 2-3,1-2,B-1\n   - during: 8\n   - during: D\n     after: 3-H,2-H,1-H,B-2\n   - during: BB\n     after: B-1\n   - during: 6\n   - during: 6-3\n' +
//////        '   - during: BB\n     after: B-1\n   - during: 8\n   - during: S\n     after: 1-3,B-1\n   - during: E4\n     after: 3-H,1-2,B-1\n   - during: K\n   - during: K\n   - during: 7\n   - during: K\n   - during: 5\n   - during: D\n     after: B-2\n   - during: K\n   - during: 6-3\n' +
//////        'date: 20140122\n';
//////    String ws4y = '{"visitor":{"name": Seattle,"gameNumber": WS1,"lineup":\n' +
//////        '   -\n      - name: Santana,Carlos\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n      - name: DeJesus,David\n        positions:\n         - position: PH\n           visitor: 36\n           home: 37\n' +
//////        '   -\n      - name: Fowler,Dexter\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Gonzalez,Adrian\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Braun,Ryan\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Craig,Allen\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Zobrist,Ben\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Zimmerman,Ryan\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n      - name: Chisenhall,Lonnie\n        positions:\n         - position: PR\n           visitor: 16\n           home: 9\n         - position: B3\n           visitor: 17\n           home: 9\n' +
//////        '   -\n      - name: Ellis,Mark\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Rios,Alex\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n      - name: Jaso,John\n        positions:\n         - position: PH\n           visitor: 35\n           home: 37\n' +
//////        '   rotation:\n      - name: Lohse,Kyle\n        visitor: 0\n        home: 0\n      - name: Clippard,Tyler\n        visitor: 35\n        home: 33\n' +
//////        '   results:\n   - during: 6\n   - during: BB\n     after: B-1\n   - during: BB\n     after: 1-2,B-1\n   - during: K\n   - during: 5\n   - during: BB\n     after: B-1\n' +
//////        '   - during: K\n   - during: 7\n   - during: D\n     after: 1-3,B-2\n   - during: 5-3\n   - during: S\n     after: B-1\n   - during: 8\n   - during: D\n     after: 1-H,B-2\n' +
//////        '   - during: K\n   - during: D\n     after: 2-H,B-2\n   - during: HBP\n     after: B-1\n   - before: 2-3,1-2/PB\n     during: 6\n   - during: 7\n   - during: K\n   - during: BB\n     after: B-1\n' +
//////        '   - during: D\n     after: 1-3,B-2\n   - during: K\n   - during: 8\n   - during: 4-3\n   - during: 4\n   - during: 7\n   - during: 8\n   - during: S\n     after: B-1\n' +
//////        '   - during: K\n   - during: K\n   - during: S\n     after: B-1\n   - during: 5-4-3/DP\n     after: 1x2,Bx1\n   - during: 9\n   - during: 9\n   - during: 6-3\n   - during: 8\n   - during: 6-3\n   - during: 6\n' +
//////        'home:{"name": Pittsburgh,"gameNumber": WS1,"lineup":\n'+
//////        '   -\n      - name: Bernadina,Roger\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n      - name: Rodriguez,Alex\n        positions:\n         - position: PH\n           visitor: 27\n           home: 29\n         - position: B3\n           visitor: 30\n           home: 29\n' +
//////        '   -\n      - name: Venable,Max\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: McCutcheon,Andrew\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Morneau,Justin\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Willingham,Josh\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Betemit,Wilson\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n      - name: Mastroianni,Darin\n        positions:\n         - position: LF\n           visitor: 30\n           home: 29\n' +
//////        '   -\n      - name: Walker,Neil\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Thole,Josh\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Hardy,J.J.\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
//////        '   rotation:\n      - name: Gonzalez,Gio\n        visitor: 0\n        home: 0\n      - name: Mujica,Edward\n        visitor: 33\n        home: 32\n      - name: Betancourt,Rafael\n        visitor: 37\n        home: 35\n' +
//////        '   results:\n   - during: S\n     after: B-1\n   - during: 3-6\n     after: 1x2,B-1\n   - during: 6\n   - during: BB\n     after: 1-2,B-1\n   - during: 9\n   - during: 6\n   - during: 9\n   - during: S\n     after: B-1\n' +
//////        '   - during: 6-3\n   - during: S\n     after: B-1\n   - during: 6\n   - during: 5\n   - during: K\n   - during: K\n   - during: 7\n   - during: 7\n   - during: BB\n     after: B-1\n' +
//////        '   - during: 6\n   - during: S\n     after: 1-2,B-1\n   - during: BB\n     after: 2-3,1-2,B-1\n   - during: 8\n   - during: D\n     after: 3-H,2-H,1-H,B-2\n   - during: BB\n     after: B-1\n   - during: 6\n   - during: 6-3\n' +
//////        '   - during: BB\n     after: B-1\n   - during: 8\n   - during: S\n     after: 1-3,B-1\n   - during: E4\n     after: 3-H,1-2,B-1\n   - during: K\n   - during: K\n   - during: 7\n   - during: K\n   - during: 5\n   - during: D\n     after: B-2\n   - during: K\n   - during: 6-3\n' +
//////        'date: 20140122\n';
//////    String ws5y = '{"visitor":{"name": Seattle,"gameNumber": WS1,"lineup":\n' +
//////        '   -\n      - name: Santana,Carlos\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n      - name: DeJesus,David\n        positions:\n         - position: PH\n           visitor: 36\n           home: 37\n' +
//////        '   -\n      - name: Fowler,Dexter\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Gonzalez,Adrian\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Braun,Ryan\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Craig,Allen\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Zobrist,Ben\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Zimmerman,Ryan\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n      - name: Chisenhall,Lonnie\n        positions:\n         - position: PR\n           visitor: 16\n           home: 9\n         - position: B3\n           visitor: 17\n           home: 9\n' +
//////        '   -\n      - name: Ellis,Mark\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Rios,Alex\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n      - name: Jaso,John\n        positions:\n         - position: PH\n           visitor: 35\n           home: 37\n' +
//////        '   rotation:\n      - name: Lohse,Kyle\n        visitor: 0\n        home: 0\n      - name: Clippard,Tyler\n        visitor: 35\n        home: 33\n' +
//////        '   results:\n   - during: 6\n   - during: BB\n     after: B-1\n   - during: BB\n     after: 1-2,B-1\n   - during: K\n   - during: 5\n   - during: BB\n     after: B-1\n' +
//////        '   - during: K\n   - during: 7\n   - during: D\n     after: 1-3,B-2\n   - during: 5-3\n   - during: S\n     after: B-1\n   - during: 8\n   - during: D\n     after: 1-H,B-2\n' +
//////        '   - during: K\n   - during: D\n     after: 2-H,B-2\n   - during: HBP\n     after: B-1\n   - before: 2-3,1-2/PB\n     during: 6\n   - during: 7\n   - during: K\n   - during: BB\n     after: B-1\n' +
//////        '   - during: D\n     after: 1-3,B-2\n   - during: K\n   - during: 8\n   - during: 4-3\n   - during: 4\n   - during: 7\n   - during: 8\n   - during: S\n     after: B-1\n' +
//////        '   - during: K\n   - during: K\n   - during: S\n     after: B-1\n   - during: 5-4-3/DP\n     after: 1x2,Bx1\n   - during: 9\n   - during: 9\n   - during: 6-3\n   - during: 8\n   - during: 6-3\n   - during: 6\n' +
//////        'home:{"name": Pittsburgh,"gameNumber": WS1,"lineup":\n'+
//////        '   -\n      - name: Bernadina,Roger\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n      - name: Rodriguez,Alex\n        positions:\n         - position: PH\n           visitor: 27\n           home: 29\n         - position: B3\n           visitor: 30\n           home: 29\n' +
//////        '   -\n      - name: Venable,Max\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: McCutcheon,Andrew\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Morneau,Justin\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Willingham,Josh\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Betemit,Wilson\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n      - name: Mastroianni,Darin\n        positions:\n         - position: LF\n           visitor: 30\n           home: 29\n' +
//////        '   -\n      - name: Walker,Neil\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Thole,Josh\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Hardy,J.J.\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
//////        '   rotation:\n      - name: Gonzalez,Gio\n        visitor: 0\n        home: 0\n      - name: Mujica,Edward\n        visitor: 33\n        home: 32\n      - name: Betancourt,Rafael\n        visitor: 37\n        home: 35\n' +
//////        '   results:\n   - during: S\n     after: B-1\n   - during: 3-6\n     after: 1x2,B-1\n   - during: 6\n   - during: BB\n     after: 1-2,B-1\n   - during: 9\n   - during: 6\n   - during: 9\n   - during: S\n     after: B-1\n' +
//////        '   - during: 6-3\n   - during: S\n     after: B-1\n   - during: 6\n   - during: 5\n   - during: K\n   - during: K\n   - during: 7\n   - during: 7\n   - during: BB\n     after: B-1\n' +
//////        '   - during: 6\n   - during: S\n     after: 1-2,B-1\n   - during: BB\n     after: 2-3,1-2,B-1\n   - during: 8\n   - during: D\n     after: 3-H,2-H,1-H,B-2\n   - during: BB\n     after: B-1\n   - during: 6\n   - during: 6-3\n' +
//////        '   - during: BB\n     after: B-1\n   - during: 8\n   - during: S\n     after: 1-3,B-1\n   - during: E4\n     after: 3-H,1-2,B-1\n   - during: K\n   - during: K\n   - during: 7\n   - during: K\n   - during: 5\n   - during: D\n     after: B-2\n   - during: K\n   - during: 6-3\n' +
//////        'date: 20140122\n';
//////    String ws6y = '{"visitor":{"name": Seattle,"gameNumber": WS1,"lineup":\n' +
//////        '   -\n      - name: Santana,Carlos\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n      - name: DeJesus,David\n        positions:\n         - position: PH\n           visitor: 36\n           home: 37\n' +
//////        '   -\n      - name: Fowler,Dexter\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Gonzalez,Adrian\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Braun,Ryan\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Craig,Allen\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Zobrist,Ben\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Zimmerman,Ryan\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n      - name: Chisenhall,Lonnie\n        positions:\n         - position: PR\n           visitor: 16\n           home: 9\n         - position: B3\n           visitor: 17\n           home: 9\n' +
//////        '   -\n      - name: Ellis,Mark\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Rios,Alex\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n      - name: Jaso,John\n        positions:\n         - position: PH\n           visitor: 35\n           home: 37\n' +
//////        '   rotation:\n      - name: Lohse,Kyle\n        visitor: 0\n        home: 0\n      - name: Clippard,Tyler\n        visitor: 35\n        home: 33\n' +
//////        '   results:\n   - during: 6\n   - during: BB\n     after: B-1\n   - during: BB\n     after: 1-2,B-1\n   - during: K\n   - during: 5\n   - during: BB\n     after: B-1\n' +
//////        '   - during: K\n   - during: 7\n   - during: D\n     after: 1-3,B-2\n   - during: 5-3\n   - during: S\n     after: B-1\n   - during: 8\n   - during: D\n     after: 1-H,B-2\n' +
//////        '   - during: K\n   - during: D\n     after: 2-H,B-2\n   - during: HBP\n     after: B-1\n   - before: 2-3,1-2/PB\n     during: 6\n   - during: 7\n   - during: K\n   - during: BB\n     after: B-1\n' +
//////        '   - during: D\n     after: 1-3,B-2\n   - during: K\n   - during: 8\n   - during: 4-3\n   - during: 4\n   - during: 7\n   - during: 8\n   - during: S\n     after: B-1\n' +
//////        '   - during: K\n   - during: K\n   - during: S\n     after: B-1\n   - during: 5-4-3/DP\n     after: 1x2,Bx1\n   - during: 9\n   - during: 9\n   - during: 6-3\n   - during: 8\n   - during: 6-3\n   - during: 6\n' +
//////        'home:{"name": Pittsburgh,"gameNumber": WS1,"lineup":\n'+
//////        '   -\n      - name: Bernadina,Roger\n        positions:\n         - position: LF\n           visitor: 0\n           home: 0\n      - name: Rodriguez,Alex\n        positions:\n         - position: PH\n           visitor: 27\n           home: 29\n         - position: B3\n           visitor: 30\n           home: 29\n' +
//////        '   -\n      - name: Venable,Max\n        positions:\n         - position: RF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: McCutcheon,Andrew\n        positions:\n         - position: CF\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Morneau,Justin\n        positions:\n         - position: B1\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Willingham,Josh\n        positions:\n         - position: DH\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Betemit,Wilson\n        positions:\n         - position: B3\n           visitor: 0\n           home: 0\n      - name: Mastroianni,Darin\n        positions:\n         - position: LF\n           visitor: 30\n           home: 29\n' +
//////        '   -\n      - name: Walker,Neil\n        positions:\n         - position: B2\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Thole,Josh\n        positions:\n         - position: C\n           visitor: 0\n           home: 0\n' +
//////        '   -\n      - name: Hardy,J.J.\n        positions:\n         - position: SS\n           visitor: 0\n           home: 0\n' +
//////        '   rotation:\n      - name: Gonzalez,Gio\n        visitor: 0\n        home: 0\n      - name: Mujica,Edward\n        visitor: 33\n        home: 32\n      - name: Betancourt,Rafael\n        visitor: 37\n        home: 35\n' +
//////        '   results:\n   - during: S\n     after: B-1\n   - during: 3-6\n     after: 1x2,B-1\n   - during: 6\n   - during: BB\n     after: 1-2,B-1\n   - during: 9\n   - during: 6\n   - during: 9\n   - during: S\n     after: B-1\n' +
//////        '   - during: 6-3\n   - during: S\n     after: B-1\n   - during: 6\n   - during: 5\n   - during: K\n   - during: K\n   - during: 7\n   - during: 7\n   - during: BB\n     after: B-1\n' +
//////        '   - during: 6\n   - during: S\n     after: 1-2,B-1\n   - during: BB\n     after: 2-3,1-2,B-1\n   - during: 8\n   - during: D\n     after: 3-H,2-H,1-H,B-2\n   - during: BB\n     after: B-1\n   - during: 6\n   - during: 6-3\n' +
//////        '   - during: BB\n     after: B-1\n   - during: 8\n   - during: S\n     after: 1-3,B-1\n   - during: E4\n     after: 3-H,1-2,B-1\n   - during: K\n   - during: K\n   - during: 7\n   - during: K\n   - during: 5\n   - during: D\n     after: B-2\n   - during: K\n   - during: 6-3\n' +
//////        'date: 20140122\n';
//    try {
//      ps = new ProjectScoresheet(ws1y);
//    } catch (InvalidProjectScoresheetException e) {
//      e.printStackTrace();
//      System.exit(-1);
//    }
//    String ws1y_to_s = ps.toString();
//    ps.setOutputMode('new');
//    String ws1y_to_y = ps.toString();
////    out.println(ws1y_to_y);
////    out.println(ws1y);
//    compareStrings(ws1i_to_y,ws1y);
//    compareStrings(ws1i_to_s,ws1y_to_s);
//    compareStrings(ws1i_to_y,ws1y_to_y);
////    ps.generateOds();
////    ProjectScoresheetUtilities.generateOds(ps);
//    String s2014_001 = 'SDZ(001)\nKipnis, Jason~B2~0.0:Jay, Jon~CF~0.0:Martinez, Victor~C~0.0:Holliday, Matt~LF~0.0:Alonzo, Yonder~B3~0.0:Scutaro, Marco~DH~0.0:Cruz. Nelson~RF~0.0:Young, Michael~B3~0.0:Escobar, Yunel~SS~0.0:Wainwright, Adam~P~0.0\n'+
//        '~7~;~S~B-1;~S~1-2,B-1;~K~;~S~2-H,1-3,B-1;~S~3-H,1-3,B-1;~S~3-H,1-3,B-1;~D~3-H,1-H,B-2;~S~2-H,B-1;~S~1-2,B-1;~6~;~3-1~;~4-3~;~S~B-1;~S~1-2,B-1;~HR~2-H,1-H,B-H;~S~B-1;~6~;~3-1~;~BB~B-1;~3-1~1-2,Bx1;~D~2-H,B-2;~3-1~;~1-3~;~6-3~;~3~;~6-3~;~K~;~S~B-1;1-2/WP~T~2-H,B-3;~6-3~;~6-3~;~7~;~K~;~BB~B-1;~BB~1-2,B-1;~S~2-H,1-3,B-1;~HBP~1-2,B-1;~3~;~6~;~K~;~D~B-2;~S~2-3,B-1;~6~;~6-4-3/DP~1x2,Bx1;~K~;~9~;~BB~B-1;~K~\n'+
//        'PIT(001)\nMarte, Starling~LF~0.0:Infante, Omar~B2~0.0:McCutchen, Andrew~CF~0.0:Morneau, Justin~B1~0.0:Donaldson, Josh~B3~0.0:Blackmon, Charlie~RF~0.0:Tejada, Miguel~DH~0.0:Navarro, Dioner~C~0.0:Hardy, JJ~SS~0.0:Tillman, Chris~P~0.0\n'+
//        '~7~;~4~;~4-3~;~S~B-1;~K~;~K~;~8~;~K~;~HR~B-H;~7~;~6~;~6-3~;~3-1~;~K~;~4-3~;~K~;~K~;~7~;~K~;~6-3~;~S~B-1;~BB~1-2,B-1;~6-4-3/DP~2-3,1x2,Bx1;~8~;~S~B-1;~S~1-3,B-1;~7/SF~3-H,Bx;~K~;~7~;~K~;~8~;~S~B-1;~6-3~\n'+
//        '20140302~~\n';
////    try {
////      ps = new ProjectScoresheet(s2014_001);
////    } catch (InvalidProjectScoresheetException e) {
////      e.printStackTrace();
////      System.exit(-1);
////    }
////    ProjectScoresheetUtilities.generateOds(ps);
//    try {
//      byte[] ba = Files.readAllBytes(Paths.get('SDZ001PIT001.pss'));
//      ps = new ProjectScoresheet(new String(ba));
//      assert(ps.toString().equals(s2014_001));
//      ProjectScoresheetUtilities.generateOds(ps);
//    } catch (Exception e) {
//      e.printStackTrace();
//    }
//
//
//    out.println('Successful Test');
//  }


//  if ($ps->toString() !== ProjectScoresheet::fromString($ps->toString())->toString()) {
//    print 'Error 4\n';
//    print $ps->toString();
//    print ProjectScoresheet::fromString($ps->toString())->toString();
//    exit;
//  }
  
  $ps = new ProjectScoresheet;
  TestPs::checkExpected($ps,'{"visitor":{"name":"","gameNumber":"0","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"","gameNumber":"0","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,1);
  $ps->assignTeam(Side::Visitor,'Pittsburgh','001');
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"","gameNumber":"0","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,1);
  $ps->assignTeam(Side::Home,'San Diego','001');
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,3);
  $play = array();
  $play[Side::Visitor] = array();
  $play[Side::Visitor][0] = Player::initial('Morgan',null);
  $ps->battingOrder(Side::Visitor, 0, $play[Side::Visitor][0], Position::position('RF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,4);
  $play[Side::Visitor][1] = Player::initial('McCutchen',null);
  $ps->battingOrder(Side::Visitor, 1, $play[Side::Visitor][1], Position::position('CF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,5);
  $play[Side::Visitor][2] = Player::initial('Betemit',null);
  $ps->battingOrder(Side::Visitor, 2, $play[Side::Visitor][2], Position::position('B3'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,6);
  $play[Side::Visitor][3] = Player::initial('Duda',null);
  $ps->battingOrder(Side::Visitor, 3, $play[Side::Visitor][3], Position::position('B1'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,7);
  $play[Side::Visitor][4] = Player::initial('Willingham',null);
  $ps->battingOrder(Side::Visitor, 4, $play[Side::Visitor][4], Position::position('DH'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,8);
  $play[Side::Visitor][5] = Player::initial('Tabata',null);
  $ps->battingOrder(Side::Visitor, 5, $play[Side::Visitor][5], Position::position('LF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,9);
  $play[Side::Visitor][6] = Player::initial('Scutaro',null);
  $ps->battingOrder(Side::Visitor, 6, $play[Side::Visitor][6], Position::position('SS'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,10);
  $play[Side::Visitor][7] = Player::initial('Thole',null);
  $ps->battingOrder(Side::Visitor, 7, $play[Side::Visitor][7], Position::position('C'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,11);
  $play[Side::Visitor][8] = Player::initial('Walker',null);
  $ps->battingOrder(Side::Visitor, 8, $play[Side::Visitor][8], Position::position('B2'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',false,false,12);
  $play[Side::Visitor][9] = Player::initial('Hudson',null);
  $ps->pitcher(Side::Visitor, $play[Side::Visitor][9]);
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',true,false,13);

  $play[Side::Home] = array();
  $play[Side::Home][0] = Player::initial('Bourne',null);
  $ps->battingOrder(Side::Home, 0, $play[Side::Home][0], Position::position('RF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',true,false,14);
  $play[Side::Home][1] = Player::initial('Jay',null);
  $ps->battingOrder(Side::Home, 1, $play[Side::Home][1], Position::position('CF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[],[]],"rotation":[],"results":[]}}',true,false,15);
  $play[Side::Home][2] = Player::initial('Murphy',null);
  $ps->battingOrder(Side::Home, 2, $play[Side::Home][2], Position::position('B3'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[],[]],"rotation":[],"results":[]}}',true,false,16);
  $play[Side::Home][3] = Player::initial('Holliday',null);
  $ps->battingOrder(Side::Home, 3, $play[Side::Home][3], Position::position('B1'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[],[]],"rotation":[],"results":[]}}',true,false,17);
  $play[Side::Home][4] = Player::initial('Young',null);
  $ps->battingOrder(Side::Home, 4, $play[Side::Home][4], Position::position('LF'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[],[],[],[]],"rotation":[],"results":[]}}',true,false,18);
  $play[Side::Home][5] = Player::initial('Abreu',null);
  $ps->battingOrder(Side::Home, 5, $play[Side::Home][5], Position::position('DH'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Abreu","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[],[],[]],"rotation":[],"results":[]}}',true,false,19);
  $play[Side::Home][6] = Player::initial('Escobar',null);
  $ps->battingOrder(Side::Home, 6, $play[Side::Home][6], Position::position('SS'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Abreu","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Escobar","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[],[]],"rotation":[],"results":[]}}',true,false,20);
  $play[Side::Home][7] = Player::initial('Buck',null);
  $ps->battingOrder(Side::Home, 7, $play[Side::Home][7], Position::position('C'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Abreu","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Escobar","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[]],"rotation":[],"results":[]}}',true,false,21);
  $play[Side::Home][8] = Player::initial('Carroll',null);
  $ps->battingOrder(Side::Home, 8, $play[Side::Home][8], Position::position('B2'));
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Abreu","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Escobar","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Carroll","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[],"results":[]}}',true,false,22);
  $play[Side::Home][9] = Player::initial('Vazquez',null);
  $ps->pitcher(Side::Home, $play[Side::Home][9]);
  TestPs::checkExpected($ps,'{"visitor":{"name":"Pittsburgh","gameNumber":"001","lineup":[[{"player":{"name":"Morgan","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"McCutchen","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Betemit","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Duda","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Willingham","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Tabata","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Scutaro","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Thole","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Walker","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Hudson","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]},"home":{"name":"San Diego","gameNumber":"001","lineup":[[{"player":{"name":"Bourne","positions":[{"position":{"pos":"RF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Jay","positions":[{"position":{"pos":"CF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Murphy","positions":[{"position":{"pos":"B3","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Holliday","positions":[{"position":{"pos":"B1","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Young","positions":[{"position":{"pos":"LF","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Abreu","positions":[{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Escobar","positions":[{"position":{"pos":"SS","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Buck","positions":[{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}]}}],[{"player":{"name":"Carroll","positions":[{"position":{"pos":"B2","when":{"visitor":"0","home":"0"}}}]}}]],"rotation":[{"player":{"name":"Vazquez","positions":[{"position":{"pos":"P","when":{"visitor":"0","home":"0"}}}]}}],"results":[]}}',true,true,23);
  TestPS::checkSituation($ps,1,Side::Visitor,0,0,0,0,0,0,0,null,null,null,true,24);
  $ps->s1();
  TestPS::checkSituation($ps,1,Side::Visitor,0,0,0,1,0,0,0,null,null,$play[Side::Visitor][0],false,25);
  $ps->s1();
  TestPS::checkSituation($ps,1,Side::Visitor,0,0,0,2,0,0,0,null,$play[Side::Visitor][0],$play[Side::Visitor][1],false,26);
  $ps->k();
  TestPS::checkSituation($ps,1,Side::Visitor,1,0,0,2,0,0,0,null,$play[Side::Visitor][0],$play[Side::Visitor][1],false,27);
  $ps->s1();
  TestPS::checkSituation($ps,1,Side::Visitor,1,0,0,3,0,0,0,$play[Side::Visitor][0],$play[Side::Visitor][1],$play[Side::Visitor][3],false,28);
  $ps->fo('9',1,0,0,-1);
  TestPS::checkSituation($ps,1,Side::Visitor,2,1,0,3,0,0,0,null,$play[Side::Visitor][1],$play[Side::Visitor][3],false,29);
  $ps->fo0('7');
  TestPS::checkSituation($ps,1,Side::Home,0,1,0,3,0,0,0,null,null,null,true,30);
  $ps->gb1('43');
  TestPS::checkSituation($ps,1,Side::Home,1,1,0,3,0,0,0,null,null,null,false,31);
  $ps->gb1('63');
  TestPS::checkSituation($ps,1,Side::Home,2,1,0,3,0,0,0,null,null,null,false,32);
  $ps->bb();
  TestPS::checkSituation($ps,1,Side::Home,2,1,0,3,0,0,0,null,null,$play[Side::Home][2],false,33);
  $ps->po1('13');
  TestPS::checkSituation($ps,2,Side::Visitor,0,1,0,3,0,0,0,null,null,null,true,34);
  // 2
  $ps->fo0('5');
  TestPS::checkSituation($ps,2,Side::Visitor,1,1,0,3,0,0,0,null,null,null,false,35);
  $ps->fo0('8');
  TestPS::checkSituation($ps,2,Side::Visitor,2,1,0,3,0,0,0,null,null,null,false,36);
  $ps->gb1('63');
  TestPS::checkSituation($ps,2,Side::Home,0,1,0,3,0,0,0,null,null,null,true,37);
  $ps->fo0('8');
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,0,0,0,null,null,null,false,38);
  $ps->s1();
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,1,0,0,null,null,$play[Side::Home][4],false,39);
  $ps->cs1('2-6');
  TestPS::checkSituation($ps,2,Side::Home,2,1,0,3,1,0,0,null,null,null,false,40);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,1,0,0,null,null,$play[Side::Home][4],false,41);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,0,0,0,null,null,null,false,42);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Home,0,1,0,3,0,0,0,null,null,null,true,43);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Visitor,2,1,0,3,0,0,0,null,null,null,false,44);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Visitor,1,1,0,3,0,0,0,null,null,null,false,45);
  $ps->undo();
  TestPS::checkSituation($ps,2,Side::Visitor,0,1,0,3,0,0,0,null,null,null,true,46);
  $ps->undo();
  TestPS::checkSituation($ps,1,Side::Home,2,1,0,3,0,0,0,null,null,$play[Side::Home][2],false,47);
  $ps->undo();
  TestPS::checkSituation($ps,1,Side::Home,2,1,0,3,0,0,0,null,null,null,false,48);
  $ps->bb();
  TestPS::checkSituation($ps,1,Side::Home,2,1,0,3,0,0,0,null,null,$play[Side::Home][2],false,49);
  $ps->po1('13');
  TestPS::checkSituation($ps,2,Side::Visitor,0,1,0,3,0,0,0,null,null,null,true,50);
  // 2 again
  $ps->fo0('5');
  TestPS::checkSituation($ps,2,Side::Visitor,1,1,0,3,0,0,0,null,null,null,false,51);
  $ps->fo0('8');
  TestPS::checkSituation($ps,2,Side::Visitor,2,1,0,3,0,0,0,null,null,null,false,52);
  $ps->gb1('63');
  TestPS::checkSituation($ps,2,Side::Home,0,1,0,3,0,0,0,null,null,null,true,53);
  $ps->fo0('8');
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,0,0,0,null,null,null,false,54);
  $ps->s1();
  TestPS::checkSituation($ps,2,Side::Home,1,1,0,3,1,0,0,null,null,$play[Side::Home][4],false,55);
  $ps->cs1('2-6');
  TestPS::checkSituation($ps,2,Side::Home,2,1,0,3,1,0,0,null,null,null,false,56);
  $ps->s1();
  TestPS::checkSituation($ps,2,Side::Home,2,1,0,3,2,0,0,null,null,$play[Side::Home][5],false,57);
  $ps->s(0,0,2,2);
  TestPS::checkSituation($ps,2,Side::Home,2,1,0,3,3,0,0,$play[Side::Home][5],$play[Side::Home][6],null,false,58);
  $ps->k();
  TestPS::checkSituation($ps,3,Side::Visitor,0,1,0,3,3,0,0,null,null,null,true,59);
  // 3
  $ps->k();
  TestPS::checkSituation($ps,3,Side::Visitor,1,1,0,3,3,0,0,null,null,null,false,60);
  $ps->bb();
  TestPS::checkSituation($ps,3,Side::Visitor,1,1,0,3,3,0,0,null,null,$play[Side::Visitor][1],false,61);
  $ps->fo0('4');
  TestPS::checkSituation($ps,3,Side::Visitor,2,1,0,3,3,0,0,null,null,$play[Side::Visitor][1],false,62);
  $ps->fo0('7');
  TestPS::checkSituation($ps,3,Side::Home,0,1,0,3,3,0,0,null,null,null,true,63);
  $ps->bb();
  TestPS::checkSituation($ps,3,Side::Home,0,1,0,3,3,0,0,null,null,$play[Side::Home][8],false,64);
  $ps->gb1('31');
  TestPS::checkSituation($ps,3,Side::Home,1,1,0,3,3,0,0,null,$play[Side::Home][8],null,false,65);
  $ps->k();
  TestPS::checkSituation($ps,3,Side::Home,2,1,0,3,3,0,0,null,$play[Side::Home][8],null,false,66);
  $ps->t();
  TestPS::checkSituation($ps,3,Side::Home,2,1,1,3,4,0,0,$play[Side::Home][2],null,null,false,67);
  $ps->bb();
  TestPS::checkSituation($ps,3,Side::Home,2,1,1,3,4,0,0,$play[Side::Home][2],null,$play[Side::Home][3],false,68);
  $ps->s1();
  TestPS::checkSituation($ps,3,Side::Home,2,1,2,3,5,0,0,null,$play[Side::Home][3],$play[Side::Home][4],false,69);
  $ps->d(0,2,3,3);
  TestPS::checkSituation($ps,3,Side::Home,2,1,4,3,6,0,0,$play[Side::Home][5],null,null,false,70);
  $ps->wp1();
  TestPS::checkSituation($ps,3,Side::Home,2,1,5,3,6,0,0,null,null,null,false,71);
  $ps->gb1('63');
  TestPS::checkSituation($ps,4,Side::Visitor,0,1,5,3,6,0,0,null,null,null,true,72);
  // 4
  $ps->k();
  TestPS::checkSituation($ps,4,Side::Visitor,1,1,5,3,6,0,0,null,null,null,false,73);
  $ps->k();
  TestPS::checkSituation($ps,4,Side::Visitor,2,1,5,3,6,0,0,null,null,null,false,74);
  $ps->fo0('2');
  TestPS::checkSituation($ps,4,Side::Home,0,1,5,3,6,0,0,null,null,null,true,75);
  $ps->s1();
  TestPS::checkSituation($ps,4,Side::Home,0,1,5,3,7,0,0,null,null,$play[Side::Home][7],false,76);
  $ps->sac1('31');
  TestPS::checkSituation($ps,4,Side::Home,1,1,5,3,7,0,0,null,$play[Side::Home][7],null,false,77);
  $ps->fo0('8');
  TestPS::checkSituation($ps,4,Side::Home,2,1,5,3,7,0,0,null,$play[Side::Home][7],null,false,78);
  $ps->fo0('8');
  TestPS::checkSituation($ps,5,Side::Visitor,0,1,5,3,7,0,0,null,null,null,true,79);
  // 5
  $ps->gb1('63');
  TestPS::checkSituation($ps,5,Side::Visitor,1,1,5,3,7,0,0,null,null,null,false,80);
  $ps->bb();
  TestPS::checkSituation($ps,5,Side::Visitor,1,1,5,3,7,0,0,null,null,$play[Side::Visitor][8],false,81);
  $ps->s(0,0,2,2);
  TestPS::checkSituation($ps,5,Side::Visitor,1,1,5,4,7,0,0,$play[Side::Visitor][8],$play[Side::Visitor][0],null,false,82);
  $ps->gb('43',1,1,0,-1);
  TestPS::checkSituation($ps,5,Side::Visitor,2,2,5,4,7,0,0,$play[Side::Visitor][0],null,null,false,83);
  $ps->s1();
  TestPS::checkSituation($ps,5,Side::Visitor,2,3,5,5,7,0,0,null,null,$play[Side::Visitor][2],false,84);
  $ps->fo0('4');
  TestPS::checkSituation($ps,5,Side::Home,0,3,5,5,7,0,0,null,null,null,true,85);
  $ps->gb1('43');
  TestPS::checkSituation($ps,5,Side::Home,1,3,5,5,7,0,0,null,null,null,false,86);
  $ps->k();
  TestPS::checkSituation($ps,5,Side::Home,2,3,5,5,7,0,0,null,null,null,false,87);
  $ps->fo0('7');
  TestPS::checkSituation($ps,6,Side::Visitor,0,3,5,5,7,0,0,null,null,null,true,88);
  $ps->s1();
  TestPS::checkSituation($ps,6,Side::Visitor,0,3,5,6,7,0,0,null,null,$play[Side::Visitor][4],false,89);
  $ps->k();
  TestPS::checkSituation($ps,6,Side::Visitor,1,3,5,6,7,0,0,null,null,$play[Side::Visitor][4],false,90);
  $ps->fo0('7');
  TestPS::checkSituation($ps,6,Side::Visitor,2,3,5,6,7,0,0,null,null,$play[Side::Visitor][4],false,91);
  $ps->fo0('4');
  TestPS::checkSituation($ps,6,Side::Home,0,3,5,6,7,0,0,null,null,null,true,92);
  $play[Side::Visitor][10] = Player::initial('Villanueva',null);
  $ps->pitcher(Side::Visitor, $play[Side::Visitor][10]);
  $ps->gb1('63');
  TestPS::checkSituation($ps,6,Side::Home,1,3,5,6,7,0,0,null,null,null,false,93);
  $ps->s1();
  TestPS::checkSituation($ps,6,Side::Home,1,3,5,6,8,0,0,null,null,$play[Side::Home][6],false,94);
  $ps->fo0('7');
  TestPS::checkSituation($ps,6,Side::Home,2,3,5,6,8,0,0,null,null,$play[Side::Home][6],false,95);
  $ps->fo0('7');
  TestPS::checkSituation($ps,7,Side::Visitor,0,3,5,6,8,0,0,null,null,null,true,96);
  $ps->gb1('63');
  TestPS::checkSituation($ps,7,Side::Visitor,1,3,5,6,8,0,0,null,null,null,false,97);
  $ps->fo0('8');
  TestPS::checkSituation($ps,7,Side::Visitor,2,3,5,6,8,0,0,null,null,null,false,98);
  $ps->k();
  TestPS::checkSituation($ps,7,Side::Home,0,3,5,6,8,0,0,null,null,null,true,99);
  $ps->gb1('53');
  TestPS::checkSituation($ps,7,Side::Home,1,3,5,6,8,0,0,null,null,null,false,100);
  $ps->gb1('43');
  TestPS::checkSituation($ps,7,Side::Home,2,3,5,6,8,0,0,null,null,null,false,101);
  $ps->s1();
  TestPS::checkSituation($ps,7,Side::Home,2,3,5,6,9,0,0,null,null,$play[Side::Home][2],false,102);
  $ps->sb1();
  TestPS::checkSituation($ps,7,Side::Home,2,3,5,6,9,0,0,null,$play[Side::Home][2],null,false,103);
  $ps->k();
  TestPS::checkSituation($ps,8,Side::Visitor,0,3,5,6,9,0,0,null,null,null,true,104);
  $play[Side::Home][10] = Player::initial('Johnson',null);
  $ps->battingOrder(Side::Home, 3, $play[Side::Home][10], Position::position('B1'));
  #$ps->pitcher('Adams');
  $play[Side::Home][11] = Player::initial('Adams',null);
  $ps->pitcher(Side::Home, $play[Side::Home][11]);
  $ps->fo0('4');
  TestPS::checkSituation($ps,8,Side::Visitor,1,3,5,6,9,0,0,null,null,null,false,105);
  $ps->hr();
  TestPS::checkSituation($ps,8,Side::Visitor,1,4,5,7,9,0,0,null,null,null,false,106);
  $ps->k();
  TestPS::checkSituation($ps,8,Side::Visitor,2,4,5,7,9,0,0,null,null,null,false,107);
  $ps->k();
  TestPS::checkSituation($ps,8,Side::Home,0,4,5,7,9,0,0,null,null,null,true,108);
  $play[Side::Visitor][11] = Player::initial('Mujica',null);
  #$ps->pitcher(Side::Visitor, $play[Side::Visitor][11]);
  $ps->pitcher(Side::Visitor, $play[Side::Visitor][11]);
  $ps->hr();
  TestPS::checkSituation($ps,8,Side::Home,0,4,6,7,10,0,0,null,null,null,false,109);
  $play[Side::Home][12] = Player::initial('Cruz',null);
  #$ps->battingOrder(Side::Home, 6, $play[Side::Home][12], Position::position('PH'));
  $ps->hitter(Side::Home,$play[Side::Home][12]);
  $ps->gb1('63');
  TestPS::checkSituation($ps,8,Side::Home,1,4,6,7,10,0,0,null,null,null,false,110);
  $ps->k();
  TestPS::checkSituation($ps,8,Side::Home,2,4,6,7,10,0,0,null,null,null,false,111);
  $ps->k();
  TestPS::checkSituation($ps,9,Side::Visitor,0,4,6,7,10,0,0,null,null,null,true,112);
  TestPS::checkAssert(! $ps->lineupValid(Side::Home),113);
    try {
      $ps->move(Side::Home,5,Position::position('DH'));
    } catch (Exception $ex) {
      print "Should not get here - 114: " . $ex->getMessage() . "\n";
      exit;
    }
  TestPS::checkAssert($ps->lineupValid(Side::Home),115);
  $play[Side::Home][13] = Player::initial('Rivera',null);
  $ps->pitcher(Side::Home,$play[Side::Home][13]);
  $play[Side::Visitor][12] = Player::initial('Presley',null);
  $ps->hitter(Side::Visitor,$play[Side::Visitor][12]);
  $ps->gb1('31');
  TestPS::checkSituation($ps,9,Side::Visitor,1,4,6,7,10,0,0,null,null,null,false,116);
  $ps->d2();
  TestPS::checkSituation($ps,9,Side::Visitor,1,4,6,8,10,0,0,null,$play[Side::Visitor][7],null,false,117);
  $ps->fo0('4');
  TestPS::checkSituation($ps,9,Side::Visitor,2,4,6,8,10,0,0,null,$play[Side::Visitor][7],null,false,118);
  $ps->wp1();
  TestPS::checkSituation($ps,9,Side::Visitor,2,4,6,8,10,0,0,$play[Side::Visitor][7],null,null,false,119);
  TestPS::checkAssert(! $ps->getSituation()->gameOver(),120);
  #$ps->debugOn();
  $ps->fo0('8');
  TestPS::checkSituation($ps,9,Side::Home,0,4,6,8,10,0,0,null,null,null,true,121,true);
  try {
    $g1 = $ps->toString();
    $ps = ProjectScoresheet::fromString($g1);
    $g2 = $ps->toString();
    TestPS::checkAssert($g1 == $g2,122);
  } catch (Exception $ex) {
    print('Should not get here 123: ' + $ex->getMessage() . "\n");
    print $g1 . "\n";
    print $g2 . "\n";
    exit;
  }
  TestPS::checkSituation($ps,9,Side::Home,0,4,6,8,10,0,0,null,null,null,true,124,true);
  print "Test successful\n";  

?>
