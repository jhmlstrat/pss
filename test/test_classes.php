<?php

require_once('../api/apiClass.php');
  $api = new ApiClass();

  echo "<html>\n<meta charset='UTF-8'>\n";
  echo "HERE<br>";

  echo "\nInjury:       ";
  echo $api->testInjury();
  echo "\nMove:         ";
  echo $api->testMove();
  echo "\nPlayer:       ";
  echo $api->testPlayer();
  echo "\nPosition:     ";
  echo $api->testPosition();
  echo "\nResult:       ";
  echo $api->testResult();
  echo "\nRoster:       ";
  echo $api->testRoster();
  echo "\nRosterItem:   ";
  echo $api->testRosterItem();
  echo "\nRosters:      ";
  echo $api->testRosters();
  echo "\nSchedule:     ";
  echo $api->testSchedule();
  echo "\nScheduleItem: ";
  echo $api->testScheduleItem();
  echo "\nSide:         ";
  echo $api->testSide();
  echo "\nSituation:    ";
  echo $api->testSituation();
  echo "\nWhen:         ";
  echo $api->testWhen();

  echo "\n</html>";

?>
