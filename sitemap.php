<?php
require('./includes/config.php');
header ('Content-type: text/xml; charset=utf-8');

//Categories
$queryCats = mysqli_query($con, "SELECT url FROM subcategories WHERE vis = 1 ORDER BY ix ASC;");
//Products
$queryProducts = mysqli_query($con, "SELECT a.url, date(a.dateadded) dateAdded, date(a.dateupdated) dateUpdated, b.url catUrl FROM products a
LEFT JOIN subcategories b ON b.id = a.subCatID WHERE a.active = 1;");
?>
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
  <loc><?php echo SITE_WEBROOT; ?></loc>
  <changefreq>daily</changefreq>
  <priority>1.0</priority>
</url>
<?php
while ($category = mysqli_fetch_assoc($queryCats)) {
?>
<url>
  <loc><?php echo SITE_WEBROOT . $category['url']; ?></loc>
  <changefreq>weekly</changefreq>
  <priority>0.75</priority>
</url>
<?php 
}
while ($product = mysqli_fetch_assoc($queryProducts)) {
$date = ($product['dateUpdated']) ? $product['dateUpdated'] : $product['dateAdded'];
?> 
<url>
  <loc><?php echo SITE_WEBROOT . $product['catUrl'] . '/' . $product['url']; ?></loc>
  <lastmod><?php echo $date; ?></lastmod>
  <changefreq>weekly</changefreq>
  <priority>1.0</priority>
</url>
<?php
}
?>
</urlset>
<?php
mysqli_free_result($queryCats);
mysqli_free_result($queryProducts);
?>