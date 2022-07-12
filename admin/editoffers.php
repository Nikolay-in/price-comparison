<?php session_start();
include_once('../includes/config.php');
if (!isset($_SESSION['adminid'])) {
  header('location:logout.php');
} else {
    include_once('./includes/editoffers.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title><?php if ($act == "add") { echo "Add"; } else if ($act == "edit") { echo "Edit"; } ?> Offers</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" crossorigin="anonymous"/>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>
    </head>
    <body class="sb-nav-fixed">
      <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
          <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"><i class="fas fa-list me-2"></i><?php echo "$breadcrumb";?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/admin/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/admin/offers.php">Manage Offers</a></li>
                            <li class="breadcrumb-item active"><?php echo $breadcrumb;?></li>
                        </ol>
                        <div class="card mb-4 mt-4">
                            <form method="post">
                                <div class="card-body">
                                    <table class="table table-borderless table-hover">
                                    <tr>
                                        <th class="col-2 align-middle">Product ID:</th>
                                        <td><input class="form-control" id="productid" name="productID" type="text" value="<?php echo $productId;?>" required /></td>
                                    </tr>
                                    <?php if ( $act == "edit" ) {?>
                                    <tr>
                                        <th class="col-2 align-middle">Product:</th>
                                        <td class="fw-bold"><span class="text-hover-image"><i class="fas fa-camera fa-lg text-primary me-2"></i><img style="display:none;" alt="<?php echo $productImage; ?>" /></span><a href="editproducts.php?act=edit&id=<?php echo $productId; ?>" target="_blank"><i class="fas fa-edit fa-lg mx-2"></i></a><a href="<?php echo SITE_WEBROOT . "$subCatUrl/$productUrl"; ?>" target="_blank" style="text-decoration: none;"><i class="fas fa-external-link-alt fa-lg mx-2"></i>&nbsp;<span class="fs-5"><?php echo $productName;?></span></a></td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <th class="col-2 align-middle">Vendor Title:</th>
                                        <td><input class="form-control" id="title" name="title" type="text" value="<?php echo htmlspecialchars($title);?>" required /></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Vendor:</th>
                                        <td colspan="3">
                                        <select class="form-select" id="vendor" name="vendor">
                                            <?php echo $vendorList;?>
                                        </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Vendor Product Code:</th>
                                        <td colspan="3"><input class="form-control" id="title" name="vendorCode" type="text" value="<?php echo $vendorCode;?>" /></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Price:</th>
                                        <td colspan="3"><input class="form-control" id="price" name="price" type="number" step=".01" value="<?php echo $price;?>" required /></td>
                                    </tr>
                                    <?php if ( $act == "edit" ) {?>
                                    <tr>
                                        <th class="col-2 align-middle">Old Price:</th>
                                        <td colspan="3"><?php echo $oldprice;?></td>
                                    </tr>
                                    <?php } ?>
                                    <tr>
                                        <th class="col-2 align-middle">Link:</th>
                                        <td colspan="3"><input class="form-control" id="link" name="link" type="text" value="<?php echo $link;?>" required /></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Active:</th>
                                        <td class="col-6" colspan="3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" name="active" id="switch" <?php if ( $active == "1" ) { echo "checked"; }?> /></div></td>
                                    </tr>
                                    <?php if ( $act == "edit" ) { ?>
                                    <tr>
                                        <th class="col-2 align-middle">Last Alive:</th>
                                        <td colspan="3"><?php echo $lastAlive;?></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Price Updated:</th>
                                        <td colspan="3"><?php echo ($priceUpdated) ? $priceUpdated : 'Never';?></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Date Updated:</th>
                                        <td colspan="3"><?php echo $dateUpdated;?></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Date Added:</th>
                                        <td colspan="3"><?php echo $dateAdded;?></td>
                                   </tr>
                                   
                                    
                                    <?php } ?>
                                    <tr>
                                        <?php if ( $act == "add" ) { ?>
                                        <td colspan="4" style="text-align:center ;">
                                            <button type="submit" class="btn btn-success btn-block me-1" name="add">Add</button>
                                         <a class="btn btn-secondary me-1" href="offers.php" role="button">Cancel</a>
                                        </td>
                                        <?php } else if ( $act == "edit" ) { ?>
                                        <td colspan="4" style="text-align:center ;">
                                            <button type="submit" class="btn btn-primary btn-block me-1" name="update">Update</button>
                                            <a class="btn btn-secondary me-1" href="offers.php" role="button">Cancel</a>
                                            <a class="btn btn-danger" href="editoffers.php?act=del&id=<?php echo $id;?>" onClick="return confirm('Do you really want to delete?');" role="button">Delete</a>
                                        </td>
                                        <?php } ?>
                                   </tr>
                                    </tbody>
                                </table>
                            </div>
                            </form>
                        </div>
            </main>
          <?php include('../includes/footer.php');?>
            </div>
        </div>
    </body>
</html>
<?php } ?>
