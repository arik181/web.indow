<? 
initializeDataTable($selector       = "#groupsTable", 
                    $ajaxEndPoint   = "/groups/list_json",
                    $columns        = array("id", 
                                            "group_name",
                                            "address",
                                            "email",
                                            "phone",
                                            "usercount"),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/groups/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no groups available."
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
                var dataTable = $("#groupsTable").DataTable();
                var filterColumn = 3; // status column
                var val = $(this).val();
                if (val)
                {
                    val = '^'+$(this).val()+'$';
                }
                dataTable.column( filterColumn )
                    .search( val , true, false )
            } );
                    .draw();
    });
</script>

<script type="text/javascript">
//Add Filter for Display TODO: Needs implementation during groups phase
    $(document).ready(function() {
        var span = $('<span class="filter">Display </span>')
            .prependTo($('.top .dataTables_filter'));
        var select = $('<select><option value="">Select</option><option value="1">My Own</option><option value="0">All</option></select>')
            .appendTo(span)
            .on( 'change', function () {

            } );
    });
</script>

<table id="groupsTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Group Name</th>
            <th>Address</th>
            <th>Contact Email</th>
            <th>Contact Phone</th>
            <th>Users</th>
        </tr>
    </thead>
</table>

