var tmp;
if ($.fn.popover) {
    tmp = $.fn.popover.Constructor.prototype.show;
    $.fn.popover.Constructor.prototype.show = function () {
      tmp.call(this);
      if (this.options.callback) {
        this.options.callback();
      }
    }
}
var current_total_rows;


$.fn.error_fraction = function() {
    if (this.val().indexOf('/') !== -1) {
        this.addClass('fraction_error');
    } else {
        this.removeClass('fraction_error');
    }
}

function validate_width(row, elem, calc_price) {
    var height = row.find('.height_input').val();
    var width = row.find('.width_input').val();
    var product_type_id = row.find('.product_type_options').val();
    var sqft = parseFloat((Math.ceil(width) * Math.ceil(height) / 144).toFixed(2));
    var disp_sqft = sqft;
    var price = 0;

    var x = $(row).find('.width_input');
    var y = $(row).find('.height_input');
    x.error_fraction();
    y.error_fraction();
    var width = $(x).attr('data-width');
    var height = $(y).attr('data-height');
    var high  = Math.max(height,width);
    var low   = Math.min(height,width);
    var width2 = $(x).attr('data-width2');
    var height2 = $(y).attr('data-height2');
    var high2  = Math.max(height2,width2);
    var low2   = Math.min(height2,width2);
    var secondary = width2 ? true : false;
    if((x.val() > high || y.val() > low) && (x.val() > low || y.val() > high) && (!secondary || ((x.val() > high2 || y.val() > low2) && (x.val() > low2 || y.val() > high2)))){
        $(elem).addClass('error');
        price = 0;
        disp_sqft = 0;
    } else {
        $(x).removeClass('error');
        $(y).removeClass('error');
        price = get_price(product_type_id, sqft);
    }

    if (calc_price) {
        row.find('.item_sqft').text(disp_sqft);
        row.find('.item_price').text(price.toFixed(2));
        row.find('.item_price').attr('data-price', price.toFixed(2));
    }
}

$.fn.addspinner = function () {
    if (this.length) {
        var spincont = $('<div style="position: absolute" id="spin_cont" class="modal-backdrop fade in"></div>');
        
        $(this[0]).prepend(spincont).css('position', 'relative');
        var opts = {
          lines: 13, // The number of lines to draw
          length: 20, // The length of each line
          width: 10, // The line thickness
          radius: 30, // The radius of the inner circle
          corners: 1, // Corner roundness (0..1)
          rotate: 0, // The rotation offset
          direction: 1, // 1: clockwise, -1: counterclockwise
          color: '#ffffff', // #rgb or #rrggbb or array of colors
          speed: 1, // Rounds per second
          trail: 60, // Afterglow percentage
          shadow: false, // Whether to render a shadow
          hwaccel: false, // Whether to use hardware acceleration
          className: 'spinner', // The CSS class to assign to the spinner
          zIndex: 2e9, // The z-index (defaults to 2000000000)
          top: '50%', // Top position relative to parent
          left: '50%' // Left position relative to parent
        };
        var spinner = new Spinner(opts).spin(spincont[0]);
    }
};
$.fn.addcontentspinner = function () {
    if (this.length) {
        //var spincont = $('<div style="position: absolute" id="spin_cont" class="modal-backdrop fade in"></div>');
        
        //$(this[0]).prepend(spincont).css('position', 'relative');
        var opts = {
          lines: 13, // The number of lines to draw
          length: 20, // The length of each line
          width: 10, // The line thickness
          radius: 30, // The radius of the inner circle
          corners: 1, // Corner roundness (0..1)
          rotate: 0, // The rotation offset
          direction: 1, // 1: clockwise, -1: counterclockwise
          color: '#000000', // #rgb or #rrggbb or array of colors
          speed: .7, // Rounds per second
          trail: 60, // Afterglow percentage
          shadow: false, // Whether to render a shadow
          hwaccel: false, // Whether to use hardware acceleration
          className: 'spinner', // The CSS class to assign to the spinner
          zIndex: 2e9, // The z-index (defaults to 2000000000)
          top: '50%', // Top position relative to parent
          left: '50%' // Left position relative to parent
        };
        var spinner = new Spinner(opts).spin(this[0]);
    }
};

function balert(message) {
    bootbox.dialog({message: message, buttons: {okay: {className: 'btn-blue', label: 'Okay'}}});
}


if (typeof indow_module === 'undefined') {
    var indow_module = undefined;
}

function get_square_feet(data) {
    if (data.measurements.B !== undefined && data.measurements.D !== undefined) {
        var width = Math.max(data.measurements.A ? data.measurements.A : 0, data.measurements.B ? data.measurements.B : 0);
        var height = Math.max(data.measurements.C ? data.measurements.C : 0, data.measurements.D ? data.measurements.D : 0);
        var sqft = parseFloat((Math.ceil(height) * Math.ceil(width) / 144).toFixed(2));
        if (isNaN(sqft)) {
            return 0;
        } else {
            return sqft;
        }
    } else {
        return 0;
    }
}

function get_new_item(mfg_status, id) {
    var item = {
        id:                     id ? id : 'new',
        room:                   '',
        width:                  0,
        height:                 0,
        edging_id:              1,
        price:                  0,
        location:               '',
        unit_num:               '',
        product_id:             4,
        special_geom:           0,
        frame_depth_id:         window.indow_module === 'freebird' ? null : 1,
        product_types_id:       66 ,
        subproducts:            {},
        measurements:           {},
        manufacturing_status:   1,
        acrylic_panel_thickness: '',
        price_override: null
    };
    if (mfg_status !== undefined) {
        item.manufacturing_status = mfg_status;
    }
    if (indow_module === 'jobsites') {
        item.width = '';
        item.height = '';
        item.edging_id = '';
        item.price = 0;
    } else if(indow_module === 'orders') {
        item.valid = 1;
    }
    return item;
}

//this function is duplicated in quick_estimate.js. any relevant changes must also be made there
function get_price(product_type_id, sqft, module) {
    var price, product;
    sqft = parseFloat(sqft);
    if (module === 'order_review') {
        product = products_info_msrp[product_type_id];
    } else {
        product = products_info[product_type_id];
    }
    if (!product) {
        return 0;
    }
    if (!sqft && product.product_id != 3) {
        return 0;
    }
    var unit_price = parseFloat(product.unit_price);
    if (product.unit_price_type === 'unit' || product.product_id == 3) {
        price = unit_price;
    } else {
        price = sqft * unit_price;
    }
    if (price < product.min_price) {
        price = parseFloat(product.min_price);
    }
    return Math.ceil(price);
}

