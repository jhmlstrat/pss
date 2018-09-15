<?php

require_once('./apiClass.php');
if ($_GET['check'] == 'TooManyMLs') {

  $api = new ApiClass();

  echo "<html>\n<meta charset='UTF-8'>\n";
  echo "HERE<br>";

  echo $api->printConfig();

  echo "\n</html>";
} else {
  echo "Should not be here - print_config";
}

?>
