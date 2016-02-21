var existing_active_customers = [];
var indow_module_obj;
$(indow_module_obj = function() {
    function total_rows() {
		var data = itable.data();
        var totals = get_totals(data);
        $('#subtotal').html('$' + totals.subtotal.toFixed(2));
        $('#grand_total').html('$' + totals.total.toFixed(2));
        if (totals.spec_geom) {
            $('.spec_geom_toggle').show();
            $('#spec_geom_fee').html('$' + totals.spec_geom.toFixed(2));
            $('#spec_geom_count').html(totals.spec_geom_count);
        } else {
            $('.spec_geom_toggle').hide();
        }
		var total_sqft = 0;
		var total_windows = 0;
		$.each(data, function (i, item) {
			if (item.product_id != 3) {
				total_windows += 1;
				var width = Math.max(item.measurements['A'], item.measurements['B']);
				var height = Math.max(item.measurements['C'], item.measurements['D']);
				var sqft = width * height;
				if (sqft) {
					total_sqft += sqft / 144;
				}
			}
		});
		$('#total_windows').html(total_windows);
		$('#total_sqft').html(parseFloat(parseFloat(total_sqft).toFixed(2)));
    }
    function init_hash() {
        var hash = window.location.hash;
        if (hash === '#orders-anchor') {
            $('[data-target="#ordersTable"]').click();
        } else if (hash === '#estimates-anchor') {
            $('[data-target="#estimatesTable"]').click();
        } else if (hash === '#quotes-anchor') {
            $('[data-target="#quotesTable"]').click();
        }
    }
    function getItemTableOptions(site_id) {
        var options = {
            "dom": "<rt>",
            "pagingType": "full_numbers",
            "iDisplayLength": 5000,
            "language":{
                "paginate":{
                    "previous":"&laquo;",
                    "next":"&raquo;",
                },
                "emptyTable": 'There are no windows associated with this site.',
            },
            "columnDefs": [{
                "targets": [0],
                "orderable": false
            }, { "type": "natural", "targets": [2,3] }],
            "sorting": [[2,'asc'], [3,'asc']],
            "columns": [
                {'data':'id'},
                {'data':'manufacturing_status'},
                {'data':'room'},
                {'data':'location'},
                {'data':'width'},
                {'data':'height'},
                {'data':'product_id'},
                {'data':'product_types_id'},
                {'data':'edging_id'},
                {'data':'special_geom'},
                {'data':'price'},
                {'data':'id'},
                {'data':'id'}
            ],
            "createdRow": function (row, data, index) {
                get_totals([data]);
                stylizeRow(row, data, index, false, itable, total_rows);
            },
        };
        if (site_id !== 0) {
            options.ajax = '/sites/window_json/' + site_id;
        }
        return options;
    }
    function getEstimateSelectOptions() {
        var options = {
            "dom": '',
            "iDisplayLength": 5000,
            "language":{
                "emptyTable": 'There are no estimates available.',
            },
            "columns": [
                {'data':'id'},
                {'data':'customer_name'},
                {'data':'item_count'},
                {'data':'price'},
                {'data':'created'},
                {'data':'created_by'},
                {'data':'dealer'}
            ],
            "createdRow": function (row, data, index) {
                $('td:eq(0)', row).html('<a title="View" class="icon" href="/estimates/edit/' + data.id + '" target="_blank"><i class="sprite-icons view"></i></a>');
                $(row).click(function () {
                    window.location = '/orders/create_preorder/' + site_id + '/' + data.id;
                })
                .find('.icon').click(function (e) {
                    e.stopPropagation();
                });
            },
        };
        return options;
    }
    var itable = $('#windowTable').DataTable(getItemTableOptions(site_id));
    var estimatSelectTable = $('#estimateSelectTable').DataTable(getEstimateSelectOptions());
    bind_itable_js(itable, $('#windowTable'), total_rows, {submit_page: submit_page});

    $('#checkall').click(function () {
        $('.withselected').prop('checked', $(this).prop('checked')).change();
    });

    $('.assoc-users').hide();
    $('body').on('click', '.select-users', function(event) {
        $('.assoc-users').show();
        event.preventDefault();
        var html = '<tr class="association_list_row"><td><a href="javascript:void(0);" class="association_list_cell assoc-user-del"><i class="fa fa-times"></i></a></td><td class="association_list_cell">' + $(this).text() + '</td><td><input type="hidden" name="associatedUsers[]" value="' + $(this).attr('alt') + '" /></td></tr>';
        $('.assoc-users').append(html);
        $('#livesearch').html("");
    });

    $('body').on('click', '.assoc-user-del', function(event) {
        event.preventDefault();
        $(this).parents('tr').remove();
    });


    $('body').on('click','#remove-tab',function() {
        $('.addtab_stripe').toggle('slow', 'linear');
        $('.tab-content').toggle('slow', 'swing');
        $('#hide-show').val(($('#hide-show').val()==1)?0:1);
        $('li#new_customer_tab a').css({color:''});
    });

    $('#add-exist-cust-table').on('click', '.remove_customer', function () {
        var row = $(this).closest('tr');
        delete existing_active_customers[$(this).data('id')];
        add_utable.row(row).remove().draw();

    })

    function build_estimate () {
        return {
        };
    }

    function submit_page(save, createnew, review_info) {
        var missing = [];
        $('.jrequire').each(function () {
            if (!$(this).val()) {
                missing.push($(this).data('name'));
            }
        });
        if (missing.length) {
            var message = 'Please fill in the following field(s) before continuing: ' + missing.join(', ');
            bootbox.dialog({message: message, buttons: {okay: {className: 'btn-blue', label: 'Okay'}}});
            return;
        }
        
        var site = {};

        var items = itable.data();
        var cleanitems = [];
        $.each(items, function (i, e) {
            if (e.manufacturing_status == 1) {
                cleanitems.push(e);
            }
        });
        site.items = cleanitems;

        if (save) {
            var missing_room = 0;
            $.each(site.items, function (i, item) {
                if (!item.room) {
                    missing_room++;
                }
            });
            if (missing_room && !confirm('Room is required.  If you continue saving, blank rooms will be populated with \'unknown\'')) {
                return;
            }
            if (missing_room) {
                $.each(site.items, function (i, item) {
                    if (!item.room) {
                        item.room = 'unknown';
                    }
                });
            }
        }

        site.customers = customer_manager_get_customers();

        //site.users = user_manager_get_users();
        site.users = [];

        site.notes_customer = $('[name="save_customer_notes"]').val();
        site.notes_internal = $('[name="save_internal_notes"]').val();
        site.tech_notes = $('#tech_notes').val();
        site.save = !!save;
        site.createnew = createnew;
        site.delete_items = indow_delete_items;
        if (review_info) {
            site.review_info = review_info;
        }

        $('#customer_data').val($.toJSON(site));
        $('#jobsites_form').submit();
    }

    $('#orderReviewSave').click(function () {
        submit_page(false, 'order', get_order_review_info());
    });

    $('#savesite').click(function (e) {
        e.preventDefault();
        submit_page(true, true);
    });
    $('#withselected_delete').click(function () {
        withselected_delete(itable);
        total_rows();
    });

    $('#windowTable').on('change', '.withselected', function () {
        var product_type_id, $parentrow, data;
        var $row = $(this).closest('tr');
        var checked = $(this).prop('checked') ? 1 : 0;
        if ($row.hasClass('subitem')) {
            product_type_id = $row.data('id');
            $parentrow = $row.parents('tr').prev();
            data = itable.row($parentrow).data();
            data.subproducts[product_type_id].checked = checked;
        } else {
            data = itable.row($row).data();
            data.checked = checked;
        }
    });

    $('.savenotes').click(function () {
        var elem = $(this);
        var cont = $(this).closest('.notescont');
        var textarea = cont.find('textarea');
        var note = textarea.val();
        var input = cont.find('.savenotes');
        var save = input.val();
        if (note) {
            elem.prop('disabled', 1);
            $.post('/sites/save_notes/' + site_id +'/' + primary_customer_id, {note: note, save: save}, function (response) {
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
    function req_customer() {
        if (customer_manager_get_primary()) {
            return true;            
        } else {
            balert('A customer is required to continue.');
            return false;
        }
    }
    $('#edit_site_button').popover({
        content: $('#site_edit_form'),
        html: true,
        placement: 'right'
    });
    $('#create_preorder_button').popover({
        content: $('#preordercont'),
        html: true,
        placement: 'left',
        callback: function () {
            var estimates = $('#estimatesTable').DataTable().data();
            var ptable = $('#estimateSelectTable').DataTable();
            if (!ptable.data().length) {
                $.each(estimates, function (i, e) {
                    ptable.row.add(e).draw();
                });
            }
        }
    });

	$('#edit_tech').popover({
        content: $('#edit_tech_form'),
        html: true,
        placement: 'left'
    });

    $('#save_tech_notes').click(function (e) {
        e.preventDefault();
        $.post('/sites/save_tech_notes/' + site_id, {notes: $('#tech_notes').val()}, function (response) {
            alert(response.message);
        });
    });

    $('.add_items_to_order').click(function () {
        $('.add_items_to_order').prop('disabled', true);
        var order_id = $(this).data('id');
        var item_ids = window.indow_add_order_items;
        var post = {order_id: order_id, items: item_ids};
        
        $.post('/orders/add_items/' + order_id, {items: window.indow_add_order_items}, function () {
        window.location = '/orders/edit/' + order_id;
        });
    });

    init_hash();

 });