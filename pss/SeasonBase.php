<?php

  namespace Scoring;

abstract class SeasonBase
{
    public $leagueName;
    public $roster_;
    public $schedule_;
    public $moves_;

    abstract public function isValid();
}
?>
