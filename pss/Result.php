<?php
  namespace ProjectScoresheet;
  class Result {
    public $before;
    public $during;
    public $after;
    function __construct() {
      $this->before='';
      $this->during='';
      $this->after='';
    }
    public static function fromString($str) {
      $inst = new self();
      $js = json_decode($str);
      if (array_key_exists("before",$js->result)) $inst->before = $js->result->before;
      if (array_key_exists("during",$js->result)) $inst->during = $js->result->during;
      if (array_key_exists("after",$js->result)) $inst->after = $js->result->after;
      return $inst;
    }
    public function toString() {
      $rtn='{"result":{';
      if ($this->before !== '') $rtn .= '"before":"' . $this->before . '",';
      $rtn .= '"during":"' . $this->during . '"';
      #if ($this->after !== '' and $this->after !== 'Bx') $rtn .= ',"after":"' . $this->after . '"';
      if ($this->after !== '') $rtn .= ',"after":"' . $this->after . '"';
      $rtn .= '}}';
      return $rtn;
    }
    public function json() {
      return json_decode($this->toString());
    }
  }
?>
