$(document).ready(function() {
    //Check if only inactive products are requested by the url parameters
    let params = new URLSearchParams(location.search);
    let active = params.get('active');
    let ajaxQuery = "";
    let activeButton = "";
    if ( active == '0' ) {
        ajaxQuery = '../admin/includes/getProducts.php?active=0';
        activeButton = '<a href=\"products.php\" class=\"btn btn-primary\" role=\"button\"><i class=\"fas fa-eye fa-lg me-2\"></i>View All</a>';
    } else {
        ajaxQuery = '../admin/includes/getProducts.php';
        activeButton = '<a href=\"products.php?active=0\" class=\"btn btn-primary\" role=\"button\"><i class=\"fas fa-eye-slash fa-lg me-2\"></i>View Only Inactive</a>';
    }

    var t = $('#datatables').DataTable( {
        "dom": '<"toolbar">frtip',
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "pageLength": 50,
        "order": [[ 8, 'desc'], [ 9, 'desc']],
        "createdRow": function (row) {
            $(row).addClass("align-middle");
        },
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    if (row[1] != null) {
                        return '<div class=\"text-hover-image\"><i class=\"far fa-image fa-lg text-primary \"></i><img style=\"display:none;\" alt=\"' + data + '\" /></div>';
                    } else {
                        return data;
                    }
                },
                "targets": 1
            },
            {
                "render": function ( data, type, row ) {
                    let eye = "";
                    if (row[11] == "1") {
                        eye = "<i class=\"fas fa-eye fa-lg text-success\"></i>&nbsp;";
                    } else if (row[11] == "0") {
                        eye = "<i class=\"fas fa-eye-slash fa-lg text-danger\"></i>&nbsp;";
                    }
                    return eye + '<a href=\"editproducts.php?act=edit&id=' + row[0] + '\" class=\"text-decoration-none fw-bold\">' + data + '</a>';
                },
                "targets": 2
            },
            {
                "render": function ( data, type, row ) {
                    if (row[7] != null) {
                    return data + '<br />└&nbsp;' + row[7];
                    } else {
                        return data;
                    }
                },
                "targets": 6
            },
            {
                "render": function ( data, type, row ) {
                    if (row[0] != null) {
                        return '<a target=\"_blank\" href=\"/' + row[12] + '/' + row[10] + '\"><i class=\"fas fa-external-link-alt fa-lg m-1\"></i></a><a href=\"editproducts.php?act=edit&id=' + row[0] + '\"><i class=\"fas fa-edit fa-lg m-1\"></i></a><a href=\"editproducts.php?act=del&id=' + row[0] + '\" onClick=\"return confirm(\'Do you really want to delete?\');\"><i class=\"fa fa-trash-alt fa-lg m-1 text-danger\"</i></a>';
                    } else {
                        return "";
                    }
                },
                "targets": 10
            },
            { "targets": [7, 11], "visible": false}
        ],
        "ajax": ajaxQuery
    } );
    $("div.toolbar").html('<a class="btn btn-primary me-2" href="editproducts.php?act=add" role="button"><i class="fas fa-plus fa-lg me-2"></i>Add Product</a>' + activeButton);
    $( ".toolbar" ).addClass( "float-start" );
    $( "#datatables_info" ).addClass( "float-start" );

    t.on( 'draw.dt', function () {
        var table = $('#datatables').DataTable();
        var PageInfo = $('#datatables').DataTable().page.info();

        t.column(0, { page: 'current' }).nodes().each( function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
        } );

        //Show main image on hover
        var yOff = 15;
        var xOff = 20;
        $(".text-hover-image").hover(function (e) {
            var pathToImage = $(this).children("img").attr("alt");
            $("body").append("<p id='image-when-hovering-text'><img style=\"width: 384px;\" src='" + pathToImage + "'/></p>");
            $("#image-when-hovering-text")
                .css("position", "absolute")
                .css("top", (e.pageY - yOff) + "px")
                .css("left", (e.pageX + xOff) + "px")
                .fadeIn("fast");
        },

        function () {
            $("#image-when-hovering-text").remove();
        });

        $(".text-hover-image").mousemove(function (e) {
            $("#image-when-hovering-text")
                .css("top", (e.pageY - yOff) + "px")
                .css("left", (e.pageX + xOff) + "px");
        });
    } );
} );