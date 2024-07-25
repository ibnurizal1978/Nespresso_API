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

#percentage
$s1 = "SELECT count(id) as total FROM results_summary";
$h1 = mysqli_query($conn, $s1);
$r1 = mysqli_fetch_assoc($h1);

#percentage - which win 
$s2 = "SELECT count(id) as total FROM results_summary where success_status = 'WIN'";
$h2 = mysqli_query($conn, $s2);
$r2 = mysqli_fetch_assoc($h2);
$win_percentage = round(($r2['total']/$r1['total'])*100);
if($win_percentage == 0) { $win = '0'; }else{ $win = $win_percentage; }

http_response_code(200);
echo json_encode(['message' => 'success', 'percentage' => $win], JSON_PRETTY_PRINT);
exit();
?>