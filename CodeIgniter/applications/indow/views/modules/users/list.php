<?php $curr_uid = $this->ion_auth->get_user_id(); ?>
    <? 
initializeDataTable($selector       = "#usersTable", 
                    $ajaxEndPoint   = "/users/list_json/".$curr_uid,
                    $columns        = array("id", 
                                            "group_name",
                                            "first_name",
                                            "last_name",
                                            "zipcode",
                                            "username",
                                             "check_user",
                                             "active"),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/users/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no users available.",
                     $extraCreationJS = "
                     $('td:eq(6)', row).html(data['check_user']).css('display','none');
                     $('td:eq(7)', row).css('display','none');
                     "
                    );
?>

<script type="text/javascript">
//Add Filter for Status
    $(document).ready(function() {
        var span = $('<span class="filter">User Status </span>')
            .prependTo($('.top .dataTables_filter'));
        var select = $('<select id="activity_select"><option value="">Both</option><option value="1">Active</option><option value="0">Disabled</option></select>')
            .appendTo(span)
            .on('change', function () {
                var dataTable = $("#usersTable").DataTable();
                var filterColumn = 7; // status column
                var val = $(this).val();
                if (val)
                {
                    val = '^'+$(this).val()+'$';
                }
                dataTable.column( filterColumn )
                    .search( val , true, false )
                    .draw();
            } );
            $('#activity_select').val(1).change();
    });
</script>

<table id="usersTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Group Name</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Zipcode</th>
            <th>Username</th>
            <th style="display: none;">UserCheck</th>
            <th style="display: none;">Active</th>
        </tr>
    </thead>
</table>

