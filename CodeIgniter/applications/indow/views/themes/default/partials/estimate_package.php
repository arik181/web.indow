<?
if ($estimate_id === 0)
{ 
    $estimate_package = array();

} else if (isset($dealer_id)) {

    $this->load->model('group_model');
    $estimate_package = $this->group_model->get_estimate_package($dealer_id);
    array_push($estimate_package, 
        array( 
            'deleted' => 0, 
            'filename' => "Indow Estimate Package", 
            'group_id' => $dealer_id, 
            'id' => 0, 
            'type' => "package", 
            'uploaded' => ""));
} else {

    $estimate_package = 
        array( 
            'deleted' => 0, 
            'filename' => "Indow Estimate Package", 
            'group_id' => 0, 
            'id' => 0, 
            'type' => "package", 
            'uploaded' => "");
}
?>

<script>
    $(function () {
        var files = <?= json_encode($estimate_package) ?>;
        console.log(files);
        $('#estimate_package_button').popover({
            content: $('#estimate_package'),
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
                "emptyTable": "There are no files available. Make sure that you have first saved the estimate.",
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
                    $('td:eq(0)', row).html('<a target="_blank" href="/estimates/package/' + <?=$estimate_id?> + '"><i class="icon fa fa-search"></i></a>');

                } else {

                    $('td:eq(0)', row).html('<a href="/estimates/file_download/' + data.id + '"><i class="icon fa fa-download"></i></a>');
                }
            }
        };
        var itable = $('#estimate_package_table').DataTable(tableoptions);
        $.each(files, function (i, e) {
            itable.row.add(e).draw();
        });
    });
</script>
<style>
    #estimate_package_button_cont .popover {
        min-width: 500px;
    }
    #estimate_package .fa {
        margin-top: 5px;
    }
</style>
<div id="estimate_package_button_cont">
    <button id="estimate_package_button" class="btn btn-blue btn-sm">Estimate Package</button>
</div>
<div class="clonecont">
    <div id="estimate_package">
        <table id="estimate_package_table" class="display table table-hover condensed dataTable" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Download</th>
                    <th>Filename</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
