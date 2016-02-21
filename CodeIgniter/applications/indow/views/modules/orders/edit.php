<?php if (isset($message)): ?>
    <div class="well" id="flash_data">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<?php
$save_perm = $is_indow || ($this->permissionslibrary->has_edit_permission(6) && (empty($order->id) || $this->permissionslibrary->_user->in_admin_rep_group));
$disabled = (!$save_perm) ? ' disabled':'';
?>
<script>
    var mode = '<?= $mode ?>';
    var order_id = <?= isset($order->id) ? $order->id : 0 ?>;
    var products_info = <?= json_encode($product_info) ?>;
    var indow_product_options = '<?= addslashes(str_replace("\n", '', form_dropdown('',$product_options,'','class="form-control input-sm product_options"'))) ?>';
    var indow_edging_options = '<?= addslashes(str_replace("\n", '', form_dropdown('edging_id',$edging_options,'','class="form-control input-sm edging_options"'))) ?>';
    var indow_active_fees = <?= json_encode($active_fees) ?>;
    var indow_fee_info = <?= json_encode($fee_info) ?>;
    var no_payment_options = <?= ($save_perm)? 'true':'false'; ?>;
    var indow_start_payments = <?= json_encode($payments) ?>;
    var indow_payments = {};
    var indow_payment_type_options = <?= json_encode($payment_type_options) ?>;
    var indow_delete_items = [];
    var indow_user_name = <?= json_encode($user_name) ?>;
    var indow_disabled_status = <?= json_encode($disabled_status) ?>;
    var indow_message = <?= json_encode($extramessage) ?>;
    var indow_module = 'orders';
    var indow_wholesale_discount = <?= json_encode($wholesale_discount) ?>;
    var override_perm = <?= $this->permissionslibrary->_user->in_admin_group ? 'true' : 'false' ?>;
    var auto_refresh = <?= json_encode($this->session->flashdata('refresh')) ?>;
    var indow_legacy = <?= json_encode($legacy) ?>;
    <? if (isset($dealer_address->credit)) { ?>
        var indow_credit_limit = <?= json_encode($dealer_address->credit) ?>;
    <? } ?>

</script>
<script src="/assets/theme/default/js/bootbox.min.js"></script>
<script src="/assets/theme/default/js/jquery.json.js"></script>
<script src="/assets/theme/default/js/shared.js"></script>
<script src="/assets/theme/default/js/orders.js"></script>
<script src="/assets/theme/default/js/z9170.js"></script>
<script src="/assets/theme/default/js/orderdata.js"></script>

<style>
    #customer_manager {
        display: none;
    }
    .cut_image_link {
        display: block !important;
    }
    td.price {
        white-space: nowrap;
        padding-left: 0px;
        padding-right: 10px;
    }
    td.price .dollar {
        display: inline-block;
    }
    td.price input[type="text"], td.sprice input[type="text"] {
        margin-right: 10px !important;
        margin-left: 0px !important;
        padding-left: 2px;
        padding-right: 2px;
        width: 50px;
        text-align: right;
    }
    td.price input[type="checkbox"], td.sprice input[type="checkbox"] {
        position: relative;
        top: 2px;
    }
    <? if ($this->permissionslibrary->_user->in_admin_group) { ?>
        td.price, td.sprice {
            text-align: left !important;
        }
    <? } ?>
</style>

<form id="orderform" method="post">
    <input id="orderdata" name="orderdata" type="hidden">
</form>

<div id="clonecont">
    <?= draw_fees_list($fees_sorted, true); ?>
    <?= $this->load->view('themes/default/partials/product_checkboxes'); ?>
    <?= $this->load->view('themes/default/partials/windowoptions', array('module' => 'orders', 'mode'=>$mode)); ?>
</div>

<div id="warnings_cont"></div>

<div class="row">
    <div class="col-xs-4 popover-md">
        <?= $contact_info ?>
    </div>
    <div class="col-xs-4 popover-sm">
        <?= $site_info ?>
    </div>
    <div class="col-xs-4 text-right">
        <div class="header_blue">
            <? if (!empty($order->id)) { ?>
                <span style="margin-right: 15px;">ORDER #</span><?= $order->id ?>
            <? } ?>
        </div>
        <? if (!empty($measure_tech)) { ?>
            Measure Tech: <?= $measure_tech ?>
        <? } ?>
    </div>
