<?php
  namespace Scoring;

  abstract class MoveType {
    const ToMajors = 0;
    const ToMinors = 1;
    const OnDL = 2;
    const OffDL = 3;
    const TradedAway = 4;
    const TradedFor = 5;

    public static function fromString($mt) {
      switch ($mt) {
        case "Fm minors":  return 0;
        case "To minors":  return 1;
        case "On DL":      return 2;
        case "Off DL":     return 3;
        case "Traded":     return 4;
        case "Traded for": return 5;
        default:           print "Bad Move: " . $mt . "<br/>";return -1;
      }
    }
    public static function toString($mt) {
      switch ($mt) {
        case 0:  return "Fm minors";
        case 1:  return "To minors";
        case 2:  return "On DL";
        case 3:  return "Off DL";
        case 4:  return "Traded";
        case 5:  return "Traded for";
        default: print "Bad move type: " . $mt . "<br/>";return "Bad move type";
      }
    }
  }
?>
