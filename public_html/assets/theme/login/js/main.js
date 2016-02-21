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
				$('#startNend').show();
			} else {
				$('#startNend').hide();
			}
		});
	
    if ($('select[name=modifier_type]').val() == 'discount'){
			$('#startNend').show();
		} else {
			$('#startNend').hide();
		
    }    
    
	$(function() {
		$( "#start_date" ).datepicker();
		$( "#end_date" ).datepicker();
	  });
});
