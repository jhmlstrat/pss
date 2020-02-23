<?php
  namespace Scoring;

  class Games {
		
    function __construct($year = 2017) {
    }

    public function toString() {
    }
    public function json() {
      return json_decode($this->toString());
    }
    public function getGames() {
      // TBD
      return '[]';
    }
  }
?>
