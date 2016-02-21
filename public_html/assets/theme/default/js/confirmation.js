$(function () {

    var tableoptions = {
        "dom": "<rt>",
        "pagingType": "full_numbers",
        "language":{
            "paginate":{
                "previous":"&laquo;",
                "next":"&raquo;",
            },
            "emptyTable": "There are no items associated with this order.",
        },
        "columnDefs": [{
            "targets": 0,
            "orderable": false
        }],
        "columns": [
            {'data':'unit_num'},
            {'data':'room'},
            {'data':'location'},
            {'data':'width'},
            {'data':'height'},
            {'data':'product'},
            {'data':'product_type'},
            {'data':'edging'},
            {'data':'special_geom'},
            {'data':'price'}
        ],
        "createdRow": function (row, data, index) {
            $('td:eq(9)', row).html('$' + parseFloat(data.price).toFixed(2));
            $('td:eq(8)', row).html(data.special_geom == 1 ? 'Yes' : 'No');
            if (getObjectSize(data.subproducts)) {
                render_subproducts_ro(itable, row);
            }
        },
        "iDisplayLength": 9000,
    };

    var itable = $('#itemtable').DataTable(tableoptions);
    $.each(indow_items, function (i, e) {
        itable.row.add(e);
    });
    itable.draw();
    fix_height();

    $('#submitpage').prop('disabled', true);

    $('#confirmation_sig').keyup(function(){
        if($(this).val() != ''){
            $('#submitpage').prop('disabled', false);
        } else {
            $('#submitpage').prop('disabled', true);
        }
    });

    $('#submitpage').click(function (e) {
        e.preventDefault();
        var sig = $('#confirmation_sig').val();

        if(sig != ''){
            bootbox.dialog({
                message: "Are you sure you want to submit the order?",
                title: "Save",
                buttons: {
                    danger: {
                        label: "Cancel",
                        className: "btn-blue"
                    },
                    success: {
                        label: "Submit Order",
                        className: "btn-blue",
                        callback: function() {
                            $('#confirmform').submit();
                        }
                    }
                }
            });
        }
    });
});