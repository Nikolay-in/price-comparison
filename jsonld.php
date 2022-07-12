<?php
// json-ld data extractor for google
include_once('./includes/config.php');
if (isset($_GET['id']) || isset($product['id'])) {
    
    $jsonId = (isset($_GET['id'])) ? $_GET['id'] : $product['id'];
    $query = mysqli_query($con, "SELECT a.id, a.name, e.brandName, a.model, a.image, b.minPrice, b.maxPrice, c.subCatName, count(d.id) offers, c.url catUrl, a.url, a.description FROM products a
    LEFT JOIN (
        SELECT MIN(price) minPrice, MAX(price) maxPrice, productID FROM offers WHERE price > 0
        GROUP BY productID ) b
    ON b.productID = a.id
    LEFT JOIN subcategories c ON c.id = a.subCatID
    RIGHT JOIN offers d 
    ON a.id = d.productID
    LEFT JOIN brand e ON e.id = a.brandID
    WHERE a.id = $jsonId;");
    $result = mysqli_fetch_array($query);

    if ($result) {
        $name = $result['name'];
        if ($result['description']) {
            $description = implode('. ', unserialize($result['description']));
            $description = trim(preg_replace('/\s+|\r/', ' ', $description));
            $metaDesc = ' - ' . mb_substr($description, 0, 100) . '...';
        } else {
            $metaDesc = '';
        }
        $description = 'Сравни между ' . $result['offers'] . ' оферти с цени започващи от ' . $result['minPrice'] . ' лв. - ' . $result['subCatName'] . $metaDesc;
        $brand = $result['brandName'];
        $images = [];
        foreach (explode(', ', $result['image']) as $img) {
            array_push($images, SITE_IMAGES . $img);
        }
        $offersCount = $result['offers'];
        $url = SITE_WEBROOT . $result['catUrl'] . '/' . $result['url'];
        $minPrice = $result['minPrice'];
        $maxPrice = $result['maxPrice'];
        $data = [ (Object) [
            "@context" => "https://schema.org/",
            "@type" => "Product",
            "name" => "$name",
            "description" => $description,
            "brand" => (Object) [
                "@type" => "Brand",
                "name" => $brand
            ],
            "image" => $images,
            "offers" => (Object) [
                "@type" => "AggregateOffer",
                "highPrice" => $maxPrice,
                "lowPrice" => $minPrice,
                "priceCurrency" => "BGN",
                "offerCount" => $offersCount,
                "offers" => (Object) [
                    "@type" => "Offer",
                    "url" => "$url",
                    "price" => $minPrice,
                    "priceCurrency" => "BGN",
                    "itemCondition" => "https://schema.org/NewCondition",
                    "availability" => "https://schema.org/InStock",
                    ]
                ]
            ], (Object) [
            "@context" =>  "https://schema.org",
            "@type"  => "BreadcrumbList",
            "itemListElement"  => [ 
                (Object) [
                    "@type"  => "ListItem",
                    "position"  => 1,
                    "name"  => "Bestprices",
                    "item"  => SITE_WEBROOT
                ],
                (Object) [
                    "@type"  => "ListItem",
                    "position"  => 2,
                    "name"  => $result['subCatName'],
                    "item"  => SITE_WEBROOT . $result['catUrl']
                ], 
                (Object) [
                    "@type"  => "ListItem",
                    "position"  => 3,
                    "name"  => $name,
                    "item"  => $url
                ]
                ]
            ]
        ];
       
        echo( json_encode($data) );
    }
}
?>
