<?php
//Check if inactive offers are requested
$inactive = "";
if (isset($_GET['active']) && $_GET['active'] == 0) {
    $inactive = "WHERE a.active = 0";
}

// DB table to use
$table = <<<EOT
(
    SELECT
    a.id,
    a.productID,
    a.price,
    a.oldprice,
    a.dateAdded,
    a.priceUpdated,
    a.lastAlive,
    a.link,
    b.name AS vendorname,
    c.name,
    c.image,
    d.brandName,
    c.model,
    a.active
    FROM offers a
    JOIN vendors b ON a.vendorID = b.id
    JOIN products c ON a.productID = c.id
    LEFT JOIN brand d ON c.brandID = d.id
    $inactive
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
    array( 'db' => 'productID', 'dt' => 1 ),
    array( 'db' => 'image', 'dt' => 2 , 'formatter' => function ( $d, $row ) {
        $image = explode(", " , $d);
        $image[0] = ($image[0] == '') ? 'no-image.png' : $image[0];
        return '/images/' . $image[0];
    }),
    array( 'db' => 'name', 'dt' => 3 ),
    array( 'db' => 'brandName', 'dt' => 4 ),
    array( 'db' => 'model', 'dt' => 5 ),
    array( 'db' => 'vendorname', 'dt' => 6 ),
    array( 'db' => 'price', 'dt' => 7 ),
    array( 'db' => 'oldprice', 'dt' => 8 ),
    array( 'db' => 'dateAdded', 'dt' => 9 ),
    array( 'db' => 'priceUpdated', 'dt' => 10 ),
    array( 'db' => 'lastAlive', 'dt' => 11 ),
    array( 'db' => 'active', 'dt' => 12 ),    
    array( 'db' => 'link', 'dt' => 13 )
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