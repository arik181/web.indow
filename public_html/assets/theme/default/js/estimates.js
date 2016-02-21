var indow_module_obj, est_total_rows, estimates_totals, indow_ajax_save, indow_save_fees;
$(indow_module_obj = function () {
    function save_fees() {
        var save = {
            fees: indow_active_fees,
            user_fees: indow_user_fees,
            delete_user_fees: indow_delete_user_fees
        }
        ajax_save(save);
    }
    indow_save_fees = save_fees;
    setTimeout(function () { //needs to be bound after the one off fees view even listener is bound later in the dom, hence the delay
        $('#content_view').on('click', '.fees_apply, #submit_one_off_fee, .delete-modifier', save_fees);
    }, 300);
    $('#estimate_status, #reason_for_closing, #follow_up').change(function (e) {
        if (e.originalEvent) {
            ajax_save();
        }
    });
    $('body').on('click', '.delete-modifier', function () { alert('test'); });
    function ajax_save(data, callback_data) {
        if (!data) {
            data = {};
        }
        if (estimate_id) {
            data.estimate_data = get_estimate_data();
            $.post('/estimates/ajax_save/' + estimate_id, {data: $.toJSON(data)}, function (response) {
                if (response.additem) {
                    addItem(response.additem);
                    $('#addnewitem').prop('disabled', false);
                }
                if (response.new_subproducts) {
                    add_child_products(callback_data.parentrow, response.new_subproducts);
                }
                if (response.message) {
                    alert(response.message);
                }
                if (response.user_fees) {
                    indow_user_fees = response.user_fees;
                    update_user_fees();
                }
            });
        }
    }
    indow_ajax_save = ajax_save;

    function row_html(subitem, sqft) {
        var product = products_info[subitem.product_type_id];
        var ret = "<tr class='childrow'><td class='smalltd'><input name='id' class='withselected' type='checkbox' value='" + subitem.id + "'>";
        var price = get_price(subitem.product_type_id, sqft) * subitem.quantity;
        ret += "<input name='product_type_id' type='hidden' class='product_type_id' value='" + subitem.product_type_id + "'></td>";
        ret += "<td class='widetd'>" + product.product_type + "</td>";
        ret += "<td>Qty <input name='quantity' class='form-control inline input-sm input_tiny quantity' value='" + subitem.quantity + "'></td><td class='pricetd'>$<span name='price' class='item_price'>" + price.toFixed(2) + "</span></td></tr>";
        return ret;
    }

    function add_child_products ($row, products) {
        var row, nextrow, childtable, newrowhtml, i, shown, sqft;
        row = table.row($row);
        shown = row.child.isShown();
        sqft = $row.find('.item_sqft').html();

        if (!shown) {
            newrowhtml = "<h4 class='products-head'><span class='pull-left'>&#8627;</span>Associated Products</h4>";
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

    function get_child_items(currow) {
        if (table.row(currow).child.isShown()) {
            var items = [];
            $(currow).next().find('tr').each(function () {
                var item = {};
                $(this).find('input, span').each(function () {
                    var name = $(this).attr('name');
                    if (name === 'id') {
                        item.checked = $(this).prop('checked');
                    }
                    if (this.tagName === 'SPAN') {
                        item[$(this).attr('name')] = $(this).html();
                    } else {
                        item[$(this).attr('name')] = $(this).val();
                    }
                });
                items.push(item);
            });
            return items;
        }
        return [];
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
    
    function get_items() {
        var items = [], children;
        $('#itemsTable tbody > tr[role="row"]').each(function () {
            var item = get_item(this);
            children = get_child_items(this);
            if (children.length) {
                item.children = children;
            }
            items.push(item);
        });
        return items;
    }

    function build_estimate () {
        return {
            items: get_items(),
            delete_items: delete_items
        };
    }
    function get_estimate_data() {
        return {
            estimate_total:     estimates_totals.total,
            total_square_feet:  $('#total_sqft').html(),
            followup:           $('#follow_up').prop('checked') ? 1 : 0,
            site_id:            indow_site_id,
            closed: $('#estimate_status').val(),
            reason_for_closing: $('#reason_for_closing').val()
        };
    }

    function submit_page(save, createnew) {
        var estimate = build_estimate();
        if (!customer_manager_get_primary()) {
            alert('A primary customer is required to continue.');
            return;
        }
        estimate.customers = customer_manager_get_customers();

        estimate.estimate_data = get_estimate_data();
        estimate.notes = $('.notes_text').val()
        estimate.fees = indow_active_fees;
        estimate.user_fees = indow_user_fees;
        estimate.delete_user_fees = indow_delete_user_fees;
        estimate.save = !!save;
        estimate.createnew = !!createnew;

        $('#estimatedata').val($.toJSON(estimate));
        $('#estimateform').submit();
    }

    function total_rows() {
        var subtotal = 0,
            total = 0,
            sqft,
            sqft_total = 0,
            totals = {},
            total_products = 0;
        $('.item_price').each(function () {
        });

        var data = [];
        $('#itemsTable tbody tr[role="row"]').each(function () {
            var price = parseFloat($(this).find('.item_price').html());
            var children = get_child_items(this);
            var special_geom = $(this).find('.special_geom').prop('checked') ? 1 : 0;
            data.push({price: price, special_geom: special_geom});
            $.each(children, function (i, e) {
                price += parseFloat(e.price);
            });
            subtotal += price;
            sqft_total += parseFloat($(this).find('.item_sqft').html());
        });

        non_accessories = $('.product_options :selected:not([value="3"])');
        sqft = $('.item_sqft');

        $('.quantity').each(function (i, e) {
            total_products += parseInt($(this).val(), 10);
        });

        totals = calc_fees_discounts(subtotal, data);

        $('#total_windows').text(non_accessories.length);
        $('#total_products').text(sqft.length + total_products);
        $('#total_sqft').text(Math.round(sqft_total * 100) / 100);

        estimates_totals = totals;

        write_totals(totals);
    }

    est_total_rows = total_rows;
    if (estimates_mode === 'add/edit') {
        var dataTable = $('#itemsTable').dataTable({
                "columnDefs": [{
                  "targets": 0,
                  "orderable": false
                }],
                "iDisplayLength": 500,
                "ajax": "/estimates/item_list_json/" + estimate_id,
                "dom": "<rt>",
                "pagingType": "full_numbers",
                "language": {
                    "paginate": {
                        "previous":"&laquo;",
                        "next":"&raquo;",
                    },
                    "emptyTable": "There are no items associated with this estimate.",
                },
                "aaSorting": [[1,'asc'], [2,'asc']],
                "aoColumnDefs": [{ "type": "natural", "targets": [1,2] }],
                "columns": [
                    {'data':'id'},
                    {'data':'room'},
                    {'data':'location'},
                    {'data':'width'},
                    {'data':'height'},
                    {'data':'product_id'},
                    {'data':'product_types_id'},
                    {'data':'edging_id'},
                    {'data':'special_geom'},
                    {'data':'square_feet'},
                    {'data':'price'},
                    {'data':'id'},            
                ],
                "createdRow": function( row, data, index ) {
                    $('td:eq(0)', row).html('<input class="withselected" type="checkbox" name="id" value="' + data.id + '">');
                    $('td:eq(1)', row).html('<input name="room" class="form-control input-sm" type="text" value="' + data['room'] + '" data-row="' + data['id'] + '"></input>');
                    $('td:eq(2)', row).html('<input name="location" class="form-control input-sm" type="text" value="' + data['location'] + '" data-row="' + data['id'] + '"></input>');
                    $('td:eq(3)', row).html('<input name="width" type="text" value="' + data['width'] + '" class="form-control input-sm width_input" data-row="' + data['id'] + '"></input>');
                    $('td:eq(4)', row).html('<input name="height" type="text" value="' + data['height'] + '" class="form-control input-sm height_input" data-row="' + data['id'] + '"></input>');
                    $('td:eq(5)', row).html(estimates_product_options).find('select').val(data.product_id);
                    $('td:eq(6)', row).html(estimates_product_type_options).find('select').val(data.product_types_id);
                    $('td:eq(7)', row).html(estimates_edging_options).find('select').val(data.edging_id);
                    $('td:eq(8)', row).html('<input name="special_geom" type="checkbox" class="special_geom">').find('input').prop('checked', parseInt(data.special_geom, 10));
                    $('td:eq(9)', row).html('<span class="item_sqft">' + data.square_feet + '</span>');
                    $('td:eq(10)', row).html('$<span name="price" class="item_price" data-price="'+parseFloat(data.price).toFixed(2)+'">' + parseFloat(data.price).toFixed(2) + '</span>');
                    $('td:eq(11)', row).html('<button class="btn btn-sm btn-default btn-info addproducts"><i class="fa fa-plus"></i></button>');
                    filter_select($('td:eq(6) select', row), data.product_id, data.product_types_id);
                    if (estimates_subitems[data.id]) {
                        add_child_products($(row), estimates_subitems[data.id]);
                    }
                },
                "initComplete": function() {
                    total_rows();
                    $('.width_input').change();
                    $('.height_input').change();
                    $('.product_type_options').change();
                }
        });
        var table = dataTable.api();
        var delete_items = [];
        var set_customer = 0;
        var page_totals;
        $('#itemsTable')
                .on('change', 'input, select', function (e) {
                    if (!estimate_id || !e.originalEvent || $(this).is('.withselected, .assoc_product_checkbox')) {
                        return;
                    }
                    var $row = $(this).closest('tr');
                    var row_id = $row.find('.withselected').val();
                    var curval = $(this).val();
                    setTimeout(function() { //allow price calculation to trigger before reading values of row
                        if ($row.hasClass('childrow')) {
                            var subproduct = {update_subproduct: {
                                'id': row_id,
                                quantity: curval
                            }};
                            ajax_save(subproduct);
                        } else {
                            ajax_save({update_item: get_item($row)});
                        }
                    }, 50);
                })
                .on('change', '.product_type_options', function () {
                    var row = $(this).parents('tr');
                    var product_type_id = $(this).val();

                    /* set max width and heights */
                    if (product_type_id)
                    {
                        var height_input = $( row ).find('.height_input').attr("data-height", products_info[product_type_id].max_height);
                        var width_input = $( row ).find('.width_input').attr("data-width", products_info[product_type_id].max_width);

                        if (product_type_id == 1 || product_type_id == 66) {
                            height_input.attr('data-height2', 73.5);
                            width_input.attr('data-width2', 97.5);
                        } else {
                            height_input.removeAttr('data-height2');
                            width_input.removeAttr('data-width2');
                        }

                        validate_width(row, row.find('.width_input, .height_input'), true)
                    }

                    total_rows();
                })
                .on('change', '.product_options', function () {
                    var elem = $(this).parents('tr').find('.product_type_options');
                    filter_select(elem, $(this).val(), null);
                    elem.change();
                })
                .on('keyup', '.width_input, .height_input', function () {
                    var row = $(this).closest("tr");
                    validate_width(row, this);
                })
                .on('change', '.width_input, .height_input', function () {
                    var row = $(this).parents('tr');

                    if (table.row(row).child.isShown()) {
                        row.next().find('.quantity').change(); //force prices to update.  This does some unnecessary dom traversal and can be made more efficient later if time permits. ie: never
                    }
                    validate_width(row, row.find('.width-input, .height-input'), true);


                    total_rows();
                })
                .on('change', '.quantity', function () {
                    var row = $(this).closest('tr');
                    var parentRow = $(this).parents('.childproductstable').parents('tr').prev();
                    var sqft = parentRow.find('.item_sqft').html();
                    var item = row.find('.product_type_id').val();
                    var price = get_price(item, sqft) * $(this).val();
                    row.find('.item_price').text(price.toFixed(2));
                    row.find('.item_price').attr('data-price', price.toFixed(2));
                    total_rows();
                })
                .on('click', '.addproducts', function () {
                    var checkboxes = $('#productcheckboxes').clone();
                    if (table.row($(this).parents('tr')).child.isShown()) {
                        var pids = [];
                        var product_inputs = table.row($(this).parents('tr')).child().find('.product_type_id');
                        product_inputs.each(function () {
                            pids.push(parseInt($(this).val(), 10));
                        });
                        checkboxes.find('.cboxrow').each(function () { //remove products from popover that have already been added
                            if ($.inArray($(this).data('id'), pids) !== -1) {
                                $(this).remove();
                            }
                        });
                        if (!checkboxes.find('.cboxrow').length) {
                            checkboxes.html('No additional products may be added.');
                        }
                    }
                    if ($(this).data('has-popover') === undefined) {
                        $(this).popover({
                            content: function() {return checkboxes.html()},
                            html: true,
                            placement: 'bottom'
                        }).popover('show').data('has-popover', true);
                    } else {
                        $(this).parent().find('.popover-content').html(checkboxes);
                    }
                })
                .on('click', '.addproductssubmit', function () {
                    var popover = $(this).parents('.popover');
                    var parentrow = $(this).parents('tr');
                    var row = table.row(parentrow);
                    var new_products = [];
                    popover.find('input').each(function () {
                        if ($(this).prop('checked')) {
                            new_products.push({
                                product_type_id: $(this).val(),
                                id: 'new',
                                quantity: 1
                            });
                        }
                    });
                    popover.prev().click(); //close popover
                    if (estimate_id) {
                        ajax_save({new_subproducts: new_products, parent_id: parentrow.find('.withselected').val()}, {parentrow: parentrow});
                    } else {
                        add_child_products(parentrow, new_products);
                    }
                }).on('click', '.special_geom', function () {
                    total_rows();
                });
        $('#change_owner').change(function () {
            ajax_save({change_owner: $(this).val()});
        });

        function new_item() {
            var item = {
                id: 'new',
                room: 'unknown',
                location: '',
                width: 0,
                height: 0,
                product: '',
                product_id: 4,
                product_types_id: 66,
                edging_id: 1,
                special_geom: 0,
                price: 0,
                square_feet: 0,
            }
            return item;
        }

        function addItem(id) {
            var item = new_item();
            if (id) {
                item.id = id;
            }
            table.row.add(item).draw();
            $('#itemsTable > tbody > tr:last .product_options').change(); //force price to update
        }

        $('#addnewitem').click(function () {
            if (estimate_id) {
                $(this).prop('disabled', true);
                ajax_save({additem: new_item()});
            } else {
                addItem();
            }
        });

        $("#checkall").click(function() {
            $('.withselected').prop('checked', $(this).prop('checked'));
        });

        function delete_selected() {
            $('.withselected:checked').each(function () {
                var type;
                if ($(this).val() !== 'new') {
                
                    if ($(this).closest('tr').hasClass('childrow')) {
                        type = 'subitem';
                    } else {
                        type = 'item';
                    }
                    delete_items.push({type: type, id: $(this).val()});
                    if (estimate_id) {
                        ajax_save({delete_items: delete_items})
                    }
                }
                var tr = $(this).closest('tr');
                if (!tr.hasClass('childrow')) {
                    table.row(tr).remove().draw();
                } else {
                    var subitemtable = tr.closest('table');
                    tr.remove();
                    if (!subitemtable.find('tr').length) {
                        table.row(subitemtable.parents('tr').prev()).child('').hide();
                    }
                }
            });
            total_rows();
        }

        function create_new_from_selected() {
            bootbox.dialog({
                message: "You have unsaved changes on the page.  Do you want to save them before continuing?",
                title: "Save",
                buttons: {
                    danger: {
                        label: "Discard Changes",
                        className: "btn-blue",
                        callback: function() {
                            submit_page(false, true);
                        }
                    },
                    success: {
                        label: "Save Changes",
                        className: "btn-blue",
                        callback: function() {
                            submit_page(true, true);
                        }
                    }
                }
            });
        }

        function duplicate_from_selected() {
            $('tbody tr[role="row"] .withselected:checked').each(function () {
                var i = 0;
                var row = $(this).closest('tr');
                var item = get_item(row, true);
                var new_products = [];
                item.id = 'new';
                table.row.add(item).draw();
                children = get_child_items(row);
                if (children.length) {
                    for (i=0; i < children.length; ++i) {
                        if (children[i].checked) {
                            new_products.push({
                                product_type_id: children[i].product_type_id,
                                id: 'new',
                                quantity: children[i].quantity
                            });
                        }
                    }
                    if (new_products.length) {
                        var newrow = $('#itemsTable tbody > tr[role="row"] :last');
                        add_child_products(newrow, new_products);
                    }
                }
            });
            total_rows();
            $('#saveestimate').click();
        }

        $('#withselected_apply').click(function () {
            var option = $('#withselected_option').val();
            if (option === 'delete') {
                delete_selected();
            } else if (option === 'createnew') {
                create_new_from_selected();
            } else if (option === 'duplicate') {
                duplicate_from_selected();
            }
        });
        
        $('#saveestimate').click(function () {
            submit_page(true, false);
        });
        
        $('#mass_edit_submit').click(function () {
            $('.mass-edit').each(function() {
                var mass_edit = $(this);
                var target = $(this).data('target'); 
                $('.withselected:checked').each(function () {
                    var row = $(this).closest('tr');
                    if (mass_edit.parent().find('.mass-edit-checkbox').prop('checked')) {
                        row.find(target).val(mass_edit.val()).change();
                    }
                });
            });
            ajax_save({allitems: get_items()});
        });
        $('#fees_discounts_cont input').each(function () {
            if ($.inArray(parseInt($(this).val(), 10), indow_active_fees) !== -1) {
                $(this).prop('checked', true);
            }
        });
        $('#fees_discounts').popover({
            content: $('#fees_discounts_cont').clone(),
            html: true,
            placement: 'top'
        });
        $('#fees_discounts_button_cont').on('click', '.fees_apply', function () {
            var fees = [];
            $(this).parents('.popover').find('input:checked').each(function () {
                fees.push(parseInt($(this).val(), 10));
            });
            indow_active_fees = fees;
            total_rows();
            $('#fees_discounts').click(); //close popover
        });
        $('#topbuttons').on('click', '#assign_tech', function () {
            var tech_id = $('#tech_id').val();
            var tech_name = $( "#tech_id option:selected" ).text();
            var elem = $(this).prop('disabled', 1);
            $.post('/estimates/assign_tech/' + estimate_id, {tech_id: $('#tech_id').val()}, function (response) {
                $('#measurejob').click(); //close popover;
                elem.prop('disabled', 0);
                if (response.success) {
                    $('#measurejob').replaceWith('<b>Tech: </b>' + tech_name);
                }
                alert(response.message);
            });
        });
        $('#go_to').change(function () {
            if ($(this).val() === 'createnew') {
                create_new_from_selected();
            } else {
                if (confirm('Any unsaved data on the page will be lost. Continue?')) {
                    window.location = "/estimates/edit/" + $(this).val();
                }
            }
        });
    }

    $('.savenotes').click(function () {
        var elem = $(this);
        var cont = $(this).closest('.notescont');
        var textarea = cont.find('textarea');
        var note = textarea.val();
        if (note) {
            elem.prop('disabled', 1);
            $.post('/estimates/save_notes/' + estimate_id, {note: note}, function (response) {
                elem.prop('disabled', 0);
                if (response.success) {
                    textarea.val('');
                    note = $('<div>').text(note).html();
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
    $('#measurejob').popover({
        content: $('#techmodal'),
        html: true,
        placement: 'bottom'
    });

    $('#estimate_status').change(function () {
        var status = $(this).val();
        var rfc = $('#reason_for_closing_cont');
        if (status == 0) {
            rfc.hide();
        } else {
            rfc.show();
        }
    }).change();
});
