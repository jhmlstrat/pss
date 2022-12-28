<?php

namespace Scoring;

require_once "BatterStats.php";
require_once "Game.php";
require_once "Injury.php";
require_once "Move.php";
require_once "PitcherStats.php";

class GameStats {
    private $config;

    private $year;
    private $team;
    private $batStats;
    private $pitchStats;
    private $fieldStats;

    public function __construct($year, $team) {
        $json = file_get_contents("../data/config.json");
        $confs = json_decode($json, true);
        foreach ($confs['years'] as $conf) {
            if ($conf['year'] == $year) {
                $this->config = $conf;
            }
        }
        $this->year = $year;
        $this->$team = $team;
        $this->batStats = array();
        $this->pitchStats = array();
        $this->fieldStats = array();
    }

    public static function buildOldStatFiles($year, $team) {
        //TBD
        $bats = array();
        $es = array();
        $bat = fopen('../../cgi-bin/jhml/data/' . $year . '/' . $team . 'bat', 'r');
        while (($line = fgets($bat)) !== false) {
            list ($game, $b, $e) = BatterStats::parseStatFileLine($line); 
            $bats[$game] = $b;
            $es[$game] = $e;
        }
        // print_r($bats);
        // print_r($es);
        fclose($bat);

        $game = fopen('../../cgi-bin/jhml/data/' . $year . '/' . $team . 'game', 'r');
        $games = array();
        while (($line = fgets($game)) !== false) {
            list ($gameNo, $g) = Game::parseStatFileLine($line); 
            $games[$gameNo] = $g;
        }
        // print_r($games);
        fclose($game);

        $moves = fopen('../../cgi-bin/jhml/data/' . $year . '/' . $team . 'moves', 'r');
        $injuries = array();
        $transactions = array();
        while (($line = fgets($moves)) !== false) {
            list ($game, $i1, $i2, $i3, $t1, $t2, $t3, $t4, $t5, $t6, $t7, $t8, $t9, $t10) = explode (":", trim($line));
            $injuries[$game] = [Injury::fromStatFileString($i1),Injury::fromStatFileString($i2),Injury::fromStatFileString($i3)];
            $transactions[$game] = [Move::fromStatFileString($t1), Move::fromStatFileString($t2), Move::fromStatFileString($t3),
                                    Move::fromStatFileString($t4), Move::fromStatFileString($t5), Move::fromStatFileString($t6),
                                    Move::fromStatFileString($t7), Move::fromStatFileString($t8), Move::fromStatFileString($t9),
                                    Move::fromStatFileString($t10)];
        }
        fclose($moves);
        // print_r($injuries);
        // print_r($transactions);

        $pit = fopen('../../cgi-bin/jhml/data/' . $year . '/' . $team . 'pit', 'r');
        $pits = array();
        while (($line = fgets($pit)) !== false) {
            list ($game, $p) = PitcherStats::parseStatFileLine($line); 
            $pits[$game] = $p;
        }
        // print_r($pits);
        fclose($pit);


        $psss = glob('../data/'. $year . '/*' . strtoupper($team) . '*.pss');
        foreach ($psss as $pss) {
            // print($pss . "\n");
            $fn = str_replace('.pss', '', preg_replace('/.*\//', "", $pss));
            $a = substr($fn, 0, 3);
            $ag = intval(substr($fn, 3, 3));
            $h = substr($fn, 6, 3);
            $hg = intval(substr($fn, 9, 3));
            //error_log($a.'('.$ag.')@'.$h.'('.$hg.')'.PHP_EOL,3,'error_log');
            $g = Game::getGameFromScoreSheet($year, $a, $ag, $h, $hg);
	    list($ba, $ea, $ia, $ma, $pa, $bh, $eh, $ih, $mh, $ph) = $g->gameStats();
            if ($a == strtoupper($team)) {
		$game = $ag;
		$bats[$game] = $ba;
		$es[$game] = $ea;
		$pits[$game] = $pa;
            } else  {
                $game = $hg;
		$bats[$game] = $bh;
		$es[$game] = $eh;
		$pits[$game] = $ph;
	    }
	    $games[$game] = $g;
	    
            // print(strval($game) . ":" . $g->toStatFileString() . "\n");
        }


        ksort($bats);
        ksort($es);
        $bat = fopen('../data/' . $year . '/' . $team . 'bat', 'w');
        foreach ($bats as $game => $bs) {
            usort($bs, fn($a, $b) => strcmp($a->name, $b->name));
            // print_r($game);
            // print_r($bs);
            $line = strval($game);
            foreach($bs as $b) {
                $line .= ":" . $b->toStatFileString($es[$game]);
            }
            // print($line . "\n");
            fwrite($bat, $line . "\n");
        }
        fclose($bat);

        ksort($games);
        $game = fopen('../data/' . $year . '/' . $team . 'game', 'w');
        foreach ($games as $gameNo => $g) {
            // print_r($g);
            fwrite($game, strval($gameNo) . ":" . $g->toStatFileString() . "\n");
        }
        fclose($game);

        ksort($transactions);
        $moves = fopen('../data/' . $year . '/' . $team . 'moves', 'w');
        foreach ($transactions as $game => $transaction) {
            // print_r($transaction);
            $line = strval($game);
            foreach ($injuries[$game] as $i) {
                // print_r($i);
                $line .= ":" . $i->toStatFileString();
            }
            foreach ($transaction as $t) {
                $line .= ":" . $t[0]->toStatFileString() . "~" . $t[1]->toStatFileString();
            }
            fwrite($moves, $line . "\n");
        }
        fclose($moves);


        ksort($pits);
        $pit = fopen('../data/' . $year . '/' . $team . 'pit', 'w');
        foreach ($pits as $game => $ps) {
            usort($ps, fn($a, $b) => strcmp($a->name, $b->name));
            // print_r($game);
            // print_r($ps);
            $line = strval($game);
            foreach($ps as $p) {
                $line .= ":" . $p->toStatFileString();
            }
            // print($line . "\n");
            fwrite($pit, $line . "\n");
        }
        fclose($pit);

    }
}

?>
