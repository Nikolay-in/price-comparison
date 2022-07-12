<?php session_start();
include_once('../includes/config.php');
if (!isset($_SESSION['adminid'])) {
    header('location:logout.php');
} else {
    include_once('./includes/crawler.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Crawler</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" crossorigin="anonymous"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.2/datatables.min.css" crossorigin="anonymous"/>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.2/datatables.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="../js/chart.min.js"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>
        <script type="text/javascript" src="js/vendorsBars.js"></script>
    </head>
    <body class="sb-nav-fixed">
      <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
         <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"><i class="fas fa-spider me-2"></i>Crawler</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/admin/">Dashboard</a></li>
                            <li class="breadcrumb-item active">Crawler</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="m-1"><i class="fas fa-cog me-2"></i>Settings</h5>
                            </div>
                            <form method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <table class="table table-borderless table-hover">
                                    <tbody>
                                        <tr>
                                            <th class="col-4 align-middle">Status:</th>
                                            <td class="col-8">
                                                    <?php if ( $status == "1") { ?>
                                                    <label class="form-check-label text-success fw-bold opacity-75" for="onoff">On: Stand-by</label>
                                                    <?php } else if ( $status == "2" ) {?>
                                                    <label class="form-check-label text-success fw-bold " for="onoff">On: Working</label>
                                                    <?php } else if ( $status == "0" ) {?>
                                                    <label class="form-check-label text-danger fw-bold" for="onoff">Off</label>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="col-4 align-middle">On / Off:</th>
                                            <td class="col-8">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="onoff" name="onoff" <?php if ( $onOff == "1" ) { ?>checked<?php }?> >
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="col-4 align-middle">Last run:</th>
                                            <td class="col-8">
                                                    <label class="form-check-label fw-bold"><?php echo $lastRun; ?></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="col-4 align-middle">Working hours (From-To):</th>
                                            <td class="col-8">
                                                <div class="col-2 mb-1">
                                                <select class="form-select form-select-sm" id="hourFrom" name="hourFrom">
                                                    <?php foreach ($allHours as $hour) {
                                                            if ($hour == $workFrom) {
                                                                echo "<option value=\"$hour\" selected>$hour</option>";
                                                            } else {
                                                                echo "<option value=\"$hour\">$hour</option>";
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                                </div>
                                                <div class="col-2">
                                                <select class="form-select form-select-sm" id="hourTo" name="hourTo">
                                                    <?php foreach ($allHours as $hour) {
                                                            if ($hour == $workTo) {
                                                                echo "<option value=\"$hour\" selected>$hour</option>";
                                                            } else {
                                                                echo "<option value=\"$hour\">$hour</option>";
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            </td>
                                        <tr>
                                            <th class="col-4 align-middle">How often to scan vendors categories (Days):</th>
                                            <td class="col-8">
                                                <div class="col-2">
                                                    <input type="number" class="form-control form-control-sm" id="vendorCatFreq" name="vendorCatFreq" value="<?php echo $catFreq; ?>">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="col-4 align-middle">How often to scan products (Days):</th>
                                            <td class="col-8">
                                                <div class="col-2">
                                                    <input type="number" class="form-control form-control-sm" id="vendorProdFreq" name="vendorProdFreq" value="<?php echo $prodFreq; ?>">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="col-4 align-middle">Sec / Page:</th>
                                            <td class="col-8">
                                                <div class="col-2">
                                                    <input type="number" class="form-control form-control-sm" id="secPerPages" name="secPerPages" value="<?php echo $secPage; ?>">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="col-4 align-middle">User-agent:</th>
                                            <td class="col-8">
                                                <select class="form-select form-select-sm" aria-label=".form-select-sm example" id="userAgent" name="userAgent">
                                                    <?php 
                                                        $uaListString = "";
                                                        foreach ($uaList as $key => $value) {
                                                            $selected = "";
                                                            $selectedRandom = "";
                                                            if ( ($key + 1) == $uaSet) {
                                                                $selected = "selected";
                                                            } else if ( $uaSet == "0" ) {
                                                                $selectedRandom = "selected";
                                                            }
                                                            $uaListString .= "<option value=\"" . ($key + 1) . "\" $selected>$value</option>";
                                                        }
                                                        echo "<option value=\"0\" $selectedRandom>Random for each session</option>" . $uaListString;
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="col-4 align-middle">User agents list:</th>
                                            <td class="col-8">
                                                <textarea class="form-control" id="userAgents" name="userAgents" rows="6"><?php echo $uaListText; ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="col-4 align-middle">Delete dead temp products after (Days) (0 - off):</th>
                                            <td class="col-8">
                                                <div class="col-2">
                                                    <input type="number" class="form-control form-control-sm" id="delTempDays" name="delTempDays" value="<?php echo $delTempDays; ?>">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="col-4 align-middle">Deactivate dead offers after (Days) (0 - off):</th>
                                            <td class="col-8">
                                                <div class="col-2">
                                                    <input type="number" class="form-control form-control-sm" id="deactiveOffersDays" name="deactiveOffersDays" value="<?php echo $deactiveOffersDays; ?>">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="col-4 align-middle">Delete inactive offers after (Days) (0 - off):</th>
                                            <td class="col-8">
                                                <div class="col-2">
                                                    <input type="number" class="form-control form-control-sm" id="delOffersDays" name="delOffersDays" value="<?php echo $delOffersDays; ?>">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="text-align:center ;">
                                                <button type="submit" class="btn btn-success btn-block me-1" name="submitSettings">Submit Changes</button>
                                                <a class="btn btn-secondary me-1" href="crawler.php" role="button">Cancel</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            </form>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                            <i class="fas fa-user-tie me-2"></i>Vendors
                            </div>
                            <form method="post" enctype="multipart/form-data">
                                <div class="card-body">
                                    <table class="table table-hover text-center">
                                        <thead>
                                            <tr>
                                                <th class="col-3">Vendor</th>
                                                <th class="col-1">Crawl</th>
                                                <th class="col-1">Catdir</th>
                                                <th class="col-1">Crawler Name</th>
                                                <th class="col-1">Offers (Active/Total)</th>
                                                <th class="col-4">SubCategories</th>
                                                <th class="col-1">Edit Vendor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php echo $vendors; ?>
                                            <tr>
                                                <td colspan="8" style="text-align:center ;">
                                                    <button type="submit" class="btn btn-success btn-block me-1" name="submitVendors">Submit Changes to Vendors</button>
                                                    <a class="btn btn-secondary me-1" href="crawler.php" role="button">Cancel</a>
                                                </td>
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