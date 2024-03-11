<?php

require_once "..\pss\Spreadsheet\Spreadsheet.php";
//use PhpOffice\PhpSpreadsheet\Writer\Ods;
use PHPUnit\Framework\TestCase;

class SpreadsheetTest extends TestCase
{
  // Create a new Spreadsheet object
  spreadsheet = new Spreadsheet();
}

// Save the spreadsheet as ODS format
//$writer = new Ods($spreadsheet);
//$writer->save('blank_spreadsheet.ods');

echo 'Blank spreadsheet created successfully.';
?>