function total_subrows(row) {
    var total = 0;
    $.each(row.subproducts, function (i, sp) {
        total += sp.price_override !== null ? parseFloat(sp.price_override) : get_price(sp.product_type_id, 0) * sp.quantity;
    });
    return total;
}

function get_thickness(ptype, sqft) {
    ptype = parseInt(ptype, 10);
    var product = products_info[ptype];
    var spec_products = [6, 7, 8, 45, 46, 47, 70, 71, 74];
    if ($.inArray(ptype, spec_products) !== -1) {
        return '1/4"';
    } else {
        if (product.product_id == 1) {
            if (sqft < 20) {
                return '1/8"';
            } else {
                return '3/16"';
            }
        } else {
            return '1/8"';
        }
    }
}

function get_totals(data, credit_limit, module) {
    var rowprice;
    var total = 0;
    var items_over_limit = 0;
    var total_over_limit = false;
    $.each(data, function (i, row) {
        var sqft = get_square_feet(row);
        if (window.indow_module === 'orders') {
            rowprice = row.price_override !== null ? parseFloat(row.price_override) : get_price(row.product_types_id, sqft, module);
        } else {
            rowprice = get_price(row.product_types_id, sqft, module);
        }
        row.price = rowprice;
        row.acrylic_panel_thickness = get_thickness(row.product_types_id, sqft);
        if (credit_limit !== undefined && rowprice > credit_limit) {
            items_over_limit++;
        }
        total += rowprice + total_subrows(row);
    });

    var totals = calc_fees_discounts(total, data, module);

    totals.warnings = [];
    if (credit_limit) {
        if (items_over_limit) {
           // totals.warnings.push(items_over_limit + ' items excede the credit limit of $' + parseFloat(credit_limit).toFixed(2) + '.');
        }
        if (total / 2 > credit_limit) {
           // totals.warnings.push('1/2 the total is over the credit limit of $' + parseFloat(credit_limit).toFixed(2) + '.');
        }
    }

    if (typeof indow_payments !== 'undefined') {
        var payments = 0;
        $.each(indow_payments, function (i, e) {
            payments += parseFloat(e.payment_amount);
        });
        totals.payments = payments;
        totals.due = totals.total - totals.payments;
    }
    console.log('ttotals', totals);
    return totals;
}

function fix_height() { //forces dealer/shipto/customer columns to be same height.
    var max = 0;
    var chunks = $('.data-chunk');
    chunks.each(function () {
        max = Math.max(max, $(this).height());
    });
    chunks.css('height', max + 10 + 'px');
}

function filter_subproducts(itable, $row, $checkboxes) {
    var data = itable.row($row).data();
    var subproducts = {};
    $.each(data.subproducts, function (i, e) {
        subproducts[e.product_type_id] = 1;
    });
    $checkboxes.each(function () {
        if (subproducts[$(this).val()]) {
            $(this).parent().remove();
        }
    });
}

function clean_array(data) { //turns table.data() into normal array;
    var items = [];
    $.each(data, function (i, e) {
        items.push(e);
    });
    return items;
}

function render_subproducts(itable, $row, readonly) {
    var html, table, rowhtml;
    var row = itable.row($row);
    var data = row.data();
    if (!getObjectSize(data.subproducts)) {
        row.child('').hide();
    } else {
        html = "<h4 class='products-head'><span class='pull-left'>&#8627;</span>Associated Products</h4>";
        table = $('<table class="childproductstable"></table>');
        $.each(data.subproducts, function (i, sp) {
            var price;
            var product = products_info[sp.product_type_id];
            rowhtml = '<tr class="subitem" data-id="' + sp.product_type_id + '">';
            if (!readonly) {
                rowhtml +='<td class="smalltd"><input type="checkbox" class="withselected" data-product_type_id="' + sp.product_type_id + '" value="' + sp.id + '"></input></td>';
            }
            rowhtml += '<td style="width: 60%;">' + product.product_type + '</td><td width="20%">Qty: ';
            if (readonly) {
                rowhtml += sp.quantity;
            } else {
                rowhtml += '<input class="inline input_tiny quantity input-sm form-control" data-product_type_id="' + sp.product_type_id + '" value="' + sp.quantity + '">';
            }
            rowhtml += '</td>';
            if (indow_module === 'jobsites' || indow_module === 'orders' || indow_module === 'quotes') {
                price = Math.max(product.unit_price, product.min_price) * sp.quantity;
                if (indow_module === 'orders') {
                    rowhtml += '<td width="10%" class="sprice text-right">' + get_subprice_html(price, sp.price_override) + '</td><td width="30%"></td>';
                } else {
                    rowhtml += '<td class="sprice text-right">$' + Math.ceil(price).toFixed(2) + '</td><td width="35%"></td>';
                }
            }
            rowhtml += '</tr>';
            table.append(rowhtml);
        });
        html += $('<div></div>').append(table).html();
        row.child(html).show();
    }
}
function render_subproducts_ro(itable, $row) {
    var html, table, rowhtml;
    var row = itable.row($row);
    var data = row.data();
    if (!getObjectSize(data.subproducts)) {
        row.child('').hide();
    } else {
        table = $('<table class="childproductstable"></table>');
        $.each(data.subproducts, function (i, sp) {
            var price = sp.price_override !== null ? sp.price_override : sp.price;
            rowhtml = '<tr><td style="width: 30%"><td>' + sp.name + '</td><td>Qty: ' + sp.quantity + '</td><td style="width: 68px">$' + parseFloat(price).toFixed(2) + '</td></tr>';
            table.append(rowhtml);
        });
        html = $('<div></div>').append(table).html();
        row.child(html).show();
    }
}

function get_parent_row($row) {
    return $row.parents('tr').prev();
}

function withselected_delete(table) {
    var renderqueue = [];
    var id;
    $('.withselected:checked').each(function () {
        var type, data, $parentrow;
        var $row = $(this).closest('tr');
        if ($row.hasClass('subitem')) {
            id = $(this).val();
            var product_type_id = $(this).data('product_type_id');
            $parentrow = get_parent_row($row);
            type = 'subitem';
            data = table.row($parentrow).data();
            if (data) {
                delete data.subproducts[product_type_id];
                renderqueue.push($parentrow);
            }
        } else {
            id = table.row($row).data().id;
            type = 'item';
            table.row($row).remove().draw();
        }
        if (id !== 'new') {
            indow_delete_items.push({type: type, id: id});
        }
        if (window.total_rows) {
            total_rows();
        }
    });
    $.each(renderqueue, function (i, row) { //needs to be done outside of the checkbox loop because it rewrites the table, including the checkboxes you are iterating through and bad things happen
        render_subproducts(table, row);
    });
}

