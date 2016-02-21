$('customer_edit_btn').click(function(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
});

var customer_manager_active_customers = {};
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
var customer_manager_init = function () {
    //ajax search
    var activeindex = 0;
    var resultcount = 0;
    var lastsearch = '';
    var searchstring;

    $('#groupname').on('input', '', function() {
        var activeclass;
        searchstring = encodeURIComponent($(this).val());
        if (searchstring.length && searchstring != lastsearch) {
            // $.get('/groups/ajax_permissions_search/' + searchstring, function (results) {
            $.get('/groups/ajax_search/' + searchstring, function (results) {
                if (searchstring.length) { //this extra check is for if someone holds backspace, the ajax will return with results for the character before last, after the results have been hidden, causing them to reappear
                    lastsearch = searchstring;
                    var csr = $('#group_search_results');
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
                            /* Populate group hints */
                            var cell = $('<td class="search_customer ' + activeclass + '" data-index="' + i + '" data-groupname="' + customer.group_name + '">')
                            cell.text(customer.group_name);
                            var row = $('<tr>').addClass(i % 2 ? 'even' : 'odd').append(cell);
                            csr.append(row);
                        });
                    } else {
                        csr.html('No Results');
                    }
                    $('#group_search_results').show();
                }
            });
        } else if (!searchstring.length) {
            $('#group_search_results').hide();
        }
    }).blur(function () {
        $('#group_search_results').fadeOut(500);
    }).focus(function () {
        if (resultcount && searchstring.length) {
            $('#group_search_results').show();
        }
    });
    $('#group_search_results')
        .on('mouseenter', '.search_customer', function () {
            activeindex = $(this).data('index');
            //markActive();
        /* click to select group */
        }).on('click', '.search_customer', function () {
            $('#groupname').val($(this).data('groupname'))
        });
            $('#newcust').popover({
        html: true,
        content: $('#newcustdiv'),
        placement: 'bottom'
    });

};
$(customer_manager_init);