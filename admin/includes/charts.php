<?php
session_start(); 
include_once('../../includes/config.php');
if (isset($_SESSION['adminid'])) {

    //Return temp products added last 30 days
    if ( isset($_GET['type']) && $_GET['type'] == "newTempProducts30days" ) {
        $query=mysqli_query($con,"SELECT count(*) as count, DATE(dateadded) AS date FROM tempproducts WHERE modelStrip IS NULL AND date(dateadded)>=now() - INTERVAL 30 day GROUP BY date ORDER BY date ASC;");
        $resultNewTemp = mysqli_fetch_all($query, MYSQLI_ASSOC);
        $query=mysqli_query($con,"SELECT count(*) as count, DATE(dateadded) AS date FROM tempproducts WHERE modelStrip IS NOT NULL AND date(dateadded)>=now() - INTERVAL 30 day GROUP BY date ORDER BY date ASC;");
        $resultTemp = mysqli_fetch_all($query, MYSQLI_ASSOC);
        $data = array();
        $i = 0;
        for ($daysAgo = -29; $daysAgo <= 0; $daysAgo++) {
            $date = date('Y-m-d', strtotime($daysAgo . ' days'));

            //Push New Temp Products 
            $data[$i]['date'] = $date;
            $data[$i]['newTemp'] = "0";
            foreach ($resultNewTemp as $row) {
                if ($row['date'] == $date) {
                    $data[$i]['newTemp'] = $row['count'];
                }
            }

            //Push Temp Products 
            $data[$i]['temp'] = "0";
            foreach ($resultTemp as $row) {
                if ($row['date'] == $date) {
                    $data[$i]['temp'] = $row['count'];
                }
            }

            $i++;
        }
        echo json_encode($data);
    }

    //Piechart of vendors temp products
    if ( isset($_GET['type']) && $_GET['type'] == "vendorsTempProducts" ) {
        $query=mysqli_query($con,"SELECT b.name as vendor, count(*) as tempProducts FROM tempproducts a LEFT JOIN vendors b ON a.vendorId = b.id GROUP BY b.id ORDER BY tempProducts DESC");
        $data = array();
        for ($i = 0; $result=mysqli_fetch_array($query); $i++) {
            $data[$i]['vendor'] = $result['vendor'];
            $data[$i]['tempProducts'] = $result['tempProducts'];
        }
        echo json_encode($data);
    }

    //Return products added last 30 days
    if ( isset($_GET['type']) && $_GET['type'] == "products30days" ) {
        $query=mysqli_query($con,"SELECT count(*) as count, DATE(dateadded) AS date FROM products WHERE date(dateadded)>=now() - INTERVAL 30 day GROUP BY date ORDER BY date ASC");
        $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
        $data = array();
        $i = 0;
        for ($daysAgo = -29; $daysAgo <= 0; $daysAgo++) {
            $date = date('Y-m-d', strtotime($daysAgo . ' days'));
            $data[$i]['date'] = $date;
            $data[$i]['count'] = "0";
            foreach ($result as $row) {
                if ($row['date'] == $date) {
                    $data[$i]['count'] = $row['count'];
                }
            }
            $i++;
        }
        echo json_encode($data);
    }


    //Return Active and Assigned ; Assigned and Inactive ; Unassigned and Inactive
    if ( isset($_GET['type']) && $_GET['type'] == "activeProducts" ) {
        $data = array();

        $query=mysqli_query($con,"SELECT count(*) as AA FROM products WHERE active = '1' AND subCatID != '0'");
        $result=mysqli_fetch_array($query);
        $data['AA'] = $result['AA'];

        $query=mysqli_query($con,"SELECT count(*) as AI FROM products WHERE active = '0' AND subCatID != '0'");
        $result=mysqli_fetch_array($query);
        $data['AI'] = $result['AI'];
        
        $query=mysqli_query($con,"SELECT count(*) as UI FROM products WHERE active = '0' AND subCatID = '0'");
        $result=mysqli_fetch_array($query);
        $data['UI'] = $result['UI'];
        
        echo json_encode($data);
    }


    //Return offers added last 30 days
    if ( isset($_GET['type']) && $_GET['type'] == "offers30days" ) {
        $query=mysqli_query($con,"SELECT count(*) as count, DATE(dateApproved) AS date FROM offers WHERE date(dateApproved) >= now() - INTERVAL 30 day GROUP BY date ORDER BY date ASC");
        $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
        $daysAgo = -29;
        $data = array();
        $i = 0;
        for ($daysAgo = -29; $daysAgo <= 0; $daysAgo++) {
            $date = date('Y-m-d', strtotime($daysAgo . ' days'));
            $data[$i]['date'] = $date;
            $data[$i]['count'] = "0";
            foreach ($result as $row) {
                if ($row['date'] == $date) {
                    $data[$i]['count'] = $row['count'];
                }
            }
            $i++;
        }
        echo json_encode($data);
    }

    //Return Active offers for Active products ; Inactive Offers ; Active offers but for Inactive products
    if ( isset($_GET['type']) && $_GET['type'] == "activeOffers" ) {
        $data = array();

        $query=mysqli_query($con,"SELECT count(*) as AA FROM offers LEFT JOIN ( SELECT id, active FROM products ) actProducts ON offers.productID = actProducts.id WHERE offers.active = '1' AND actProducts.active = '1'");
        $result=mysqli_fetch_array($query);
        $data['AA'] = $result['AA'];

        $query=mysqli_query($con,"SELECT count(*) as IO FROM offers WHERE active = '0'");
        $result=mysqli_fetch_array($query);
        $data['IO'] = $result['IO'];
        
        $query=mysqli_query($con,"SELECT count(*) as AI FROM offers LEFT JOIN ( SELECT id, active FROM products ) actProducts ON offers.productID = actProducts.id WHERE offers.active = '1' AND actProducts.active = '0'");
        $result=mysqli_fetch_array($query);
        $data['AI'] = $result['AI'];
        
        echo json_encode($data);
    }

    //Return pichart total offers by vendors
    if ( isset($_GET['type']) && $_GET['type'] == "vendorsOffers" ) {
        $query=mysqli_query($con,"SELECT vendors.name as vendor, count(*) as offers FROM offers LEFT JOIN vendors ON offers.vendorID = vendors.id GROUP BY vendors.id ORDER BY offers DESC");
        $data = array();

        for ($i = 0; $result=mysqli_fetch_array($query); $i++) {
            $data[$i]['vendor'] = $result['vendor'];
            $data[$i]['offers'] = $result['offers'];
        }
        echo json_encode($data);
    }

    //Product price history
    if (isset($_GET['type']) && isset($_GET['id']) && $_GET['type'] == 'priceHistory') {

        $query = mysqli_query($con, "SELECT pricehistory.date, offers.id offerID, pricehistory.price
        FROM pricehistory
        LEFT JOIN offers ON pricehistory.offerID = offers.id
        LEFT JOIN products ON products.id = offers.productID
        WHERE pricehistory.price > 0 AND products.id = " . $_GET['id'] . "
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
                // Fill only the first date with prices
                $date = date('d-m-Y', strtotime($line['date']));
                if ($date == array_keys($data)[0]) {
                    $data[$date]['prices'][$line['offerID']] = $line['price'];
                }
            }
            // var_dump($data);
            //Fill all the dates with the prices of the first day (To be done: upon offer deletion => make price = 0 in history that day => and dont add to next days in graph)
            if (count($result) > 1) {
                $keys = array_keys($data);  
                for ($i = 1; $i < count($keys); $i++) {
                    $data[$keys[$i]]['prices'] = $data[$keys[0]]['prices'];
                }
            }
            //Update the daily prices accordingly
            foreach ($result as $line) {
                $date = date('d-m-Y', strtotime($line['date']));
                $data[$date]['prices'][$line['offerID']] = $line['price'];
            }

            //Calculate daily average price
            foreach (array_keys($data) as $key => $result) {
                $avg = array_sum($data[$result]['prices']) / count($data[$result]['prices']);
                $data[$result]['avg'] = number_format($avg, 2, '.', '');
            }
            echo json_encode($data);
        }
    }
}

?>