</div>

<div class="row">
    <br style="clear: both;"><?= $this->load->view('modules/customers/customer_manager', array('tab' => isset($order->id) ? 1 : 0)); ?>
</div>

<? if (!empty($dealer_address)) { ?>

<h3 class="content-header first">Dealer Info</h3>

<div class="row">
    <div class="col-xs-5">
        <h3 class="bigger"><a href="/groups/edit/<?= $dealer_id ?>"><?= $dealer_address->name ?></a></h3>
    </div>
    <div class="col-xs-6">
        <? if (!empty($order_id) && $this->permissionslibrary->_user->in_admin_group) { ?>
        <label><?= form_checkbox('js_pricing', '', @$order->js_pricing, 'id="js_pricing"') ?> Use Jobsite Pricing</label>
        <? } ?>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <h4>Dealer Location</h4>
        <div id="contact_info_view" class="info_view">
            <?= display_address($dealer_address) ?>
        </div>
    </div>
    <div class="col-xs-7 col-xs-offset-1">
        <? if (!empty($creator)) { ?>
        <h4>Associated User</h4>
        <div class="row">
            <div class="col-xs-4">
                <? if (!empty($order_id) && $this->permissionslibrary->_user->in_admin_group) { ?>
                    <?= form_dropdown('change_owner', $change_owner_users, $creator->id, "class='input-sm form-control' id='change_owner'"); ?>
                <? } else { ?>
                    <?= $creator->first_name . ' ' . $creator->last_name ?>
                <? } ?>
            </div>
            <div class="col-xs-3">
                <?= $creator->phone_1 ?>
            </div>
            <div class="col-xs-5">
                <?= $creator->email_1 ?>
            </div>
        </div>
        <? } ?>
    </div>
</div>
<? } ?>


<h3 class="content-header">Shipping Info</h3>
<div class="row">
    <div class="col-xs-4 popover-sm">
        <?= $ship_to ?>
    </div>
    <div class="col-xs-4 col-xs-offset-1">
        <?= $this->load->view('/modules/orders/bundle_tool', array('disabled' => $disabled)) ?>
    </div>
</div>
<div class="row inline-form-spaced order-status">
    <div class="col-xs-12 form-inline show-grid">
        <div class="form-group">
            <label for="ship_method" class="control-label">Ship Method</label><br>
            <?
            $ship_method_options = array(
                'Will Call'         => 'Will Call',
                'Local Delivery'    => 'Local Delivery',
                'Ground'            => 'Ground',
                'Air'               => 'Air',
                'Truck'             => 'Truck',
                'International'     => 'International',
            );
            echo form_dropdown('ship_method', $ship_method_options, @$order->ship_method, "class='form-control input-sm' id='ship_method' style='width: 180px;' $disabled");
            ?>
        </div>
        <div class="form-group">
            <label for="carrier" class="control-label">Carrier</label><br>
            <?
            $carrier_options = array(
                'UPS'               => 'UPS',
                'UPS Freight'       => 'UPS Freight',
                'FedEx Freight'     => 'FedEx Freight',
                'DHL'               => 'DHL',
                'Ceva Logistics'    => 'Ceva Logistics',
                'Other'             => 'Other',
            );
            echo form_dropdown('carrier', $carrier_options, @$order->carrier, "class='form-control input-sm' id='carrier' style='width: 168px;' $disabled");
            ?>
        </div>
        <div class="form-group">
            Tracking Number<br>
            <input name="tracking_num" id="tracking_num" class="input-sm form-control" value="<?= @$order->tracking_num ?>" <?= $disabled ?>>
        </div>
    </div>
</div>


