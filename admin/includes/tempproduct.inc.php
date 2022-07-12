<?php
include_once(__DIR__ . '/commonfunctions.php');
//Code for viewing
if (isset($_GET['model']) && isset($_GET['brand'])) {
    $model = $_GET['model'];
    $brand = $_GET['brand'];
    //Check if there is existing product with this brand and model
    $exists = 0;
    $query = mysqli_query($con, "SELECT a.id, a.ean, a.image, a.name, b.brandName, a.brandID, a.model, a.description, d.catName, c.subCatName, a.subCatID, a.dateadded, a.dateupdated, MIN(offers.price) minPrice, MAX(offers.price) maxPrice, COUNT(offers.id) offers FROM products a 
    LEFT JOIN brand b ON a.brandID = b.id 
    LEFT JOIN subcategories c ON a.subCatID = c.id 
    LEFT JOIN categories d ON c.categoryid = d.id 
    JOIN offers ON offers.productID = a.id
    WHERE a.brandID = '$brand' AND a.modelStrip = '$model'");
    $resultExs = [];
    //If it is existing we fetch data to push to front page
    if (mysqli_num_rows($query) > 0) { 
        $resultExs = mysqli_fetch_array($query);
        $exists = $resultExs['id']; 
    }
    
    //We get the data for the new temp product -> future offer
    $query = mysqli_query($con, "SELECT a.id, e.name vendorName, a.image, a.name, b.brandName, a.brandID, a.model, a.ean, d.catName catName, c.subCatName subCatName, a.description, a.url, a.subCatID, a.price, a.dateadded, a.lastAlive FROM tempproducts a 
    LEFT JOIN brand b ON a.brandID = b.id 
    LEFT JOIN subcategories c ON a.subCatID = c.id 
    LEFT JOIN categories d ON c.categoryid = d.id 
    LEFT JOIN vendors e ON a.vendorId = e.id 
    WHERE modelStrip = '$model' AND a.active = 1 AND a.brandID = $brand");
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC);

    //Declare vars
    $title = $result[0]['name'];
    $brandId = $result[0]['brandID'];
    $model = $result[0]['model'];
    $ean = 'null';
    $subCatID = $result[0]['subCatID'];
    $prices = [];
    $images = [];
    $imagesOrig = [];
    $offersIds = [];
    foreach ($result as $item) {
        $imagesArr = unserialize($item['image']);
        $vendorName = $item['vendorName'];
        array_unshift($imagesArr, $vendorName);
        array_push($images, $imagesArr);
        array_push($prices, $item['price']);
        array_push($offersIds, $item['id']);
        if ($ean == 'null' && $item['ean']) {
            $ean = $item['ean'];
        }
    }
    $offersIds = implode(',', $offersIds);
    //If there is existing product put its data as default for the front page
    if ($exists > 0) {
        $title = $resultExs['name'];
        $brandId = $resultExs['brandID'];
        $model = $resultExs['model'];
        $ean = (!$resultExs['ean']) ? 'null' : $resultExs['ean'];
        $subCatID = $resultExs['subCatID'];
        array_push($prices, $resultExs['minPrice'], $resultExs['maxPrice']);
        $imagesOrig = explode(', ', $resultExs['image']);
        //Test
        $resultExs['id'] = 'existing';
        $resultExs['vendorName'] = 'Offers: ' . $resultExs['offers'];
        $resultExs['price'] =  $resultExs['minPrice'] . ' - ' . $resultExs['maxPrice'];
        $resultExs['lastAlive'] = 'N/A';
        array_unshift($result, $resultExs);
    }
    $maxPrice = max($prices);
    $minPrice = min($prices);

    //Get brands
    $sql=mysqli_query($con,"SELECT * FROM brand ORDER BY id ASC");
    $brandList = "";
    if ( $brand == 0 ) {
        $brandList .= "<option value=\"0\" class=\"fw-bold\" selected>(Unassigned) (Present)</option>";
    } else {
        $brandList .= "<option value=\"0\">(Unassigned)</option>";
    }
    while ($resultBrand=mysqli_fetch_array($sql)) {
        $brandName = $resultBrand['brandName'];
            if ( $resultBrand['id'] == $brandId ) {
            $brandList .= "<option value=\"$resultBrand[id]\" class=\"fw-bold\" selected>$brandName</option>";
        } else {
            $brandList .= "<option value=\"$resultBrand[id]\">$brandName</option>";
        }
    }

    //Get the categories list
    $sql=mysqli_query($con,"select subcategories.id id, categories.catName catName, subCatName from subcategories LEFT JOIN categories ON subcategories.categoryid = categories.id ORDER BY categories.ix, subcategories.ix ASC");
    $catlist = "";
    $category = "";
    if ( $subCatID == null || $subCatID == 0 ) {
        $catlist .= "<option value=\"0\" class=\"fw-bold\" selected>(Unassigned) (Present)</option>";
    } else {
        $catlist .= "<option value=\"0\">(Unassigned)</option>";
    }
    while ($resultcat=mysqli_fetch_array($sql)) {
        $catID = $resultcat['id'];
        $catName = $resultcat['catName'];
        $subCatName = $resultcat['subCatName'];
        if ( $catName != $category ) {
            $catlist .= "<option class=\"text-dark bg-light\" disabled>$catName</option>";
        }
        if ( $subCatID == $catID ) {
            $catlist .= "<option value=\"$catID\" class=\"fw-bold\" selected>&nbsp;└&nbsp;$subCatName (Present)</option>";
        } else {
            $catlist .= "<option value=\"$catID\">&nbsp;└&nbsp;$subCatName</option>";
        }
        $category = $resultcat['catName'];
    }
}

//Code for ADDING 
if(isset($_POST['submit'])) {
    $title = $_POST['title'];
    $images = explode(',', $_POST['images']);
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $brandOrig = $_GET['brand'];
    $modelStrip = $_GET['model'];
    $ean = ($_POST['ean'] == 'null') ? '' : $_POST['ean'];
    $subCat = $_POST['subCat'];
    $description = ($_POST['description']) ? serialize(explode(PHP_EOL, $_POST['description'])) : null;
    $offers = explode(',', $_POST['offers']);
    $exists = (isset($_POST['exists'])) ? $_POST['exists'] : null;
    echo addproduct($title, $images, $brand, $model, $ean, $subCat, $description, $offers, $brandOrig, $modelStrip, $exists);
}

//Function for generating SEO Urls
function seourl($name) {
    $cyr = ['+', 'А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ъ','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ъ','ю','я'];
    $lat = ['plus', 'A','B','V','G','D','E','ZH','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','H','C','CH','SH','SHT','Y','A','YU','YA','a','b','v','g','d','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sht','y','a','yu','ya'];
    $name = trim(str_replace($cyr, $lat, $name));
    return preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
}

//Function to make filename from title
function fileNameFromTitle($title) {
    $cyr = ['+', 'А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ъ','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ъ','ю','я'];
    $lat = ['plus', 'A','B','V','G','D','E','ZH','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','H','C','CH','SH','SHT','Y','A','YU','YA','a','b','v','g','d','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sht','y','a','yu','ya'];
    $name = trim(str_replace($cyr, $lat, $title));
    return preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
}

//Move images
function moveImages($images, $name) {
    if ($images[0]) {
        $countFiles = count($images);
        $fileNames = [];
        for ($i = 0; $i < $countFiles; $i++) {
            $image = $_SERVER['DOCUMENT_ROOT'] . '/images/temp/' . $images[$i];
            $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $fileName = fileNameFromTitle($name);
            $originalName = $fileName;
            $x = "2";
            while (file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/' . $fileName . '.webp')) {
                $fileName = $originalName . "-" . $x;
                $x++;
            }
            $output = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $fileName . '.webp';
            $convertResult = convertImage($image, $output);
            if ($convertResult && file_exists($output)) {
                unlink($image);
                array_push($fileNames, $fileName . '.webp');
            } else {
                array_push($fileNames, 'temp/' . $images[$i]);
            }
        }
        $fileNames = implode(', ', $fileNames);
        //Create thumbnails
        createThumbnails($fileNames);
        return $fileNames;
    } else {
        return null;
    }
}

//Add new images to product
function updateProductImages($images, $name, $oldImages) {
    //Leave only images to be deleted in $oldImages
    foreach ($images as $image) {
        if (substr($image, -5) == '-orig') {
            $image = substr($image, 0, strlen($image) - 5);
            $index = array_search($image, $oldImages);
            if ($index !== false) {
                array_splice($oldImages, $index, 1);
            }
        }
    }
    //Delete old images
    foreach ($oldImages as $oldImage) {
        //Delete main images
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/" . $oldImage) ) {
            unlink($_SERVER['DOCUMENT_ROOT'] . "/images/" . $oldImage);
        }
        //Delete thumbnails
        $thumbName = strtolower(pathinfo($oldImage, PATHINFO_FILENAME));
        $ext = strtolower(pathinfo($oldImage, PATHINFO_EXTENSION));
        $thumbName .= '-t.' . $ext;
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/" . $thumbName) ) {
            unlink($_SERVER['DOCUMENT_ROOT'] . "/images/" . $thumbName);
        }
    }
    //Move new images and rename
    $newImages = [];
    foreach ($images as $image) {
        $fileName = fileNameFromTitle($name);
        $originalName = $fileName;
        $x = "2";
        $fromTemp = false;
        if (substr($image, -5) == '-orig') {
            $dir = '/images/';
            $image = substr($image, 0, strlen($image) - 5);
        } else {
            $dir = '/images/temp/';
            $fromTemp = true;
        }
        while (file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/' . $fileName . '.webp')) {
            if ($fileName . '.webp' == $image && $fromTemp == false) {
                break;
            } else {
                $fileName = $originalName . "-" . $x;
                $x++;
            }
        }
        $input = $_SERVER['DOCUMENT_ROOT'] . $dir . $image;
        $inputExt = strtolower(pathinfo($image, PATHINFO_EXTENSION));

        $output = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $fileName . '.webp';
        $outputThumb = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $fileName . '-t.webp';

        if ($fromTemp) {
            $convertResult = convertImage($input, $output);
            if ($convertResult) {
                unlink($input);
                //Create thumbnail
                createThumbnails($fileName . '.webp');
            }
        } else if ($input != $output) {
            rename($input, $output);
            //Rename thumbnail
            $inputFileName = strtolower(pathinfo($image, PATHINFO_FILENAME));
            $inputThumb = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $inputFileName . '-t.' . $inputExt;
            rename($inputThumb, $outputThumb);
        }
        if (file_exists($output)) {
            array_push($newImages, $fileName . '.webp');
        }
    }
    $fileNames = implode(', ', $newImages);
    return $fileNames;
}

