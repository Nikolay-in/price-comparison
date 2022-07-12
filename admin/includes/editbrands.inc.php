<?php
//Code for adding
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $sql=mysqli_query($con,"select id from brand where brandName='$name'");
    $row=mysqli_num_rows($sql);
    if( $row > 0 ) {
        echo "<script>alert('Vendor already exists.');</script>";
    } else {
        $query=mysqli_query($con,"INSERT into brand(brandName) values('$name')");
        if ($query) {
            echo "<script type='text/javascript'> document.location = 'brands.php'; </script>";
        }
    }
//Code for updating
} else if (isset($_POST['update'])) {
    $id = $_GET['id'];
    $name = $_POST['name'];
    $query=mysqli_query($con,"UPDATE brand set brandName = '$name' where id = '$id'");
    if ($query) {
        echo "<script type='text/javascript'> document.location = 'brands.php'; </script>";
    }
}
//Code for deleting
if (isset($_GET['id']) && $_GET['act'] == "del") {
    $id=$_GET['id'];
    $name = "";
    $breadcrumb = "";
    $act = "";
    $msg=mysqli_query($con,"delete from brand where id='$id'");
    if ($msg) {
        echo "<script type='text/javascript'> document.location = 'brands.php'; </script>";
    }
}

//Editor
if ( isset($_GET['act']) && $_GET['act'] == "add" ) {
    $act = $_GET['act'];
    $name = "";
    $breadcrumb = "Add Brand";
}
if ( isset($_GET['act']) && $_GET['act'] == "edit" && isset($_GET['id'])) {
    $act = $_GET['act'];
    $id = $_GET['id'];
    $query=mysqli_query($con,"select * from brand where id='$id'");
    $result=mysqli_fetch_array($query);
    $name = $result['brandName'];
    $breadcrumb = "Edit Brand";
}
?>