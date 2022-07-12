<?php session_start();
include_once('../includes/config.php');
if (!isset($_SESSION['adminid'])) {
  header('location:logout.php');
  } else {
    include_once('./includes/newtempproducts.inc.php');
   ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>New Temporary Products</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" crossorigin="anonymous"/>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.2/datatables.min.css" crossorigin="anonymous"/>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.11.2/datatables.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="/js/scripts.js"></script>
        <script type="text/javascript" src="js/newTempProducts.js"></script>
    </head>
    <body class="sb-nav-fixed">
      <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
         <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4 pb-4">
                        <h1 class="mt-4"><i class="fas fa-hourglass-start me-2"></i>New Temporary Products <?php echo '(' . $unlabeled . ' of ' . $total . ')'; ?></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/admin/">Dashboard</a></li>
                            <li class="breadcrumb-item active">New Temporary Products</li>
                        </ol>
                        <div class="position-relative mb-2">
                            <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php if (is_numeric($cat)) { ?>
                                            Category: <?php echo $result[0]['subCatName']; ?>
                                        <?php } else { ?>
                                            All Categories
                                        <?php } ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php if (is_numeric($cat)) { ?>
                                            <li><a class="dropdown-item" href="newtempproducts.php?p=1&l=<?php echo $limit . $v . $b . $s . $a; ?>">All Categories</a></li>
                                        <?php } 
                                        foreach ($categories as $category) {
                                            if ($cat != $category['id']) { ?>
                                                <li><a class="dropdown-item" href="newtempproducts.php?p=1<?php echo $l . '&c=' . $category['id'] . $v . $b. $s . $a; ?>"><?php echo $category['subCatName'] . ' (' . $category['count'] . ')'; ?></a></li>
                                        <?php } else { ?>
                                            <li><a class="dropdown-item active disabled" href="#"><?php echo $category['subCatName']; ?></a></li>
                                        <?php }
                                        } ?> 
                                    </ul>
                            </div>
                            <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php if (is_numeric($vendor)) { ?>
                                            Vendor: <?php echo $result[0]['vendorName']; ?>
                                        <?php } else { ?>
                                            All Vendors
                                        <?php } ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php if (is_numeric($vendor)) { ?>
                                            <li><a class="dropdown-item" href="newtempproducts.php?p=1<?php echo $l . $c . $b. $s . $a; ?>">All Vendors</a></li>
                                        <?php } 
                                        foreach ($vendors as $vendorEntry) {
                                            if ($vendor != $vendorEntry['id']) { ?>
                                                <li><a class="dropdown-item" href="newtempproducts.php?p=1<?php echo $l . $c . '&v=' . $vendorEntry['id'] . $b . $s . $a; ?>"><?php echo $vendorEntry['name'] . ' (' . $vendorEntry['count'] . ')'; ?></a></li>
                                        <?php } else { ?>
                                            <li><a class="dropdown-item active disabled" href="#"><?php echo $vendorEntry['name']; ?></a></li>
                                        <?php }
                                        } ?> 
                                    </ul>
                            </div>
                            <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php if (is_numeric($brand)) { ?>
                                            Brand: <?php echo $result[0]['brandName']; ?>
                                        <?php } else { ?>
                                            All Brands
                                        <?php } ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php if (is_numeric($brand)) { ?>
                                            <li><a class="dropdown-item" href="newtempproducts.php?p=1<?php echo $l . $c . $v. $s . $a; ?>">All Brands</a></li>
                                        <?php } 
                                        foreach ($brands as $brandEntry) {
                                            if ($brand != $brandEntry['id']) { ?>
                                                <li><a class="dropdown-item" href="newtempproducts.php?p=1<?php echo $l . $c . $v . '&b=' . $brandEntry['id']. $s . $a; ?>"><?php echo $brandEntry['brandName'] . ' (' . $brandEntry['count'] . ')'; ?></a></li>
                                        <?php } else { ?>
                                            <li><a class="dropdown-item active disabled" href="#"><?php echo $brandEntry['brandName']; ?></a></li>
                                        <?php }
                                        } ?> 
                                    </ul>
                            </div>
                            <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        Sort by: <?php echo $sortTitle; ?> 
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php 
                                        foreach ($sorts as $sort) {
                                            if ($sortBy != $sort[0]) { ?>
                                                <li><a class="dropdown-item" href="newtempproducts.php?p=1<?php echo $l . $c . $v . $b . $sort[0] . $a; ?>">Sort by: <?php echo $sort[1]; ?></a></li>
                                        <?php } else { ?>
                                            <li><a class="dropdown-item active disabled" href="#">Sort by: <?php echo $sort[0]; ?></a></li>
                                        <?php }
                                        } ?> 
                                    </ul>
                            </div>
                            <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php echo $limit; ?> Per page
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php $limits = [20, 30, 50, 100];
                                        foreach ($limits as $perPage) {
                                            if ($perPage != $limit) { ?>
                                                <li><a class="dropdown-item" href="newtempproducts.php?p=1&l=<?php echo $perPage . $c . $v . $b . $s . $a; ?>"><?php echo $perPage; ?> Per Page</a></li>
                                        <?php } else { ?>
                                            <li><a class="dropdown-item active disabled" href="#"><?php echo $perPage; ?> Per Page</a></li>
                                        <?php }
                                        } ?> 
                                    </ul>
                            </div>
                            <?php if (!isset($_GET['a'])) { ?>
                                <a href="newtempproducts.php?p=1<?php echo $l . $c . $v . $b . $s . '&a=0'; ?>" class="btn btn-primary" role="button"><i class="fas fa-eye-slash fa-lg me-2"></i>Show Inactive</a>
                            <?php } else { ?>
                                <a href="newtempproducts.php?p=1<?php echo $l . $c . $v . $b . $s; ?>" class="btn btn-primary" role="button"><i class="fas fa-eye ch me-2"></i>Show Active</a>    
                            <?php } ?>
                        </div>
                        <form method="post" enctype="multipart/form-data">
                            <table class="table text-center border">
                                <tbody>
                                    <?php $index = $offset + 1;
                                    foreach ($result as $product) { ?>
                                    <tr id="item-<?php echo $index; ?>">
                                        <td class="col-12 text-start fw-bold bg-secondary bg-opacity-10" colspan="4"><?php echo $index++ . '. '; ?><a href="<?php echo $product['url']?>" target="_blank" style="text-decoration: none;"><i class="fas fa-external-link-alt me-1"></i><?php echo $product['name']?></a>
                                            <?php if ($index != ($offset + $limit + 1)) { ?> <a class="scrollToNext float-end" href="#item-<?php echo $index; ?>"><i class="fas fa-arrow-down fa-lg mx-2"></i></a><?php } ?>
                                            <?php if ($product['active'] == 1) { ?><a class="float-end" href="newtempproducts.php?deactivate=<?php echo $product['id']; ?>"><i class="fas fa-eye-slash fa-lg mx-2"></i></a><?php } else { ?> <a class="float-end" href="newtempproducts.php?activate=<?php echo $product['id']; ?>"><i class="fas fa-eye fa-lg mx-2"></i></a> <?php } ?>
                                        </td>
                                    </tr>
                                    <tr class="align-top text-start">
                                        <td class="col-2">
                                            <img src="<?php echo SITE_TEMPIMAGES . unserialize($product['image'])[0]; ?>" class="img-fluid" style="max-height: 250px;">
                                        </td>
                                        <td class="col-5">
                                            <table style="width:100%">
                                                <tr>   
                                                    <td colspan="2"><p class="fw-bold">Model:</p></td>
                                                </tr>
                                                <tr>
                                                    <td class="col-8 pe-4">
                                                    <?php 
                                                    $title = preg_replace('/[^A-Z0-9+\-\/\.()\p{L} ]+/u', '', strtoupper($product['model']));
                                                    $title = preg_replace('/[\/\.()]+/u', ' ', $title);
                                                    $title = explode(' ', $title); 
                                                    $title = array_filter($title, function($v, $k) { return $v != ''; }, ARRAY_FILTER_USE_BOTH );
                                                    if ($title[0] == $product['brandName']) { array_shift($title); }
                                                    $wordId = 0;
                                                    $wordSet = [];
                                                    foreach ($title as $word) { ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input radioWord" type="radio" id="radio-<?php echo $product['id'] . '-' . $wordId; ?>" name="radio-<?php echo $product['id']; ?>">
                                                            <label class="form-check-label" id="label-<?php echo $product['id'] . '-' . $wordId; ?>" for="radio-<?php echo $product['id'] . '-' . $wordId; ?>">
                                                            <?php 
                                                            array_push($wordSet, $word);
                                                            echo implode('-', $wordSet);
                                                            $wordId++;?>
                                                            </label>
                                                        </div>                                                          
                                                    <?php } ?>
                                                        <div class="form-check">
                                                            <input type="hidden" id="hiddenRadio[<?php echo $product['id']; ?>]" name="hiddenRadio[<?php echo $product['id']; ?>]" value="">
                                                            <input class="form-check-input radioWord" type="radio" id="radio-<?php echo $product['id'] . '-none'; ?>" name="radio-<?php echo $product['id']; ?>" checked>
                                                            <label class="form-check-label" id="label-<?php echo $product['id'] . '-none'; ?>" for="radio-<?php echo $product['id'] . '-none'; ?>">
                                                            none
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td class="col-4 ps-4">
                                                    <?php 
                                                    foreach ($title as $word) { ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input checkBoxWord" type="checkbox" id="checkBox-<?php echo $product['id'] . '-' . $wordId; ?>" name="checkBox-<?php echo $product['id'] . '-' . $wordId; ?>">
                                                            <label class="form-check-label" id="label-<?php echo $product['id'] . '-' . $wordId; ?>" for="checkBox-<?php echo $product['id'] . '-' . $wordId; ?>">
                                                            <?php echo $word; $wordId++;?>
                                                            </label>
                                                            <a class="checkList" style="cursor: pointer; color: #0d6efd;" prodid="<?php echo $product['id']; ?>" brandid="<?php echo $product['brandID']; ?>" brandname="<?php echo $product['brandName']; ?>" word="<?php echo $word; ?>"><i class="fas fa-th-list fa-lg"></i></a>
                                                        </div>
                                                    <?php } ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <div class="input-group">
                                                            <input type="hidden" id="hiddenModels[<?php echo $product['id']; ?>]" name="hiddenModels[<?php echo $product['id']; ?>]" value="">
                                                            <input type="text" class="form-control mt-2" id="models[<?php echo $product['id']; ?>]" name="models[<?php echo $product['id']; ?>]" value="" placeholder="(Model goes here ...)">
                                                            <button class="btn btn-outline-primary  mt-2" type="button" onClick='clearAll(<?php echo $product['id']; ?>);'><i class="fas fa-broom"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td class="col-3">
                                            <p class="fw-bold">Description:</p>
                                            <ul>
                                            <?php 
                                            if ($product['description']) {
                                                $desc = unserialize($product['description']);
                                                foreach ($desc as $line) { 
                                                    echo '<li>' . $line . '</li>';
                                                } 
                                            } else {
                                                echo 'No Description Available.';
                                            }
                                            ?>
                                            </ul>
                                        </td>
                                        <td class="col-2">
                                            <p><b>Vendor:</b><br><?php echo $product['vendorName']?></p>
                                            <p><b>ID:</b><br><?php echo $product['id']?></p>
                                            <p><b>Category:</b><br><?php echo $product['catName'];?><br>&nbsp;└&nbsp;<?php echo $product['subCatName'];?></p>
                                            <p><b>Added:</b><br><?php echo $product['dateadded']?></p>
                                            <p><b>Last Alive:</b><br><?php echo $product['lastAlive']?></p>
                                            <p><b>Price:</b><br><?php echo $product['price']?></p>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <li class="page-item<?php if ($page == 1) { echo ' disabled'; }?>">
                                        <a class="page-link" href="<?php echo $page > 1 ? 'newtempproducts.php?p=' . ($page - 1) . $linkAdd : '#';?>">Previous</a>
                                    </li>
                                    <li class="page-item<?php if ($page == 1) { echo ' active'; }?>">
                                        <a class="page-link" href="<?php echo $page != 1 ? 'newtempproducts.php?p=1' . $linkAdd : '#';?>">1</a>
                                    </li>
                                    <?php for ($i = 2; $i < $pages; $i++) { 
                                        if ($i == 4 && $page > 7) { ?>
                                            <li class="page-item"><span class="page-link">...</span></li>
                                            <?php $i = $page - 4;
                                        continue;
                                        }
                                        if ($page < $pages - 6 && $i == $page + 4) { ?>
                                            <li class="page-item"><span class="page-link">...</span></li>
                                            <?php $i = $pages - 3;
                                        continue;
                                        }
                                        if ($i == $page) { ?>
                                        <li class="page-item active"><span class="page-link"><?php echo $i; ?></span></li>
                                        <?php } else { ?>
                                        <li class="page-item"><a class="page-link" href="newtempproducts.php?p=<?php echo $i . $linkAdd; ?>"><?php echo $i; ?></a></li>
                                    <?php }
                                    } ?>
                                    <?php if ($pages > 1) {?>
                                    <li class="page-item<?php if ($page == $pages) { echo ' active'; }?>">
                                    <a class="page-link" href="<?php echo $page != $pages ? 'newtempproducts.php?p=' . $pages . $linkAdd : '#';?>"><?php echo $pages; ?></a>
                                    </li>
                                    <?php } ?>
                                    <li class="page-item<?php if ($page == $pages) { echo ' disabled'; }?>">
                                        <a class="page-link" href="<?php echo $page < $pages ? 'newtempproducts.php?p=' . ($page + 1) . $linkAdd : '#';?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                            <div class="col-12" style="text-align:center;"> <button type="submit" class="btn btn-success btn-block" name="submit">Submit</button> </div>
                        </form>
                    </div>
                </main>
  <?php include('../includes/footer.php');?>
            </div>
            
        </div>
    <div id="checkListWrapper" style="display: none; min-width: 720px; width=100%; height: 100%; overflow: auto; background: white; border: solid; z-index: 10000;">
    <form method="post" enctype="multipart/form-data">
        <div id="controls" style="position: sticky; top: 0px; z-index: 10000;" class="bg-white">
            <h5 class="mt-2">Target: <span id="brandName"></span> - <input type="text" id="clModel" name="clModel" class="form-control w-50 d-inline" value="">
            <i id="resync" class="fas fa-sync text-primary ms-2" style="cursor: pointer;"></i>
            <button id="toggleClBoxes" type="button" class="btn btn-primary float-end">Toggle All</button>
            </h5>
            <h6 class="mt-2 d-inline">Model to apply:</h6> <input type="text" id="clModelApply" name="clModelApply" class="form-control w-50 d-inline" value="">
            <span class="form-check float-end">
            <input type="checkbox" id="hideStrip" name="hideStrip" class="form-check-input"><label for="hideStrip" class="form-check-label">Hide M/S</label>
            </span>
            <input type="hidden" id="clBrandId" value="">
            <hr>
        </div>
        <div id="checkList" class="cl" style=""></div>
    </form>
    </div
    </body>
</html>
<?php } ?>