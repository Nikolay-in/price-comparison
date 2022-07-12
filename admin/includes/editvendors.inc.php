<?php
$act = "";
if (isset($_GET['id'])) { $catid=$_GET['id']; }
if (isset($_GET['act'])) { $act=$_GET['act']; }

//Code for Adding
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    if ( $_FILES['image']['tmp_name'] ) { 
        $image = $_FILES;
    } else {
        $image = '';
    }
    $website = $_POST['website'];
    $description = $_POST['description'];
    $catdir = $_POST['catdir'];
    $crawlName = $_POST['crawlName'];
    if (isset($_POST['vis']) && $_POST['vis'] == "on") { 
        $vis = 1; 
    } else {
        $vis = 0;
    }
    echo addvendor($name, $image, $website, $description, $vis, $catdir, $crawlName);
//Code for Editing
} else if (isset($_POST['update'])) {
    $id = $_GET['id'];
    $name = $_POST['name'];
    if ( $_FILES['image']['tmp_name'] ) { 
        $image = $_FILES;
    } else {
        $image = "no-new-image";
    }
    $website = $_POST['website'];
    $description = $_POST['description'];
    if ( isset($_POST['vis']) && $_POST['vis'] == "on") { 
        $vis = 1; 
    } else {
        $vis = 0;
    }
    $catdir = $_POST['catdir'];
    $crawlName = $_POST['crawlName'];
    $catSet = "";
    if (isset($_POST['catSet'])) { $catSet = $_POST['catSet']; }
    echo editvendor($id, $name, $image, $website, $description, $vis, $catdir, $crawlName, $catSet);
//Code for Deleting Cat and Subcat
} else if ( ($act == "delcat" && isset($catid)) || ($act == "delsubcat" && isset($catid))) {
    $breadcrumb =  "";
    $name = "";
    $website = "";
    $description = "";
    $catdir = "";
    $crawlName = "";
    $vis = "";
    echo delcat($act, $catid);
}
//Code for Deleting Vendor
if (isset($_GET['id']) && $_GET['act'] == "del") {
    $id = $_GET['id'];
    $breadcrumb =  "";
    $name = "";
    $website = "";
    $description = "";
    $catdir = "";
    $crawlName = "";
    $vis = "";
    echo delvendor($id);
}

//Deleting logo
if ( isset($_GET['act']) && $_GET['act'] == "edit" && isset($_GET['id']) && isset($_GET['delpic'])) {
    //Get Image name
    $id = $_GET['id'];
    $query=mysqli_query($con,"SELECT logo FROM vendors WHERE id='$id'");
    $result=mysqli_fetch_array($query);
    $fileName = $result[0];
    //Delete old and upload new image
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/logos/' . $fileName )) {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/images/logos/' . $fileName );
        $msg=mysqli_query($con,"UPDATE vendors SET logo = '' WHERE id='$id'");
        if( $msg ) {
            echo "<script type='text/javascript'> document.location = 'editvendors.php?act=edit&id=$id'; </script>";
        }
    } else {
        echo "<script>alert('No logo exists.');</script>";
    }
}

//Function Adding Vendor
function addvendor($name, $image, $website, $description, $vis, $catdir, $crawlName) {
    global $con;
    $name = mysqli_real_escape_string($con, $name);
    $sql=mysqli_query($con,"select id from vendors where name='$name'");
    $row=mysqli_num_rows($sql);
    if( $row > 0 ) {
        return "<script>alert('Vendor already exists.');</script>";
    } else {
        //Image Handling
        $fileName = '';
        if ( $image != '' ) {
            $fileName = UploadImage($image, $name);
        }
        $crawlName = !empty($crawlName) ? "'$crawlName'" : "NULL";
        $catdir = !empty($catdir) ? "'$catdir'" : "NULL";
        //Inserting to db
        $query=mysqli_query($con,"INSERT into vendors(name, logo, website, description, vis, catdir, crawlName) values('$name', '$fileName', '$website', '$description', '$vis', $catdir, $crawlName)");
        if ($query) {
            return "<script type='text/javascript'> document.location = 'vendors.php'; </script>";
        } else {
            return "<script>alert('There was an error.');</script>"; //stava za headers debug
        }
    }
}

