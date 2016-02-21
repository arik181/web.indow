<script>
    $(function() {
        function markActive() {
            $('.search_customer.active').removeClass('active');
            results = $('.search_customer');
            if (results.length > activeindex) {
                $(results[activeindex]).addClass('active');
            }
        }
        function selectUser(id) {
            $('#customer_id').val(id);
            contactinfo_updatedata(id);
        }
        var activeindex = 0;
        var resultcount = 0;
        var lastsearch = '';
        var searchstring;
        $('#ajax_customer_search')
            .on('input', '', function() {
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
                                    var cell = $('<td class="search_customer ' + activeclass + '" data-index="' + i + '" data-userid="' + customer.id + '" />')
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
            }).keydown(function (e) {
                if (e.keyCode === 40 && activeindex < resultcount - 1) { //down
                    activeindex++;
                    markActive();
                } else if (e.keyCode === 38 && activeindex > 0) { //up
                    activeindex--;
                    markActive();
                } else if (e.keyCode === 13 && activeindex < resultcount) { //enter
                    selectUser($($('.search_customer')[activeindex]).data('userid'));
                }
            }).blur(function () {
                $('#customer_search_results').fadeOut(500);
            }).focus(function () {
                if (resultcount && searchstring.length) {
                    $('#customer_search_results').show();
                }
            });
        $('#customer_search_results')
            .on('mouseenter', '.search_customer', function () {
                activeindex = $(this).data('index');
                markActive();
            }).on('click', '.search_customer', function () {
                selectUser($(this).data('userid'));
            });
                $('#newcust').popover({
            html: true,
            content: $('#newcustdiv'),
            placement: 'bottom'
        });

        $('#cancelnewcust').click(function (e) {
            e.preventDefault();
            $('#newcust').popover('hide');
            $('#newcustform')[0].reset();
        });
        $('#newcustform').submit(function (e) {
            var form = this;
            e.preventDefault();
            var formdata = $(this).serialize() + '&ajax=1';
            $.post('/customers/add', formdata, function(data) {
                if (data.success) {
                    form.reset();
                    $('#newcust').popover('hide');
                    selectUser(data.userid);
                } else {
                }
                run_flash(data.message);
            }, 'json');
        });
    });
</script>
<style>
    #customer_search_results {
        top: 10px;
        position: absolute;
        width: 200px;
        background-color: #ffffff;
        border: 1px solid #999999;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        display: none;
        z-index: 4;
    }
    .search_customer {
        padding: 4px;
        border-bottom: 1px solid #cccccc;
    }
    .search_customer:last-child {
        border-bottom: 0px;
    }
    .search_customer.active {
        background-color: #cecece;
    }
    #srcont {
        position: relative;
        top: 22px;
    }
</style>

<span id="srcont">
    <table class="display table dataTable no-footer" id="customer_search_results"></table>
</span>
<input class="form-control input-sm" id="ajax_customer_search" type="text" name="customer_name" placeholder="Search by Customer Email/Name to Add" size="30"> &nbsp;&nbsp;