<script>
    $(document).ready(function(){
       $('.fulfillment_child').show(); 
    });
    </script>
<?php 
initializeDataTable($selector       = "#ordersFullTable", 
                    $ajaxEndPoint   = "/fulfillment/order_json/1",
                    $columns        = array("id", 
                                            "name", 
                                            "customer", 
                                            "company", 
                                            "address", 
                                            "created"
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
  
<table id="ordersFullTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Order</th>
            <th>Customer</th>
            <th>Company</th>
            <th>Address</th>
            <th>Order Submitted</th>
        </tr>
    </thead>
</table>
</div>