<h3 class="content-header">Order Status</h3>
<div class="row order-status show-grid">
    <div class="form-inline col-xs-12">
        <div class="form-group">
            <? $extra = array('class'=>'control-label'); ?>
            <?= form_label('Current Status', 'current_status', $extra); ?><br />
            <? $name = 'status_code'; ?>
            <? $options = $status_options; ?>
            <? $selected = @$order->status_code;
            $extra = ' class="form-control input-sm" style="width: 180px" id="status_code"'.$disabled; ?>
            <?= form_dropdown($name, $options, $selected, $extra); ?>
        </div>

        <? if (empty($dealer)) { ?>
            <div class="form-group" style="width: 218px;"></div>
        <? } ?>

        <div class="form-group">
            <? $extra   = array('class'=>'control-label'); ?>
            <?= form_label('Est. Ship Date', 'commit_date', $extra); ?><br />
            <? $name = 'commit_date';
            $extra = 'type="text" class="form-control input-sm datepicker" id="commit_date"'.$disabled;
            $date = isset($order->commit_date) ? date('Y-m-d', strtotime($order->commit_date)) : '';
            echo form_input($name, $date, $extra); ?>
        </div>


        <div class="form-group">
            <?= form_label('Requested Ship Date', 'requested_ship', array('class'=>'control-label')); ?><br />
            <? $name = 'expedite_date';
            $extra = 'type="text" class="form-control input-sm datepicker" id="requested_ship"'.$disabled;
            $date = isset($order->expedite_date) ? date('Y-m-d', strtotime($order->expedite_date)) : '';
            echo form_input($name, $date, $extra); ?>
        </div>

        <div class="form-group checkbox inline-down-sm">
            <? $name    = 'order_confirmation_sent'; ?>
            <? $value   = '1'; ?>
            <? $checked = @$order->order_confirmation_sent == 1; ?>
        </div>

        <? if (!empty($dealer)) { ?>
            <div class="form-group">
                <? $extra   = array('class'=>'control-label'); ?>
                <?= form_label('PO#', 'po_num', $extra); ?><br />
                <? $name = 'po_num'; ?>
                <? $extra = 'type="text" class="form-control input-sm"'.$disabled; ?>
                <?= form_input($name, @$order->po_num, $extra); ?>
            </div>
        <? } ?>
        <div class="form-group">
            <label>
            <?= form_checkbox('expedite', '', @$order->expedite, "id='exp_check' style='position: relative; top: 4px; margin-left: 5px; margin-top: 18px;' $disabled") ?>
            Expedite
            </label><br>
            <? if (!empty($order_id) && $this->permissionslibrary->_user->in_admin_group) { ?>
                <label>
                <?= form_checkbox('remake', '', @$order->remake, "style='margin-left: 5px;' $disabled") ?>
                Remake
                </label>
            <? } ?>
        </div>
    </div>
</div>



