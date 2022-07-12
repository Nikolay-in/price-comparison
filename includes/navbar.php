<nav class="sb-topnav navbar navbar-expand fixed-top navbar-light bg-light" style="min-height: 56px;">
    <div class="container-fluid w-100 h-100 bg-light">
        <div id="menu" class="h-100">
            <div class="d-inline">
            <button id="sidebarToggle" class="btn btn-outline-secondary d-xl-none align-middle me-2"><i class="navbar-toggler-icon"></i></button>
            <a class="navbar-brand align-middle" href="<?php echo SITE_WEBROOT; ?>"><img src="/assets/logo.png" class="h-100" alt="<?php echo SITE_WEBROOT; ?>"></a>
            </div>
        </div>
        <div class="mx-auto col-md-5 d-none d-sm-block" style="min-width: 360px;">
            <div class="position-relative d-flex">
                <input class="form-control shadow-none searchBox" style="padding-left: 30px;" type="text" id="search" placeholder="Търси (напр. Samsung galaxy S22 ...)" aria-describedby="btnNavbarSearch" name="searchkey" />
                <span class="position-absolute opacity-75" style="top: 7px; left: 9px;" id="btnNavbarSearch" for="searchkey"><i class="far fa-search"></i></span>
            </div>
        </div>
        <div id="searchWrapper">
        <button class="btn btn-outline-secondary float-end mx-auto col-md-5 d-block d-sm-none" id="searchToggle" ><i class="far fa-search"></i></button>
        </div>
    </div>
    
    <div id="mobile-search" class="mobile-search bg-light text-center py-2 d-block d-sm-none">
        <div>
            <div class="input-group mx-auto mb-search-input">
            <input type="text" name="mobileSearch" id="mobileSearch" placeholder="Търси (напр. Samsung galaxy S22 ...)" class="form-control shadow-none mx-auto searchBox"> 
            </div>
        </div>
        <div class="input-autocomplete" style="display: none;"></div>
    </div>
</nav>