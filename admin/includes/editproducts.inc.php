<?php
include_once(__DIR__ . '/commonfunctions.php');
$act = "";
if (isset($_GET['act'])) { $act=$_GET['act']; }
//Code for ADDING 
if(isset($_POST['add'])) {
    $name = $_POST['name'];
    $url = "";
    if ( $_POST['urlRadio'] == "custom" ) {
        $url = $_POST['customUrl'];
    } else if ( $_POST['urlRadio'] == "default" ) {
        $url = null;
    }
    if (isset($_FILES)) { $images = $_FILES; }
    $brand = $_POST['brand'];
    if (isset($_POST['model'])) { $model = $_POST['model']; }
    $category = $_POST['category'];
    if (isset($_POST['ean'])) { $ean = $_POST['ean']; }
    if (isset($_POST['description'])) { $description = serialize(explode(PHP_EOL, $_POST['description'])); }
    if (isset($_POST['active'])) { 
        $active = 1; 
    } else {
        $active = 0;
    }
    echo addproduct($name, $url, $images, $brand, $model, $category, $ean, $description, $active);
//Code for UPDATING
} else if(isset($_POST['update'])) {
    $id=$_GET['id'];
    $name = $_POST['name'];
    $url = "";
    if ( $_POST['urlRadio'] == "custom" ) {
        $url = $_POST['customUrl'];
    } else if ( $_POST['urlRadio'] == "default" ) {
        $url = null;
    }
    if (isset($_FILES)) { $images = $_FILES; }
    $brand = $_POST['brand'];
    if (isset($_POST['model'])) { $model = $_POST['model']; }
    $category = $_POST['category'];
    if (isset($_POST['ean'])) { $ean = $_POST['ean']; }
    if (isset($_POST['description'])) { $description = serialize(explode(PHP_EOL, $_POST['description'])); }
    if (isset($_POST['active'])) { 
        $active = 1; 
    } else {
        $active = 0;
    }
    echo editproduct($id, $name, $images, $brand, $model, $category, $ean, $description, $active, $url);
}

//Code for deleting
if (isset($_GET['id']) && $_GET['act'] == "del") {
    $id=$_GET['id'];
    $breadcrumb = "";
    $name = "";
    $brandList = "";
    $model = "";
    $modelStrip = "";
    $catlist = "";
    $ean = "";
    $description = "";
    $active = "";
    $dateAdded = "";
    $dateUpdated = "";
    //Delete images
    $query=mysqli_query($con,"SELECT image FROM products WHERE id='$id'");
    $result=mysqli_fetch_array($query);
    $imagesArray = explode(", ", $result[0]);
    for ($i = 0; $i < count($imagesArray); $i++) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/" . $imagesArray[$i]) ) {
            unlink($_SERVER['DOCUMENT_ROOT'] . "/images/" . $imagesArray[$i]);
        }
    }
    //Delete thumbs
    deleteThumbnails($result[0]);
    //Delete product, its offers and its price history
    $msg=mysqli_multi_query($con,"DELETE FROM products WHERE id='$id'; DELETE FROM offers WHERE productID = '$id'; DELETE FROM pricehistory WHERE productId = '$id'");
    echo "<script type='text/javascript'> document.location = 'products.php'; </script>";
}

