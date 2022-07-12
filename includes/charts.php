<?php
session_start(); 
include_once('./config.php');
//Product price history
if (isset($_GET['type']) && isset($_GET['id']) && $_GET['type'] == 'priceHistory') {
    $query = mysqli_query($con, "SELECT pricehistory.date, offers.id offerID, pricehistory.price
    FROM pricehistory
    LEFT JOIN offers ON pricehistory.offerID = offers.id
    LEFT JOIN products ON products.id = offers.productID
    WHERE products.id = " . $_GET['id'] . "
    ORDER BY pricehistory.date ASC;");
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
    $data = [];
    if ($result) {
        $startDate = $result[0]['date'];
        $date = date('d-m-Y', strtotime($startDate));
        $today = date('d-m-Y');
        while (strtotime($date) <= strtotime($today)) {
            //Fill with dates
            $data[$date]['prices'] = [];
            $data[$date]['avg'] = '';
            $timestamp = strtotime($date);
            $date = date('d-m-Y', mktime(0, 0, 0, date("m", $timestamp), date("d", $timestamp)+1,   date("Y", $timestamp)));
        }
        foreach ($result as $line) {
            //Cycle through dates and insert each price
            foreach (array_keys($data) as $key => $result) {
                if (strtotime($result) >= strtotime($line['date'])) {
                    if ($line['price'] == 0 && isset($data[$result]['prices'][$line['offerID']])) {
                        unset($data[$result]['prices'][$line['offerID']]);
                    } else {
                        $data[$result]['prices'][$line['offerID']] = $line['price'];
                    }
                }
            }
        }
        // Calculate daily average price
        foreach (array_keys($data) as $key => $result) {
            $avg = array_sum($data[$result]['prices']) / count($data[$result]['prices']);
            $data[$result]['avg'] = number_format($avg, 2, '.', '');
        }
        echo json_encode($data);
    }
}
?>