function getObjectSize(obj) {
    var len = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) len++;
    }
    return len;
}

function calc_fee_amount(subtotal, item) {
    var amount = 0;
    if (item.modifier_type === 'fee' || item.modifier_type === 'tax') {
        if (item.modifier === 'dollar') {
            amount = item.amount;
        } else if (item.modifier === "percent") {
            amount = item.amount * subtotal / 100
        }
    } else if (item.modifier_type === 'discount') {
        if (item.modifier === 'dollar') {
            amount = item.amount * -1;
        } else if (item.modifier === 'percent') {
            amount = (item.amount * subtotal / 100) * -1;
        }
    }
    amount *= item.quantity;
    return Math.round(amount * 100) / 100;
}

function calc_fees_discounts (subtotal, data, module) {
    var wholesale_obj;
    var ret = {spec_geom: 0, spec_geom_count: 0, modifiers: {fees: [], discounts: [], taxes: []}}
    var modifiers_total = 0;
    if (typeof indow_active_fees === 'undefined') {
        window.indow_active_fees = []; //this is stupid but js hoisting requires it to be done this way.
    }
    if (typeof indow_user_fees === 'undefined') {
        window.indow_user_fees = []; //this is stupid but js hoisting requires it to be done this way.
    }
    if (!data) {
        data = [];
    }
    function sort_modifier(modifier) {
        if (modifier.modifier_type === 'fee') {
            ret.modifiers.fees.push(modifier);
        } else if (modifier.modifier_type === 'discount') {
            ret.modifiers.discounts.push(modifier);
        } else if (modifier.modifier_type === 'tax') {
            ret.modifiers.taxes.push(modifier);
        }
    }
    if (module !== 'order_review') {
        $.each(indow_active_fees, function (i, fee_id) {
            var item = indow_fee_info[fee_id];
            if (!item) {
                return;
            }
            var modifier = {
                id: fee_id,
                modifier_type: item.modifier_type,
                description: item.description,
                quantity: 1,
                type: 'normal',
                amount: calc_fee_amount(subtotal, item)
            };
            sort_modifier(modifier);
        });
        $.each(indow_user_fees, function (id, item) {
            var modifier = {
                id: id,
                modifier_type: item.modifier_type,
                quantity: item.quantity,
                description: item.description,
                type: 'user',
                amount: calc_fee_amount(subtotal, item)
            };
            sort_modifier(modifier);
        });
    }

    $.each(data, function (i, item) {
        if (parseInt(item.special_geom, 10)) {
            if (item.price) {
                ret.spec_geom_count++;
                console.log(item.price, 'spec_geom');
                ret.spec_geom += Math.max(50, item.price * .2);
            }
        }
    });
    ret.modifiers.all = ret.modifiers.fees.concat(ret.modifiers.discounts, ret.modifiers.taxes);
    ret.subtotal = subtotal;
    ret.total = ret.subtotal + ret.spec_geom;
    ret.wholesale_discount = 0;
    if (module === 'order_review' && typeof indow_review_wholesale_discount !== 'undefined' && indow_review_wholesale_discount) {
        wholesale_obj = indow_review_wholesale_discount;
    } else if (typeof indow_wholesale_discount !== 'undefined' && indow_wholesale_discount) {
        wholesale_obj = indow_wholesale_discount;
    }
    if (wholesale_obj !== undefined) {
        if (wholesale_obj.type === 'percent') {
            ret.wholesale_discount = ret.subtotal * wholesale_obj.amount / -100;
        } else if (wholesale_obj.type === 'dollar') {
            ret.wholesale_discount = -1 * wholesale_obj.amount;
        }
    }
    ret.total += ret.wholesale_discount;
    $.each(ret.modifiers.all, function (i, mod) {
        ret.total += mod.amount;
    });
    
    return ret;
}

function window_popup_show(elem, itable) {
    $('.showwindow').not(elem).popover('hide');
    var $popover = $(elem).next();
    var $row = $(elem).closest('tr');
    var row = itable.row($row);
    var data = row.data();
    console.log(data);
    $popover.find('input, select, textarea').each(function (i, input) {
        var val;
        var name = $(this).attr('name');
        if ($(this).hasClass('measurement')) {
            val = isNaN(data.measurements[name]) ? '' : data.measurements[name];
        } else {
            val = data[name];
        }
        if (val !== undefined) {
            if ($(this).attr('type') === 'checkbox') {
                $(this).prop('checked', val && val !== '0');
            }
            $(this).val(val);
        }
        $(this).data('orig', val);
    });
    var shown_measurements = get_required_measurements($popover.find('.window_shape_options').val());
    show_hide_measurements($popover, shown_measurements);
    filter_select($popover.find('.product_type_options'), data.product_id, data.product_types_id);
    if (indow_module === 'quotes') {
        $popover.find('input, select, textarea').each(function () {
            if (this.tagName === 'SELECT') {
                var val = $(this).find('option[value="' + $(this).val() + '"]').html();
                var content = $('<div>').append($('<input>').val(val)).html();
                $(this).replaceWith($('<input>').val(val).addClass('form-control input-sm').prop('disabled', 1));
            } else {
                $(this).prop('disabled', 1)
            }
        });
        $popover.find('button').css('visibility', 'hidden');
    } else if (indow_module === 'jobsites') {
        $popover.find('.windowoptionscheck').hide();
    } else if (indow_module === 'freebird') {
        $popover.find('[title]').tooltip();
        if (!$popover.closest('tr').next().length) {
            $popover.find('.measurenext').hide();
        }
        check_custom_measurement_status($popover);
    }
    var spines;
    if (data.top_spine == 1 && data.side_spines == 1) {
        spines = 'Both';
    } else if (data.top_spine == 1) {
        spines = 'Top';
    } else if (data.side_spines == 1) {
        spines = 'Side';
    } else {
        spines = 'None';
    }
    $popover.find('.spines_display').html(spines);
    $popover.find('.spines_edit').val(spines);
    $popover.find('#validation_response .alert-warning').html('');
}

