<?php
$act = "";
if ( isset($_GET['id'])) { $catid=$_GET['id']; }
if ( isset($_GET['act'])) { $act=$_GET['act']; }

//Code for ADDING 
if( isset($_POST['add']) ) {
    $name = $_POST['name'];
    $url = "";
    if ( $_POST['urlRadio'] == "custom" ) {
        $url = $_POST['customUrl'];
    } else if ( $_POST['urlRadio'] == "default" ) {
        $url = null;
    }

    if ( isset($_POST['vis']) ) { 
        $vis = 1; 
    } else {
        $vis = 0;
    }

    if ( isset($_POST['parent']) ) { 
        $parent = $_POST['parent']; 
    } else {
        $parent = 0;
    }

    if (isset($_POST['icon'])) {
        $icon = $_POST['icon'];
    } else {
        $icon = '';
    }

    $aliExId = isset($_POST['aliExId']) ? $_POST['aliExId'] : '';

    echo addcat($act, $url, $name, $vis, $parent, $icon, $aliExId);
//Code for UPDATING
} else if(isset($_POST['update'])) {
    $id=$_GET['id'];
    $name = $_POST['name'];
    $ix=$_POST['fix'];
    $oix=$_POST['ofix'];
    $oparent = "";
    $url = "";

    if ( $_POST['urlRadio'] == "custom" ) {
        $url = $_POST['customUrl'];
    } else if ( $_POST['urlRadio'] == "default" ) {
        $url = null;
    }

    if (isset($_POST['oparent'])) { $oparent=$_POST['oparent']; }

    $vis = "";
    if (isset($_POST['vis'])) { $vis = $_POST['vis']; }

    $parent = "";
    if (isset($_POST['parent'])) { $parent = $_POST['parent']; }
    
    $icon = "";
    if (isset($_POST['icon'])) { $icon = $_POST['icon']; }

    $aliExId = isset($_POST['aliExId']) ? $_POST['aliExId'] : '';

    echo editcatsubcat($act, $id, $name, $ix, $oix, $parent, $vis, $oparent, $url, $icon, $aliExId);
}

//Code For deleting cat/subcat
if ( ( isset($_GET['id']) && $_GET['act'] == "delcat" ) || ( isset($_GET['id']) && $_GET['act'] == "delsubcat" ) ) {
    $act = $_GET['act'];
    $id = $_GET['id'];
    //Below are to avoid errors in the error log.
    $cat = "";
    $name = "";
    $breadcrumb = "";
    $vis = "";
    echo delcatsubcat($act, $id);
}

