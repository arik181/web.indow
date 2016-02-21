function shipping_row(data, row) {
    if (!window.indow_dealer_filter_list_shipping) {
        window.indow_dealer_filter_list_shipping = [];
    }
    if ($.inArray(data.dealer, indow_dealer_filter_list_shipping) === -1) {
        indow_dealer_filter_list_shipping.push(data.dealer);
        $('#dealer_filter2').append($('<option>').text(data.dealer));
    }
    var ship = ['Will Call', 'Local Delivery', 'Ground', 'Air', 'Truck', 'International'];
    var ship_option='<select name="ship_method" class="input-sm form-control changedata" style="width: 80px; padding: 2px;">';
    for (i=0;i<ship.length;i++) {
        if(ship[i] == data.ship_method) {
            ship_option += '<option selected>'+ship[i]+'</option>';
        } else {
            ship_option += '<option>' + ship[i] + '</option>';
        }           
    }
    ship_option +='</select>';
    var carrier = ['UPS', 'UPS Freight', 'FedEx Freight', 'DHL', 'Ceva Logistics', 'Other'];
    var carrier_option='<select name="carrier" class="input-sm form-control changedata" style="width: 80px; padding: 2px;">';
    for (i=0; i<carrier.length; i++) {
        if(carrier[i] == data.carrier) {
            carrier_option += '<option selected>' + carrier[i] + '</option>';
        } else {
            carrier_option += '<option>' + carrier[i] + '</option>';
        }
    }
    carrier_option += '</select>';
    if (data.combined) {
        $('td:eq(0)',  row).html('<a class="ulist_cell info-link" href="/fulfillment/combine_view/'  + data.id + '">combined</a>');
    } else {
        $('td:eq(0)',  row).html('<a class="ulist_cell info-link" href="/orders/edit/'  + data.id + '">' + data.id + '</a>');
    }
    console.log(data);
    $('td:eq(4)', row).html($('#clonecont .shipping_status_code_options').clone().val(data.status));
    $('td:eq(8)',  row).html(ship_option);
    $('td:eq(9)',  row).html(carrier_option);
    $('td:eq(10)', row).html($('<input style="width: 120px;" class="changedata form-control input-sm" name="tracking_num">').val(data.tracking_num));
    $('td:eq(11)',  row).html('$'  + parseFloat(data.ship_fee).toFixed(2));
    $('td:eq(12)', row).html('<input type="checkbox" name="labels">');
    $('td:eq(13)', row).html('<input type="checkbox" name="lists">');
    if (parseInt(data.expedite, 10)) {
        $(row).addClass('expedite-row');
    }
}

function packaging_row(data, row) {
    if (!window.indow_dealer_filter_list) {
        window.indow_dealer_filter_list = [];
    }
    if ($.inArray(data.dealer, indow_dealer_filter_list) === -1) {
        indow_dealer_filter_list.push(data.dealer);
        $('#dealer_filter').append($('<option>').text(data.dealer));
    }
    if (data.combined) {
        $('td:eq(0)',  row).html('<a class="ulist_cell info-link" href="/fulfillment/combine_view/'  + data.id + '">combined</a>');
    } else {
        $('td:eq(0)',  row).html('<a class="ulist_cell info-link" href="/orders/edit/'  + data.id + '">' + data.id + '</a>');
    }
    $('td:eq(1)', row).html($('#clonecont .status_code_options').clone().val(data.status));
    var linkid = data.combined ? 'c' + data.id : data.id;
    $('td:eq(7)', row).html('<a class="btn btn-sm btn-blue" target="_blank" href="/fulfillment/product_ship_label/' + linkid + '">Labels</a><a class="btn btn-sm btn-blue" target="_blank" href="/fulfillment/print_packing_list/' + linkid + '">Packing List</a>');
    if (parseInt(data.expedite, 10)) {
        $(row).addClass('expedite-row');
    }
}