//Create offers and check if it is a promotion
function createOffers($offers, $productID) {
    global $con;
    if (count($offers) > 0) {
        $tempIds = '';
        if (count($offers) == 1) {
            $tempIds = $offers[0];
        } else {
            $tempIds = implode(', ', $offers);
        }
        //Check for a promotion
        //Get the min live price and the last date it applies for
        $query = mysqli_query($con, "WITH latestPricePerOffer AS (
            SELECT id, offerID, ROW_NUMBER() OVER (PARTITION BY pricehistory.tempProdId ORDER BY date DESC) AS row_num, date, price
            FROM pricehistory	
            WHERE price > 0 AND tempProdId IN ( $tempIds )
        )
        SELECT MIN(price) minPrice, MAX(date) lastDate FROM latestPricePerOffer WHERE row_num = 1;");
        $result = mysqli_fetch_array($query);
        $latestMinPrice = $result['minPrice'];
        $lastDate = $result['lastDate'];
        //Get the min price for the days before last date
        $query = mysqli_query($con, "WITH lastPricePerOffer AS (
            SELECT id, offerID, ROW_NUMBER() OVER (PARTITION BY pricehistory.tempProdId ORDER BY date DESC) AS row_num, date, price
            FROM pricehistory	
            WHERE price > 0 AND date < '$lastDate' AND tempProdId IN ( $tempIds )
        )
        SELECT MIN(price) minPrice FROM lastPricePerOffer WHERE row_num = 1;");
        $result = mysqli_fetch_array($query);
        if ($result['minPrice']) {
            $lastMinPrice = $result['minPrice'];
            $discount = ROUND((($lastMinPrice - $latestMinPrice) / $lastMinPrice) * 100);
            mysqli_query($con, "UPDATE products SET discount = '$discount' WHERE id = '$productID'");
        }

        //Start transfering temp products as offers
        $query = mysqli_query($con, "SELECT id, vendorId, productCode, price, oldPrice, name, url, dateadded, lastAlive FROM tempproducts WHERE id IN ($tempIds)");
        $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
        foreach ($result as $row) {
            $tempProdId = $row['id'];
            $vendorId = $row['vendorId'];
            $productCode = $row['productCode'];
            $price = $row['price'];
            $oldPrice = $row['oldPrice'];
            $name = $row['name'];
            $url = $row['url'];
            $dateadded = $row['dateadded'];
            $lastAlive = $row['lastAlive'];
            mysqli_query($con, "INSERT INTO offers(productID, vendorID, vendorCode, price, oldprice, title, link, dateAdded, lastAlive, dateApproved, active) values($productID, '$vendorId', '$productCode', '$price', '$oldPrice', '$name', '$url', '$dateadded', '$lastAlive', CURRENT_TIMESTAMP(), '1')");
            //Transfer price history to the new offer and null tempProdId
            $offerID = mysqli_insert_id($con);
            mysqli_query($con,"UPDATE pricehistory SET offerID = '$offerID', tempProdId = NULL WHERE tempProdId = '$tempProdId'");
        }
    }
}

