// Initiate sidenav and search functionality
$(document).ready(() => {

    // Toggle sidenav
    // Uncomment Below to persist sidebar toggle between refreshes
    // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
    //     document.body.classList.toggle('sb-sidenav-toggled');
    // }
    $('#sidebarToggle').click(() => {
        document.body.classList.toggle('sb-sidenav-toggled');
        localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        if ($('body').hasClass('sb-sidenav-toggled')) {
            $('body').toggleClass('search-toggled', false);
            searchButtonState();
        }
    });

    // Collapse sidenav between clicks
    $('button.btn-toggle').click(() => {
        $('div.collapse').collapse('hide');
    });

    // Hide sidenav or mobile search bar on outside click
    $('#layoutSidenav_content').click(function () {
        if ($('body').hasClass('sb-sidenav-toggled') || $('body').hasClass('search-toggled')) {
            $('body').toggleClass(['sb-sidenav-toggled', 'search-toggled'], false);
            searchButtonState();
        }
    });

    // On search toggle - hide sidenav
    $('#searchToggle').click(function () {
        $('body').toggleClass('search-toggled');
        if ($('body').hasClass('search-toggled')) {
            $('body').toggleClass('sb-sidenav-toggled', false);
        }
        searchButtonState();
    });

    // Show selected category in sidenav between refreshes
    if (typeof catId !== 'undefined') {
        $('div#collapse-' + catId).collapse('show');
    }

    // Initiate search autocomplete functionality
    function extractLast(term) {
        return term.split(/,\s*/).pop();
    }

    // Apply to both desktop and mobile search box
    $(".searchBox").each(function () {
        // don't navigate away from the field on tab when selecting an item
        $(this).on("keydown", function (event) {
            if (event.keyCode === $.ui.keyCode.TAB &&
                $(this).autocomplete("instance").menu.active) {
                event.preventDefault();
            }
            let width = $(this).outerWidth();
            $(this).autocomplete("instance")._resizeMenu = function () {
                this.menu.element.outerWidth(width);
            }
        })
            .autocomplete({
                delay: 800,
                minLength: 2,
                source: function (request, response) {
                    $.getJSON("/search.php", {
                        term: extractLast(request.term)
                    }, response);
                },
                search: function () {
                    // Fire google search event
                    gtag('event', 'search', {
                        'search_term': this.value
                    });
                    // custom minLength
                    var term = extractLast(this.value);
                    if (term.length < 2) {
                        return false;
                    }
                },
                focus: function () {
                    // prevent value inserted on focus
                    return false;
                },
                select: function (event, ui) {
                    // var terms = split( this.value );
                    // // remove the current input
                    // terms.pop();
                    // // add the selected item
                    // terms.push( ui.item.value );
                    // // add placeholder to get the comma-and-space at the end
                    // terms.push( "" );
                    // this.value = terms.join( ", " );
                    // return false;
                }
            })
            .autocomplete("instance")._renderItem = function (ul, item) {
                return $("<li>")
                    .append("<a href=\"" + item.url + "\"><div><div class=\"col-3 thumbWrapper d-inline-block my-auto text-center py-2 ms-1\"><img src=\"" + item.image + "\" class=\"searchThumb\"></div><div class=\"col-9 d-inline-block align-middle my-auto py-2 ms-1\"><span class=\"elipsis-offer text-dark\">" + item.name + "</span></div></div></a>")
                    .appendTo(ul);
            }
    });
});

// Toggle mobile search button icon
function searchButtonState() {
    $('#searchToggle').empty();
    if ($('body').hasClass('search-toggled')) {
        $('#searchToggle').append('<i class="far fa-times fa-fw"></i>');
    } else {
        $('#searchToggle').append('<i class="far fa-search fa-fw"></i>');
    }
}