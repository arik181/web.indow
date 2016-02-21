<?php $curr_uid = $this->ion_auth->get_user_id(); ?>
<? 
initializeDataTable($selector       = "#reportsTable", 
                    $ajaxEndPoint   = "/reports/list_json/".$curr_uid,
                    $columns        = array(
                                            'id', 
                                            'name', 
                                            'created',
                                        ),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/reports/view/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "No reports have been added.",
                    $extraCreationJS = "$('td:eq(2)', row).html(data.created_f);",
                    $dom = '\'<"top"ilp<"clear">>rt<"bottom"p<"clear">>\''
                    );
?>
<style>
    .quick-estimates-btns .popover {
        min-width: 620px !important;
    }
</style>
<script type="text/javascript">
//Add Filter for Status
    $(document).ready(function() {
        /*
        var span = $('<span class="filter">Filter By Status </span>')
            .prependTo($('.top .dataTables_filter'));
        var select = $('<?= str_replace("\n", "", form_dropdown('', $status_options)) ?>')
            .appendTo(span)
            .on('change', function () {
                var dataTable = $("#ordersTable").DataTable();
                var filterColumn = 6; // status column
                var val = $(this).val();

                console.log(dataTable.column( filterColumn ));
                dataTable.column( filterColumn )
                    .search( val , true, false )
                    .draw();
            } ); */
        $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
        $('#topbuttons a').popover({
            content: $('#addreportform'),
            html: true,
            placement: 'bottom'
        });
    });
</script>
<div id="clonecont">
    <form class="form-horizontal" id="addreportform" method="post">
        <div class="form-group">
            <div class="col-md-3">
                <label class="control-label" for="report_name">Report Name</label>
            </div>
            <div class="col-md-9"><input required="required" id="report_name" name="name" class="form-control input-sm"></div>
        </div>
        <div class="form-group">
            <div class="col-md-3">
                <label class="control-label" for="display_type">Display</label>
            </div>
            <div class="col-md-3"><?= form_dropdown('display_type', array('My Own Estimates', 'All Estimates'), '', 'class="form-control input-sm"') ?></div>
            <div class="col-md-6 text-right">
                <label class="inline control-label" for="report_from">From </label><input required="required" style="width: 33%" id="report_from" name="from" class="inline datepicker form-control input-sm">
                <label class="inline control-label" for="report_to"> to </label><input required="required" style="width: 33%; margin-right: 0px" id="report_to" name="to" class="inline datepicker form-control input-sm">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <input type="submit" value="Create" class="pull-right btn btn-blue">
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
//Add Filter for Display TODO: Needs implementation during users phase
    /*
    $(document).ready(function() {
       var span = $('<span class="filter">Display </span>')
            .prependTo($('.top .dataTables_filter'));
        var select = $('<select><option value="">Select</option><option value="users">My Own</option><option value="">All</option></select>')
            .appendTo(span)
            .on('change', function () {
                var dataTable = $("#ordersTable").DataTable();
                var filterColumn = 8; // status column
                var val = $(this).val();
                if (val)
                {
                    val = '^'+$(this).val()+'$';
                }
                dataTable.column( filterColumn )
                    .search( val , true, false )
                    .draw();
            } );
    });
    */
</script>

<table id="reportsTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Report Name</th>
            <th>Created</th>
        </tr>
    </thead>
</table>