<div class="row">
    <div class="col-sm-12">
        <div class="panel-group" id="customerNotesAccordion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#customerNotesAccordion" href="#collapseOne">
                            Show Status History <span class="glyphicon glyphicon-chevron-down pull-right">
                        </a>
                    </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse">
                    <div class="panel-body">
                        <? if (count($status_history)) { ?>
                        <dl class="dl-horizontal">
                            <? foreach($status_history as $sc) { ?>
                                    <dt><?= date("M j @ g:ia", strtotime($sc->status_changed)) ?></dt>
                                    <dd><?= $sc->name ?> changed the status to <?= $sc->description ?>.</dd>
                            <? } ?>
                        </dl>
                        <? } else { ?>
                            <div class="alert alert-warning">There is no history available.</div>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<? if ($save_perm) { ?>
<br><br><button class="submitpage nomargin btn btn-blue">Save</button>
<? } ?>
<h3 class="content-header">Order Notes</h3>
<? if (isset($order_notes)): ?>
<div class="row">
    <div class="col-sm-12">
      <?= $order_notes ?>
   </div>
</div>
<? endif; ?>

<?php if($this->permissionslibrary->_user->in_admin_group) { ?>

    <? if (isset($internal_notes)) { ?>
        <?= $internal_notes ?>
    <? } ?>

<?php } ?>

<h3 class="content-header">Order Details</h3>
<? if (isset($status_code) && $status_code < 200 && $freebird && $this->permissionslibrary->has_edit_permission(14)) { ?>
<button class="btn btn-blue pull-right btn-sm" id="send_measurement_form">Send Measurement Form</button><br><br><br>
<? } ?>

<div class="row" id="order_tabbar">
    <div class="col-xs-12">
        <?= $order_tabbar ?>
    </div>
</div>


<? function draw_item_table($table_id, $index, $is_indow, $save_perm) { ?>
    <table data-index="<?= $index ?>" id="<?= $table_id ?>" class="itemtable display table table-hover condensed dataTable" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th><?= ($save_perm) ? '<input type="checkbox" class="checkall" />':''; ?></th>
                <th style="display: none">Status</th>
                <th>Window</th>
                <th>Item&nbsp;#</th>
                <th>Room<div class="room_req">*required</div></th>
                <th>Location</th>
                <th><span title="Special Geometry" class="special-geo">&#x22BE;</span></th>
                <th>Product</th>
                <th>P.Type</th>
                <th>Tubing</th>
                <th>Thickness</th>
                <th>Width</th>
                <th>Height</th>
                <th>Sqft</th>
                <th>Price</th>
                <th>Product</th>
            </tr>
        </thead>
    </table>
<? } ?>

<div id="order_tables" class="row">
    <div class="col-xs-12">
        <div class="tab-content">
            <!-- Order Items Window Measurements -->
            <div class="order-form tab-pane active" id="order_items_tab">
                <div class="order-form-pane">
                    <? draw_item_table('items_table_1', 0, $is_indow, $save_perm) ?>
                </div>
            </div>
            <!-- Back Order Items Window Measurements -->
            <div class="order-form tab-pane" id="backorder_items_tab">
                <div class="backorder-form-pane">
                    <? draw_item_table('items_table_2', 1, $is_indow, $save_perm) ?>
                </div>
            </div>
            <!-- Re-Order Items Window Measurements -->
            <div class="order-form tab-pane" id="reorder_items_tab">
                <div class="reorder-form-pane">
                    <? draw_item_table('items_table_3', 2, $is_indow, $save_perm) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($save_perm): ?>
<? $this->load->view('themes/default/partials/multi_edit_bar'); ?>
<?php endif; ?>

<br style="clear: both"><br>
<div class="row">
    <div class="col-md-12">
        <div class="form-inline pull-left">
        </div>
        <div class="pull-right">
        <? if (isset($order->id)) { ?>
            <a target="_blank" href="/orders/csv_export/<?= $order->id ?>" class="btn btn-blue btn-sm">Export</a>
        <? } ?>
        <? if ($is_indow && isset($order->id)) { ?>
            <? /* <button id="send_order_confirmation" class="btn btn-blue btn-sm">Order Confirmation</button> */ ?>
            <a class="btn btn-blue btn-sm" target="_blank" href="/orders/confirmation/<?= $order->id ?>">Review</a>
        <? } ?>
        </div>
    </div><br style="clear: both"><br>
</div>


<div class="row" id="payment_details">
    <div class="form-inline col-xs-12">
        <h4>Payment Details &nbsp;
            <? if (!empty($dealer)) { ?>
                <small>Credit Line: <?= $dealer->credit ? 'Yes' : 'No' ?>&nbsp;&nbsp;&nbsp; Credit Hold: <?= $dealer->credit_hold ? 'Yes' : 'No' ?>
                <input style="display: none;" id="credit_limit" type="checkbox" <?= !empty($dealer->credit) ? 'checked="checked"' : '' ?>></small>
            <? } ?>
        </h4>
    </div>
</div>

<table id="paymentsTable" class="display dataTable table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Received</th>
            <th>Payment Type</th>
            <th>Amount</th>
            <?php if($save_perm): ?>
            <th>Options</th>
            <?php endif; ?>
        </tr>
    </thead>

    <tbody>
        <tr id="addpaymentform">
            <?php if($save_perm): ?>
            <td>
                <? $extra = array('class'=>'control-label'); ?>
                <? $name = 'payment_received'; ?>
                <? $extra = 'type="text" class="datepicker form-control input-sm"'; ?>
                <?= form_input($name, '', $extra); ?>
            </td>

            <td>
                <? $extra = array('class'=>'control-label'); ?>
                <? $name = 'payment_type_id'; ?>
                <? $extra = ' class="form-control input-sm" '; ?>
                <?= form_dropdown($name, $payment_type_options, '', $extra); ?>
            </td>

            <td>
                <? $extra = array('class'=>'control-label'); ?>
                <? $name = 'payment_amount'; ?>
                <? $extra = 'type="text" class="form-control inline input-sm" style="width: 100px;"'; ?>
                <span>$</span> <?= form_input($name, '', $extra); ?>
                <button id="addpayment" class="btn btn-sm btn-blue" type="button">Add</button>
            </td>

            <td>
                <button id="fees_discounts" class="btn btn-sm btn-blue btn-block nomargin">Fees & Discounts</button>
            </td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<br>
<div class="row show-grid-md">
    <div class="col-xs-5 col-xs-offset-7">
        <?= $this->load->view('/themes/default/partials/totals') ?>
    </div>
</div>
<?php if($save_perm) { ?>
<div class="row show-grid-md">
    <div class="col-xs-12">
        <?= $this->load->view('themes/default/partials/single_use_fees') ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <ul class="list-inline pull-right">
            <li><?= $this->load->view('themes/default/partials/order_package') ?></li>
        </ul>
    </div>
</div>
<?php } else { ?>
<script>
    var indow_user_fees = <?= !empty($user_fees) ? json_encode($user_fees) : '{}' ?>;
</script>
<? } ?>


<? if (!empty($order->estimate_id) && $this->permissionslibrary->has_view_permission(2)) { ?>
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseEstimates" class="collapsed">
                        Customer Estimate <span class="glyphicon glyphicon-chevron-down pull-right"></span>
                    </a>
                </h4>
            </div>
            <div id="collapseEstimates" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="pull-left">Estimate Created on <?= date("m/d/Y", strtotime($order->created));  ?></div>
                    <div class="pull-right"><a href="/estimates/export_data/<?=$order->estimate_id?>">Export Data</a> &nbsp; | &nbsp; <a href="/estimates/index/<?= $order->id ?>">View All Associated Estimates</a></small></div>

                    <table id="estimatesTable" class="display dataTable table table-hover" cellspacing="0" width="100%">
                        <thead>
                        <tr>
                            <th>Item #</th>
                            <th>Room<div class="room_req">*required</div></th>
                            <th>Location</th>
                            <th>Width</th>
                            <th>Height</th>
                            <th>Product</th>
                            <th>Product Type</th>
                            <th>Tubing</th>
                            <th>Special Geom</th>
                            <th>Retail</th>
                        </tr>
                        </thead>
                    </table>
                    <style>
                        #estimate_totals span {
                            display: inline-block;
                            min-width: 150px;
                        }
                    </style>
                    <div id="estimate_totals" style="display: inline-block;" class="pull-right">
                        <span class="total_header">Subtotal</span>
                        <span class="total_value text-right">$<?= money_format('%i', $estimate_totals['subtotal']) ?></span><br>

                        <span class="total_header">Grand Total</span>
                        <span class="total_value text-right">$<?= money_format('%i', $estimate_totals['total']) ?></span>
                    </div>
                    <?
                    initializeDataTable(
                        $selector       = "#estimatesTable",
                        $ajaxEndPoint   = "/estimates/item_list__wsubitems_json/" . $order->estimate_id,
                        $columns        = array("unit_num",
                            "room",
                            "location",
                            "width",
                            "height",
                            "product",
                            "product_type",
                            "edging",
                            "special_geom_yn",
                            "price",
                        ),
                        $primaryKey     = "id",
                        $actionButtons  = array(),
                        $actionColumn   = 0,
                        $emptyString    = "There are no estimates available.",
                        $rowCreationJS = "
                                console.log(data);
                                $('td:eq(9)', row).html('$' + parseFloat(data.price).toFixed(2));
                                if (getObjectSize(data.subproducts)) {
                                    render_subproducts(dataTable, row, true);
                                }
                            ",
                        '"p"'
                    );
                    ?>
                </div>
            </div>
        </div>
    </div>
<? } ?>
<?php if($save_perm): ?>
<div class="row">
    <div class="col-xs-12">
        <hr />
        <? if (isset($order->id)) { ?>
        <a href="/orders/delete/<?= $order->id ?>" class="btn btn-gray delete pull-left">Delete Order</a>
        <? } else { ?>
        <a href="/orders" class="btn btn-gray pull-left">Cancel</a>
        <? } ?>
        <div class="inline">
            <button class="submitpage btn btn-blue pull-right ctrl_s">Save</button>
        </div>
        <label class="inline pull-right">
            <input name="followup" id="follow_up" type="checkbox" <?= isset($order->followup) && $order->followup ? 'checked="checked"' : ''?>> Follow up
        </label>
    </div>
</div>
<?php endif; ?>

<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js'); ?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js'); ?>"></script>