//Function Adding cat / subcat
function addcat($act, $url, $name, $vis, $parent, $icon, $aliExId) {
    global $con;
    //Check if name is unique.
    $name = mysqli_real_escape_string($con, $name);
    $sql=mysqli_query($con,"SELECT id FROM categories WHERE catName='$name'");
    $row=mysqli_num_rows($sql);
    $sql=mysqli_query($con,"SELECT id FROM subcategories WHERE subCatName='$name'");
    $row2=mysqli_num_rows($sql);
    if( $row > 0 ) {
        return "<script>alert('Category with the same name: \"$name\" already exists.');</script>";
    } else if ( $row2 > 0 ) {
        return "<script>alert('SubCategory with the same name: \"$name\" already exists.');</script>";
    } else {
        //Check default or custom SEO url
        if ( $url == null ) {
            $url = seourl($name);
        } else {
            $url = seourl($url);
        }
        //Check if seo url is unique
        $sql=mysqli_query($con,"SELECT id FROM categories WHERE url='$url'");
        $row=mysqli_num_rows($sql);
        $sql=mysqli_query($con,"SELECT id FROM subcategories WHERE url='$url'");
        $row2=mysqli_num_rows($sql);
        if( $row > 0 ) {
            return "<script>alert('Category with the same SEO url: \"$url\" already exists.');</script>";
        } else if ( $row2 > 0 ) {
            return "<script>alert('SubCategory with the same SEO url: \"$url\" already exists.');</script>";
        } else {
            if ( $act == "addcat" ) { // Category
                $icon = mysqli_real_escape_string($con, $icon);
                $sql=mysqli_query($con, "select MAX(ix) from categories");
                $maxix=mysqli_fetch_array($sql);
                $maxix=$maxix[0] + 1;
                $msg=mysqli_query($con,"INSERT into categories(catName, vis, ix, url, icon) values('$name', '$vis', '$maxix', '$url', '$icon')");
                if ($msg) {
                    $lastId = mysqli_insert_id($con);
                    $lastId .= "c";
                    urlmap($url, $lastId, "add");
                    return "<script type='text/javascript'> document.location = 'categories.php'; </script>";
                } else {
                    return "<script>alert('There was an error.');</script>"; //stava za headers debug
                }
            } else if ( $act == "addsubcat" ) { // Subcategory
                $query=mysqli_query($con, "SELECT MAX(ix) from subcategories where categoryid = '$parent'");
                $ix=mysqli_fetch_array($query);
                $ix=$ix[0] + 1;
                $msg=mysqli_query($con,"INSERT into subcategories(subCatName, vis, categoryid, ix, url, aliExId) values('$name', '$vis', '$parent', '$ix', '$url', '$aliExId')");
                if ($msg) {
                    $lastId = mysqli_insert_id($con);
                    $lastId .= "s";
                    urlmap($url, $lastId, "add");
                    return "<script type='text/javascript'> document.location = 'categories.php'; </script>";
                } else {
                    return "<script>alert('There was an error.');</script>";
                }
            } else {
                return "<script>alert('There was an error.');</script>";
            }
        }
    }
}

