<?php
//Convert images
function convertImage($input, $output) {
    $image = new Imagick($input);
    $image->setFormat('webp');
    if ($image->getImageWidth() > 600) {
        $image->scaleImage(600, 0);
    }
    $result = $image->writeImage($output);
    $image->clear();
    return $result;
}

//Create thumbnails
function createThumbnails($fileNames) {
    $fileNames = explode(', ', $fileNames);
    foreach ($fileNames as $image) {
        if ($image) {
            $thumbName = strtolower(pathinfo($image, PATHINFO_FILENAME));
            $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $thumbName .= '-t.' . $ext;
            $thumb = new Imagick($_SERVER['DOCUMENT_ROOT'] . "/images/" . $image);
            $thumb->scaleImage(200, 0);
            $thumb->writeImage($_SERVER['DOCUMENT_ROOT'] . "/images/" . $thumbName);
            $thumb->clear();
        }
    }
}

//Create thumbnails
function deleteThumbnails($fileNames) {
    $fileNames = explode(', ', $fileNames);
    foreach ($fileNames as $image) {
        $thumbName = strtolower(pathinfo($image, PATHINFO_FILENAME));
        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $thumbName .= '-t.' . $ext;
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/" . $thumbName) ) {
            unlink($_SERVER['DOCUMENT_ROOT'] . "/images/" . $thumbName);
        }
    }
}

//Update prices updated table
function refreshPricesUpdated() {
    $con = $GLOBALS['con'];
    $query = mysqli_query($con, "TRUNCATE TABLE pricesupdated");
    if ($query) {
        $query = mysqli_query($con, "SELECT * FROM
        ( SELECT a.productID pid, b.name, ROW_NUMBER() OVER (PARTITION BY c.categoryid ORDER BY DATE(a.priceUpdated) DESC) AS rn, DATE(MAX(a.priceUpdated)) priceUpdated, d.minPrice
        FROM offers a
        LEFT JOIN products b ON b.id = a.productID
        LEFT JOIN subcategories c ON c.id = b.subCatID
        LEFT JOIN (
            SELECT productID, MIN(price) minPrice FROM offers
            WHERE active = 1
            GROUP BY productID
            ) d
        ON d.productID = a.productID
        WHERE b.active = 1 AND priceUpdated IS NOT NULL AND d.minPrice > 0
        GROUP BY pid
        ORDER BY categoryid, rn ) products
        WHERE rn <= 6;");
        while ($result = mysqli_fetch_array($query)) {
            $pid = $result['pid'];
            $name = $result['name'];
            $rn = $result['rn'];
            $date = $result['priceUpdated'];
            $minPrice = $result['minPrice'];
            mysqli_query($con, "INSERT into pricesupdated (pid, name, rn, date, minPrice) values ('$pid', '$name', '$rn', '$date', '$minPrice')");
        }
        return "<br>PricesUpdated table refreshed.";
    } else {
        return 'Msg: ' . mysqli_error($con);
    }
}

//Update product discount
function updateProductDiscount($productID) {
    $con = $GLOBALS['con'];
    //Get the min live price and the last date it applies for
    $query = mysqli_query($con, "WITH latestPricePerOffer AS (
        SELECT a.id, a.offerID, ROW_NUMBER() OVER (PARTITION BY a.offerID ORDER BY date DESC) AS row_num, a.date, a.price
                FROM pricehistory a
                LEFT JOIN offers ON offers.id = a.offerID
                LEFT JOIN products ON products.id = offers.productID
                WHERE a.price > 0 AND offers.active = 1 AND products.id = '$productID'
            )
        SELECT MIN(price) minPrice, MAX(date) lastDate FROM latestPricePerOffer WHERE row_num = 1;");
    $result = mysqli_fetch_array($query);
    $latestMinPrice = $result['minPrice'];
    $lastDate = $result['lastDate'];
    //Get the min price for the days before last date
    $query = mysqli_query($con, "WITH latestPricePerOffer AS (
        SELECT a.id, a.offerID, ROW_NUMBER() OVER (PARTITION BY a.offerID ORDER BY date DESC) AS row_num, a.date, a.price
                FROM pricehistory a
                LEFT JOIN offers ON offers.id = a.offerID
                LEFT JOIN products ON products.id = offers.productID
                WHERE a.price > 0 AND date < '$lastDate' AND products.id = '$productID'
            )
        SELECT MIN(price) minPrice FROM latestPricePerOffer WHERE row_num = 1;");
    $result = mysqli_fetch_array($query);
    if ($result['minPrice']) {
        $lastMinPrice = $result['minPrice'];
        $discount = ROUND((($lastMinPrice - $latestMinPrice) / $lastMinPrice) * 100);
        mysqli_query($con, "UPDATE products SET discount = '$discount' WHERE id = '$productID'");
        return "<br> Discount updated for $productID: $discount";
    }
}

//Update discounts table
function refreshDiscounts() {
    $con = $GLOBALS['con'];
    $query = mysqli_query($con, "TRUNCATE TABLE discounts");
    if ($query) {
        $query = mysqli_query($con, "SELECT * FROM 
        (SELECT products.id pid, name, DENSE_RANK() OVER (PARTITION BY subcategories.categoryid ORDER BY discount DESC) AS dr, discount, c.minPrice
         FROM products
         LEFT JOIN subcategories ON subcategories.id = subCatID
         LEFT JOIN (
            SELECT productID, MIN(price) minPrice FROM offers
            WHERE active = 1
            GROUP BY productID
            ) c
         ON c.productID = products.id
         WHERE products.active = 1) products 
        WHERE dr <= 3 AND discount > 0;");
        while ($result = mysqli_fetch_array($query)) {
            $pid = $result['pid'];
            $name = $result['name'];
            $dr = $result['dr'];
            $discount = $result['discount'];
            $minPrice = $result['minPrice'];
            mysqli_query($con, "INSERT into discounts (pid, name, rn, discount, minPrice) values ('$pid', '$name', '$dr', '$discount', '$minPrice')");
        }
        return "<br>Discounts table refreshed.";
    } else {
        return 'Msg: ' . mysqli_error($con);
    }
}


?>