<?php
  namespace Jhml;

  class Rotation  {

    public $year;
    public $team = '';
    public $pitchers = array();
    private $RotationFile = "rotation";

    public function getRotation() {return $this->toString();}
    public function setRotation($data) {}

    private static function fromString($str) {
      $js = json_decode($str);
      $inst = new self($js->rotation->year,$js->rotation->team,false);
      for ($i =0; $i < count($js->rotation->rotation); $i++) {
        $gm = $js->rotation->rotation[$i]->game;
        $inst->pitchers[$gm-1] = $js->rotation->rotation[$i]->pitcher;
      }
      return $inst;
    }
    public function toString() {
      $rtn = '{"rotation":{"team":"' . $this->team . '","year":"' . $this->year . '"';
      $rtn .= ',"rotation":[';
      for ($i =0; $i < count($this->pitchers); $i++) {
        if ($i > 0) $rtn .= ',';
        $rtn .= '{"game":"' . ($i+1) . '"';
        $rtn .= ',"pitcher":"' . $this->pitchers[$i] . '"}';
      }
      $rtn .= ']}}';
      return $rtn;
    }
    public function json() {
      return json_decode($this->toString());
    }
    public static function writeRotationFile($str) {
      $js = json_decode($str);
      $inst = new self($js->rotation->year,$js->rotation->team,false);
      $inst = $inst->fromString($str);
      $inst->readGames();
      file_put_contents($inst->RotationFile,$inst->toString());
    }

    function __construct($year=2016, $team='',$doFile=true) {
      //print($year);
      //print($team);
      $this->year = $year;
      $this->team = $team;
      for ($i=0; $i<102; $i++) $this->pitchers[]='';
      $this->RotationFile = '../data/' . $year . '/' . $this->team . $this->RotationFile;
      if ($doFile && file_exists($this->RotationFile)) {
        $str = file_get_contents($this->RotationFile);
        $tmp = self::fromString($str);
        for ($i=0; $i<102; $i++) $this->pitchers[$i]=$tmp->pitchers[$i];
      }
      if ($doFile) $this->readGames();
    }
    private function readGames() {
    }
  }
?>
