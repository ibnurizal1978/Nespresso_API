<?php
ini_set('display_errors',1);  error_reporting(E_ALL);
header("Access-Control-Allow-Methods: POST");
ini_set('MAX_EXECUTION_TIME', 3600);
ini_set("log_errors", 1);
ini_set("error_log", "/tmp/php-error.log");
ignore_user_abort(true);
set_time_limit(0);
include_once 'config.php';
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

$session_id     = input_data($_POST['session_id']);
$questions_id   = input_data($_POST['questions_id']);
$answers_id     = input_data($_POST['answers_id']);
$total_time     = input_data($_POST['total_time']);
$deviceID       = input_data($_POST['deviceID']);

if(@$session_id == '' || $answers_id == '' || $total_time == '' || $questions_id == '' || $deviceID == '') {
    http_response_code(400);
    echo json_encode(['message' => 'one of field is empty']);
    exit();
}

//check if the session id sent is in the database?
$s = "SELECT uuid FROM users WHERE uuid = '".$session_id."'";
$h = mysqli_query($conn, $s);
if(mysqli_num_rows($h)  == 0) {
    http_response_code(400);
    echo json_encode(['message' => 'invalid session_id']);
    exit();
}

$q_id   = preg_split ("/\,/", $questions_id); 
$a_id     = preg_split ("/\,/", $answers_id);

//check if the users already in the results table?
$s = "SELECT uuid FROM results WHERE uuid = '".$session_id."'";
$h = mysqli_query($conn, $s);
if(mysqli_num_rows($h)  > 0) {
    http_response_code(400);
    echo json_encode(['message' => 'duplicate session_id']);
    exit();
}

//insert into results
$total = count(@$q_id);
for ($i=0; $i<$total; $i++) {
    if(@$q_id[$i]) {
        $s  = "INSERT INTO results SET questions_id = '".$q_id[$i]."', answers_id = '".$a_id[$i]."', uuid = '".$session_id."', total_time = '".$total_time."'";
        mysqli_query($conn,$s);
    }
}

//check total_score 
$s = "SELECT count(a.id) as total_score FROM answers a INNER JOIN results b ON a.id = b.answers_id WHERE a.is_correct = 'YES' AND a.id IN ($answers_id) AND b.uuid = '".$session_id."'";
$h = mysqli_query($conn, $s);
$r = mysqli_fetch_array($h);

if($r['total_score'] < 4)
{ 
    $success_status = 'FAILED';
}else{
    $success_status = 'WIN';
}

$tim = date("i:s", $total_time);
$sql = "INSERT INTO results_summary SET uuid = '".$session_id."', total_time = '".$total_time."', total_score = '".$r['total_score']."', success_status = '".$success_status."', deviceID = '".$deviceID."'";
mysqli_query($conn, $sql);

http_response_code(200);
echo json_encode(['message' => 'success', 'total_time' => $tim, 'total_score' => $r['total_score'], 'success_status' => $success_status]);
exit();
?>
