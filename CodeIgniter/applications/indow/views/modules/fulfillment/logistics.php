<script src="<?php echo base_url('assets/theme/default/js/jquery.json.js');?>"></script>
<div id="clonecont">
    <?= form_dropdown('status', $status_options, '', 'class="changedata status_code_options input-sm form-control" style="width: 135px; padding: 4px"') ?>
    <?= form_dropdown('status', $shipping_status_options, '', 'class="changedata shipping_status_code_options input-sm form-control" style="width: 135px; padding: 4px"') ?>
    <?= form_dropdown('status', $filter_status_options, '', 'style="width: 125px;" id="status_filter_options" class="inline input-sm form-control"') ?>
</div>
<script>
    var indow_combined_orders = <?= json_encode($combined_orders) ?>;
    var indow_combined_orders_packaging = <?= json_encode($combined_orders_packaging) ?>;
</script>

<script src="<?php echo base_url('assets/theme/default/js/main.js');?>"></script>
<div style="margin-top: -50px"></div>
<style>
.expedite-row, .expedite-row:hover, .expedite-row td, .expedite-row td:hover {
    background-color: #FFD5D5 !important;
}
#shippingTable_filter {
    position: relative;
    top: -50px;
}
#shippingTable {
    position: relative;
    top: -20px;
}
#packagingTable .btn {
    margin-left: 0px !important;
    margin-right: 6px;
}
</style>
<?php 
initializeDataTable($selector       = "#shippingTable", 
                    $ajaxEndPoint   = "/fulfillment/shipments_json",
                    $columns        = array("id",
                                            "name",
                                            "dealer",
                                            "address", 
                                            "status",
                                            "build_date",
                                            "commit_date",
                                            "panel",
                                            "ship_method",
                                            "carrier",
											"tracking_num",
                                            "ship_fee",
                                            "id",
                                            "id"
                                            
                                        ),
                    $primaryKey     = "id",
                    $actionButtons  = array(
                                                array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/orders/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no records available.",
                    $extraCreationJS = "shipping_row(data, row); ",
                    $dom ='\'<"top"if<"clear">>rt<"bottom"<"clear">>\'',
                    $extra = 'nopaging',
                    $omitscript = false,
                    $filter = false                                   
                    
                    );
?>

<?php 
initializeDataTable($selector       = "#packagingTable", 
                    $ajaxEndPoint   = "/fulfillment/packaging_json",
                    $columns        = array("id",
                                            "status",
                                            "commit_date",
                                            "panel",
                                            "dealer", 
                                            "ship_method",
                                            "carrier",
                                            "id",
                                        ),
                    $primaryKey     = "id",
                    $actionButtons  = array(
                                            array('class' => 'icon',
                                                  'title' => 'View',
                                                  'href'  => '/orders/edit/',
                                                  'innerHtml'  => '<i class="sprite-icons view"></i>'
                                                 )
                                           ),
                    $actionColumn   = 0,
                    $emptyString    = "There are no records available.",
                    $extraCreationJS = "packaging_row(data, row); ",
                    $dom ='\'<"top"if<"clear">>rt<"bottom"<"clear">>\'',
                    $extra = 'nopaging',
                    $omitscript = false,
                    $filter = false
                    );
?>
<?php if (isset($message)): ?>
<div class="well" id="flash_data">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<?= $logistics_tabbar ?>
<div class="tab-content">
    <div class="association-form tab-pane active content-row" id="shipping_tab">
        <a class="nomargin btn btn-blue btn-header btn-sm" href="/combine_orders" style="margin-left: 0px">Combine Orders</a>
        <table id="shippingTable" class="display table table-hover" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Dealer</th>
                    <th>Shipping</th>
                    <th>Status</th>
                    <th>Build</th>
                    <th>Commit</th>
                    <th>Panels</th>
                    <th>Ship Method</th>
                    <th>Carrier</th>
                    <th>Tracking #</th>
                    <th>Ship Fee</th>
                    <th>Labels</th>
                    <th>Packing Lists</th>

                </tr>
            </thead>

        </table>
        <div class="row">
            <div class="action-btn pull-right" style="text-align: right">
                With Selected:<br>
                <button id="logistics_print" class="pull-right btn-blue btn">Print</button>
            </div>
        </div>
        <?= $logistics_data ?>
    </div>
    <div class="packaging-form tab-pane content-row" id="packaging_tab">
        <table id="packagingTable" class="display table table-hover" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Commit</th>
                    <th>Panels</th>
                    <th>Dealer</th>
                    <th>Ship Method</th>
                    <th>Carrier</th>
                    <th>Print</th>
                </tr>
            </thead>

        </table>
        <?= $logistics_data ?>
    </div>
</div>
<form id="print_form" style="display: none;" method="post" action="/fulfillment/logistics_print" target="_blank">
    <input type="hidden" name="data" id="print_data">
</form>
<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/fulfillment_logistics.js');?>"></script>
