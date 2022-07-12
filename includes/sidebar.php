<?php 
$query = mysqli_query($con, "SELECT b.id catId, b.ix catIX, b.icon, b.catName, a.id subCatId, a.ix subCatIX, a.subCatName, a.url FROM subcategories a
LEFT JOIN categories b ON b.id = a.categoryid
WHERE a.vis = 1 AND b.vis = 1 
ORDER BY b.ix ASC, a.ix ASC;");
$result = mysqli_fetch_all($query, MYSQLI_ASSOC);
?>

<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-light"  id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="flex-shrink-0 p-3 bg-white" style="width: 280px;">
                    <span class="fs-5 fw-semibold d-flex align-items-center pb-3 mb-3 border-bottom" style="color: rgba(0, 0, 0, .65);">Категории</span>
                    <ul class="list-unstyled ps-0">
                    <?php 
                    if ($result) {
                        $currCat = 0;
                        for ($i = 0; $i < count($result); $i++) {
                            if ($currCat != $result[$i]['catId']) { ?>
                                <li class="mb-1">
                                    <button class="btn btn-toggle align-items-center rounded text-start w-100 shadow-none collapsed" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $result[$i]['catId']; ?>" aria-expanded="false">
                                    <?php echo $result[$i]['icon'] . '<span class="w-100">' . $result[$i]['catName'] . '</span>'; ?>
                                    </button>
                                    <div class="collapse" id="collapse-<?php echo $result[$i]['catId']; ?>">
                                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                      <?php } ?>
                                        <li><a href="/<?php echo $result[$i]['url']; ?>" class="link-dark rounded" <?php if (isset($subCatId) && $subCatId == $result[$i]['subCatId']) { ?>aria-selected="true" aria-checked="true"<?php } ?>><?php echo $result[$i]['subCatName']; ?></a></li>
                      <?php if ($i == (count($result) - 1) || $result[$i + 1]['catId'] != $result[$i]['catId']) { ?>
                                    </ul>
                                    </div>
                                </li>
                      <?php }
                            $currCat = $result[$i]['catId'];
                        }
                    } ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</div>