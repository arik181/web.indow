var manufacturing_table; //data table.  This variable is set in production.php when the table is created.
var table_options = {
    dom: '',
    language: {
        emptyTable: 'The order has no associated items.',
    },
    columns: [
        {data: 'id'},
        {data: 'unit_num'},
        {data: 'room'},
        {data: 'location'},
        {data: 'product'},
        {data: 'product_type'},
        {data: 'special_geom'},
        {data: 'id'},
        {data: 'id'},
        {data: 'id'},
        {data: 'id'},
        {data: 'id'},
        {data: 'id'},
        {data: 'id'}
    ],
    createdRow: function(row, data, index) {
        var item_check = $('<input type="checkbox" class="item_check">');
        item_check.change(function() {
            $(this).closest('tr').find('.print_check').prop('checked', $(this).prop('checked'));
        });
        $('td:eq(0)', row).html(item_check);
        $('td:eq(6)', row).html($('<input type="checkbox" disabled="disabled">').prop('checked', parseInt(data.special_geom)));
        $('td:eq(8)', row).html('<input type="checkbox" class="print_check" name="laser">');
        if (parseInt(data.calcs)) {
			$('td:eq(7)', row).html('<input type="checkbox" class="print_check" name="tubing">');
            $('td:eq(9)', row).html('<input type="checkbox" class="print_check" name="traveler">');
            $('td:eq(13)', row).html('<input type="checkbox" class="print_check" name="dxf">');
        } else {
			$('td:eq(7)', row).html($('<input type="checkbox" class="print_check" name="tubing">').prop('disabled', true).prop('title', 'Tubing cannot be printed until cut image scripts have run.'));
            $('td:eq(9)', row).html($('<input type="checkbox" class="print_check" name="traveler">').prop('disabled', true).prop('title', 'Traveler cannot be printed until cut image scripts have run.'));
            $('td:eq(13)', row).html($('<input type="checkbox" class="print_check" name="dxf">').prop('disabled', true).prop('title', 'DXF files cannot be downloaded until cut image scripts have run.'));
        }
        $('td:eq(10)', row).html('<input type="checkbox" class="print_check" name="product_labels">');
        if (parseInt(data.sleeves)) {
            $('td:eq(11)', row).html('<input type="checkbox" class="order_print print_check" name="sleeves">');
            $('td:eq(12)', row).html('<input type="checkbox" class="order_print print_check" name="sleeve_labels">');
        } else {
            $('td:eq(11)', row).html('');
            $('td:eq(12)', row).html('');
        }
        return row;
    },
    iDisplayLength: 9000
};

function create_items_table(parent_table, row, order) {
    var table_cont = $('#clonecont .items_table_cont').clone().attr('id', 'item_table_' + order.id);
    var itable = table_cont.find('table').DataTable(table_options);
    $.each(order.items, function (i, item) {
        itable.row.add(item).draw();
    });
    var trow = parent_table.row(row);
    trow.child(table_cont);
}
    

function manufacturing_row(data, row) {
    $('td:eq(0)', row).html('<input type="checkbox" class="order_check">');
    $('td:eq(7)', row).html('<input type="checkbox" class="order_print print_check" name="laser">');
    if (data.calculations) {
		$('td:eq(6)', row).html('<input type="checkbox" class="order_print print_check" name="tubing">');
        $('td:eq(8)', row).html('<input type="checkbox" class="order_print print_check" name="traveler">');
        $('td:eq(12)', row).html('<input type="checkbox" class="order_print print_check" name="dxf">');
    } else {
		$('td:eq(6)', row).html($('<input type="checkbox" class="order_print print_check" name="tubing">').prop('disabled', true).prop('title', 'Tubing cannot be printed until cut image scripts have run.'));
        $('td:eq(8)', row).html($('<input type="checkbox" class="order_print print_check" name="traveler">').prop('disabled', true).prop('title', 'Traveler cannot be printed until cut image scripts have run.'));
        $('td:eq(12)', row).html($('<input type="checkbox" class="order_print print_check" name="dxf">').prop('disabled', true).prop('title', 'DXF files cannot be downloaded until cut image scripts have run.'));
    }
    $('td:eq(9)', row).html('<input type="checkbox" class="order_print print_check" name="product_labels">');
    $('td:eq(10)', row).html('<input type="checkbox" class="order_print print_check" name="sleeves">');
    $('td:eq(11)', row).html('<input type="checkbox" class="order_print print_check" name="sleeve_labels">');
    if (parseInt(data.expedite, 10)) {
        $(row).addClass('expedite-row');
    }
    if (data.items) {
        create_items_table(manufacturing_table, row, data);
    }
}

