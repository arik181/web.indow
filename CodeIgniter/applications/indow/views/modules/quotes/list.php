<?php $curr_uid = $this->data['user']; ?>
<? 
initializeDataTable($selector       = "#quotesTable", 
                    $ajaxEndPoint   = "/quotes/list_json/".$curr_uid->id,
                    $columns        = array("id", 
                                            "customer_name",
                                            "jobsite",
                                            "status",
                                            "item_count",
                                            "price",
                                            "created",
                                            "created_by_name",
                                            "dealer",
                                            "check_user" ),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/quotes/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no quotes available.",
                     $extraCreationJS = "$('td:eq(9)', row).html(data['check_user']).css('display','none');"
                    );
?>
<script type="text/javascript">
//Add Filter for Status
    $(document).ready(function() {
        var span = $('<span class="filter">Filter By Status </span>')
            .prependTo($('.top .dataTables_filter'));
        var select = $('<select><option value="">Select</option><option value="Open">Open</option><option value="Closed">Closed</option></select>')
            .appendTo(span)
            .on('change', function () {
                var dataTable = $("#quotesTable").DataTable();
                var filterColumn = 3; // status column
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
</script>
<?= my_own_filter('#quotesTable', FALSE); ?>

<table id="quotesTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Customer</th>
            <th>Job Site</th>
            <th>Status</th>
            <th>Windows</th>
            <th>Est. Cost</th>
            <th>Created</th>
            <th>Creator</th>
             <th>Dealer</th>
            <th style="display:none;">Check</th>
        </tr>
    </thead>
</table>
