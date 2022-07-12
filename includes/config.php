<?php
//Hide all error reportings in php.ini also.
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('DB_SERVER','***');
define('DB_USER','***');
define('DB_PASS' ,'***');
define('DB_NAME', '***');
$con = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

//Website Specific
define('SITE_WEBROOT', "https://bestprices.bg/");
define('SITE_IMAGES', "https://bestprices.bg/images/");
define('SITE_TEMPIMAGES', "https://bestprices.bg/images/temp/");
define('SITE_LOGOS', "https://bestprices.bg/images/logos/");

?>