//Function Editing Cat / Subcat
function editcatsubcat($act, $id, $name, $ix, $oix, $parent, $vis, $oparent, $url, $icon, $aliExId) {
    global $con;
    //Check if name is unique.
    $name = mysqli_real_escape_string($con, $name);
    $exclSameCat = "";
    $exclSameSubCat = "";
    if ( $act == "editcat" ) {
        $exclSameCat = " AND id != '$id'";
    } else if ( $act == "editsubcat" ) {
        $exclSameSubCat = " AND id != '$id'";
    }
    $sql=mysqli_query($con,"SELECT id FROM categories WHERE catName='$name'" . $exclSameCat);
    $row=mysqli_num_rows($sql);
    $sql=mysqli_query($con,"SELECT id FROM subcategories WHERE subCatName='$name'" . $exclSameSubCat);
    $row2=mysqli_num_rows($sql);
    if( $row > 0 ) {
        return "<script>alert('Category with the same name: \"$name\" already exists.');</script>";
    } else if ( $row2 > 0 ) {
        return "<script>alert('SubCategory with the same name: \"$name\" already exists.');</script>";
    } else {
        //Check default or custom SEO url
        if ( $url == null ) {
            $url = seourl($name);
        } else {
            $url = seourl($url);
        }
        //Check if seo url is unique
        $sql=mysqli_query($con,"SELECT id FROM categories WHERE url='$url'" . $exclSameCat);
        $row=mysqli_num_rows($sql);
        $sql=mysqli_query($con,"SELECT id FROM subcategories WHERE url='$url'" . $exclSameSubCat);
        $row2=mysqli_num_rows($sql);
        if( $row > 0 ) {
            return "<script>alert('Category with the same SEO url: \"$url\" already exists.');</script>";
        } else if ( $row2 > 0  ) {
            return "<script>alert('SubCategory with the same SEO url: \"$url\" already exists.');</script>";
        } else {
            //Check vis
            if ( $vis == "on" ) {
                $vis = "1";
            } else {
                $vis = "0";
            }
            //Category
            if ( $act == "editcat" ) { 
                $ovis = $_POST['ovis'];
                $icon = mysqli_real_escape_string($con, $icon);
                if ( $ovis == 1 && $vis == 0 ) {
                    mysqli_query($con,"UPDATE subcategories SET vis = '$vis' WHERE categoryid = '$id'");
                }
                //Reordering ix
                if ( $ix != 0 ) {
                    $start = min($ix, $oix);
                    $end = max($ix, $oix);
                    $query=mysqli_query($con,"SELECT id, ix from categories where ix between '$start' and '$end' and id != '$id' ORDER BY ix ASC"); //Take the IDs of categories to be moved
                    $msg=mysqli_query($con,"UPDATE categories set catName='$name', ix='$ix', vis='$vis', url='$url', icon='$icon' where id='$id'"); //Move our category to the new position and update all
                    while ($result=mysqli_fetch_array($query)) { //Move the categories
                        $rid=$result['id'];
                        if ( $ix > $oix ) {
                            $result2=mysqli_query($con,"UPDATE categories set ix = ix - 1 where id='$rid'");
                        } else if ( $oix > $ix ) {
                            $result2=mysqli_query($con,"UPDATE categories set ix = ix + 1 where id='$rid'");
                        }
                    }
                    if( $msg ) {
                        urlmap($url, $id . "c", "edit");
                        return "<script type='text/javascript'> document.location = 'categories.php'; </script>";
                    }
                } else {
                    $msg=mysqli_query($con,"UPDATE categories set catName='$name', vis='$vis', url='$url', icon='$icon' where id='$id'");
                    if( $msg ) {
                        urlmap($url, $id . "c", "edit");
                        return "<script type='text/javascript'> document.location = 'categories.php'; </script>";
                    }
                }
            //Subcategory
            } else if ( $act == "editsubcat" ) {
                $ovis = $_POST['ovis'];
                if ( $ovis == 0 && $vis == 1 ) {
                    if ( $parent == 0 ) { //Ako ne smenqme parent
                        mysqli_query($con,"UPDATE categories SET vis = '$vis' WHERE id = '$oparent'");
                    } else { //Ako smenqme parent
                        mysqli_query($con,"UPDATE categories SET vis = '$vis' WHERE id = '$parent'");
                    }
                } else if ( $ovis == 1 && $vis == 0 ) {
                    $query=mysqli_query($con, "SELECT id FROM subcategories WHERE categoryid = $oparent AND vis = 1 AND id !='$id'");
                    $row=mysqli_num_rows($query);
                    if( $row == 0 ) {
                        mysqli_query($con,"UPDATE categories SET vis = '0' WHERE id = '$oparent'");
                    }
                } else if ( $ovis == 1 && $vis == 1 && $parent != 0 ) {
                    mysqli_query($con,"UPDATE categories SET vis = '1' WHERE id = '$parent'");
                    $query=mysqli_query($con, "SELECT id FROM subcategories WHERE categoryid = $oparent AND vis = 1 AND id !='$id'");
                    $row=mysqli_num_rows($query);
                    if( $row == 0 ) {
                        mysqli_query($con,"UPDATE categories SET vis = '0' WHERE id = '$oparent'");
                    }
                }
                //If changing parent category
                if ( $parent != 0 ) {
                    $query=mysqli_query($con, "SELECT id, ix from subcategories where categoryid = $parent order by ix desc");
                    $result=mysqli_fetch_array($query);
                    $ix=$result['ix'] + 1;
                    $query2=mysqli_query($con, "SELECT id, ix from subcategories where categoryid = $oparent and ix > $oix");
                    while ($result=mysqli_fetch_array($query2)) { //Move the categories
                        $rid=$result['id'];
                        $result2=mysqli_query($con,"UPDATE subcategories set ix = ix - 1 where id='$rid'");
                    }
                    $msg=mysqli_query($con,"UPDATE subcategories set subCatName='$name', vis='$vis', categoryid='$parent', ix='$ix', url='$url', aliExId='$aliExId' where id='$id'");
                } else if ( $ix != 0 ) { //Reordering ix
                    $start = min($ix, $oix);
                    $end = max($ix, $oix);
                    $query=mysqli_query($con,"SELECT id, ix from subcategories where ix between '$start' and '$end' AND id != '$id' AND categoryid='$oparent' ORDER BY ix ASC"); //Take the IDs of subcategories to be moved
                    $msg=mysqli_query($con,"UPDATE subcategories set subCatName='$name', ix='$ix', vis='$vis', categoryid='$oparent', url='$url', aliExId='$aliExId' where id='$id'"); //Move our subcategory to the new position and update all
                    while ($result=mysqli_fetch_array($query)) { //Move the categories
                        $rid=$result['id'];
                        if ( $ix > $oix ) {
                            $result2=mysqli_query($con,"UPDATE subcategories set ix = ix - 1 where id='$rid'");
                        } else if ( $oix > $ix ) {
                            $result2=mysqli_query($con,"UPDATE subcategories set ix = ix + 1 where id='$rid'");
                        }
                    }
                } else {
                    $msg=mysqli_query($con,"UPDATE subcategories set subCatName='$name', vis='$vis', url='$url', aliExId='$aliExId' where id='$id'");
                }
                if( $msg ) {
                    urlmap($url, $id . "s", "edit");
                    return "<script type='text/javascript'> document.location = 'categories.php'; </script>";
                }
            } else {
                return "<script>alert('There was an error.');</script>";
            }
        }
    }
}

