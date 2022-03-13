<?php

  namespace ProjectScoresheet;

class Result
{
    public $before;
    public $during;
    public $after;
    public function __construct()
    {
        $this->before='';
        $this->during='';
        $this->after='';
    }
    public static function fromString($str)
    {
        $inst = new self();
        $js = json_decode($str);
        if (property_exists($js->result, "before")) {
            $inst->before = $js->result->before;
        }
        if (property_exists($js->result, "during")) {
            $inst->during = $js->result->during;
        }
        if (property_exists($js->result, "after")) {
            $inst->after = $js->result->after;
        } else {
            if (property_exists($js->result, "during") && $js->result->during != "") {
                $inst->after = 'Bx';
            }
        }
        return $inst;
    }
    public function toString()
    {
        $rtn='{"result":{';
        if ($this->before !== '') {
            $rtn .= '"before":"' . $this->before . '",';
        }
        $rtn .= '"during":"' . $this->during . '"';
        if ($this->after !== '' and $this->after !== 'Bx') {
            $rtn .= ',"after":"' . $this->after . '"';
        }
        // if ($this->after !== '') $rtn .= ',"after":"' . $this->after . '"';
        $rtn .= '}}';
        return $rtn;
    }
    public function json()
    {
        return json_decode($this->toString());
    }
}
?>