//Function upload images
function UploadImages($images, $name) {
    if ( $images["images"]["tmp_name"][0] ) {
        $countFiles = count($images['images']['name']);
        $fileNames = [];
        for ($i=0;$i<$countFiles;$i++) {
            $imageFileType = strtolower(pathinfo($images['images']['name'][$i],PATHINFO_EXTENSION));
            $extensions = ['png', 'jpg', 'jpeg', 'webp'];

            $check = getimagesize($images["images"]["tmp_name"][$i]);
            if ( $check == false || !in_array($imageFileType, $extensions) ) {
                continue;
            }
            $cyr = ['+', 'А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ъ','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ъ','ю','я'];
            $lat = ['plus', 'A','B','V','G','D','E','ZH','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','H','C','CH','SH','SHT','Y','A','YU','YA','a','b','v','g','d','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sht','y','a','yu','ya'];
            $name = str_replace($cyr, $lat, $name);
            $fileName = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
            $originalName = $fileName;
            $x = "2";
            while (file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/' . $fileName . '.webp')) {
                $fileName = $originalName . "-" . $x;
                $x++;
            }
            $output = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $fileName .= '.webp';
            exec('convert ' . $images['images']['tmp_name'][$i] . ' -resize "600x>" -format webp ' . $output);
            //move_uploaded_file($images['images']['tmp_name'][$i], $_SERVER['DOCUMENT_ROOT'] . '/images/' . $fileName);
            unlink($images['images']['tmp_name'][$i]);
            array_push($fileNames, $fileName);
        }
        $fileNames = (count($fileNames)) ? implode(', ', $fileNames) : '';
        //Create thumbnails
        createThumbnails($fileNames);
        return $fileNames;
    } else {
        return null;
    }
}

//Function Adding Product
function addproduct($name, $url, $images, $brand, $model, $category, $ean, $description, $active) {
    global $con;
    $name = mysqli_real_escape_string($con, $name);
    if ( $active == "1" && $category == "0") {
        echo "<script>alert('Product \'$name\' is added as INACTIVE due to Unassigned SubCaregory.');</script>"; //Echo because script needs to continue working!
        $active = 0;
    }
    $sql=mysqli_query($con,"SELECT id FROM products WHERE name='$name'");
    $row=mysqli_num_rows($sql);
    $sql=mysqli_query($con,"SELECT id FROM products WHERE ean='$ean'");
    $row2=mysqli_num_rows($sql);
    if( $row > 0 ) {
        return "<script>alert('Product with this name already exists.');</script>";
    } else if ( $row2 > 0 ) {
        return "<script>alert('Product with this EAN already exists.');</script>";
    } else {
        //Check default or custom SEO url
        if ( $url == null ) {
            $url = seourl($name);
        } else {
            $url = seourl($url);
        }
        $sql=mysqli_query($con,"SELECT id FROM products WHERE url='$url'");
        $row=mysqli_num_rows($sql);
        if( $row > 0 ) {
            return "<script>alert('Product with the same SEO url: \"$url\" already exists.');</script>";
        } else {
            //Image Handling
            $fileNames = UploadImages($images, $name);
            //Check if null
            $ean = !empty($ean) ? $ean : "NULL";
            //Inserting to db
            $query=mysqli_query($con,"INSERT into products(name, image, brandID, model, subCatID, ean, description, active, url) values('$name', '$fileNames', '$brand', '$model', '$category', $ean, '$description', '$active', '$url')");
            if ($query) {
                return "<script type='text/javascript'> document.location = 'products.php'; </script>";
            } else {
                $error = mysqli_error($con);
                return "<script>alert('$error');</script>"; //stava za headers debug
            }
        }
    }
}

