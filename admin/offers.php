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
        <title>Manage Offers</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" crossorigin="anonymous"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.2/datatables.min.css" crossorigin="anonymous"/>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.2/datatables.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="js/dataTables.rowsGroup.js"></script>
        <script type="text/javascript" src="js/getOffers.js"></script>
    </head>
    <body class="sb-nav-fixed">
      <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
         <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4 pb-4">
                        <h1 class="mt-4"><i class="fas fa-tags me-2"></i>Manage Offers</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/admin/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Manage Offers</li>
                        </ol>
                        <table id="datatables" class="table table-striped table-hover border-dark align-middle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>PID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Vendor</th>
                                    <th>Price</th>
                                    <th>OldPrice</th>
                                    <th>Added</th>
                                    <th>Price Updated</th>
                                    <th>Last Alive</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>PID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Vendor</th>
                                    <th>Price</th>
                                    <th>OldPrice</th>
                                    <th>Added</th>
                                    <th>Price Updated</th>
                                    <th>Last Alive</th>
                                    <th>Action</th>
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