function window_popup_revert(elem) {
    var $popover = $(elem).closest('.popover');
    $popover.find('input, select, textarea').each(function () {
        if ($(this).attr('type') == 'checkbox') {
            $(this).prop('checked', $(this).data('orig'));
        } else {
            $(this).val($(this).data('orig')).change();
        }
    });
}

function window_popup_save(elem, $popover, data) {
    data.measurements = {};
    var orig_product = data.product_types_id;
    $popover.find('input, select, textarea').each(function () {
        var value;
        var name = $(this).attr('name');
        if ($(this).attr('type') === 'checkbox') {
            value = $(this).prop('checked') ? 1 : 0;
        } else {
            value = $(this).val();
        }
        if (name) {
            if ($(this).hasClass('measurement')) {
                data.measurements[name] = value;
            } else {
                data[name] = value;
            }
        }
    });
    if (window.indow_module === 'orders' && orig_product != data.product_types_id) {
        data.price_override = null;
    }
    console.log('old', orig_product, 'new', data.product_types_id);
    if (indow_module === 'jobsites') {
        data.width = data.measurements.B ? data.measurements.B : '';
        data.height = data.measurements.D ? data.measurements.D : '';
    }
}

function filter_select ($select, product_id, curval) {
    $select.html('');
    $.each(products_info, function (i, product) {
        if (product.product_id == product_id && (product.product_id != 3 || product.not_opening_specific == 1)) {
            $select.append('<option value="' + product.id + '">' + product.product_type + '</option>');
        }
    });
    $select.val(curval).change();
}

function get_required_measurements(shape_id) {
    if (shape_id == 1 || shape_id == 2 || window.indow_module === 'freebird') {
        return {'A': 1, 'B': 1, 'C': 1, 'D': 1, 'E': 1, 'F': 1};
    } else {
        return {'A': 1, 'B': 1, 'C': 1, 'D': 1, 'E': 1, 'F': 1, 'G': 1, 'H': 1, 'I': 1, 'J': 1};
    }
}

function show_hide_measurements($popover, shown_measurements) {
    $popover.find('.measurement').each(function () {
        if (shown_measurements[$(this).attr('name')]) {
            if($(this).data('mode') != 'view' && (indow_module !== 'jobsites' || window.indow_measurements_editable)){
                $(this).prop('disabled', false).closest('.form-group').show();
            } else {
                $(this).prop('disabled', true).closest('.form-group').show();
            }

        } else {
            $(this).prop('disabled', true).closest('.form-group').hide();
        }
    });
}

function calc_spines($popover) {
    if (window.indow_module !== 'freebird') {
        return;
    }
    var B = $popover.find('#popover-B').val() || 0;
    var C = $popover.find('#popover-C').val() || 0;
    if ($popover.find('.plus_one').prop('checked')) {
        B += 1;
        C += 1;
    }
    var top_spine_needed 	= (B >= 42 && C >= 30); // if big window, we need top spine
	var side_spines_needed 	= (C >= 60 && B >= 36); // if bigger window we need side spines too
    var spines;
    $popover.find('[name="top_spine"]').val(top_spine_needed ? 1 : 0);
    $popover.find('[name="side_spines"]').val(side_spines_needed ? 1 : 0);
    if (top_spine_needed && side_spines_needed) {
        spines = 'both';
    } else if (top_spine_needed) {
        spines = 'top';
    } else if (side_spines_needed) {
        spines = 'side';
    } else {
        spines = 'none';
    }
    $popover.find('.spines_display').html(spines);
}

function write_totals(totals, options) {
    if (options === undefined) {
        options = {};
    }
    var idprefix = options.idprefix ? options.idprefix : '';
    if (!idprefix) {
        idprefix = '';
    }
    $('#' + idprefix + 'subtotal').html('$' + totals.subtotal.toFixed(2));
    $('#' + idprefix + 'grand_total').html('$' + totals.total.toFixed(2));
    if (totals.payments) {
        $('#' + idprefix + 'payments').html('$' + totals.payments.toFixed(2));
        $('#' + idprefix + 'balance').html('$' + totals.due.toFixed(2));
        $('.paymenttoggle').show();
    } else {
        $('#' + idprefix + 'indow_totals .paymenttoggle').hide();
    }
    if (totals.spec_geom) {
        $('#' + idprefix + 'indow_totals .spec_geom_toggle').show();
        $('#' + idprefix + 'spec_geom_fee').html('$' + totals.spec_geom.toFixed(2));
        $('#' + idprefix + 'spec_geom_count').html(totals.spec_geom_count);
    } else {
        $('#' + idprefix + 'indow_totals .spec_geom_toggle').hide();
    }
    if (totals.wholesale_discount) {
        $('#' + idprefix + 'indow_totals .wholesale_discount_toggle').show();
        $('#' + idprefix + 'wholesale_discount').html('$' + totals.wholesale_discount.toFixed(2));
    } else {
        $('#' + idprefix + 'indow_totals .wholesale_discount_toggle').hide();
    }
    $('#' + idprefix + 'indow_totals .indow_fee').remove();
    var fees_end = $('#' + idprefix + 'fees_end');
    if (totals.modifiers && totals.modifiers.all) {
        $.each(totals.modifiers.all, function (i, mod) {
            if (mod.quantity > 1) {
                mod.description += ' (' + mod.quantity + ')';
            }
            var fee_key = $('<dt class="indow_fee">').text(mod.description);
            var fee_val = $('<dd class="indow_fee">').text('$' + parseFloat(mod.amount).toFixed(2));
            if (mod.type === 'user' && !options.no_allow_fees_remove) {
                fee_val.append($('<i class="delete-modifier fa fa-times"></i>').data('id', mod.id));
            }
            fees_end.before(fee_key);
            fees_end.before(fee_val);
        });
    }
}

function bind_fees_discounts(total_rows, orientation) {
    $('#fees_discounts_cont input').each(function () {
        if ($.inArray(parseInt($(this).val(), 10),  indow_active_fees) !== -1) {
            $(this).prop('checked', true);
        }
    });
    $('#fees_discounts').popover({
        content: $('#fees_discounts_cont').clone(),
        html: true,
        placement: orientation
    });
    $('#content_view').on('click', '.fees_apply', function () {
        var fees = [];
        $(this).parents('.popover').find('input:checked').each(function () {
            fees.push(parseInt($(this).val(), 10));
        });
        indow_active_fees = fees;
        total_rows();
        $('#fees_discounts').click(); //close popover
    });
}
$(function () {
    $('#content_view').on('click', '.glabel', function () {
        $(this).prev().click();
    }).on('click', '.fglabel', function () {
        $(this).closest('.form-group').find('input, select').focus();
    });
    var ctrl_s = $('.ctrl_s');
    if (ctrl_s.length) {
        $(document).keydown(function (e) {
            if (e.ctrlKey && e.keyCode === 83) {
                e.preventDefault();
                ctrl_s.click();
            }
        });
    }
});

