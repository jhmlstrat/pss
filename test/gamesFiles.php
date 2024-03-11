<?php

require_once "../pss/GameStats.php";

 \Scoring\GameStats::buildOldStatFiles(2019,'pit');

// $r2019->writeOldRosterFiles();

require_once "../pss/SpreadSheet.php";

 \Scoring\SpreadSheet::buildSpreadSheet(2019,'pit');

?>
