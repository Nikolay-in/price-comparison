<?php session_start();
include_once('../includes/config.php');
if (!isset($_SESSION['adminid'])) {
    header('location:logout.php');
} else {
    include_once('./includes/editcat.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>
        <?php if ( $act == "addcat" ) {
            echo "Add Category";
        } else if ( $act == "addsubcat" ) {
            echo "Add SubCategory";
        } else if ( $act == "editcat" ) {
            echo "Edit Category";
        } else if ( $act == "editsubcat" ) {
            echo "Edit SubCategory";
        }
        ?>
        </title>
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
                        <h1 class="mt-4"><i class="fas fa-edit me-2"></i><?php echo "$cat $name";?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/admin/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/admin/categories.php">Manage Categories</a></li>
                            <li class="breadcrumb-item active"><?php echo $breadcrumb;?></li>
                        </ol>
                        <div class="card mb-4 mt-4">
                            <form method="post">
                                <div class="card-body">
                                    <table class="table table-borderless table-hover">
                                        <tbody>
                                            <tr>
                                                <th class="col-2 align-middle">Name:</th>
                                                <td><input class="form-control" id="name" name="name" type="text" value="<?php echo $name;?>" required /></td>
                                            </tr>
                                            <tr>
                                                <th class="col-2 align-middle">SEO Url:</th>
                                                <td colspan="3" class="fw-bold">
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <input class="form-check-input mt-0" type="radio" name="urlRadio" value="default" aria-label="Radio button for following text input" <?php if ($act == "editcat" || $act == "editsubcat") { echo $isCustUrl == false ? 'checked' : ''; } else { echo 'checked'; } ?> />
                                                        </div>
                                                        <input class="form-control" id="disabledDefault" name="disabledDefault" type="text" value="<?php if ($act == "editcat" || $act == "editsubcat") { if ($isCustUrl == false) { echo $url; } else { echo $seoUrl; } } else { echo 'Default.'; } ?>" disabled />
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="col-2 align-middle">&nbsp;</th>
                                                <td>
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <input class="form-check-input mt-0" type="radio" name="urlRadio" value="custom" aria-label="Radio button for following text input" <?php if ($act == "editcat" || $act == "editsubcat") { echo $isCustUrl == true ? 'checked' : ''; } ?> />
                                                        </div>
                                                        <input class="form-control" id="customUrl" name="customUrl" type="text" placeholder="Custom" value="<?php if ($act == "editcat" || $act == "editsubcat") { echo $isCustUrl == true ? $url : ''; } ?>" />
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php if ($act == 'addcat' || $act == 'editcat') { ?>
                                            <tr>
                                                <th class="col-2 align-middle">Icon:</th>
                                                <td><input class="form-control" id="icon" name="icon" type="text" value="<?php echo $icon;?>" /></td>
                                            </tr>
                                            <?php } ?>
                                            <?php if ( $act == "addsubcat" || $act == "editsubcat" ) { ?>
                                            <tr>
                                                <th class="col-2 align-middle">Parent Category:</th>
                                                <td>
                                                    <select class="form-select" id="parent" name="parent">
                                                        <?php echo $catList; ?>
                                                    </select>
                                                 </td>

                                            </tr>
                                            <?php } ?>
                                            <?php if ( $act == "editcat" || $act == "editsubcat" ) { ?>
                                            <tr>
                                                <th class="col-2 align-middle">Position:</th>
                                                <td>
                                                    <select class="form-select" id="fix" name="fix">
                                                        <?php echo $ixList; ?>
                                                    </select>
                                                    <input type="hidden" id="ofix" name="ofix" value="<?php echo $ix; ?>">
                                                    <?php if ( $act == "editsubcat" ) { ?>
                                                    <input type="hidden" id="oparent" name="oparent" value="<?php echo $parentid; ?>">
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <?php if ($act == "editsubcat" || $act == "addsubcat") { ?>
                                            <tr>
                                                <th class="col-2 align-middle">Aliexpress category Id:</th>
                                                <td><input class="form-control" id="aliExId" name="aliExId" type="text" value="<?php echo $aliExId;?>" /></td>
                                            </tr>
                                            <?php } ?>
                                            <tr>
                                                <th class="col-2 align-middle">Visible:</th>
                                                <td colspan="3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" name="vis" id="switch" <?php if ( $vis == "1" ) { echo "checked"; }?> /></div></td>
                                                <input type="hidden" id="ovis" name="ovis" value="<?php echo $vis; ?>">
                                            </tr>
                                            <tr>
                                                <?php if ( $act == "addcat" || $act == "addsubcat" ) { ?>
                                                <td colspan="4" style="text-align:center ;"><button type="submit" class="btn btn-success btn-block me-1" name="add">Add</button>
                                                <a class="btn btn-secondary me-1" href="categories.php" role="button">Cancel</a>
                                                <?php } else if ( $act == "editcat" ) { ?>
                                                <td colspan="4" style="text-align:center ;"><button type="submit" class="btn btn-primary btn-block me-1" name="update">Update</button>
                                                <a class="btn btn-secondary me-1" href="categories.php" role="button">Cancel</a>
                                                <a class="btn btn-danger" href="editcategory.php?act=delcat&id=<?php echo $catid;?>" onClick="return confirm('Do you really want to delete?');" role="button">Delete</a></td>
                                                <?php } else if ( $act == "editsubcat" ) { ?>
                                                <td colspan="4" style="text-align:center ;"><button type="submit" class="btn btn-primary btn-block me-1" name="update">Update</button>
                                                <a class="btn btn-secondary me-1" href="categories.php" role="button">Cancel</a>
                                                <a class="btn btn-danger" href="editcategory.php?act=delsubcat&id=<?php echo $catid;?>" onClick="return confirm('Do you really want to delete?');" role="button">Delete</a></td>
                                                <?php }?>
                                                
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                </main>
          <?php include('../includes/footer.php');?>
            </div>
        </div>
    </body>
</html>
<?php } ?>
