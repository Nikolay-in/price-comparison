$(document).ready(function() {
    var t = $('#datatables').DataTable( {
        "dom": '<"toolbar">frtip',
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "pageLength": 50,
        "order": [[ 1, 'asc'], [ 7, 'asc']],
        "rowsGroup": [1, 2, 3, 4, 5],
        "columnDefs": [
            {
                // The `data` parameter refers to the data for the cell (defined by the
                // `data` option, which defaults to the column being worked with, in
                // this case `data: 0`.
                "render": function ( data, type, row ) {
                    let eye = "";
                    if ( row[14] == "1" ) {
                        eye = "<i class=\"fas fa-eye fa-lg text-success\"></i>&nbsp;";
                    } else if ( row[14] == "0" ) {
                        eye = "<i class=\"fas fa-eye-slash fa-lg text-danger\"></i>&nbsp;";
                    }
                    if ( data ) {
                        return eye + row[16] + '&nbsp;<a href=\"editcategory.php?act=editcat&id=' + row[0] + '\" style=\"text-decoration: none;\">' + data + '</a>';
                    } else {
                        return null;
                    }
                },
                "targets": 2
            },
            {
                "render": function ( data, type, row ) {
                    if (row[0] != null) {
                        return '<a href=\"/' + row[12] + '\" target=\"_blank\"><i class=\"fas fa-external-link-alt fa-lg mx-1\"></i></a><a href=\"editcategory.php?act=editcat&id=' + row[0] + '\"><i class=\"fas fa-edit fa-lg mx-1\"></i></a><a href=\"editcategory.php?act=delcat&id=' + row[0] + '\" onClick=\"return confirm(\'Do you really want to delete?\');\"><i class=\"fa fa-trash-alt fa-lg mx-1 text-danger\" aria-hidden=\"true\"></i></a>';
                    } else {
                        return null;
                    }
                },
                "targets": 3
            },
            {
                "render": function ( data, type, row ) {
                    let eye = "";
                    if (row[15] == "1") {
                        eye = "<i class=\"fas fa-eye fa-lg text-success\"></i>&nbsp;";
                    } else if (row[15] == "0") {
                        eye = "<i class=\"fas fa-eye-slash fa-lg text-danger\"></i>&nbsp;";
                    }
                    if ( data ) {
                        return eye + '<a href=\"editcategory.php?act=editsubcat&id=' + row[11] + '\" style=\"text-decoration: none;\">' + data + '</a>';;
                    } else {
                        return null;
                    }
                },
                "targets": 8
            },
            {
                "render": function ( data, type, row ) {
                    if (data != null) {
                        return '<a href=\"/' + row[13] + '\" target=\"_blank\"><i class=\"fas fa-external-link-alt fa-lg mx-1\"></i></a><a href=\"editcategory.php?act=editsubcat&id=' + data + '\"><i class=\"fas fa-edit fa-lg mx-1\"></i></a><a href=\"editcategory.php?act=delsubcat&id=' + data + '\" onClick=\"return confirm(\'Do you really want to delete?\');\"><i class=\"fa fa-trash-alt fa-lg mx-1 text-danger\" aria-hidden=\"true\"></i></a>';
                    } else {
                        return null;
                    }
                },
                "targets": 11
            },
            { "targets": [0, 6, 12, 13, 14, 15], "visible": false}
        ],
        "ajax": "../admin/includes/getCategories.php"
    } );
    $("div.toolbar").html('<a class="btn btn-primary me-2" href="editcategory.php?act=addcat" role="button"><i class="fas fa-plus fa-lg me-2"></i>Add Category</a><a class="btn btn-primary" href="editcategory.php?act=addsubcat" role="button"><i class="fas fa-plus me-2"></i>Add SubCategory</a>');
    $( ".toolbar" ).addClass( "float-start" );
    $( "#datatables_info" ).addClass( "float-start" );
    
    t.on( 'draw.dt', function () {
        var table = $('#datatables').DataTable();
        var PageInfo = $('#datatables').DataTable().page.info();
    } );
} );