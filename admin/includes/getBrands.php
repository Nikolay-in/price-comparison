<?php
// DB table to use
$table = <<<EOT
(
    SELECT a.id id, a.brandName name, IFNULL(products.products, 0) products, IFNULL(SUM(offers.offers), 0) offers 
    FROM brand a
    LEFT JOIN ( 
      SELECT c.id id, COUNT(*) products
      FROM brand c
      RIGHT JOIN products a
      ON c.id = a.brandID
      GROUP BY c.id
      ) products
    ON a.id = products.id
    LEFT JOIN (
      SELECT a.brandID brandID, offers.offers offers
      FROM products a
        JOIN (
          SELECT a.id id, COUNT(*) AS offers
          FROM products a
          JOIN offers b
          ON a.id = b.productID
          GROUP BY a.id
         ) offers
        ON a.id = offers.id
    ) offers
    ON a.id = offers.brandID
    GROUP BY a.id
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
    array( 'db' => 'products', 'dt' => 2 ),
    array( 'db' => 'offers', 'dt' => 3 )
);

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