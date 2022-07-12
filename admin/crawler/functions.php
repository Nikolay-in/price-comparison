<?php
//Check status
function crawlerStatus($flag = null, $newStatus = null) {
    //global $con;
    $con = $GLOBALS['con'];
    $query = mysqli_query($con, "SELECT status, onoff, workFrom, workTo, lastRun FROM crawler");
    $result = mysqli_fetch_array($query);

    $workFrom = date('H:i', strtotime($result['workFrom']));
    $workTo = date('H:i', strtotime($result['workTo']));

    if ( $result['onoff'] == "1" ) {
        if ( $result['status'] == "1" ) {
            if ( time() >= strtotime($workFrom) && time() <= strtotime($workTo) ) { //Working time
                if ( isset($flag) && $flag == "cron" ) { //only cronjob can start the crawler
                    $date = date_create($result['lastRun']);
                    $lastRun = date_format($date, 'Y-m-d');
                    $today = date('Y-m-d');
                    if ( $lastRun < $today ) { //if crawler was last run before todays date
                        $query = mysqli_query($con, "UPDATE crawler SET status = '2' WHERE id = '0'");
                        if ( $query ) {
                            mysqli_query($con, "UPDATE crawler SET lastRun = current_timestamp() WHERE id = '0'");
                            return "12"; //Start working!
                        }
                    } else {
                        return "11"; //Standing-by, last run was today.
                    }
                } else {
                    return "11"; //Bot settings updated from adminpanel, standing-by in working hours, waiting for cronjob to start.
                }
            } else { //Non-working time
                return "11"; //Standing-by
            }
        } else if ( $result['status'] == "2" ) { //Working.
            if ( time() < strtotime($workFrom) || time() > strtotime($workTo) ) { //Non-working time
                if ( isset($flag) && $flag == "bot" ) {
                    $query = mysqli_query($con, "UPDATE crawler SET status = '1' WHERE id = '0'");
                    if ( $query ) {
                        return "21"; //Set to Stand-by.
                    }
                } else {
                    return "22"; //Bot settings updated from adminpanel, the bot will stop crawling itself
                }
            } else if (isset($flag) && $flag == "bot" && $newStatus == 1) { //Crawling completed
                mysqli_query($con, "UPDATE crawler SET status = '1' WHERE id = '0'");
                return "21"; //Set to Stand-by.
            } else { //Working time
                return "22"; //Working.
            }
        } else if ( $result['status'] == "0" ) { //Turned off.
            if ( isset($flag) && $flag == "admin" ) {
                $query = mysqli_query($con, "UPDATE crawler SET status = '1' WHERE id = '0'");
                if ( $query ) {
                    return "01"; //Bot switched on from adminpanel, set to Stand-by regardless the working hours, waiting for cronjob to start the crawler.
                }
            } else {
                return "00"; //Bot is off
            }
        } else {
            return "Invalid status code.";
        }
    } else if ( $result['onoff'] == "0" && $result['status'] != "0") {
        if ( isset($flag) && $flag == "admin" && $result['status'] == "1" ) { //Only admin can turn off from stand-by
            $query = mysqli_query($con, "UPDATE crawler SET status = '0' WHERE id = '0'");
            if ( $query ) {
                return "10"; // Set to off
            }
        } else if ( isset($flag) && $flag == "bot" && $result['status'] == "2" ) { //Only bot can turn off from working
            $query = mysqli_query($con, "UPDATE crawler SET status = '0' WHERE id = '0'");
            if ( $query ) {
                return "20"; // Set to off
            }
        } else {
            return $result['status'] . $result['status'];
        }
    } else {
        return "00"; //Bot is off
    }
}