//Function Deleting cat / subcat
function delcatsubcat($act, $id) {
    global $con;
    if ( $act == "delcat") {
        //Move categories 
        $query=mysqli_query($con,"SELECT ix FROM categories WHERE id='$id'");
        $result=mysqli_fetch_array($query);
        if ( $result['ix'] != null ) { 
            $rix=$result['ix'];
            $query=mysqli_query($con,"SELECT ix, id FROM categories WHERE ix > '$rix' ORDER BY ix ASC"); //Take the IDs of categories to be moved
            while ($result=mysqli_fetch_array($query)) { //Move the categories
                $rid=$result['id'];
                $result2=mysqli_query($con,"UPDATE categories SET ix = ix - 1 where id='$rid'");
            }
        }
        //Associated subcategories to be deleted from urlmap
        $query=mysqli_query($con,"SELECT id FROM subcategories WHERE categoryid='$id'");
        while ($result=mysqli_fetch_array($query)) {
            urlmap(null, $result['id'] . "s", "del");
        }
        //Delete category and subcategories
        $msg=mysqli_multi_query($con,"DELETE from categories where id='$id'; delete from subcategories where categoryid='$id'");
        if( $msg ) {
            urlmap(null, $id . "c", "del");
            return "<script type='text/javascript'> document.location = 'categories.php'; </script>";
        }
    } else if ( $act == "delsubcat") {
        $query=mysqli_query($con,"select ix, categoryid from subcategories where id='$id'");
        $result=mysqli_fetch_array($query);
        if ( $result['ix'] != null ) { 
            $rix=$result['ix'];
            $catid=$result['categoryid'];
            $query=mysqli_query($con,"select id, ix from subcategories where ix > '$rix' and categoryid = '$catid' ORDER BY ix ASC"); //Take the IDs of subcategories to be moved
            while ($result=mysqli_fetch_array($query)) { //Move the categories
                $rid=$result['id'];
                $result2=mysqli_query($con,"UPDATE subcategories set ix = ix - 1 where id='$rid'");
            }
        }
        $msg=mysqli_query($con,"DELETE from subcategories where id='$id'");
        if( $msg ) {
            urlmap(null, $id . "s", "del");
            return "<script type='text/javascript'> document.location = 'categories.php'; </script>";
        }
    }
}

