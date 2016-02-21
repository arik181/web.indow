<script>
    var indow_product_options = '<?= addslashes(str_replace("\n", '', form_dropdown('',$product_options,'','class="form-control input-sm product_options"'))) ?>';
    var indow_delete_items = [];
    var products_info = <?= json_encode($product_info) ?>;
    var order_id = <?= $order_id ?>;
    var indow_module = 'freebird';
    var indow_edging_lookup = <?= json_encode($this->order_model->id_name_array('edging')) ?>;
    var indow_customer_ids = <?= json_encode($customer_ids) ?>;
    var indow_legacy = <?= json_encode($legacy) ?>;
</script>
<style>
    .popover-content .tooltip-inner {
        padding-left: 8px !important;
    }
    .popover-content .tooltip {
        min-width: 200px !important;
    }
    .popover {
        z-index: 1040 !important;
    }
    .popover-title {
        display: none;
    }
    #validation_response {
        font-size: 1.1em;
        font-weight: bold;
    }
    #validation_response .alert-error {
        color: #990000;
    }
    #customer_edit_btn {
        display: none;
    }
</style>
<script src="/assets/theme/default/js/bootbox.min.js"></script>
<script src="/assets/theme/default/js/jquery.json.js"></script>
<script src="/assets/theme/default/js/shared.js"></script>
<script src="/assets/theme/default/js/measure_order.js"></script>
<script src="/assets/theme/default/js/z9170.js"></script>
<script src="/assets/theme/default/js/orderdata.js"></script>
<div id="clonecont">
    <?= $this->load->view('themes/default/partials/product_checkboxes'); ?>
    <?= $this->load->view('themes/default/partials/windowoptions', array('module' => 'freebird')); ?>
</div>
<div class="container">
    <div class="row show-grid-sm">
        <div class="col-xs-3">
            <h3>Order# <?= $order->order_number ?></h3>
        </div>
        <div class="col-xs-6 col-xs-offset-2">
            <div style="font-size: 22px; margin-top: -60px; margin-bottom: 10px;">Welcome to <span class="indow_blue">MEASURE</span>, the Indow measurement<br>portal.   Please follow the instructions below.</div>
        </div>
    </div>
</div>
<div class="greybar">
    <div class="container">
        <div class="row">
            <div class="col-md-4 data-chunk">
                <h2>Customer Info</h2>
                <?= $customer ? render_contact_info($customer, isset($customer->addr) ? $customer->addr : null) : 'No Customer'; ?>
            </div>
            <div class="col-md-4 data-chunk">
                <h2>Shipping Address</h2>
                <? // display_address($shipping_address) ?>
                <?= $ship_to ?>
            </div>
        </div>
    </div>
</div>
<div class="bluebar">
    <div class="container">
        <div class="row">
            <h3 class="barheader">Order Items</h3>
        </div>
    </div>
</div>
<div class="container freebird" id="content_view">
    <div class="row">
        <br><?= $this->load->view('themes/default/partials/freebird_instructions', array('hide_validation' => true, 'key_title' => 'Product Type Key')); ?><br>
    </div>
    
    <div class="row padtop">
        <h2>Window List</h2>
        <div id="measured_count"></div>
        <table id="itemtable" class="display table table-hover" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th><input type="checkbox" id="checkall"></th>
                    <th>Status</th>
                    <th>Room</th>
                    <th>Location</th>
                    <th>Product</th>
                    <th>Product Type</th>
                    <th>Tubing</th>
                    <th>Width</th>
                    <th>Height</th>
                    <th>Dimensions</th>
                </tr>
            </thead>
        </table>
        <button id="additem" class="btn btn-default pull-right">Add Item</button>
        <br><br>
        With Selected:<br>
        <button class="btn btn-default" id="withselected_delete">Delete Items</button>
    </div>
    <div class="row">
        <br><?= $this->load->view('themes/default/partials/product_key', array('hide_validation' => true, 'key_title' => 'Product Type Key')); ?><br><br>
    </div>
    <div class="row">
        <input style="position: relative; top: 2px;" type="checkbox" id="understand">
        <label class="inline" for="understand">I understand that by clicking 'Submit Measurements' below no changes to my order will be possible without speaking with an Indow representative..</label>
        <hr>
        <form method="post" id="measureform">
            <input id="measuredata" type="hidden" name="measuredata">
            <button id="saveorder" type="button" disabled="disabled" class="btn btn-blue nomargin">Submit Measurements</button>
            <button style="display: none;" id="cancelorder" type="button" class="btn btn-blue pull-right">Cancel</button>
        </form>
    </div>
</div>
<? $this->load->view('themes/default/partials/freebird_instructions_modal') ?>
