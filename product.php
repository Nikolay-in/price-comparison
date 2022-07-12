<?php
session_start(); 
include_once('./includes/config.php');
include_once('./includes/functions.php');
if ( isset($_GET['url']) ) {
    $url = strtolower($_GET['url']);
    $sql=mysqli_query($con,"SELECT a.id, a.name, a.url, a.image, d.brandName, a.model, b.minPrice, c.subCatName, c.url subCatUrl, c.aliExId, count(e.id) offers, a.description FROM products a
    LEFT JOIN (
        SELECT MIN(price) minPrice, productID FROM offers
        WHERE price > 0 AND active = 1
        GROUP BY productID ) b
    ON b.productID = a.id
    LEFT JOIN subcategories c ON c.id = a.subCatID
    LEFT JOIN brand d ON d.id = a.brandID
    RIGHT JOIN (
        SELECT offers.id, offers.productID FROM offers
        LEFT JOIN vendors ON offers.vendorID = vendors.id
        WHERE vendors.vis = 1 ) e 
    ON a.id = e.productID
    WHERE a.url = '$url'");
    $product=mysqli_fetch_array($sql);
    if (!$product['id']) {
        http_response_code(404);
        include('./404.php'); 
        die;
    }
    $productName = htmlentities($product['name']);
    $description = unserialize($product['description']); //array
    $metaDesc = ($description) ? htmlentities(implode(', ', $description)) : '';
    $metaDesc = mb_substr($metaDesc, 0 , 100);
    $query = mysqli_query($con, "SELECT b.name, b.logo, a.title, a.link, a.price, a.oldprice, date(a.dateAdded) dateAdded, date(a.priceUpdated) priceUpdated, active, lastAlive FROM offers a
    LEFT JOIN vendors b 
    ON a.vendorID = b.id
    WHERE a.price > 0 AND b.vis = 1 AND productID = " . $product['id'] . "
    ORDER BY a.price ASC, a.active DESC;");
    $offers = mysqli_fetch_all($query, MYSQLI_ASSOC);
} else {
    http_response_code(404);
    include('./404.php'); 
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Сравни между <?php echo $product['offers']; ?> оферти с цени започващи от <?php echo $product['minPrice']; ?> лв. - <?php echo $product['subCatName']?> - <?php echo $product['brandName'] . ' ' . str_replace('-', ' ', $product['model']); ?>. <?php echo $metaDesc; ?>...">
        <meta name="keywords" content="<?php echo $product['brandName'] . ' ' . str_replace('-', ' ', $product['model']); ?>">        
        <title><?php echo $productName; ?> - <?php echo $product['subCatName']?> | Bestprices.bg</title>
        <meta property="name" content="<?php echo $productName; ?> - <?php echo $product['subCatName']?> | Bestprices.bg">
        <meta property="image" content="<?php echo SITE_WEBROOT; ?>images/<?php echo explode(', ', $product['image'])[0]; ?>">
        <meta property="url" content="<?php echo SITE_WEBROOT . $product['subCatUrl'] . '/' . $url; ?>">
        
        <meta property="og:title" content="<?php echo $productName; ?> - <?php echo $product['subCatName']?> | Bestprices.bg">
        <meta property="og:image" content="<?php echo SITE_WEBROOT; ?>images/<?php echo explode(', ', $product['image'])[0]; ?>">
        <meta property="og:description" content="Сравни между <?php echo $product['offers']; ?> оферти с цени започващи от <?php echo $product['minPrice']; ?> лв. - <?php echo $product['subCatName']?> - <?php echo $product['brandName'] . ' ' . str_replace('-', ' ', $product['model']); ?>. <?php echo $metaDesc; ?>...">
        <meta property="og:url" content="<?php echo SITE_WEBROOT . $product['subCatUrl'] . '/' . $url; ?>">
        <meta property="og:type" content="product">
        <meta property="og:locale" content="bg_BG">

        <link rel="preload image" as="image" href="<?php echo SITE_WEBROOT; ?>images/<?php echo explode(', ', $product['image'])[0]; ?>"> 
        <link rel="image_src" href="<?php echo SITE_WEBROOT; ?>images/<?php echo explode(', ', $product['image'])[0]; ?>">
        <link rel="canonical" href="<?php echo SITE_WEBROOT . $product['subCatUrl'] . '/' . $url; ?>">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="shortcut icon" href="/favicon.ico">
        <link rel="apple-touch-icon" href="/favicon.ico">
        <link rel="preload stylesheet" href="/css/styles.css" as="style" type="text/css" crossorigin="anonymous">             
        <link rel="preload stylesheet" href="/css/custom.css" as="style" type="text/css" crossorigin="anonymous">
        <link rel="preload stylesheet" href="/css/splide.min.css" as="style" type="text/css" crossorigin="anonymous">

        <script src="/js/all.min.js" async></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" crossorigin="anonymous" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" defer></script>
        <script src="/js/splide.min.js" defer></script>
        <script src="/js/jquery-ui.min.js" defer></script>
        <script src="/js/chart.min.js" defer></script>
        <script src="/js/scripts.js" defer></script>
        <script src="/js/product.js" defer></script>
        <script type="application/ld+json"><?php include_once('./jsonld.php'); ?></script>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-8MTY1LKZW4"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-8MTY1LKZW4');
        </script>
    </head>
    <body class="sb-nav-fixed">
        <!-- Navigation-->
        <?php include_once('./includes/navbar.php'); ?>
        <!-- Main layout -->
        <div id="layoutSidenav">
            <!-- Sidenav -->
            <?php include_once('./includes/sidebar.php'); ?>
            <!-- Content -->
            <div id="layoutSidenav_content">
                <!-- Product header -->
                <header>
                    <div class="container-fluid text-center my-3">
                        <div id="productTitle" class="row d-block d-lg-none h6 px-3 text-start"><?php echo $productName; ?></div>
                        <div class="row mt-0 mb-3 px-3">
                            <div class="col-6 col-md-3 col-lg-3 p-0">
                                <?php if (isset($_SESSION['adminid'])) {?> <a href="/admin/editproducts.php?act=edit&id=<?php echo $product['id']; ?>" target="_blank" class="expand position-absolute top-0 end-0 p-1 rounded bg-light opacity-75"><i class="fas fa-edit fa-2x"></i></a> <?php } ?>
                                <a href="#" class="pop">
                                <div class="imgWrap position-relative img-thumbnail h-100"><img src="/images/<?php echo explode(', ', $product['image'])[0]; ?>" class="m-auto" style="max-height: 17rem; max-width: 100%;" alt="<?php echo $productName; ?>">
                                <span class="expand position-absolute bottom-0 end-0 p-1 rounded bg-light opacity-75"><i class="fas fa-expand fa-2x"></i></span>
                                </div>
                                </a>
                            </div>
                            <div class="px-2 col-6 col-md-6 col-lg-6 px-0 text-start">
                                <div class="d-none d-lg-block text-start fw-bold h5"><?php echo $productName; ?></div>
                                <div class="d-inline-block badge rounded-pill bg-primary my-1" style="width: min-content;">oт <span class="h5 fw-bold"><?php echo number_format($product['minPrice'], 2); ?></span> лв.</div>
                                <div class="d-inline-block badge rounded-pill bg-secondary my-1" style="width: min-content;"><?php echo $product['offers']; ?> оферти</div>
                                <canvas id="priceHistory" class="d-none d-md-block mt-4" height="120" data-id="<?php echo $product['id']; ?>"></canvas>
                            </div>   
                            <div class="d-none d-md-block col-md-3 col-lg-3 border">
                                <?php if ($product['aliExId']) { ?>
                                <input id="ali" type="hidden" value="<?php echo $product['aliExId'] ?>">
                                <?php } ?>
                            </div>
                        </div>
                        <canvas id="mobilePriceHistory" class="d-block d-md-none mt-3 pe-2" height="150" data-id="<?php echo $product['id']; ?>"></canvas>
                    </div>
                </header>
                <!-- Main -->
                <main>
                <div class="card my-3 mx-3">
                    <div class="card-header fw-bold">Оферти</div>
                    <div class="card-body p-0">
                        <table class="table ">
                            <tbody>
                                <?php foreach ($offers as $offer) { 
                                    $inactiveDaysAgo = round((time() - strtotime($offer['lastAlive'])) / (60 * 60 * 24)); 
                                    $inactive = ($offer['active']) ? false : true; ?>
                                    <tr>
                                        <td class="row py-4 ps-3 pe-0 mx-0"<?php if ($offer['price'] == $product['minPrice'] && !$inactive) {?> style="background: radial-gradient(#caffd8, #ffffff);"<?php } else if ($inactive) { ?> style="-webkit-filter: grayscale(1);" <?php } ?>>  
                                            <div class="col-7 col-sm-9 ps-0 d-inline-block d-sm-flex ps-0"> 
                                                <div class="col-10 col-sm-4 col-md-3 d-inline-block my-auto px-3">
                                                    <img src="<?php echo SITE_LOGOS . $offer['logo']; ?>" class="w-100" alt="<?php echo htmlentities($offer['name']); ?>">
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-8 d-inline-block my-auto">
                                                    <a href="<?php echo ($inactive) ? '#' : $offer['link']; ?>" target="_blank" class="elipsis-offer"<?php if ($inactive) {?> onclick="return false;"<?php } ?> rel="nofollow"><?php echo $offer['title']; ?></a>
                                                    <?php if ($inactive && $inactiveDaysAgo == 0) { ?>
                                                    <p class="fs-6 text-black-50 mb-0"><small>(<b>неактивна</b> от: днес)</small></p>
                                                    <?php } else if ($inactive && $inactiveDaysAgo > 0) { ?>
                                                        <p class="fs-6 text-black-50 mb-0"><small>(<b>неактивна</b> от: <?php echo ($inactiveDaysAgo == 1) ? $inactiveDaysAgo . ' ден' : $inactiveDaysAgo . ' дни'; ?>)</small></p>                                                    
                                                    <?php }
                                                    $addedDaysAgo = round((time() - strtotime($offer['dateAdded'])) / (60 * 60 * 24));
                                                    if ($addedDaysAgo == 0) { ?>
                                                    <p class="fs-6 text-black-50"><small>(добавена днес)</small></p>
                                                    <?php } else { ?>
                                                    <p class="fs-6 text-black-50 mb-0"><small>(добавена преди: <?php echo ($addedDaysAgo == 1) ? $addedDaysAgo . ' ден' : $addedDaysAgo . ' дни'; ?>)</small></p>
                                                    <?php } 
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-5 col-sm-3 d-inline-block my-auto">
                                                <a href="<?php echo ($inactive) ? '#' : $offer['link']; ?>" target="_blank" class="text-decoration-none"<?php if ($inactive) {?> onclick="return false;"<?php } ?> rel="nofollow">
                                                    <?php if ($offer['priceUpdated'] && $offer['oldprice'] > 0) {
                                                        $priceUpdatedDaysAgo = round((time() - strtotime($offer['priceUpdated'])) / (60 * 60 * 24)); 
                                                        if ($offer['oldprice'] < $offer['price']) {
                                                            $badgePillColor = "bg-danger bg-opacity-50";
                                                        } else {
                                                            $badgePillColor = "bg-primary bg-opacity-50";
                                                            if ($offer['price'] == $product['minPrice']) {
                                                                $badgePillColor = "bg-success";
                                                            }
                                                        }
                                                        ?>
                                                <span class="badge rounded-pill fs-6 <?php echo $badgePillColor; ?>"><?php echo number_format($offer['price'], 2); ?> лв.</span>
                                                <p class="fs-6 text-danger text-decoration-line-through mb-0"><?php echo number_format($offer['oldprice'], 2); ?> лв.</p>
                                                <?php if ($priceUpdatedDaysAgo == 0) { ?>
                                                    <p class="fs-6 mb-0 text-black-50"><small>(променена днес)</small></p>
                                                    <?php } else { ?>
                                                        <p class="fs-6 mb-0 text-black-50"><small>(променена преди: <?php echo ($priceUpdatedDaysAgo == 1) ? $priceUpdatedDaysAgo . ' ден' : $priceUpdatedDaysAgo . ' дни'; ?>)</small></p>
                                                        <?php }
                                                } else { ?>
                                                <span class="badge rounded-pill fs-6<?php if ($offer['price'] == $product['minPrice']) {?> bg-success<?php } else { ?>  bg-primary bg-opacity-50<?php } ?>"><?php echo number_format($offer['price'], 2); ?> лв.</span>
                                                <?php } ?>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="btn-close btn-lg float-end bg-secondary position-absolute top-0 end-0 mt-1 me-1" aria-label="Close" onclick="$('#imagemodal').modal('hide');" style="z-index: 100;"></button>
                            <section id="main-carousel" class="splide" aria-label="My Awesome Gallery">
                                <div class="splide__track">
                                    <ul class="splide__list">
                                        <?php foreach (explode(', ', $product['image']) as $image) {?>
                                            <li class="splide__slide">
                                            <div class="card-img d-flex h-100">
                                            <img src="/images/<?php echo $image; ?>" data-splide-lazy="/images/<?php echo $image; ?>" alt="<?php echo $productName; ?>" class="px-auto m-auto" style="width: 100%;">
                                            </div>    
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </section>
            
                            <ul id="thumbnails" class="thumbnails">
                            <?php foreach (explode(', ', $product['image']) as $image) {?>
                                <li class="thumbnail"><img src="/images/<?php echo getThumb($image); ?>" data-splide-lazy="/images/<?php echo getThumb($image); ?>" alt="<?php echo $productName; ?>" class="m-auto" style="max-height: 100%; max-width: 100%;"></li>
                            <?php } ?>
                            </ul>
                        </div>
                        </div>
                    </div>
                </div>
                </main>
                <!-- Footer-->
                <?php include_once('./includes/footer.php'); ?>
            </div>
        </div>
    </body>
</html>