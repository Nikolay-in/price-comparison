$(document).ready(function() {
    //Check if single or non single products are requested by the url parameters
    let params = new URLSearchParams(location.search);
    let hideSingle = params.get('hideSingle');
    let exists = params.get('exists');
    let ajaxQuery = '../admin/includes/getTempProducts.php';
    let activeButton = "";
    if ( hideSingle == '1' ) {
        ajaxQuery += '?hideSingle=1';
        activeButton += '<a href=\"tempproducts.php\" class=\"btn btn-primary\" role=\"button\"><i class=\"fas fa-eye fa-lg fa-lg me-2\"></i>View All</a>';
    } else {
        activeButton += '<a href=\"tempproducts.php?hideSingle=1\" class=\"btn btn-primary\" role=\"button\"><i class=\"fas fa-eye-slash fa-lg me-2\"></i>Hide Single Products</a>';
    }
    if ( exists == '1' ) {
        ajaxQuery += '?exists=1';
        activeButton += '&nbsp;<a href=\"tempproducts.php\" class=\"btn btn-primary\" role=\"button\"><i class=\"fas fa-list fa-lg fa-lg me-2\"></i>View All</a>';
    } else {
        activeButton += '&nbsp;<a href=\"tempproducts.php?exists=1\" class=\"btn btn-primary\" role=\"button\"><i class=\"fas fa-database fa-lg me-2\"></i>Show Existing Only</a>';
    }

    var t = $('#datatables').DataTable( {
        "dom": '<"toolbar">frtip',
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "pageLength": 50,
        "rowsGroup": [1, 2, 3, 4],
        "order": [[3, 'desc']],
        "orderFixed": [[3, 'desc'], [ 2, 'desc'], [ 7, 'desc']],
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    if (row[1] != null) {
                        let exists = '';
                        if (row[12] == 1) {
                            exists = '<i class=\"fas fa-database fa-lg text-success me-2\"></i>';
                        }
                        return '<b>'+ exists + row[1] + ':<br /><a href=\"tempproduct.php?brand=' + row[11] + '&model=' + encodeURIComponent(data) + '\">' + data + '</a></b>';
                    } else {
                        return data;
                    }
                },
                "targets": 2
            },
            {
                "render": function ( data, type, row ) {
                    if (row[10] != null) {
                    return row[10] + '<br />└&nbsp;' + data;
                    } else {
                        return data;
                    }
                },
                "targets": 4
            },
            {
                "render": function ( data, type, row ) {
                    if (row[0] != null) {
                        return '<a href=\"' + data + '\" target=\"_blank\" ><i class=\"fas fa-external-link-alt fa-lg mx-1\"></i></a><a href=\"tempproducts.php?delete=' + row[0] + '\" onClick=\"return confirm(\'Do you really want to delete?\');\"><i class=\"fa fa-trash-alt fa-lg mx-1 text-danger\"</i></a>';
                    } else {
                        return "";
                    }
                },
                "targets": 9
            },
            { "targets": [1, 10], "visible": false}
        ],
        "ajax": ajaxQuery
    } );
    $("div.toolbar").html(activeButton);
    $( ".toolbar" ).addClass( "float-start" );
    $( "#datatables_info" ).addClass( "float-start" );

    t.on( 'draw.dt', function () {
        var table = $('#datatables').DataTable();
        var PageInfo = $('#datatables').DataTable().page.info();
        t.column(0, { page: 'current' }).nodes().each( function (cell, i) {
        cell.innerHTML = i + 1 + PageInfo.start;
        } );
    } );
} );