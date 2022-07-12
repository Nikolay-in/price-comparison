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
        <title>Manage Categories</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" crossorigin="anonymous"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.2/datatables.min.css" crossorigin="anonymous"/>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.2/datatables.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="../js/all.min.js"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>
        <script type="text/javascript" src="js/dataTables.rowsGroup.js"></script>
        <script type="text/javascript" src="js/getCategories.js"></script>

    </head>
    <body class="sb-nav-fixed">
      <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
         <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4 pb-4">
                        <h1 class="mt-4"><i class="fas fa-th me-2"></i>Manage Categories</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/admin/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage Categories</li>
                        </ol>
                                <table id="datatables" class="table table-striped table-hover border-dark align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID.</th>
                                            <th>#</th>
                                            <th>Category</th>
                                            <th>Edit</th>
                                            <th>Products</th>
                                            <th>Offers</th>
                                            <th>ID.</th>
                                            <th>#</th>
                                            <th class="col-2">Subcategory</th>
                                            <th>Products</th>
                                            <th>Offers</th>
                                            <th>Edit</th>
                                            <th>CatUrl</th>
                                            <th>SubCatUrl</th>
                                            <th>CatVis</th>
                                            <th>SubCatVis</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ID.</th>
                                            <th>#</th>
                                            <th>Category</th>
                                            <th>Edit</th>
                                            <th>Products</th>
                                            <th>Offers</th>
                                            <th>ID.</th>
                                            <th>#</th>
                                            <th>Subcategory</th>
                                            <th>Products</th>
                                            <th>Offers</th>
                                            <th>Edit</th>
                                            <th>CatUrl</th>
                                            <th>SubCatUrl</th>
                                            <th>CatVis</th>
                                            <th>SubCatVis</th>
                                        </tr>
                                    </tfoot>
                                </table>
                    </div>
                </main>
  <?php include('../includes/footer.php');?>
            </div>
        </div>
    </body>
</html>
<?php } ?>