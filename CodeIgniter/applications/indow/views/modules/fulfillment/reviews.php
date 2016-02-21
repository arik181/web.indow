<script>
    $(document).ready(function(){
       $('.fulfillment_child').show(); 
    });
    </script>
    
    
<?php 
initializeDataTable($selector       = "#reviewFullTable", 
                    $ajaxEndPoint   = "/fulfillment/final_review_json/1",
                    $columns        = array("id", 
                                            "name", 
                                            "modified", 
                                            "customer", 
                                            "company", 
                                            "payment",
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
                    $emptyString    = "There are no records available.",
                     $extraCreationJS = "$('td:eq(5)', row).html($('<input type=\"checkbox\" disabled/>').prop('checked', data['payment']));"
                    );
?>
<script type="text/javascript">

</script> 

<div class="fulfillment_full_data_table">
<table id="reviewFullTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Order</th>
            <th>Status_Update</th>
            <th>Customer</th>
            <th>Company</th>
            <th>Payment</th>
            <th>Modified</th>
        </tr>
    </thead>
</table>
</div>