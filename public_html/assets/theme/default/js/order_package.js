$(function () {
    function total_rows () {
        var data = [];
        $.each(dataTable.data(), function (i, e) {
            data.push(e);
        });
        if (typeof indow_credit_limit !== 'undefined' && $('#credit_limit').prop('checked')) {
            var totals = get_totals(data, indow_credit_limit);
        } else {
            var totals = get_totals(data);
        }
        $('#warnings_cont').html('');
        if (totals.warnings) {
            $.each(totals.warnings, function (i, text) {
                $('#warnings_cont').append('<div class="alert alert-danger">' + text + '</div>');
            });
        }
        write_totals(totals);
        return totals;
    }

    function get_order_data() {
        var order_data = {};
        var fields = $('.order-status input, .order-status select, #follow_up');
        fields.each(function () {
            var fieldval;
            if ($(this).attr('type') === 'checkbox') {
                fieldval = $(this).prop('checked') ? 1 : 0;
            } else {
                fieldval = $(this).val();
            }
            order_data[$(this).attr('name')] = fieldval;
        });
        if (order_data.expedite_date === '') {
            order_data.expedite_date = null;
        }
        if (order_data.commit_date === '') {
            order_data.commit_date = null;
        }
        if (indow_shipping_address) {
            order_data.shipping_address_id = indow_shipping_address;
        }
        order_data.site_id = indow_site_id;

        return order_data;
    }

    function getOrderTableOptions(order_id, mfg_status, empty_language, mode) {
        var options = {
            "dom": "<rt>",
            "pagingType": "full_numbers",
            "iDisplayLength": 5000,
            "language":{
                "paginate":{
                    "previous":"&laquo;",
                    "next":"&raquo;",
                },
                "emptyTable": 'There are no ' + empty_language + ' items associated with this order',
            },
            "order": [[ 0, "asc" ]],
            "columns": [
                {'data':'room'},
                {'data':'location'},
                {'data':'product_id'},
                {'data':'product_types_id'},
                {'data':'edging_id'},
                {'data':'special_geom'},
                {'data':'price'}
            ],
            "createdRow": function (row, data, index) {
                stylizeRow(row, data, index, false, dataTable, total_rows, mode);
            }
        };
        if (order_id !== 0) {
            options.ajax = '/orders/order_item_list_json/' + order_id + '/' + mfg_status;
        }
        return options;
    }

    var mode = 'order';
    var dataTable = $('#itemsTable').DataTable(getOrderTableOptions(order_id, 1, 'included', mode));

    var order = {
        addpayments: {},
        deletepayments: []
    };
    var new_payment_count = 0;

    $('#submitpage').click(function () {
        var items = [];
        var cleanitems = [];
        $.each(dataTable.data(), function (i, item) {
            cleanitems.push(item);
        });
        $('.notes_text').each(function () {
            order[$(this).data('jsname')] = $(this).val();
        });
        order.items          = cleanitems;
        order.customers      = customer_manager_get_customers();
        order.fees           = indow_active_fees;
        order.user_fees      = indow_user_fees;
        order.delete_user_fees = indow_delete_user_fees;
        order.order_data     = get_order_data();
        if (typeof indow_bundle_orders !== 'undefined') {
            order.bundle = indow_bundle_orders;
        }
        order.delete_items = indow_delete_items;
        if (check_required_info()) {
            $('#orderdata').val($.toJSON(order));
            $('#orderform').submit();
            console.log(order);
        }
    });
    bind_fees_discounts(total_rows, 'top');
    $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});

    $('#status_code option').each(function () {
        if ($.inArray($(this).val(), indow_disabled_status) !== -1) {
            $(this).prop('disabled', 1);
        }
    });
});

