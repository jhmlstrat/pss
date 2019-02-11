<?php

class Config {

  public $config; //= {};

  function __construct() {
    $json = file_get_contents("../data/config.json");
    $this->config = json_decode($json, true);
    foreach ($this->config['years'] as $year) {
      $dir = "../data/" . $year['year'];
      if (!file_exists($dir) && !is_dir($dir)) {
        mkdir($dir);         
      } 
    }
  }
}
?>