function stylizeRow ( row, data, index, dontRenderSubproducts, itable, total_rows, mode) {
    get_totals([data]); //force price to update

    var disabled = '';
    if(mode == 'view'){ disabled = 'disabled' }  //not sure why this was added but its breaking things
    var disabled = mode === 'view';

    if (mode === 'order_review') {
        module = 'order_review';
    } else {
        module = indow_module;
    }
    var showbutton, addbutton, show_text;

    if (module === 'freebird') {
        show_text = 'Measure';
    } else {
        show_text = '<i class="fa fa-search"></i>';
    }
    showbutton = $('<button data-id="' + data.id + '" class="btn btn-sm btn-default btn-info showwindow">' + show_text + '</button>');
    if (data.notes && (module === 'jobsites' || module === 'orders')) {
        showbutton.addClass('btn-notes');
    }
    showbutton.popover({
        elem: showbutton,
        content: $('#windowoptions').clone(),
        html: true,
        placement: (module === 'jobsites' || module === 'freebird') ? 'left' : 'right',
        callback: function () {
            window_popup_show(this.elem, itable);
        }
    });
    addbutton = $('<button data-id="' + data.id + '" class="btn btn-default btn-sm addproducts btn-info"><i class="fa fa-plus"></i></button>');
    addbutton.popover({
        content: $('#productcheckboxes').clone(),
        html: true,
        placement: 'bottom'
    });
    var select = $('<select class="form-control input-sm"></select>').attr('name', 'product_types_id').val(data.product_types_id).addClass('changedata product_type_options');
    filter_select(select, data.product_id, data.product_types_id);
    data.product_types_id = select.val();

    var select_html = '<select class="narrow-pad mfg_status form-control changedata input-sm" name="manufacturing_status"><option value="1">Include</option><option value="3">Back Order</option>';
    select_html += '<option value="4">Re-Order</option></select>';
    var mfg_status = $(select_html).val(data.manufacturing_status);

    if (module === 'orders') {
        $('td:eq(0)', row).html('<input type="checkbox" class="withselected">');
        $('td:eq(1)', row).html(mfg_status).hide();
        $('td:eq(2)', row).html($('<div class="showbuttoncont"></div>').append(showbutton));
        //$('td:eq(3)', row).html($('<input type="text"  name="unit_num" class="changedata form-control input-sm">').val(data.unit_num));
        $('td:eq(4)', row).html($('<input type="text"  name="room" class="changedata form-control input-sm">').val(data.room));
        $('td:eq(5)', row).html($('<input type="text"  name="location" class="changedata form-control input-sm">').val(data.location));
        $('td:eq(6)', row).html($('<input type="checkbox" name="special_geom" class="changedata">').prop('checked', parseInt(data.special_geom, 10)));
        $('td:eq(7)', row).html($(indow_product_options).attr('name', 'product_id').val(data.product_id).addClass('changedata'));
        $('td:eq(8)', row).html(select);
        $('td:eq(9)', row).html($(indow_edging_options).val(data.edging_id).addClass('changedata'));
        $('td:eq(10)', row).addClass('thickness');
        $('td:eq(11)', row).html($('<input type="text" name="width" class="changedata form-control input-sm">').val(data.measurements.B));
        $('td:eq(12)', row).html($('<input type="text" name="height" class="changedata form-control input-sm">').val(data.measurements.D));
        $('td:eq(13)', row).html(get_square_feet(data)).addClass('sqft');
        $('td:eq(14)', row).html(get_price_html(data.price, data.price_override)).addClass('price text-right');
        $('td:eq(15)', row).html(addbutton).addClass('addproducttd');
        if (!parseInt(data.valid, 10)) {
            $(row).addClass('invalid-measurements');
        }
        if (disabled) {
            $(row).find('input, select').prop('disabled', true);
        }
    } else if (module === 'jobsites') {
        $('td:eq(0)', row).html('<input type="checkbox" class="withselected" name="id" value="' + data.id + '">');
        $('td:eq(1)', row).html($('#clonecont .mfg_status_pop').clone().val(data.manufacturing_status).addClass('changedata'));
        $('td:eq(2)', row).html($('<input type="text" name="room" class="changedata form-control input-sm">').val(data.room));
        $('td:eq(3)', row).html($('<input type="text" name="location" class="changedata form-control input-sm">').val(data.location));
        //$('td:eq(3)', row).html($('<input name="width" class="changedata form-control input-sm">').val(data.measurements.B).prop('disabled', true));
        $('td:eq(4)', row).html(data.measurements.B);
        //$('td:eq(4)', row).html($('<input name="height" class="changedata form-control input-sm">').val(data.measurements.D).prop('disabled', true));
        $('td:eq(5)', row).html(data.measurements.D);
        $('td:eq(6)', row).html($(indow_product_options).attr('name', 'product_id').val(data.product_id).addClass('changedata'));
        $('td:eq(7)', row).html(select);
        $('td:eq(8)', row).html($(indow_edging_options).val(data.edging_id));
        $('td:eq(9)', row).html($('<input type="checkbox" name="special_geom" class="changedata">').prop('checked', parseInt(data.special_geom, 10)));
        $('td:eq(10)', row).html('$' + parseFloat(data.price).toFixed(2)).addClass('price text-right');
        $('td:eq(11)', row).html($('<div class="showbuttoncont"></div>').append(showbutton));
        $('td:eq(12)', row).html(addbutton).addClass('addproducttd');
        if (!parseInt(data.valid, 10)) {
            $(row).addClass('invalid-measurements');
        }
    } else if (module === 'quotes') {
        data.width = data.measurements.B;
        data.height = data.measurements.D;
        $('td:eq(2)', row).html(data.width);
        $('td:eq(3)', row).html(data.height);
        $('td:eq(7)', row).html($('<input type="checkbox" name="special_geom" class="changedata">').prop('checked', parseInt(data.special_geom, 10)).prop('disabled', true));
        $('td:eq(8)', row).html('$' + parseFloat(data.price).toFixed(2));
    } else if (module === 'freebird') {
        $('td:eq(0)', row).html('<input type="checkbox" class="withselected" name="id" value="' + data.id + '">');
        $('td:eq(1)', row).html($('#clonecont .mfg_status_pop').clone().val(data.manufacturing_status).addClass('changedata'));
        $('td:eq(2)', row).html(data.room);
        $('td:eq(3)', row).html(data.location);
        $('td:eq(7)', row).html(data.measurements.B);
        $('td:eq(8)', row).html(data.measurements.D);
        showbutton.attr('title', 'Enter or update your window details.').tooltip();
        $('td:eq(9)', row).html($('<div class="showbuttoncont"></div>').append(showbutton));
        var ptype = products_info[data.product_types_id];
        var product = [null, 'Legacy Insert', 'Skylight Insert', 'Accessories', 'T2 Insert'][ptype.product_id];
        $('td:eq(4)', row).html(product);
        $('td:eq(5)', row).html(ptype.abbrev);
        $('td:eq(6)', row).html(indow_edging_lookup[data.edging_id]);
        //$('td:eq(10)', row).html(addbutton).addClass('addproducttd');
        if (!parseInt(data.valid, 10)) {
            $(row).addClass('invalid-measurements');
        } else {
            $(row).removeClass('invalid-measurements');
        }
        update_measured_window_count();
    } else if (module === 'order_review') {
        get_totals([data], undefined, 'order_review');
        data.width = data.measurements.B ? data.measurements.B : '';
        data.height = data.measurements.D ? data.measurements.D : '';
        $('td:eq(2)', row).html(data.width);
        $('td:eq(3)', row).html(data.height);
        $('td:eq(4)', row).html(indow_products_json[data.product_id]);
        $('td:eq(5)', row).html(products_info[data.product_types_id].product_type);
        
        $('td:eq(6)', row).html(indow_js_edging[data.edging_id]);
        var spec_geom = $('<input type="checkbox" name="special_geom" class="changedata">').prop('checked', parseInt(data.special_geom, 10)).prop('disabled', 1);
        $('td:eq(7)', row).html(spec_geom);
        $('td:eq(8)', row).html('$' + parseFloat(data.price).toFixed(2)).addClass('price text-right');
    }
    if (getObjectSize(data.subproducts) && !dontRenderSubproducts) {
        render_subproducts(itable, row, disabled || indow_module === 'quotes');
    }
    total_rows();
}

