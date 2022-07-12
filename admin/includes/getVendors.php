<?php
// DB table to use
$table = <<<EOT
(
    SELECT a.id id, a.name name, a.logo logo, a.website website, a.description description, a.vis vis, a.catdir catdir, offers.offers, activecats.cats activecats, cats.cats cats, activesubcats.subcats activesubcats, assignedsubcats.assignedsubcats assignedsubcats, subcats.subcats subcats
    FROM vendors a
    LEFT JOIN (
        SELECT b.id vendor, COUNT(c.productID) AS offers
        from vendors b 
        LEFT JOIN offers c
        ON b.id = c.vendorID
        GROUP BY b.id
    ) offers
    ON a.id = offers.vendor
    LEFT JOIN (
        SELECT d.id id, COUNT(e.id) AS cats
        from vendors d
        LEFT JOIN vendorscats e
        ON d.id = e.vendorid
        GROUP BY d.id
    ) cats
    ON a.id = cats.id
    LEFT JOIN (
        SELECT d.id id, COUNT(e.id) AS cats
        from vendors d
        LEFT JOIN vendorscats e
        ON d.id = e.vendorid
        WHERE active = 1
        GROUP BY d.id
    ) activecats
    ON a.id = activecats.id
    LEFT JOIN (
        SELECT a.vendorid as id, COUNT(b.id) AS subcats
        from vendorscats a
        LEFT JOIN vendorssubcats b
        ON a.id = b.categoryid
        WHERE b.active = 1
        GROUP BY a.vendorid
    ) activesubcats
    ON a.id = activesubcats.id
    LEFT JOIN (
        SELECT a.vendorid id, COUNT(b.id) AS assignedsubcats
        from vendorscats a
        LEFT JOIN vendorssubcats b
        ON a.id = b.categoryid
        WHERE b.assign != 0
        GROUP BY a.vendorid
    ) assignedsubcats
    ON a.id = assignedsubcats.id
    LEFT JOIN (
        SELECT a.vendorid id, COUNT(b.id) AS subcats
        from vendorscats a
        LEFT JOIN vendorssubcats b
        ON a.id = b.categoryid
        GROUP BY a.vendorid
    ) subcats
    ON a.id = subcats.id
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
    array( 'db' => 'name', 'dt' => 1 ),
    array( 'db' => 'logo', 'dt' => 2 , 'formatter' => function ( $d, $row ) {
        $image = explode(", " , $d);
        $image[0] = ($image[0] == '') ? 'no-logo.jpg' : $image[0];
        return '/images/logos/' . $image[0];
    }),
    array( 'db' => 'website', 'dt' => 3 ),
    array( 'db' => 'description', 'dt' => 4 ),
    array( 'db' => 'activecats', 'dt' => 5 ),
    array( 'db' => 'activesubcats', 'dt' => 6 ),
    array( 'db' => 'offers', 'dt' => 7 ),
    array( 'db' => 'vis', 'dt' => 9 ),
    array( 'db' => 'assignedsubcats', 'dt' => 10 ),
    array( 'db' => 'cats', 'dt' => 11 ),
    array( 'db' => 'subcats', 'dt' => 12 )
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