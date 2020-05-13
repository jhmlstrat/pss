<?php

require_once "../pss/Schedule.php";
require_once "../pss/Situation.php";
require_once "../pss/Rosters.php";
require_once "../pss/Result.php";

class ApiClass {

  private $config; //= {};

  function __construct() {
    $json = file_get_contents("../data/config.json");
    $this->config = json_decode($json, true);
    foreach ($this->config['years'] as $year) {
      $dir = "../data/" . $year['year'];
      if (!file_exists($dir) && !is_dir($dir)) {
        mkdir($dir);         
      } 
    }
  }

  function printConfig() {
    return print_r($this->config,true);
  }

  function testInjury() {
    $inj = new \Scoring\Injury;
    $expected = '{"injury":{"gameNumber":"0","duration":"0"}}';
    if ($inj->toString() !== $expected) {
      print "Error 1<br>";
      print $inj->toString();
      print $expected;
      exit;
    }
    $expected = '{"injury":{"gameNumber":"1","duration":"REM"}}';
    $inj = \Scoring\Injury::fromString($expected);
    if ($inj->toString() !== $expected) {
      print "Error 2<br>";
      print $inj->toString();
      print $expected;
      exit;
    }
    print "Test successful<br>";  
  }

  function testMove() {
    $move = new \Scoring\Move;
    $expected = '{"move":{"moveType":"Fm minors","gameNumber":"0"}}';
    if ($move->toString() !== $expected) {
      print "Error 1<br>";
      print $move->toString();
      print $expected;
      exit;
    }
    $expected = '{"move":{"moveType":"To minors","gameNumber":"1"}}';
    $move = \Scoring\Move::fromString($expected);
    if ($move->toString() !== $expected) {
      print "Error 2<br>";
      print $move->toString();
      print $expected;
      exit;
    }
    print "Test successful<br>";  
  }

  function testPlayer() {
    $play = new \ProjectScoresheet\Player;
    $expected = '{"player":{"name":""}}';
    if ($play->toString() !== $expected) {
      print "Error 1<br>";
      print $play->toString();
      print $expected;
      exit;
    }
    $play = \ProjectScoresheet\Player::initial('Test Player');
    $expected = '{"player":{"name":"Test Player"}}';
    if ($play->toString() !== $expected) {
      print "Error 2<br>";
      print $play->toString();
      print $expected;
      exit;
    }
    $s = $play->toString();
    $play = \ProjectScoresheet\Player::fromString($s);
    if ($play->toString() !== $expected) {
      print "Error 3<br>";
      print $play->toString();
      print $expected;
      exit;
    }
    $expected='{"player":{"name":"Test Player","strat":{"realTeam":"PIN","hand":"R","positionsPlayed":[{"position":{"pos":"LF","rating":"1","e":"2","arm":"-4","t":"","pb":""}},{"position":{"pos":"CF","rating":"3","e":"2","arm":"-4","t":"","pb":""}}],"ab":"309","bb":"","hitL":"9.9","hitR":"29.1","obL":"16.9","obR":"40.1","tbL":"9.9","tbR":"35.6","hrL":"0.0","hrR":"1.4","bpsL":"5.0","bpsR":"5.0","bphrL":"0.0","bphrR":"3.0","clL":"-2.0","clR":"0.0","powerL":"w","powerR":"n","dpL":"11.0","dpR":"11.0","running":"16","hitAndRun":"D","bunting":"B","lead":"*4-6","caught":"-","first":"19","second":"13","ip":"0.0","hold":"0","endurance":"","balk":"0","wp":"0","batting":"","age":"29"}}}';
    $play = \ProjectScoresheet\Player::fromString($expected);
    if ($play->toString(true) !== $expected) {
      print "Error 5<br>";
      print strspn($play->toString(true) ^ $expected, "\0");
      print $play->toString(true) . ' ' . strlen($play->toString(true)). "<br>";
      print $expected;
      exit;
    }
    print "Test successful<br>";  
  }

  function testPosition() {
    $pos = new \ProjectScoresheet\Position;
    $expected = '{"position":{"pos":"DH","when":{"visitor":"0","home":"0"}}}';
    if ($pos->toString() !== $expected) {
      print "Error 1<br>";
      print $pos->toString();
      print $expected;
      exit;
    }
    $pos->p(\ProjectScoresheet\Position::position('C'));
    $expected = '{"position":{"pos":"C","when":{"visitor":"0","home":"0"}}}';
    if ($pos->toString() !== $expected) {
      print "Error 2<br>";
      print $pos->toString();
      print $expected;
      exit;
    }
    $pos->when(5,10);
    $expected = '{"position":{"pos":"C","when":{"visitor":"5","home":"10"}}}';
    if ($pos->toString() !== $expected) {
      print "Error 3<br>";
      print $pos->toString();
      print $expected;
      exit;
    }
    $pos2 = \ProjectScoresheet\Position::fromString($pos->toString());
    if ($pos->toString() !== $pos2->toString()) {
      print "Error 3.1<br>";
      print $pos->toString();
      print $pos2->toString();
      exit;
    }
    $pos3 = \ProjectScoresheet\Position::fromString(json_encode($pos2->json()));
    if ($pos3->toString() !== $pos2->toString()) {
      print "Error 3.2<br>";
      print $pos3->toString();
      print $pos2->toString();
      exit;
    }
    $ps=\ProjectScoresheet\Position::positionArray();
    if (count($ps) != 13) {
      print "Error 3.5<br>";
      exit;
    }
    foreach ($ps as $i => $p) {
      //print "${i}<br>";
      if (\ProjectScoresheet\Position::position($p) != $i) {
        print "Error 4 - ${i}<br>";
        exit;
      }
      if (\ProjectScoresheet\Position::positionString($i) != $p) {
        print "Error 5 - ${i}<br>";
        exit;
      }
    }
    print "Test successful<br>";  
  }

  function testResult() {
    $rslt=new \ProjectScoresheet\Result;
    $rslt->before='22;34';
    $rslt->during='here';
    $rslt->after='done';
    $expected='{"result":{"before":"22;34","during":"here","after":"done"}}';
    if ($rslt->toString() !== $expected) {
      print "Error 1<br>";
      print $rslt->toString();
      print $expected;
      exit;
    }
    $rslt->after='Bx';
    $expected='{"result":{"before":"22;34","during":"here"}}';
    if ($rslt->toString() !== $expected) {
      print "Error 2<br>";
      print $rslt->toString();
      print $expected;
      exit;
    }
    $rslt2=\ProjectScoresheet\Result::fromString($rslt->toString());
    if ($rslt->toString() !== $rslt2->toString()) {
      print "Error 3<br>";
      print $rslt->toString();
      print $rslt2->toString();
      exit;
    }
    $rslt3 = \ProjectScoresheet\Result::fromString(json_encode($rslt2->json()));
    if ($rslt3->toString() !== $rslt2->toString()) {
      print "Error 4<br>";
      print $rslt3->toString();
      print $rslt2->toString();
      exit;
    }
    print "Test successful<br>";  
  }

