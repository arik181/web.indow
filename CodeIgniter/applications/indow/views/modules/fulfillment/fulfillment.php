<?php
    global $status_codes_actual; // fixes scope issue for draw_status_datatable function
    $status_codes_actual = $status_codes;
function draw_status_datatable($status_code) {
    global $status_codes_actual;
    $status_codes = $status_codes_actual;
    ##########
    initializeDataTable($selector       = '#status_' . $status_code . '_table', 
                        $ajaxEndPoint   = '/fulfillment/order_by_status_json/' . $status_code . '/1',
                        $columns        = array(
                                            'id',
                                            'id',
                                            'status_date',
                                            'customer',
                                            'company',
                                            'pcount',
                                            'creator'
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

    <div class="fulfillment_data_table">
          <h3><?= $status_codes[$status_code] ?> (<?= $status_code ?> Status)</h3>
    <table id="status_<?= $status_code ?>_table" class="display table table-hover" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Actions</th>
                <th>Order</th>
                <th>Status Update</th>
                <th>Customer</th>
                <th>Company</th>
                <th>Panel/Product Count</th>
                <th>Creator</th>
               
            </tr>
        </thead>
    </table>
    <a href="/fulfillment/orders_by_status/<?= $status_code ?>" class="fulfillment_view_all_link">View all</a>
    </div>

<? }
function draw_status_datatable_payment($status_code) {
    global $status_codes_actual;
    $status_codes = $status_codes_actual;

initializeDataTable($selector       = '#status_' . $status_code . '_table', 
                    $ajaxEndPoint   = '/fulfillment/order_by_status_json/' . $status_code . '/1',
                    $columns        = array("id", 
                                            "id", 
                                            "status_date", 
                                            "customer", 
                                            "company", 
                                            "payment",
                                             "submitted_date",
                                             "creator"
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
                    $extraCreationJS = "$('td:eq(5)', row).html($('<input type=\"checkbox\" disabled/>').prop(\"checked\", parseInt(data.payment)));"
                    );
?>
<div class="fulfillment_data_table">
	<h3><?= $status_codes[$status_code] ?> (<?= $status_code ?> Status)</h3>
	<table id="status_<?= $status_code ?>_table" class="display table table-hover" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>Actions</th>
				<th>Order</th>
				<th>Status Update</th>
				<th>Customer</th>
				<th>Company</th>
				<th>Payment</th>
				<th>Submitted</th>
				<th>Creator</th>
			</tr>
		</thead>
	</table>
	<a href="/fulfillment/orders_by_status/<?= $status_code ?>/1" class="fulfillment_view_all_link">View all</a>
</div>

<? } ?>

<? draw_status_datatable(100) ?>

<?
initializeDataTable($selector       = "#scheduleTable", 
                    $ajaxEndPoint   = "/fulfillment/schedule_json/1",
                    $columns        = array("id", 
                                            "name",
                                            "status_update", 
                                            "customer", 
                                            "company", 
                                            "address", 
                                            "msr",
                                            "creator"
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

<div class="fulfillment_data_table">
<h3>Schedule Measurement (200 Status)</h3>
<table id="scheduleTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Order</th>
            <th>Status update</th>
            <th>Customer</th>
            <th>Company</th>
            <th>Address</th>
            <th>MSR Requested</th>
            <th>Creator</th>
        </tr>
    </thead>
</table>
<a href="/fulfillment/schedule" class="fulfillment_view_all_link">View all</a>
</div>

<? draw_status_datatable(280) ?>

<?php 
initializeDataTable($selector       = "#ordersTable", 
                    $ajaxEndPoint   = "/fulfillment/order_json/1",
                    $columns        = array("id", 
                                            "name", 
                                            "customer", 
                                            "company", 
                                            "address", 
                                            "created",
                                            "creator"
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


<div class="fulfillment_data_table">
    <h3>New Orders (300 Status)</h3>
  
<table id="ordersTable" class="display table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Actions</th>
            <th>Order</th>
            <th>Customer</th>
            <th>Company</th>
            <th>Address</th>
            <th>Order Submitted</th>
            <th>Creator</th>
        </tr>
    </thead>
</table>
<a href="/fulfillment/order" class="fulfillment_view_all_link">View all</a>
</div>

<? draw_status_datatable_payment(320) ?>
<? draw_status_datatable_payment(330) ?>
<? draw_status_datatable(350) ?>
<? draw_status_datatable(380) ?>
<? draw_status_datatable(400) ?>
<? draw_status_datatable(480) ?>
<? draw_status_datatable(500) ?>
<? draw_status_datatable(600) ?>
<? draw_status_datatable(680) ?>
