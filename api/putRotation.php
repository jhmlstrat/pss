<?php
#if ($_SERVER['HTTP_X_AUTHORIZATION'] != 'TooManyMLs') return;
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// get posted data
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
  //error_log(file_get_contents("php://input"),3,'error_log');
  //parse_str(file_get_contents("php://input"), $_PUT);
  $foo = json_decode(file_get_contents("php://input"));
  foreach ($foo as $key => $value) {
    //unset($_PUT[$key]);
    $_PUT[str_replace('amp;', '', $key)] = $value;
  }
  //$_REQUEST = array_merge($_REQUEST, $_PUT);
}
$data = isset($_PUT['data']) ? $_PUT['data'] : die();

include_once '../pss/Rotation.php';

\Jhml\Rotation::writeRotationFile(json_encode($data));
?>