  function testRoster() {
    $r = new \Jhml\Roster;
    $expected = '{"roster":{"team":"","batters":[],"pitchers":[]}}';
    if ($r->toString() !== $expected) {
      print "Error 1<br>";
      print $r->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $r->team = 'PIT';
    $ll = 'Marte, Starling	29	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    \Scoring\RosterItem::processMove($ri,15,\Scoring\MoveType::ToMinors);
    $r->addBatter($ri);
    $expected = '{"roster":{"team":"PIT","batters":[{"rosterItem":{"player":{"name":"Marte, Starling"},"team":"PIT","moves":[{"move":{"moveType":"To minors","gameNumber":"15"}}],"injuries":[],"startGame":"0","endGame":"999"}}],"pitchers":[]}}';
    if ($r->toString() !== $expected) {
      print "Error 2<br>";
      print $r->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ll = 'Donaldson, Josh	32	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $expected = '{"roster":{"team":"PIT","batters":[{"rosterItem":{"player":{"name":"Donaldson, Josh"},"team":"PIT","moves":[],"injuries":[],"startGame":"0","endGame":"999"}},{"rosterItem":{"player":{"name":"Marte, Starling"},"team":"PIT","moves":[{"move":{"moveType":"To minors","gameNumber":"15"}}],"injuries":[],"startGame":"0","endGame":"999"}}],"pitchers":[]}}';
    if ($r->toString() !== $expected) {
      print "Error 3<br>";
      print $r->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ll = 'Cole, Gerrit	26	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    \Scoring\RosterItem::processMove($ri,15,\Scoring\MoveType::ToMinors);
    $r->addPitcher($ri);
    $expected = '{"roster":{"team":"PIT","batters":[{"rosterItem":{"player":{"name":"Donaldson, Josh"},"team":"PIT","moves":[],"injuries":[],"startGame":"0","endGame":"999"}},{"rosterItem":{"player":{"name":"Marte, Starling"},"team":"PIT","moves":[{"move":{"moveType":"To minors","gameNumber":"15"}}],"injuries":[],"startGame":"0","endGame":"999"}}],"pitchers":[{"rosterItem":{"player":{"name":"Cole, Gerrit"},"team":"PIT","moves":[{"move":{"moveType":"To minors","gameNumber":"15"}}],"injuries":[],"startGame":"0","endGame":"999"}}]}}';
    if ($r->toString() !== $expected) {
      print "Error 5<br>";
      print $r->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ll = 'Gonzalez, Gio	31	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addPitcher($ri);
    $expected = '{"roster":{"team":"PIT","batters":[{"rosterItem":{"player":{"name":"Donaldson, Josh"},"team":"PIT","moves":[],"injuries":[],"startGame":"0","endGame":"999"}},{"rosterItem":{"player":{"name":"Marte, Starling"},"team":"PIT","moves":[{"move":{"moveType":"To minors","gameNumber":"15"}}],"injuries":[],"startGame":"0","endGame":"999"}}],"pitchers":[{"rosterItem":{"player":{"name":"Cole, Gerrit"},"team":"PIT","moves":[{"move":{"moveType":"To minors","gameNumber":"15"}}],"injuries":[],"startGame":"0","endGame":"999"}},{"rosterItem":{"player":{"name":"Gonzalez, Gio"},"team":"PIT","moves":[],"injuries":[],"startGame":"0","endGame":"999"}}]}}';
    if ($r->toString() !== $expected) {
      print "Error 6<br>";
      print $r->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $b=$r->getBatters();
    if (count($b) != 2 || $b[0]->player->name != "Donaldson, Josh" || $b[1]->player->name != "Marte, Starling") {
      print "Error 7<br>";
      exit;
    }
    $p=$r->getPitchers();
    if (count($p) != 2 || $p[0]->player->name != "Cole, Gerrit" || $p[1]->player->name != "Gonzalez, Gio") {
      print "Error 8<br>";
      exit;
    }
    $r = \Jhml\Roster::fromString($expected);
    if ($r->toString() !== $expected) {
      print "Error 9<br>";
      print $r->toString(true) . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ma = $r->getMajors();
    if ($ma->toString() !== $expected) {
      print "Error 10<br>";
      print $ma->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ma = $r->getMajors(15);
    $expected = '{"roster":{"team":"PIT","batters":[{"rosterItem":{"player":{"name":"Donaldson, Josh"},"team":"PIT","moves":[],"injuries":[],"startGame":"0","endGame":"999"}}],"pitchers":[{"rosterItem":{"player":{"name":"Gonzalez, Gio"},"team":"PIT","moves":[],"injuries":[],"startGame":"0","endGame":"999"}}]}}';
    if ($ma->toString() !== $expected) {
      print "Error 11<br>";
      print $ma->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $mi = $r->getMinors();
    $expected = '{"roster":{"team":"PIT","batters":[],"pitchers":[]}}';
    if ($mi->toString() !== $expected) {
      print "Error 12<br>";
      print $mi->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $mi = $r->getMinors(15);
    $expected = '{"roster":{"team":"PIT","batters":[{"rosterItem":{"player":{"name":"Marte, Starling"},"team":"PIT","moves":[{"move":{"moveType":"To minors","gameNumber":"15"}}],"injuries":[],"startGame":"0","endGame":"999"}}],"pitchers":[{"rosterItem":{"player":{"name":"Cole, Gerrit"},"team":"PIT","moves":[{"move":{"moveType":"To minors","gameNumber":"15"}}],"injuries":[],"startGame":"0","endGame":"999"}}]}}';
    if ($mi->toString() !== $expected) {
      print "Error 13<br>";
      print $mi->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $r->move('Marte, Starling',\Scoring\MoveType::ToMajors,17);
    $expected = '{"roster":{"team":"PIT","batters":[{"rosterItem":{"player":{"name":"Donaldson, Josh"},"team":"PIT","moves":[],"injuries":[],"startGame":"0","endGame":"999"}},{"rosterItem":{"player":{"name":"Marte, Starling"},"team":"PIT","moves":[{"move":{"moveType":"To minors","gameNumber":"15"}},{"move":{"moveType":"Fm minors","gameNumber":"17"}}],"injuries":[],"startGame":"0","endGame":"999"}}],"pitchers":[{"rosterItem":{"player":{"name":"Gonzalez, Gio"},"team":"PIT","moves":[],"injuries":[],"startGame":"0","endGame":"999"}}]}}';
    $ma = $r->getMajors(17);
    if ($ma->toString() !== $expected) {
      print "Error 14<br>";
      print $ma->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $mi = $r->getMinors(17);
    $expected = '{"roster":{"team":"PIT","batters":[],"pitchers":[{"rosterItem":{"player":{"name":"Cole, Gerrit"},"team":"PIT","moves":[{"move":{"moveType":"To minors","gameNumber":"15"}}],"injuries":[],"startGame":"0","endGame":"999"}}]}}';
    if ($mi->toString() !== $expected) {
      print "Error 15<br>";
      print $mi->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $r = new \Jhml\Roster;
    $r->team = 'PIT';
    $ll = 'Player 1	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 2	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 3	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 4	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 5	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 6	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 7	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 8	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 9	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 10	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 11	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 12	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 13	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 14	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 15	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 16	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 17	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 18	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 19	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 20	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 21	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 22	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 23	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 24	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    $ll = 'Player 25	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    if ($r->isValid() == false) {
      print "Error 16<br>";
    }
    $ll = 'Player 26	999	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $r->addBatter($ri);
    if ($r->isValid() == true) {
      print "Error 17<br>";
    }
    if ($r->isValid(85) == false) {
      print "Error 18<br>";
    }

    print "Test successful<br>";  
  }

  function testRosterItem() {
    $ri = new \Scoring\RosterItem;
    $expected = '{"rosterItem":{"player":{"name":""},"team":"","moves":[],"injuries":[],"startGame":"0","endGame":"999"}}';
    if ($ri->toString() !== $expected) {
      print "Error 1<br>";
      print $ri->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ll = 'Marte, Starling	29	PIT';
    $ri = \Scoring\RosterItem::fromRosterFileString($ll);
    $expected = '{"rosterItem":{"player":{"name":"Marte, Starling","strat":{"realTeam":"","hand":"R","positionsPlayed":[],"ab":"0","bb":"","hitL":"0.0","hitR":"0.0","obL":"0.0","obR":"0.0","tbL":"0.0","tbR":"0.0","hrL":"0.0","hrR":"0.0","bpsL":"0.0","bpsR":"0.0","bphrL":"0.0","bphrR":"0.0","clL":"0.0","clR":"0.0","powerL":"","powerR":"","dpL":"0.0","dpR":"0.0","running":"","hitAndRun":"","bunting":"","lead":"","caught":"","first":"0","second":"0","ip":"0.0","hold":"0","endurance":"","balk":"0","wp":"0","batting":"","age":"29"}},"team":"PIT","moves":[],"injuries":[],"startGame":"0","endGame":"999"}}';
    if ($ri->toString(true) !== $expected) {
      print "Error 2<br>";
      print $ri->toString(true) . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ml = '17:Marte, Starling~1~none:none~REM~none:none~REM~none:Santana, Danny~To minors~Cabrera, Asdrubal~Off DL:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors';
    \Scoring\RosterItem::addMoveFileString($ri,$ml);
    $expected = '{"rosterItem":{"player":{"name":"Marte, Starling","strat":{"realTeam":"","hand":"R","positionsPlayed":[],"ab":"0","bb":"","hitL":"0.0","hitR":"0.0","obL":"0.0","obR":"0.0","tbL":"0.0","tbR":"0.0","hrL":"0.0","hrR":"0.0","bpsL":"0.0","bpsR":"0.0","bphrL":"0.0","bphrR":"0.0","clL":"0.0","clR":"0.0","powerL":"","powerR":"","dpL":"0.0","dpR":"0.0","running":"","hitAndRun":"","bunting":"","lead":"","caught":"","first":"0","second":"0","ip":"0.0","hold":"0","endurance":"","balk":"0","wp":"0","batting":"","age":"29"}},"team":"PIT","moves":[],"injuries":[{"injury":{"gameNumber":"17","duration":"1"}}],"startGame":"0","endGame":"999"}}';
    if ($ri->toString(true) !== $expected) {
      print "Error 3<br>";
      print $ri->toString(true) . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ml = '19:Marte, Starling~REM~FooManChu:none~REM~none:none~REM~none:Santana, Danny~To minors~Cabrera, Asdrubal~Off DL:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors';
    \Scoring\RosterItem::addMoveFileString($ri,$ml);
    $expected = '{"rosterItem":{"player":{"name":"Marte, Starling","strat":{"realTeam":"","hand":"R","positionsPlayed":[],"ab":"0","bb":"","hitL":"0.0","hitR":"0.0","obL":"0.0","obR":"0.0","tbL":"0.0","tbR":"0.0","hrL":"0.0","hrR":"0.0","bpsL":"0.0","bpsR":"0.0","bphrL":"0.0","bphrR":"0.0","clL":"0.0","clR":"0.0","powerL":"","powerR":"","dpL":"0.0","dpR":"0.0","running":"","hitAndRun":"","bunting":"","lead":"","caught":"","first":"0","second":"0","ip":"0.0","hold":"0","endurance":"","balk":"0","wp":"0","batting":"","age":"29"}},"team":"PIT","moves":[{"move":{"moveType":"On DL","gameNumber":"20"}}],"injuries":[{"injury":{"gameNumber":"17","duration":"1"}},{"injury":{"gameNumber":"19","duration":"0"}}],"startGame":"0","endGame":"999"}}';
    if ($ri->toString(true) !== $expected) {
      print "Error 4<br>";
      print $ri->toString(true) . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ml = '20:none~REM~none:none~REM~none:none~REM~none:Marte, Starling~On DL~FooManChu~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors';
    \Scoring\RosterItem::addMoveFileString($ri,$ml);
    $expected = '{"rosterItem":{"player":{"name":"Marte, Starling","strat":{"realTeam":"","hand":"R","positionsPlayed":[],"ab":"0","bb":"","hitL":"0.0","hitR":"0.0","obL":"0.0","obR":"0.0","tbL":"0.0","tbR":"0.0","hrL":"0.0","hrR":"0.0","bpsL":"0.0","bpsR":"0.0","bphrL":"0.0","bphrR":"0.0","clL":"0.0","clR":"0.0","powerL":"","powerR":"","dpL":"0.0","dpR":"0.0","running":"","hitAndRun":"","bunting":"","lead":"","caught":"","first":"0","second":"0","ip":"0.0","hold":"0","endurance":"","balk":"0","wp":"0","batting":"","age":"29"}},"team":"PIT","moves":[{"move":{"moveType":"On DL","gameNumber":"20"}}],"injuries":[{"injury":{"gameNumber":"17","duration":"1"}},{"injury":{"gameNumber":"19","duration":"0"}}],"startGame":"0","endGame":"999"}}';
    if ($ri->toString(true) !== $expected) {
      print "Error 5<br>";
      print $ri->toString(true) . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ris = \Scoring\RosterItem::processTradedFor($ml);
    if (count($ris) != 0) {
      print "Error 6<br>";
      print count($ris) . "<br>";
      exit;
    }
    $ml = '25:none~REM~none:none~REM~none:none~REM~none:FooManChu~To minors~Marte, Starling~Off DL:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors';
    \Scoring\RosterItem::addMoveFileString($ri,$ml);
    $expected = '{"rosterItem":{"player":{"name":"Marte, Starling","strat":{"realTeam":"","hand":"R","positionsPlayed":[],"ab":"0","bb":"","hitL":"0.0","hitR":"0.0","obL":"0.0","obR":"0.0","tbL":"0.0","tbR":"0.0","hrL":"0.0","hrR":"0.0","bpsL":"0.0","bpsR":"0.0","bphrL":"0.0","bphrR":"0.0","clL":"0.0","clR":"0.0","powerL":"","powerR":"","dpL":"0.0","dpR":"0.0","running":"","hitAndRun":"","bunting":"","lead":"","caught":"","first":"0","second":"0","ip":"0.0","hold":"0","endurance":"","balk":"0","wp":"0","batting":"","age":"29"}},"team":"PIT","moves":[{"move":{"moveType":"On DL","gameNumber":"20"}},{"move":{"moveType":"Off DL","gameNumber":"25"}}],"injuries":[{"injury":{"gameNumber":"17","duration":"1"}},{"injury":{"gameNumber":"19","duration":"0"}}],"startGame":"0","endGame":"999"}}';
    if ($ri->toString(true) !== $expected) {
      print "Error 6<br>";
      print $ri->toString(true) . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ml = '35:none~REM~none:none~REM~none:none~REM~none:Marte, Starling~Traded~FooManChu~Traded for:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors:none~To minors~none~To minors';
    \Scoring\RosterItem::addMoveFileString($ri,$ml);
    $expected = '{"rosterItem":{"player":{"name":"Marte, Starling","strat":{"realTeam":"","hand":"R","positionsPlayed":[],"ab":"0","bb":"","hitL":"0.0","hitR":"0.0","obL":"0.0","obR":"0.0","tbL":"0.0","tbR":"0.0","hrL":"0.0","hrR":"0.0","bpsL":"0.0","bpsR":"0.0","bphrL":"0.0","bphrR":"0.0","clL":"0.0","clR":"0.0","powerL":"","powerR":"","dpL":"0.0","dpR":"0.0","running":"","hitAndRun":"","bunting":"","lead":"","caught":"","first":"0","second":"0","ip":"0.0","hold":"0","endurance":"","balk":"0","wp":"0","batting":"","age":"29"}},"team":"PIT","moves":[{"move":{"moveType":"On DL","gameNumber":"20"}},{"move":{"moveType":"Off DL","gameNumber":"25"}},{"move":{"moveType":"Traded","gameNumber":"35"}}],"injuries":[{"injury":{"gameNumber":"17","duration":"1"}},{"injury":{"gameNumber":"19","duration":"0"}}],"startGame":"0","endGame":"34"}}';
    if ($ri->toString(true) !== $expected) {
      print "Error 8<br>";
      print $ri->toString(true) . "<br>";
      print $expected . "<br>";
      exit;
    }
    $ris = \Scoring\RosterItem::processTradedFor($ml);
    if (count($ris) != 1) {
      print "Error 9<br>";
      print count($ris) . "<br>";
      exit;
    }
    $expected = '{"rosterItem":{"player":{"name":"FooManChu","strat":{"realTeam":"","hand":"R","positionsPlayed":[],"ab":"0","bb":"","hitL":"0.0","hitR":"0.0","obL":"0.0","obR":"0.0","tbL":"0.0","tbR":"0.0","hrL":"0.0","hrR":"0.0","bpsL":"0.0","bpsR":"0.0","bphrL":"0.0","bphrR":"0.0","clL":"0.0","clR":"0.0","powerL":"","powerR":"","dpL":"0.0","dpR":"0.0","running":"","hitAndRun":"","bunting":"","lead":"","caught":"","first":"0","second":"0","ip":"0.0","hold":"0","endurance":"","balk":"0","wp":"0","batting":"","age":"99"}},"team":"","moves":[{"move":{"moveType":"Traded for","gameNumber":"35"}}],"injuries":[],"startGame":"35","endGame":"999"}}';
    if ($ris[0]->toString(true) !== $expected) {
      print "Error 10<br>";
      print $ris[0]->toString(true) . "<br>";
      print $expected . "<br>";
      exit;
    }
    $expected = '{"rosterItem":{"player":{"name":"FooManChu","strat":{"realTeam":"","hand":"R","positionsPlayed":[],"ab":"0","bb":"","hitL":"0.0","hitR":"0.0","obL":"0.0","obR":"0.0","tbL":"0.0","tbR":"0.0","hrL":"0.0","hrR":"0.0","bpsL":"0.0","bpsR":"0.0","bphrL":"0.0","bphrR":"0.0","clL":"0.0","clR":"0.0","powerL":"","powerR":"","dpL":"0.0","dpR":"0.0","running":"","hitAndRun":"","bunting":"","lead":"","caught":"","first":"0","second":"0","ip":"0.0","hold":"0","endurance":"","balk":"0","wp":"0","batting":"","age":"99"}},"team":"","moves":[{"move":{"moveType":"Traded for","gameNumber":"35"}},{"move":{"moveType":"To minors","gameNumber":"42"}}],"injuries":[{"injury":{"gameNumber":"37","duration":"1"}},{"injury":{"gameNumber":"39","duration":"0"}}],"startGame":"35","endGame":"999"}}';
    $ri = \Scoring\RosterItem::fromString($expected);
    if ($ri->toString(true) !== $expected) {
      print "Error 11<br>";
      print $ri->toString(true) . "<br>";
      print $expected . "<br>";
      exit;
    }
    if ($ri->isMajors(22) !== false) {
      print "Error 12<br>";
      exit;
    }
    if ($ri->isMajors(41) !== true) {
      print "Error 13<br>";
      exit;
    }
    if ($ri->isMajors(42) !== false) {
      print "Error 14<br>";
      exit;
    }
    if ($ri->isMinors(22) !== false) {
      print "Error 15<br>";
      exit;
    }
    if ($ri->isMinors(41) !== false) {
      print "Error 16<br>";
      exit;
    }
    if ($ri->isMinors(42) !== true) {
      print "Error 17<br>";
      exit;
    }
    if ($ri->isInjured(22) !== false) {
      print "Error 18<br>";
      exit;
    }
    if ($ri->isInjured(38) !== true) {
      print "Error 19<br>";
      exit;
    }
    if ($ri->isInjured(38,[0,37]) !== false) {
      print "Error 20<br>";
      exit;
    }
    if ($ri->isInjured(38,[0,38]) !== true) {
      print "Error 21<br>";
      exit;
    }
    if ($ri->isInjured(39,[0,39]) !== false) {
      print "Error 22<br>";
      exit;
    }
    if ($ri->isInjured(40) !== false) {
      print "Error 23<br>";
      exit;
    }
    print "Test successful<br>";  
  }

  function testRosters() {
    $NewRosterFile = "/Rosters.json";
    if (file_exists('../data/2017/' . $NewRosterFile)) unlink('../data/2017/' . $NewRosterFile);
    $r2017 = new \Jhml\Rosters(2017);
    $t17 = $r2017->getTeams();
    $r2018n = new \Jhml\Rosters(2018);
    $r2018 = new \Jhml\Rosters(2018,true);
    $t18n = $r2018n->getTeams();
    $t18 = $r2018->getTeams();
    $expected=["ATL","BRI","COL","CWS","FRE","KCR","LAD","MIA","MIL","MIN","OAK","PIT","SFQ","STL","TBP","WAS"];
    if ($t17 !== $expected) {
      print "Error 1<br>";
      print_r($t17);print "<br/>";
      print_r($expected); print "<br>";
      exit;
    }
    $expected=["ATL","BRI","COL","CWS","FRE","LAD","MIA","MIL","MIN","OAK","PIT","SFQ","STL","TBP","TOR","WAS"];
    if ($t18n !== $expected) {
      print "Error 2n<br>";
      print_r($t18n);print "<br/>";
      print_r($expected); print "<br>";
      exit;
    }
    if ($t18 !== $expected) {
      print "Error 2<br>";
      print_r($t18);print "<br/>";
      print_r($expected); print "<br>";
      exit;
    }
    $expected=[36,36,35,36,36,35,37,36,36,36,36,36,36,36,36,36];
    for ($i=0; $i < count($t17); $i++) {
      if ($t17[$i] == "FRE") continue;
      $r = $r2017->getRoster($t17[$i]);
      $ma = $r->getMajors(0);
      if (count($ma->batters) + count($ma->pitchers) != $expected[$i]) {
        print "Error 3 " . $t17[$i] . "  " . (count($ma->batters) + count($ma->pitchers)) . "<br>";
        exit;
      }
    }
    $expected=[36,36,36,36,36,37,35,36,36,36,36,36,36,36,36,36];
    for ($i=0; $i < count($t18); $i++) {
      if ($t18[$i] == "FRE") continue;
      $r = $r2018->getRoster($t18[$i]);
      $ma = $r->getMajors(0);
      if (count($ma->batters) + count($ma->pitchers) != $expected[$i]) {
        print "Error 4 " . $t18[$i] . "  " . (count($ma->batters) + count($ma->pitchers)) . "<br>";
        exit;
      }
    }
    $r1 = \Jhml\Rosters::fromString($r2017->toString());
    $rs1 = $r1->toString();
    $rs2017 = $r2017->toString();
    if ($rs1 != $rs2017) {
      print "Error 5<br>";
      print strlen($rs1) . "<br>";
      print strlen($rs2017) . "<br>";
      for ($i=0; $i < min(strlen($rs1), strlen($rs2017)); $i++) {
        if ($rs1[$i] != $rs2017[$i]) {
          print $i . "<br/>";
          print substr($rs1,max(0,$i-200),800) . "<br/>";
          print substr($rs2017,max(0,$i-200),800) . "<br/>";
          $i = strlen($rs1) + 1;
        }
      } 
      exit;
    }
    $r1 = \Jhml\Rosters::fromString($r2017->toString(true));
    $rs1 = $r1->toString(true);
    $rs2017 = $r2017->toString(true);
    if ($rs1 != $rs2017) {
      print "Error 6<br>";
      print strlen($rs1) . "<br>";
      print strlen($rs2017) . "<br>";
      for ($i=0; $i < min(strlen($rs1), strlen($rs2017)); $i++) {
        if ($rs1[$i] != $rs2017[$i]) {
          print $i . "<br/>";
          print substr($rs1,max(0,$i-200),800) . "<br/>";
          print substr($rs2017,max(0,$i-200),800) . "<br/>";
          $i = strlen($rs1) + 1;
        }
      } 
      exit;
    }
    $r2017->writeRosterFile();
    $r2017n = new \Jhml\Rosters(2017);
    $rs2017n = $r2017n->toString(false);
    if ($rs2017n != $rs2017n) {
      print "Error 7<br>";
      print strlen($rs2017n) . "<br>";
      print strlen($rs2017) . "<br>";
      for ($i=0; $i < min(strlen($rs2017n), strlen($rs2017)); $i++) {
        if ($rs2017n[$i] != $rs2017[$i]) {
          print $i . "<br/>";
          print substr($rs2017n,max(0,$i-200),800) . "<br/>";
          print substr($rs2017,max(0,$i-200),800) . "<br/>";
          $i = strlen($rs2017n) + 1;
        }
      } 
      exit;
    }
    $rs2018n = $r2018n->toString(true);
    if ($rs2018n == $rs2018) {
      print "Error 8<br>";
    }
    $expected=["atl","bri","col","cws","fre","kcr","lad","mia","mil","min","oak","pit","sfq","stl","tbp","was"];
    foreach ($expected as $team) {
      if (file_exists('../data/2017/' . $team . 'moves')) unlink('../data/2017/' . $team . 'moves');
    }
    $r2017->writeOldMovesFiles();
    foreach ($expected as $team) {
      if (! file_exists('../data/2017/' . $team . 'moves')) {
        print "Error 9 - " . $team . "<br>";
        exit;
      }
    }
    print "Test successful<br>";  
  }

  function testSchedule() {
    $s_2017 = new \Scoring\Schedule(2017);
    $expected='{"home":[{"scheduleItem":{"homeTeam":"pit","awayTeam":"mia","numberOfGames":"4","season":"0","results":[{"away":{"team":"mia","gameNumber":"60","runs":"11","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"19","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"61","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"20","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"62","runs":"2","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"21","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"63","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"22","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"mia","numberOfGames":"3","season":"0","results":[{"away":{"team":"mia","gameNumber":"64","runs":"2","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"23","runs":"1","hits":"0","errors":"0","starter":""},"innings":"11","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"65","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"24","runs":"3","hits":"0","errors":"0","starter":""},"innings":"10","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"66","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"25","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"col","numberOfGames":"4","season":"0","results":[{"away":{"team":"col","gameNumber":"55","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"42","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"56","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"43","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"57","runs":"0","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"44","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"58","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"45","runs":"8","hits":"0","errors":"0","starter":""},"innings":"11","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"col","numberOfGames":"3","season":"0","results":[{"away":{"team":"col","gameNumber":"24","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"11","runs":"0","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"25","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"12","runs":"8","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"26","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"13","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"sfq","numberOfGames":"3","season":"0","results":[{"away":{"team":"sfq","gameNumber":"82","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"77","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"sfq","gameNumber":"83","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"78","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"sfq","gameNumber":"84","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"79","runs":"5","hits":"0","errors":"0","starter":""},"innings":"10","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"was","numberOfGames":"3","season":"0","results":[{"away":{"team":"was","gameNumber":"74","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"68","runs":"8","hits":"0","errors":"0","starter":""},"innings":"11","date":"09/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"was","gameNumber":"75","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"69","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"09/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"was","gameNumber":"76","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"70","runs":"7","hits":"0","errors":"0","starter":""},"innings":"9","date":"09/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"cws","numberOfGames":"3","season":"0","results":[{"away":{"team":"cws","gameNumber":"39","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"46","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"cws","gameNumber":"40","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"47","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"cws","gameNumber":"41","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"48","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"bri","numberOfGames":"3","season":"0","results":[{"away":{"team":"bri","gameNumber":"35","runs":"0","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"51","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/21/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"bri","gameNumber":"36","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"52","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/21/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"bri","gameNumber":"37","runs":"12","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"53","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/21/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"min","numberOfGames":"3","season":"0","results":[{"away":{"team":"min","gameNumber":"22","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"34","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"min","gameNumber":"23","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"35","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"min","gameNumber":"24","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"36","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"stl","numberOfGames":"3","season":"0","results":[{"away":{"team":"stl","gameNumber":"34","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"56","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"stl","gameNumber":"35","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"57","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"stl","gameNumber":"36","runs":"16","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"58","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"mil","numberOfGames":"2","season":"0","results":[{"away":{"team":"mil","gameNumber":"9","runs":"13","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"9","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mil","gameNumber":"10","runs":"7","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"10","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"oak","numberOfGames":"2","season":"0","results":[{"away":{"team":"oak","gameNumber":"16","runs":"7","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"17","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"oak","gameNumber":"17","runs":"12","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"18","runs":"10","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"atl","numberOfGames":"2","season":"0","results":[{"away":{"team":"atl","gameNumber":"47","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"32","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"04/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"atl","gameNumber":"48","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"33","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"04/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"lad","numberOfGames":"2","season":"0","results":[{"away":{"team":"lad","gameNumber":"72","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"80","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/14/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"lad","gameNumber":"73","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"81","runs":"18","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/14/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"tbp","numberOfGames":"2","season":"0","results":[{"away":{"team":"tbp","gameNumber":"57","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"71","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/06/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"tbp","gameNumber":"58","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"72","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/06/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"sfq","numberOfGames":"3","season":"1","results":[{"away":{"team":"sfq","gameNumber":"94","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"88","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"sfq","gameNumber":"95","runs":"11","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"89","runs":"8","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"sfq","gameNumber":"96","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"90","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"was","numberOfGames":"3","season":"1","results":[{"away":{"team":"was","gameNumber":"88","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"85","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"was","gameNumber":"89","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"86","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"was","gameNumber":"90","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"87","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"pit","awayTeam":"kcr","numberOfGames":"3","season":"1","results":[{"away":{"team":"kcr","gameNumber":"85","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"91","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"kcr","gameNumber":"86","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"92","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"kcr","gameNumber":"87","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"93","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}}],"away":[{"scheduleItem":{"homeTeam":"mia","awayTeam":"pit","numberOfGames":"3","season":"0","results":[{"away":{"team":"pit","gameNumber":"26","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"67","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"27","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"68","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"28","runs":"1","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"69","runs":"10","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"col","awayTeam":"pit","numberOfGames":"3","season":"0","results":[{"away":{"team":"pit","gameNumber":"39","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"52","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"40","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"53","runs":"11","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"41","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"54","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"sfq","awayTeam":"pit","numberOfGames":"4","season":"0","results":[{"away":{"team":"pit","gameNumber":"73","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"78","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"74","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"79","runs":"7","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"75","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"80","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"76","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"81","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"sfq","awayTeam":"pit","numberOfGames":"3","season":"0","results":[{"away":{"team":"pit","gameNumber":"1","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"1","runs":"3","hits":"0","errors":"0","starter":""},"innings":"11","date":"03/11/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"2","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"2","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/11/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"3","runs":"1","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"3","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/11/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"was","awayTeam":"pit","numberOfGames":"4","season":"0","results":[{"away":{"team":"pit","gameNumber":"61","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"67","runs":"0","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/25/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"62","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"68","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/25/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"63","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"69","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/25/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"64","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"70","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/25/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"was","awayTeam":"pit","numberOfGames":"3","season":"0","results":[{"away":{"team":"pit","gameNumber":"65","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"71","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"66","runs":"7","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"72","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"67","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"73","runs":"3","hits":"0","errors":"0","starter":""},"innings":"15","date":"08/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"cws","awayTeam":"pit","numberOfGames":"2","season":"0","results":[{"away":{"team":"pit","gameNumber":"49","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"cws","gameNumber":"42","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"50","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"cws","gameNumber":"43","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"bri","awayTeam":"pit","numberOfGames":"2","season":"0","results":[{"away":{"team":"pit","gameNumber":"54","runs":"12","hits":"0","errors":"0","starter":""},"home":"{"team":"bri","gameNumber":"38","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/21/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"55","runs":"13","hits":"0","errors":"0","starter":""},"home":"{"team":"bri","gameNumber":"39","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/21/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"min","awayTeam":"pit","numberOfGames":"2","season":"0","results":[{"away":{"team":"pit","gameNumber":"37","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"min","gameNumber":"25","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"38","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"min","gameNumber":"26","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"stl","awayTeam":"pit","numberOfGames":"2","season":"0","results":[{"away":{"team":"pit","gameNumber":"59","runs":"2","hits":"0","errors":"0","starter":""},"home":"{"team":"stl","gameNumber":"37","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"60","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"stl","gameNumber":"38","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"mil","awayTeam":"pit","numberOfGames":"3","season":"0","results":[{"away":{"team":"pit","gameNumber":"6","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"mil","gameNumber":"6","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"7","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"mil","gameNumber":"7","runs":"8","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"8","runs":"7","hits":"0","errors":"0","starter":""},"home":"{"team":"mil","gameNumber":"8","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"oak","awayTeam":"pit","numberOfGames":"3","season":"0","results":[{"away":{"team":"pit","gameNumber":"14","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"oak","gameNumber":"13","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"15","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"oak","gameNumber":"14","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"16","runs":"0","hits":"0","errors":"0","starter":""},"home":"{"team":"oak","gameNumber":"15","runs":"11","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"atl","awayTeam":"pit","numberOfGames":"3","season":"0","results":[{"away":{"team":"pit","gameNumber":"29","runs":"11","hits":"0","errors":"0","starter":""},"home":"{"team":"atl","gameNumber":"44","runs":"0","hits":"0","errors":"0","starter":""},"innings":"9","date":"04/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"30","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"atl","gameNumber":"45","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"04/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"31","runs":"1","hits":"0","errors":"0","starter":""},"home":"{"team":"atl","gameNumber":"46","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"04/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"lad","awayTeam":"pit","numberOfGames":"3","season":"0","results":[{"away":{"team":"pit","gameNumber":"82","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"lad","gameNumber":"74","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/14/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"83","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"lad","gameNumber":"75","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/14/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"84","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"lad","gameNumber":"76","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/14/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"kcr","awayTeam":"pit","numberOfGames":"2","season":"0","results":[{"away":{"team":"pit","gameNumber":"4","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"kcr","gameNumber":"1","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"5","runs":"2","hits":"0","errors":"0","starter":""},"home":"{"team":"kcr","gameNumber":"2","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"mia","awayTeam":"pit","numberOfGames":"3","season":"1","results":[{"away":{"team":"pit","gameNumber":"100","runs":"16","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"97","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/03/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"101","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"98","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/03/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"102","runs":"7","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"99","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/03/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"col","awayTeam":"pit","numberOfGames":"3","season":"1","results":[{"away":{"team":"pit","gameNumber":"97","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"97","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"98","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"98","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"99","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"99","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}},{"scheduleItem":{"homeTeam":"tbp","awayTeam":"pit","numberOfGames":"3","season":"1","results":[{"away":{"team":"pit","gameNumber":"94","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"tbp","gameNumber":"88","runs":"0","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"95","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"tbp","gameNumber":"89","runs":"0","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"96","runs":"1","hits":"0","errors":"0","starter":""},"home":"{"team":"tbp","gameNumber":"90","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}}],"results":[{"away":{"team":"pit","gameNumber":"1","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"1","runs":"3","hits":"0","errors":"0","starter":""},"innings":"11","date":"03/11/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"2","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"2","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/11/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"3","runs":"1","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"3","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/11/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"4","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"kcr","gameNumber":"1","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"5","runs":"2","hits":"0","errors":"0","starter":""},"home":"{"team":"kcr","gameNumber":"2","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"6","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"mil","gameNumber":"6","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"7","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"mil","gameNumber":"7","runs":"8","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"8","runs":"7","hits":"0","errors":"0","starter":""},"home":"{"team":"mil","gameNumber":"8","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mil","gameNumber":"9","runs":"13","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"9","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mil","gameNumber":"10","runs":"7","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"10","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"24","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"11","runs":"0","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"25","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"12","runs":"8","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"26","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"13","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"14","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"oak","gameNumber":"13","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"15","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"oak","gameNumber":"14","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"16","runs":"0","hits":"0","errors":"0","starter":""},"home":"{"team":"oak","gameNumber":"15","runs":"11","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"oak","gameNumber":"16","runs":"7","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"17","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"oak","gameNumber":"17","runs":"12","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"18","runs":"10","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"60","runs":"11","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"19","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"61","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"20","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"62","runs":"2","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"21","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"63","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"22","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"64","runs":"2","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"23","runs":"1","hits":"0","errors":"0","starter":""},"innings":"11","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"65","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"24","runs":"3","hits":"0","errors":"0","starter":""},"innings":"10","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"mia","gameNumber":"66","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"25","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"26","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"67","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"27","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"68","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"28","runs":"1","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"69","runs":"10","hits":"0","errors":"0","starter":""},"innings":"9","date":"03/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"29","runs":"11","hits":"0","errors":"0","starter":""},"home":"{"team":"atl","gameNumber":"44","runs":"0","hits":"0","errors":"0","starter":""},"innings":"9","date":"04/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"30","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"atl","gameNumber":"45","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"04/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"31","runs":"1","hits":"0","errors":"0","starter":""},"home":"{"team":"atl","gameNumber":"46","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"04/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"atl","gameNumber":"47","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"32","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"04/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"atl","gameNumber":"48","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"33","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"04/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"min","gameNumber":"22","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"34","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"min","gameNumber":"23","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"35","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"min","gameNumber":"24","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"36","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"37","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"min","gameNumber":"25","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"38","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"min","gameNumber":"26","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"39","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"52","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"40","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"53","runs":"11","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"41","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"54","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"55","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"42","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"56","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"43","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"57","runs":"0","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"44","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"col","gameNumber":"58","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"45","runs":"8","hits":"0","errors":"0","starter":""},"innings":"11","date":"06/22/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"cws","gameNumber":"39","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"46","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"cws","gameNumber":"40","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"47","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"cws","gameNumber":"41","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"48","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"49","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"cws","gameNumber":"42","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"50","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"cws","gameNumber":"43","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/08/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"bri","gameNumber":"35","runs":"0","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"51","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/21/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"bri","gameNumber":"36","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"52","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/21/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"bri","gameNumber":"37","runs":"12","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"53","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/21/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"54","runs":"12","hits":"0","errors":"0","starter":""},"home":"{"team":"bri","gameNumber":"38","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/21/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"55","runs":"13","hits":"0","errors":"0","starter":""},"home":"{"team":"bri","gameNumber":"39","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"07/21/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"stl","gameNumber":"34","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"56","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"stl","gameNumber":"35","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"57","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"stl","gameNumber":"36","runs":"16","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"58","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"59","runs":"2","hits":"0","errors":"0","starter":""},"home":"{"team":"stl","gameNumber":"37","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"60","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"stl","gameNumber":"38","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"61","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"67","runs":"0","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/25/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"62","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"68","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/25/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"63","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"69","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/25/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"64","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"70","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/25/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"65","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"71","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"66","runs":"7","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"72","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"08/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"67","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"was","gameNumber":"73","runs":"3","hits":"0","errors":"0","starter":""},"innings":"15","date":"08/27/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"was","gameNumber":"74","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"68","runs":"8","hits":"0","errors":"0","starter":""},"innings":"11","date":"09/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"was","gameNumber":"75","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"69","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"09/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"was","gameNumber":"76","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"70","runs":"7","hits":"0","errors":"0","starter":""},"innings":"9","date":"09/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"tbp","gameNumber":"57","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"71","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/06/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"tbp","gameNumber":"58","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"72","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/06/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"73","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"78","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"74","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"79","runs":"7","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"75","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"80","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"76","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"sfq","gameNumber":"81","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"sfq","gameNumber":"82","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"77","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"sfq","gameNumber":"83","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"78","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"sfq","gameNumber":"84","runs":"4","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"79","runs":"5","hits":"0","errors":"0","starter":""},"innings":"10","date":"10/09/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"lad","gameNumber":"72","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"80","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/14/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"lad","gameNumber":"73","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"81","runs":"18","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/14/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"82","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"lad","gameNumber":"74","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/14/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"83","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"lad","gameNumber":"75","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/14/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"84","runs":"10","hits":"0","errors":"0","starter":""},"home":"{"team":"lad","gameNumber":"76","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"10/14/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"was","gameNumber":"88","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"85","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"was","gameNumber":"89","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"86","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"was","gameNumber":"90","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"87","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/18/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"sfq","gameNumber":"94","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"88","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"sfq","gameNumber":"95","runs":"11","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"89","runs":"8","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"sfq","gameNumber":"96","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"90","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"kcr","gameNumber":"85","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"91","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"kcr","gameNumber":"86","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"92","runs":"1","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"kcr","gameNumber":"87","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"pit","gameNumber":"93","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/26/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"94","runs":"6","hits":"0","errors":"0","starter":""},"home":"{"team":"tbp","gameNumber":"88","runs":"0","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"95","runs":"3","hits":"0","errors":"0","starter":""},"home":"{"team":"tbp","gameNumber":"89","runs":"0","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"96","runs":"1","hits":"0","errors":"0","starter":""},"home":"{"team":"tbp","gameNumber":"90","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"11/30/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"97","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"97","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"98","runs":"9","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"98","runs":"5","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"99","runs":"5","hits":"0","errors":"0","starter":""},"home":"{"team":"col","gameNumber":"99","runs":"6","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/01/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"100","runs":"16","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"97","runs":"2","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/03/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"101","runs":"8","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"98","runs":"4","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/03/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false},{"away":{"team":"pit","gameNumber":"102","runs":"7","hits":"0","errors":"0","starter":""},"home":"{"team":"mia","gameNumber":"99","runs":"3","hits":"0","errors":"0","starter":""},"innings":"9","date":"12/03/17","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}';
    if ($s_2017->getSchedule('pit') !== $expected) {
      print "Error 1<br>";
      print $s_2017->getSchedule('pit') . "<br>";
      print $expected . "<br>";
      exit;
    }
    $expected=["atl","bri","col","cws","kcr","lad","mia","mil","min","oak","pit","sfq","stl","tbp","was"];
    foreach ($expected as $team) {
      if (file_exists('../data/2017/' . $team . 'sched.html')) unlink('../data/2017/' . $team . 'sched.html');
    }
    $s_2017->generateSchedules();
    foreach ($expected as $team) {
      if (! file_exists('../data/2017/' . $team . 'sched.html')) {
        print "Error 2 - " . $team . "<br>";
        exit;
      }
    }
    $s_2018 = new \Scoring\Schedule(2018);
    print "Test successful<br>";  
  }

  function testScheduleItem() {
    $si = new \Scoring\ScheduleItem;
    $expected='{"scheduleItem":{"homeTeam":"","awayTeam":"","numberOfGames":"","season":"","results":[]}}';
    if ($si->toString() !== $expected) {
      print "Error 1<br>";
      print $si->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $si = \Scoring\ScheduleItem::newSI("Home", "Away", 3, \Scoring\Seasons::Fall);
    $si->results_[0] = new \Scoring\Game;
    $si->results_[0]->team_[0] = 'Away';
    $si->results_[0]->team_[1] = 'Home';
    $expected='{"scheduleItem":{"homeTeam":"Home","awayTeam":"Away","numberOfGames":"3","season":"1","results":[{"away":{"team":"Away","gameNumber":"0","runs":"0","hits":"0","errors":"0","starter":""},"home":"{"team":"Home","gameNumber":"0","runs":"0","hits":"0","errors":"0","starter":""},"innings":"9","date":"","scoreSheet":false,"winner":"","loser":"","save":"","day":false,"bps":{"left":"0","right":"0"},"bphr":"left":"0","right":"0"},"final":false}]}}';
    if ($si->toString() !== $expected) {
      print "Error 2<br>";
      print $si->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    print "Test successful<br>";  
  }

  function testSide() {
    $side = new \ProjectScoresheet\Side;
    $side = $side->sides();
    if ($side[0] !== \ProjectScoresheet\Side::Visitor || $side[1] !== \ProjectScoresheet\Side::Home) {
      print "Error 1<br>";
      print $side[0] . " is not " . \ProjectScoresheet\Side::Visitor . " or ";
      print $side[1] . " is not " . \ProjectScoresheet\Side::Home . "<br>";
      exit;
    }
    print "Test successful<br>";  
  }

  function testSituation() {
    $sit = new \ProjectScoresheet\Situation;
    $expected='{"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0","inning":"1","side":"0","first":"","second":"","third":"","batter":"","pitcher":"","gameOver":"false"}}';
    if ($sit->toString() !== $expected) {
      print "Error 1<br>";
      print $sit->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $sit->switchSides();
    $expected='{"situation":{"outs":"0","runsV":"0","runsH":"0","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0","inning":"1","side":"1","first":"","second":"","third":"","batter":"","pitcher":"","gameOver":"false"}}';
    if ($sit->toString() !== $expected) {
      print "Error 2<br>";
      print $sit->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $sit->runs[\ProjectScoresheet\Side::Visitor]=3;
    $sit->runs[\ProjectScoresheet\Side::Home]=3;
    $sit->inning=9;
    $expected='{"situation":{"outs":"0","runsV":"3","runsH":"3","hitsV":"0","hitsH":"0","errorsV":"0","errorsH":"0","inning":"9","side":"1","first":"","second":"","third":"","batter":"","pitcher":"","gameOver":"false"}}';
    if ($sit->toString() !== $expected) {
      print "Error 3<br>";
      print $sit->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    $sit->addRun();
    $sit->addHit();
    $sit->addError();
    $expected='{"situation":{"outs":"0","runsV":"3","runsH":"4","hitsV":"0","hitsH":"1","errorsV":"1","errorsH":"0","inning":"9","side":"1","first":"","second":"","third":"","batter":"","pitcher":"","gameOver":"true"}}';
    if ($sit->toString() !== $expected) {
      print "Error 4<br>";
      print $sit->toString() . "<br>";
      print $expected . "<br>";
      exit;
    }
    print "Test successful<br>";  
  }

  function testWhen() {
    $when = new \ProjectScoresheet\When;
    //$expected = "visitor: 0<br>home: 0<br>";
    $expected = '{"when":{"visitor":"0","home":"0"}}';
    if ($when->toString() !== $expected) {
      print "Error 1<br>";
    }
    $when->away(5);
    $when->home(10);
    //$expected = "visitor: 5<br>home: 10<br>";
    $expected = '{"when":{"visitor":"5","home":"10"}}';
    if ($when->toString() !== $expected) {
      print "Error 2<br>";
    }
    $when2 = \ProjectScoresheet\When::fromString($when->toString());
    if ($when->toString() !== $when2->toString()) {
      print "Error 3<br>";
      print $when->toString();
      print $when2->toString();
      exit;
    }
    $when3 = \ProjectScoresheet\When::fromString(json_encode($when2->json()));
    if ($when3->toString() !== $when2->toString()) {
      print "Error 4<br>";
      print $when3->toString();
      print $when2->toString();
      exit;
    }
    print "Test successful<br>";  
  }
}

?>
