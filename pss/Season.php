<?php
  namespace Jhml;
  require_once "Rosters.php";
  //require_once "Schedule.php";

  class Season extends \Scoring\SeasonBase {
    public $leagueName = "JHML";
    public $rosters; 
    //public $schedule_ = new Schedule;

    public function __construct() {
      $this->rosters = new Rosters;
    }
    public function isValid() {
      return true;
    }
  }
?>
