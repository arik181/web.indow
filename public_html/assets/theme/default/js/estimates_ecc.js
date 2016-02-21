$(function () {
    function get_options() {
        return tableoptions = {
                "columnDefs": [{
                  "targets": 0,
                  "orderable": false
                }],
                "iDisplayLength": 500,
                "language": {
                    "emptyTable": "There are no items associated with this estimate.",
                },
                "dom": "<rt>",
                "pagingType": "full_numbers",
                "columns": [
                    {'data':'id'},
                    {'data':'room'},
                    {'data':'location'},
                    {'data':'width'},
                    {'data':'height'},
                    {'data':'product'},
                    {'data':'product_types_id'},
                    {'data':'edging_id'},
                    {'data':'special_geom'},
                    {'data':'price'}
                ],
                "createdRow": function( row, data, index ) {
                    $('td:eq(0)', row).html('<input class="withselected" type="checkbox" name="id">');
                    $('td:eq(5)', row).html(estimates_product_options[data.product]);
                    $('td:eq(6)', row).html(products_info[data.product_types_id].abbrev);
                    $('td:eq(7)', row).html(estimates_edging_options[data.edging_id]);
                    $('td:eq(8)', row).html(data.special_geom == 1 ? 'Yes': 'No');
                },
        }
    }
    
    function row_html(data) {
        var ret = '<tr><td>' + products_info[data.product_type_id].product_type + '</td>';
        ret += '<td>Qty: ' + data.quantity + '</td><td class="text-right">$' + data.price + '</td>';
        ret += '</tr>';
        return ret;
    }

    function add_child_products (row, products) {
        var shown = row.child.isShown();
        if (!shown) {
            newrowhtml = "<h4 class='nomargin'>Associated Products</h4>";
            childtable = $("<table class='childproductstable'></table>");
        } else {
            childtable = row.child().find('.childproductstable');
        }

        for (var i=0; i<products.length; i++) {
            childtable.append(row_html(products[i]));
        }

        if (!shown) {
            newrowhtml += $('<div>').append(childtable).html();
            row.child(newrowhtml).show();
        }
    }
    
    $(function () {
        function total_rows(subtotal) {
            var total = 0;
            var data = table1.data();
            if (subtotal === undefined) {
                subtotal = 0;
                total_sq_feet = 0;
                $.each(data, function (i, item) {
                    subtotal += parseFloat(item.price);
                    if (item.children) {
                        $.each(item.children, function (i, e) {
                            subtotal += parseFloat(item.price);
                        });
                    }
                    total_sq_feet += (item.width * item.height) / 144;                    
                });
            }
            totals = calc_fees_discounts(subtotal, data);
            write_totals(totals);            
        }
        tableoptions = get_options();
        var table1r = $('#itable1').dataTable(tableoptions);
        var table1 = table1r.api();

        tableoptions = get_options();
        tableoptions.language.emptyTable = 'There are no items left to add to this estimate.';
        var table2r = $('#itable2').dataTable(tableoptions);
        var table2 = table2r.api();
        var totals = {};
        var total_sq_feet = 0;

        
        //populate tables
        var subtotal = 0;
        $.each(estimates_items, function (i, item) {
            var ctable, rownum;
            item.product = products_info[item.product_types_id].product_id;
            item.id = "new";
            if (item.checked) {
                rownum = table1r.fnAddData(item);
                ctable = table1;
                subtotal += parseFloat(item.price);
                total_sq_feet += (item.width * item.height) / 144;
                if (item.children) {
                    $.each(item.children, function (i, subitem) {
                        subtotal += parseFloat(subitem.price);
                    });
                }
            } else {
                rownum = table2r.fnAddData(item);
                ctable = table2;
            }
            if (item.children) {
                row = ctable.row(rownum);
                add_child_products(row, item.children);
            }
        });
        total_rows(subtotal);

        $('.checkall').click(function () {
            $(this).parents('table').find('.withselected').prop('checked', $(this).prop('checked'));
        });
 
        $('#itable1').on('click', 'tr', function () {
            var row = table1.row(this);
        });
        $('#moveitemup').click(function() {
            $('#itable2').find('.withselected:checked').each(function () {
                var row = table2.row($(this).parents('tr'));
                var data = row.data();
                var rowindex = table1r.fnAddData(data);
                var newrow = table1.row(rowindex);
                if (row.child.isShown()) {
                    newrow.child(row.child()).show();
                }
                row.remove().draw();
            });
            total_rows();
        });
        $('#moveitemdown').click(function() {
            $('#itable1').find('.withselected:checked').each(function () {
                var row = table1.row($(this).parents('tr'));
                var data = row.data();
                var rowindex = table2r.fnAddData(data);
                var newrow = table2.row(rowindex);
                if (row.child.isShown()) {
                    newrow.child(row.child()).show();
                }
                row.remove().draw();
            });
            total_rows();
        });
        $('#submit_page').click(function () {
            var estimate = {
                save: true,
                items: [],
                fees: indow_active_fees,
                user_fees: {},
                delete_user_fees: [],
                customers: estimates_customers,
                estimate_data: {
                    parent_estimate_id: estimates_parent_id,
                    name: $('#estimate_name').val(),
                    estimate_total: totals.total,
                    total_square_feet: total_sq_feet,
                    site_id: estimates_site_id
                }
            };
            var count = 0;
            $.each(indow_user_fees, function (i, e) {
                delete e.id;
                estimate.user_fees['new_' + count] = e;
                count++;
            });
            
            if (estimate.estimate_data.name == '') {
                alert('Please enter a name for the estimate before continuing.');
                return;
            }

            var data = table1.data();
            $.each(data, function (i, item) {
                estimate.items.push(item);
                estimate.total_square_feet += item.height * item.width;
            });
            $('#estimatedata').val($.toJSON(estimate));
            $('#estimateform').submit();
        });
    });

});
