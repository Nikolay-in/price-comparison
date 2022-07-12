<?php
//Code for updating crawler settings
if (isset($_POST['submitSettings'])) {
    if ( isset($_POST['onoff']) ) {
        $onOff = "1";
    } else {
        $onOff = "0";
    }

    $hourFrom = date('H:i:s', strtotime($_POST['hourFrom']));
    $hourTo = date('H:i:s', strtotime($_POST['hourTo']));
    $vendorCatFreq = $_POST['vendorCatFreq'];
    $vendorProdFreq = $_POST['vendorProdFreq'];
    $secPerPages = $_POST['secPerPages'];
    $userAgent = $_POST['userAgent'];
    $userAgents = $_POST['userAgents'];
    $deactiveOffersDays = $_POST['deactiveOffersDays'];
    $delOffersDays = $_POST['delOffersDays'];
    $delTempDays = $_POST['delTempDays'];
    if ( $userAgent > count(explode("\n", $userAgents)) ) {
        $userAgent = 0;
    }
    $query=mysqli_query($con,"UPDATE crawler SET onoff = '$onOff', workFrom = '$hourFrom', workTo = '$hourTo', catFreq = '$vendorCatFreq', prodFreq = '$vendorProdFreq', secPage = '$secPerPages', uaSet = '$userAgent', uaList = '$userAgents', deactiveOffersDays = '$deactiveOffersDays', delOffersDays = '$delOffersDays', delTempDays = '$delTempDays' WHERE id = '0'");
    if ($query) {
        include_once("./crawler/functions.php");
        //"admin" keeps status on stby even in working hours and lets only cronjob/core.php to activate working.
        crawlerStatus("admin");
        echo "<script type='text/javascript'> document.location = 'crawler.php'; </script>";
    }
}

//Code for updating vendors setting
if (isset($_POST['submitVendors'])) {
    $sql = "";
    $vendors = $_POST['vendors'];
    $counter = 1;
    $emptyClass = null;
    foreach ( $vendors as $vendor ) {
        $id = $vendor['id'];
        $ocrawl = $vendor['ocrawl'];
        if ( isset($vendor['crawl']) ) {
            $crawl = "1";
        } else {
            $crawl = "0";
        }
        if ( $ocrawl != $crawl ) {
            $query2=mysqli_query($con,"SELECT name, crawlName, catdir FROM vendors WHERE id = '$id'");
            $result2=mysqli_fetch_array($query2);
            if ($crawl == "1" && ( $result2['crawlName'] == NULL || $result2['catdir'] == NULL) ) {
                if ($counter > 1) {
                    $emptyClass .= ", ";
                }
                $emptyClass .= $result2['name'];
                $crawl = "0";
                $counter++;
            }
            $query2=mysqli_query($con,"UPDATE vendors SET crawl = '$crawl' WHERE id = '$id'");
        }
    }
    if ($emptyClass != null) {
        echo "<script type='text/javascript'> alert('Cannot enable crawling for the following vendors due to empty crawl name or category directory: $emptyClass.'); document.location = 'crawler.php'; </script>";
    } else {
        echo "<script type='text/javascript'> document.location = 'crawler.php'; </script>";
    }
}


//Editor
$query=mysqli_query($con,"SELECT * FROM crawler");
$result=mysqli_fetch_array($query);
$status = $result['status'];
$lastRun = $result['lastRun']; 
$onOff = $result['onoff'];

//Format 00:00:00 to 00:00
$workFrom = date('H:i', strtotime($result['workFrom']));
$workTo = date('H:i', strtotime($result['workTo']));

//Create a list of 24 hours
$allHours = array();
for ($i = 0; $i < 24; $i++) {
    if ( $i < 10 ) {
        array_push($allHours, "0" . $i . ":00");
    } else {
        array_push($allHours, $i . ":00");
    }
}
$catFreq = $result['catFreq'];
$prodFreq = $result['prodFreq'];
$secPage = $result['secPage'];
$uaSet = $result['uaSet'];
$uaListText = $result['uaList'];
$uaList = explode("\n", $result['uaList']);
$deactiveOffersDays = $result['deactiveOffersDays'];
$delOffersDays = $result['delOffersDays'];
$delTempDays = $result['delTempDays'];

