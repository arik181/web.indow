<?
if ($order_id === 0)
{
    $order_package = array();

} else if (isset($dealer_id)) {

    $this->load->model('group_model');
    $order_package = $this->group_model->get_order_package($dealer_id);
    array_push($order_package, 
        array( 
            'deleted' => 0, 
            'filename' => "Indow Order Package", 
            'group_id' => $dealer_id, 
            'id' => 0, 
            'type' => "package", 
            'uploaded' => ""));
} else {

    $order_package = 
        array(
            array( 
                'deleted' => 0, 
                'filename' => "Indow Order Package", 
                'group_id' => 0, 
                'id' => 0, 
                'type' => "package", 
                'uploaded' => ""
            )
        
        );
}
?>

<script>
    $(function () {
        var files = <?= json_encode($order_package) ?>;
        console.log(files);
        $('#order_package_button').popover({
            content: $('#order_package'),
            html: true,
            placement: 'top'
        });
        var tableoptions = {
            "dom": "<rt>",
            "pagingType": "full_numbers",
            "iDisplayLength": 5000,
            "language":{
                "paginate":{
                    "previous":"&laquo;",
                    "next":"&raquo;",
                },
                "emptyTable": "There are no files available. Make sure that you have first saved the order.",
            },
            "columnDefs": [{
                "targets": 0,
                "orderable": false
            }],
            "columns": [
                {'data':'id'},
                {'data':'filename'},
            ],
            "createdRow": function (row, data, index) {
                if ( data.id == 0 )
                {
                    $('td:eq(0)', row).html('<a href="/orders/package/' + <?=$order_id?> + '"><i class="icon fa fa-download"></i></a>');

                } else {

                    $('td:eq(0)', row).html('<a href="/orders/file_download/' + data.id + '"><i class="icon fa fa-download"></i></a>');
                }
            }
        };
        var itable = $('#order_package_table').DataTable(tableoptions);
        $.each(files, function (i, e) {
            itable.row.add(e).draw();
        });
    });
</script>
<style>
    #order_package_button_cont .popover {
        min-width: 500px;
    }
    #order_package .fa-download {
        margin-top: 5px;
    }
</style>
<div id="order_package_button_cont">
    <button id="order_package_button" class="btn btn-blue btn-sm">Order Package</button>
</div>
<div class="clonecont">
    <div id="order_package">
        <table id="order_package_table" class="display table table-hover condensed dataTable" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Download</th>
                    <th>Filename</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
