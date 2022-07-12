<?php 
session_start(); 
include_once('./includes/config.php');
include_once('./includes/functions.php');

//Get discounts
$query = mysqli_query($con, "SELECT pid, a.image, a.name, a.description, b.url catUrl, a.url, a.discount, minPrice FROM discounts 
LEFT JOIN products a ON a.id = discounts.pid
LEFT JOIN subcategories b ON b.id = a.subCatID
WHERE a.active = 1
ORDER BY RAND();");
$discounts = mysqli_fetch_all($query, MYSQLI_ASSOC);

//Get newest
$query = mysqli_query($con, "SELECT a.id, a.image, a.name, a.description, b.url catUrl, a.url, offers.count, minPrice FROM latest 
LEFT JOIN products a ON a.id = latest.pid
LEFT JOIN subcategories b ON b.id = a.subCatID
LEFT JOIN (
SELECT productID, count(*) count FROM offers
LEFT JOIN vendors ON offers.vendorID = vendors.id
WHERE vendors.vis = 1
GROUP BY productID
) offers
ON offers.productID = a.id
WHERE a.active = 1
ORDER BY latest.date DESC
LIMIT 18;");
$latest = mysqli_fetch_all($query, MYSQLI_ASSOC);

//Get last price updated
$query = mysqli_query($con, "SELECT a.id, a.image, a.name, a.description, b.url catUrl, a.url, offers.count, minPrice FROM pricesupdated 
LEFT JOIN products a ON a.id = pricesupdated.pid
LEFT JOIN subcategories b ON b.id = a.subCatID
LEFT JOIN (
SELECT productID, count(*) count FROM offers
LEFT JOIN vendors ON offers.vendorID = vendors.id
WHERE vendors.vis = 1
GROUP BY productID
) offers
ON offers.productID = a.id
WHERE a.active = 1
ORDER by pricesupdated.id ASC;");
$pricesUpdated = mysqli_fetch_all($query, MYSQLI_ASSOC);

//Get top brands
$query = mysqli_query($con, "SELECT brandName FROM brand ORDER BY products DESC LIMIT 8;");
$result = mysqli_fetch_all($query, MYSQLI_ASSOC);
$topBrands = [];
foreach ($result as $brand) {
    $brand = $brand['brandName'];
    if (strlen($brand) > 3) {
        $brand = ucfirst(strtolower($brand));
    }
    array_push($topBrands, $brand);
}
$topBrands = implode(', ', $topBrands);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Сравни цените на електроника и битова техника на водещи марки като <?php echo $topBrands; ?> и други.">
        <title>Намери най-изгодните оферти на пазара | Bestprices.bg</title>
        <meta property="name" content="Намери най-изгодните оферти на пазара | Bestprices.bg">
        <meta property="image" content="<?php echo SITE_WEBROOT; ?>assets/logobig.jpg">
        <meta property="url" content="<?php echo SITE_WEBROOT; ?>">
        
        <meta property="og:title" content="Намери най-изгодните оферти на пазара | Bestprices.bg">
        <meta property="og:image" content="<?php echo SITE_WEBROOT; ?>assets/logobig.jpg">
        <meta property="og:description" content="Сравни цените на електроника и битова техника на водещи марки като <?php echo $topBrands; ?> и други.">
        <meta property="og:url" content="<?php echo SITE_WEBROOT; ?>">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="bg_BG">
        
        <link rel="image" href="<?php echo SITE_WEBROOT; ?>assets/logobig.jpg"> 
        <link rel="image_src" href="<?php echo SITE_WEBROOT; ?>assets/logobig.jpg">
        <link rel="canonical" href="<?php echo SITE_WEBROOT; ?>">
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
        <script src="/js/scripts.js" defer></script>
        <script src="/js/index.js" defer></script>

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
                <!-- Main -->
                <main>
                    <div class="container-fluid text-center my-3">
                        <div class="position-relative" style="color: rgba(0, 0, 0, .65); height: 2rem;">
                            <h5 class="position-absolute start-0 text-start fw-semibold">Продукти в промоция &raquo;</h5>
                            <!-- <div class="position-absolute bottom-0 end-0 text-end fs-6 float-end">към всички &raquo;</div> -->
                        </div>

                        <!-- Splide -->
                        <section id="discountSplide" class="splide text-center my-3" aria-label="Splide Basic HTML Example">
                            <div class="splide__track">
                                <ul class="splide__list">
                                    <?php foreach ($discounts as $item) {?>
                                    <li class="splide__slide">
                                        <div class="col mx-1">
                                            <a href="/<?php echo $item['catUrl'] . '/' . $item['url']; ?>" class="text-decoration-none fw-bold">
                                                <div class="card border-0">
                                                    <div class="card-img position-relative d-flex" style="height: 10rem;">
                                                        <img src="/images/<?php echo getThumb($item['image']); ?>" data-splide-lazy="/images/<?php echo getThumb($item['image']); ?>" class="m-auto splide-img" alt="<?php echo htmlentities($item['name']); ?>">
                                                        <div class="position-absolute bottom-0 start-0 badge rounded-pill bg-primary bg-opacity-75">oт <span class="h6 fw-bold"><?php echo number_format($item['minPrice'], 2); ?></span> лв.</div>
                                                        <div class="position-absolute top-0 end-0 badge rounded-pill bg-success fs-6"><i class="fas fa-angle-double-down"></i> <?php echo $item['discount']; ?>%</div>
                                                    </div>
                                                    <div class="w-100 px-1 mt-2 elipsis"><?php echo $item['name']; ?></div>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </section>
                        
                        <header class="bg-light container px-4 px-lg-5 py-3 py-lg-4 mb-3" style="color: rgba(0, 0, 0, .65);">
                            <div class="text-center">
                                <h1 class="fw-bolder fs-2">Сравни цените на водещи марки като</h1>
                                <p class="lead fw-normal mt-2 mb-0"><?php echo $topBrands; ?> и други.</p>
                            </div>
                        </header>

                        <!-- Section-->
                        <section class="pb-5">
                            <div class="position-relative" style="color: rgba(0, 0, 0, .65); height: 2rem;">
                                <h5 class="position-absolute start-0 text-start fw-semibold">Нови продукти &raquo;</h5>
                                <!-- <div class="position-absolute bottom-0 end-0 text-end fs-6 float-end">към всички &raquo;</div> -->
                            </div>
                            <!-- splide -->
                            <section id="newestSplide" class="splide text-center my-3" aria-label="Splide Basic HTML Example">
                                <div class="splide__track">
                                    <ul class="splide__list">
                                    <?php foreach ($latest as $item) {?>
                                        <li class="splide__slide">
                                            <div class="col mx-1">
                                                <a href="/<?php echo $item['catUrl'] . '/' . $item['url']; ?>" class="text-decoration-none fw-bold">
                                                    <div class="card border-0">
                                                        <div class="card-img position-relative d-flex" style="height: 10rem;">
                                                            <img src="/images/<?php echo getThumb($item['image']); ?>" data-splide-lazy="/images/<?php echo getThumb($item['image']); ?>" class="m-auto splide-img" alt="<?php echo htmlentities($item['name']); ?>">
                                                            <div class="position-absolute bottom-0 start-0 badge rounded-pill bg-primary bg-opacity-75">oт <span class="h6 fw-bold"><?php echo number_format($item['minPrice'], 2); ?></span> лв.</div>
                                                            <div class="position-absolute top-0 end-0 badge rounded-pill bg-secondary bg-opacity-75"><?php echo $item['count']; ?> оферти</div>
                                                        </div>
                                                        <div class="w-100 px-1 mt-2 elipsis"><?php echo $item['name']; ?></div>
                                                    </div>
                                                </a>
                                            </div>
                                        </li>
                                    <?php } ?>
                                    </ul>
                                </div>
                            </section>
                        

                            <div class="position-relative" style="color: rgba(0, 0, 0, .65); height: 2rem;">
                                <h5 class="position-absolute start-0 text-start fs-5 fw-semibold">С последно променена цена &raquo;</h5>
                                <!-- <div class="position-absolute bottom-0 end-0 text-end fs-6 float-end">към всички &raquo;</div> -->
                            </div>
                            <!-- splide -->
                            <section id="pricesUpdatedSplide" class="splide text-center my-3" aria-label="Splide Basic HTML Example">
                                <div class="splide__track">
                                    <ul class="splide__list">
                                        <?php foreach ($pricesUpdated as $item) {?>
                                        <li class="splide__slide">
                                            <div class="col mx-1">
                                                <a href="/<?php echo $item['catUrl'] . '/' . $item['url']; ?>" class="text-decoration-none fw-bold">
                                                    <div class="card border-0">
                                                        <div class="card-img position-relative d-flex" style="height: 10rem;">
                                                            <img src="/images/<?php echo getThumb($item['image']); ?>" data-splide-lazy="/images/<?php echo getThumb($item['image']); ?>" class="m-auto splide-img" alt="<?php echo htmlentities($item['name']); ?>">
                                                            <div class="position-absolute bottom-0 start-0 badge rounded-pill bg-primary bg-opacity-75">oт <span class="h6 fw-bold"><?php echo number_format($item['minPrice'], 2); ?></span> лв.</div>
                                                            <div class="position-absolute top-0 end-0 badge rounded-pill bg-secondary bg-opacity-75"><?php echo $item['count']; ?> оферти</div>
                                                        </div>
                                                        <div class="w-100 px-1 mt-2 elipsis"><?php echo $item['name']; ?></div>
                                                    </div>
                                                </a>
                                            </div>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </section>
                        </section>
                    </div>
                </main>
                <!-- Footer-->
                <?php include_once('./includes/footer.php'); ?>
            </div>
        </div>
    </body>
</html>
