<?php
$act = "";
if (isset($_GET['act'])) { $act=$_GET['act']; }
//Code for ADDING 
if(isset($_POST['add'])) {
    $productID = $_POST['productID'];
    $title = $_POST['title'];
    $vendor = $_POST['vendor'];
    $vendorCode = $_POST['vendorCode'];
    $price = $_POST['price'];
    $link = $_POST['link'];
    if (isset($_POST['active'])) { 
        $active = 1; 
    } else {
        $active = 0;
    }
    echo addoffer($productID, $title, $vendor, $vendorCode, $price, $link, $active);
//Code for UPDATING
} else if(isset($_POST['update'])) {
    $id = $_GET['id'];
    $productID = $_POST['productID'];
    $title = $_POST['title'];
    $vendor = $_POST['vendor'];
    $vendorCode = $_POST['vendorCode'];
    $price = $_POST['price'];
    $link = $_POST['link'];
    if (isset($_POST['active'])) { 
        $active = 1; 
    } else {
        $active = 0;
    }
    echo editoffer($id, $productID, $title, $vendor, $vendorCode, $price, $link, $active);
}

//Code for deleting
if ( $act == "del" && isset($_GET['id']) ) {
    $id=$_GET['id'];
    $msg=mysqli_query($con,"DELETE from offers WHERE id='$id'");
    if ($msg) {
        echo "<script type='text/javascript'> document.location = 'offers.php'; </script>";
    }
}

//Function Adding Offer
function addoffer($productID, $title, $vendor, $vendorCode, $price, $link, $active) {
    global $con;
    $sql=mysqli_query($con,"SELECT id from offers WHERE productID='$productID' AND vendorID='$vendor'");
    $row=mysqli_num_rows($sql);
    $sql2=mysqli_query($con,"SELECT id from products WHERE id='$productID'");
    $row2=mysqli_num_rows($sql2);
    if( $row > 0 ) {
        return "<script>alert('Offer for this product from this vendor already exists.');</script>";
    } else if ( $row2 == 0 ) {
        return "<script>alert('There is no such product with ID provided.');</script>";
    } else {
        $query=mysqli_query($con,"INSERT into offers(productID, title, vendorID, vendorCode, price, link, active) values('$productID', '$title', '$vendor', '$vendorCode', '$price', '$link', '$active')");
        if ($query) {
            $offerID = mysqli_insert_id($con);
            mysqli_query($con,"INSERT into pricehistory(offerID, price) values('$offerID', '$price')");
            return "<script type='text/javascript'> document.location = 'offers.php'; </script>";
        } else {
            return "<script>alert('There was an error.');</script>"; //stava za headers debug
        }
    }
}

//Function Edit Offer
function editoffer($id, $productID, $title, $vendor, $vendorCode, $price, $link, $active) {
    global $con;
    $sql=mysqli_query($con,"SELECT id FROM offers WHERE productID='$productID' AND vendorID='$vendor' AND id != '$id' AND link = '$link'");
    $row=mysqli_num_rows($sql);
    if( $row > 0 ) {
        return "<script>alert('Offer for this product from this vendor already exists.');</script>";
    } else {
        $query=mysqli_query($con,"UPDATE offers SET productID = '$productID', title = '$title', vendorID = '$vendor', vendorCode = '$vendorCode', oldprice = price, price = '$price', link = '$link', active = '$active', dateupdated = current_timestamp() WHERE id = '$id'");
        if ($query) {
            //Inserting to price history
            $sql=mysqli_query($con,"SELECT id FROM pricehistory WHERE offerID = '$id' AND date = CURDATE()");
            $row=mysqli_num_rows($sql);
            if ( $row > 0 ) {
                mysqli_query($con,"UPDATE pricehistory SET price = '$price' WHERE offerID = '$id' AND date = CURDATE()");
            } else {
                mysqli_query($con,"INSERT into pricehistory(offerID, price) values('$id', '$price')");
            }
            return "<script type='text/javascript'> document.location = 'offers.php'; </script>";
        } else {
            return "<script>alert('There was an error.');</script>"; //stava za headers debug
        }
    }
}

//Editor

//Adding Offer
if ( $act == "add" ) {
    $productId = "";
    $title = "";
    $vendor = "";
    $vendorCode = "";
    $price = "";
    $link = "";
    $vendorList = "";
    $active = "";
    $sql=mysqli_query($con,"select id, name from vendors ORDER BY id ASC");
    while ($result=mysqli_fetch_array($sql)) {
        $vendorList .= "<option value=\"$result[id]\">$result[name]</option>";
    }
    $breadcrumb = "Add Offer";
}

//Editing Offers
if ( $act == "edit" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql=mysqli_query($con,"SELECT offers.*, products.id productId, products.name productName, products.url productUrl, products.image productImage, subcategories.url subCatUrl FROM offers LEFT JOIN products ON offers.productID = products.ID LEFT JOIN subcategories ON products.subCatID = subcategories.id WHERE offers.id = '$id'");
    $result=mysqli_fetch_array($sql);
    $title = $result['title'];
    $vendor = $result['vendorID'];
    $vendorCode = $result['vendorCode'];
    $price = $result['price'];
    $oldprice = $result['oldprice'];
    $link = $result['link'];
    $active = $result['active'];
    $dateAdded = $result['dateAdded'];
    $dateUpdated = $result['dateUpdated'];
    $priceUpdated = $result['priceUpdated'];
    $lastAlive = $result['lastAlive'];
    $productId = $result['productId'];
    $productName = $result['productName'];
    $productUrl = $result['productUrl'];
    $productImage = explode(", ", $result['productImage']);
    $productImage = SITE_IMAGES . $productImage[0];
    $subCatUrl = $result['subCatUrl'];
    $vendorList = "";
    $sql=mysqli_query($con,"select id, name from vendors ORDER BY id ASC");
    while ($result=mysqli_fetch_array($sql)) {
        if ( $result['id'] == $vendor ) {
            $vendorList .= "<option value=\"$result[id]\" selected>$result[name]</option>";
        } else {
        $vendorList .= "<option value=\"$result[id]\">$result[name]</option>";
        }
    }
    $breadcrumb = "Edit Offer";
}
?>