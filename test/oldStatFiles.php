<?php

require_once "../pss/Rosters.php";

$r2023 = new \Jhml\Rosters(2023);

$r2023->writeOldRosterFiles();
$r2023->writeRosterFile();

?>
