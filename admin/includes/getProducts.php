<?php
//Check if inactive products are requested.
$inactive = "";
if (isset($_GET['active']) && $_GET['active'] == 0) {
    $inactive = "WHERE a.active = 0";
}

// DB table to use
$table = <<<EOT
(
SELECT
a.id,
a.image,
a.name,
c.brandName,
a.model,
IFNULL(offers.count, 0) offers,
IF( CHAR_LENGTH(a.description) > 25 , CONCAT(SUBSTRING(a.description, 1, 25), " ...") , a.description) AS description,
d.subCatName,
a.dateadded,
a.dateupdated,
a.active,
a.url itemUrl,
d.url subCatUrl,
categories.catName
FROM products a
LEFT JOIN subcategories d 
ON a.subCatID = d.id
LEFT JOIN (
SELECT a.id id, a.catName catName
FROM categories a
JOIN subcategories b
ON a.id = b.categoryid 
GROUP BY a.id
) categories
ON d.categoryid = categories.id
LEFT JOIN brand c ON a.brandID = c.id
LEFT JOIN (
SELECT productID, count(*) count
FROM offers
GROUP BY productID
) offers
ON offers.productID = a.id
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
    array( 'db' => 'image', 'dt' => 1 , 'formatter' => function ( $d, $row ) {
        $image = explode(", " , $d);
        $image[0] = ($image[0] == '') ? 'no-image.png' : $image[0];
        return '/images/' . $image[0];
    }),
    array( 'db' => 'name', 'dt' => 2 ),
    array( 'db' => 'brandName', 'dt' => 3 ),
    array( 'db' => 'model', 'dt' => 4 ),
    array( 'db' => 'offers', 'dt' => 5 ),
    array( 'db' => 'catName', 'dt' => 6 ),
    array( 'db' => 'subCatName', 'dt' => 7 ),
    array( 'db' => 'dateadded', 'dt' => 8 ),
    array( 'db' => 'dateupdated', 'dt' => 9 ),
    array( 'db' => 'itemUrl', 'dt' => 10 ),
    array( 'db' => 'active', 'dt' => 11 ),
    array( 'db' => 'subCatUrl', 'dt' => 12 )
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