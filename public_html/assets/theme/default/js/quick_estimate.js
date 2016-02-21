$(function () {
    $('#qecont').click(function () {
        var popover = $(this).next();
        customer_manager_active_customers = {};
        if (!$('#quick_estimate_table .qe_row').length) {
            $('#add_row').click();
        }
    });
    $('#qecont').parent().on('change', '.product_option', function () {
        var elem = $(this).parents('tr').find('.product_type_option');
        filter_select(elem, $(this).val());
    }).on('click', '.closepopover', function () {
        $('#qe_button').click();
    }).on('click', '#quick_estimate_submit', function () {
        var items = [], estimate = {}, total_sqft = 0, i = 0, sqft = 0;
        $('#quick_estimate_table .qe_row').each(function (i, row) {
            var item = {};
            $(row).find('input, select, span').each(function (i, e) {
                if ($(this).attr('name')) {
                    if (e.tagName === 'SPAN') {
                        item[$(this).attr('name')] = $(this).html();
                    } else {
                        item[$(this).attr('name')] = $(this).val();
                    }
                }
            });
            item.id = "new";
            sqft = (Math.ceil(item.width) * Math.ceil(item.height) / 144);
            total_sqft += sqft;
            item.price = get_price(item.product_types_id, sqft)
            var quantity = parseInt($(row).find('.qty').val(), 10);
            for (i = 0; i < quantity; i++) {
                items.push(item);
            }
        });
        estimate.customer_id = 1;
        estimate.save = true;
        estimate.items = items;
        estimate.customers = customer_manager_get_customers();
        estimate.estimate_data = {
            estimate_total: $('#total').html(),
            total_square_feet: total_sqft.toFixed(2)
        };
        $('#estimatedata').val($.toJSON(estimate));
        console.log(estimate);
        $('#estimateform').submit();
    }).on('change', '.product_type_option, .product_option', function () {
        var row = $(this).closest('tr');
        var product_type_id = row.find('.product_type_option').val();
        var height_input = $( row ).find('.height_input').attr("data-height", products_info[product_type_id].max_height);
        var width_input = $( row ).find('.width_input').attr("data-width", products_info[product_type_id].max_width);
        if (product_type_id == 1 || product_type_id == 66) {
            height_input.attr('data-height2', 73.5);
            width_input.attr('data-width2', 97.5);
        } else {
            height_input.removeAttr('data-height2');
            width_input.removeAttr('data-width2');
        }
        if (!product_type_id) {
            return;
        }

        validate_width(row, row.find('.width_input, .height_input'));
        update_price(this);
    })
    .on('keyup', '.width_input, .height_input', function () {
        var row = $(this).closest("tr");
        validate_width(row, this);
        update_price(this);
    });


});

    function clone_row() {
        var tr = $('#clone');
        var clone = tr.clone();

        /* set default max width and heights */
        var product_type_id = Object.keys(products_info)[0];
        //$(clone).find('.height_input').attr("data-height", products_info[product_type_id].max_height);
        //$(clone).find('.width_input').attr("data-width", products_info[product_type_id].max_width);
        $(clone).find('.product_type_option').change(); // force max width and height to be set

        //$('.qe_row').last().after(clone);
        $('#quick_estimate_table tbody').append(clone);
        $('.qe_row').last().attr('id', '');
        var elem = $(clone).find('.product_type_option');
        filter_select(elem, $(clone).find('.product_option').val());
    }

    function show_customer(object) {
        $(object).hide();
        $('#customer_pane_inner').html($('#clonecont #customer_manager_cont').clone());
        customer_manager_init();
        $('#customer_pane').show();
    }
    function update_totals() {
        var total = 0;
        $('.cost').each(function () {
            subtotal = parseFloat($(this).html());
            total = parseFloat(total) + parseFloat(subtotal);
            $('#total').html(total.toFixed(2));
        });
    }
    function delete_row(object) {
        // Count of all hidden qe_rows on page
        var hidden_count = 2;

        // Don't get rid of the last visible qe_row
        if ($('.qe_row').length > hidden_count) {
            $(object).closest('tr').remove();

        }
        update_totals();
    }

    function update_price(object) {
        var sqft;
        var row = $(object).closest('tr');
        var width_elem = row.children('#width').children('.width_input');
        var width = width_elem.val();
        var height_elem = row.children('#height').children('.height_input')
        var height = height_elem.val();
        var qty = row.children('#qty').children('.qty').val();
        var product_type_id = row.find('.product_type_option').val();
        var sqft = ((Math.ceil(width) * Math.ceil(height)) / 144);

        var product_type = products_info[product_type_id];
        var product_id = 0;
        if (product_type) {
            product_id = product_type.product_id;
        }

        if (sqft || product_id == 3) {
            price = Math.round(get_price(product_type_id, sqft)) * qty;
        } else {
            price = 0;
        }

        if ((product_id != 3) && (width == '' || height == '' || qty == '' || price == '' || height_elem.hasClass('error') || height_elem.hasClass('fraction_error') || width_elem.hasClass('error') || width_elem.hasClass('fraction_error'))) {
            price  = 0;
        }
        row.children('#cost').children('.cost').html(price.toFixed(2));
        update_totals();
    }

    function qe_close() {
        $('#qe_button').click();
    }