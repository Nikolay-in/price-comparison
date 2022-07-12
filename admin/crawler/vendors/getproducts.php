<?php
//Get products
function getProducts($crawlName, $vendorId, $url, $assign, $uaName, $delString = null, $subCatID) {
    
    //Technomarket
    if ($crawlName == 'technomarket') {
        //Settings
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);

        $currPage = 1;
        $totalPages = 1;
        $productIDs = [];
        //Continue through pages
        while ($currPage <= $totalPages) {
            //Prepare url
            $replacement = "p[]=" . $currPage .  "&l[]=";
            $link1 = explode("p[]=", $url)[0];
            $link2 = explode("&l[]=", $url)[1];
            $url = $link1 . $replacement . $link2;
            //Get content
            $doc = file_get_contents($url, false, $context);
            $json = json_decode($doc, true);
            //Get max pages
            if ($currPage == 1) {
                $stats = $json['stats'];
                $totalItems = $stats['totalItems'];
                $perPage = $stats['perPage'];
                $totalPages = ceil($totalItems / $perPage);
                echo ' Total Pages: ' . $totalPages . '<br>'; 
            }
            //Get product IDs in array 60 products in a string
            $resultIDs = $json['result'];
            while (count($resultIDs) > 0) {
                $newArr = array_splice($resultIDs, 0, 60);
                array_push($productIDs, join('', $newArr)); 
            }
            //Prepare next page
            $currPage++;
        }

        while ($ids = array_shift($productIDs)) {
            $url = 'https://www.technomarket.bg/api/page/productIds?p=' . $ids;
            //Get content
            $doc = file_get_contents($url, false, $context);
            $json = json_decode($doc, true);
            $products = [];
            foreach ($json as $product) {
                $pid = $product['code'];
                $products[$pid] = [];
                $products[$pid]['ean'] = $product['ean'];
                $products[$pid]['title'] = $product['type'] . ' ' . $product['title'];
                $products[$pid]['model'] = trim(substr($product['title'], (strpos(strtoupper($product['title']), strtoupper($product['manufacturer'])) + strlen($product['manufacturer']))));
                //$products[$pid]['modelStrip'] = preg_replace('/[^a-zA-Z0-9]+/', '', $products[$pid]['model']);
                $products[$pid]['modelStrip'] = 'null';
                $products[$pid]['url'] = 'https://www.technomarket.bg' . $product['url'];
                $products[$pid]['images'][0] = 'https://cdn.technomarket.bg/ng/media/cache/mid_thumb' . $product['image'] . '.webp';
                foreach ($product['gallery'] as $pic) {
                    $pic = 'https://cdn.technomarket.bg/ng/media/cache/mid_thumb' . $pic . '.webp';
                    array_push($products[$pid]['images'], $pic);
                }
                $products[$pid]['price'] = $product['priceInfo']['price'];
                $products[$pid]['oldPrice'] = $product['priceInfo']['old_price'];
                $products[$pid]['manufacturer'] = $product['manufacturer'];
                $products[$pid]['description'] = str_replace(array("\r\n", "\n\n", "\n", "\r"), ' ; ', $product['description']);
                $products[$pid]['description'] = str_replace(' ; 36 МЕСЕЦА ГАРАНЦИЯ', '', $products[$pid]['description']);
                $products[$pid]['description'] = str_replace(' ;  ; ', ' ; ', $products[$pid]['description']);
                $products[$pid]['description'] = str_replace('•', '', $products[$pid]['description']);
                $products[$pid]['description'] = explode(' ; ',  $products[$pid]['description']);
                $products[$pid]['description'] = array_map('trim', $products[$pid]['description']);
                $products[$pid]['description'] = array_filter($products[$pid]['description'], function($v, $k) { return $v != ''; }, ARRAY_FILTER_USE_BOTH );
                $products[$pid]['description'] = serialize($products[$pid]['description']);
                $products[$pid]['specs'] = $product['specifications'];
            }
            //Send products to function
            addProducts($vendorId, $products, $assign, $context, $delString, $subCatID);
        }
    }

    //Technopolis
    if ($crawlName == 'technopolis') {
        //Settings
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        //Pages start from 0 in technopolis :)
        $currPage = 0;
        $totalPages = 0;
        $brands = [];
        //Continue through pages
        while ($currPage <= $totalPages) {
            //Prepare url
            $url = substr_replace($url, '&page=' . $currPage, strpos($url, '&page='));
            //Get content and retry test
            $retry = 0;
            $doc = '';
            while ($doc == '' && $retry < 3) {
                $doc = file_get_contents($url, false, $context);
                $retry++;
            }
            if ($doc == '') {
                echo 'Failed <b>' . $retry . '</b> times to connect to <b>' . $url . '</b> proceeding to next url in queue.<br> ';
                break;
            }
            $dom = new DOMdocument();
            $dom->loadHTML($doc, LIBXML_NOERROR);
            unset($doc);
            //Create Dom Path
            $finder = new DomXPath($dom);
            $h1s = $dom->getElementsByTagName('h1');
            

            if ($currPage == 0) {
                //Get pages count
                $perPage = explode('pageselect=', $url)[1];
                $perPage = intval(explode('&page=', $perPage)[0]);
                $totalItems = intval(explode(': ', $h1s[0]->textContent)[1]);
                $totalPages = ceil($totalItems / $perPage);
                $totalPages--;
                echo ' Total Pages: ' . $totalPages . '<br>'; 

                //Get brands
                $brandList = $finder->query("//div[contains(@class, 'filter-box') and contains(.//p, 'Марка')]/div[@class='filter-box-content']//div[@class='checkbox-item']");
                foreach ($brandList as $brand) {
                    array_push($brands, trim($brand->textContent));
                }
            }

            //Collect products
            $gridList = $finder->query("//li[div[@data-productid]]");
            $products = [];
            foreach ($gridList as $item) {
                $divs = $item->getElementsByTagName('div');
                foreach ($divs as $div) {
                    if ($div->hasAttribute('data-productid')) {
                        $pid = $div->getAttribute('data-productid');
                        $pid = "0$pid";
                        $products[$pid] = [];
                        $products[$pid]['ean'] = 'null';
                        $prodName = $div->getAttribute('data-productname');
                        $products[$pid]['title'] = $div->getAttribute('data-productname');
                        $brandName = 'Unknown-' . $pid;
                        foreach ($brands as $brand) {
                            $brandIndex = strpos(strtoupper($prodName), strtoupper($brand . ' '));
                            if ($brandIndex !== false) {
                                $prodName = trim(substr($prodName, $brandIndex));
                                $prodName = trim(substr_replace($prodName, '', 0, strlen($brand)));
                                $brandName = $brand;
                                break;
                            }
                        }
                        $products[$pid]['model'] = $prodName;
                        $products[$pid]['modelStrip'] = 'null';
                        $products[$pid]['manufacturer'] = $brandName;
                        $products[$pid]['price'] = $div->getAttribute('data-productprice');
                        $products[$pid]['oldPrice'] = '0';
                    }
                    if ($div->getAttribute('class') == 'preview' && !isset($products[$pid]['url']) && !isset($products[$pid]['images'])) {
                        $link = $div->getElementsByTagName('a');
                        if ($link->length > 0) {
                            $products[$pid]['url'] = 'https://www.technopolis.bg' . $link->item(0)->getAttribute('href');
                            $img = $div->getElementsByTagName('img');
                            $products[$pid]['images'][0] = 'https://www.technopolis.bg' . $img->item(0)->getAttribute('data-src');
                        }
                    }
                    $products[$pid]['specs'] = 'null';
                    if ($div->getAttribute('class') == 'item-features') {
                        $products[$pid]['description'] = [];
                        $lis = $div->getElementsByTagName('li');
                        foreach ($lis as $li) {
                            array_push($products[$pid]['description'], trim($li->nodeValue));
                        }
                        $products[$pid]['description'] = serialize($products[$pid]['description']);
                    }
                }
            }
            $currPage++;
            //Send products to function
            addProducts($vendorId, $products, $assign, $context, $delString, $subCatID);
        }
    }

    if ($crawlName == 'techmart') {
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n") , "ssl"=>array("verify_peer"=>false, "verify_peer_name"=>false));
        $context = stream_context_create($opts);
        $urlArr = explode('/', $url);
        $i = count($urlArr) - 4;
        $firstPage = $urlArr[$i];
        $currPage = $urlArr[$i];
        $totalPages = $currPage;
        $limitIndex = count($urlArr) - 2;
        $limit = $urlArr[$limitIndex];
        $brands = [];
        //Continue through pages
        while ($currPage <= $totalPages) {
            //Prepare url
            $urlArr[$i] = $currPage;
            $url = implode('/', $urlArr);
            //Get content
            $doc = file_get_contents($url, false, $context);
            $dom = new DOMdocument();
            $dom->loadHTML($doc, LIBXML_NOERROR);
            unset($doc);
            //Create Dom Path
            $finder = new DomXPath($dom);
            if ($currPage == $firstPage) {
                //Get pages count
                $doubleRightArrow = $finder->query("//ul/li/a/i[contains(@class, 'fa-angle-double-right')]");
                $rightArrow = $finder->query("//ul/li/a/i[contains(@class, 'fa fa-angle-right')]");
                if ($doubleRightArrow->length > 0) {
                    $pagination = $doubleRightArrow->item(0);
                } else if ($rightArrow->length > 0) {
                    $pagination = $rightArrow->item(0);
                }
                if (isset($pagination)) {
                    $totalPages = $pagination->parentNode;
                    $totalPages = $totalPages->getAttribute('href');
                    $totalPages = explode('/', $totalPages);
                    $totalPages = end($totalPages);
                }
                echo ' Total Pages: ' . $totalPages / $limit . '<br>'; 
                //Get brands
                $brandList = $finder->query("//div[contains(@class, 'form-group') and .//label/h5/b/text() = 'Марка']/div/div/label/span");
                foreach ($brandList as $brand) {
                    array_push($brands, trim($brand->textContent));
                }
            }

            //Collect products
            //pid , title, manufacturer, model, image, price, url
            $gridList = $finder->query("//div[@class='itemsHolder']");
            $products = [];
            foreach ($gridList as $item) {
                $divs = $item->getElementsByTagName('div');
                $price = 0;
                foreach ($divs as $div) {
                    if ($div->getAttribute('class') == 'itemsPicsHolder') {
                        $url = $div->getElementsByTagName('a');
                        $url = $url[0]->getAttribute('href');
                    }
                    if ($div->getAttribute('class') == 'lazyLoadingHolder') {
                        $title = $div->getAttribute('data-title');
                        $image = $div->getAttribute('data-src');
                        $data = explode('/', $image);
                        $pid = $data[7];
                        $pid = "0$pid";
                    }
                    if (strpos($div->getAttribute('class'), 'gridPrices') !== false) {
                        $div = $div->getElementsByTagName('span');
                        $price = $div[0]->textContent;
                    }
                }
                $products[$pid] = [];
                $prodName = $title;
                $products[$pid]['title'] = $title;
                $brandName = 'Unknown-' . $pid;
                foreach ($brands as $brand) {
                    $brandIndex = strpos(strtoupper($prodName), strtoupper($brand . ' '));
                    if ($brandIndex !== false) {
                        $prodName = trim(substr($prodName, $brandIndex + strlen($brand)));
                        $brandName = $brand;
                        break;
                    }
                }
                $products[$pid]['model'] = $prodName;
                $products[$pid]['manufacturer'] = $brandName;
                $products[$pid]['price'] = $price;
                $products[$pid]['images'][0] = $image;
                $products[$pid]['url'] = $url;
            }
            $currPage += $limit;
            //Send products to function
            addProducts($vendorId, $products, $assign, $context, $delString, $subCatID);
        }
    }

    if ($crawlName == 'zora') {
        //Settings
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        //Pages
        $firstPage = 1;
        $currPage = 1;
        $totalPages = 2;
        $brands = [];
        //Continue through pages
        while ($currPage < $totalPages) {
            //Prepare url
            $url = explode('page=', $url)[0] . 'page=' . $currPage . '&per_page=50';
            //Get content
            $doc = file_get_contents($url, false, $context);
            $dom = new DOMdocument();
            $dom->loadHTML($doc, LIBXML_NOERROR);
            unset($doc);
            //Create Dom Path
            $finder = new DomXPath($dom);
            if ($currPage == $firstPage) {
                //Get pages count
                $totalPages = $finder->query("//ul[contains(@class, 'pagination')]/li[@class='last']/a");
                if ($totalPages->length > 0) {
                    $totalPages = $totalPages[0]->getAttribute('data-ajax-page');
                } else {
                    $totalPages = 1;
                }
                echo ' Total Pages: ' . $totalPages . '<br>'; 
                //Get brands
                $brandList = $finder->query("//ul[contains(@class, '_filter-vendors-list')]/li/a[contains(@class, '_filter-vendors-list-item-link')]");
                foreach ($brandList as $brand) {
                    array_push($brands, trim($brand->textContent));
                }
            }

            //Collect products
            //pid , title, manufacturer, model, image, price, url
            $gridList = $finder->query("//div[@class='_product-inner']");
            $products = [];
            foreach ($gridList as $item) {
                $links = $item->getElementsByTagName('a');
                $link = $links[0]->getAttribute('href');
                $image = $links[0]->getElementsByTagName('img');
                $image = $image[0]->getAttribute('data-src');
                $pid = explode('/', $link);
                $pid = end($pid);
                $title = $links[1]->getAttribute('title');
                if (strpos($title, '|')) {
                    $title = explode('|', $title)[0];
                }
                $divs = $item->getElementsByTagName('div');
                $price = 0;
                $priceDiscounted = '';
                $desc = [];
                foreach ($divs as $div) {
                    if ($div->getAttribute('class') == '_product-price-discounted' && $div->getElementsByTagName('del')->length > 0) {
                        $del = $div->getElementsByTagName('del');
                        $priceDiscounted = $del[0]->textContent;
                        $priceDiscounted = str_replace(['&nbsp;', ' ', 'лв.', ' '], '', $priceDiscounted);
                        $priceDiscounted = str_replace(',', '.', $priceDiscounted);
                    }
                    if ($div->getAttribute('class') == '_product-price') {
                        $price = $div->textContent;
                        $price = str_replace(['&nbsp;', ' ', 'лв.', ' '], '', $price);
                        $price = str_replace(',', '.', $price);
                    }
                    if ($div->getAttribute('class') == '_product-details-properties') {
                        $lis = $div->getElementsByTagName('li');
                        foreach ($lis as $li) {
                            $lines = $li->getElementsByTagName('span');
                            if ($lines->length > 0) {
                                $line = $lines[0]->textContent . ' ' . $lines[1]->textContent;
                                array_push($desc, $line);
                            }
                        }
                    }
                }
                $oldPrice = 0;
                if ($priceDiscounted != '') {
                    $oldPrice = $price;
                    $price = $priceDiscounted;
                }

                $products[$pid] = [];
                $prodName = $title;
                $products[$pid]['title'] = $title;
                $brandName = 'Unknown-' . $pid;
                foreach ($brands as $brand) {
                    $brandIndex = strpos(strtoupper($prodName), strtoupper($brand . ' '));
                    if ($brandIndex !== false) {
                        $prodName = trim(substr($prodName, $brandIndex + strlen($brand)));
                        $brandName = $brand;
                        break;
                    }
                }
                if (count($desc) > 0) {
                    $products[$pid]['description'] = serialize($desc);
                }
                $products[$pid]['model'] = $prodName;
                $products[$pid]['manufacturer'] = $brandName;
                $products[$pid]['price'] = $price;
                $products[$pid]['oldPrice'] = $oldPrice;
                $products[$pid]['images'][0] = explode('?', $image)[0];
                $products[$pid]['url'] = $link;
            }
            $currPage++;
            //Send products to function
            addProducts($vendorId, $products, $assign, $context, $delString, $subCatID);
        }
    }

    if ($crawlName == 'buybest') {
        //Settings
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n") , "ssl"=>array("verify_peer"=>false, "verify_peer_name"=>false));
        $context = stream_context_create($opts);
        //Pages
        $firstPage = 1;
        $currPage = 1;
        $totalPages = $currPage;
        $brands = [];
        //Continue through pages
        while ($currPage <= $totalPages) {
            //Prepare url
            $url = explode('&page=', $url)[0] . '&page=' . $currPage;
            //Get content
            $doc = file_get_contents($url, false, $context);
            $dom = new DOMdocument();
            $dom->loadHTML($doc, LIBXML_NOERROR);
            unset($doc);
            //Create Dom Path
            $finder = new DomXPath($dom);
            if ($currPage == $firstPage) {
                //Get pages count
                $totalPages = $finder->query("//ul[contains(@class, 'pagination')]/li/a");
                if ($totalPages->length > 0) {
                    $length = $totalPages->length;
                    $totalPages = $totalPages[$length - 2]->textContent;
                } else {
                    $totalPages = 1;
                }
                echo ' Total Pages: ' . $totalPages . '<br>'; 
                //Get brands
                $brandList = $finder->query("//form//label[contains(@for, 'manufacturer')]");
                foreach ($brandList as $brand) {
                    array_push($brands, trim($brand->textContent));
                }
            }

            //Collect products
            //pid , title, manufacturer, model, image, price, url
            $items = $finder->query("//div[contains(@class, 'item-container')]");
            $products = [];
            foreach ($items as $item) {
                $links = $item->getElementsByTagName('a');
                $link = $links[0]->getAttribute('href');
                $image = $links[0]->getElementsByTagName('img')[0]->getAttribute('src');
                $pid = explode('/', $link)[3];
                $title = $links[1]->textContent;
                $prices = $item->getElementsByTagName('div')[1]->getElementsByTagName('span');
                $oldPrice = 0;
                $desc = [];
                $price = 0;
                if ($prices->length == 2) {
                    $oldPrice = trim($prices[0]->getElementsByTagName('strong')[0]->textContent . '.' . $prices[0]->getElementsByTagName('sup')[0]->textContent);
                    $price = trim($prices[1]->getElementsByTagName('strong')[0]->textContent . '.' . $prices[0]->getElementsByTagName('sup')[0]->textContent);
                } else if ($prices->length == 1) {
                    $price = trim($prices[0]->getElementsByTagName('strong')[0]->textContent . '.' . $prices[0]->getElementsByTagName('sup')[0]->textContent);
                }

                $products[$pid] = [];
                $prodName = $title;
                $products[$pid]['title'] = $title;
                $brandName = 'Unknown-' . $pid;
                foreach ($brands as $brand) {
                    $brandIndex = strpos(strtoupper($prodName), strtoupper($brand . ' '));
                    if ($brandIndex !== false) {
                        $prodName = trim(substr($prodName, $brandIndex + strlen($brand)));
                        $brandName = $brand;
                        break;
                    }
                }
                $products[$pid]['model'] = $prodName;
                $products[$pid]['manufacturer'] = $brandName;
                $products[$pid]['price'] = $price;
                $products[$pid]['oldPrice'] = $oldPrice;
                $products[$pid]['images'][0] = $image;
                $products[$pid]['url'] = $link;
            }
            $currPage++;
            //Send products to function
            addProducts($vendorId, $products, $assign, $context, $delString, $subCatID);
        }
    }

    if ($crawlName == 'bros') {
        //Settings
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        //Pages
        $firstPage = 0;
        $currPage = 0;
        $totalPages = $currPage;
        $brands = [];
        //Continue through pages
        while ($currPage <= $totalPages) {
            //Prepare url
            $url = explode('/available/', $url)[0] . '/available/' . $currPage . '/filter&pp=80';
            //Get content
            $doc = file_get_contents($url, false, $context);
            $dom = new DOMdocument();
            $dom->loadHTML($doc, LIBXML_NOERROR);
            unset($doc);
            //Create Dom Path
            $finder = new DomXPath($dom);
            if ($currPage == $firstPage) {
                //Get pages count
                $totalPages = $finder->query("//div[@class='pagination']//div[@class='page-button']");
                if ($totalPages->length > 0) {
                    $totalPages = $totalPages->item($totalPages->length - 1)->textContent;
                    $totalPages--;
                } else {
                    $totalPages = 0;
                }
                echo ' Total Pages: ' . $totalPages . '<br>'; 
                //Get brands
                $brandList = $finder->query("//div[contains(@class, 'brands-scrollable')]/a");
                foreach ($brandList as $brand) {
                    $brand = trim($brand->nodeValue);
                    if (strrpos($brand, '(')) {
                        $brand = trim(substr($brand, 0 , strrpos($brand, '(')));
                    }
                    array_push($brands, $brand);
                }
            }
    
            //Collect products
            //pid , title, manufacturer, model, image, price, url
            $items = $finder->query("//div[@class='row']/div[contains(@class, 'product')]");
            $products = [];
            foreach ($items as $item) {
                $link = $item->getElementsByTagName('a')[0]->getAttribute('href');
                $image = $item->getElementsByTagName('a')[0]->getElementsByTagName('img')[0]->getAttribute('src');
                $title = $item->getElementsByTagName('a')[0]->getElementsByTagName('img')[0]->getAttribute('alt');
                $pid = $item->getElementsByTagName('a')[0]->getAttribute('data-id');
                $pid = "0$pid";
                $price = 0;
                $oldPrice = 0;
                $spans = $item->getElementsByTagName('span');
                foreach ($spans as $span) {
                    if ($span->getAttribute('itemprop') == 'price') {
                        $price = $span->getAttribute('content');
                    }
                }
                if ($item->getElementsByTagName('del')->length > 0) {
                    $oldPrice = $item->getElementsByTagName('del')[0]->textContent;
                }
                
                $products[$pid] = [];
                $prodName = $title;
                $products[$pid]['title'] = $title;
                $brandName = '';
                $metas =  $item->getElementsByTagName('meta');
                foreach ($metas as $meta) {
                    if ($meta->hasAttribute('itemprop') && $meta->getAttribute('itemprop') == 'brand') {
                        $brandName = $meta->getAttribute('content');
                    }
                }
                if ($brandName == '') {
                    $brandName = 'Unknown-' . $pid;
                    foreach ($brands as $brand) {
                        $brandIndex = strpos(strtoupper($prodName), strtoupper($brand . ' '));
                        if ($brandIndex !== false) {
                            $prodName = trim(substr($prodName, $brandIndex + strlen($brand)));
                            $brandName = $brand;
                            break;
                        }
                    }
                }
                $products[$pid]['model'] = $prodName;
                $products[$pid]['manufacturer'] = $brandName;
                $products[$pid]['price'] = $price;
                $products[$pid]['oldPrice'] = $oldPrice;
                $products[$pid]['images'][0] = $image;
                $products[$pid]['url'] = $link;
            }
            $currPage++;
            //Send products to function
            // addProducts($vendorId, $products, $assign, $context, $delString, $subCatID);
        }
    }

    if ($crawlName == 'ozone') {
        //Settings
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        //Pages
        $firstPage = 1;
        $currPage = 1;
        $totalPages = $currPage;
        $brands = [];
        //Continue through pages
        while ($currPage <= $totalPages) {
            //Prepare url
            $url = explode('?limit=100&p=', $url)[0] . '?limit=100&p=' . $currPage;
            //Get content
            $doc = file_get_contents($url, false, $context);
            $dom = new DOMdocument();
            $dom->loadHTML($doc, LIBXML_NOERROR);
            unset($doc);
            //Create Dom Path
            $finder = new DomXPath($dom);
            //Get pages count
            $totalPages = $finder->query("//div[@class='pages']/a");
            if ($totalPages->length > 0) {
                $totalPages = $totalPages->item($totalPages->length - 2)->textContent;
            } else {
                $totalPages = 0;
            }
            if ($currPage == $firstPage) {
                //Get brands
                $brandList = $finder->query("//div[contains(@class, 'oz_common_brand')]/ul/li/label/span");
                foreach ($brandList as $brand) {
                    $brand = trim($brand->nodeValue);
                    if (strrpos($brand, '(')) {
                        $brand = trim(substr($brand, 0 , strrpos($brand, '(')));
                    }
                    array_push($brands, $brand);
                }
                echo ' Total Pages: ' . $totalPages . '<br>'; 
            }

            //Collect products
            //pid , title, manufacturer, model, image, price, url
            $items = $finder->query("//div[@class='category-products']//a[@class='product-box']");
            $products = [];
            foreach ($items as $item) {
                $link = $item->getAttribute('href');
                $image = $item->getElementsByTagName('img')[0]->getAttribute('src');
                $pid = explode('/', $link)[4];
                $title = '';
                $price = 0;
                $oldPrice = 0;
                $spans = $item->getElementsByTagName('span');
                foreach ($spans as $span) {
                    if ($span->getAttribute('class') == 'title') {
                        $title = $span->textContent;
                    }
                    if ($span->getAttribute('class') == 'price') {
                        $price = $span->textContent;
                        $price = trim(str_replace([',', '&nbsp;', ' ', 'лв.', ' '], ['.', ''], $price));
                    }
                }
                if ($item->getElementsByTagName('p')->length > 0) {
                    $oldPrice = $item->getElementsByTagName('p')[0]->getElementsByTagName('span')[1]->textContent;
                    $oldPrice = trim(str_replace([',', '&nbsp;', ' ', 'лв.', ' '], ['.', ''], $oldPrice));
                }
                $desc = '';
                if ($item->getElementsByTagName('li')->length > 0) {
                    $desc = [];
                    foreach ($item->getElementsByTagName('li') as $line) {
                        $spec = trim($line->getElementsByTagName('strong')[0]->textContent) . ' ' . trim($line->getElementsByTagName('span')[0]->textContent);
                        array_push($desc, $spec);
                    }
                    $desc = serialize($desc);
                }
                
                $products[$pid] = [];
                $prodName = $title;
                $products[$pid]['title'] = $title;
                $brandName = 'Unknown-' . $pid;
                if ($desc) { $products[$pid]['description'] = $desc; }
                foreach ($brands as $brand) {
                    $brandIndex = strpos(strtoupper($prodName), strtoupper($brand . ' '));
                    if ($brandIndex !== false) {
                        $prodName = trim(substr($prodName, $brandIndex + strlen($brand)));
                        $brandName = $brand;
                        break;
                    }
                }
                $products[$pid]['model'] = $prodName;
                $products[$pid]['manufacturer'] = $brandName;
                $products[$pid]['price'] = $price;
                $products[$pid]['oldPrice'] = $oldPrice;
                $products[$pid]['images'][0] = $image;
                $products[$pid]['url'] = $link;
            }
            $currPage++;
            //Send products to function
            addProducts($vendorId, $products, $assign, $context, $delString, $subCatID);
        }
    }

    if ($crawlName == 'technika') {
        //Settings
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        //Pages
        $firstPage = 1;
        $currPage = 1;
        $totalPages = $currPage;
        $brands = [];
        //Continue through pages
        while ($currPage <= $totalPages) {
            //Prepare url
            $url = explode('&page=', $url)[0] . '&page=' . $currPage;
            //Get content
            $doc = file_get_contents($url, false, $context);
            $dom = new DOMdocument();
            $dom->loadHTML($doc, LIBXML_NOERROR | LIBXML_NOWARNING);
            unset($doc);
            //Create Dom Path
            $finder = new DomXPath($dom);
            
            if ($currPage == $firstPage) {
                //Get pages count
                $totalPages = $finder->query("//div[@class='c-pager__pagination']")[0]->textContent;
                $totalPages = explode(' ', $totalPages);
                $totalPages = end($totalPages);
                echo ' Total Pages: ' . $totalPages . '<br>'; 
                //Get brands
                $brandList = $finder->query("//div[contains(@class, 'attribute_filter_brand')]//li/a");
                foreach ($brandList as $brand) {
                    $brand = trim($brand->nodeValue);
                    if (strrpos($brand, '(')) {
                        $brand = trim(substr($brand, 0 , strrpos($brand, '(')));
                    }
                    array_push($brands, $brand);
                }
            }

            //Collect products
            //pid , title, manufacturer, model, image, price, url
            $items = $finder->query("//div[@class='c-category__products-list']/ul/li/div/div[contains(@class, 'js-product-grid-view-content')]");
            $products = [];
            foreach ($items as $item) {
                $image = [];
                $images = $item->getElementsByTagName('img');
                foreach ($images as $img) {
                    if (strpos($img->getAttribute('class'), 'c-product-grid__product-image') !== false) {
                        array_push($image, 'https://technika.bg' . $img->getAttribute('data-image-src'));
                    }
                }
                $anchor = $item->getElementsByTagName('h3')[0]->getElementsByTagName('a')[0];
                $pid = $anchor->getAttribute('data-productid');
                $pid = "0$pid";
                $link = 'https://technika.bg' . $anchor->getAttribute('href');
                $info = $anchor->textContent;
                $desc = 'null';
                if (!strpos($info, ',')) {
                    $title = $info;
                } else {
                    $info = explode(',', $info);
                    $title = trim(array_shift($info));
                    $info = array_map('trim', $info);
                    $desc = serialize($info);
                }
                $price = 0;
                $oldPrice = 0;
                $spans = $item->getElementsByTagName('span');
                foreach ($spans as $span) {
                    if (strpos($span->getAttribute('class'), 'price-value')) {
                        $price = $span->textContent;
                        $price = trim(str_replace([',', '&nbsp;', ' ', 'лв.', ' '], [''], $price));
                        break;
                    }
                }
                if ($item->getElementsByTagName('del')->length > 0) {
                    $oldPrice = $item->getElementsByTagName('del')[0]->textContent;
                    $oldPrice = trim(str_replace([',', '&nbsp;', ' ', 'лв.', ' '], [''], $oldPrice));
                }
                               
                $products[$pid] = [];
                $prodName = $title;
                $products[$pid]['title'] = $title;
                $brandName = 'Unknown-' . $pid;
                if ($desc) { $products[$pid]['description'] = $desc; }
                foreach ($brands as $brand) {
                    $brandIndex = strpos(strtoupper($prodName), strtoupper($brand . ' '));
                    if ($brandIndex !== false) {
                        $prodName = trim(substr($prodName, $brandIndex + strlen($brand)));
                        $brandName = $brand;
                        break;
                    }
                }
                $products[$pid]['model'] = $prodName;
                $products[$pid]['manufacturer'] = $brandName;
                $products[$pid]['price'] = $price;
                $products[$pid]['oldPrice'] = $oldPrice;
                $products[$pid]['images'] = $image;
                $products[$pid]['url'] = $link;
            }
            $currPage++;
            //Send products to function
            addProducts($vendorId, $products, $assign, $context, $delString, $subCatID);
        }
    }

    if ($crawlName == 'ofisite') {
        //Settings
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n") , "ssl"=>array("verify_peer"=>false, "verify_peer_name"=>false));
        $context = stream_context_create($opts);
        //Pages
        $firstPage = 1;
        $currPage = 1;
        $totalPages = 1;
        $brands = [];
        //Continue through pages
        while ($currPage <= $totalPages) {
            //Prepare url
            $url = explode('&page=', $url)[0] . '&page=' . $currPage;
            //Get content
            $doc = file_get_contents($url, false, $context);
            $dom = new DOMdocument();
            $dom->loadHTML($doc, LIBXML_NOERROR | LIBXML_NOWARNING);
            unset($doc);
            //Create Dom Path
            $finder = new DomXPath($dom);
            
            if ($currPage == $firstPage) {
                //Get pages count
                $totalPages = $finder->query("//div[@class='_pagination']/ul");

                if ($totalPages->length > 0) {
                    $totalPages = $totalPages[0]->getAttribute('data-pages');
                } else {
                    $totalPages = 1;
                }
                echo ' Total Pages: ' . $totalPages . '<br>'; 
                //Get brands
                $brandList = $finder->query("//div[contains(@class, '_filter-vendors')]/ul/li/label");
                foreach ($brandList as $brand) {
                    $brand = trim($brand->nodeValue);
                    if (strrpos($brand, '(')) {
                        $brand = trim(substr($brand, 0 , strrpos($brand, '(')));
                    }
                    array_push($brands, $brand);
                }
            }

            //Collect products
            //pid , title, manufacturer, model, image, price, url
            $items = $finder->query("//script[@id='js-cc-page-data']")[0]->textContent;
            $items = explode('cc_page_data = ', $items)[1];
            $items = trim(explode(';', $items)[0]);
            $items = json_decode($items, true);
            $products = [];
            foreach ($items['products'] as $item) {
                $image[0] = explode('?', $item['image_url'])[0];
                $link = $item['url'];
                $pid = $item['parameter_id'];
                $pid = "0$pid";
                $title = $item['name'];
                $price = 0;
                $oldPrice = 0;
                if ($item['discount_price'] !== '') {
                    $price = $item['discount_price'];
                    $oldPrice = $item['price'];
                } else if ($item['price'] == null) {
                    continue;
                } else {
                    $price = $item['price'];
                }                             
                $products[$pid] = [];
                $prodName = $title;
                $products[$pid]['title'] = $title;
                $brandName = 'Unknown-' . $pid;
                foreach ($brands as $brand) {
                    $brandIndex = strpos(strtoupper($prodName), strtoupper($brand . ' '));
                    if ($brandIndex !== false) {
                        $prodName = trim(substr($prodName, $brandIndex + strlen($brand)));
                        $brandName = $brand;
                        break;
                    }
                }
                $products[$pid]['model'] = $prodName;
                $products[$pid]['manufacturer'] = $brandName;
                $products[$pid]['price'] = $price;
                $products[$pid]['oldPrice'] = $oldPrice;
                $products[$pid]['images'] = $image;
                $products[$pid]['url'] = $link;
            }
            $currPage++;
            //Send products to function
            addProducts($vendorId, $products, $assign, $context, $delString, $subCatID);
        }
    }

    if ($crawlName == 'stokatastoki') {
        //Settings
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n") , "ssl"=>array("verify_peer"=>false, "verify_peer_name"=>false));
        $context = stream_context_create($opts);
        //Pages
        $firstPage = 1;
        $currPage = 1;
        $totalPages = $currPage;
        $brands = [];
        //Continue through pages
        while ($currPage <= $totalPages) {
            //Prepare url
            $url = explode('/page-', $url)[0] . '&page=' . $currPage . '?limit=96';
            //Get content
            $doc = file_get_contents($url, false, $context);
            $dom = new DOMdocument();
            $dom->loadHTML($doc, LIBXML_NOERROR | LIBXML_NOWARNING);
            unset($doc);
            //Create Dom Path
            $finder = new DomXPath($dom);
            
            if ($currPage == $firstPage) {
                //Get pages count
                $totalPages = $finder->query("//ul[@class='pagination']");
                if ($totalPages->length > 0) {
                    $totalPages = $totalPages[0]->lastChild->getElementsByTagName('a')[0]->getAttribute('href');
                    $totalPages = explode('/page-', $totalPages)[1];
                    $totalPages = explode('?', $totalPages)[0];
                } else {
                    $totalPages = 1;
                }
                echo ' Total Pages: ' . $totalPages . '<br>'; 
                //Get brands
                $brandList = $finder->query("//li[@data-id='manufacturers']//a[@class='mfp-value-link']");
                foreach ($brandList as $brand) {
                    $brand = trim($brand->nodeValue);
                    if (strrpos($brand, '(')) {
                        $brand = trim(substr($brand, 0 , strrpos($brand, '(')));
                    }
                    array_push($brands, $brand);
                }
            }

            //Collect products
            //pid , title, manufacturer, model, image, price, url
            $items = $finder->query("//div[contains(@class, 'main-products')]//div[@class='product-thumb']");
            $products = [];
            foreach ($items as $item) {
                //First check if it is in stock
                $inStock = $item->getElementsByTagName('b');
                if ($inStock->length > 0) {
                    if ($inStock[0]->textContent == "Временно изчерпан" || $inStock[0]->textContent == "Скоро, очаква се...") {
                        continue;
                    }
                }  
                $image[0] = $item->getElementsByTagName('img')[0]->getAttribute('src');
                $link = $item->getElementsByTagName('a')[1]->getAttribute('href');
                $link = explode('?', $link)[0];
                $anchors = $item->getElementsByTagName('a');
                $pid = '';
                foreach ($anchors as $anchor) {
                    if ($anchor->hasAttribute('data-product_id')) {
                        $pid = $anchor->getAttribute('data-product_id');
                        $pid = "0$pid";
                    }
                }
                $divs = $item->getElementsByTagName('div');
                $title = '';
                $price = 0;
                $oldPrice = 0;
                foreach ($divs as $div) {
                    if ($div->getAttribute('class') == 'name') {
                        $title = $div->textContent;
                    }
                    if ($div->getAttribute('class') == 'price') {
                        $price = $div->getElementsByTagName('span')[0]->textContent;
                        $price = trim(str_replace([',', '&nbsp;', ' ', 'лв.', ' '], [''], $price));
                    }
                }
                                            
                $products[$pid] = [];
                $prodName = $title;
                $products[$pid]['title'] = $title;
                $brandName = 'Unknown-' . $pid;
                foreach ($brands as $brand) {
                    $brandIndex = strpos(strtoupper($prodName), strtoupper($brand . ' '));
                    if ($brandIndex !== false) {
                        $prodName = trim(substr($prodName, $brandIndex + strlen($brand)));
                        $brandName = $brand;
                        break;
                    }
                }
                $products[$pid]['model'] = $prodName;
                $products[$pid]['manufacturer'] = $brandName;
                $products[$pid]['price'] = $price;
                $products[$pid]['oldPrice'] = $oldPrice;
                $products[$pid]['images'] = $image;
                $products[$pid]['url'] = $link;
            }
            $currPage++;
            //Send products to function
            addProducts($vendorId, $products, $assign, $context, $delString, $subCatID);
        }
    }
}
?>