//Get Vendors Info
$query=mysqli_query($con,"SELECT id, name, logo, vis, crawl, catdir, crawlName FROM vendors");
$vendors = "";
$arrayId = "0";
while ($result=mysqli_fetch_array($query)) {
    if ( $result['vis'] == "1" ) {
        $vis = "<i class=\"fas fa-eye text-success fa-lg\"></i>";
    } else {
        $vis = "<i class=\"fas fa-eye-slash text-danger fa-lg\"></i>";
    }
    if ( $result['crawl'] == "1" ) {
        $crawl = "checked";
    } else {
        $crawl = "";
    }
    if ( $result['catdir'] == null ) {
        $catdir = "<span class=\"text-danger fw-bold\">No</label>";
    } else {
        $catdir = "<span class=\"text-success fw-bold\">Yes</label>";
    }
    if ($result['crawlName']) {
        $crawlName = "<span class=\"text-success fw-bold\">Yes</label>";
    } else {
        $crawlName = "<span class=\"text-danger fw-bold\">No</label>";
    }

    //Get crawling, assigned and total subcats with one query IFNULL(activeOffers.activeOffers, 0)
    $query2=mysqli_query($con,"SELECT IFNULL(activeOffers.activeOffers, 0) activeOffers, IFNULL(offers.offers, 0) offers, IFNULL(crawl.crawl, 0) crawl, IFNULL(assigned.assigned, 0) assigned, IFNULL(total.total, 0) total FROM vendors 
    LEFT JOIN (
    SELECT count(*) as activeOffers, offers.vendorID as vendor FROM offers WHERE offers.active = 1 AND offers.vendorID = ".$result['id']." ) activeOffers
    ON vendors.id = activeOffers.vendor
    LEFT JOIN (
    SELECT count(*) as offers, offers.vendorID as vendor FROM offers WHERE offers.vendorID = ".$result['id']." ) offers
    ON vendors.id = offers.vendor
    LEFT JOIN (
    SELECT count(*) as crawl, vendorscats.vendorid as vendor FROM vendorssubcats LEFT JOIN vendorscats ON vendorssubcats.categoryid = vendorscats.id WHERE vendorscats.vendorid = ".$result['id']." AND vendorssubcats.crawl = 1 
    ) crawl
    ON vendors.id = crawl.vendor
    LEFT JOIN (
    SELECT count(*) as assigned, vendorscats.vendorid as vendor FROM vendorssubcats LEFT JOIN vendorscats ON vendorssubcats.categoryid = vendorscats.id WHERE vendorscats.vendorid = ".$result['id']." AND vendorssubcats.assign != 0 ) assigned
    ON vendors.id = assigned.vendor
    LEFT JOIN (
    SELECT count(*) as total, vendorscats.vendorid as vendor FROM vendorssubcats LEFT JOIN vendorscats ON vendorssubcats.categoryid = vendorscats.id WHERE vendorscats.vendorid = ".$result['id']." ) total
    ON vendors.id = total.vendor
    WHERE vendors.id = ".$result['id']."");
    $resultCats=mysqli_fetch_array($query2);
    
    $vendors .= "<tr class=\"align-middle\">
                    <input type=\"hidden\" id=\"barID-$arrayId\" class=\"barID\" value=\"" . $arrayId . "\">
                    <input type=\"hidden\" id=\"barCrawl-$arrayId\" value=\"" . $resultCats['crawl'] . "\">
                    <input type=\"hidden\" id=\"barAssigned-$arrayId\" value=\"" . $resultCats['assigned'] . "\">
                    <input type=\"hidden\" id=\"barTotal-$arrayId\" value=\"" . $resultCats['total'] . "\">
                    <td>
                        <a href=\"editvendors.php?act=edit&id=" . $result['id'] . "\" class=\"mx-auto\" target=\"_blank\" style=\"text-decoration: none;\">
                        <img src=\"" . SITE_LOGOS . $result['logo'] . "\" class=\"img-fluid mt-3\" style=\"max-height: 100px;\">
                        <div class=\"my-3 fs-6\">" . $vis . "&nbsp;" . $result['name'] . "</a></div>
                    </td>
                    <td class=\"align-middle \">
                        <input type=\"hidden\" id=\"vendorId\" name=\"vendors[$arrayId][id]\" value=\"" . $result['id'] . "\">
                        <input type=\"hidden\" id=\"ocrawl\" name=\"vendors[$arrayId][ocrawl]\" value=\"" . $result['crawl'] . "\">
                        <div class=\"form-check form-switch d-flex justify-content-center\"><input class=\"form-check-input\" type=\"checkbox\" role=\"switch\" name=\"vendors[$arrayId][crawl]\" id=\"crawl\" $crawl/></div>
                    </td>
                    <td>
                    $catdir
                    </td>
                    <td>
                    $crawlName
                    </td>
                    <td>
                    <span class=\"fw-bold\">" . $resultCats['activeOffers'] . " / " . $resultCats['offers'] . "</label>
                    </td>
                    <td>
                    <canvas id=\"bar-$arrayId\"></canvas>
                    </td>
                    <td>
                        <a href=\"editvendors.php?act=edit&id=" . $result['id'] . "\" class=\"mx-auto\" target=\"_blank\" style=\"text-decoration: none;\"><i class=\"fas fa-edit fa-lg mx-1\"></i>
                    </td>
                </tr>";
    $arrayId++;
}
?>