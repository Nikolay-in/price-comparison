<?php
include_once('./includes/config.php');
if (isset($_GET['term'])) {
    $term = $_GET['term'];
    $term = preg_replace('/\s+/', '%', $term);
    $term = mysqli_real_escape_string($con, $term);
    $query = mysqli_query($con, "SELECT a.image, a.name, a.url, b.url catUrl FROM products a 
    LEFT JOIN subcategories b ON b.id = a.subCatID
    WHERE a.name LIKE '%$term%'
    LIMIT 10;");
    
    $data = [];
    while ($result = mysqli_fetch_array($query)) {
        $name = $result['name'];
        $image = explode(', ', $result['image'])[0];
        $thumbName = strtolower(pathinfo($image, PATHINFO_FILENAME));
        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $thumb = "/images/" . $thumbName .  '-t.' . $ext;
        $url = '/' . $result['catUrl'] . '/' . $result['url']; 

        $item = (Object) [
            'name' => "$name",
            'image' => $thumb,
            'url' => $url
        ];
        array_push($data, $item);
    }
    echo( json_encode($data) );
}
?>
