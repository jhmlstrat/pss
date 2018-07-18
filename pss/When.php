<?php
  namespace ProjectScoresheet;
  class When {
    private $ab;
    function __construct() {
      $this->ab = array( "away" => 0, "home" => 0 );
    }
    public static function fromString($str) {
      $inst = new self();
      $js = json_decode($str);
      $inst->ab["away"] = intval($js->when->visitor);
      $inst->ab["home"] = intval($js->when->home);
      return $inst;
    }
    function away($ab) {
      $this->ab["away"] = $ab;
    }
    function home($ab) {
      $this->ab["home"] = $ab;
    }
    function toString() {
      return '{"when":{"visitor":"' . $this->ab["away"] . '","home":"' . $this->ab["home"] . '"}}';
    }
    function when() {
      return array($this->ab["away"],$this->ab["home"]);
    }
    public function json() {
      return json_decode($this->toString());
    }
  }
?>
