<?php

  namespace ProjectScoresheet;

class When
{
    private $_ab;
    public function __construct()
    {
        $this->_ab = array( "away" => 0, "home" => 0 );
    }
    public static function fromString($str)
    {
        $inst = new self();
        $js = json_decode($str);
        $inst->_ab["away"] = intval($js->when->visitor);
        $inst->_ab["home"] = intval($js->when->home);
        return $inst;
    }
    public function away($ab)
    {
        $this->_ab["away"] = $ab;
    }
    public function home($ab)
    {
        $this->_ab["home"] = $ab;
    }
    public function toString()
    {
        return '{"when":{"visitor":"' . $this->_ab["away"] . 
            '","home":"' . $this->_ab["home"] . '"}}';
    }
    public function when()
    {
        return array($this->_ab["away"],$this->_ab["home"]);
    }
    public function json()
    {
        return json_decode($this->toString());
    }
}
?>
