function contactinfo_updatedata() {
    var com_translate, value, prefix, formFields;
    com_translate = ['Home', 'Work', 'Mobile'];
    formFields = $('#editcustomerform').serializeArray();
    $.each(formFields, function(i, field) {
        prefix = field.name.substring(0,10);
        if (prefix === 'email_type' || prefix === 'phone_type') {
            value = com_translate[field.value];
        } else {
            value = field.value
        }
        $('#contact_' + field.name).text(value);
    });
}
function contactinfo_submit(form) {
    var url = $(form).attr('action');
    var poststring = $(form).serialize() + '&ajax=1';
    $('#editcustomersave').prop('disabled', true);
    $.post(url, poststring, function(response) {
        $('#editcustomersave').prop('disabled', false);
        if (response.success) {
            contactinfo_updatedata();
            run_flash('Customer information saved.');
            $('#customer_edit_btn').popover('hide');
        }
    }, 'json');
    return false;
}
