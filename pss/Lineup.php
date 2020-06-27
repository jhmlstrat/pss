<?php
  namespace ProjectScoresheet;
  require_once 'Player.php';
  class Lineup {
    private $battingOrder;
    private $pitchingOrder;
    function __construct() {
      $this->battingOrder=array();
      for ($i = 0; $i < 9; $i ++) {
        $this->battingOrder[$i]=array();
      }
      $this->pitchingOrder=array();
     }
    public static function fromString($str) {
      $inst = new self();
      #print $str . "\n";
      $lu = json_decode('{' . preg_replace('/,"rotation".*/','',$str) . '}');
      $ro = json_decode('{' . preg_replace('/.*,"rotation"/','"rotation"',$str) . '}');
      foreach ($lu as $rows) {
        $i = 0;
        if ($rows != null){
          foreach ($rows as $row) {
            foreach ($row as $p) {
              array_push($inst->battingOrder[$i],Player::fromString(json_encode($p)));
            }
            $i ++;
          }
        }
      }
      foreach ($ro as $row) {
        if ($row != null) {
          foreach ($row as $p) {
            array_push($inst->pitchingOrder,Player::fromString(json_encode($p)));
          }
        }
      }
      return $inst;
    }
    public function toString() {
      $rtn = '"lineup":[[';
      for ($i=0; $i < count($this->battingOrder); $i ++) {
        if ($i > 0) $rtn .= '],[';
        for ($j=0; $j < count($this->battingOrder[$i]); $j ++) {
          if ($j > 0) $rtn .= ',';
          $rtn .= $this->battingOrder[$i][$j]->toString();
        }
      }
      $rtn .= ']],"rotation":[';
      for ($i=0; $i < count($this->pitchingOrder); $i ++) {
        if ($i > 0) $rtn .= ',';
        $rtn .= $this->pitchingOrder[$i]->toString();
      }
      $rtn .= ']';
      
      return $rtn;
    }
  public function isValid() {
    $rtn = (count($this->pitchingOrder) !== 0);
    $tmp=array();
    for ($i = 0; $i < 9; $i ++) {
      $tmp[$i]=false;
    }
    for ($i = 0; $i < 9; $i ++) {
      $c = count($this->battingOrder[$i]);
      if ($c > 0) {
        $play = $this->battingOrder[$i][$c-1];
        $c = count($play->positions);
        if ($c > 0) {
          $ord = $play->positions[$c - 1]->position;
          if ($ord === 0) { $ord = 1; }
          if ($ord <= count($tmp)) $tmp[$ord-1] = true;
        }
      }
    }
    for ($i=0; $i<9; $i ++) {$rtn = $rtn && $tmp[$i];}
    return $rtn;
  }
    public function getCurrentPitcher () {
      if (count($this->pitchingOrder) == 0) {return null;}
      return $this->pitchingOrder[count($this->pitchingOrder)-1];
    }
    public function getHitter($spot) {
      if (count($this->battingOrder[$spot]) == 0) {return null;}
      return $this->battingOrder[$spot][count($this->battingOrder[$spot])-1];
    }
    public function getHitters($spot) {
      return $this->battingOrder[$spot];
    }
    public function getPitchers() {
      return $this->pitchingOrder;
    }
    public function insertIntoBat ($spot, $player) {
      $this->battingOrder[$spot][] = $player;
    }
    public function insertIntoPitch ($player) {
      $this->pitchingOrder[] = $player;
    }
    public function movePlayer ($spot, $pos) {
      $this->battingOrder[$spot][count($this->battingOrder[$spot])-1]->newPosition($pos);
    }
  }

