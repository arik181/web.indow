$(function () {
    $('.print_report, .csv_report').click(function (e) {
        e.preventDefault();
        var $form = $(this).closest('form');
        var data = {};
        var url = $form.serialize();
        if ($(this).hasClass('print_report')) {
            window.open('/fulfillment/report/print?' + url);
        } else if ($(this).hasClass('csv_report')) {
            $('#csv_frame').attr('src', '/fulfillment/report/csv?' + url)
        }
    });
    $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
});