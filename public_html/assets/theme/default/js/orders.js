$(function () {
    $('body').on('click', '.cut_image_link', function () {
        var itable = $(this).closest('table').DataTable();
        var data = itable.row($(this).closest('tr')).data();
        var item_id = data.id;
        window.open('/fulfillment/cut_image/' + item_id);
    }).on('change', '.spines_edit', function () {
        var $popover = $(this).closest('.popover-content');
        var val = $(this).val();
        var top = (val == 'Top' || val == 'Both') ? 1 : 0;
        var side = (val == 'Side' || val == 'Both') ? 1 : 0;
        $popover.find('[name="top_spine"]').val(top);
        $popover.find('[name="side_spines"]').val(side);
    }).on('change', '.popover-content select, .popover-content input', function () {
        $(this).closest('.popover-content').find('.windowoptionssave').prop('disabled', true);
    });
    function total_rows () {
        var data = [];
        $.each(itable[0].data(), function (i, e) {
            data.push(e);
        });
        $.each(itable[1].data(), function (i, e) {
            data.push(e);
        });
        $.each(itable[2].data(), function (i, e) {
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
        reload_pricing(data);
        return totals;
    }
    window.total_rows = total_rows;

    function reload_pricing(data) { // make sure the data is loaded then refresh page.  datatables is supposed to have a callback to make this better but it doesnt seem to work in this version
        if (window.auto_refresh) {
            if (data.length && !window.auto_refreshing) {
                window.auto_refreshing = true;
                setTimeout(function () {
                    $('.submitpage').click();
                }, 250);
            }
        }
    }

    function get_order_data() {
        var order_data = {};
        var fields = $('.order-status input, .order-status select, #follow_up, #js_pricing');
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
        if (window.indow_shipping_address) {
            if ((indow_shipping_address + "").substr(0,1) === 'd') {
                order_data.dealer_shipping_address_id = indow_shipping_address.substr(1, 100);
                order_data.shipping_address_id = null;
            } else {
                order_data.shipping_address_id = indow_shipping_address;
                order_data.dealer_shipping_address_id = null;
            }
        }
        order_data.site_id = indow_site_id;

        return order_data;
    }

    function getOrderTableOptions(order_id, mfg_status, empty_language, tindex, mode) {
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
            "columnDefs": [{
                "targets": [0,1,7],
                "orderable": false
            }],
            //"sorting": [[4,'asc'], [5,'asc'], [3,'asc']],
            "bSort": false,
            "columns": [
                {'data':'id'},
                {'data':'manufacturing_status'},
                {'data':'id'},
                {'data':'unit_num'},
                {'data':'room'},
                {'data':'location'},
                {'data':'special_geom'},
                {'data':'product_id'},
                {'data':'product_types_id'},
                {'data':'edging_id'},
                {'data':'acrylic_panel_thickness'},
                {'data':'width'},
                {'data':'height'},
                {'data':'id'},
                {'data':'price'},
                {'data':'id'}
            ],
            "createdRow": function (row, data, index) {
                stylizeRow(row, data, index, false, itable[tindex], total_rows, mode);
            }
        };
        if (order_id !== 0) {
            options.ajax = '/orders/order_item_list_json/' + order_id + '/' + mfg_status;
        }
        return options;
    }

    function getEstimateTableOptions() {
        return {
            "dom": "<rt>",
            "pagingType": "full_numbers",
            "language":{
                "paginate":{
                    "previous":"&laquo;",
                    "next":"&raquo;",
                },
                "emptyTable": 'There are no ' + empty_language + ' items associated with this order',
            },
            "columnDefs": [{
                "targets": [0,1,7],
                "orderable": false
            }],
            "columns": [
                {'data':'manufacturing_status'},
                {'data':'id'},
                {'data':'unit_num'},
                {'data':'room'},
                {'data':'location'},
                {'data':'product_id'},
                {'data':'product_types_id'},
                {'data':'id'}
            ],
            "createdRow": function (row, data, index) {
                stylizeRow(row, data, index, false, itable[tindex], total_rows);
            }
        };
    }

    var itable = [
        $('#items_table_1').DataTable(getOrderTableOptions(order_id, 1, 'included', 0, mode)),
        $('#items_table_2').DataTable(getOrderTableOptions(order_id, 3, 'back order', 1, mode)),
        $('#items_table_3').DataTable(getOrderTableOptions(order_id, 4, 're-order', 2, mode))
    ];

    var order = {
        addpayments: {},
        deletepayments: []
    };
    var new_payment_count = 0;

    $('.itemtable').each(function (i) {
        var ctable = itable[i];
        bind_itable_js(ctable, this, total_rows);

        $(this).on('change', '.mfg_status', function () {
            var $row = $(this).closest('tr');
            var row = itable[i].row($row);
            var data = row.data();
            if ($(this).val() === 'del') {
                indow_delete_items.push({type: 'item', id: data.id});
                row.remove().draw();
                total_rows();
            } else {
                var mfg_status = parseInt($(this).val(), 10);
                var status_itable = {
                    1: 0,
                    3: 1,
                    4: 2
                };
                var itable_id = status_itable[mfg_status];
                itable[itable_id].row.add(data);
                itable[itable_id].draw();
                row.remove().draw();
                total_rows();
            }
        });
    });

    $('.submitpage').click(function () {
        var items = [];
        var cleanitems = [];
        $.each(itable[0].data(), function (i, item) {
            cleanitems.push(item);
        });
        $.each(itable[1].data(), function (i, item) {
            cleanitems.push(item);
        });
        $.each(itable[2].data(), function (i, item) {
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
		order.change_owner   = $('#change_owner').val();
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

    $.each(indow_start_payments, function (i, e) {
        add_payment(e);
    });
    function add_payment (payment) {
        var extra_htm = '';
        if (payment.id === 'new') {
            new_payment_count++;
            order.addpayments[new_payment_count] = payment;
            extra_htm = 'data-newid="' + new_payment_count + '"';
            indow_payments['new_' + new_payment_count] = payment;
        } else {
            indow_payments[payment.id] = payment;
        }
        var row = '<tr><td>' + payment.payment_received + '</td><td>' + indow_payment_type_options[payment.payment_type_id] + '</td>';
        row += '<td>$' + parseFloat(payment.payment_amount).toFixed(2) + '</td>';
        if(no_payment_options){
            row += '<td><a onclick="return false" href="#" class="remove_payment" ' + extra_htm + ' data-id="' + payment.id + '">Remove Payment</a></td>';
        }
        row += '</tr>';
        $('#addpaymentform').before(row);
        total_rows();
    }
    $('#addpayment').click(function () {
        var payment = {};
        $(this).closest('tr').find('input, select').each(function () {
            if ($(this).attr('name')) {
                if (!$(this).val()) {
                    alert('All fields are required.');
                    payment = null;
                    return false;
                }
                payment[$(this).attr('name')] = $(this).val();
            }
        });
        if (payment !== null) {
            payment.id = 'new';
            add_payment(payment);
        }
    });
    $('#content_view').on('click', '.remove_payment', function () {
        var newid = $(this).data('newid');
        if (newid) {
            delete order.addpayments[newid];
            delete indow_payments['new_' + newid]
        } else {
            order.deletepayments.push($(this).data('id'));
            delete indow_payments[$(this).data('id')];
        }
        $(this).closest('tr').remove();
        total_rows();
    });

    $('#send_measurement_form').click(function () {
        var elem = this;
        $(this).prop('disabled', true);
        $.post('/orders/send_measurement_form/' + order_id, function (response) {
            alert(response.message);
            //bootbox.alert(response.message);
            $(elem).prop('disabled', false);
            if (response.success) {
                $(elem).remove();
                $('#status_code').val(response.status_id);
            }
        });
    });
    $('#send_order_confirmation').click(function () {
        var elem = this;
        $(this).prop('disabled', true);
        $.post('/orders/send_order_confirmation/' + order_id, function (response) {
            //bootbox.alert(response.message);
            alert(response.message);
            $(elem).prop('disabled', false);
            if (response.success) {
                $(elem).remove();
            }
        });
    });

    $('.savenotes').click(function () {
        var elem = $(this);
        var type = $(this).data('type');
        var cont = $(this).closest('.notescont');
        var textarea = cont.find('textarea');
        var note = textarea.val();
        if (note) {
            elem.prop('disabled', 1);
            note = $('<div>').text(note).html();
            $.post('/orders/save_notes/' + order_id, {type: type, note: note}, function (response) {
                elem.prop('disabled', 0);
                if (response.success) {
                    textarea.val('');
                    var notehtml = '<dl class="dl-horizontal"><dt>' + response.date + '<br>by ' + indow_user_name + '</dt><dd>' + note + '</dd></dl>'
                    cont.find('.notesinner').prepend(notehtml);
                    cont.find('.notesempty').remove();
                    var count = cont.find('.dl-horizontal .dl-horizontal').length;
                    cont.find('.notes-count').html('(' + count + ')').removeClass('hidden');
                    alert('Note Saved');
                }
            });
        }
    });

    $('.checkall').click(function () {
        $(this).closest('table').find('.withselected').prop('checked', $(this).prop('checked'));
    });
    $('#order_tabbar li').click(function () {
        $('.withselected').prop('checked', false);
    });

    $('#status_code option').each(function () {
        if ($.inArray($(this).val(), indow_disabled_status) !== -1) {
            $(this).prop('disabled', 1);
        }
    });
    $('#credit_limit').click(function () {
        total_rows();
    });
    if (indow_message) {
        bootbox.dialog({message: indow_message, buttons: {okay: {className: 'btn-blue', label: 'Okay'}}});
    }
});
