<?php
// DB table to use
$table = <<<EOT
(
SELECT categories.id id, categories.ix ix, categories.vis catVis , categories.catName catName, categories.url catUrl, categories.icon icon, q.catProducts catProducts, q.catOffers catOffers, q.subCatID subCatID, q.subix subix, q.subCatVis subCatVis, q.subCatName subCatName, q.subCatUrl subCatUrl, q.subCatProducts subCatProducts, q.subCatOffers subCatOffers
FROM categories
LEFT JOIN (
SELECT catOffers.id id , catOffers.catName, catProducts.products catProducts, catOffers.catOffers catOffers, c.id subCatID, c.vis subCatVis, c.ix subix, c.subCatName subCatName, c.url subCatUrl, subCatProducts.subCatProducts subCatProducts, subCatOffers.subCatOffers subCatOffers
FROM products a
JOIN offers b
ON a.id = b.productID
RIGHT JOIN subcategories c 
ON a.subCatID = c.id
LEFT JOIN ( 
SELECT h.id id, h.catName catName, COUNT(f.id) catOffers 
FROM products e
JOIN offers f
ON e.id = f.productID
RIGHT JOIN subcategories g
ON g.id = e.subCatID
RIGHT JOIN categories h
ON g.categoryid = h.id
GROUP BY h.id
) catOffers
ON c.categoryid = catOffers.id
LEFT JOIN ( 
SELECT g.id subCatID, COUNT(f.id) subCatOffers 
FROM products e
JOIN offers f
ON e.id = f.productID
RIGHT JOIN subcategories g
ON g.id = e.subCatID
GROUP BY g.id
) subCatOffers
ON c.id = subCatOffers.subCatID
LEFT JOIN (
SELECT f.id subCatID, COUNT(e.id) subCatProducts
FROM subcategories f
LEFT JOIN products e
ON e.subCatID = f.id 
GROUP BY f.id
) subCatProducts
ON subCatProducts.subCatID = c.id
LEFT JOIN (
SELECT a.id catID, a.ix ix, a.catName catName, COUNT(c.id) products
FROM categories a
LEFT JOIN subcategories b
ON a.id = b.categoryid 
LEFT JOIN products c
ON c.subCatID = b.id
GROUP BY catName
) catProducts
ON catOffers.id = catProducts.catID
GROUP BY c.id
    ) q
ON categories.id = q.id
) temp
EOT;

// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'id', 'dt' => 0, ),
    array( 'db' => 'ix', 'dt' => 1 ),
    array( 'db' => 'catName', 'dt' => 2 ),
    array( 'db' => 'id', 'dt' => 3 ),
    array( 'db' => 'catProducts', 'dt' => 4 ),
    array( 'db' => 'catOffers', 'dt' => 5 ),
    array( 'db' => 'subCatID', 'dt' => 6 ),
    array( 'db' => 'subix', 'dt' => 7 ),
    array( 'db' => 'subCatName', 'dt' => 8 ),
    array( 'db' => 'subCatProducts', 'dt' => 9 ),
    array( 'db' => 'subCatOffers', 'dt' => 10 ),
    array( 'db' => 'subCatID', 'dt' => 11 ),
    array( 'db' => 'catUrl', 'dt' => 12 ),
    array( 'db' => 'subCatUrl', 'dt' => 13 ),
    array( 'db' => 'catVis', 'dt' => 14 ),
    array( 'db' => 'subCatVis', 'dt' => 15 ),
    array( 'db' => 'icon', 'dt' => 16 )
    
);
 
// SQL server connection information
//$sql_details = get_include_contents('../includes/config.php');
$sql_details = array(
    'user' => 'sravni',
    'pass' => 'sravnikomparki123',
    'db'   => 'sravni',
    'host' => 'localhost'
);
 
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
 
require( 'ssp.class.php' );
 
echo json_encode(
    SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);