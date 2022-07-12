<?php
include_once('./includes/config.php');
include_once('./includes/functions.php');
if ( isset($_GET['cat']) ) {
    if ( strpos( $_GET['cat'], "s") ) {
        $id = str_replace("s", "", $_GET['cat']);
        $sql=mysqli_query($con,"SELECT subCatName, url, categoryid FROM subcategories WHERE id = $id AND vis = 1");
        if (mysqli_num_rows($sql) > 0) {
            $result=mysqli_fetch_array($sql);
            $catId = $result['categoryid'];
            $subCatId = $id;
            $type = 1;
            $catName = $result['subCatName'];
            $url = $result['url'];
            $brandSelected = (isset($_GET['brand']) && $_GET['brand'] != null) ? $_GET['brand'] : 0;
            $brandFilter = ($brandSelected != 0) ? "AND brandID = $brandSelected" : '';
            $brandList = [];
            
            //Get total products
            $query = mysqli_query($con, "SELECT count(id) count FROM products WHERE subCatID = $subCatId $brandFilter AND products.active = 1");
            $result = mysqli_fetch_array($query);
            $totalProducts = $result['count'];
            
            //Per Page
            $perPage = 20;
            
            $page = (isset($_GET['p']) && $_GET['p'] != null) ? $_GET['p'] : 1;
            $offset = ($page > 1) ? $perPage * ($_GET['p'] - 1) : 0;
            $pages = ceil($totalProducts / $perPage);
            
            $brandFilter = ($brandSelected != 0) ? "AND a.brandID = $brandSelected" : '';
            $paginationAdd = ($brandSelected != 0) ? "/$brandSelected" : '';
            $orderBy = 'a.dateadded DESC';
            if ((isset($_GET['sort']) && $_GET['sort'] == 'asc') || (isset($_GET['sort']) && $_GET['sort'] == 'desc')) {
                $orderBy = 'c.minPrice ' . $_GET['sort'];
                $paginationAdd = "/$brandSelected/" . $_GET['sort'];
            }


            if ($offset < $totalProducts) {
                $query = mysqli_query($con, "SELECT a.id, a.image, a.name, a.description, d.url catUrl, a.url, count(b.id) count, c.minPrice FROM products a
                RIGHT JOIN (
                    SELECT offers.id, offers.productID FROM offers
                    LEFT JOIN vendors ON offers.vendorID = vendors.id
                    WHERE offers.active = 1 AND vendors.vis = 1 ) b 
                ON b.productID = a.id
                LEFT JOIN (
                    SELECT MIN(price) minPrice, productID FROM offers
                    WHERE price > 0 AND active = 1
                    GROUP BY productID ) c
                ON c.productID = a.id
                LEFT JOIN subcategories d 
                ON d.id = a.subCatID
                WHERE a.subCatID = $subCatId $brandFilter
                AND a.active = 1 AND d.vis = 1
                GROUP BY id
                ORDER BY $orderBy
                LIMIT $perPage OFFSET $offset;");
                $products = mysqli_fetch_all($query, MYSQLI_ASSOC);
            }

            //Get brands list
            $query = mysqli_query($con, "SELECT a.brandID, b.brandName, count(*) count FROM products a
            LEFT JOIN brand b ON b.id = a.brandID
            WHERE a.subCatID = $subCatId
            GROUP BY brandID
            ORDER BY count DESC;");
            if ($query) {
                $brands = mysqli_fetch_all($query, MYSQLI_ASSOC);
                foreach ($brands as $brand) {
                    if ($brand['brandID'] == $brandSelected) {
                        $currBrandName = $brand['brandName'];
                    }
                    if (count($brandList) <= 6) {
                        $brandName = $brand['brandName'];
                        if (strlen($brandName) > 3) {
                            $brandName = ucfirst(strtolower($brandName));
                        }
                        array_push($brandList, $brandName);
                    }
                }
                $brandList = implode(', ', $brandList);
            }

        } else {
            http_response_code(404);
            echo "No Such SubCategory.";
            //include('./404.php'); 
            die;
        }
    } else if ( strpos($_GET['cat'], "c") ) {
        $id = str_replace("c", "", $_GET['cat']);
        $sql=mysqli_query($con,"SELECT catName, icon, url FROM categories WHERE id = $id");
        if ( $sql ) {
            $result=mysqli_fetch_array($sql);
            $catId = $id;
            $type = 0;
            $catName = $result['catName'];
            $url = $result['url'];
            $icon = $result['icon'];
            // echo SITE_WEBROOT . "<br />Category<br />name: $result[catName]<br />Icon: $result[icon]<br />Url: $result[url]";
        } else {
            echo "No Such Category."; 
            die();
        }
    } else if ( $_GET['cat'] == null ) {
        http_response_code(404);
        echo "404.";
        //include('./404.php'); 
        die;
    }
} else {
    http_response_code(404);
    echo "404...";
    //include('./404.php'); 
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Сравни цените на продукти на водещи марки като <?php echo $brandList;?> и други.">
        <title><?php echo $catName; ?> | Bestprices.bg</title>
        <meta property="name" content="<?php echo $catName; ?> | Bestprices.bg">
        <meta property="url" content="<?php echo SITE_WEBROOT . $url; ?>">

        <meta property="og:title" content="<?php echo $catName; ?> | Bestprices.bg">
        <meta property="og:description" content="Сравни цените на продукти на водещи марки като <?php echo $brandList;?> и други.">
        <meta property="og:url" content="<?php echo SITE_WEBROOT . $url; ?>">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="bg_BG">

        <link rel="canonical" href="<?php echo SITE_WEBROOT . $url; ?>">
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="shortcut icon" href="/favicon.ico">
        <link rel="apple-touch-icon" href="/favicon.ico">
        <link rel="preload stylesheet" href="/css/styles.css" as="style" type="text/css" crossorigin="anonymous">             
        <link rel="preload stylesheet" href="/css/custom.css" as="style" type="text/css" crossorigin="anonymous">

        <script src="/js/all.min.js" async></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js" crossorigin="anonymous" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous" defer></script>
        <script src="/js/jquery-ui.min.js" defer></script>
        <script src="/js/scripts.js" defer></script>

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
                        <div class="position-relative text-end h-100" style="color: rgba(0, 0, 0, .65); height: 2rem;">
                            <div class="float-start text-start fs-5 fw-semibold"><?php echo $catName; ?></div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-light btn-outline-secondary dropdown-toggle shadow-none btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php if (!isset($currBrandName)) {?>
                                        Марка
                                    <?php } else { echo $currBrandName; } ?>
                                </button>
                                <ul class="dropdown-menu">
                                        <li><a class="dropdown-item <?php echo ($brandSelected == 0) ? 'disabled' : ''; ?>" href="/<?php echo $url; ?>">Всички</a></li>
                                        <?php foreach ($brands as $brand) { ?>
                                        <li><a class="dropdown-item <?php echo ($brandSelected == $brand['brandID']) ? 'disabled' : ''; ?>" href="/<?php echo $url; ?>/1/<?php echo $brand['brandID']; ?>"><?php echo $brand['brandName']; ?></a></li>
                                        <?php } ?>
                                </ul>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-light btn-outline-secondary dropdown-toggle shadow-none btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                       <?php if ($_GET['sort'] == 'asc') { 
                                            echo 'Цена: възходяща'; 
                                        } else if ($_GET['sort'] == 'desc') {
                                            echo 'Цена: низходяща';
                                        } else {
                                            echo 'Подреди по';
                                        } ?>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item <?php echo ($_GET['sort'] == null) ? 'disabled' : ''; ?>" href="/<?php echo ($brandSelected != 0) ? $url . '/1/' . $brandSelected : $url . '/1'; ?>">Най-нови</a></li>
                                    <li><a class="dropdown-item <?php echo ($_GET['sort'] == 'asc') ? 'disabled' : ''; ?>" href="/<?php echo $url . '/1/' . $brandSelected; ?>/asc">Цена: възходяща</a></li>
                                    <li><a class="dropdown-item <?php echo ($_GET['sort'] == 'desc') ? 'disabled' : ''; ?>" href="/<?php echo $url . '/1/' . $brandSelected; ?>/desc">Цена: низходяща</a></li>
                                </ul>
                            </div>

                        </div>
                        <?php if (isset($products)) { ?>
                        <div class="row mt-0 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xxl-5 g-3 mb-3 px-2">
                            <?php foreach ($products as $item) {?>
                            <div class="col mb-2">
                                <a href="/<?php echo $item['catUrl'] . '/' . $item['url']; ?>" class="text-decoration-none fw-bold">
                                    <div class="card border-0">
                                        <div class="card-img position-relative d-flex" style="height: 10rem;">
                                            <img src="/images/<?php echo getThumb($item['image']); ?>" class="m-auto" style="max-height: 100%; max-width: 100%;" loading="lazy" alt="<?php echo htmlentities($item['name']); ?>">
                                            <div class="position-absolute bottom-0 start-0 badge rounded-pill bg-primary bg-opacity-75">oт <span class="h6 fw-bold"><?php echo number_format($item['minPrice'], 2); ?></span> лв.</div>
                                            <div class="position-absolute top-0 end-0 badge rounded-pill bg-secondary bg-opacity-75"><?php echo $item['count']; ?> оферти</div>
                                        </div>
                                        <div class="w-100 px-1 mt-2 elipsis"><?php echo htmlentities($item['name']); ?></div>
                                    </div>
                                </a>
                            </div>
                            <?php } ?>
                        </div>
                        <!-- Pagination -->
                        <ul class="pagination justify-content-center">
                            <li class="page-item<?php if ($page == 1) { echo ' disabled'; }?>">
                                <a class="page-link" href="<?php echo $page > 1 ? '/' . $url . '/' . ($page - 1) . $paginationAdd : '#';?>"><i class="far fa-chevron-left"></i></a>
                            </li>
                            <li class="page-item<?php if ($page == 1) { echo ' active'; }?>">
                                <a class="page-link" href="<?php echo $page != 1 ? '/' . $url . '/1' . $paginationAdd : '#';?>">1</a>
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
                                <li class="page-item"><a class="page-link" href="/<?php echo $url; ?>/<?php echo $i . $paginationAdd; ?>"><?php echo $i; ?></a></li>
                            <?php }
                            } ?>
                            <?php if ($pages > 1) {?>
                            <li class="page-item<?php if ($page == $pages) { echo ' active'; }?>">
                            <a class="page-link" href="<?php echo $page != $pages ? '/' . $url . '/' . $pages . $paginationAdd : '#';?>"><?php echo $pages; ?></a>
                            </li>
                            <?php } ?>
                            <li class="page-item<?php if ($page == $pages || $pages == 0) { echo ' disabled'; }?>">
                                <a class="page-link" href="<?php echo $page < $pages ? '/' . $url . '/' . ($page + 1) . $paginationAdd : '#';?>"><i class="far fa-chevron-right"></i></a>
                            </li>
                        </ul>
                        <?php } ?>
                    </div>
                </main>
                <!-- Footer-->
                <?php include_once('./includes/footer.php'); ?>
            </div>
        </div>
    <script>
        const catId = <?php echo $catId; ?>;
    </script>
    </body>
</html>