//Delete Temps
function deleteTemp($brandOrig, $modelStrip, $offers) {
    global $con;
    $query = mysqli_query($con, "SELECT id, image FROM tempproducts WHERE brandID = '$brandOrig' AND modelStrip = '$modelStrip'");
    $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
    foreach ($result as $row) {
        if ($row['image']) {
            $images = unserialize($row['image']);
            foreach ($images as $image) {
                $img = $_SERVER['DOCUMENT_ROOT'] . '/images/temp/' . $image;
                if (file_exists($img)) {
                    unlink($img);
                }
            }
        }
    }
    //Delete temp products which became offers
    $ids = implode(', ', $offers);
    mysqli_query($con, "DELETE FROM tempproducts WHERE id IN ($ids)");
    //Set images to NULL and active to 0 to whats left unwanted temp product
    $query = mysqli_query($con, "UPDATE tempproducts SET image = null, active = 0 WHERE brandID = '$brandOrig' AND modelStrip = '$modelStrip'");
}

//Update brands products count
function updateBrandsProducts() {
    $con = $GLOBALS['con'];
    $query = mysqli_query($con, "SELECT brandID, count(*) products FROM products GROUP BY brandID;");
    if ($query) {
        while ($result = mysqli_fetch_array($query)) {
            $id = $result['brandID'];
            $products = $result['products'];
            mysqli_query($con, "UPDATE brand SET products = '$products' WHERE id = '$id'");
        }
    } else {
        echo 'Msg: ' . mysqli_error($con);
    }
}