//Insert vendors cats / subcats
function AddCatsSubcats($vendorId, $catsArray) {
    $con = $GLOBALS['con'];
    $inserted = 'No';
    //Get current cats / subcats of the vendor
    $query = mysqli_query($con, "SELECT b.catID catID, b.catName, a.id as subCatID, a.subCatName FROM vendorssubcats a JOIN (SELECT b.id catID, b.catName catName FROM vendorscats b WHERE b.vendorid = $vendorId) b ON a.categoryid = b.catID ORDER BY b.catID ASC, subCatID ASC");
    if ($query) {
        $result = mysqli_fetch_all($query);
        //Match and get the missing cats / subcats
        foreach ($catsArray as $category) {
            //Check each main category
            $query2 = mysqli_query($con, "SELECT id FROM vendorscats WHERE vendorid = '$vendorId' AND catName = '$category[0]'");
            //If we already have the main category
            if ($result2 = mysqli_fetch_array($query2)) {
                //Get existing subcats and match with the new
                $query2 = mysqli_query($con, "SELECT subCatName FROM vendorssubcats WHERE categoryid = '$result2[id]'");
                $existingSubcats = [];
                //Put them in array
                while($row = mysqli_fetch_array($query2)) {
                    $existingSubcats[] = $row[0];
                }
                $catsQueryArray = [];
                //Check each new cat if it exists in our db, if does not, add it to query array
                foreach ($category as $subcat) {
                    if (is_array($subcat)) {
                        if (!in_array($subcat[0], $existingSubcats)) {
                            //Add each missing subcat to an query array
                            $catsQueryArray[] = '(\'' . $result2['id'] . '\', \'' . mysqli_real_escape_string($con, $subcat[0]) . '\', \'' . mysqli_real_escape_string($con, $subcat[1]) . '\')';
                        }
                    }
                }
                //If we have new categories, insert them
                if (count($catsQueryArray) > 0) {
                    $catsQueryString = implode(', ', $catsQueryArray);
                    $query2 = mysqli_query($con, "INSERT INTO vendorssubcats (categoryid, subCatName, url) values $catsQueryString;");
                    $inserted = 'Yes';
                }
            } else { //If the main category is new
                //Insert new cat and get the ID
                $catName = mysqli_real_escape_string($con, $category[0]);
                $query2 = mysqli_query($con, "INSERT INTO vendorscats (vendorid, catName) values ('$vendorId', '$catName');");
                $inserted = 'Yes';
                if ($query2) {
                    $catID = mysqli_insert_id($con);
                    $catsQueryArray = [];
                    foreach ($category as $subcat) {
                        if (is_array($subcat)) {
                            $catsQueryArray[] = '(\'' . $catID . '\', \'' . mysqli_real_escape_string($con, $subcat[0]) . '\', \'' . mysqli_real_escape_string($con, $subcat[1]) . '\')';
                        }
                    }
                    //Insert to db
                    if (count($catsQueryArray) > 0) {
                        $catsQueryString = implode(', ', $catsQueryArray);
                        $query2 = mysqli_query($con, "INSERT INTO vendorssubcats (categoryid, subCatName, url) values $catsQueryString;");
                        $inserted = 'Yes';
                    }
                }
            }
        }
        //Update last lastCatsCrawled time
        mysqli_query($con, "UPDATE vendors SET lastCatsCrawl = current_timestamp() WHERE id = '$vendorId'");
        echo 'Inserted cats : <strong>' . $inserted . '</strong>';
    } else {
        $error = mysqli_error($con);
        return "<script>alert('$error');</script>";
    }
}

//Handling images
function getImages($images, $title, $context) {
    $cyr = ['А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ъ','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ъ','ю','я'];
    $lat = ['A','B','V','G','D','E','ZH','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','H','C','CH','SH','SHT','Y','A','YU','YA','a','b','v','g','d','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sht','y','a','yu','ya'];
    $title = str_replace($cyr, $lat, $title);
    $title = preg_replace('/[^a-z0-9]+/', '-', strtolower($title));
    $fileNames = [];
    foreach ($images as $imgUrl) {
        $imgUrl = str_replace(' ', '%20', $imgUrl);
        if (getimagesize($imgUrl) == false) {
            continue;
        } else {
            $fileName = $title;
            $imageFileType = strtolower(pathinfo($imgUrl,PATHINFO_EXTENSION));
            $x = "2";
            while (file_exists(__DIR__ . '/../../images/temp/' . $fileName . '.' . $imageFileType)) {
                $fileName = $title . "-" . $x;
                $x++;
            }
            $fileName .= '.' . $imageFileType;
            $copied = copy($imgUrl, __DIR__ . '/../../images/temp/' . $fileName, $context);
            if ($copied == true) {
                array_push($fileNames, $fileName);
            }
        }
    }
    return $fileNames;
}

