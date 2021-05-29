<?php

  namespace Jhml;

  require_once "Rosters.php";

class Season extends \Scoring\SeasonBase
{
    public $leagueName = "JHML";
    public $rosters;

    public function __construct()
    {
        $this->rosters = new Rosters();
    }
    public function isValid()
    {
        return true;
    }
}
?>