//Function Editing Vendor
function editvendor($id, $name, $image, $website, $description, $vis, $catdir, $crawlName, $catSet) {
    global $con;
    $parOldVis = "";
    $parNewVis = "";
    $parent = "";
    $crawlName = !empty($crawlName) ? "'$crawlName'" : "NULL";
    $catdir = !empty($catdir) ? "'$catdir'" : "NULL";
    if (is_array($catSet)) {
        foreach ($catSet as $value) {
            $csType = $value['type'];
            $csId = $value['id'];
            $csOvis = $value['ovis'];

            if ( isset($value['newvis']) && $value['newvis'] == "on" ) {
                $csNewVis = 1;
            } else {
                $csNewVis = 0;
            }
            if ( isset($value['crawl']) && $value['crawl'] == "on" && $value['newassign'] != "0" ) {
                $csCrawl = 1;
            } else {
                $csCrawl = 0;
            }
            $csOassign = "";
            $csNewAssign = "";
            if (isset($value['oassign'])) { $csOassign = $value['oassign']; } 
            if (isset($value['newassign'])) { $csNewAssign = $value['newassign']; } 

            //Flag for disabling subcats if cat is disabled
            if ( $csType == "cat" ) { $parNewVis = $csNewVis; $parOldVis = $csOvis; }
            //Flag for enabling parent category when enabling subcat and parent is inactive
            if ( $csType == "cat" && $parent != $csId ) { $parent = $csId; }

            if ( $csType == "cat" && $csOvis != $csNewVis ) {
                $query=mysqli_query($con,"UPDATE vendorscats set active = '$csNewVis' where id = '$csId'");
                if ( $csNewVis == "0" ) {
                    $query=mysqli_query($con,"UPDATE vendorssubcats set active = '0' where categoryid = '$csId'"); //Deactivate all subcats with the category
                }
            }

            if ( $csType == "subCat") {
                $csOcrawl = $value['ocrawl'];
                if ( ( $csOvis != $csNewVis ) || ( $csOassign != $csNewAssign ) || ( $csOcrawl != $csCrawl ) ) { 
                    $query=mysqli_query($con,"UPDATE vendorssubcats set assign = '$csNewAssign' , active = '$csNewVis' , crawl = '$csCrawl' where id = '$csId'");//Update subcat accordingly
                    if ( $csNewVis == "1" ) {
                        $query=mysqli_query($con,"UPDATE vendorscats set active = '1' where id = '$parent'");//Activate category if subcat is activated
                    }
                    if ( $csOvis == "1" && $csNewVis == "0" ) {
                        $query=mysqli_query($con, "SELECT id FROM vendorssubcats WHERE categoryid = '$parent' AND active = '1'");//Check how many deactivated subcats are in the cat
                        $row=mysqli_num_rows($query);
                        if( $row == 0 ) {
                            mysqli_query($con,"UPDATE vendorscats SET active = '0' WHERE id = '$parent'");//Deactivate cat
                        }
                    }
                }
                if ( isset($value['url']) ) {
                    $url = mysqli_real_escape_string($con, $value['url']);
                    $query=mysqli_query($con,"UPDATE vendorssubcats SET url = '$url' WHERE id = '$csId'");
                }
                if ( isset($value['delString']) ) {
                    $delString = '';
                    if ($value['delString'] == '') {
                        $delString = 'NULL';
                    } else {
                        $delString = serialize(array_map('trim', explode(',', $value['delString'])));
                        $delString = "'" . mysqli_real_escape_string($con, $delString) . "'";
                    }
                    $query=mysqli_query($con,"UPDATE vendorssubcats SET delString = $delString WHERE id = '$csId'");
                }
                if ( isset($value['crawl']) && $csNewAssign == "0" && $value['crawl'] == "on" ) {
                    $query=mysqli_query($con, "SELECT subCatName FROM vendorssubcats WHERE id = '$csId'");//Get te name of the subcat
                    $result=mysqli_fetch_array($query);
                    $subCatName = $result['subCatName'];
                    echo "<script type='text/javascript'> alert('SubCategory: \'$subCatName\' cant be enabled for crawling because its unassigned.'); </script>";
                }
            }
        }
    }
    //Get old image name
    $query=mysqli_query($con,"SELECT logo FROM vendors WHERE id='$id'");
    $result=mysqli_fetch_array($query);
    $fileName = $result[0];
    //If we upload new image, delete old and upload new image
    if ( $image != "no-new-image" ) {
        if ( $fileName != '' && file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/logos/' . $fileName ) ) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/images/logos/' . $fileName );
        }
        //Image Handling
        $fileName = UploadImage($image, $name);
    }
    $query=mysqli_query($con,"UPDATE vendors set name = '$name' , logo = '$fileName', website = '$website', description = '$description', vis = '$vis', catdir = $catdir, crawlName = $crawlName where id = '$id'");
    if ($query) {
        return "<script type='text/javascript'> document.location = 'editvendors.php?act=edit&id=$id'; </script>";
    }
}

