function force_download(ids, mode) {
    var form = $('<form method="post" action="/fulfillment/export_orders">');
    form.append('<input type="hidden" name="mode" value="' + mode + '">');
    $.each(ids, function (i, id) {
        form.append('<input type="hidden" name="id[]" value="' + id + '">');
    });
    var iframe = $('#csv_export_frame');
    iframe.contents().find('form').remove();
    var iFrameDoc = iframe[0].contentDocument || iframe[0].contentWindow.document;
    iFrameDoc.write($('<div>').append(form).html());
    iFrameDoc.close();
    iframe.contents().find('form').submit();
}

$('document').ready(function(){
    var scheduling_table = $('#schedulingTable').DataTable();

    $('.fulfillment_child').show();

    $('#schedulingTable').on('change', '.changedata', function () {
        var value;
        if ($(this).attr('type') === 'checkbox') {
            value = $(this).prop('checked') ? 1 : 0;
        } else {
            value = $(this).val();
        }

        var itable = $('#schedulingTable').DataTable();
        var row = $(this).closest('tr');
        var data = itable.row(row).data();

        var td = $(this).closest('td');
        var cell = itable.cell(td);
        var check_download = row.find('.download').prop('checked');
        cell.data(value);
        draw_row(data, row);
        row.find('.download').prop('checked', check_download); //recheck download box after redrawing row

        var field = $(this).attr('name');

        data[field] = value;
        var order_id = data.id;
        var post = {};
        post[field] = value;
        $.post('/fulfillment/update_order/' + order_id, post);
    });

    $('#schedulingTable').on('click', '.download', function () {
        var row = $(this).closest('tr');
        var data = scheduling_table.row(row).data();
        data.checked = $(this).prop('checked');
    });

    $('#planning_download').click(function () {
        var order_ids = [];

        $.each(scheduling_table.data(), function (i, order) {
            if (order.checked) {
                order_ids.push(order.id);
            }
        });
        if (!order_ids.length) {
            alert('No orders selected.');
        } else {
            //$('#csv_export_frame').attr('src', '/fulfillment/export_orders?' + order_ids.join('&'));
            force_download(order_ids, 'orders');
        }
    });

    $('.filter_date').datepicker({dateFormat: 'yy-mm-dd'});
});

        