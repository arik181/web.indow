if (typeof indow_module === 'undefined') {
    var indow_module = undefined;
}

$('customer_edit_btn').click(function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();    
});

var customer_manager_active_customers = {};
function customer_manager_get_primary () {
    var i;
    var customers = customer_manager_get_customers();
    for (i=0; i<customers.length; ++i) {
        if (customers[i].primary) {
            return customers[i].id;
        }
    }
    return false;
}

function customer_manager_get_customers () {
    var customers = [];
    $.each(customer_manager_active_customers, function (i, e) {
        var primary;
        if (e.primary !== undefined) {
            primary = e.primary;
        } else {
            primary = 0;
        }
        customers.push({id: i, primary: primary});
    });
    return customers;
}

function customer_manager_get_ids() {
    var ids = [];
    $.each(customer_manager_active_customers, function (i, e) {
        ids.push(i);
    });
    return ids;
}

var customer_manager_init = function () {
    function markActive() {
        $('.search_customer.active').removeClass('active');
        results = $('.search_customer');
        if (results.length > activeindex) {
            $(results[activeindex]).addClass('active');
        }
    }
    function gen_popover_content(data) {
        var html = data.name + '<br>';
        if (data.address) {
            var addr = data.address;
            html += addr.address + ' ' + addr.address_ext + '<br>' + addr.city + ', ' + addr.state + '<br>';
            if (addr.country) {
                html += addr.country + '<br>'
            }
        }
        if (data.organization_name) {
            html += data.organization_name + '<br>';
        }
        html += '<br><span class="contact_type">Rep</span>' + data.rep + '<br><br>';
        if (data.email_1) {
            html += '<span class="contact_type">' + indow_email_options[data.email_type_1] + '</span>' + data.email_1 + '<br>';
        }
        if (data.email_2) {
            html += '<span class="contact_type">' + indow_email_options[data.email_type_2] + '</span>' + data.email_2 + '<br>';
        }
        if (data.phone_1) {
            html += '<span class="contact_type">' + indow_phone_options[data.phone_type_1] + '</span>' + data.phone_1 + '<br>';
        }
        if (data.phone_2) {
            html += '<span class="contact_type">' + indow_phone_options[data.phone_type_2] + '</span>' + data.phone_2 + '<br>';
        }
        if (data.phone_3) {
            html += '<span class="contact_type">' + indow_phone_options[data.phone_type_3] + '</span>' + data.phone_3 + '<br>';
        }
        html += '<br><div class="text-center"><button data-id="' + data.id + '" type="button" class="btn btn-sm btn-blue make_primary">Make Primary</button></div>';
        return html;
    }
    function get_options() {
        return tableoptions = {
                "columnDefs": [{
                  "targets": 0,
                  "orderable": false
                }],
                "iDisplayLength": 500,
                "language": {
                    "emptyTable": "There are no associated customers.",
                },
                "dom": "<rt>",
                "pagingType": "full_numbers",
                "columns": [
                    {'data':'id'},
                    {'data':'name'},
                    {'data':'id'},
                ],
                "createdRow": function( row, data, index ) {
                    var actions = '<a class="icon" title="view" target="_blank" href="/customers/edit/' + data.id + '"><i class="sprite-icons view"></i></a>';
                    actions += '<a data-id="' + data.id + '" class="remove_customer icon" icon" title="remove"><i class="fa fa-times" alt="1"></i></a>';
                    var details = $('<a class="icon"><i class="fa fa-info-circle"></i></a>');
                    var popover = {
                        content: gen_popover_content(data),
                        html: true,
                        placement: 'right'
                    };
                    details.popover(popover);
                    $('td:eq(0)', row).html(actions);
                    $('td:eq(2)', row).html(details);
                },
        }
    }

    function ajax_save_customers() {
        if (window.indow_ajax_save && window.indow_module_obj) {
            indow_ajax_save.call(indow_module_obj, {customers: customer_manager_get_customers()});
        }
    }

    function add_customer(customer, init) {
        $('#quick_estimate_submit').prop("disabled",false);
        customer_manager_active_customers[customer.id] = customer;
        ctabler.fnAddData(customer);
        if (!init) {
            ajax_save_customers();
        }
    }

    function get_customer (id) {
        if (customer_manager_active_customers[id] === undefined) {
            $.get('/customers/customer_manager_get_customer/' + id, function (customer) {
                // alert (JSON.stringify(customer));
                add_customer(customer);
                if (customer_manager_get_customers().length === 1) {
                    make_primary(id);
                }
            });
        } else {
            alert('That customer is already added');
        }
    }

    /* update the retail price if new primary customer added */
    function updateRetailPrice(id) {
        $('.item_price').each(function () {
            var retail = $(this).data('price');
            /* $(this).html(+retail + +10); */
        });
    }

    function make_primary(id) {

        $.each(customer_manager_active_customers, function (i, e) {
            if (e.id == id) {
                customer_manager_active_customers[i].primary = 1;
            } else {
                customer_manager_active_customers[i].primary = 0;
            }
        });
        contactinfo_updatedata(id, 1);
        updateRetailPrice(id);
        ajax_save_customers();
    }
    $('#contact_form_newcustform').submit(function (e) {
        e.preventDefault();
        var address = $(this).find('#contact_form_address').val();
        if ((indow_module === 'quotes' || indow_module === 'orders') && !address) {
            alert('Customer address field is required.');
            return;
        }
        $('#submit_customer_form').prop('disabled', 1);
        var data = $(this).serialize() + '&ajax=true';

        $.post('/customers/add', data, function (response)
        {
            if (response.success) {

                $('#contact_form_newcustform')[0].reset();

                if (response.site_id && typeof indow_set_site === 'function' && !parseInt(indow_site_id, 10)) {
                    indow_set_site(response.site, response.site_id);
                }

                get_customer(response.userid);

                $('a[href="#customer_tab2"]').click();
            }
            else
            {
                //run_flash(response.message); //uncomment if you want this to use flash messages instead.
                alert(response.message);
            }

            $('#submit_customer_form').prop('disabled', 0);

        }).fail(function () {
            $('#submit_customer_form').prop('disabled', 0);
            alert ('You do not have permission to add customers.');
        });
    });
    $('#customer_manager_table').on('click', '.remove_customer', function () {
        var row = $(this).closest('tr');
        if (customer_manager_active_customers[$(this).data('id')].primary) {
            contactinfo_updatedata(0, 1);
        }
        delete customer_manager_active_customers[$(this).data('id')];
        if(!Object.keys(customer_manager_active_customers).length){
            $('#quick_estimate_submit').prop("disabled",true);
        }
        console.log(customer_manager_active_customers);
        ctable.row(row).remove().draw();
        ajax_save_customers();
        
    }).on('click', '.make_primary', function () {
        var id = $(this).data('id');
        make_primary(id);
        $(this).closest('.popover').prev().click();
    });

    $(document).on('click', '#manage_customers', function () {
        $('#customer_manager').toggle(500);
    });

    tableoptions = get_options();
    // alert (JSON.stringify(tableoptions));
    var ctabler = $('#customer_manager_table').not('#clonecont #customer_manager_table').dataTable(tableoptions);
    var ctable = ctabler.api();

    $.each(customer_manager_customers, function (i, e) {
        add_customer(e, true);
    });

    //ajax search
    var activeindex = 0;
    var resultcount = 0;
    var lastsearch = '';
    var searchstring;
    $('#ajax_customer_search').on('input', '', function() {
        var activeclass;
        searchstring = encodeURIComponent($(this).val());
        if (searchstring.length && searchstring != lastsearch) {
            $.get('/users/ajax_search/' + searchstring, function (results) {
                if (searchstring.length) { //this extra check is for if someone holds backspace, the ajax will return with results for the character before last, after the results have been hidden, causing them to reappear
                    lastsearch = searchstring;
                    var csr = $('#customer_search_results');
                    resultcount = results.length;
                    activeindex = 0;
                    if (resultcount) {
                        csr.html('');
                        $.each(results, function(i, customer) {
                            if (!i) {
                                activeclass = 'active';
                            } else {
                                activeclass = '';
                            }
                            var cell = $('<td class="search_customer ' + activeclass + '" data-index="' + i + '" data-userid="' + customer.id + '" data-addr="' + customer.has_address + '" />')
                            cell.text(customer.first_name + ' ' + customer.last_name);
                            cell.append('<br>');
                            cell.append(document.createTextNode(customer.email_1));
                            var row = $('<tr />').addClass(i % 2 ? 'even' : 'odd').append(cell);
                            csr.append(row);
                        });
                    } else {
                        csr.html('No Results');
                    }
                    $('#customer_search_results').show();
                }
            });
        } else if (!searchstring.length) {
            $('#customer_search_results').hide();
        }
    }).blur(function () {
        $('#customer_search_results').fadeOut(500);
    }).focus(function () {
        if (resultcount && searchstring.length) {
            $('#customer_search_results').show();
        }
    });

    $('#customer_search_results').on('mouseenter', '.search_customer', function () {
        activeindex = $(this).data('index');
        markActive();
    }).on('click', '.search_customer', function () {
        if ((indow_module === 'quotes' || indow_module === 'orders') && $(this).data('addr') != 1) {
            alert('The customer cannot be added without an address.');
            return;
        }
        get_customer($(this).data('userid'));
    });

    $('#newcust').popover({
        html: true,
        content: $('#newcustdiv'),
        placement: 'bottom'
    });

};
$(customer_manager_init);