//Function edit urlmap.txt
function urlmap($url, $id, $action) {
    $contents = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/urlmap.txt");
    if ( $action == "add" ) {
        if ( strpos($contents, $url . " ") ) {
            $delString = explode($url . " ", $contents);
            $delString = explode("\n", $delString[1]);
            $delString = $url . " " . $delString[0];
            $contents = str_replace($delString, '', $contents);
        }
        $contents .= $url . " " . $id . "\n";
        $contents = str_replace("\n\n", "\n", $contents);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/urlmap.txt", $contents);
    } else if ( $action == "edit" ) {
        $id = " " . $id;
        $url = $url . $id;
        if ( strpos($contents, $id) ) {
            $editString = explode($id, $contents);
            if ( !strpos($editString[0], "\n") ) {
                $editString = $editString[0] . $id;
                $contents = str_replace($editString, $url, $contents);
            } else {
                $editString = explode("\n", $editString[0]);
                $editString = end($editString);
                $editString = $editString . $id;
                $contents = str_replace($editString, $url, $contents);
            }
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/urlmap.txt", $contents);
        }
    } else if ( $action == "del" ) {
        $id = " " . $id . "\n";
        $url = "";
        if ( strpos($contents, $id) ) {
            $editString = explode($id, $contents);
            if ( !strpos($editString[0], "\n") ) {
                $editString = $editString[0] . $id;
                $contents = str_replace($editString, $url, $contents);
            } else {
                $editString = explode("\n", $editString[0]);
                $editString = end($editString);
                $editString = $editString . $id;
                $contents = str_replace($editString, $url, $contents);
            }
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/urlmap.txt", $contents);
        }
    }

}

//Function for generating SEO Urls
function seourl($name) {
    $cyr = ['А','Б','В','Г','Д','Е','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ъ','Ю','Я','а','б','в','г','д','е','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ъ','ю','я'];
    $lat = ['A','B','V','G','D','E','ZH','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','H','C','CH','SH','SHT','Y','A','YU','YA','a','b','v','g','d','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','sht','y','a','yu','ya'];
    $name = str_replace($cyr, $lat, $name);
    $url = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
    return $url;
}


//Viewer

//Adding cat / subcat
if ( $act == "addcat" || $act == "addsubcat" ) {
    if ( $act == "addcat" ) {
        $cat = "Add Category";
        $breadcrumb = "Add Category";
    } else if ( $act == "addsubcat" ) {
        $cat = "Add SubCategory";
        $breadcrumb = "Add SubCategory";
        $aliExId = '';
        $parent = "";
        //Get all cats dropdown list
        $catList = "";
        $query=mysqli_query($con,"select id, catName from categories");
        while ($result=mysqli_fetch_array($query))
        {
            $catList .= "<option value=\"" . $result['id'] . "\">" . $result['catName'] . "</option>";
        }
    }
    $name = "";
    $icon = '';
    $vis = "0";
}

