$(function () {
    function total_rows () {
        //placeholder function. do not remove.
    }

    var tableoptions = {
        "ajax": "/orders/order_item_list_json/" + order_id + "/1_2/1",
        "dom": "<rt>",
        "pagingType": "full_numbers",
        "iDisplayLength": 500,
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
            {'data':'id'},
            {'data':'manufacturing_status'},
            {'data':'room'},
            {'data':'location'},
            {'data':'product_id'},
            {'data':'product_types_id'},
            {'data':'edging_id'},
            {'data':'width'},
            {'data':'height'},
            {'data':'id'}
        ],
        "createdRow": function (row, data, index) {
            stylizeRow(row, data, index, false, itable, total_rows);
        }
    };

    var itable = $('#itemtable').DataTable(tableoptions);
    bind_itable_js(itable, $('#itemtable'), total_rows);

    function submit_page(cancel) {
        var items = itable.data();
        var cleanitems = [];
        $.each(items, function (i, item) {
            cleanitems.push(item);
        });
        var order = {
            items:              cleanitems,
            delete_items:       indow_delete_items,
            cancel:             cancel,
            shipping_address:   indow_shipping_address
        };
        $('#measuredata').val($.toJSON(order));
        $('#measureform').submit();
    }
    $('#cancelorder').click(function () {
        submit_page(true);
    });
    $('#saveorder').click(function () {
        var message, term;
        var invalid_count = $('.invalid-measurements').length;
        if (!invalid_count) {
            message = 'All your measurements validated. Click okay to proceed.';
        } else {
            term = invalid_count == 1 ? 'window' : 'windows';
            message = invalid_count + ' ' + term + ' did not pass validation. If you are aware of each of the issues and still want to proceed click OK, or click cancel and fix the windows that did not pass validation.';
        }
        bootbox.dialog({message: message, buttons: {
            cancel: {className: 'btn-gray pull-left', label: 'Cancel'}, 
            okay: {className: 'btn-blue', label: 'Okay', callback: function () { submit_page(false); }}
        }});
    });

    $('#additem').click(function () {
        var item = get_new_item();
        $.post('/orders/update_item_ajax/' + order_id, item, function (item_id) {
            item.id = item_id;
            itable.row.add(item).draw();
        });
    });

    $('#withselected_delete').click(function () {
        withselected_delete(itable);
        var delete_items = [];
        var delete_subitems = [];
        $.each(indow_delete_items, function(i, e) {
            if (e.type === 'item') {
                delete_items.push(e.id);
            } else {
                delete_subitems.push(e.id);
            }
        });
        $.post('/orders/delete_order_items_ajax/' + order_id, {items: delete_items, subitems: delete_subitems});
        total_rows();
    });

    $('#checkall').click(function () {
        $('.withselected').prop('checked', $(this).prop('checked'));
    });

    fix_height();
    $('#windowoptions .mfg_status_pop').attr('title', 'HOLD is for windows you want to order later.');

    $('body').on('change', '.window_shape_options', function () {
        var $popover = $(this).closest('.popover-content');
        var spec_geom = $popover.find('[name="special_geom"]');
        if ($(this).val() == 3) {
            var btext = 'Click <a href="/assets/measure_custom.pdf">here</a> for instructions on how to measure custom shaped windows.';
            if (!$('.modal-body').length) {
                // bootbox.dialog({message: btext, buttons: {okay: {className: 'btn-blue', label: 'Okay'}}});
            }
            //bootbox does not seem to play nicely with the open popover, so using a regular alert for now.
            //alert(btext);
            spec_geom.val(1);
        } else {
            spec_geom.val(0);
        }
        check_custom_measurement_status($popover);
    }).on('change', '.popover-content select, .popover-content input', function () {
        $(this).closest('.popover-content').find('.windowoptionssave').prop('disabled', true);
    }).on('keyup', '.measurement', function () {
        check_custom_measurement_status($(this).closest('.popover-content'));
    }).on('change', '.plus_one, .own_tools', function () {
        var $popover = $(this).closest('.popover-content');
        check_custom_measurement_status($popover);
        calc_spines($popover);
    });
    $('#understand').change(function () {
        $('#saveorder').prop('disabled', !$(this).prop('checked'))
    });

});
function check_custom_measurement_status($popover) {
    var diag;
    var shape = $popover.find('.window_shape_options').val();
    if (shape == 3) {
        var A_field = $popover.find('[name="A"]');
        var B_field = $popover.find('[name="B"]');
        var C_field = $popover.find('[name="C"]');
        var D_field = $popover.find('[name="D"]');
        var E_field = $popover.find('[name="E"]');
        var F_field = $popover.find('[name="F"]');
        var A = convertToDecimal($.trim(A_field.val()));
        var C = convertToDecimal($.trim(C_field.val()));
        var Ac = parseFloat(A);
        var Cc = parseFloat(C);
        var plus_1 = $popover.find('[name="freebird_laser"]').prop('checked');
        if (plus_1) {
            Ac += 1;
            Cc += 1;
        }
        diag = Math.sqrt(Ac*Ac + Cc*Cc);
        if (plus_1) {
            diag -= 1;
        }

        if (!window.indow_legacy && !$popover.find('[name="own_tools"]').prop('checked')) {
            diag -= 2;
        }
        if (isNaN(diag)) {
            diag = 0;
        }

        E_field.prop('disabled', true).val(diag.toFixed(2));
        F_field.prop('disabled', true).val(diag.toFixed(2));
        var new_B = parseFloat(A).toFixed(2);
        var new_D = parseFloat(C).toFixed(2);
        new_B = isNaN(new_B) ? 0 : new_B;
        new_D = isNaN(new_D) ? 0 : new_D;
        B_field.prop('disabled', true).val(new_B);
        D_field.prop('disabled', true).val(new_D);
        $popover.find('[name="extension"]').prop('checked', false);
        $popover.find('.extension_cont').hide();
        $popover.find('.custom_instructions').show();
    } else {
        $popover.find('[name="B"]').prop('disabled', false);
        $popover.find('[name="D"]').prop('disabled', false);
        $popover.find('[name="E"]').prop('disabled', false);
        $popover.find('[name="F"]').prop('disabled', false);
        $popover.find('.extension_cont').show();
        $popover.find('.custom_instructions').hide();
    }
}
function update_measured_window_count() {
    var data = $('#itemtable').DataTable().data();
    var measured_count = 0;
    $.each(data, function (i, row) {
        if (parseInt(row.measured, 10)) {
            measured_count++;
        }
    });
    $('#measured_count').text(measured_count + ' / ' + data.length + ' windows measured.');
}
