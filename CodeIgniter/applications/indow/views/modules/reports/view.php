<? 
initializeDataTable($selector       = "#reportsTable", 
                    $ajaxEndPoint   = "/reports/report_json/".$report_id,
                    $columns        = array(
                                            'customer', 
                                            'units', 
                                            'windows',
                                            'price',
                                            'status',
                                        ),
                    $primaryKey     = "id",
                    $actionButtons  = array(),
                    $actionColumn   = null,
                    $emptyString    = "No orders found.",
                    $extraCreationJS = "$('td:eq(3)', row).html('$' + parseFloat(data.price).toFixed(2));
                    ",
                    $dom = '""'
                    );
?>
<style>
    .quick-estimates-btns .popover {
        min-width: 550px !important;
    }
</style>
<script type="text/javascript">
//Add Filter for Status
    $(document).ready(function() {
        $('#filterbystatus').on('change', function () {
            var dataTable = $("#reportsTable").DataTable();
            var filterColumn = 4; // status column
            var val = $(this).val();

            console.log(dataTable);
            dataTable.column( filterColumn )
                .search( val , true, false )
                .draw();
        } );
    });
</script>
<div class="row">
    <div class="col-xs-12">
        <h2>Report Name: <?= $report->name ?></h2>
    </div>
</div>
<div class="row">
    <div class="col-xs-6">
        <h3>Report Type: <?= ($report->display_type)? 'All Estimates':'My Own Estimates'; ?><br />
            Date Range of Report: <?= date('m/d/Y', strtotime($report->from)) ?> to <?= date('m/d/Y', strtotime($report->to)) ?>
        </h3>
    </div>
    <div class="col-xs-6">
        <br />
        <span class="pull-right">Filter by Status <?= form_dropdown('', array('' => '', 'Open' => 'Open', 'Closed' => 'Closed'), '', 'style="width: 150px" class="form-control inline input-sm" id="filterbystatus"') ?></span>
    </div>
</div>

<table id="reportsTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Customer</th>
            <th>Units</th>
            <th>Total Openings</th>
            <th>Total Cost</th>
            <th>Status</th>
        </tr>
    </thead>
</table>

<a href="/reports/delete/<?= $report_id ?>" class="delete btn btn-gray pull-left">Delete Report</a>
<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js');?>"></script>
