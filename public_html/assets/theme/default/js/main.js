$.fn.add_order_totals_function = function() {
    if (this.length) {
        this.dataTable().fnSettings().aoDrawCallback.push({
            "fn": function () {
                order_totals_function(this);
            },
            "sName": "user"
        });
    }
}
function order_totals_function(elem) {
    var data = elem.DataTable().data();
    var max_num = 0;
    var max_str = '';
    var total_sqft = 0;
    var total_items = 0;
    var total_weight = 0;
    $.each(data, function (i, e) {
        total_items += parseInt(e.panel, 10);
        total_weight += parseFloat(e.total_weight);
        total_sqft += e.total_sqft;
        if (e.sqft > max_num) {
            console.log(e.sqft);
            max_num = e.sqft;
            max_str = e.dimension;
        }
    });
    $('.total_panels').html(total_items);
    $('.total_weight').html(parseFloat(total_weight).toFixed(2));
    $('.total_sqft').html(parseFloat(total_sqft / 144).toFixed(2));
    $('.largest_dimension').html(max_str);
}
function total_checked_rows(table) {
    var order_table = $(table).DataTable();
    var max_num = 0;
    var max_str = '';
    var total_sqft = 0;
    var total_items = 0;
    var total_weight = 0;
    $(table).find('.check-box:checked').each(function () {
        var tr = $(this).closest('tr');
        var order = order_table.row(tr).data();
        total_items += parseInt(order.panel, 10);
        total_sqft += parseFloat(order.total_sqft);
        total_weight += parseFloat(order.total_weight ? order.total_weight : 0);
        if (order.sqft > max_num) {
            max_num = order.sqft;
            max_str = order.dimension;
        }
    });
    $('.total_panels').html(total_items);
    $('.total_sqft').html(total_sqft ? parseFloat(total_sqft / 144).toFixed(2) : 0);
    $('.largest_dimension').html(max_str ? max_str : '0x0');
    $('.total_weight').html(parseFloat(total_weight).toFixed(2));
}
function run_flash(message) { //leave global. called by scripts on page
    if (message !== undefined) {
        $('#content_view .content').prepend('<div class="well" id="flash_data">' + message + '</div>');
    }
    if ( $('#flash_data').length > 0 )
    {
        $('#flash_data').delay(2000).animate({opacity: 0}, 750, function () {
            $(this).slideUp(1000, function () {
                $(this).remove();
            });
        });
    }
}
$(document).ready(function ()
{
    $( document ).ajaxSuccess(function( event, request, settings ) {
        if(request.getResponseHeader('X-INDOW-LOGIN')) {
            window.location = '/login';
        }
    });
    run_flash();
    $('#remove_signature').click(function (e)
    {
        e.preventDefault();

        $.ajax({
            url: document.URL.replace('/edit', '')+"/remove_pic/signature"
        }).done(function () {
            $('#sig_pic').remove();
            $('#remove_signature').remove();
        });
    });

    $('#remove_logo').click(function (e)
    {
        e.preventDefault();

        $.ajax({
            url: document.URL.replace('/edit', '')+"/remove_pic/logo"
        }).done(function () {
            $('#logo_pic').remove();
            $('#remove_logo').remove();
        });
    });

    if ( !("placeholder" in document.createElement("input")) ) {
        $("input[placeholder], textarea[placeholder]").each(function() {
            var val = $(this).attr("placeholder");
            if ( this.value == "" ) {
                this.value = val;
            }
            $(this).focus(function() {
                if ( this.value == val ) {
                    this.value = "";
                }
            }).blur(function() {
                if ( $.trim(this.value) == "" ) {
                    this.value = val;
                }
            })
        });
 
        // Clear default placeholder values on form submit
        $('form').submit(function() {
            $(this).find("input[placeholder], textarea[placeholder]").each(function() {
                if ( this.value == $(this).attr("placeholder") ) {
                    this.value = "";
                }
            });
        });
    }

    $('select[name=modifier_type]').change(function(){
        if ($(this).val() == 'discount') {
            $('#start_date').removeAttr('disabled');
            $('#end_date').removeAttr('disabled');
            $('#startNend').show();

        } else {
            $('#start_date').attr("disabled", "disabled");
            $('#end_date').attr("disabled", "disabled");
            $('#startNend').hide();

        }

        if ($(this).val() == 'msrp') {
            $('select[name="modifier"]').val('percent').prop('disabled', true).after('<input id="hide_mod" type="hidden" name="modifier" value="percent">'); //readonly needs to be an option for selects. :\ 
        } else {
            $('select[name="modifier"]').prop('disabled', false);
            $('#hide_mod').remove();
        }
        return true;
    }).change();
    
	$(function() {
		$( "#start_date" ).datepicker();
		$( "#end_date" ).datepicker();
	  });
          
});
