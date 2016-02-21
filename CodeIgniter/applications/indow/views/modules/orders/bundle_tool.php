<?
if (!isset($dealer_id)) {
    $dealer_id=0;
}
if (isset($order->id)) {
    $order_id = $order->id;
}
if (!isset($order_id)) {
    $order_id=0;
}
?>
<style>
    #bundle_checkbox_cont .popover {
        min-width: 600px;
    }
    #bundlelist .fa-info-circle {
        margin-left: 10px;        
    }
</style>
<script>
    var indow_bundle_orders = <?= isset($bundle_orders) ? json_encode($bundle_orders) : '[]' ?>;
    $(function () {
        $('#bundle_checkbox').click(function (e) {
            e.preventDefault();
        })
        .popover({
            content: $('#bundlecont'),
            html: true,
            placement: 'right',
            "callback": function () {
                $('#bundlelist .table_checkbox').each(function () {
                    $(this).prop('checked', $.inArray($(this).val(), indow_bundle_orders) !== -1);
                });
            }
        })
        .prop('checked', indow_bundle_orders.length);
        var tableoptions = {
            "ajax": '/orders/bundle_list/<?= $dealer_id . '/' . $order_id ?>',
            "dom": "<rt>",
            "iDisplayLength": 5000,
            "language":{
                "emptyTable": "There are no orders available for bundling.",
            },
            "columns": [
                {'data':'id'},
                {'data':'order_num'},
                {'data':'po_num'},
                {'data':'customer'},
                {'data':'num_win'},
                {'data':'created'},
                {'data':'status_code'}
            ],
            "columnDefs": [{
                "targets": 0,
                "orderable": false
            }],
            "createdRow": function (row, data, index) {
                var checked = $.inArray(data.id, indow_bundle_orders) !== -1;
                $('td:eq(0)', row).html($('<input class="table_checkbox" type="checkbox" value="' + data.id + '">').prop('checked', checked));
                $('td:eq(6)', row).append($('<i data-toggle="tooltip" data-placement="right" class="icon fa fa-info-circle"></i>').attr('title', data.description).tooltip('show'));
            },
        };
        var itable = $('#bundlelist').DataTable(tableoptions);
        $('.table_checkall').click(function () {
            $(this).closest('table').find('.table_checkbox').prop('checked', $(this).prop('checked'));
        });
        $('#bundle_orders_save').click(function () {
            var orders = [];
            $('#bundlelist').find('.table_checkbox:checked').each(function () {
                orders.push($(this).val());
            });
            indow_bundle_orders = orders;
            $('#bundle_checkbox').click().prop('checked', orders.length);
        });
    });
</script>
<div id="bundle_checkbox_cont">
    <input type="checkbox" id="bundle_checkbox" <?= isset($disabled) ? $disabled : '' ?>> <label for="bundle_checkbox">Bundle</label>
</div>
<div class="clonecont">
    <div id="bundlecont">
        <h3>Select orders to bundle</h3>
        <table id="bundlelist" class="display table table-hover condensed" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th><input type="checkbox" class="table_checkall"></th>
                    <th>Order</th>
                    <th>PO #</th>
                    <th>Customer Name</th>
                    <th># of Windows</th>
                    <th>Order Date</th>
                    <th>Status</th>
                </tr>
            </thead>
        </table>
        <button id="bundle_orders_save" class="btn btn-blue pull-right">Save</button><br style="clear: both">
    </div>
</div>