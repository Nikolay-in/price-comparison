<?php session_start();
include_once('../includes/config.php');
if (!isset($_SESSION['adminid'])) {
  header('location:logout.php');
} else {
    include_once('./includes/editbrands.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title><?php if ( $act == "add" ) { echo "Add Brand"; } else if ( $act == "edit" ) { echo "Edit Brand"; }?></title>
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
                        <h1 class="mt-4"><i class="fa fa-copyright me-2"></i><?php echo "$breadcrumb";?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/admin/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/admin/brands.php">Manage Brands</a></li>
                            <li class="breadcrumb-item active"><?php echo $breadcrumb;?></li>
                        </ol>
                        <div class="card mb-4 mt-4">
                            <form method="post">
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <th>Name:</th>
                                                <td><input class="form-control" id="name" name="name" type="text" value="<?php echo $name;?>" required /></td>
                                            </tr>
                                                <?php if ( $act == "add" ) { ?>
                                                <td colspan="4" style="text-align:center ;"><button type="submit" class="btn btn-success btn-block me-1" name="add">Add</button>
                                                <a class="btn btn-secondary me-1" href="#" onClick="window.history.back()" role="button">Cancel</a></td>
                                                <?php } else if ( $act == "edit" ) { ?>
                                                <td colspan="4" style="text-align:center ;"><button type="submit" class="btn btn-primary btn-block me-1" name="update">Update</button>
                                                <a class="btn btn-secondary me-1" href="#" onClick="window.history.back()" role="button">Cancel</a>
                                                <a class="btn btn-danger" href="editbrands.php?act=del&id=<?php echo $id;?>" onClick="return confirm('Do you really want to delete');" role="button">Delete</a></td>
                                                <?php } ?>
                                                
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
