$(document).ready(function() {
    var t = $('#datatables').DataTable( {
        "dom": '<"toolbar">frtip',
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "pageLength": 50,
        "order": [5 , "desc"],
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    let eye = "";
                    if (row[9] == "1") {
                        eye = "<i class=\"fas fa-eye fa-lg text-success\"></i>&nbsp;";
                    } else if (row[9] == "0") {
                        eye = "<i class=\"fas fa-eye-slash fa-lg text-danger\"></i>&nbsp;";
                    }
                    return eye + '<a href=\"editvendors.php?act=edit&id=' + row[0] + '\" style=\"text-decoration: none;\">' + data + '</a>';
                },
                "targets": 1
            },
            {
                "render": function ( data, type, row ) {
                    if (data != null) {
                    return '<div class=\"text-hover-image\"><i class=\"far fa-image fa-lg text-primary \"></i><img style=\"display:none;\" alt=\"' + data + '\" /></div>';
                    } else {
                        return data;
                    }
                },
                "targets": 2
            },
            {
                "render": function ( data, type, row ) {
                    return '<a href=\"' + data + '\" target=\"_blank\">' + data + '</a>';
                },
                "targets": 3
            },
            {
                "render": function ( data, type, row ) {
                    if ( row[11] == null ) { row[11] = "0"; }
                    if ( data == null ) { data = "0"; }
                    return data + ' / ' + row[11];
                },
                "targets": 5
            },
            {
                "render": function ( data, type, row ) {
                    if ( row[12] == null) { row[12] = "0"; }
                    if ( data == null ) { data = "0"; }
                    if ( row[10] == null ) { row[10] = "0"; }
                    return data + ' / ' + row[10] + ' / ' + row[12];
                },
                "targets": 6
            },
            {
                "render": function ( data, type, row ) {
                    if (row[0] != null) {
                        return '<a href=\"editvendors.php?act=edit&id=' + row[0] + '\"><i class=\"fas fa-edit fa-lg mx-1\"></i></a><a href=\"editvendors.php?act=del&id=' + row[0] + '\" onClick=\"return confirm(\'Do you really want to delete?\');\"><i class=\"fa fa-trash-alt fa-lg mx-1 text-danger\" aria-hidden=\"true\"></i></a>';
                    } else {
                        return null;
                    }
                },
                "targets": 8
            },
            { "targets": [9, 10, 11, 12], "visible": false}
        ],
        "ajax": "../admin/includes/getVendors.php"
    } );
    $("div.toolbar").html('<a class="btn btn-primary me-1" href="editvendors.php?act=add" role="button"><i class="fas fa-plus fa-lg me-2"></i>Add Vendor</a>');
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