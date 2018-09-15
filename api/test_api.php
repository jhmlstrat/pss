<?php

require_once('./apiClass.php');
if ($_GET['check'] == 'TooManyMLs') {

  $api = new ApiClass();

  echo "<html>\n<meta charset='UTF-8'>\n";
  echo "HERE<br>";

  echo "Injury:       ";
  echo $api->testInjury();
  echo "Move:         ";
  echo $api->testMove();
  echo "Player:       ";
  echo $api->testPlayer();
  echo "Position:     ";
  echo $api->testPosition();
  echo "Result:       ";
  echo $api->testResult();
  echo "Roster:       ";
  echo $api->testRoster();
  echo "RosterItem:   ";
  echo $api->testRosterItem();
  echo "Rosters:       ";
//  echo $api->testRosters();
  echo "Schedule: ";
  echo $api->testSchedule();
  echo "ScheduleItem: ";
  echo $api->testScheduleItem();
  echo "Side:         ";
  echo $api->testSide();
  echo "Situation:    ";
  echo $api->testSituation();
  echo "When:         ";
  echo $api->testWhen();

  echo "\n</html>";
} else {
  echo "Should not be here - print_config";
}

?>
