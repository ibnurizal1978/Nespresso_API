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
$interaction_id = input_data($param[3]);
//echo $interaction_id;
//exit();

if(@$interaction_id == '') {
    http_response_code(400);
    echo json_encode(['message' => 'empty ID']);
    exit();
}


//$interaction_id = input_data($_GET['interactionID']);
$s = "UPDATE tbl_interaction SET status = 1, endDateTime = now() WHERE interactionID = '".$interaction_id."'";
if(mysqli_query($conn, $s)) {
    http_response_code(200);
    echo json_encode(['message' => 'success'], JSON_PRETTY_PRINT);
    exit();
}else{
    http_response_code(200);
    echo json_encode(['message' => 'invalid ID'], JSON_PRETTY_PRINT);
    exit();
}


?>