//EDIT CATEGORIES
if ( $act == "editcat") {
    $query=mysqli_query($con,"SELECT * FROM categories WHERE id='$catid'");
    $result=mysqli_fetch_array($query);
    $cat = "Edit Category: ";
    $name = htmlspecialchars($result['catName']);
    $icon = htmlspecialchars($result['icon']);
    $ix = $result['ix'];
    $vis = $result['vis'];
    $url = $result['url'];
    $isCustUrl = false;
    $seoUrl = seourl($result['catName']);
    if ($url != $seoUrl) {
        $isCustUrl = true;
    }
    $breadcrumb = "Edit Category";

    //Get ix dropdown list
    $query=mysqli_query($con,"SELECT id, ix, catName FROM categories ORDER BY ix ASC");
    $ixList = "";
    while ($result=mysqli_fetch_array($query))
    {
        if ( $result['ix'] == "1" && $ix == "1" ) {
            $ixList .= "<option value=\"0\" class=\"fw-bold\" selected>1. First (Present)</option>";
        } else if ( $result['ix'] == "1" && $ix != "2" ) {
            $ixList .= "<option value=\"1\">1. First</option><option value=\"2\">2. After: " . $result['catName'] . "</option>";
        } else if ( $result['ix'] == "1" && $ix == "2" ) {
            $ixList .= "<option value=\"1\">1. First</option><option value=\"0\" class=\"fw-bold\" selected>2. After: " . $result['catName'] . " (Present)</option>";
        } else if ( $result['ix'] == ($ix - 1) ) {
            $ixList .= "<option value=\"0\" class=\"fw-bold\" selected>" . ($result['ix'] + 1) . ". After: " . $result['catName'] . " (Present)</option>";
        }  else  if ( $result['ix'] == $ix ) {
            $ixList .= "";
        }  else  if ( $result['ix'] < $ix ) {
            $ixList .= "<option value=\"" . ($result['ix'] + 1) . "\">" . ($result['ix'] + 1) . ". After: " . $result['catName'] . "</option>";
        } else  if ( $result['ix'] > $ix ) {
            $ixList .= "<option value=\"" . $result['ix'] . "\">" . $result['ix'] . ". After: " . $result['catName'] . "</option>";
        }
    }
//EDIT SUBCATEGORIES
} else if ( $act == "editsubcat") {
    $query=mysqli_query($con,"select * from subcategories where id='$catid'");
    $result=mysqli_fetch_array($query);
    $cat = "Edit SubCategory: ";
    $name = htmlspecialchars($result['subCatName']);
    $ix = $result['ix'];
    $aliExId = $result['aliExId'];
    $vis = $result['vis'];
    $url = $result['url'];
    $isCustUrl = false;
    $seoUrl = seourl($result['subCatName']);
    if ($url != $seoUrl) {
        $isCustUrl = true;
    }
    $parent = $result['categoryid'];
    $breadcrumb = "Edit SubCategory";

    //Get all cats dropdown list
    $catList = "";
    $ixList = "";
    $query=mysqli_query($con,"select categoryid from subcategories where id='$catid'");
    $result=mysqli_fetch_array($query);
    $parentid = $result['categoryid'];
    $query=mysqli_query($con,"select id, catName from categories");
    while ($result=mysqli_fetch_array($query))
    {
        if ( $result['id'] == $parentid) {
            $catList .= "<option value=\"0\" class=\"fw-bold\" selected>" . $result['catName'] . " (Present)</option>";
        } else {
            $catList .= "<option value=\"" . $result['id'] . "\">" . $result['catName'] . "</option>";
        }
    }

    //Get ix dropdown
    $query=mysqli_query($con,"select id, ix, subCatName from subcategories where categoryid='$parentid' ORDER BY ix ASC");
    while ($result=mysqli_fetch_array($query))
    {
        if ( $result['ix'] == "1" && $ix == "1" ) {
            $ixList .= "<option value=\"0\" class=\"fw-bold\" selected>1. First (Present)</option>";
        } else if ( $result['ix'] == "1" && $ix != "2" ) {
            $ixList .= "<option value=\"1\">1. First</option><option value=\"2\">2. After: " . $result['subCatName'] . "</option>";
        } else if ( $result['ix'] == "1" && $ix == "2" ) {
            $ixList .= "<option value=\"1\">1. First</option><option value=\"0\" class=\"fw-bold\" selected>2. After: " . $result['subCatName'] . " (Present)</option>";
        } else if ( $result['ix'] == ($ix - 1) ) {
            $ixList .= "<option value=\"0\" class=\"fw-bold\" selected>" . ($result['ix'] + 1) . ". After: " . $result['subCatName'] . " (Present)</option>";
        }  else  if ( $result['ix'] == $ix ) {//After itself is no needed
            $ixList .= "";
        }  else  if ( $result['ix'] < $ix ) {
            $ixList .= "<option value=\"" . ($result['ix'] + 1) . "\">" . ($result['ix'] + 1) . ". After: " . $result['subCatName'] . "</option>";
        } else  if ( $result['ix'] > $ix ) {
            $ixList .= "<option value=\"" . $result['ix'] . "\">" . $result['ix'] . ". After: " . $result['subCatName'] . "</option>";
        }
    }
}
?>