$(function () {
    function get_print_items() {
        var orders = [];
        $('#shippingTable tr').each(function () {
            var labels = $(this).find('[name="labels"]').prop('checked');
            var lists = $(this).find('[name="lists"]').prop('checked');
            if (labels || lists) {
                var data = shipping_table.row(this).data();
                orders.push({
                    id:         data.id,
                    combined:   !!data.combined,
                    labels:     labels,
                    lists:      lists
                });
            }
        }); 
        return orders;
    }
    $('#logistics_print').click(function () {
        var items = get_print_items();
        if (!items) {
            alert('No items have been selected for printing.');
        } else {
           $('#print_data').val($.toJSON(items));
           $('#print_form').submit();
        }
        
    });
    // Shipping Table
    $('#shippingTable_filter, #packagingTable_filter').children('label').hide();

    var span = $('<span style="margin-right: 0px;" class="filter">Search By Order #</span>')
    .prependTo($('#shippingTable_wrapper .top .dataTables_filter'));

    var span2 = $('<span style="margin-right: 0px;" class="filter">Search By Dealer</span>')
    .prependTo($('#shippingTable_wrapper .top .dataTables_filter'));

    var select2 = $('<select id="dealer_filter2" style="width: 125px; margin-left: 10px" class="inline input-sm form-control"><option value="">Select</option></select>')
        .appendTo(span2)
        .on('change', function () {
            var dataTable = $("#shippingTable").DataTable();
            var filterColumn = 2; // status column
            var val = $(this).val();
            if (val) {
                val = '^'+$(this).val()+'$';
            }
            dataTable.column( filterColumn )
              .search( val , true, false )
              .draw();
        });

    var select = $('<input class="inline input-sm form-control" style="width: 125px" type="text" />')
    .appendTo(span)
    .on('keyup', function () {
        var dataTable = $("#shippingTable").DataTable();
        var filterColumn = 0; // status column
        var val = $(this).val();
        if (val)
        {
            val = '^'+$(this).val()+'$';
        }
        dataTable.column( filterColumn )
            .search( val , true, false )
            .draw();
    } );

    //packaging table order # filter
    var span = $('<span style="margin-right: 0px;" class="filter">Search By Order #</span>')
    .prependTo($('#packagingTable_wrapper .top .dataTables_filter'));

    var select = $('<input class="inline input-sm form-control" style="width: 125px" type="text" />')
    .appendTo(span)
    .on('keyup', function () {
        var dataTable = $("#packagingTable").DataTable();
        var filterColumn = 0; // status column
        var val = $(this).val();
        if (val)
        {
            val = '^'+$(this).val()+'$';
        }
        dataTable.column( filterColumn )
            .search( val , true, false )
            .draw();
    } );

    var span = $('<span class="filter">Filter By Status </span>')
          .prependTo($('#shippingTable_wrapper .top .dataTables_filter'));

    var select = $('#status_filter_options')
            .appendTo(span)
            .on('change', function () {
                var dataTable = $("#shippingTable").DataTable();
                var filterColumn = 4; // status column
                var val = $(this).val();
                if (val) {
                    val = '^'+$(this).val()+'$';
                }
                dataTable.column( filterColumn )
                  .search( val , true, false )
                  .draw();
            });
    var span = $('<span class="filter">Filter By Dealer </span>')
          .prependTo($('#packagingTable_wrapper .top .dataTables_filter'));

    var select = $('<select id="dealer_filter" style="width: 125px;" class="inline input-sm form-control"><option value="">Select</option></select>')
            .appendTo(span)
            .on('change', function () {
                var dataTable = $("#packagingTable").DataTable();
                var filterColumn = 4; // status column
                var val = $(this).val();
                if (val) {
                    val = '^'+$(this).val()+'$';
                }
                dataTable.column( filterColumn )
                  .search( val , true, false )
                  .draw();
            });

    var shipping_table = $('#shippingTable').DataTable();
    var packaging_table = $('#packagingTable').DataTable();

    $.each(indow_combined_orders, function (i, order) {
        shipping_table.row.add(order).draw();
    });
    $.each(indow_combined_orders_packaging, function (i, order) {
        packaging_table.row.add(order).draw();
    });

    $('#shippingTable, #packagingTable').on('change', '.changedata', function () {
        var datatable = $(this).closest('table').DataTable();
        var tr = $(this).closest('tr');
        var row = datatable.row(tr);
        var data = row.data();
        var post = {};
        post.combined = data.combined ? 1 : 0;
        post[$(this).attr('name')] = $(this).val();
        $.post('/fulfillment/update_order_combined/' + data.id, post);
    });

    // Packaging Table
    var searchbox = $('#packagingTable_filter input');
    searchbox.addClass('inline input-sm form-control');
    searchbox.css('width','125px');

    $('#shippingTable th:nth-child(6)').click(); //sort by commit date without rewriting datatables helper
});
