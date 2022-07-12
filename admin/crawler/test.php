<?php
//include_once(__DIR__ . './../../includes/config.php');
//include_once('./functions.php');
// $startDate = '25-04-2022';
// $date = $startDate;
// $today = date('d-m-Y');
// while (strtotime($date) <= strtotime($today)) {
//     echo $date . "\n";
//     $timestamp = strtotime($date);
//     $date = date('d-m-Y', mktime(0, 0, 0, date("m", $timestamp), date("d", $timestamp)+1,   date("Y", $timestamp)));
// }

$date1 = '01-06-2022';
$date2 = '2022-06-01';
echo strtotime($date1) . "\n";
echo strtotime($date2);
?>