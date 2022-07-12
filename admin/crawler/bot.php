<?php
//Check status
$status = crawlerStatus("bot");
echo "Bot: (Code: " . $status . ") <br />" ;

//Get Settings
$query = mysqli_query($con, "SELECT catFreq, prodFreq, secPage, uaList, uaSet, delTempDays, deactiveOffersDays , delOffersDays FROM crawler");
$result = mysqli_fetch_array($query);
$catFreq = $result['catFreq'];
$prodFreq = $result['prodFreq'];
$secPage = $result['secPage'];
$uaList = explode("\n", $result['uaList']);
$uaSet = $result['uaSet'];
$delTempDays = $result['delTempDays'];
$deactiveOffersDays = $result['deactiveOffersDays'];
$delOffersDays = $result['delOffersDays'];
$uaCount = (count($uaList) - 1);

//Get vendors for Categories crawling if its due according the category crawling frequency (catFreq)
include_once('./vendors/getcategories.php');
$query = mysqli_query($con, "SELECT id, catdir, crawlName FROM vendors WHERE crawl = '1' AND catdir IS NOT NULL AND crawlName IS NOT NULL AND ( date(lastCatsCrawl) <= now() - INTERVAL $catFreq day OR lastCatsCrawl IS NULL )");
while ($result = mysqli_fetch_array($query)) {
    //Set user agent
    $uaName = ($uaSet == 0) ? $uaList[rand(0, $uaCount)] : $uaList[$uaSet - 1];
    getCategories($result['crawlName'], $result['id'], $result['catdir'], $uaName); 
}

//Get vendors for products crawling
$urlsArray = [];
$query = mysqli_query($con, "SELECT id, crawlName FROM vendors WHERE crawl = '1' AND crawlName IS NOT NULL AND ( date(lastCrawl) <= now() - INTERVAL $prodFreq day OR lastCrawl IS NULL ) ORDER BY RAND()");
while ($result = mysqli_fetch_array($query)) {
    //Get urls
    $query2 = mysqli_query($con, "SELECT a.id subCatID, a.url url, a.assign assign, a.delString delString, b.vendorid vendorid FROM vendorssubcats a JOIN vendorscats b ON b.id = a.categoryid JOIN vendors c ON c.id = b.vendorid WHERE a.crawl = 1 AND b.vendorid = $result[id] AND a.assign != 0 ORDER BY RAND();");
    while ($result2 = mysqli_fetch_array($query2)) {
        $urlObj = (object) [];
        $urlObj->vendor = $result2['vendorid'];
        $urlObj->crawlName = $result['crawlName'];
        $urlObj->subCatID = $result2['subCatID'];
        $urlObj->url = $result2['url'];
        $urlObj->assign = $result2['assign'];
        $urlObj->delString = $result2['delString'];
        array_push($urlsArray, $urlObj);
    }
}
//Sort by random (Method 1)
// $i = rand(0, count($urlsArray) - 1);
// while (count($urlsArray) > 0) {
//     echo $urlsArray[$i]->vendor . ' - ' . $urlsArray[$i]->crawlName . ' - ' . $urlsArray[$i]->url . ' - ' . $urlsArray[$i]->assign . " <br />";
//     array_splice($urlsArray, $i, 1);
//     if (count($urlsArray) > 0) {
//         $i = rand(0, count($urlsArray) - 1);
//     }
// }

// Sort 1 vendor per link (Method 2)
include_once('./vendors/getproducts.php');
$lastPrintedVendor = '';
$i = 0;
//Set user agent
$uaName = ($uaSet == 0) ? $uaList[rand(0, $uaCount)] : $uaList[$uaSet - 1];
while (count($urlsArray) > 0) {
    if ($i == count($urlsArray)) {
        $i = 0;
        continue;
    }
    $lastId = count($urlsArray) - 1;
    if (($urlsArray[$i]->crawlName != $lastPrintedVendor) || ($urlsArray[0]->crawlName == $urlsArray[$lastId]->crawlName)) {
        print_r( " <br />" . $urlsArray[$i]->vendor . ' - ' . $urlsArray[$i]->crawlName . ' - ' . $urlsArray[$i]->url . ' - ' . $urlsArray[$i]->assign . " <br />");
        getProducts($urlsArray[$i]->crawlName, $urlsArray[$i]->vendor, str_replace(' ', '%20', $urlsArray[$i]->url), $urlsArray[$i]->assign, $uaName, $urlsArray[$i]->delString, $urlsArray[$i]->subCatID);
        $lastPrintedVendor = $urlsArray[$i]->crawlName;
        array_splice($urlsArray, $i, 1);
        //Reset timeout limit after each category url is crawled (60s by default max_execution time in php.ini)
        set_time_limit(60);
        continue;
    } else {
        $i++;
    }
}
//Deactivate old offers and Delete temp products parameter - days ago
cleanUp($delTempDays, $deactiveOffersDays, $delOffersDays);
//Update discounts
echo refreshDiscounts();
//Update prices updated table
echo refreshPricesUpdated();
?>