//Update latest table
function refreshLatest() {
    $con = $GLOBALS['con'];
    $query = mysqli_query($con, "TRUNCATE TABLE latest");
    if ($query) {
        $query = mysqli_query($con, "SELECT * FROM
        (SELECT a.id pid, a.name, DENSE_RANK() OVER (PARTITION BY b.categoryid ORDER BY DATE(c.dateAdded) DESC) AS dr, DATE(c.dateAdded) dateAdded, d.minPrice
        FROM products a
        LEFT JOIN subcategories b ON b.id = subCatID
        LEFT JOIN (
            SELECT productID, MIN(price) minPrice FROM offers
            WHERE active = 1
            GROUP BY productID
            ) d
        ON d.productID = a.id
        RIGHT JOIN (
            SELECT productID, dateAdded FROM offers
            WHERE active = 1
            ) c ON c.productID = a.id
        WHERE a.active = 1) products
        WHERE dr <= 3
        GROUP BY pid  
        ORDER BY dateAdded DESC;");
        while ($result = mysqli_fetch_array($query)) {
            $pid = $result['pid'];
            $name = $result['name'];
            $dr = $result['dr'];
            $date = $result['dateAdded'];
            $minPrice = $result['minPrice'];
            mysqli_query($con, "INSERT into latest (pid, name, dr, date, minPrice) values ('$pid', '$name', '$dr', '$date', '$minPrice')");
        }
    } else {
        echo 'Msg: ' . mysqli_error($con);
    }
}

//Function Adding Product
function addproduct($title, $images, $brand, $model, $ean, $subCat, $description, $offers, $brandOrig, $modelStrip, $exists) {
    global $con;
    $name = trim(mysqli_real_escape_string($con, $title));
    if ($exists == null) { //If the product is new
        $sql=mysqli_query($con,"SELECT id FROM products WHERE name='$name'");
        $row=mysqli_num_rows($sql);
        $sql=mysqli_query($con,"SELECT id FROM products WHERE ean='$ean' AND ean IS NOT NULL");
        $row2=mysqli_num_rows($sql);
        if( $row > 0 ) {
            return "<script>alert('Product with this name already exists.'); history.back();</script>";
        } else if ( $row2 > 0 ) {
            return "<script>alert('Product with this EAN already exists.'); history.back();</script>";
        } else {
            //Create SEO url
            $url = seourl($title);
            $sql=mysqli_query($con,"SELECT id FROM products WHERE url='$url'");
            $row=mysqli_num_rows($sql);
            if( $row > 0 ) {
                return "<script>alert('Product with the same SEO url: \"$url\" already exists.'); history.back();</script>";
            } else {
                //Move selected images
                $fileNames = moveImages($images, $title);
                
                //Create new product
                $ean = !empty($ean) ? $ean : "NULL";
                //Escape the description
                $description = mysqli_real_escape_string($con, $description);
                //Inserting to db
                $query=mysqli_query($con,"INSERT INTO products(name, image, brandID, model, modelStrip, subCatID, ean, description, active, url) values('$name', '$fileNames', '$brand', '$model', '$modelStrip', '$subCat', $ean, '$description', '1', '$url')");
                $productID = mysqli_insert_id($con);
                if ($query) {
                    //Create Offers from temp products
                    createOffers($offers, $productID);
                    //Delete unneeded images and temp products
                    deleteTemp($brandOrig, $modelStrip, $offers);
                    //Calculate the product discount
                    updateProductDiscount($productID);
                    //Refresh latest products table
                    refreshLatest();
                    //Update prices updated table
                    refreshPricesUpdated();
                    //Refresh discounts table
                    refreshDiscounts();
                    //Update brands total products
                    updateBrandsProducts();

                    if ($_POST['goToNext'] == 'true') {
                        $query = mysqli_query($con, "SELECT a.id id, b.count, a.brandID, a.modelStrip FROM tempproducts a 
                        LEFT JOIN (SELECT brandID, modelStrip, subCatID, count(*) count FROM tempproducts GROUP BY brandID, modelStrip) b ON a.modelStrip = b.modelStrip AND a.brandID = b.brandID
                        WHERE a.active = 1
                        ORDER BY b.count DESC, a.modelStrip DESC, a.dateadded DESC
                        LIMIT 1;");
                        if (mysqli_num_rows($query) == 1) {
                            $result = mysqli_fetch_array($query);
                            return "<script> document.location = 'tempproduct.php?brand=" . $result['brandID'] . "&model=" . urlencode($result['modelStrip']) . "'; </script>";
                        }
                        return "<script> alert('No more temp products.'); document.location = 'tempproducts.php'; </script>";
                    }
                    return "<script> document.location = 'tempproducts.php'; </script>";
                } else {
                    $error = mysqli_error($con);
                    return "<script>alert(\"$error\");</script>"; //stava za headers debug
                }
            }
        }
    } else { //If we add new offer to an old product
        $productID = $exists;
        //Create SEO url in case we change the name
        $url = seourl($title);
        //Get old images list
        $query = mysqli_query($con, "SELECT image FROM products WHERE id = '$productID'");
        $result = mysqli_fetch_array($query);
        $oldImages = explode(', ', $result['image']);
        //Merge new images to old product
        $fileNames =  updateProductImages($images, $title, $oldImages);
        //Update product info
        $ean = !empty($ean) ? $ean : "NULL";
        //Inserting to db
        $query = mysqli_query($con, "UPDATE products SET ean = $ean, image = '$fileNames', name = '$name', model = '$model', subCatID = '$subCat', description = '$description', dateupdated = current_timestamp(), url = '$url' WHERE id = '$productID'");
        if ($query) {
            //Create Offers from temp products
            createOffers($offers, $productID);
            //Delete unneeded images, temp products and deactivate unneedded temps (delete pics) but keep them as inactive 
            deleteTemp($brandOrig, $modelStrip, $offers);
            //Calculate the product discount
            updateProductDiscount($productID);
            //Refresh latest products table
            refreshLatest();
            //Update prices updated table
            refreshPricesUpdated();
            //Refresh discounts table
            refreshDiscounts();

            if ($_POST['goToNext'] == 'true') {
                $query = mysqli_query($con, "SELECT a.id id, b.count, a.brandID, a.modelStrip FROM tempproducts a 
                LEFT JOIN (SELECT brandID, modelStrip, subCatID, count(*) count FROM tempproducts GROUP BY brandID, modelStrip) b ON a.modelStrip = b.modelStrip AND a.brandID = b.brandID
                WHERE a.active = 1 AND EXISTS(SELECT * FROM products WHERE products.brandID = a.brandID AND products.modelStrip = a.modelStrip) = 1
                ORDER BY b.count DESC, a.modelStrip DESC, a.dateadded DESC
                LIMIT 1;");
                if (mysqli_num_rows($query) == 1) {
                    $result = mysqli_fetch_array($query);
                    return "<script> document.location = 'tempproduct.php?brand=" . $result['brandID'] . "&model=" . urlencode($result['modelStrip']) . "'; </script>";
                }
                return "<script> alert('No more temp existing products.'); document.location = 'tempproducts.php'; </script>";
            }
            return "<script> document.location = 'tempproducts.php?exists=1'; </script>";
        } else {
            $error = mysqli_error($con);
            return "<script>alert('$error');</script>";
        }
    }
}
?>