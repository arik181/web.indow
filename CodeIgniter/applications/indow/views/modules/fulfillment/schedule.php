<script>
    $(document).ready(function(){
       $('.fulfillment_child').show(); 
    });
    </script>
 <?php 
initializeDataTable($selector       = "#scheduleFullTable", 
                    $ajaxEndPoint   = "/fulfillment/schedule_json/1",
                    $columns        = array("id", 
                                            "name",
                                            "status_update", 
                                            "customer", 
                                            "company", 
                                            "address", 
                                            "msr"
                        ),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/orders/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no records available."
                    );
?>


<div class="fulfillment_full_data_table">
<table id="scheduleFullTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Order</th>
            <th>Status update</th>
            <th>Customer</th>
            <th>Company</th>
            <th>Address</th>
            <th>MSR Requested</th>
        </tr>
    </thead>
</table>

</div>