function input_to_span(elem) {
    $(elem).find('input').each(function () {
        if ($(this).attr('type') === 'checkbox') {
            if ($(this).attr('name') !== 'special_geom') {
                $(this).remove();
            }
        } else {
            var val = $(this).val();
            $(this).replaceWith($(this).val());
        }
    });
}

function fix_height() { //forces dealer/shipto/customer columns to be same height.
    var max = 0;
    var chunks = $('.data-chunk');
    chunks.each(function () {
        max = Math.max(max, $(this).height());
    });
    chunks.css('height', max + 10 + 'px');
}

function get_order_review_info() {
    return {
        shipping_address:   indow_shipping_address,
        bundle:             indow_bundle_orders,
        po_num:             $('#orderNumber').val(),
        notes:              $('#orderNotes').val(),
        commit_date:        $('#review_commit_date').val()
    };
}

function order_review_copy_site_items() {
    var site_items = $('#windowTable').DataTable().data();
    var review_table = $('#review_itemtable').DataTable();
    review_table.clear();
    write_totals({subtotal: 0, total: 0}, {idprefix: 'order_review'});
    $.each(site_items, function (i, item) {
        if (item.checked == 1 && item.manufacturing_status == 1) {
            review_table.row.add(item).draw();
        }
    });
    review_table.draw();
    input_to_span($('#review_itemtable'));
}

function get_checked_items() {
    var site_items = $('#windowTable').DataTable().data();
    var item_ids = [];
    $.each(site_items, function (i, item) {
        if (item.checked == 1 && item.manufacturing_status == 1 && item.id) {
            item_ids.push(parseInt(item.id, 10));
        }
    });
    return item_ids;
}

function order_review_copy_quote_items() {
    var quote_items = $('#quoteitems').DataTable().data();
    var review_table = $('#review_itemtable').DataTable();
    review_table.clear();
    
    $.each(quote_items, function (i, item) {
        review_table.row.add(item).draw();
    });
    review_table.draw();
    input_to_span($('#review_itemtable'));
}

function order_review_show() {
    $('#order_review_customer').html($('#contact_info_view').html());
    $('#order_review_customer .contact_group').remove();
    if (indow_module === 'jobsites') {
        order_review_copy_site_items();
    } else if (indow_module === 'quotes') {
        order_review_copy_quote_items();
    }
    $('#orderReview').modal('show');
    setTimeout(fix_height, 1000); //for some reason this only works after the modal is done animating, and bootstrap modal callbacks do not get called - numerous bug reports online stating the same
}

function check_required_info() {
    var problems = [];
    if (!indow_site_id) {
        problems.push('A job site must be entered before continuing.');
    }
    var has_primary = false;
    $.each(customer_manager_get_customers(), function (i, e) {
        has_primary = has_primary || e.primary;
    });
    if (!has_primary) {
        problems.push('A primary customer must be added before continuing.');
    }
    if (problems.length) {
        alert(problems.join('\n'));
    }
    return !problems.length
}

var indow_item_tables = [];

function get_price_html(price, override) {
    var dprice, html;
    if (window.indow_module === 'orders') {
        dprice = override !== null ? override : price;
    } else {
        dprice = price;
    }
    if (window.indow_module === 'orders' && window.override_perm) {
        var disabled = override === null ? 'disabled="disabled"' : '';
        var checked = override === null ? '' : 'checked="checked"';
        html = '<input ' + checked + ' type="checkbox" class="price_override"><span class="dollar">$</span><input type="text" class="inline form-control input-sm changedata" name="price_override" value="' + parseFloat(dprice).toFixed(2) + '" ' + disabled + '>';
    } else {
        html = '$' + parseFloat(dprice).toFixed(2);
    }
    return html;
}

function get_subprice_html(price, override) {
    var dprice, html;
    price = Math.ceil(price);
    if (window.indow_module === 'orders') {
        dprice = override !== null ? override : price;
    } else {
        dprice = price;
    }
    if (window.indow_module === 'orders' && window.override_perm) {
        var disabled = override === null ? 'disabled="disabled"' : '';
        var checked = override === null ? '' : 'checked="checked"';
        html = '<input ' + checked + ' type="checkbox" class="sub_price_override"><span class="dollar">$</span><input type="text" class="inline form-control input-sm subprice_input" name="price_override" value="' + parseFloat(dprice).toFixed(2) + '" ' + disabled + '>';
    } else {
        html = '$' + parseFloat(dprice).toFixed(2);
    }
    return html;
}

