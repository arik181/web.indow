$(function () {
    function total_rows () {
        var totals = get_totals(itable.data());
        write_totals(totals);
    }

    var tableoptions = {
        "dom": "<rt>",
        "pagingType": "full_numbers",
        "iDisplayLength": 5000,
        "language":{
            "paginate":{
                "previous":"&laquo;",
                "next":"&raquo;",
            },
            "emptyTable": "There are no items associated with this quote.",
        },
        "columnDefs": [{ "type": "natural", "targets": 1 }],
        "sorting": [[0,'asc'], [1,'asc']],
        "columns": [
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
            stylizeRow(row, data, index, false, itable, total_rows);
        }
    };

    if (quotes_id !== 0) {
        tableoptions.ajax = "/quotes/item_list_json/" + quotes_id;
    }
    var itable = $('#quoteitems').DataTable(tableoptions);
    bind_itable_js(itable, $('#quoteitems'), total_rows);
    bind_fees_discounts(total_rows, 'bottom');

    $('#submitpage').click(function () {
        var items = itable.data();
        var cleanitems = [];
        $.each(items, function (i, item) {
            cleanitems.push(item);
        });
        var quote = {
            items:              cleanitems,
            customers:          customer_manager_get_customers(),
            fees:               indow_active_fees,
            followup:           $('#follow_up').prop('checked') ? 1 : 0,
            site_id:            indow_site_id,
            delete_items:       indow_delete_items,
            user_fees:          indow_user_fees,
            delete_user_fees:   indow_delete_user_fees
        };
        if (check_required_info()) {
            $('#quotedata').val($.toJSON(quote));
            $('#quoteform').submit();
        }
    });

    $('#additem').click(function () {
        var item = get_new_item();
        itable.row.add(item).draw();
    });

    $('#withselected_delete').click(function () {
        withselected_delete(itable);
        total_rows();
    });

    $('#checkall').click(function () {
        $('.withselected').prop('checked', $(this).prop('checked'));
    });

    $('#promote_to_order').submit(function (e) {
        e.preventDefault();
        order_review_show();
    });

    $('#orderReviewSave').click(function () {
        var review_data = get_order_review_info();
        $('#review_data').val($.toJSON(review_data));
        $('#promote_to_order')[0].submit();
    });
});