var indow_module_obj, est_total_rows, estimates_totals, indow_ajax_save, indow_save_fees;
jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "num-html-pre": function ( a ) {
        var x = String(a).replace( /<[\s\S]*?>/g, "" );
        return parseFloat( x );
    },
 
    "num-html-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
 
    "num-html-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );

$(indow_module_obj = function () {
    $.fn.sum = function() {
        var total = 0;
        $(this).each(function (i, e) {
            total += parseFloat($(e).html());
        });
        return total;
    };

    function object_to_array(obj) {
        var ret = [];
        $.each(obj, function (i, e) {
            ret.push(e);            
        });
        return ret;
    }

    function row_html(subitem, sqft) {
        var product = products_info[subitem.product_type_id];
        var ret = "<tr class='childrow'>";
        var price = get_price(subitem.product_type_id, sqft) * subitem.quantity;
        console.log(sqft, price, subitem.product_type_id);
        ret += "<td class='widetd'>" + product.product_type + "</td>";
        ret += "<td>Qty <span class='quantity'>" + subitem.quantity + "</span></td><td class='pricetd'>$<span name='price' class='item_price'>" + price.toFixed(2) + "</span></td></tr>";
        return ret;
    }

    function add_child_products ($row, products) {
        var row, nextrow, childtable, newrowhtml, i, shown, sqft;
        row = table.row($row);
        shown = row.child.isShown();
        sqft = $row.find('.item_sqft').html();

        if (!shown) {
            newrowhtml = "";
            childtable = $("<table class='childproductstable'></table>");
        } else {
            nextrow = $row.next();
            childtable = nextrow.find('.childproductstable');
        }

        for (var i=0; i<products.length; i++) {
            childtable.append(row_html(products[i], sqft));
        }

        if (!shown) {
            newrowhtml += $('<div>').append(childtable).html();
            row.child(newrowhtml).show();
        }
        total_rows();
    }

    function get_item(row, include_all) {
        var item = {};
        $(row).find('select, input, span').each(function () {
            var name = $(this).attr('name');
            if (!name) {
                return;
            }
            if (this.tagName === 'INPUT' || this.tagName === 'SELECT') {
                if ($(this).attr('type') === 'checkbox' && name !== 'id') {
                    if ($(this).prop('checked')) {
                        item[name] = 1;
                    } else {
                        item[name] = 0;
                    }
                } else {
                    if (name === 'id') {
                        item.checked = $(this).prop('checked');
                    }
                    item[name] = $(this).val();
                }
            } else if (this.tagName === 'SPAN') {
                item[name] = $(this).html();
            }
        });
        if (include_all) {
            item.product_id = $(row).find('.product_options').val();
            item.square_feet = Math.round(Math.ceil(item.height) * Math.ceil(item.width) / 144 * 100) / 100;
        }
        return item;
    }

    function total_rows() {
        var subtotal = 0,
            total = 0,
            sqft,
            sqft_total = 0,
            totals = {},
            total_products = 0,
            total_windows = 0;
        $('.item_price').each(function () {
        });

        var data = [];
        $('#itemsTable tbody tr[role="row"]').each(function () {
            var price = parseFloat($(this).find('.item_price').html());
            var special_geom = $(this).find('.special_geom').text() == 'yes' ? 1 : 0;
            data.push({price: price, special_geom: special_geom});
            sqft_total += parseFloat($(this).find('.item_sqft').html());
        });
        sqft = $('.item_sqft');

        subtotal = $('.item_price').sum();
        totals = calc_fees_discounts(subtotal, data);
        total_windows = $('tr[role="row"]').length - 1;
        total_products = $('.quantity').sum() + total_windows;
        $('#total_windows').text(total_windows);
        $('#total_products').text(total_products);
        $('#total_sqft').text(Math.round(sqft_total * 100) / 100);

        write_totals(totals);
    }
    est_total_rows = total_rows;


    var ajax_url = null;
    if (entity_type === 'estimate') {
        ajax_url = "/estimates/item_list_json/" + entity_id;
    } else if (entity_type === 'quote') {
        ajax_url = "/quotes/item_list_json/" + entity_id;
    }
    var dataTable = $('#itemsTable').dataTable({
            "columnDefs": [{
              "targets": 0,
              "orderable": false
            }],
            "iDisplayLength": 500,
            "ajax": ajax_url,
            "dom": "<rt>",
            "pagingType": "full_numbers",
            "language": {
                "paginate": {
                    "previous":"&laquo;",
                    "next":"&raquo;",
                },
                "emptyTable": "There are no items associated with this " + entity_type + '.',
            },
            "aaSorting": [[0,'asc'], [1,'asc']],
            "aoColumnDefs": [{ "type": "num-html", "targets": 1 }],
            "columns": [
                {'data':'room'},
                {'data':'location'},
                {'data':'width'},
                {'data':'height'},
                {'data':'product_id'},
                {'data':'product_types_id'},
                {'data':'edging_id'},
                {'data':'special_geom'},
                {'data':'price'}
            ],
            "createdRow": function( row, data, index ) {
                $('td:eq(0)', row).html('<span>' +  data['room']     + '</span>');
                $('td:eq(1)', row).html('<span>' +  data['location'] + '</span>');
                if (entity_type === 'quote') {
                    $('td:eq(2)', row).html('<span>' +  Math.max(data.measurements.A, data.measurements.B)    + '</span>');
                    $('td:eq(3)', row).html('<span>' +  Math.max(data.measurements.C, data.measurements.D)   + '</span>');
                } else {
                    $('td:eq(2)', row).html('<span>' +  data['width']    + '</span>');
                    $('td:eq(3)', row).html('<span>' +  data['height']   + '</span>');
                }
                $('td:eq(4)', row).html('<span>' +  data['product']  + '</span>');
                $('td:eq(5)', row).html('<span>' +  products_info[data['product_types_id']].product_type  + '</div>');
                $('td:eq(6)', row).html('<span>' +  data['edging']   + '</div>');
                //$('td:eq(7)', row).html('<input name="special_geom" type="checkbox" class="special_geom">').find('input').prop('checked', parseInt(data.special_geom, 10)).attr('disabled', 'disabled');
                $('td:eq(7)', row).html('<span class=\'special_geom\'>' + ((data['special_geom'] == 0) ? 'no' : 'yes') + '</span>');
                $('td:eq(8)', row).html('$<span name="price" class="item_price" data-price="'+parseFloat(data.price).toFixed(2)+'">' + parseFloat(data.price).toFixed(2) + '</span>');
                if (estimates_subitems[data.id]) {
                    add_child_products($(row), estimates_subitems[data.id]);
                } else if (data.subproducts && getObjectSize(data.subproducts)) {
                    add_child_products($(row), object_to_array(data.subproducts));
                }
            },
            "initComplete": function() {
                total_rows();
                $('.width_input').change();
                $('.height_input').change();
            }
    });
    var table = dataTable.api();
    var delete_items = [];
    var set_customer = 0;
    var page_totals;
    if (entity_type === 'quote') {
        $('#itemsTable').on( 'draw.dt', function () {
            setTimeout(window.print, 500); // allow subproducts to load
        });
    }
});
