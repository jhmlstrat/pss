<?php

namespace Scoring;

require_once "FielderStats.php";

class BatterStats {
    public $name;
    public $ab;
    public $run;
    public $hit;
    public $rbi;
    public $d;
    public $t;
    public $hr;
    public $bb;
    public $k;
    public $dp;
    public $hbp;
    public $sac;
    public $sf;
    public $sb;
    public $cs;
    public $beginPlay;
    public $bpsMade;
    public $bpsMissed;
    public $bphrMade;
    public $bphrMissed;

    public function __construct($name) {
        $this->name = $name;
        $this->ab = 0;
        $this->run = 0;
        $this->hit = 0;
        $this->rbi = 0;
        $this->d = 0;
        $this->t = 0;
        $this->hr = 0;
        $this->bb = 0;
        $this->k = 0;
        $this->dp = 0;
        $this->hbp = 0;
        $this->sac = 0;
        $this->sf = 0;
        $this->sb = 0;
	$this->cs = 0;
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
        $fielderStats = null;
        list($name, $ab, $run, $hit, $rbi, $d, $t, $hr, $bb, $k, $e, $dp, $hbp, $sac, $sf, $sb, $cs) = explode ('~', $str);
        $inst = new self($name);
        $inst->ab = $ab;
        $inst->run = $run;
        $inst->hit = $hit;
        $inst->rbi = $rbi;
        $inst->d = $d;
        $inst->t = $t;
        $inst->hr = $hr;
        $inst->bb = $bb;
        $inst->k = $k;
        $inst->dp = $dp;
        $inst->hbp = $hbp;
        $inst->sac = $sac;
        $inst->sf = $sf;
        $inst->sb = $sb;
        $inst->cs = $cs;
        if ($e != 0) {
            $fielderStats = new FielderStats($name);
            $fielderStats->e = $e;
        }
        // return list($inst, $fielderStats);
        return [$inst, $fielderStats];
    }

    public function toStatFileString($fielderStats) {
        $e = 0;
        for ($i = 0; $i < count($fielderStats); $i++) {
            if ($fielderStats[$i]->name == $this->name) $e = $e + $fielderStats[$i]->e;
        }
        $rtn = $this->name . "~";
        $rtn .= $this->ab . "~";
        $rtn .= $this->run . "~";
        $rtn .= $this->hit . "~";
        $rtn .= $this->rbi . "~";
        $rtn .= $this->d . "~";
        $rtn .= $this->t . "~";
        $rtn .= $this->hr . "~";
        $rtn .= $this->bb . "~";
        $rtn .= $this->k . "~";
        $rtn .= $e . "~";
        $rtn .= $this->dp . "~";
        $rtn .= $this->hbp . "~";
        $rtn .= $this->sac . "~";
        $rtn .= $this->sf . "~";
        $rtn .= $this->sb . "~";
        $rtn .= $this->cs;
        return $rtn;
    }

    public static function parseStatFileLine($line) {
        $pieces = explode(":", trim($line));
        $game = $pieces[0];
        unset($pieces[0]);
        // print($game . "\n");
        // print_r($pieces);
        $bats = array();
        $es = array();
        foreach ($pieces as $piece) {
            // print("\t" . $piece . "\n");
            list($b,$e) = BatterStats::fromStatFileString($piece);
            // print_r($b);
            // print_r($e);
            array_push($bats,$b);
            if ($e != null) array_push($es,$e);
	}
	return [$game, $bats, $es];
    }

    public function toJsonString() {
        // TBD
    }
}

?>