//Function Edit Product
function editproduct($id, $name, $images = null, $brand, $model, $category, $ean, $description, $active, $url) {
    global $con;
    $name = mysqli_real_escape_string($con, $name);
    if ( $active == "1" && $category == "0") {
        echo "<script>alert('Product \'$name\' changed to INACTIVE due to Unassigned SubCaregory.');</script>"; //Echo because script needs to continue working!
        $active = 0;
    }
    $sql=mysqli_query($con,"select id from products where name='$name' AND id != '$id'");
    $row=mysqli_num_rows($sql);
    $sql=mysqli_query($con,"select id from products where ean='$ean' AND id != '$id'");
    $row2=mysqli_num_rows($sql);
    if( $row > 0 ) {
        return "<script>alert('Product with this name already exists.');</script>";
    } else if ( $row2 > 0 ) {
        return "<script>alert('Product with this EAN already exists.');</script>";
    } else {
        //Check default or custom SEO url
        if ( $url == null ) {
            $url = seourl($name);
        } else {
            $url = seourl($url);
        }
        $sql=mysqli_query($con,"select id from products where url='$url' AND id != '$id'");
        $row=mysqli_num_rows($sql);
        if( $row > 0 ) {
            return "<script>alert('Product with the same SEO url: \"$url\" already exists.');</script>";
        } else {
            $fileNames = UploadImages($images, $name);
            $sql=mysqli_query($con,"SELECT image FROM products where id = '$id'");
            $result=mysqli_fetch_array($sql);
            $imagesNew = '';
            if ($result['image']) {
                $imagesNew = $result['image'];
            }
            if ($fileNames) {
                $imagesNew .= ', ' . $fileNames;
            }
            //Check if null
            $ean = !empty($ean) ? $ean : "NULL";
            $model = strtoupper(preg_replace('/[ |-]+/', '-', $model));
            $modelStrip = preg_replace('/[^A-Z0-9+]+/', '', strtoupper($model));
            $description = mysqli_real_escape_string($con, $description);
            $query=mysqli_query($con,"UPDATE products SET name = '$name', image = '$imagesNew', brandID = '$brand', model = '$model', modelStrip = '$modelStrip', subCatID = '$category', ean = $ean, description = '$description', active = '$active', url = '$url', dateupdated = current_timestamp() WHERE id = '$id'");
            if ($query) {
                return "<script type='text/javascript'> document.location = 'products.php'; </script>";
            } else {
                return "<script>alert('There was an error.');</script>"; //stava za headers debug
            }
        }
    }
}

//Function for generating SEO Urls
function seourl($name) {
    $cyr = ['+', 'А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ъ','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ъ','ю','я'];
    $lat = ['plus', 'A','B','V','G','D','E','ZH','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','H','C','CH','SH','SHT','Y','A','YU','YA','a','b','v','g','d','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sht','y','a','yu','ya'];
    $name = str_replace($cyr, $lat, $name);
    return preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
}

//Editor

//Adding product
if ( isset($_GET['act']) && $_GET['act'] == "add" ) {
    $act = $_GET['act'];
    $name = "";
    $image = ""; //IMAGE TO BE DONE
    $sql=mysqli_query($con,"select * from brand ORDER BY id ASC");
    $brandList = "<option value=\"0\" class=\"fw-bold\" selected>(Unassigned)</option>";
    while ($result=mysqli_fetch_array($sql)) {
        $brandList .= "<option value=\"$result[id]\">$result[brandName]</option>";
    }
    $model = "";
    $modelStrip = "";
    $description = "";

    //Get the categories list
    $sql=mysqli_query($con,"select subcategories.id id, categories.catName catName, subCatName from subcategories LEFT JOIN categories ON subcategories.categoryid = categories.id ORDER BY categories.ix, subcategories.ix ASC");
    $catlist = "<option value=\"0\" class=\"fw-bold\" selected>(Unassigned)</option>";
    $category = "";
    while ($resultcat=mysqli_fetch_array($sql)) {
        $catID = $resultcat['id'];
        $catName = $resultcat['catName'];
        $subCatName = $resultcat['subCatName'];
        if ( $catName != $category ) {
            $catlist .= "<option class=\"text-dark bg-light\" disabled>$catName</option>";
        }
        $catlist .= "<option value=\"$catID\">&nbsp;└&nbsp;$subCatName</option>";
        $category = $resultcat['catName'];
    }
    $dateAdded = "";
    $dateUpdated = "";
    $active = "";
    $ean = "";
    $breadcrumb = "Add Product";
}

