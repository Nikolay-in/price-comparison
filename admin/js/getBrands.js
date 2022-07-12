$(document).ready(function() {
    var t = $('#datatables').DataTable( {
        "dom": '<"toolbar">frtip',
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "pageLength": 50,
        "columnDefs": [
            {
                "render": function ( data, type, row ) {
                    return '<a href=\"editbrands.php?act=edit&id=' + row[0] + '\" style=\"text-decoration: none;\">' + data + '</a>';
                },
                "targets": 1
            },
            {
                "render": function ( data, type, row ) {
                    if (row[0] != null) {
                        return '<a href=\"editbrands.php?act=edit&id=' + row[0] + '\"><i class=\"fas fa-edit fa-lg mx-1\"></i></a><a href=\"editbrands.php?act=del&id=' + row[0] + '\" onClick=\"return confirm(\'Do you really want to delete?\');\"><i class=\"fa fa-trash-alt fa-lg mx-1 text-danger\" aria-hidden=\"true\"></i></a>';
                    } else {
                        return null;
                    }
                },
                "targets": 4
            },
        ],
        "ajax": "../admin/includes/getBrands.php"
    } );
    $("div.toolbar").html('<a class="btn btn-primary" href="editbrands.php?act=add" role="button"><i class="fas fa-plus fa-lg me-2"></i>Add Brand</a>');
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