//Function Deleting Vendor
function delvendor($id) {
    global $con;
    $query=mysqli_query($con,"SELECT logo FROM vendors WHERE id='$id'");
    $result=mysqli_fetch_array($query);
    $fileName = $result[0];
    $msg=mysqli_query($con,"DELETE FROM vendors WHERE id='$id'");
    if( $msg ) {
        if ( $fileName != ''  &&  file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/logos/' . $fileName )) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/images/logos/' . $fileName );
        }
        return "<script type='text/javascript'> document.location = 'vendors.php'; </script>";
    }
}

//Function Deleting Vendors Cat / Subcat
function delcat($act, $id) {
    global $con;
    if ( $act == "delcat" ) {
        $msg=mysqli_multi_query($con,"delete from vendorscats where id='$id'; delete from vendorssubcats where categoryid='$id'");
        return "<script type='text/javascript'> window.history.back(); </script>";
    } else if ( $act == "delsubcat" ) {
        $msg=mysqli_query($con,"delete from vendorssubcats where id='$id'");
        return "<script type='text/javascript'> window.history.back(); </script>";
    }
}

//Function upload images
function UploadImage($image, $name) {
    $extensions = ['png', 'jpg', 'jpeg', 'webp'];
    $imageFileType = strtolower(pathinfo($image['image']['name'],PATHINFO_EXTENSION));

    if (in_array($imageFileType, $extensions) && getimagesize($image['image']['tmp_name']) ) {
        $cyr = ['А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ъ','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ъ','ю','я'];
        $lat = ['A','B','V','G','D','E','ZH','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','H','C','CH','SH','SHT','Y','A','YU','YA','a','b','v','g','d','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sht','y','a','yu','ya'];
        $name = str_replace($cyr, $lat, $name);
        $fileName = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
        $originalName = $fileName;
        $x = "2";
        while (file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/logos/' . $fileName . '.' . $imageFileType)) {
            $fileName = $originalName . "-" . $x;
            $x++;
        }
        $fileName .= '.' . $imageFileType;
        move_uploaded_file($image['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/logos/' . $fileName);
        return $fileName;
    } else {
        return null;
    }
}

//Add / Edit Viewer
if ( $act == "add" ) {
    $id = "";
    $name  = "";
    $logo = "";
    $website  = "";
    $description  = "";
    $vis  = "";
    $catdir  = "";
    $crawlName = "";
    $breadcrumb = "Add Vendor";
}
if ( $act == "edit" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query=mysqli_query($con,"SELECT * FROM vendors WHERE id='$id'");
    $result=mysqli_fetch_array($query);
    $name = $result['name'];
    $logo = ($result['logo'] == '') ? 'no-logo.jpg' : $result['logo'];
    $website = $result['website'];
    $description = $result['description'];
    $vis = $result['vis'];
    $catdir = $result['catdir'];
    $crawlName = $result['crawlName'];
    $breadcrumb = "Edit Vendor";
}
?>