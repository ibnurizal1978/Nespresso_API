<?php
ini_set('display_errors',1);  error_reporting(E_ALL);
include_once 'config.php';
header("Access-Control-Allow-Methods: POST");
ini_set('MAX_EXECUTION_TIME', 3600);
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");
ignore_user_abort(true);
set_time_limit(0);
$req = "POST";

//token
$app_id     = 'P1c0';
$date       = date('Ymd');
$token      = hash("sha256",$app_id.$date);

if(@$_POST['token'] == '') {
  http_response_code(400);
  echo json_encode(['message' => 'empty token']);
  exit();
}

if(@$_POST['token'] != $token) {
  http_response_code(400);
  echo json_encode(['message' => 'token mismatch', 'token' => $token]);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] <> $req) {
  http_response_code(400);
  echo json_encode(['message' => 'The request is using the '.$req.' method']);
  exit();
}

/* ----- START ------ */

//create session
$GUID = GUID();
$device_id = input_data($_POST['deviceID']);
$project_id = input_data($_POST['projectID']);

$s = "INSERT INTO tbl_interaction SET interactionID = '".$GUID."', deviceID = '".$device_id."', projectID = '".$project_id."', createdDateTime = now()";
mysqli_query($conn, $s);

http_response_code(200);
echo json_encode(['message' => 'success', 'interactionID' => $GUID], JSON_PRETTY_PRINT);
exit();
?>