//Insert / update products
function addProducts($vendor, $products, $assign, $context, $delString = null, $subCatID) {
    $con = $GLOBALS['con'];
    $productsArr = [];
    while (count($products) > 0) {
        //Check if we already have the offers (works)
        $productsArr = array_splice($products, 0, 30); //30 moje da se promeni na 60 za po malko SQL zaqwki
        $sqlString = 'vendorCode = \'' . join('\' OR vendorCode = \'', array_keys($productsArr)) . '\'';
        $lastAliveIDs = [];
        $casesArray = [];
        $casesOldPriceArray = [];
        $idsArray = [];
        $query = mysqli_query($con, "SELECT offers.id id, vendorCode, price, productID FROM offers LEFT JOIN products ON products.id = offers.productID WHERE vendorID = $vendor AND ( $sqlString )");
        $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
        foreach ($result as $row) {
            array_push($lastAliveIDs, $row['id']);
            $vendorCode = $row['vendorCode'];
            $newPrice = number_format($productsArr[$vendorCode]['price'], 2, '.', '');
            //If we find different prices, we put ids and prices in SQL string and update price history (for less queries)
            if ($row['price'] != $newPrice) { 
                $productID = $row['productID'];
                $caseString = 'when id = ' . $row['id'] . ' then \'' . $newPrice . '\'';
                $caseStringOldPrice = 'when id = ' . $row['id'] . ' then \'' . $row['price'] . '\'';
                echo '<br>Existing offer New Price: ' . $caseString . ' - Old Price: ' . $caseStringOldPrice;
                array_push($casesArray, $caseString);
                array_push($casesOldPriceArray, $caseStringOldPrice);
                array_push($idsArray, $row['id']); 
                //Inserting to price history
                $offerID = $row['id'];
                $sql=mysqli_query($con,"SELECT id FROM pricehistory WHERE offerID = '$offerID' AND date = CURDATE()");
                $row=mysqli_num_rows($sql);
                if ( $row > 0 ) {
                    mysqli_query($con,"UPDATE pricehistory SET price = '$newPrice' WHERE offerID = '$offerID' AND date = CURDATE()");
                } else {
                    mysqli_query($con,"INSERT into pricehistory(offerID, price) values('$offerID', '$newPrice')");
                }
                //Update the product discount
                echo updateProductDiscount($productID);
            }
            //Remove item from productsArr, to to remain new products only
            unset($productsArr[$vendorCode]);
        }
        //If we have at least one new price we update offers prices (works)
        if (count($idsArray) > 0) {
            //Set new price of offers 
            $casesString = implode(' ', $casesArray);
            $casesOldPriceString = implode(' ', $casesOldPriceArray);
            $idsString = implode(', ', $idsArray);
            mysqli_query($con, "UPDATE offers SET price = (case $casesString end), oldprice = (case $casesOldPriceString end), priceUpdated = current_timestamp() WHERE id IN ( $idsString );");
        }
        //Update last alive datestamp
        $sqlString = implode(', ', $lastAliveIDs);
        mysqli_query($con, "UPDATE offers SET lastAlive = current_timestamp(), active = 1 WHERE id IN ( $sqlString );");
        
        //Check if the rest of productsArr are already in the temp products table and update lastAlive (works)
        $sqlString = 'productCode = \'' . join('\' OR productCode = \'', array_keys($productsArr)) . '\'';
        mysqli_query($con, "UPDATE tempproducts SET lastAlive = current_timestamp() WHERE vendorId = $vendor AND ( $sqlString );");
        $query = mysqli_query($con, "SELECT id, productCode, price FROM tempproducts WHERE vendorId = $vendor AND ( $sqlString )");
        $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
        foreach ($result as $row) {
            $productID = $row['id'];
            $vendorCode = $row['productCode'];
            $newPrice = number_format($productsArr[$vendorCode]['price'], 2, '.', '');
            $oldPrice = number_format($row['price'], 2, '.', '');
            //Check if price has changed
            if ($oldPrice != $newPrice) { 
                mysqli_query($con,"UPDATE tempproducts SET price = '$newPrice', oldPrice = '$oldPrice' WHERE id = '$productID';");
                //Update price history
                $sql=mysqli_query($con,"SELECT id FROM pricehistory WHERE tempProdId = '$productID' AND date = CURDATE()");
                $row=mysqli_num_rows($sql);
                if ( $row > 0 ) {
                    mysqli_query($con,"UPDATE pricehistory SET price = '$newPrice' WHERE tempProdId = '$productID' AND date = CURDATE()");
                } else {
                    mysqli_query($con,"INSERT into pricehistory (tempProdId, price) values('$productID', '$newPrice')");
                }
                //Update the product discount
                echo updateProductDiscount($productID);
            }
            //Remove item from productsArr, to to remain new products only 
            unset($productsArr[$vendorCode]);
        }
        //Insert the rest of productsArr into temp products table 
        $idKeys = array_keys($productsArr);
        foreach ($idKeys as $prodCode) {
            $oldPrice = (isset($productsArr[$prodCode]['oldPrice']) && $productsArr[$prodCode]['oldPrice'] > 0) ? $productsArr[$prodCode]['oldPrice'] : 0;
            //Check brand
            $brand = mysqli_real_escape_string($con, strtoupper($productsArr[$prodCode]['manufacturer']));
            $query = mysqli_query($con, "SELECT id FROM brand WHERE brandName = '$brand'");
            if ($query) {
                $row = mysqli_num_rows($query);
                if ($row > 0) {
                    $result = mysqli_fetch_assoc($query);
                    $brand = $result['id'];
                } else {
                    $query = mysqli_query($con, "INSERT INTO brand (brandName) VALUES ( '$brand' )");
                    $brand = mysqli_insert_id($con);
                }
            }
            //Copy images to temp folder and set all vars
            $images = serialize(getImages($productsArr[$prodCode]['images'], $productsArr[$prodCode]['title'], $context));
            $ean = 'null';
            if (isset($productsArr[$prodCode]['ean']) && preg_match('/[0-9]{10,20}/', $productsArr[$prodCode]['ean'])) {
                $ean = mysqli_real_escape_string($con, trim($productsArr[$prodCode]['ean']));
            }
            $title = mysqli_real_escape_string($con, preg_replace('/[ ]+/', ' ', $productsArr[$prodCode]['title']));
            if ($delString) {
                $delStringArr = unserialize($delString);
                foreach ($delStringArr as $string) {
                    if (strpos($title, $string) === 0) {
                        $title = trim(substr($title, strlen($string)));
                    }
                }
            }
            $model = mysqli_real_escape_string($con, preg_replace('/[ ]+/', ' ', $productsArr[$prodCode]['model']));
            $modelStrip = (isset($productsArr[$prodCode]['modelStrip']) && $productsArr[$prodCode]['modelStrip'] != 'null') ? '\'' . mysqli_real_escape_string($con, $productsArr[$prodCode]['modelStrip']) . '\'' : 'null';
            $desc = (isset($productsArr[$prodCode]['description']) && $productsArr[$prodCode]['description'] != 'null') ? '\'' . mysqli_real_escape_string($con, $productsArr[$prodCode]['description']) . '\'' : 'null';
            $specs = (isset($productsArr[$prodCode]['specs']) && $productsArr[$prodCode]['specs'] != 'null') ? '\'' . mysqli_real_escape_string($con, json_encode($productsArr[$prodCode]['specs'])) . '\'' : 'null';
            $price = mysqli_real_escape_string($con, number_format($productsArr[$prodCode]['price'], 2, '.', ''));
            $url = mysqli_real_escape_string($con, str_replace(' ', '%20', $productsArr[$prodCode]['url']));
            //Add to temp products table
            mysqli_query($con, "INSERT INTO tempproducts (vendorId, ean, productCode, subCatID, name, brandID, model, modelStrip, description, specs, image, price, oldPrice, url) VALUES ( '$vendor', $ean, '$prodCode', '$assign', '$title', '$brand', '$model', $modelStrip, $desc, $specs, '$images', '$price', '$oldPrice', '$url')");
            $tempProdId = mysqli_insert_id($con);
            $error = mysqli_error($con);
            if ($error) { echo $error . ' ID: ' . $tempProdId . ' PRICE: ' . $price; }
            mysqli_query($con,"INSERT into pricehistory (tempProdId, price) values('$tempProdId', '$price')");
            $error = mysqli_error($con);
            if ($error) { echo $error . ' ID: ' . $tempProdId . ' PRICE: ' . $price; }
        }
    }
    //Update last crawled vendor subcat
    mysqli_query($con, "UPDATE vendorssubcats SET lastCrawl = current_timestamp() WHERE id = '$subCatID'");
    //Update last crawled vendor
    mysqli_query($con, "UPDATE vendors SET lastCrawl = current_timestamp() WHERE id = '$vendor'");
}

