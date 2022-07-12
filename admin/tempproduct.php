<?php session_start();
include_once('../includes/config.php');
if (!isset($_SESSION['adminid'])) {
  header('location:logout.php');
} else {
    include_once('./includes/tempproduct.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Create new product with offers</title>
        <link href="../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" crossorigin="anonymous"/>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="js/tempProduct.js"></script>
    </head>
    <body class="sb-nav-fixed">
      <?php include_once('includes/navbar.php');?>
        <div id="layoutSidenav">
          <?php include_once('includes/sidebar.php');?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"><i class="fas fa-list me-2"></i>Create new product with offers</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/admin/">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="/admin/tempproducts.php">Temporary Products</a></li>
                            <li class="breadcrumb-item active">Create new product with offers</li>
                        </ol>
                        <?php if (isset($_GET['model']) && isset($_GET['brand']) && !isset($_POST['submit'])) { ?>
                        <div class="card mt-4">
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <th class="text-center position-relative"><span>Pictures</span><button id="clearImages" class="btn btn-sm btn-outline-primary position-absolute top-0 end-0" type="button"><i class="me-1 fas fa-broom"></i>Clear all images</button></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                        <?php
                                        $imageNo = 1;
                                        //If existing
                                        if ($exists > 0) {
                                            foreach ($imagesOrig as $image) { ?>
                                            <td class="col-2 position-relative float-start">
                                            <label class="position-relative" for="cb-img-<?php echo $imageNo; ?>">
                                                <img src="<?php echo SITE_IMAGES . $image; ?>" width="100%" style="position: relative;">
                                                <span class="position-absolute top-0 start-0 p-1 rounded bg-light opacity-75">Existing</span>
                                                <div class="text-hover-image expand position-absolute bottom-0 end-0 p-1 rounded text-primary bg-light opacity-75"><i class="fas fa-expand fa-2x"></i><img style="display:none;" alt="<?php echo SITE_IMAGES . $image; ?>" /></div>
                                                <input class="cb-img form-check-input position-absolute start-0 bottom-0 mb-2" type="checkbox" id="cb-img-<?php echo $imageNo; ?>" name="img-<?php echo $imageNo; ?>" value="<?php echo $image; ?>-orig" checked>
                                            </label>
                                            </td>
                                        <?php $imageNo++; } }
                                        foreach ($images as $vendor => $imagesArr) { 
                                            $vendor = array_shift($imagesArr);
                                            foreach ($imagesArr as $image) { ?>
                                            <td class="col-2 position-relative float-start">
                                            <label class="position-relative" for="cb-img-<?php echo $imageNo; ?>">
                                                <img src="<?php echo SITE_TEMPIMAGES . $image; ?>" width="100%" style="position: relative;">
                                                <span class="position-absolute top-0 start-0 p-1 rounded bg-light opacity-75"><?php echo $vendor; ?></span>
                                                <div class="text-hover-image expand position-absolute bottom-0 end-0 p-1 rounded text-primary bg-light opacity-75"><i class="fas fa-expand fa-2x"></i><img style="display:none;" alt="<?php echo SITE_TEMPIMAGES . $image; ?>" /></div>
                                                <input class="cb-img form-check-input position-absolute start-0 bottom-0 mb-2" type="checkbox" id="cb-img-<?php echo $imageNo; ?>" name="img-<?php echo $imageNo; ?>" value="<?php echo $image; ?>">
                                            </label>
                                            </td>
                                            <?php $imageNo++; } } ?>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table">
                                    <thead>
                                        <th class="col-1 text-center">Vendor</th>
                                        <th class="col-3 text-center">Title</th>
                                        <th class="col-1 text-center">EAN</th>
                                        <th class="col-2 text-center">Model</th>
                                        <th class="col-2 text-center">Category</th>
                                        <th class="col-1 text-center">Price</th>
                                        <th class="col-1 text-center">Added</th>
                                        <th class="col-1 text-center">Last Alive</th>
                                    </thead>
                                    <tbody>
                                        <?php $index = 0; 
                                            foreach ($result as $item) { ?>
                                        <tr>
                                            <td <?php if ($item['id'] == 'existing') { echo 'class="bg-success text-dark bg-opacity-25"'; } ?>>
                                                <div class="form-check position-relative">
                                                    <?php if ($item['id'] != 'existing') { ?>
                                                    <input class="form-check-input cb-offer" type="checkbox" id="offer-<?php echo $item['id']; ?>" name="offer-<?php echo $item['id']; ?>" value="<?php echo $item['id']; ?>" checked>
                                                    <?php } ?>
                                                    <label id="label-<?php echo $item['id']; ?>-offer" class="form-check-label " for="offer-<?php echo $item['id']; ?>">
                                                        <?php if ($item['id'] == 'existing') { ?>
                                                            <a target="_blank" href="editproducts.php?act=edit&id=<?php echo $exists;?>"><i class="fas fa-external-link-alt fa-lg mx-1"></i></a>
                                                        <?php } 
                                                        echo $item['vendorName']; ?>
                                                    </label>
                                                </div>
                                            </td>
                                        <td>
                                            <div class="form-check">
                                            <input type="radio" id="item-<?php echo $item['id']; ?>-title" class="form-check-input radioTitle" name="vendor-title" value="<?php echo htmlspecialchars($item['name']); ?>" <?php echo ($index == 0) ? 'checked' : ''; ?> >
                                            <label id="label-<?php echo $item['id']; ?>-title" class="form-check-label" for="item-<?php echo $item['id']; ?>-title"><?php echo $item['name']; ?></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                            <input type="radio" id="item-<?php echo $item['id']; ?>-EAN" class="form-check-input radioEAN" name="vendor-EAN" value="<?php echo (!$item['ean']) ? 'null' : $item['ean']; ?>" <?php echo ($item['ean'] == $ean) ? 'checked' : ''; ?> >
                                            <label id="label-<?php echo $item['id']; ?>-EAN" class="form-check-label" for="item-<?php echo $item['id']; ?>-EAN"><?php echo (!$item['ean']) ? 'null' : $item['ean']; ?></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                            <input type="radio" id="item-<?php echo $item['id']; ?>-model" class="form-check-input radioModel" name="vendor-model" value="<?php echo $item['model']; ?>" <?php echo ($index == 0) ? 'checked' : ''; ?> >
                                            <label id="label-<?php echo $item['id']; ?>-model" class="form-check-label" for="item-<?php echo $item['id']; ?>-model"><?php echo $item['model']; ?></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check">
                                            <input type="radio" id="item-<?php echo $item['id']; ?>-cat" class="form-check-input radioCat" name="vendor-cat" value="<?php echo $item['subCatID']; ?>" <?php echo ($index == 0) ? 'checked' : ''; ?> >
                                            <label id="label-<?php echo $item['id']; ?>-cat" class="form-check-label" for="item-<?php echo $item['id']; ?>-cat"><?php echo $item['catName'] . '<br>└&nbsp; ' . $item['subCatName']; ?></label>
                                            </div>
                                        </td>
                                        <td><?php echo $item['price']; ?></td>
                                        <td><?php echo $item['dateadded']; ?></td>
                                        <td><?php echo $item['lastAlive']; ?></td>
                                        </tr>
                                        <?php $index++;} ?>
                                    </tbody>
                                </table> 
                                <table class="table">
                                    <thead>
                                        <th class="text-center position-relative"><span>Description</span></th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <?php 
                                                $description = null;
                                                foreach ($result as $item) {
                                                    if (($item['id'] == 'existing') && $item['description']) {
                                                        $description = implode(PHP_EOL, unserialize($item['description']));
                                                    }
                                                ?>
                                            <td class="col-3 position-relative float-start">
                                                <div class="form-check">
                                                    <input type="radio" id="item-<?php echo $item['id']; ?>-desc" class="form-check-input radioDesc" name="vendor-desc" value="<?php echo $item['id']; ?>" <?php echo ($item['id'] == 'existing') ? 'checked' : ''; ?> >
                                                    <label id="label-<?php echo $item['id']; ?>-desc" class="form-check-label" for="item-<?php echo $item['id']; ?>-desc"><?php echo $item['description'] ? implode(PHP_EOL, unserialize($item['description'])) : 'No Description Available.'; ?></label>
                                                </div>
                                            </td>
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table> 
                            </div>     
                        </div>

                        <div class="card mt-4">
                            <form method="post" enctype="multipart/form-data">
                                <div class="card-header">
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="(Title goes here ...)">
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td id="productImages" class="col-3 float-start">
                                                <?php
                                                    $imagesInput = []; 
                                                    if ($exists > 0) { 
                                                    $imgNo = 1;
                                                    foreach ($imagesOrig as $image) { 
                                                        $width = 46;
                                                        $margin = 'mx-1';
                                                        if ($imgNo == 1) {
                                                            $width = 100;
                                                            $margin = 'mb-2';
                                                        } ?>
                                                    <a href="#" id="img-<?php echo $imgNo; ?>" class="expand"><img src="/images/<?php echo $image; ?>" class="<?php echo $margin; ?>" width="<?php echo $width; ?>%" style="position: relative;"></a>
                                                <?php $imgNo++;} }
                                                    foreach ($imagesOrig as $img) {
                                                        array_push($imagesInput, $img . '-orig');
                                                    }
                                                    ?>
                                                </td>
                                                <input type="hidden" id="imagesInput" name="images" value="<?php echo implode(',', $imagesInput);?>">
                                                <td class="col-3 float-start">
                                                <p><b>Brand:</b><br>
                                                <select class="form-select" id="brandInput" name="brand">
                                                    <?php echo $brandList;?>
                                                </select></p>
                                                <p><b>Model:</b><br><span id="model"><?php echo $model; ?></span></p>
                                                <input type="hidden" id="modelInput" name="model" value="<?php echo $model; ?>">
                                                <p><b>EAN:</b><br><span id="ean"><?php echo $ean; ?></span></p>
                                                <input type="hidden" id="eanInput" name="ean" value="<?php echo $ean; ?>">
                                                <p><b>Category:</b>
                                                <select class="form-select" id="subCatInput" name="subCat">
                                                    <?php echo $catlist;?>
                                                </select></p>
                                                <p><b>Lowest Price:</b><br><span id="minPrice"><?php echo $minPrice; ?></span></p>
                                                <p><b>Highest Price:</b><br><span id="maxPrice"><?php echo $maxPrice; ?></span></p> 
                                                </td>
                                                <td class="col-6 float-start">
                                                    <textarea class="form-control" id="description" name="description" value="" placeholder="(Description ...)" rows=16><?php if ($description) { echo $description; }?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" style="text-align:center ;">
                                                    <input type="hidden" id="offers" name="offers" value="<?php echo $offersIds; ?>">
                                                    <input type="hidden" id="exists" name="exists" value="<?php echo $exists; ?>">
                                                    <input type="hidden" id="goToNext" name="goToNext" value="false">
                                                    <button type="submit" class="btn btn-success btn-block me-1" name="submit">Submit</button>
                                                    <button type="submit" class="btn btn-success btn-block me-1" onclick="document.getElementById('goToNext').value = 'true';" name="submit">Submit & Go To Next &raquo;</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
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
