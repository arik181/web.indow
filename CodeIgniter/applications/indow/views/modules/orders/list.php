<style>
    th:nth-child(12), td:nth-child(12) {
        display: none;
    }
</style>
<?php $curr_uid = $this->ion_auth->get_user_id(); ?>
<? 
initializeDataTable($selector       = "#ordersTable", 
                    $ajaxEndPoint   = "/orders/list_json/".$curr_uid,
                    $columns        = array(
                                            'id',
                                            'order_name',
                                            'status',
                                            'customer',
                                            'dealer',
                                            'created_by', 
                                            'po_num',
                                            'num_win', 
                                            'total', 
                                            'order_date', 
                                            'updated',
                                            'open'
                                        ),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/orders/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no orders available.",
          $extraCreationJS = "
                $('td:eq(2)', row).html(data.status + ' <i class=\"icon fa fa-info-circle\" title=\"' + data.status_name + '\"></i>');
				if (data.dealer_id) {
					$('td:eq(4)', row).html($('<a href=\"/groups/edit/' + data.dealer_id + '\"></a>').text(data.dealer));
				}
                $('td:eq(8)', row).html('$' + parseFloat(data.total).toFixed(2));
          "
                    , '\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'', 'orderlist');
?>

<script type="text/javascript">
//Add Filter for Status
    $(document).ready(function() {
        var span = $('<span class="filter">Filter By Status </span>')
            .prependTo($('.top .dataTables_filter'));
        var select = $('<?= str_replace("\n", "", form_dropdown('', $status_options)) ?>')
            .appendTo(span)
            .on('change', function () {
                var val, filterColumn;
                var dataTable = $("#ordersTable").DataTable();
                filterColumn = 2; // status column
                val = $(this).val();
                if (val === 'open') {
                    dataTable.column(2)
                    .search( '' , true, false )
                    .draw();
                    filterColumn = 11;
                    val = 1;
                } else {
                    dataTable.column(11)
                    .search( '' , true, false )
                    .draw();
                }

                console.log(dataTable.column( filterColumn ));
                dataTable.column( filterColumn )
                    .search( val , true, false )
                    .draw();
            } ).val('open').change();
    });
</script>

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

<?php if(!empty($orders)):?>
<table id="ordersTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Order #</th>
            <th>Status</th>
            <th>Customer</th>
            <th>Dealer</th>
            <th>Created By</th>
            <th>PO #</th>
            <th># of Inserts</th>
            <th>Order Total</th>
            <th>Order Date</th>
            <th>Updated</th>
            <th>Open</th>
            
        </tr>
    </thead>
</table>
<!--button type="button" class="btn btn-blue view-all btn-sm">View All</button-->
<?php else:?>
    <div id="list_empty_message">No orders have been added.</div>
<?php endif;?>
