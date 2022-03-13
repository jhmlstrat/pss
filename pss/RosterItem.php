<?php

  namespace Scoring;

  require_once "Injury.php";
  require_once "Move.php";
  require_once "Player.php";

class RosterItem
{
    public $player;
    public $team;
    public $moves;
    public $injuries;
    public $startGame;
    public $endGame;

    public function __construct()
    {
        $this->player =  new \ProjectScoreSheet\Player();
        $this->team = '';
        $this->moves = [];
        $this->injuries = [];
        $this->startGame = 0;
        $this->endGame = 999;
    }
    public static function fromRosterFileString($str)
    {
        $inst = new self();
        $pieces = explode("\t", chop($str));
        $inst->player = new \ProjectScoreSheet\Player();
        $inst->player->name = $pieces[0];
        $inst->player->age = $pieces[1];
        $inst->team = $pieces[2];
        return $inst;
    }
    public function toRosterFileString()
    {
        return $this->player->name . "\t". $this->player->age . "\t" . $this->team;
    }
    private function _normalize($str)
    {
        return 
            str_replace("-", "", str_replace(".", "", str_replace("'", "", $str)));
    }
    public static function processTradedFor($str)
    {
        $rtn = [];
        $pieces = explode(":", chop($str));
        if (count($pieces) != 14) {
            return;
        }
        $gameNo = intval($pieces[0]);
        for ($i=4; $i <= 13; $i++) {
            $inner = explode("~", $pieces[$i]);
            if ($inner[2] == "PITCHERS" || $inner[2] == "BATTERS") {
                continue;
            }
            if ($inner[3] == MoveType::toString(MoveType::TRADED_FOR)) {
                if ($inner[2] == "none") {
                    continue;
                }
                $inst = new self();
                $inst->player = new \ProjectScoreSheet\Player();
                $inst->player->name = $inner[2];
                $inst->startGame = $gameNo;
                $move = new Move();
                $move->gameNumber = $gameNo;
                $move->moveType = MoveType::TRADED_FOR;
                $inst->moves[] = $move;
                $rtn[] = $inst;
            }
        }
        return $rtn;
    }
    public static function addMoveFileString(&$ri, $str)
    {
        $player = $ri->player;
        $pieces = explode(":", chop($str));
        if (count($pieces) != 14) {
            return;
        }
        $gameNo = intval($pieces[0]);
        for ($i=1; $i <= 3; $i++) {
            $inner = explode("~", $pieces[$i]);
            if ($ri->_normalize($player->name) == $inner[0]) {
                $inj = new Injury();
                $inj->gameNumber = $gameNo;
                if ($inner[1] == 'REM') {
                    $inj->duration = 0;
                } else {
                    $inj->duration = intval($inner[1]);
                }
                $ri->injuries[] = $inj;
                if ($inner[2] != 'none') {
                    RosterItem::processMove($ri, $gameNo + 1, MoveType::ON_DL);
                }
            }
        }
        for ($i=4; $i <= 13; $i++) {
            $inner = explode("~", $pieces[$i]);
            if ($ri->_normalize($player->name) == $inner[0]) {
                // print "processMove: " . $inner[0] . "  " . $inner[1] . "<br/>";
                RosterItem::processMove(
                    $ri, $gameNo, MoveType::fromString($inner[1])
                );
            }
            if ($ri->_normalize($player->name) == $inner[2]) {
                // print "processMove: " . $inner[2] . "  " . $inner[3] . "<br/>";
                RosterItem::processMove(
                    $ri, $gameNo, MoveType::fromString($inner[3])
                );
            }
        }
    }
    public static function processMove(&$ri, $gameNo, $moveType)
    {
        $doIt = true;
        if ($moveType == MoveType::ON_DL or $moveType == MoveType::TRADED_FOR) {
            for ($j=0; $j < count($ri->moves); $j++) {
                if ($ri->moves[$j]->gameNumber == $gameNo
                    && ($ri->moves[$j]->moveType == MoveType::ON_DL
                    || $ri->moves[$j]->moveType == MoveType::TRADED_FOR)
                ) {
                    $doIt = false;
                }
            }
        }
        if ($doIt) {
            $move = new Move();
            $move->gameNumber = $gameNo;
            $move->moveType = $moveType;
            if ($moveType == MoveType::TRADED_AWAY) {
                $ri->endGame = $gameNo -1;
            }
            $ri->moves[] = $move;
        }
    }
    public static function fromString($str)
    {
        $inst = new self();
        $js = json_decode($str);
        $ri = $js->rosterItem;
        $player = $ri->player;
        $ps = '{"player":' . json_encode($player) . '}';
        $inst->player = \ProjectScoreSheet\Player::fromString($ps);
        $moves = $ri->moves;
        for ($i=0; $i < count($moves); $i++) {
            $inst->moves[] = \Scoring\Move::fromString(json_encode($moves[$i]));
        }
        $injuries = $ri->injuries;
        for ($i=0; $i < count($injuries); $i++) {
            $inst->injuries[] 
                = \Scoring\Injury::fromString(json_encode($injuries[$i]));
        }
        $inst->startGame = $ri->startGame;
        $inst->endGame = $ri->endGame;
        return $inst;
    }
    public function toString($includeStrat = false)
    {
        $rtn = '{"rosterItem":' . 
            substr($this->player->toString($includeStrat), 0, -1) . 
            ',"team":"' . $this->team . '"';
        $rtn .= ',"moves":[';
        for ($i =0; $i < count($this->moves); $i++) {
            if ($i > 0) {
                $rtn .= ',';
            }
            $rtn .= $this->moves[$i]->toString();
        }
        $rtn .= ']';
        $rtn .= ',"injuries":[';
        for ($i =0; $i < count($this->injuries); $i++) {
            if ($i > 0) {
                $rtn .= ',';
            }
            $rtn .= $this->injuries[$i]->toString();
        }
        $rtn .= ']';
        $rtn .= ',"startGame":"' . $this->startGame . '"';
        $rtn .= ',"endGame":"' . $this->endGame . '"';
        $rtn .= '}}';
        return $rtn;
    }
    public function json()
    {
        return json_decode($this->toString());
    }
    public function isMajors($gameNo)
    {
        $rtn = false;
        if ($this->startGame <= $gameNo && $this->endGame >= $gameNo) {
            $lastMove = MoveType::TO_MAJORS;
            for ($j=0; $j < count($this->moves); $j++) {
                if ($this->moves[$j]->gameNumber <= $gameNo) {
                    $lastMove = $this->moves[$j]->moveType;
                }
            }
            if ($lastMove == MoveType::TO_MAJORS or $lastMove == MoveType::OFF_DL
                or $lastMove == MoveType::TRADED_FOR
            ) {
                $rtn=true;
            }
        }
        return $rtn;
    }
    public function isMinors($gameNo)
    {
        $rtn = false;
        if ($this->startGame <= $gameNo && $this->endGame >= $gameNo) {
            $lastMove = MoveType::TO_MAJORS;
            for ($j=0; $j < count($this->moves); $j++) {
                if ($this->moves[$j]->gameNumber <= $gameNo) {
                    $lastMove = $this->moves[$j]->moveType;
                }
            }
            if ($lastMove == MoveType::TO_MINORS or $lastMove == MoveType::ON_DL) {
                $rtn=true;
            }
        }
        return $rtn;
    }
    public function addInjury($gameNo, $duration)
    {
        $injury = new Injury();
        $injury->gameNumber = $gameNo;
        $injury->duration = $duration;
        array_push($this->injuries, $injury);
    }
    public function isInjured($gameNo, $restAfter = [])
    {
        $rtn = false;
        if ($this->startGame <= $gameNo && $this->endGame >= $gameNo) {
            $lastInjury = new Injury();
            for ($j=0; $j < count($this->injuries); $j++) {
                if ($this->injuries[$j]->gameNumber <= $gameNo) {
                    $lastInjury = $this->injuries[$j];
                }
            }
            if ($lastInjury->duration != 0) {
                $daysRest = 0;
                for ($j=0; $j < count($restAfter); $j++) {
                    if ($restAfter[$j] >= $lastInjury->gameNumber 
                        && $restAfter[$j] < $gameNo
                    ) {
                        $daysRest ++;
                    }
                }
                $tempg = $lastInjury->gameNumber + $lastInjury->duration - $daysRest;
                if ($tempg >= $gameNo) {
                    $rtn = true;
                }
            }
        }
        return $rtn;
    }
    public static function cmp($a, $b)
    {
        if ($a->team == $b->team) {
            return strcmp($a->player->name, $b->player->name);
        }
        return strcmp($a->team, $b->team);
    }
}
?>