$(function () {
    function get_export_items() {
        var item_ids = [];
        $('.order_check').each(function () {
            var item_html = manufacturing_table.row($(this).closest('tr')).child();

            item_html.find('.item_check:checked').each(function() {
                var item_table = $(this).closest('table').DataTable();
                var tr = $(this).closest('tr');
                var row = item_table.row(tr);
                var item_id = row.data().id;
                item_ids.push(item_id);
            });
        });
        return item_ids;
    }

    function get_print_items() {
        var items = [];
        var laser_items = [];
        var dxf_items = [];
        $('.order_check').each(function () { //all visible orders
            var tr = $(this).closest('tr');
            var row = manufacturing_table.row(tr);
            var item_table = row.child().find('table').DataTable();
            if (!row.data().items.length) {
                return;
            }
            row.child().find('tbody tr').each(function () { //all item rows
                var id = item_table.row(this).data().id;
                var item = {id: id};
                var checked = false;
                $(this).find('.print_check').each(function () {
                    var name = $(this).attr('name');
                    if (name === 'laser' && $(this).prop('checked')) {
                        laser_items.push(id);
                    } else if (name === 'dxf' && $(this).prop('checked')) {
                        dxf_items.push(id);
                    } else {
                        checked = checked || $(this).prop('checked');
                        item[name] = $(this).prop('checked');
                    }
                });
                if (checked) {
                    items.push(item);
                }
            });
        });
        return {items: items, laser: laser_items, dxf: dxf_items};
    }

    $('#manufacturingTable').on('click', '.show-items', function () {
        var tr = $(this).closest('tr');
        var row = manufacturing_table.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
        } else {
            row.child.show();
        }
    }).on('change', '.order_check', function() {
        var tr = $(this).closest('tr');
        var row = manufacturing_table.row(tr);
        var boxes = row.child().find('.item_check, .print_check').not(':disabled');
        if (tr.find('[name="traveler"]').prop('disabled')) {
            boxes = boxes.not('[name="traveler"]');
        }
        boxes.prop('checked', $(this).prop('checked'));
        $(this).closest('tr').find('.print_check').not(':disabled').prop('checked', $(this).prop('checked'));
    }).on('change', '.item_check', function () {
        $(this).closest('tr').find('.print_check').prop('checked', $(this).prop('checked'));
    }).on('change', '.order_print', function () {
        var tr = $(this).closest('tr');
        var row = manufacturing_table.row(tr);
        var name = $(this).attr('name');
        row.child().find('[name="' + name + '"]').prop('checked', $(this).prop('checked'));
    });
    $('#check_all').change(function () {
        $('.order_check').prop('checked', $(this).prop('checked')).change();
    });
    $('#manufacturing_export').click(function () {
        var items = get_export_items();
        if (!items.length) {
            alert('No items selected.');
        } else {
            force_download(items, 'items');
        }
    });
    $('#manufacturing_print').click(function () {
        var items = get_print_items();
        var print_items = items.items;
        var laser_items = items.laser;
        var dxf_items = items.dxf;
        
        if (!print_items.length && !laser_items.length && !dxf_items.length) {
            alert('No items have been selected for printing.');
        } else {
            if (print_items.length) {
                $('#print_data').val($.toJSON(print_items));
                $('#print_form').submit();
            }
            if (laser_items.length) {
                $('#csv_export_frame').attr('src', '/fulfillment/laser_csv?id=' + laser_items.join());
            }
            if (dxf_items.length) {
                $('#csv_export_frame').attr('src', '/fulfillment/dxf_download?id=' + dxf_items.join());
            }
        }
    });
});