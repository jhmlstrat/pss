<?php

namespace Scoring;

class PitcherStats {
    public $name;
    public $start;
    public $w;
    public $l;
    public $s;
    public $ip;
    public $h;
    public $r;
    public $er;
    public $bb;
    public $k;
    public $hr;
    public $wildp;
    public $hbp;
    public $balk;
    public $beginPlay;
    public $bpsMade;
    public $bpsMissed;
    public $bphrMade;
    public $bphrMissed;

    public function __construct($name) {
        $this->name = $name;
        $this->start = 0;
        $this->w = 0;
        $this->l = 0;
        $this->s = 0;
        $this->ip = 0.0;
        $this->h = 0;
        $this->r = 0;
        $this->er = 0;
        $this->bb = 0;
        $this->k = 0;
        $this->hr = 0;
        $this->wildp = 0;
        $this->hbp = 0;
        $this->balk = 0;
	$this->beginPlay = array();
	$this->beginPlay[0] = 0;
	$this->beginPlay[1] = 0;
        $this->bpsMade = 0;
        $this->bpsMissed = 0;
        $this->bphrMade = 0;
        $this->bphrMissed = 0;
    }

    private static function cmp($a, $b) {
        return $b->name <=> $a->name;
    }
   
    public static function fromStatFileString($str) {
	// print($str . "\n");
        list($name, $start, $w, $l, $s, $ip, $h, $r, $er, $bb, $k, $hr, $wildp, $hbp, $balk) = explode ('~', $str);
	$inst = new self($name);
        $inst->start = $start;
        $inst->w = $w;
        $inst->l = $l;
        $inst->s = $s;
        $inst->ip = floatval($ip);
        $inst->h = $h;
        $inst->r = $r;
        $inst->er = $er;
        $inst->bb = $bb;
        $inst->k = $k;
        $inst->hr = $hr;
        $inst->wildp = $wildp;
        $inst->hbp = $hbp;
	$inst->balk = $balk;
	// print_r($inst);
	return $inst;
    }

    public function toStatFileString() {
        $rtn = $this->name . "~";
        $rtn .= $this->start . "~";
        $rtn .= $this->w . "~";
        $rtn .= $this->l . "~";
        $rtn .= $this->s . "~";
        $rtn .= number_format($this->ip,1,".","") . "~";
        $rtn .= $this->h . "~";
        $rtn .= $this->r . "~";
        $rtn .= $this->er . "~";
        $rtn .= $this->bb . "~";
        $rtn .= $this->k . "~";
        $rtn .= $this->hr . "~";
        $rtn .= $this->wildp . "~";
        $rtn .= $this->hbp . "~";
	$rtn .= $this->balk; 
	return $rtn;
    }

    public static function parseStatFileLine($line) {
        $pieces = explode(":", trim($line));
        $game = $pieces[0];
        unset($pieces[0]);
        // print($game . "\n");
        // print_r($pieces);
        $pits = array();
        foreach ($pieces as $piece) {
            // print("\t" . $piece . "\n");
            $p = PitcherStats::fromStatFileString($piece);
            array_push($pits,$p);
	}
	return [$game, $pits];
 
    }

    public function toJsonString() {
        // TBD
    }

    public function addOut() {
	$i = intval(round($this->ip,1) * 10 + 1);
	$thirds = $i % 10;
	if ($thirds == 3) $i = $i + 7;
	$this->ip = $i / 10.0;
    }
}

?>

