var user_manager_active_users = [];
function user_manager_get_users () {
    var users = [];
    $.each(user_manager_active_users, function (i, e) {
        users.push(e.user_id);
    });
    return users;
}
var user_search_init = function () {
    function markActive() {
        $('.search_user.active').removeClass('active');
        results = $('.search_user');
        if (results.length > activeindex) {
            $(results[activeindex]).addClass('active');
        }
    }

    function get_options() {
        return tableoptions = {
            "columnDefs": [{
                "targets": 0,
                "orderable": false
            }],
            "iDisplayLength": 500,
            "language": {
                "emptyTable": "There are no associated users.",
            },
            "dom": "<rt>",
            "pagingType": "full_numbers",
            "columns": [
                {'data':'user_id'},
                {'data':'name'}
            ],
            "createdRow": function( row, data, index ) {
                var actions = '<a data-id="' + data.id + '" class="remove_customer icon" icon" title="remove"><i class="fa fa-times" alt="1"></i></a>';
                $('td:eq(0)', row).html(actions);
            }
        }
    }

    function add_user(user) {
        var data = ctable.data();
        var cnew = true;
        $.each(data, function (i, e) {
            if (user.user_id == e.user_id) {
                cnew = false;
                return false;
            }
        });
        if (cnew) {
            ctable.row.add(user).draw();
            user_manager_active_users = ctable.data();
            $('#associated_user_count').html(user_manager_active_users.length);
        }
        
    }

    $('#user_manager_table').on('click', '.remove_customer', function () {
        var row = $(this).closest('tr');
        var user_id = ctable.row(row).data().user_id;
        ctable.row(row).remove().draw();
        user_manager_active_users = ctable.data();
        $('#associated_user_count').html(user_manager_active_users.length);

    });

    tableoptions = get_options();
    var ctable = $('#user_manager_table').DataTable(tableoptions);
    $.each(indow_user_assoc_init, function (i, e) {
        add_user(e);
    });

    //ajax search
    var activeindex = 0;
    var resultcount = 0;
    var lastsearch = '';
    var searchstring;
    $('#ajax_user_search').on('input', '', function() {
        dataTable = '#user_manager_table';
        var activeclass;
        searchstring = encodeURIComponent($(this).val());
        if (searchstring.length && searchstring != lastsearch) {
            $.get('/users/ajax_search/' + searchstring + '/' + 0, function (results) {
                if (searchstring.length) { //this extra check is for if someone holds backspace, the ajax will return with results for the character before last, after the results have been hidden, causing them to reappear
                    lastsearch = searchstring;
                    var csr = $('#user_search_results');
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
                            var cell = $('<td class="search_user ' + activeclass + '" data-index="' + i + '" data-userid="' + customer.id + '" />').data('name', customer.first_name + ' ' + customer.last_name);
                            cell.text(customer.first_name + ' ' + customer.last_name);
                            cell.append('<br>');
                            cell.append(document.createTextNode(customer.email_1));
                            var row = $('<tr />').addClass(i % 2 ? 'even' : 'odd').append(cell);
                            csr.append(row);
                        });
                    } else {
                        csr.html('No Results');
                    }
                    $('#user_search_results').show();
                }
            });
        } else if (!searchstring.length) {
            $('#user_search_results').hide();
        }
    }).blur(function () {
            $('#user_search_results').fadeOut(500);
        }).focus(function () {
            if (resultcount && searchstring.length) {
                $('#user_search_results').show();
            }
        });

    $('#user_search_results').on('mouseenter', '.search_user', function () {
        activeindex = $(this).data('index');
        markActive();
    }).on('click', '.search_user', function () {
        add_user({
            user_id:    $(this).data('userid'),
            name:       $(this).data('name'),
        });
        $('#user_manager_table').show();
    });


};
$(user_search_init);