<?php 
session_start(); 
include_once('../includes/config.php');
if(isset($_POST['login']))
{
    $adminusername=mysqli_real_escape_string($con, $_POST['username']);
    $pass=md5(mysqli_real_escape_string($con, $_POST['password']));
    $ret=mysqli_query($con,"SELECT * FROM admin WHERE username='$adminusername' and password='$pass'");
    $num=mysqli_fetch_array($ret);
    if($num>0)
    {
        $_SESSION['login']=$_POST['username'];
        $_SESSION['adminid']=$num['id'];
        echo "<script>document.location='';</script>";
        exit();
    }
    else
    {
        echo "<script>alert('Invalid username or password');</script>";
        echo "<script>document.location='';</script>";
        exit();
    }
}

if (isset($_SESSION['adminid'])) {
    $title = "Admin Dashboard";

    //Products
    $query=mysqli_query($con,"select id from products where active=0");
    $inactiveproducts=mysqli_num_rows($query);

    $query1=mysqli_query($con,"select id from products where dateadded >= CURRENT_DATE()-1");
    $yesterdayproducts=mysqli_num_rows($query1);

    $query2=mysqli_query($con,"select id from products where date(dateadded)>=now() - INTERVAL 7 day");
    $last7daysproducts=mysqli_num_rows($query2);

    $query3=mysqli_query($con,"select id from products where date(dateadded)>=now() - INTERVAL 30 day");
    $last30daysproducts=mysqli_num_rows($query3);

    $query4=mysqli_query($con,"select id from products");
    $totalproducts=mysqli_num_rows($query4);

    //Offers
    $query5=mysqli_query($con,"select id from offers where active=0");
    $inactiveoffers=mysqli_num_rows($query5);

    $query6=mysqli_query($con,"select id from offers where dateApproved >= CURRENT_DATE()-1");
    $yesterdayoffers=mysqli_num_rows($query6);

    $query7=mysqli_query($con,"select id from offers where date(dateApproved)>=now() - INTERVAL 7 day");
    $last7daysoffers=mysqli_num_rows($query7);

    $query8=mysqli_query($con,"select id from offers where date(dateApproved)>=now() - INTERVAL 30 day");
    $last30daysoffers=mysqli_num_rows($query8);

    $query9=mysqli_query($con,"select id from offers");
    $totaloffers=mysqli_num_rows($query9);

    //Categories
    $query10=mysqli_query($con,"select id from vendorssubcats where assign = 0");
    $unassigned=mysqli_num_rows($query10);

    $query11=mysqli_query($con,"select id from categories");
    $sitecats=mysqli_num_rows($query11);
    $query12=mysqli_query($con,"select id from categories where vis = 1");
    $siteactivecats=mysqli_num_rows($query12);

    $query13=mysqli_query($con,"select id from subcategories");
    $sitesubcats=mysqli_num_rows($query13);
    $query14=mysqli_query($con,"select id from subcategories where vis = 1");
    $siteactivesubcats=mysqli_num_rows($query14);

    $query15=mysqli_query($con,"select id from vendorscats");
    $vendorcats=mysqli_num_rows($query15);
    $query16=mysqli_query($con,"select id from vendorscats where active = 1");
    $vendoractivecats=mysqli_num_rows($query16);

    $query17=mysqli_query($con,"select id from vendorssubcats");
    $vendorsubcats=mysqli_num_rows($query17);
    $query18=mysqli_query($con,"select id from vendorssubcats where active = 1");
    $vendoractivesubcats=mysqli_num_rows($query18);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Admin Dashboard</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" crossorigin="anonymous"/> -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.2/datatables.min.css" crossorigin="anonymous"/>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.2/datatables.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="js/chartLineNewTempProd.js"></script>
        <script type="text/javascript" src="js/chartLineProducts.js"></script>
        <script type="text/javascript" src="js/pieChartVendorsTempProd.js"></script>
        <script type="text/javascript" src="js/pieChartProducts.js"></script>
        <script type="text/javascript" src="js/chartLineOffers.js"></script>
        <script type="text/javascript" src="js/pieChartOffers.js"></script>
        <script type="text/javascript" src="js/pieChartVendorsOffers.js"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>
        <script type="text/javascript" src="../js/chart.min.js"></script>
    </head>
    <body class="sb-nav-fixed">
        <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
            <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="m-1"><i class="fa fa-hourglass-half me-2"></i>New and Temporary Products</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-10">
                                        <div id="chart-container">
                                            <canvas id="tempProducts" height="90"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                    <div id="chart-container">
                                            <canvas id="tempProductsVendors"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4"> 
                            <div class="card-header">
                                <h5 class="m-1"><i class="fas fa-list me-2"></i>Products</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-10">
                                        <div id="chart-container">
                                            <canvas id="products30days" height="90"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                    <div id="chart-container">
                                            <canvas id="activeProducts"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="card bg-secondary text-white">
                                            <div class="card-body"> Inactive: 
                                                <span style="font-size:22px;"> <?php echo $inactiveproducts;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="products.php?active=0">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body">Last 24h Added: 
                                                <span style="font-size:22px;"> <?php echo $yesterdayproducts;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="products.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-info text-white">
                                            <div class="card-body"> Last 7 days added: 
                                                <span style="font-size:22px;"> <?php echo $last7daysproducts;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="products.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body">Last 30 days added: 
                                                <span style="font-size:22px;"> <?php echo $last30daysproducts;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="products.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">Total: 
                                                <span style="font-size:22px;"> <?php echo $totalproducts;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="products.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div> 
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="m-1"><i class="fas fa-tags me-2"></i>Offers</h5>
                            </div>
                            <div class="card-body">
                            <div class="row mb-4">
                                    <div class="col-md-8">
                                        <div id="chart-container">
                                            <canvas id="offers30days" height="90"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div id="chart-container">
                                            <canvas id="activeOffers"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div id="chart-container">
                                            <canvas id="vendorsOffers"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="card bg-secondary text-white">
                                            <div class="card-body"> Inactive: 
                                                <span style="font-size:22px;"> <?php echo $inactiveoffers;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="offers.php?active=0">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body">Last 24h Added: 
                                                <span style="font-size:22px;"> <?php echo $yesterdayoffers;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="offers.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-info text-white">
                                            <div class="card-body"> Last 7 days added: 
                                                <span style="font-size:22px;"> <?php echo $last7daysoffers;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="offers.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body">Last 30 days added: 
                                                <span style="font-size:22px;"> <?php echo $last30daysoffers;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="offers.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">Total: 
                                                <span style="font-size:22px;"> <?php echo $totaloffers;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="offers.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="m-1"><i class="fas fa-th me-2"></i>Categories</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <div class="card bg-secondary text-white">
                                            <div class="card-body"> Unassigned SubCategories:<br />
                                                <span style="font-size:22px;"> <?php echo $unassigned;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="vendors.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body">Site Active Categories:<br />
                                                <span style="font-size:22px;"> <?php echo $siteactivecats . ' / ' . $sitecats;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="categories.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-info text-white">
                                            <div class="card-body"> Site Active SubCategories:<br />
                                            <span style="font-size:22px;"> <?php echo $siteactivesubcats . ' / ' . $sitesubcats;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="categories.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body">Vendors Active Categories:<br />
                                                <span style="font-size:22px;"> <?php echo $vendoractivecats . ' / ' . $vendorcats;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="vendors.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">Vendors Active SubCats:<br />
                                                <span style="font-size:22px;"> <?php echo $vendoractivesubcats . ' / ' . $vendorsubcats;?></span></div>
                                            <div class="card-footer d-flex align-items-center justify-content-between">
                                                <a class="small text-white stretched-link" href="vendors.php">View Details</a>
                                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div> 
                        </div>
                    </div>
                </main>
                <?php include_once('../includes/footer.php'); ?>
            </div>
        </div>
    </body>
</html>

<?php
} 
else
{
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Admin Login | BestPrices.bg</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">

<div class="card-header">
<h2 align="center">BestPrices.bg</h2>
<hr />
    <h3 class="text-center font-weight-light my-4">Admin Login</h3></div>
                                    <div class="card-body">
                                        
                                        <form method="post">
                                            
<div class="form-floating mb-3">
<input class="form-control" name="username" type="text" placeholder="Username"  required/>
<label for="inputEmail">Username</label>
</div>
                                            

<div class="form-floating mb-3">
<input class="form-control" name="password" type="password" placeholder="Password" required />
<label for="inputPassword">Password</label>
</div>


<div class="d-flex align-items-center justify-content-between mt-4 mb-0">
<a class="small" href="password-recovery.php">Forgot Password?</a>
<button class="btn btn-primary" name="login" type="submit">Login</button>
</div>
</form>
</div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="../index.php">Back to Home Page</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
<?php include('../includes/footer.php');?>
        </div>
    </body>
</html>
<?php } ?>