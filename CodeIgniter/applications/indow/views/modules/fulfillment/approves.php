<script>
    $(document).ready(function(){
       $('.fulfillment_child').show(); 
    });
    </script>
<?php 
initializeDataTable($selector       = "#approvedFullTable", 
                    $ajaxEndPoint   = "/fulfillment/approve_json/1",
                    $columns        = array("id", 
                                            "name", 
                                             "schanged",
                                            "customer", 
                                            "company", 
                                            "pcode"                                             
                                            ),
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/orders/view/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no records available."
                    );
?>
<script type="text/javascript">

</script> 


<div class="fulfillment_full_data_table">
  
<table id="approvedFullTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Order</th>
            <th>Status Update</th>
            <th>Customer</th>
            <th>Company</th>
            <th>Panel/Product Count</th>
           
        </tr>
    </thead>
</table>
</div>