function bind_itable_js(ctable, obj, total_rows, options) {
    indow_item_tables.push(ctable);
    current_total_rows = total_rows;
    if (!options) {
        options = {};
    }
    $(obj)
        .on('change', '.changedata', function () {
            var name = $(this).attr('name');
            var $row = $(this).closest('tr');
            var row = ctable.row($row);
            var data = row.data();
            if (name === 'height') {
                data.measurements.D = $(this).val();
            } else if (name === 'width') {
                data.measurements.B = $(this).val();
            } else if ($(this).attr('type') === 'checkbox') {
                data[name] = $(this).prop('checked') ? 1 : 0;
            } else {
                data[name] = $(this).val();
            }
            if (name === 'product_types_id' || name === 'product_id') {
                data.price_override = null;
            }
            get_totals([data]);
            total_rows();
            $row.find('.price').html(get_price_html(data.price, data.price_override));
            $row.find('.sqft').html(get_square_feet(data));
            $row.find('.thickness').html(data.acrylic_panel_thickness);
            if (window.indow_module === 'freebird') {
                $.post('/orders/update_item_ajax/' + order_id, data);
            }
        }).on('change', '.price_override', function () {
            var $row = $(this).closest('tr');
            var row = ctable.row($row);
            var data = row.data();
            var input = $(this).closest('td').find('.changedata');
            var override = $(this).prop('checked');
            input.prop('disabled', !override);
            if (!override) {
                data.price_override = null;
                $row.find('[name="height"]').change();
            } else {
                data.price_override = data.price;
            }
        }).on('change', '.sub_price_override', function () {
            var $row = $(this).closest('tr');
            var product = products_info[$row.find('.withselected').data('product_type_id')];
            var $prow = $row.parents('tr').prev();
            var row = ctable.row($prow);
            var quantity = $row.find('.quantity').val();
            var input = $(this).closest('td').find('.subprice_input');
            var override = $(this).prop('checked');
            var data = row.data();
            input.prop('disabled', !override);
            var computed_price = Math.ceil(Math.max(product.unit_price, product.min_price) * quantity);
            if (!override) {
                data.subproducts[product.id].price_override = null;
                input.val(computed_price.toFixed(2));
            } else {
                data.subproducts[product.id].price_override = computed_price;
            }
            total_rows();
        }).on('change', '.subprice_input', function () {
            $(this).val(Math.ceil($(this).val()).toFixed(2));
            var $row = $(this).closest('tr');
            var product = products_info[$row.find('.withselected').data('product_type_id')];
            var $prow = $row.parents('tr').prev();
            var row = ctable.row($prow);
            var quantity = $row.find('.quantity').val();
            var input = $(this).closest('td').find('.subprice_input');
            var override = $(this).prop('checked');
            var data = row.data();
            data.subproducts[product.id].price_override = $(this).val();
            total_rows();
        }).on('change', '.product_options', function () {
            var row = $(this).closest('tr');
            var product_id = $(this).val();
            var ptype = row.find('.product_type_options');
            filter_select(ptype, product_id);
        }).on('click', '.showwindow', function () {
            var $row = $(this).closest('tr');
            var row = ctable.row($row);
            var data = row.data();

            $('.windowoptionsrevert').prop('disabled', true);
            var $popover = $(this).next();
            $popover.find('input, select').each(function (i, input) {
                var name = $(this).attr('name');
                if (data[name] !== undefined) {
                    $(this).val(data[name]);
                }
            });
        }).on('click', '.addproductssubmit', function () {
            function add_subproduct($row, product) {
                var row = ctable.row($row);
                row.data().subproducts[product.product_type_id] = product;
            }
            var popover = $(this).closest('.popover');
            var parentrow = $(this).closest('tr');
            popover.find('input').each(function () {
                var product;
                if ($(this).prop('checked')) {
                    product = {
                        product_type_id: $(this).val(),
                        id: 'new',
                        price_override: null,
                        quantity: 1
                    };
                    add_subproduct(parentrow, product);
                }
            });
            popover.prev().click(); //close popover
            render_subproducts(ctable, parentrow);
            total_rows();
        }).on('click', '.addproducts', function () {
            filter_subproducts(ctable, $(this).closest('tr'), $(this).next().find('input'));
        }).on('change', '.quantity', function () {
            var $row = $(this).closest('tr');
            var product_type_id = $row.data('id');
            var $parentrow = $row.parents('tr').prev();
            var data = ctable.row($parentrow).data();
            var price_override = data.subproducts[product_type_id].price_override;
            data.subproducts[product_type_id].quantity = $(this).val();
            if (indow_module === 'jobsites' || indow_module === 'orders') {
                //var price = $(this).val() * products_info[product_type_id].unit_price;
                var price = $(this).val() * Math.max(products_info[product_type_id].min_price, products_info[product_type_id].unit_price);
                if (indow_module === 'orders') {
                    $(this).closest('tr').find('.sprice').html(get_subprice_html(price, price_override));
                } else {
                    $(this).closest('tr').find('.sprice').html('$' + price.toFixed(2));
                }
            }
            total_rows();
        }).on('change', '.product_type_options', function () {
            total_rows();
        }).on('click', '.windowoptionssave', function() {
            var next_measure = null;
            var $popover = $(this).closest('.popover');
            var $row = $(this).closest('tr');
            var row = ctable.row($row);
            var data = row.data();
            var module;
            if (window.indow_module === 'freebird') {
                if ($popover.find('[name=frame_depth_id]').val() == '0') {
                    alert('Please select a frame depth before continuing.');
                    return;
                }
                $popover.find('.measurement').each(function () {
                    $(this).val(parseFloat(convertToDecimal($.trim($(this).val()))));
                });
                if ($(this).hasClass('measurenext')) {
                    next_measure = $(this).closest('tr').next().find('.showwindow');
                }
            }

            window_popup_save(this, $popover, data);
            if (indow_module) {
                module = indow_module;
            } else if (typeof quotes_id !== 'undefined') {
                module = 'quotes';
            } else {
                module = 'orders';
            }
            if (window.indow_module === 'freebird') {
                $.post('/orders/update_item_ajax/' + order_id, data);
            }
            stylizeRow($row, data, null, true, ctable, total_rows);
            $row.find('.mfg_status').change();
            if (next_measure) {
                next_measure.click();
            }
        }).on('keyup', '.measurement', function () {
            $('.windowoptionsrevert').prop('disabled', false);
        }).on('click', '.windowoptionscheck', function () {
            var $row = $(this).closest('tr');
            var $popover = $(this).closest('.popover-content');
            var data = ctable.row($row).data();
            var response = validateWindow(data, $row);
            if (window.indow_module === 'freebird' || window.indow_module === 'orders') {
                $popover.find('[name="valid"]').val(response.passed ? 1 : 0);
                $popover.find('[name="measured"]').val(1);
            }
            $(this).closest('.popover-content').find('.windowoptionssave').prop('disabled', false);
        }).on('click', '.windowoptionsrevert', function () {
            $('.windowoptionsrevert').prop('disabled', true);
            window_popup_revert(this);
        }).on('change', '.window_shape_options', function () {
            var show_options = get_required_measurements($(this).val());
            var $popover = $(this).closest('.popover');
            show_hide_measurements($popover, show_options);
        }).on('change', '.measurement', function () {
            calc_spines($(this).closest('.popover'));
        });
    if (!window.indow_itable_js_bound) { //this function is called 3 times on the orders page, but the following should only be bound once.
        window.indow_itable_js_bound = true;
        $('#addnewitem').click(function () {
            var itemtable, item, status_map, active_tab;
            if (indow_module === 'orders') {
                var active_tab = $('#order_tables .tab-pane.active');
                status_map = {0:1, 1:3, 2:4};
                itemtable = active_tab.find('table.itemtable').DataTable();
                item = get_new_item(status_map[active_tab.index()]);
            } else {
                itemtable = ctable;
                item = get_new_item(1);
            }
            itemtable.row.add(item).draw();
        });
        $('#withselected_apply').click(function () {
            var itemtable, type;
            var option = $('#withselected_option').val();
            if (indow_module === 'orders') {
                itemtable = indow_item_tables[$('#order_tables .tab-pane.active').index()];
            } else {
                itemtable = ctable;
            }
            if (option === 'delete') {
                withselected_delete(itemtable);
            } else if (option === 'createquote') {
                bootbox.dialog({
                    message: "Do you want to save any unsaved changes on the page before continuing?",
                    title: "Save",
                    buttons: {
                        danger: {
                            label: "Discard Changes",
                            className: "btn-blue",
                            callback: function() {
                                options.submit_page.call(indow_module_obj, false, 'quote');
                            }
                        },
                        success: {
                            label: "Save Changes",
                            className: "btn-blue",
                            callback: function() {
                                options.submit_page.call(indow_module_obj, true, 'quote');
                            }
                        }
                    }
                });
            } else if (option === 'createorder') {
                /* Order Review Modal */
                order_review_show();
            } else if (option === 'addorder') {
                window.indow_add_order_items = get_checked_items();
                $('#addToOrder').modal('show');
            }
        });
        $('#mass_edit_submit').click(function () {
            var $table;
            if (indow_module === 'orders') {
                $table = $('.order-form.active table');
            } else {
                $table = $('.default_item_table');
            }
            var itable = $table.DataTable();
            $table.find('.withselected:checked').each(function () {
                var $row = $(this).closest('tr');
                var row = itable.row($row);
                var data = row.data();
                $('.mass-edit').each(function() {
                    if ($(this).parent().find('.mass-edit-checkbox').prop('checked')) {
                        data[$(this).data('field')] = $(this).val();
                    }
                });
                get_totals([data]);
                stylizeRow($row, data, null, false, itable, total_rows);
                $($row).find('.withselected').prop('checked', true); //the checkbox gets unchecked when the row is re-rendered.
            });
        });
    }
}
function naturalSort (a, b) {
    var re = /(^-?[0-9]+(\.?[0-9]*)[df]?e?[0-9]?$|^0x[0-9a-f]+$|[0-9]+)/gi,
        sre = /(^[ ]*|[ ]*$)/g,
        dre = /(^([\w ]+,?[\w ]+)?[\w ]+,?[\w ]+\d+:\d+(:\d+)?[\w ]?|^\d{1,4}[\/\-]\d{1,4}[\/\-]\d{1,4}|^\w+, \w+ \d+, \d{4})/,
        hre = /^0x[0-9a-f]+$/i,
        ore = /^0/,
        // convert all to strings and trim()
        x = a.toString().replace(sre, '') || '',
        y = b.toString().replace(sre, '') || '',
        // chunk/tokenize
        xN = x.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
        yN = y.replace(re, '\0$1\0').replace(/\0$/,'').replace(/^\0/,'').split('\0'),
        // numeric, hex or date detection
        xD = parseInt(x.match(hre), 10) || (xN.length !== 1 && x.match(dre) && Date.parse(x)),
        yD = parseInt(y.match(hre), 10) || xD && y.match(dre) && Date.parse(y) || null;
 
    // first try and sort Hex codes or Dates
    if (yD) {
        if ( xD < yD ) {
            return -1;
        }
        else if ( xD > yD ) {
            return 1;
        }
    }
 
    // natural sorting through split numeric strings and default strings
    for(var cLoc=0, numS=Math.max(xN.length, yN.length); cLoc < numS; cLoc++) {
        // find floats not starting with '0', string or 0 if not defined (Clint Priest)
        var oFxNcL = !(xN[cLoc] || '').match(ore) && parseFloat(xN[cLoc], 10) || xN[cLoc] || 0;
        var oFyNcL = !(yN[cLoc] || '').match(ore) && parseFloat(yN[cLoc], 10) || yN[cLoc] || 0;
        // handle numeric vs string comparison - number < string - (Kyle Adams)
        if (isNaN(oFxNcL) !== isNaN(oFyNcL)) {
            return (isNaN(oFxNcL)) ? 1 : -1;
        }
        // rely on string comparison if different types - i.e. '02' < 2 != '02' < '2'
        else if (typeof oFxNcL !== typeof oFyNcL) {
            oFxNcL += '';
            oFyNcL += '';
        }
        if (oFxNcL < oFyNcL) {
            return -1;
        }
        if (oFxNcL > oFyNcL) {
            return 1;
        }
    }
    return 0;
}
 
jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "natural-asc": function ( a, b ) {
        return naturalSort(a,b);
    },
 
    "natural-desc": function ( a, b ) {
        return naturalSort(a,b) * -1;
    }
} );