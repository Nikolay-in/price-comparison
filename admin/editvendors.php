<?php session_start();
include_once('../includes/config.php');
if (!isset($_SESSION['adminid'])) {
  header('location:logout.php');
} else {
    include_once('./includes/editvendors.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title><?php if ( $act == "add" ) { echo "Add Vendor"; } else if ( $act == "edit" ) { echo "Edit Vendor"; }?></title>
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
                        <h1 class="mt-4"><i class="fas fa-user-tie me-2"></i><?php echo "$breadcrumb";?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/admin/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/admin/vendors.php">Manage Vendors</a></li>
                            <li class="breadcrumb-item active"><?php echo $breadcrumb;?></li>
                        </ol>
                        <div class="card mb-4 mt-4">
                            <form method="post" enctype="multipart/form-data" id="vendorForm">
                                <div class="card-body">
                                    <table class="table table-borderless table-hover">
                                        <tbody>
                                            <tr>
                                                <th class="col-6 align-middle">Name:</th>
                                                <td class="col-6"><input class="form-control" id="name" name="name" type="text" value="<?php echo $name;?>" required /></td>
                                            </tr>
                                            <tr>
                                                <th class="col-6 align-middle">Logo:</th>
                                                <td>
                                                    <?php if ( $act == "edit" ) { ?>
                                                    <div class="row">
                                                        <div class="col-6 float-start position-relative">
                                                            <?php if ( $logo != "no-logo.jpg" ) { ?>
                                                            <a href="editvendors.php?act=edit&id=<?php echo $id; ?>&delpic=1" class="position-absolute rounded top-0 end-0 me-3  ps-1 pe-1 bg-light" onClick=\"return confirm('Do you really want to delete this picture?');\"><i class="fa fa-trash-alt text-danger" aria-hidden="true"></i></a>
                                                            <?php } ?>
                                                            <a href="#" class="pop"><img src="<?php echo SITE_LOGOS . $logo; ?>" class="img-thumbnail rounded mb-2" alt="<?php echo $name;?> logo"></a>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    <div class="input-group">
                                                        <input type="file" class="form-control" name="image" id="inputlogo">
                                                        <div class="row">
                                                            <div class="col">
                                                                <label class="input-group-text" for="inputlogo">Change Logo</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="col-6 align-middle">Website:</th>
                                                <td class="col-6"><input class="form-control" id="website" name="website" type="text" value="<?php echo $website;?>" required /></td>
                                            </tr>
                                            <tr>
                                                <th class="col-6 align-middle">Description:</th>
                                                <td class="col-6"><input class="form-control" id="description" name="description" type="text" value="<?php echo $description;?>" required /></td>
                                            </tr>
                                            <tr>
                                                <th class="col-6 align-middle">Categories Directory:</th>
                                                <td class="col-6"><input class="form-control" id="catdir" name="catdir" type="text" value="<?php echo $catdir;?>" required /></td>
                                            </tr>
                                            <tr>
                                                <th class="col-6 align-middle">Crawler name:</th>
                                                <td class="col-6"><input class="form-control" id="crawlName" name="crawlName" type="text" value="<?php echo $crawlName;?>" /></td>
                                            </tr>
                                            <tr>
                                                <th class="col-6 align-middle">Visible:</th>
                                                <td class="col-6" colspan="3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" name="vis" id="switch" <?php if ( $vis == "1" ) { echo "checked"; }?> /></div></td>
                                            </tr>
                                            <?php if ( $act == "add" ) { ?>
                                            <tr>
                                                <td colspan="5" style="text-align:center ;">
                                                    <button type="submit" class="btn btn-success btn-block me-1" name="add">Add</button>
                                                    <a class="btn btn-secondary me-1" href="vendors.php" role="button">Cancel</a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                    </table>
                                            <?php if ( $act == "edit" ) { ?>
                                    <table class="table table-borderless table-hover">
                                            <tr>
                                                <th class="col-4 align-middle">Categories discovered:</th>
                                                <th class="col-1 align-middle">Delete</th>
                                                <th class="col-2 align-middle">URL</th>
                                                <th class="col-2 align-middle">Delete String</th>
                                                <th class="col-2 align-middle">Products assigned to:</th>
                                                <th class="col-1 align-middle">Crawl</th>
                                            </tr>
                                                <?php //Get categories
                                                    $sql=mysqli_query($con,"SELECT subcategories.id id, categories.catName catName, subCatName from subcategories LEFT JOIN categories ON subcategories.categoryid = categories.id ORDER BY categories.ix, subcategories.ix ASC");
                                                    $resultcat = mysqli_fetch_all($sql, MYSQLI_ASSOC);
                                                    //while ($resultcat[]=mysqli_fetch_array($sql)) {} //Same as above! Good to know!
                                                    $catSetID = 0;
                                                    $query=mysqli_query($con,"select id, active, catName from vendorscats where vendorid='$id'");
                                                    while ($result=mysqli_fetch_array($query))
                                                    {
                                                ?>
                                            <tr>
                                                <td class="ps-1 align-middle">
                                                    <?php 
                                                    if ($result['active'] == "1") {
                                                        echo "<i class=\"fas fa-eye text-success fa-lg mx-1\"></i>&nbsp;"; 
                                                    } else {
                                                        echo "<i class=\"fas fa-eye-slash text-danger fa-lg mx-1\"></i>&nbsp;";
                                                    }
                                                    echo $result['catName'];
                                                    ?>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" id="hiddenid" name="catSet[<?php echo $catSetID; ?>][type]" value="cat">
                                                        <input type="hidden" id="hiddenid" name="catSet[<?php echo $catSetID; ?>][id]" value="<?php echo $result['id']; ?>">
                                                        <input type="hidden" id="ovis" name="catSet[<?php echo $catSetID; ?>][ovis]" value="<?php echo $result['active']; ?>">
                                                        <input class="form-check-input" type="checkbox" role="switch" name="catSet[<?php echo $catSetID; ?>][newvis]" id="switch" <?php if ( $result['active'] == "1" ) { echo "checked"; }?> />
                                                        <a href="editvendors.php?act=delcat&id=<?php echo $result['id']; ?>" onClick="return confirm('Do you really want to delete? This will also delete all corresponding subcategories!');"><i class="fa fa-trash-alt text-danger fa-lg" aria-hidden="true"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $catSetID++;
                                            $query2=mysqli_query($con,"SELECT id, assign, active, subCatName, url, delString, crawl FROM vendorssubcats WHERE categoryid='$result[id]';");
                                                while ($result2=mysqli_fetch_array($query2))
                                                {
                                            ?>
                                            <tr>
                                                <td class="align-middle">
                                                    <?php 
                                                    if ($result2['active'] == "1") {
                                                        echo "└&nbsp;<i class=\"fas fa-eye text-success fa-lg mx-1\"></i>";
                                                        if ( $result2['assign'] == 0 ) { 
                                                            echo "<i class=\"far fa-circle text-danger fa-lg mx-1\"></i>";
                                                        } else {
                                                            echo "<i class=\"fas fa-check-circle text-success fa-lg mx-1\"></i>";
                                                        }
                                                    } else {
                                                        echo "└&nbsp;<i class=\"fas fa-eye-slash text-danger fa-lg mx-1\"></i>";
                                                        if ( $result2['assign'] == 0 ) { 
                                                            echo "<i class=\"far fa-circle text-danger fa-lg mx-1\"></i>";
                                                        } else {
                                                            echo "<i class=\"fas fa-check-circle text-success fa-lg mx-1\"></i>";
                                                        }
                                                    }
                                                    ?><a href="<?php echo $result2['url']; ?>" target="_blank" style="text-decoration: none;"><i class="fas fa-external-link-alt fa-lg mx-1"></i><?php echo $result2['subCatName']; ?></a>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" id="hiddenid" name="catSet[<?php echo $catSetID; ?>][type]" value="subCat">
                                                        <input type="hidden" id="hiddenid" name="catSet[<?php echo $catSetID; ?>][id]" value="<?php echo $result2['id']; ?>">
                                                        <input type="hidden" id="ovis" name="catSet[<?php echo $catSetID; ?>][ovis]" value="<?php echo $result2['active']; ?>">
                                                        <input class="form-check-input" type="checkbox" role="switch" name="catSet[<?php echo $catSetID; ?>][newvis]" id="switch" <?php if ( $result2['active'] == "1" ) { echo "checked"; } ?> />
                                                        <a href="editvendors.php?act=delsubcat&id=<?php echo $result2['id']; ?>" onClick="return confirm('Do you really want to delete?');"><i class="fa fa-trash-alt text-danger fa-lg" aria-hidden="true"></i></a>
                                                        
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <input class="form-check-input mt-0" type="checkbox" name="catSet[<?php echo $catSetID; ?>][editUrl]" onclick="ToggleTextBox('url-<?php echo $result2['id']; ?>')">
                                                        </div>
                                                        <input type="text" class="form-control" value="<?php echo $result2['url']; ?>" name="catSet[<?php echo $catSetID; ?>][url]" id="url-<?php echo $result2['id']; ?>" disabled>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="input-group">
                                                        <div class="input-group-text">
                                                            <input class="form-check-input mt-0" type="checkbox" name="catSet[<?php echo $catSetID; ?>][editDelString]" onclick="ToggleTextBox('delString-<?php echo $result2['id']; ?>')">
                                                        </div>
                                                        <?php
                                                        if ($result2['delString'] != NULL) {
                                                            $delString = unserialize($result2['delString']);
                                                            $delString = implode(', ', $delString);
                                                        } else {
                                                            $delString = null;
                                                        }
                                                        ?>
                                                        <input type="text" class="form-control" value="<?php echo $delString; ?>" name="catSet[<?php echo $catSetID; ?>][delString]" id="delString-<?php echo $result2['id']; ?>" placeholder = "(String1, String2 ...)" disabled>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                        <input type="hidden" id="oassign" name="catSet[<?php echo $catSetID; ?>][oassign]" value="<?php echo $result2['assign']; ?>">
                                                        <select class="form-select" aria-label=".form-select-sm example" id="assignlist" name="catSet[<?php echo $catSetID; ?>][newassign]">
                                                        <?php
                                                        $category = "";
                                                        if ( $result2['assign'] == null || $result2['assign'] == 0 ) {
                                                            echo "<option value=\"0\" class=\"fw-bold\" selected>(Unassigned) (Present)</option>";
                                                        } else {
                                                            echo "<option value=\"0\">(Unassigned)</option>";
                                                        }
                                                        for ($x = 0; isset($resultcat[$x]); $x++) { 
                                                            $catID = $resultcat[$x]['id'];
                                                            $catName = $resultcat[$x]['catName'];
                                                            $subCatName = $resultcat[$x]['subCatName'];
                                                            if ( $catName != $category ) {
                                                                echo "<option class=\"text-dark bg-light\" disabled>$catName</option>";
                                                            } 
                                                            if ( $result2['assign'] == $catID ) {
                                                                echo "<option value=\"$catID\" class=\"fw-bold\" selected>&nbsp;└&nbsp;$subCatName (Present)</option>";
                                                            } else {
                                                                echo "<option value=\"$catID\">&nbsp;└&nbsp;$subCatName</option>";
                                                            }
                                                            $category = $resultcat[$x]['catName'];
                                                        }
                                                        ?>
                                                        </select>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="form-check form-switch">
                                                        <input type="hidden" id="ocrawl" name="catSet[<?php echo $catSetID; ?>][ocrawl]" value="<?php echo $result2['crawl']; ?>">
                                                        <input class="form-check-input" type="checkbox" role="switch" name="catSet[<?php echo $catSetID; ?>][crawl]" id="switch" <?php if ( $result2['crawl'] == "1" ) { echo "checked"; } ?> />
                                                    </div>
                                                </td>
                                            </tr>
                                                            <?php
                                                            $catSetID++;
                                                            }
                                                        }
                                                }    ?>
                                                <?php if ( $act == "edit" ) { ?>
                                            <tr>
                                                <td colspan="6" style="text-align:center ;">
                                                    <button type="submit" class="btn btn-primary btn-block me-1" name="update">Update</button>
                                                    <a class="btn btn-secondary me-1" href="vendors.php" role="button">Cancel</a>
                                                    <a class="btn btn-danger" href="editvendors.php?act=del&id=<?php echo $id;?>" onClick="return confirm('Do you really want to delete?');" role="button">Delete</a>
                                                </td>
                                            </tr>
                                                <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
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
                    </div>
                </main>
          <?php include('../includes/footer.php');?>
            </div>
        </div>
    </body>
</html>
<?php } ?>