//Editing product
if ( isset($_GET['act']) && $_GET['act'] == "edit" && isset($_GET['id'])) {
    $act = $_GET['act'];
    $id = $_GET['id'];
    $query=mysqli_query($con,"SELECT * FROM products WHERE id='$id'");
    $result=mysqli_fetch_array($query);
    $name = htmlspecialchars($result['name']);
    $brand = $result['brandID'];
    $model = $result['model'];
    $modelStrip = $result['modelStrip'];
    $description = ($result['description']) ? implode(PHP_EOL, unserialize($result['description'])) : '';
    $images = $result['image'];
    $subCategory = $result['subCatID'];
    $dateAdded = $result['dateadded'];
    $dateUpdated = $result['dateupdated'];
    $active = $result['active'];
    $url = $result['url'];
    $ean = $result['ean'];
    $breadcrumb = "Edit Product";
    $isCustUrl = false;
    $seoUrl = seourl($result['name']);
    if ($url != $seoUrl) {
        $isCustUrl = true;
    }

    //Get Images
    $imagesString = "";
    $imagesArray = explode(", ", $images);
    if ($imagesArray[0] == '') {
        $imagesString .= "
            <div class=\"col-3 float-start position-relative\">
                <a href=\"#\" class=\"pop\"><img src=\"" . SITE_IMAGES . "no-image.png\" class=\"img-thumbnail rounded mb-2\" alt=\"No Image Available\"></a>
            </div>";
    } else {
        for ($i = 0; $i < count($imagesArray); $i++) {
            $imagesString .= "
            <div class=\"col-3 float-start position-relative\">
                <div class=\"position-absolute top-0 start-0 ms-3\">";
            if ( $i == 0 ) {
                $imagesString .= "<a href=\"#\" class=\"rounded ps-1 pe-1 bg-light opacity-50\"><i class=\"fas fa-chevron-left text-primary\" aria-hidden=\"true\"></i></a>";
            } else {
                $imagesString .= "<a href=\"editproducts.php?act=edit&id=$id&pic=$i&move=left\" class=\"rounded ps-1 pe-1 bg-light\"><i class=\"fas fa-chevron-left text-primary\" aria-hidden=\"true\"></i></a>";
            }
            if ( $i == (count($imagesArray) - 1) ) {
                $imagesString .= "<a href=\"#\" class=\"rounded ps-1 pe-1 bg-light opacity-50\"><i class=\"fas fa-chevron-right text-primary\" aria-hidden=\"true\"></i></a>";
            } else {
                $imagesString .= "<a href=\"editproducts.php?act=edit&id=$id&pic=$i&move=right\" class=\"rounded ps-1 pe-1 bg-light\"><i class=\"fas fa-chevron-right text-primary\" aria-hidden=\"true\"></i></a>";
            }
            $imagesString .="
                </div>
                <a href=\"editproducts.php?act=edit&id=$id&delpic=$i\" class=\"position-absolute rounded top-0 end-0 me-3  ps-1 pe-1 bg-light\" onClick=\"return confirm('Do you really want to delete this picture?');\"><i class=\"fa fa-trash-alt text-danger\" aria-hidden=\"true\"></i></a>
                <a href=\"#\" class=\"pop\"><img src=\"" . SITE_IMAGES . "$imagesArray[$i]\" class=\"img-thumbnail rounded mb-2\" alt=\"$name Image $i\"></a>
            </div>";
        }
    }

    //Get brands
    $sql=mysqli_query($con,"select * from brand ORDER BY id ASC");
    $brandList = "";
    if ( $brand == 0 ) {
        $brandList .= "<option value=\"0\" class=\"fw-bold\" selected>(Unassigned) (Present)</option>";
    } else {
        $brandList .= "<option value=\"0\">(Unassigned)</option>";
    }
    while ($result=mysqli_fetch_array($sql)) {
        $brandName = $result['brandName'];
         if ( $result['id'] == $brand ) {
            $brandList .= "<option value=\"$result[id]\" class=\"fw-bold\" selected>$brandName</option>";
        } else {
            $brandList .= "<option value=\"$result[id]\">$brandName</option>";
        }
    }
    
    //Get the categories list
    $sql=mysqli_query($con,"select subcategories.id id, categories.catName catName, subCatName from subcategories LEFT JOIN categories ON subcategories.categoryid = categories.id ORDER BY categories.ix, subcategories.ix ASC");
    $catlist = "";
    $category = "";
    if ( $subCategory == null || $subCategory == 0 ) {
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
        if ( $subCategory == $catID ) {
            $catlist .= "<option value=\"$catID\" class=\"fw-bold\" selected>&nbsp;└&nbsp;$subCatName (Present)</option>";
        } else {
            $catlist .= "<option value=\"$catID\">&nbsp;└&nbsp;$subCatName</option>";
        }
        $category = $resultcat['catName'];
    }

    //Get offers
    $sql=mysqli_query($con,"SELECT a.id, b.name vendorName, title, link, price, oldprice, dateAdded, dateUpdated, priceUpdated, dateApproved, lastAlive, active FROM offers a LEFT JOIN vendors b ON b.id = a.vendorID WHERE a.productID = $id");
    $offers = mysqli_fetch_all($sql, MYSQLI_ASSOC);
    
}

