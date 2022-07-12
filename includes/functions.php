<?php
//Get thumbnail from img string
function getThumb($image) {
    $image = explode(', ', $image)[0];
    $thumbName = strtolower(pathinfo($image, PATHINFO_FILENAME));
    $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    return $thumbName . '-t.' . $ext; 
}
?>