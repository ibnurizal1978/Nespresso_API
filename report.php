<?php
ini_set('display_errors',1);  error_reporting(E_ALL);
include_once 'config.php';
//ob_end_clean();
//header('Content-Type: application/vnd.ms-excel; charset=utf-8');
chdir("/var/www/html/api/nespresso");
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\Writer\Xls; 

$date = date('Y-m-d'); //today date
$weekOfdays = array();
for($i =1; $i <= 7; $i++){
  $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
  $weekOfdays[] = date('Y-m-d', strtotime($date));
}


#report 5: uuid - questions - answer - correct - date
$s5 = "select uuid, questions, answers, is_correct, date_format(a.created_at, '%d-%m-%Y') as created_at FROM results a  INNER JOIN questions b ON a.questions_id = b.id INNER JOIN answers c ON a.answers_id = c.id WHERE a.created_at >= DATE(NOW()) - INTERVAL 7 DAY";
$h5 = mysqli_query($conn, $s5);

#report 6: uuid - total_score - total_time - date
$s6 = "SELECT uuid, total_score, total_time, success_status, date_format(created_at, '%d-%m-%Y') as created_at FROM results_summary WHERE created_at >= DATE(NOW()) - INTERVAL 7 DAY";
$h6 = mysqli_query($conn, $s6);


$spreadsheet = new Spreadsheet(); 

#sheet1
#report 1: How many people activate the machine
$sheet = $spreadsheet->getActiveSheet()->setTitle('sheet one');;  
$sheet->setCellValue('A1', 'How many people activate the machine?');  
$sheet->setCellValue('A2', 'Date');
$sheet->setCellValue('B2', 'Count');
$i=3;

foreach($weekOfdays as $days){
    $s1 = "SELECT  count(id) as total FROM users where created_at >= DATE(NOW()) - INTERVAL 7 DAY AND date(created_at) = '".$days."'";
    $h1 = mysqli_query($conn, $s1);
    while($r1 = mysqli_fetch_assoc($h1)) {
        $sheet->setCellValue("A$i",$days);
        $sheet->setCellValue("B$i",$r1['total']);
    $i++;
    }
}

#report 2: How many people played the full game
$sheet->setCellValue('A11', 'How many people played the full game');  
$sheet->setCellValue('A12', 'Date');
$sheet->setCellValue('B12', 'Count');
$i=13;

foreach($weekOfdays as $days){
    $s2 = "SELECT count(id) as total FROM results_summary where created_at >= DATE(NOW()) - INTERVAL 7 DAY AND date(created_at) = '".$days."'";
    $h2 = mysqli_query($conn, $s2);
    while($r2 = mysqli_fetch_assoc($h2)) {
        $sheet->setCellValue("A$i",$days);
        $sheet->setCellValue("B$i",$r2['total']);
    $i++;
    }
}


#report 3: How many people won (3/4 or 4/4)
$sheet->setCellValue('A21', 'How many people won');  
$sheet->setCellValue('A22', 'Date');
$sheet->setCellValue('B22', 'Count');
$i=23;

foreach($weekOfdays as $days){
    $s3 = "SELECT date_format(created_at, '%d-%m-%Y') as tgl, count(id) as total FROM results_summary where success_status = 'WIN' AND created_at >= DATE(NOW()) - INTERVAL 7 DAY AND date(created_at) = '".$days."' GROUP BY tgl";
    $h3 = mysqli_query($conn, $s3);
    while($r3 = mysqli_fetch_assoc($h3)) {
        $sheet->setCellValue("A$i",$days);
        $sheet->setCellValue("B$i",$r3['total']);
    $i++;
    }
}

#report 4: How many people lost
$sheet->setCellValue('A31', 'How many people lost');  
$sheet->setCellValue('A32', 'Date');
$sheet->setCellValue('B32', 'Count');
$i=33;

foreach($weekOfdays as $days){
    $s4 = "SELECT date_format(created_at, '%d-%m-%Y') as tgl, count(id) as total FROM results_summary where success_status = 'FAILED' AND created_at >= DATE(NOW()) - INTERVAL 7 DAY AND date(created_at) = '".$days."' GROUP BY tgl";
    $h4 = mysqli_query($conn, $s4);
    while($r4 = mysqli_fetch_assoc($h4)) {
        $sheet->setCellValue("A$i",$days);
        $sheet->setCellValue("B$i",$r4['total']);
    $i++;
    }
}


#sheet 2
$sheet2=$spreadsheet->createSheet()->setTitle('sheet two');;
$sheet2->setCellValue('A1', 'Session'); 
$sheet2->setCellValue('B1', 'Questions');
$sheet2->setCellValue('C1', 'Answers');
$sheet2->setCellValue('D1', 'Is Correct?');
$sheet2->setCellValue('E1', 'Date');
$i=2;
while($r5 = mysqli_fetch_assoc($h5)) {
    $sheet2->setCellValue("A$i",$r5['uuid']);
    $sheet2->setCellValue("B$i",$r5['questions']);
    $sheet2->setCellValue("C$i",$r5['answers']);
    $sheet2->setCellValue("D$i",$r5['is_correct']);
    $sheet2->setCellValue("E$i",$r5['created_at']);
$i++;
}

#sheet 3
$sheet3=$spreadsheet->createSheet()->setTitle('sheet three');;
$sheet3->setCellValue('A1', 'Session'); 
$sheet3->setCellValue('B1', 'Total Score'); 
$sheet3->setCellValue('C1', 'Total Time (seconds)'); 
$sheet3->setCellValue('D1', 'Success Status'); 
$sheet3->setCellValue('E1', 'Date'); 
$i=2;
while($r6 = mysqli_fetch_assoc($h6)) {
    $sheet3->setCellValue("A$i",$r6['uuid']);
    $sheet3->setCellValue("B$i",$r6['total_score']);
    $sheet3->setCellValue("C$i",$r6['total_time']);
    $sheet3->setCellValue("D$i",$r6['success_status']);
    $sheet3->setCellValue("E$i",$r6['created_at']);
$i++;
}

$writer = new Xls($spreadsheet);  

$writer->save('reports/nespresso-weekly-report-'.date('dmY').'.xls'); 
?>
