<?php
//Check if to show only non-single products
$hideSingle = "0";
if (isset($_GET['hideSingle']) && $_GET['hideSingle'] == 1) {
    $hideSingle = "1";
}
$exists = '';
if (isset($_GET['exists']) && $_GET['exists'] == 1) {
    $exists = 'AND EXISTS(SELECT * FROM products WHERE products.brandID = a.brandID AND products.modelStrip = a.modelStrip) = 1';
}
// DB table to use
$table = <<<EOT
(
    SELECT a.id id, b.count, a.brandID, c.brandName, a.model, a.modelStrip, e.catName, d.subCatName, f.name vendor, a.price, a.dateadded, a.lastAlive, a.url, EXISTS(SELECT * FROM products WHERE products.brandID = a.brandID AND products.modelStrip = a.modelStrip) exs FROM tempproducts a 
    LEFT JOIN (SELECT brandID, modelStrip, subCatID, count(*) count FROM tempproducts GROUP BY brandID, modelStrip) b ON a.modelStrip = b.modelStrip AND a.brandID = b.brandID
    LEFT JOIN brand c ON a.brandID = c.id
    LEFT JOIN subcategories d ON a.subCatID = d.id
    LEFT JOIN categories e ON d.categoryid = e.id
    LEFT JOIN vendors f ON a.vendorId = f.id
    WHERE a.active = 1 AND b.count > $hideSingle $exists
    ORDER BY b.count DESC, a.modelStrip DESC, a.dateadded DESC
 ) temp
EOT;

// Table's primary key
$primaryKey = 'id';
 
// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
    array( 'db' => 'id', 'dt' => 0 ),
    array( 'db' => 'brandName', 'dt' => 1 ),
    array( 'db' => 'modelStrip', 'dt' => 2 ),
    array( 'db' => 'count', 'dt' => 3 ),
    array( 'db' => 'subCatName', 'dt' => 4 ),
    array( 'db' => 'vendor', 'dt' => 5 ),
    array( 'db' => 'price', 'dt' => 6 ),
    array( 'db' => 'dateadded', 'dt' => 7 ),
    array( 'db' => 'lastAlive', 'dt' => 8 ),
    array( 'db' => 'url', 'dt' => 9 ),
    array( 'db' => 'catName', 'dt' => 10 ),
    array( 'db' => 'brandID', 'dt' => 11 ),
    array( 'db' => 'exs', 'dt' => 12 )
    
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