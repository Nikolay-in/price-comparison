<?php session_start();
include_once('../includes/config.php');
if (!isset($_SESSION['adminid'])) {
  header('location:logout.php');
} else {
    include_once('./includes/editproducts.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title><?php if ($act == "add") { echo "Add"; } else if ($act == "edit") { echo "Edit"; } ?> Product</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" crossorigin="anonymous"/>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="../js/chart.min.js"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="js/priceHistory.js"></script>
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
                            <li class="breadcrumb-item"><a href="/admin/products.php">Manage Products</a></li>
                            <li class="breadcrumb-item active"><?php echo $breadcrumb;?></li>
                        </ol>
                        <div class="card col-12 mb-4 mt-4">
                            <form method="post" enctype="multipart/form-data">
                                <div class="card-body">
                                    <table class="table table-borderless table-hover">
                                    <tr>
                                        <th class="col-2 align-middle">Title:</th>
                                        <td><input class="form-control" id="name" name="name" type="text" value="<?php echo $name;?>" required /></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">SEO Url:</th>
                                        <td colspan="3" class="fw-bold">
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <input class="form-check-input mt-0" type="radio" name="urlRadio" value="default" aria-label="Radio button for following text input" <?php if ($act == "edit") { echo $isCustUrl == false ? 'checked' : ''; } else { echo 'checked'; }?> />
                                                </div>
                                                <input class="form-control" id="disabledDefault" name="disabledDefault" type="text" value="<?php if ( $act == "edit" ) { if ($isCustUrl == false) { echo $url; } else { echo $seoUrl; } } else { echo 'Default.'; } ?>" disabled />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">&nbsp;</th>
                                        <td>
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <input class="form-check-input mt-0" type="radio" name="urlRadio" value="custom" aria-label="Radio button for following text input" <?php if ($act == "edit") { echo $isCustUrl == true ? 'checked' : ''; } ?> />
                                                </div>
                                                <input class="form-control" id="customUrl" name="customUrl" type="text" placeholder="Custom" value="<?php if ( $act == "edit" && $isCustUrl == true) { echo $url; } ?>" />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Images:</th>
                                        <td>
                                            <div class="row">
                                                <?php if ( $act == "edit" ) { echo $imagesString; }?>
                                            </div>
                                            <div class="input-group mb-3">
                                                <input type="file" class="form-control" name="images[]" id="inputGroupFiles" multiple>
                                                <div class="row">
                                                    <div class="col">
                                                        <label class="input-group-text" for="inputGroupFiles">Upload</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Brand:</th>
                                        <td>
                                            <select class="form-select" id="brand" name="brand">
                                                <?php echo $brandList; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Model:</th>
                                        <td colspan="3"><input class="form-control" id="model" name="model" type="text" value="<?php echo $model;?>" required /></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">ModelStrip:</th>
                                        <td colspan="3" class="fw-bold"><?php echo $modelStrip;?></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Category / Subcategory:</th>
                                        <td colspan="3">
                                        <select class="form-select" id="category" name="category">
                                            <?php echo $catlist;?>
                                        </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">EAN:</th>
                                        <td colspan="3"><input class="form-control" id="ean" name="ean" type="number" value="<?php echo $ean;?>" /></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Description:</th>
                                        <td colspan="3"><textarea class="form-control" id="description" name="description" rows="9" required ><?php echo $description;?></textarea></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Active:</th>
                                        <td class="col-6" colspan="3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" name="active" id="switch" <?php if ( $active == "1" ) { echo "checked"; }?> /></div></td>
                                    </tr>
                                    <tr>
                                        <th class="col-2 align-middle">Date Added:</th>
                                        <td colspan="3"><?php echo $dateAdded;?></td>
                                   </tr>
                                   <tr>
                                        <th class="col-2 align-middle">Date Updated:</th>
                                        <td colspan="3"><?php echo $dateUpdated;?></td>
                                    </tr>
                                    <tr>
                                        <?php if ( $act == "add" ) { ?>
                                        <td colspan="4" style="text-align:center ;">
                                            <button type="submit" class="btn btn-success btn-block me-1" name="add">Add</button>
                                         <a class="btn btn-secondary me-1" href="products.php" role="button">Cancel</a>
                                        </td>
                                        <?php } else if ( $act == "edit" ) { ?>
                                        <td colspan="4" style="text-align:center ;">
                                            <button type="submit" class="btn btn-primary btn-block me-1" name="update">Update</button>
                                            <a class="btn btn-secondary me-1" href="products.php" role="button">Cancel</a>
                                            <a class="btn btn-danger" href="editproducts.php?act=del&id=<?php echo $id;?>" onClick="return confirm('Do you really want to delete?');" role="button">Delete</a>
                                        </td>
                                        <?php } ?>
                                   </tr>
                                    </tbody>
                                </table>
                            </div>
                            </form>
                        </div>
                        <?php if ( $act == "edit" ) { ?>
                        <div class="card my-4">
                            <div class="card-header">
                                <h5 class="m-1"><i class="fa fa-chart-line me-2"></i>Price history</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col">
                                        <div id="chart-container">
                                            <canvas id="priceHistory" height="90"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card my-4">
                            <div class="card-header fw-bold">Offers</div>
                            <div class="card-body">
                                <?php if ($offers) { ?>
                                <table class="table">
                                    <thead>
                                        <th class="col-1 text-center">Vendor</th>
                                        <th class="col-3 text-center">Title</th>
                                        <th class="col-1 text-center">Added</th>
                                        <th class="col-1 text-center">Approved</th>
                                        <th class="col-1 text-center">Updated</th>
                                        <th class="col-1 text-center">Last Alive</th>
                                        <th class="col-1 text-center">Price Updated</th>
                                        <th class="col-1 text-center">Price</th>
                                        <th class="col-1 text-center">Old Price</th>
                                        <th class="col-1 text-center">Actions</th>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($offers as $offer) { ?>
                                        <tr>
                                            <td><?php echo $offer['vendorName']; ?></td>
                                            <td><a href="<?php echo $offer['link']; ?>" target="_blank" class="fw-bold text-decoration-none"><?php echo $offer['title']; ?></a></td>
                                            <td><?php echo $offer['dateAdded']; ?></td>
                                            <td><?php echo $offer['dateApproved']; ?></td>
                                            <td><?php echo $offer['dateUpdated']; ?></td>
                                            <td><?php echo $offer['lastAlive']; ?></td>
                                            <td><?php echo $offer['priceUpdated']; ?></td>
                                            <td><?php echo $offer['price']; ?></td>
                                            <td><?php echo $offer['oldprice']; ?></td>
                                            <td>
                                                <?php if ($offer['active'] == 1) { ?>
                                                    <i class="fas fa-eye fa-lg text-success"></i>
                                                <?php } else { ?>
                                                    <i class="fas fa-eye-slash fa-lg text-danger"></i>
                                                <?php } ?>
                                                <a target="_blank" href="editoffers.php?act=edit&id=<?php echo $offer['id']; ?>"><i class="fas fa-edit fa-lg mx-1"></i></a>
                                                <a target="_blank" href="editoffers.php?act=del&id=<?php echo $offer['id']; ?>" onclick="return confirm('Do you really want to delete?');"><i class="fas fa-trash-alt fa-lg text-danger"></i></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <?php } else { ?>
                                <h5>No Offers.</h5>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <?php } ?>
                        <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <div class="modal-body">
                                    <img src="" class="imagepreview" style="width: 100%;">
                                    <button type="button" class="btn-close float-end bg-secondary position-absolute top-0 end-0 mt-1 me-1" aria-label="Close" onclick="$('#imagemodal').modal('hide');"></button>
                                </div>
                                </div>
                            </div>
                        </div>
            </main>
          <?php include('../includes/footer.php');?>
            </div>
        </div>
    </body>
</html>
<?php } ?>