//Delete Image
if ( isset($_GET['act']) && $_GET['act'] == "edit" && isset($_GET['id']) && isset($_GET['delpic'])) {
    $id = $_GET['id'];
    $query=mysqli_query($con,"SELECT image FROM products WHERE id='$id'");
    $result=mysqli_fetch_array($query);
    $imagesArray = explode(", ", $result[0]);
    $num = $_GET['delpic'];
    $imageToDel = $imagesArray[$num];
    array_splice($imagesArray, $num, 1);
    $imagesArray = implode(', ', $imagesArray);
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/' . $imageToDel)) {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/images/' . $imageToDel);
    }
    //Del thumb
    deleteThumbnails($imageToDel);
    if ( !file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/' . $imageToDel) ) {
        $query=mysqli_query($con,"UPDATE products SET image = '$imagesArray' WHERE id='$id'");
        if ($query) {
            echo "<script type='text/javascript'> document.location = 'editproducts.php?act=edit&id=$id'; </script>";
        } else {
            echo "<script>alert('There was an error.');</script>"; //stava za headers debug
        }
    } else {
        echo "<script>alert('Not deleted.');</script>";
    }
}

//Move image
if ( isset($_GET['act']) && $_GET['act'] == "edit" && isset($_GET['id']) && isset($_GET['pic']) && isset($_GET['move'])) {
    $id = $_GET['id'];
    $pic = $_GET['pic'];
    $move = $_GET['move'];
    $query=mysqli_query($con,"SELECT image FROM products WHERE id='$id'");
    $result=mysqli_fetch_array($query);
    $imagesArray = explode(", ", $result[0]);
    if ( ( $pic > 0 && $move == "left" ) || ( ( $pic < (count($imagesArray) - 1)) && $move == "right" ) ) {
        if ( $move == "right" && ( $pic < (count($imagesArray) - 1) ) ) {
            $picNewPos = $pic + 1;
        } else if ( $move == "left" && $pic > 0 ) {
            $picNewPos = $pic - 1;
        }
        $picName = $imagesArray[$pic];
        $pic2Name = $imagesArray[$picNewPos];
        $imagesArray[$picNewPos] = $picName;
        $imagesArray[$pic] = $pic2Name;
        $imagesArray = implode(', ', $imagesArray);
        $query=mysqli_query($con,"UPDATE products SET image = '$imagesArray' WHERE id='$id'");
        if ($query) {
            echo "<script type='text/javascript'> document.location = 'editproducts.php?act=edit&id=$id'; </script>";
        } else {
            echo "<script>alert('Error moving.');</script>"; //stava za headers debug
        }
    } else {
        echo "<script>alert('Target is already at the end');</script>";
    }
}
?>