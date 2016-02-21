function contactinfo_updatedata(customer_id) {
    $.get('/customers/contact_info/' + customer_id, function (response) {

        $('#contact_info_view').replaceWith(response);

    });
}
function contactinfo_submit(form) {
    var url = $(form).attr('action');
    var poststring = $(form).serialize() + '&ajax=1';
    $('#editcustomersave').prop('disabled', true);
    $.post(url, poststring, function(response) {
        $('#editcustomersave').prop('disabled', false);
        if (response.success) {
            contactinfo_updatedata(response.customer_id);
            run_flash('Customer information saved.');
            $('#customer_edit_btn').popover('hide');
        }
    }, 'json');
    return false;
}
