<?php

  namespace ProjectScoresheet;

class Side
{
    const VISITOR = 0;
    const HOME = 1;
    public static function sides()
    {
        return array(Side::VISITOR, Side::HOME);
    }
}
?>
