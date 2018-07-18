<?php
  namespace Scoring;
  require_once 'MoveType.php';

  class Move {
    public $moveType=0;
    public $gameNumber=0;

    public static function fromString($str) {
      $inst = new self();
      $js = json_decode($str);
      if (array_key_exists("moveType",$js->move)) $inst->moveType = MoveType::fromString($js->move->moveType);
      if (array_key_exists("gameNumber",$js->move)) $inst->gameNumber = intval($js->move->gameNumber);
      return $inst;
    }
    public function toString() {
      return '{"move":{"moveType":"' . MoveType::toString($this->moveType) .'","gameNumber":"' . $this->gameNumber . '"}}';
    }
  }
?>
