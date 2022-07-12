<?php
//Main submit page
if (isset($_POST['submit']) && isset($_POST['models'])) {
    $caseModelArr = [];
    $caseModelStripArr = [];
    $idsArr = [];
    foreach ($_POST['models'] as $id => $model) {
        if ($model) {
            $model = strtoupper(preg_replace('/[ |-]+/', '-', $model));
            $modelStrip = preg_replace('/[^A-Z0-9+]+/', '', strtoupper($model));
            $caseStringModel = 'when id = \'' . $id . '\' then \'' . $model . '\'';
            $caseStringModelStrip = 'when id = \'' . $id . '\' then \'' . $modelStrip . '\''; 
            array_push($caseModelArr, $caseStringModel);
            array_push($caseModelStripArr, $caseStringModelStrip);
            array_push($idsArr, $id);
        }
    }
    if (count($idsArr) == 0) {
        echo '<script>alert(\'No changes submitted\')</script>';
    } else {
        $casesModelStr = implode(' ', $caseModelArr);
        $casesModelStripStr = implode(' ', $caseModelStripArr);
        $idsString = implode(', ', $idsArr);
        $query = mysqli_query($con, "UPDATE tempproducts SET model = (case $casesModelStr end), modelStrip = (case $casesModelStripStr end) WHERE id IN ( $idsString );");
        if (!$query) {
            echo '<script>alert(\'Error!\')</script>';
        }
    }
}

//Submit check list form
if (isset($_POST['submitCheckList']) && isset($_POST['clModelApply']) && isset($_POST['ids'])) {
    $model = strtoupper(preg_replace('/[ |-]+/', '-', $_POST['clModelApply']));
    $modelStrip = strtoupper(preg_replace('/[ |-]+/', '', $_POST['clModelApply']));
    $ids = implode(', ', $_POST['ids']);
    $query = mysqli_query($con, "UPDATE tempproducts SET model = '$model', modelStrip = '$modelStrip' WHERE id IN ( $ids );");
    if (!$query) {
        $error = mysqli_error($con);
        echo "<script>alert('$error');</script>";
    }
}

if (isset($_GET['activate'])) {
    $id = $_GET['activate'];
    $query = mysqli_query($con, "UPDATE tempproducts SET active = 1 WHERE id = $id");
    if ($query) {
        echo "<script type='text/javascript'> history.back(); </script>";
    } else {
        $error = mysqli_error($con);
        echo "<script>alert('$error');</script>";
    }
}

if (isset($_GET['deactivate'])) {
    $id = $_GET['deactivate'];
    $query = mysqli_query($con, "UPDATE tempproducts SET active = 0 WHERE id = $id");
    if ($query) {
        echo "<script type='text/javascript'> window.location = 'newtempproducts.php'; </script>";
    } else {
        $error = mysqli_error($con);
        echo "<script>alert('$error');</script>";
    }
}

//Navigation and sorting
$limit = isset($_GET['l']) ? $_GET['l'] : 20;
$l = '&l=' . $limit;

$cat = isset($_GET['c']) ? $_GET['c'] : 'a.subCatID';
$c = is_numeric($cat) ? '&c=' . $cat : '';

$vendor = isset($_GET['v']) ? $_GET['v'] : 'a.vendorId';
$v = is_numeric($vendor) ? '&v=' . $vendor : '';

$brand = isset($_GET['b']) ? $_GET['b'] : 'a.brandID';
$b = is_numeric($brand) ? '&b=' . $brand : '';

$sorts = [['&s=dateadded' , 'Date Added'], ['&s=lastAlive', 'Last Alive']];
$sortBy = isset($_GET['s']) ? $_GET['s'] : 'dateadded';
$s = isset($sortBy) ? '&s=' . $sortBy : '';

$active = isset($_GET['a']) ? 0 : 1;
$a = ($active == 0) ? '&a=' . $active : '';

$sortTitle = '';
foreach($sorts as $sort) {
    if ($sort[0] == $s) {
        $sortTitle = $sort[1];
    }
}

$linkAdd = $l . $c . $v . $b . $s . $a;

$query = mysqli_query($con, "SELECT count( if (modelStrip IS NULL, 1, NULL)) AS unlabeled, count(*) AS total FROM tempproducts a WHERE a.subCatID = $cat AND a.vendorId = $vendor AND a.brandID = $brand AND a.active = $active;");
$result = mysqli_fetch_array($query);
$unlabeled = $result['unlabeled'];
$total = $result['total'];

$pages = ceil($unlabeled / $limit);
$page = (isset($_GET['p']) && $_GET['p'] > 0 && $_GET['p'] <= $pages) ? $_GET['p'] : 1;
$offset = ($page - 1) * $limit;

$query = mysqli_query($con, "SELECT b.id, b.subCatName, count(*) count FROM tempproducts a LEFT JOIN subcategories b ON a.subCatID = b.id WHERE modelStrip IS NULL AND a.vendorId = $vendor AND a.brandID = $brand AND a.active = $active GROUP BY a.subCatID;");
$categories = mysqli_fetch_all($query, MYSQLI_ASSOC);

$query = mysqli_query($con, "SELECT b.id, b.name, count(*) count FROM tempproducts a LEFT JOIN vendors b ON a.vendorId = b.id WHERE modelStrip IS NULL AND  a.subCatID = $cat AND a.brandID = $brand AND a.active = $active GROUP BY a.vendorId;");
$vendors = mysqli_fetch_all($query, MYSQLI_ASSOC);

$query = mysqli_query($con, "SELECT b.id, b.brandName, count(*) count FROM tempproducts a LEFT JOIN brand b ON a.brandID = b.id WHERE modelStrip IS NULL AND  a.subCatID = $cat AND a.vendorId = $vendor AND a.active = $active GROUP BY a.brandID;");
$brands = mysqli_fetch_all($query, MYSQLI_ASSOC);

//Get products result
$query = mysqli_query($con, "SELECT a.id, a.name, a.image, a.model, a.brandID, b.brandName, d.catName catName, c.subCatName subCatName, a.description, a.url, e.name vendorName, a.subCatID, a.price, a.dateadded, a.lastAlive, a.active FROM tempproducts a LEFT JOIN brand b ON a.brandID = b.id LEFT JOIN subcategories c ON a.subCatID = c.id LEFT JOIN categories d ON c.categoryid = d.id LEFT JOIN vendors e ON a.vendorId = e.id WHERE modelStrip IS NULL AND a.subCatID = $cat AND a.vendorId = $vendor AND a.brandID = $brand AND a.active = $active ORDER BY $sortBy DESC LIMIT $limit OFFSET $offset;");
$result = mysqli_fetch_all($query, MYSQLI_ASSOC);

?>