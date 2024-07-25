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

if(@$_POST['deviceID'] == '') {
  http_response_code(400);
  echo json_encode(['message' => 'empty device ID']);
  exit();
}

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
$uuid = uuid();
$deviceID = input_data($_POST['deviceID']);

$s = "INSERT INTO users SET uuid = '".$uuid."', deviceID = '".$deviceID."'";
mysqli_query($conn, $s);

//get 4 random questions that is not yet sent to this user
$s = "SELECT questions_id FROM results WHERE uuid = '".$uuid."'";
$h = mysqli_query($conn, $s);
if(mysqli_num_rows($h) > 0)
{
  $s2 = "SELECT categories_id, questions, a.id, elaboration, sort, color FROM (SELECT * FROM questions ORDER BY sort limit 80) T1 WHERE id <> '".$r['questions_id']."' GROUP BY categories_id  ORDER BY sort";
}else{
  $s2 = "SELECT categories_id, questions, a.id, elaboration, sort, color FROM questions a inner join categories b ON a.categories_id = b.id ORDER BY sort";
}

$arr_questions = array();
$arr_answer = array();
$h2 = mysqli_query($conn, $s2);
while($r2 = mysqli_fetch_assoc($h2))
{

  #percentage
  $s5 = "SELECT count(b.id) as total FROM results_summary a INNER JOIN results b  USING (uuid) where questions_id = '".$r2['id']."'";
  $h5 = mysqli_query($conn, $s5);
  $r5 = mysqli_fetch_assoc($h5);

  #percentage - which win 
  $s6 = "SELECT count(b.id) as total FROM results_summary a INNER JOIN results b  USING (uuid) INNER JOIN answers c ON b.answers_id = c.id where b.questions_id = '".$r2['id']."' AND is_correct = 'YES'";
  $h6 = mysqli_query($conn, $s6);
  $r6 = mysqli_fetch_assoc($h6);
  $win_percentage = @round(($r6['total']/$r5['total'])*100);
  if($win_percentage == 0) { $win = '0'; }else{ $win = $win_percentage; }

  $row_arr_questions['sort']       = $r2['sort'];
  $row_arr_questions['categories_id'] = $r2['categories_id'];
  $row_arr_questions['questions_id']  = $r2['id'];
  $row_arr_questions['questions']     = $r2['questions'];
  $row_arr_questions['elaboration']   = $r2['elaboration'];
  $row_arr_questions['color']         = $r2['color'];
  $row_arr_questions['questions_id_percent'] = $r5['total'];
  $row_arr_questions['win_rate']             = $r6['total'];
  $row_arr_questions['fun_fact']             = "Fun fact: ".$win."% of the users have gotten this right";
  array_push($arr_questions,$row_arr_questions);

  //insert into log
  $s3 = "INSERT INTO log SET questions_id = '".$r2['id']."', uuid = '".$uuid."'";
  mysqli_query($conn, $s3);

  //get the answer
  $s4 = "SELECT id, questions_id, answers, is_correct FROM answers WHERE questions_id = '".$r2['id']."'";
  $h4 = mysqli_query($conn, $s4);
  while($r4 = mysqli_fetch_assoc($h4))
  {

    $row_arr_answer['questions_id']         = $r4['questions_id'];
    $row_arr_answer['answers_id']           = $r4['id'];
    $row_arr_answer['answers']              = $r4['answers'];
    $row_arr_answer['is_correct']           = $r4['is_correct'];
    array_push($arr_answer,$row_arr_answer);


  }

}


http_response_code(200);
echo json_encode(['message' => 'success', 'session_id' => $uuid, 'questions' => $arr_questions, 'answer' => $arr_answer], JSON_PRETTY_PRINT);
exit();
?>