/*
  $lineup = new Lineup;
  $expected = "lineup:\n  -\n  -\n  -\n  -\n  -\n  -\n  -\n  -\n  -\nrotation:\n";
  if ($lineup->toString() !== $expected) {
    print "Error 1\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 1.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('P');
  $pos->when(0,0);
  $play = Player::initial('Pettitte',$pos);
  $lineup->insertIntoPitch($play);
  $expected = "lineup:\n  -\n  -\n  -\n  -\n  -\n  -\n  -\n  -\n  -\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n";
  if ($lineup->toString() !== $expected) {
    print "Error 2\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 2.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('RF');
  $pos->when(0,0);
  $play = Player::initial('Suzuki',$pos);
  $lineup->insertIntoBat(0,$play);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n  -\n  -\n  -\n  -\n  -\n  -\n  -\n  -\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n";
  if ($lineup->toString() !== $expected) {
    print "Error 3\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 3.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('CF');
  $pos->when(0,0);
  $play = Player::initial('McCutchen',$pos);
  $lineup->insertIntoBat(1,$play);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n  -\n  -\n  -\n  -\n  -\n  -\n  -\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n";
  if ($lineup->toString() !== $expected) {
    print "Error 4\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 4.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('LF');
  $pos->when(0,0);
  $play = Player::initial('Bay',$pos);
  $lineup->insertIntoBat(2,$play);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n  -\n  -\n  -\n  -\n  -\n  -\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n";
  if ($lineup->toString() !== $expected) {
    print "Error 5\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 5.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('DH');
  $pos->when(0,0);
  $play = Player::initial('Willingham',$pos);
  $lineup->insertIntoBat(3,$play);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n  -\n    - name: Willingham\n      positions:\n        - position: DH\n          visitor: 0\n          home: 0\n  -\n  -\n  -\n  -\n  -\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n";
  if ($lineup->toString() !== $expected) {
    print "Error 6\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 6.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('B3');
  $pos->when(0,0);
  $play = Player::initial('Rodriguez',$pos);
  $lineup->insertIntoBat(4,$play);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n  -\n    - name: Willingham\n      positions:\n        - position: DH\n          visitor: 0\n          home: 0\n  -\n    - name: Rodriguez\n      positions:\n        - position: B3\n          visitor: 0\n          home: 0\n  -\n  -\n  -\n  -\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n";
  if ($lineup->toString() !== $expected) {
    print "Error 7\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 7.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('B1');
  $pos->when(0,0);
  $play = Player::initial('Morneau',$pos);
  $lineup->insertIntoBat(5,$play);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n  -\n    - name: Willingham\n      positions:\n        - position: DH\n          visitor: 0\n          home: 0\n  -\n    - name: Rodriguez\n      positions:\n        - position: B3\n          visitor: 0\n          home: 0\n  -\n    - name: Morneau\n      positions:\n        - position: B1\n          visitor: 0\n          home: 0\n  -\n  -\n  -\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n";
  if ($lineup->toString() !== $expected) {
    print "Error 8\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 8.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('B2');
  $pos->when(0,0);
  $play = Player::initial('Walker',$pos);
  $lineup->insertIntoBat(6,$play);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n  -\n    - name: Willingham\n      positions:\n        - position: DH\n          visitor: 0\n          home: 0\n  -\n    - name: Rodriguez\n      positions:\n        - position: B3\n          visitor: 0\n          home: 0\n  -\n    - name: Morneau\n      positions:\n        - position: B1\n          visitor: 0\n          home: 0\n  -\n    - name: Walker\n      positions:\n        - position: B2\n          visitor: 0\n          home: 0\n  -\n  -\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n";
  if ($lineup->toString() !== $expected) {
    print "Error 9\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 9.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('C');
  $pos->when(0,0);
  $play = Player::initial('Pena',$pos);
  $lineup->insertIntoBat(7,$play);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n  -\n    - name: Willingham\n      positions:\n        - position: DH\n          visitor: 0\n          home: 0\n  -\n    - name: Rodriguez\n      positions:\n        - position: B3\n          visitor: 0\n          home: 0\n  -\n    - name: Morneau\n      positions:\n        - position: B1\n          visitor: 0\n          home: 0\n  -\n    - name: Walker\n      positions:\n        - position: B2\n          visitor: 0\n          home: 0\n  -\n    - name: Pena\n      positions:\n        - position: C\n          visitor: 0\n          home: 0\n  -\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n";
  if ($lineup->toString() !== $expected) {
    print "Error 10\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 10.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('SS');
  $pos->when(0,0);
  $play = Player::initial('Ripken',$pos);
  $lineup->insertIntoBat(8,$play);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n  -\n    - name: Willingham\n      positions:\n        - position: DH\n          visitor: 0\n          home: 0\n  -\n    - name: Rodriguez\n      positions:\n        - position: B3\n          visitor: 0\n          home: 0\n  -\n    - name: Morneau\n      positions:\n        - position: B1\n          visitor: 0\n          home: 0\n  -\n    - name: Walker\n      positions:\n        - position: B2\n          visitor: 0\n          home: 0\n  -\n    - name: Pena\n      positions:\n        - position: C\n          visitor: 0\n          home: 0\n  -\n    - name: Ripken\n      positions:\n        - position: SS\n          visitor: 0\n          home: 0\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n";
  if ($lineup->toString() !== $expected) {
    print "Error 11\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if (! $lineup->isValid()) {
    print "Error 11.1 - valid check\n";
    exit;
  }
  $expected = "Pettitte";
  if ($lineup->getCurrentpitcher()->name !== $expected) {
    print "Error 12\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('P');
  $pos->when(1,1);
  $play = Player::initial('Uehara',$pos);
  $lineup->insertIntoPitch($play);
  $expected = "Uehara";
  if ($lineup->getCurrentpitcher()->name !== $expected) {
    print "Error 13\n";
    exit;
  }
  $pitchers = $lineup->getPitchers();
  if (count($pitchers) !== 2) {
    print "Error 13.1\n";
    exit;
  }
  $expected = "Pettitte";
  if ($pitchers[0]->name !== $expected) {
    print "Error 13.2\n";
    exit;
  }
  $expected = "Uehara";
  if ($pitchers[1]->name !== $expected) {
    print "Error 13.3\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('CF');
  $pos->when(1,1);
  $lineup->movePlayer(0,$pos);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n        - position: CF\n          visitor: 1\n          home: 1\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n  -\n    - name: Willingham\n      positions:\n        - position: DH\n          visitor: 0\n          home: 0\n  -\n    - name: Rodriguez\n      positions:\n        - position: B3\n          visitor: 0\n          home: 0\n  -\n    - name: Morneau\n      positions:\n        - position: B1\n          visitor: 0\n          home: 0\n  -\n    - name: Walker\n      positions:\n        - position: B2\n          visitor: 0\n          home: 0\n  -\n    - name: Pena\n      positions:\n        - position: C\n          visitor: 0\n          home: 0\n  -\n    - name: Ripken\n      positions:\n        - position: SS\n          visitor: 0\n          home: 0\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n  - name: Uehara\n    visitor: 1\n    home: 1\n";
  if ($lineup->toString() !== $expected) {
    print "Error 14\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 14.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('RF');
  $pos->when(1,1);
  $lineup->movePlayer(1,$pos);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n        - position: CF\n          visitor: 1\n          home: 1\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n        - position: RF\n          visitor: 1\n          home: 1\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n  -\n    - name: Willingham\n      positions:\n        - position: DH\n          visitor: 0\n          home: 0\n  -\n    - name: Rodriguez\n      positions:\n        - position: B3\n          visitor: 0\n          home: 0\n  -\n    - name: Morneau\n      positions:\n        - position: B1\n          visitor: 0\n          home: 0\n  -\n    - name: Walker\n      positions:\n        - position: B2\n          visitor: 0\n          home: 0\n  -\n    - name: Pena\n      positions:\n        - position: C\n          visitor: 0\n          home: 0\n  -\n    - name: Ripken\n      positions:\n        - position: SS\n          visitor: 0\n          home: 0\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n  - name: Uehara\n    visitor: 1\n    home: 1\n";
  if ($lineup->toString() !== $expected) {
    print "Error 15\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if (! $lineup->isValid()) {
    print "Error 15.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('CF');
  $pos->when(2,2);
  $play = Player::initial('Figgins',$pos);
  $lineup->insertIntoBat(0,$play);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n        - position: CF\n          visitor: 1\n          home: 1\n    - name: Figgins\n      positions:\n        - position: CF\n          visitor: 2\n          home: 2\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n        - position: RF\n          visitor: 1\n          home: 1\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n  -\n    - name: Willingham\n      positions:\n        - position: DH\n          visitor: 0\n          home: 0\n  -\n    - name: Rodriguez\n      positions:\n        - position: B3\n          visitor: 0\n          home: 0\n  -\n    - name: Morneau\n      positions:\n        - position: B1\n          visitor: 0\n          home: 0\n  -\n    - name: Walker\n      positions:\n        - position: B2\n          visitor: 0\n          home: 0\n  -\n    - name: Pena\n      positions:\n        - position: C\n          visitor: 0\n          home: 0\n  -\n    - name: Ripken\n      positions:\n        - position: SS\n          visitor: 0\n          home: 0\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n  - name: Uehara\n    visitor: 1\n    home: 1\n";
  if ($lineup->toString() !== $expected) {
    print "Error 16\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if (! $lineup->isValid()) {
    print "Error 16.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('LF');
  $pos->when(3,3);
  $lineup->movePlayer(3,$pos);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n        - position: CF\n          visitor: 1\n          home: 1\n    - name: Figgins\n      positions:\n        - position: CF\n          visitor: 2\n          home: 2\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n        - position: RF\n          visitor: 1\n          home: 1\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n  -\n    - name: Willingham\n      positions:\n        - position: DH\n          visitor: 0\n          home: 0\n        - position: LF\n          visitor: 3\n          home: 3\n  -\n    - name: Rodriguez\n      positions:\n        - position: B3\n          visitor: 0\n          home: 0\n  -\n    - name: Morneau\n      positions:\n        - position: B1\n          visitor: 0\n          home: 0\n  -\n    - name: Walker\n      positions:\n        - position: B2\n          visitor: 0\n          home: 0\n  -\n    - name: Pena\n      positions:\n        - position: C\n          visitor: 0\n          home: 0\n  -\n    - name: Ripken\n      positions:\n        - position: SS\n          visitor: 0\n          home: 0\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n  - name: Uehara\n    visitor: 1\n    home: 1\n";
  if ($lineup->toString() !== $expected) {
    print "Error 17\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if ($lineup->isValid()) {
    print "Error 17.1 - valid check\n";
    exit;
  }
  $pos = new Position;
  $pos->position = Position::position('P');
  $pos->when(3,3);
  $play = Player::initial('Clemens',$pos);
  $lineup->insertIntoBat(2,$play);
  $lineup->insertIntoPitch($play);
  $expected = "lineup:\n  -\n    - name: Suzuki\n      positions:\n        - position: RF\n          visitor: 0\n          home: 0\n        - position: CF\n          visitor: 1\n          home: 1\n    - name: Figgins\n      positions:\n        - position: CF\n          visitor: 2\n          home: 2\n  -\n    - name: McCutchen\n      positions:\n        - position: CF\n          visitor: 0\n          home: 0\n        - position: RF\n          visitor: 1\n          home: 1\n  -\n    - name: Bay\n      positions:\n        - position: LF\n          visitor: 0\n          home: 0\n    - name: Clemens\n      positions:\n        - position: P\n          visitor: 3\n          home: 3\n  -\n    - name: Willingham\n      positions:\n        - position: DH\n          visitor: 0\n          home: 0\n        - position: LF\n          visitor: 3\n          home: 3\n  -\n    - name: Rodriguez\n      positions:\n        - position: B3\n          visitor: 0\n          home: 0\n  -\n    - name: Morneau\n      positions:\n        - position: B1\n          visitor: 0\n          home: 0\n  -\n    - name: Walker\n      positions:\n        - position: B2\n          visitor: 0\n          home: 0\n  -\n    - name: Pena\n      positions:\n        - position: C\n          visitor: 0\n          home: 0\n  -\n    - name: Ripken\n      positions:\n        - position: SS\n          visitor: 0\n          home: 0\nrotation:\n  - name: Pettitte\n    visitor: 0\n    home: 0\n  - name: Uehara\n    visitor: 1\n    home: 1\n  - name: Clemens\n    visitor: 3\n    home: 3\n";
  if ($lineup->toString() !== $expected) {
    print "Error 18\n";
    print $lineup->toString();
    print $expected;
    exit;
  }
  if (! $lineup->isValid()) {
    print "Error 18.1 - valid check\n";
    exit;
  }
  if (Lineup::fromString($lineup->toString())->toString() !== $lineup->toString()) {
    print "Error 19\n";
    print Lineup::fromString($lineup->toString())->toString();
    print $lineup->toString();
    exit;
  }
  print "Test successful\n";  
*/
?>