//Delete old offers and temp products
function cleanUp($delTempDays, $deactiveOffersDays, $delOffersDays) {
    $con = $GLOBALS['con'];
    //Delete old temp products
    if ($delTempDays > 0) {
        $countProd = 0;
        $query = mysqli_query($con, "SELECT id, image FROM tempproducts WHERE date(lastAlive) <= now() - INTERVAL $delTempDays day");
        if (mysqli_num_rows($query) > 0) {
            while ($result = mysqli_fetch_array($query)) {
                $images = unserialize($result['image']);
                foreach ($images as $image) {
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/temp/" . $image) ) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . "/images/temp/" . $image);
                    }
                }
                $id = $result['id'];
                $del = mysqli_query($con, "DELETE FROM tempproducts WHERE id = '$id'");
                if (mysqli_affected_rows($con)) {
                    $countProd++;
                }
            }
        }
        echo '<br>Deleted dead temp products: <b>' . $countProd . '</b>';
    }

    //Deactivate dead offers
    if ($deactiveOffersDays > 0) {
        $query = mysqli_query($con, "UPDATE offers set active = '0' WHERE date(lastAlive) <= now() - INTERVAL $deactiveOffersDays day");
        $countOffers = mysqli_affected_rows($con);
        echo '<br>Deactivated dead offers: <b>' . $countOffers . '</b>';
    }

    //Delete inactive dead offers
    if ($delOffersDays > 0) {
        $query = mysqli_query($con, "DELETE from offers WHERE active = '0' AND date(lastAlive) <= now() - INTERVAL $delOffersDays day");
        $countOffers = mysqli_affected_rows($con);
        echo '<br>Deleted inactive dead offers: <b>' . $countOffers . '</b>';
    }
}
?>
