<script>
    $(document).ready(function(){
       $('.fulfillment_child').show(); 
    });
    </script>
    
    
<?php

if ($mode == '1') {
	$cols = array(
		"id", 
		"id", 
		"status_date", 
		"customer", 
		"company", 
		"payment",
		"submitted_date",
		"creator"
	);
	$js = "$('td:eq(5)', row).html($('<input type=\"checkbox\" disabled/>').prop(\"checked\", parseInt(data.payment)));";
} else {
	$cols = array(
		'id',
		'id',
		'status_date',
		'customer',
		'company',
		'pcount',
		'creator'
	);
	$js = " ";
}


initializeDataTable($selector       = "#reviewFullTable", 
                    $ajaxEndPoint   = "/fulfillment/order_by_status_json/$status_code/0",
                    $columns        = $cols,
                    $primaryKey     = "id",
                    $actionButtons  = array(array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/orders/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no records available.",
                     $extraCreationJS = $js
                    );
?>

<div class="fulfillment_full_data_table">
<table id="reviewFullTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
		<? if ($mode == 1) { ?>
			<th>Actions</th>
			<th>Order</th>
			<th>Status Update</th>
			<th>Customer</th>
			<th>Company</th>
			<th>Payment</th>
			<th>Submitted</th>
			<th>Creator</th>
		<? } else { ?>
            <th>Actions</th>
            <th>Order</th>
            <th>Status Update</th>
            <th>Customer</th>
            <th>Company</th>
            <th>Panel/Product Count</th>
			<th>Creator</th>
		<? } ?>
        </tr>
    </thead>
</table>
</div>