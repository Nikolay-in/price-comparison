<?php
include_once(__DIR__ . './../../includes/config.php');
include_once('./functions.php');
include_once('./../includes/commonfunctions.php');
//$_SERVER['DOCUMENT_ROOT'];
$status = crawlerStatus("cron");

if ($status == 11) { //Change to 12
    //Start timer
    $start=hrtime(true);
    include_once('./bot.php');
    crawlerStatus($flag = 'bot', 1);
    //End timer
    $end=hrtime(true);
    echo '<br>Finished for: <b>' . (($end-$start)/1e+6)/1000 . '</b> secs.';
} else {
    echo 'Code: ' . $status . '<br />';
} 
?>