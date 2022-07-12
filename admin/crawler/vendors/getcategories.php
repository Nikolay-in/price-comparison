<?php
//Get categories
function getCategories($crawlName, $vendorId, $catdir, $uaName) {
    
    //Technomarket
    if ($crawlName == 'technomarket') {
        echo '<br /><u>Technomarket:</u> - '; 
        $mainArray = [];
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        $doc = file_get_contents($catdir, false, $context);
        $json = json_decode($doc, true);
        $menu = $json['menu'];
        foreach($menu as $category) {
            $subArray = [];
            array_push($subArray, $category['name']);
            $subMenu = $category['children'];
            foreach($subMenu as $subCat) {
                $url = 'http://technomarket.bg/api/query' . $subCat['url'] . '?p[]=1&l[]=120';
                array_push($subArray, array( $subCat['name'], $url ));
            }
            array_push($mainArray, $subArray);
        }
        AddCatsSubcats($vendorId, $mainArray);
    }

    //Technopolis
    if ($crawlName == 'technopolis') {
        echo '<br /><u>Technopolis:</u> - '; 
        $mainArray = [];
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        $doc = file_get_contents($catdir, false, $context);
        //Custom crawling
        $mainString = explode('<ul class="main-menu-list">', $doc)[1];
        $mainString = explode('<div class="menu-overlay"></div>', $mainString)[0];
        $mainString = explode('<li class="item">', $mainString);
        array_shift($mainString);
        foreach ($mainString as $cat) {
            $ul = 0;
            $subArray = [];
            $catname = explode('<div class="items-title">', $cat)[1];
            $catname = explode('<strong>', $catname)[1];
            $catname = explode('</strong>', $catname)[0];
            array_push($subArray, $catname);
            $cat = explode('<ul class="dropdown-list">', $cat)[1];
            $cat = explode('<div class="cell cell-banner">', $cat)[0];
            $subcats = explode('<li class="yCmsComponent', $cat);
            array_shift($subcats);
            foreach ($subcats as $subcat) {
                $url = explode('<a href="', $subcat)[1];
                $url = 'https://technopolis.bg' . explode('"', $url)[0] . '?pageselect=90&page=0';
                $name = explode('title="', $subcat)[1];
                $name = explode('"', $name)[0];
                array_push($subArray, array( $name, $url ));
            }
            array_push($mainArray, $subArray);
        }
        //Add to database
        AddCatsSubcats($vendorId, $mainArray);
    }

    //Bros
    if ($crawlName == 'bros') {
        echo '<br /><u>Bros:</u> - '; 
        $mainArray = [];
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        $doc = file_get_contents($catdir, false, $context);
        //Custom crawling
        $mainString = explode('sidebar-box-content">', $doc)[1];
        $mainString = explode('</div>', $mainString)[0];
        $mainString = explode('<li><a href="', $mainString);
        array_shift($mainString);
        $tempArray = [];
        foreach ($mainString as $cat) {
            $name = explode('<', $cat)[0];
            $name = explode('>', $name)[1];
            $url = explode('"', $cat)[0] . '/date/available/0/filter&pp=80';
            if (substr_count($cat, 'icon-right-dir') > 0) {
                array_push($tempArray, $name);
                if (substr_count($cat, '<ul>') == 0) {
                    array_push($tempArray, array($name, $url));
                    array_push($mainArray, $tempArray);
                    $tempArray = [];
                }
            } else {
                array_push($tempArray, array($name, $url));
                if (substr_count($cat, '</ul></li></ul>') > 0) {
                    array_push($mainArray, $tempArray);
                    $tempArray = [];
                }
            }
        }
        //Add to database
        AddCatsSubcats($vendorId, $mainArray);
    }

    //Zora
    if ($crawlName == 'zora') {
        echo '<br /><u>Zora:</u> - '; 
        $mainArray = [];
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        $doc = file_get_contents($catdir, false, $context);
        $dom = new DOMdocument();
        $dom->loadHTML($doc, LIBXML_NOERROR | LIBXML_NOWARNING);
        unset($doc);
        //Create Dom Path
        $finder = new DomXPath($dom);
        //Custom crawling
        $finder = new DomXPath($dom);
        $cats = $finder->query("//div[contains(@data-filter-box, 'categories')]/div/div/ul/li");
        foreach ($cats as $cat) {
            $name = $cat->getElementsByTagName('a')[0]->textContent; 
            $subArray = [];
            array_push($subArray, $name);
            $subCats = $cat->getElementsByTagName('ul')[0]->getElementsByTagName('a');
            foreach ($subCats as $subCat) {
                $url = 'https://zora.bg' . $subCat->getAttribute('href') . '?per-page=24&page=1';
                $subCatName = $subCat->textContent;
                array_push($subArray, array ($subCatName, $url));
            }
            array_push($mainArray, $subArray);
        }
        //Add to database
        AddCatsSubcats($vendorId, $mainArray);
    }

    //Buybest
    if ($crawlName == 'buybest') {
        echo '<br /><u>Buybest:</u> - '; 
        $mainArray = [];
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n") , "ssl"=>array("verify_peer"=>false, "verify_peer_name"=>false));
        $context = stream_context_create($opts);
        $doc = file_get_contents('https://buybest.bg/', false, $context);
        //Custom crawling
        $dom = new DOMdocument();
        $dom->loadHTML($doc, LIBXML_NOERROR);
        unset($doc);
        //Create Dom Path
        $finder = new DomXPath($dom);
        $cats = $finder->query("//ul[contains(@class, 'nav-menu')]/li/a");
        foreach ($cats as $cat) {
            $subArray = [];
            $url = $cat->getAttribute('href') . '?per-page=24&page=1';
            $name = $cat->textContent;
            array_push($subArray, $name);
            array_push($subArray, array ($name, $url));
            array_push($mainArray, $subArray);
        }
        //Add to database
        AddCatsSubcats($vendorId, $mainArray);
    }

    //Ozone
    if ($crawlName == 'ozone') {
        echo '<br /><u>Ozone:</u> - '; 
        $mainArray = [];
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        $doc = file_get_contents($catdir, false, $context);
        //Custom crawling
        $dom = new DOMdocument();
        $dom->loadHTML($doc, LIBXML_NOERROR);
        unset($doc);
        //Create Dom Path
        $finder = new DomXPath($dom);
        $cats = $finder->query("//div[@class='main-nav-sub']/ul[contains(@class, 'main-cats-list')]/li");
        $tempArray = [];
        foreach ($cats as $cat) {
            $name = trim($cat->getElementsByTagName('h3')[0]->textContent);
            array_push($tempArray, $name);
            $subcats = $cat->getElementsByTagName('ul');
        foreach ($subcats as $subcat) {
            $link = $subcat->getElementsByTagName('a')[0];
            $url = $link->getAttribute('href') . '?limit=100&p=1';
            $name = trim($link->textContent);
            array_push($tempArray, array($name, $url));
        }
        array_push($mainArray, $tempArray);
        $tempArray = [];
         }
        //Add to database
        AddCatsSubcats($vendorId, $mainArray);
    }

    //Techmart
    if ($crawlName == 'techmart') {
        echo '<br /><u>Techmart:</u> - '; 
        $mainArray = [];
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n") , "ssl"=>array("verify_peer"=>false, "verify_peer_name"=>false));
        $context = stream_context_create($opts);
        $doc = file_get_contents($catdir, false, $context);
        //Custom crawling
        $mainString = explode('<div class="htmlSitemap">', $doc)[1];
        $mainString = explode('</main>', $mainString)[0];
        $mainString = explode('GDPR">', $mainString)[1];
        $mainString = explode('<div class="mapLevel1"', $mainString);
        array_shift($mainString);
        foreach ($mainString as $cat) {
            $subArray = [];
            $catName = explode('<a href="', $cat)[1];
            $catName = explode('">', $catName)[1];
            $catName = trim(explode('</a>', $catName)[0]);
            array_push($subArray, $catName);
            $subcats = explode('<div class="mapLevel', $cat);
            array_shift($subcats);
            for ($i = 0; $i < count($subcats); $i++) {
                $level = substr($subcats[$i], 0, 1);
                if (isset($subcats[$i+1])) {
                    $nextLevel = substr($subcats[$i+1], 0, 1);
                    if ($nextLevel > $level) {
                        continue;
                    }
                }
                $url = explode('<a href="', $subcats[$i])[1];
                $url = explode('">', $url)[0] . '/paging/0/limit/48/';
                $name = explode('<a href="', $subcats[$i])[1];
                $name = explode('">', $name)[1];
                $name = trim(explode('</a>', $name)[0]);
                array_push($subArray, array($name, $url));
            }
            array_push($mainArray, $subArray);
        }
        //Add to database
        AddCatsSubcats($vendorId, $mainArray);
    }

    //Ofisite
    if ($crawlName == 'ofisite') {
        echo '<br /><u>Ofisite:</u> - '; 
        $mainArray = [];
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n") , "ssl"=>array("verify_peer"=>false, "verify_peer_name"=>false));
        $context = stream_context_create($opts);
        $doc = file_get_contents($catdir, false, $context);
        //Custom crawling
        $mainString = explode('_navbar-inner">', $doc)[1];
        $mainString = explode('<div class="_header-fixed js-header-fixed"', $mainString)[0];
        $mainString = explode('<li ', $mainString);
        array_shift($mainString);
        $ul = 0;
        $tempArray = [];
        foreach ($mainString as $cat) {
            if ($ul == 0) {
                $name = explode('_figure-stack-label">', $cat)[1];
                $name = explode('</span>', $name)[0];
                array_push($tempArray, $name);
                if (substr_count($cat, '<ul') > 0) {
                    $ul += substr_count($cat, '<ul');
                } else {
                    $url = explode('href="', $cat)[1];
                    $url = explode('"', $url)[0];
                    array_push($tempArray, array($name , $url));
                    array_push($mainArray, $tempArray);
                    $tempArray = [];
                }
            } else if ($ul > 0 && substr_count($cat, '<ul') == 0) {
                $name = explode('_figure-stack-label">', $cat)[1];
                $name = explode('</span>', $name)[0];
                $url = explode('href="', $cat)[1];
                $url = explode('"', $url)[0] . '?per_page=96&page=1';
                array_push($tempArray, array($name , $url));
                if (substr_count($cat, '</ul>') > 0) {
                    $ul -= substr_count($cat, '</ul>');
                    if ($ul == 0) {
                        array_push($mainArray, $tempArray);
                        $tempArray = [];
                    } 
                }
            } else if ($ul > 0 && substr_count($cat, '<ul') > 0) {
                $ul += substr_count($cat, '<ul');
            } 
        }
        //Add to database
        AddCatsSubcats($vendorId, $mainArray);
    }

    //Technika
    if ($crawlName == 'technika') {
        echo '<br /><u>Technika:</u> - '; 
        $mainArray = [];
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        $doc = file_get_contents($catdir, false, $context);
        $dom = new DOMdocument();
        $dom->loadHTML($doc, LIBXML_NOERROR | LIBXML_NOWARNING);
        unset($doc);
        //Create Dom Path
        $finder = new DomXPath($dom);
        //Custom crawling
        $cats = $finder->query("//div[@id='box-13']/ul/li[contains(@class, 'c-box-dd-categories__item')]");
        $tempArray = [];
        foreach ($cats as $cat) {
            if ($cat->getElementsByTagName('span')[0]->textContent == 'ПРОМО') {
                continue;
            }
            $name = $cat->getElementsByTagName('span')[0]->textContent;
            array_push($tempArray, $name);
            if ($cat->getElementsByTagName('ul')->length > 0) {
                foreach ($cat->getElementsByTagName('ul')[0]->childNodes as $subCat) {
                    $url = 'https://technika.bg' . $subCat->getElementsByTagName('a')[0]->getAttribute('href') . '?recordsPerPage=100&page=1';
                    $name = $subCat->getElementsByTagName('span')[0]->textContent;
                    array_push($tempArray, array($name , $url));
                }
                array_push($mainArray, $tempArray);
                $tempArray = [];
            } else {
                $url = 'https://technika.bg' . $cat->getElementsByTagName('a')[0]->getAttribute('href') . '?recordsPerPage=100&page=1';
                array_push($tempArray, array($name , $url));
                array_push($mainArray, $tempArray);
                $tempArray = [];
            }
        }
        //Add to database
        AddCatsSubcats($vendorId, $mainArray);
    }

    //Stokatastoki
    if ($crawlName == 'stokatastoki') {
        echo '<br /><u>Stokatastoki:</u> - '; 
        $mainArray = [];
        $opts = array ('http' => array('header' => "User-Agent: $uaName\r\n"));
        $context = stream_context_create($opts);
        $doc = file_get_contents($catdir, false, $context);
        //Custom crawling
        $mainString = explode('mid-bar navbar-nav">', $doc)[1];
        $mainString = explode('<div class="desktop-logo-wrapper"', $mainString)[0];
        $mainString = explode('j-menu">', $mainString)[3];
        $mainString = explode('<div class="grid-item grid-item-1">', $mainString);
        array_shift($mainString);
        $tempArray = [];
        foreach ($mainString as $cat) {
            if (substr_count($cat, 'title module-title') > 0) {
                $name = explode('title module-title">', $cat)[1];
                $name = explode('</h3>', $name)[0];
                array_push($tempArray, $name);
                $subcats = explode('<ul class="module-body">', $cat)[1];
                $subcats = explode('</ul>', $subcats)[0];
                $subcats = explode('<li ', $subcats);
                array_shift($subcats);
                foreach ($subcats as $subcat) {
                    $name = explode('links-text">', $subcat)[1];
                    $name = explode('</span>', $name)[0];
                    $url = '';
                    if (substr_count($subcat, 'href="') > 0) {    
                        $url = explode('href="', $subcat)[1];
                        $url = explode('"', $url)[0] . '/page-1?limit=96'; 
                    }
                    array_push($tempArray, array($name , $url));
                }
                array_push($mainArray, $tempArray);
                $tempArray = [];
            }
        }
        //Add to database
        AddCatsSubcats($vendorId, $mainArray);
    }
}
?>