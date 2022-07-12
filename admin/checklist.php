<?php 
include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/config.php');
//include_once(__DIR__ . './../../includes/config.php');
if (isset($_GET['brandId']) && isset($_GET['word'])) {
    $brand = $_GET['brandId'];
    $word = $_GET['word'];
    $word = str_replace(['-', ' '], '%', $word);
    $brandName = $brand;
    $query = mysqli_query($con, "SELECT brandName FROM brand WHERE id = $brand");
    $result = mysqli_fetch_array($query);
    if ($result) {
        $brandName = $result['brandName'];
    }
    ?>
    <?php
    if ($word) {
        echo '<p>SQL String: ' . $word . '</p>';
        $query = mysqli_query($con, "SELECT a.id, name, image, model, modelStrip, b.brandName, a.url, c.url catUrl, d.count offers, d.minPrice, d.maxPrice FROM products a 
        LEFT JOIN brand b ON b.id = a.brandID 
        LEFT JOIN subcategories c ON c.id = a.subCatID 
        LEFT JOIN ( SELECT count(*) count, MIN(price) minPrice, MAX(price) maxPrice, productID FROM offers 
        GROUP BY productID ) d ON d.productID = a.id
        WHERE brandID = $brand AND (model LIKE '%$word%' OR modelStrip LIKE '%$word%' OR name LIKE '%$word%')");
        if (mysqli_num_rows($query)) { ?>
            <table style="width: 720px;"><th colspan=2><h4>Products (<?php echo mysqli_num_rows($query);?>) :</h4></th>
            <?php while ($result = mysqli_fetch_array($query)) { ?>
                <tr>
                    <td style="border-bottom-width: 1px;" class="position-relative text-center">
                        <img height="150px" style="max-width: 250px;" class="my-1" src="/images/<?php echo explode(', ', $result['image'])[0]; ?>">
                    </td>
                    <td class="ps-3" style="border-bottom-width: 1px;"><b><a href="/<?php echo $result['catUrl'] . '/' . $result['url']; ?>" target="_blank" class="text-decoration-none fs-5"><?php echo $result['name']; ?></a></b><a href="editproducts.php?act=edit&id=<?php echo $result['id']; ?>" target="_blank" class="fa-lg float-end"><i class="fas fa-edit fa-lg m-1"></i></a><br>
                        Brand: <b><?php echo $result['brandName']; ?></b><br>
                        Model: <b class="fs-5 text-danger model" style="cursor: pointer; text-decoration-line: underline; text-decoration-style: dotted;"><?php echo $result['model']; ?></b><br>
                        ModelStrip: <b><?php echo $result['modelStrip']; ?></b><br>
                        Offers: <b><?php echo $result['offers']; ?></b><br>
                        Prices: <b class="fs-5 text-success"><?php echo $result['minPrice'] . '</b> - <b class="fs-5 text-danger">' .  $result['maxPrice']; ?></b> Diff: <b class="fs-5 text-primary"><?php echo number_format($result['maxPrice'] - $result['minPrice'], 2, '.', ''); ?></b><br>
                    </td>
                </tr>
            <?php } ?>
            </table>
        <?php } else { ?>
            <h4>No Products Found</h4>
        <?php } ?>

        <hr>
        <?php
        if (isset($_GET['hideStrip'])) {
            $modelStrip = 'AND modelStrip IS NULL';
        } else {
            $modelStrip = "OR modelStrip LIKE '%$word%'";
        }
        $query = mysqli_query($con, "SELECT a.id, a.name, image, model, modelStrip, price, oldPrice, b.brandName, a.url, c.subCatName, d.name vendorName , a.dateadded
        FROM tempproducts a 
        LEFT JOIN brand b ON b.id = a.brandID 
        LEFT JOIN subcategories c ON c.id = a.subCatID 
        LEFT JOIN vendors d ON d.id = a.vendorId 
        WHERE brandID = $brand AND active = '1' AND (model LIKE '%$word%' $modelStrip)
        ORDER BY price DESC, modelStrip DESC;");

        if (mysqli_num_rows($query)) { 
            $queryPrices = mysqli_query($con, "SELECT MIN(price) minPrice, MAX(price) maxPrice FROM tempproducts WHERE brandID = $brand AND active = '1' AND (model LIKE '%$word%' $modelStrip)");
            $prices = mysqli_fetch_array($queryPrices); ?>
            <h4>Temp Products (<?php echo mysqli_num_rows($query);?>) / Prices (<b class="fs-4 text-success"><?php echo $prices['minPrice']; ?></b> - <b class="fs-4 text-danger"><?php echo $prices['maxPrice']; ?></b>) Diff: <b class="fs-4 text-primary"><?php echo number_format($prices['maxPrice'] - $prices['minPrice'], 2, '.', ''); ?></b></h4>
            <table class="position-relative" style="width: 720px; border-bottom-width: 1px;">
            <?php while ($result = mysqli_fetch_array($query)) { ?>
                <tr>
                    <td style="border-bottom-width: 1px;" class="position-relative text-center">
                        <label class="mx-2 w-100"  for="tempproduct-<?php echo $result['id']; ?>">
                            <img style="max-height: 150px; max-width: 250px;" src="/images/temp/<?php echo unserialize($result['image'])[0]; ?>">
                            <input type="checkbox" class="cl-cbox position-absolute form-check-input end-0 bottom-0 mb-2 me-2" id="tempproduct-<?php echo $result['id']; ?>" name="ids[]" value="<?php echo $result['id']; ?>">
                        </label>
                    </td>
                    <td class="ps-3" style="border-bottom-width: 1px;"><b><a href="<?php echo $result['url']; ?>" target="_blank" class="text-decoration-none fs-5"><?php echo $result['name']; ?></a></b><br>
                        Vendor: <b><?php echo $result['vendorName']; ?></b> (added <b><?php echo round((time() - strtotime($result['dateadded'])) / (60 * 60 * 24)); ?> days</b> ago)<br>
                        Subcategory: <b><?php echo $result['subCatName']; ?></b><br>
                        Brand: <b><?php echo $result['brandName']; ?></b><br>
                        Model: <b <?php if ($result['modelStrip']) {?>class="fs-5 text-danger model" style="cursor: pointer; text-decoration-line: underline; text-decoration-style: dotted;"<?php } ?>><?php echo $result['model']; ?></b><br>
                        ModelStrip: <b><?php echo $result['modelStrip']; ?></b><br>
                        Price: <b class="fs-4 text-success"><?php echo $result['price']; ?></b> Old Price: <b><?php echo $result['oldPrice']; ?></b>
                    </td>
                </tr>
                
                <?php } ?>
            </table>
            <div class="position-relative" style="text-align:center;">
            <button type="submit" id="submitButton" class="btn btn-success btn-block my-3" name="submitCheckList">Submit</button> 
            <span class="position-absolute mt-4 end-0 pe-2">Selected: <b id="selected">0</b></span>
            </div>
        <?php } else { ?>
            <p><h4>No Temp Products Found</h4></p>
        <?php }
    } else { ?>
    <p><h4>Model is needed</h4></p>
    <?php }
} ?>