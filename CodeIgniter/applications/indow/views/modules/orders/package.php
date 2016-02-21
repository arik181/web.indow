<script src="<?php echo base_url('assets/theme/default/js/jquery.js');?>"></script>
<link rel="icon" type="image/png" href="<?php echo base_url('favicon.ico?v=2'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/css/jquery-ui.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/css/bootstrap.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/plugins/font-awesome/css/font-awesome.min.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/css/bootstrap-theme.css');?>"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.0/css/jquery.dataTables.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/css/package.css');?>"/>
<!-- <script type="text/javascript" src="assets/js/flot/jquery-1.8.3.min.js"></script>       -->
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/js/flot/excanvas.min.js"></script><![endif]-->   
<script type="text/javascript" src="<?php echo base_url('assets/js/flot/jquery.flot.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/flot/jquery.flot.pie.js');?>" ></script>
<script src="<?php echo base_url('assets/theme/default/js/bootstrap.min.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/jquery-ui.min.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/tooltip.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/bootstrap.js');?>"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>

<script>
    var order_id = <?= isset($order->id) ? $order->id : 0 ?>;
    var products_info = <?= json_encode($product_info) ?>;
    var indow_product_options = '<?= addslashes(str_replace("\n", '', form_dropdown('',$product_options,'','class="form-control input-sm product_options"'))) ?>';
    var indow_edging_options = '<?= addslashes(str_replace("\n", '', form_dropdown('edging_id',$edging_options,'','class="form-control input-sm edging_options"'))) ?>';
    var indow_active_fees = <?= json_encode($active_fees) ?>;
    var indow_fee_info = <?= json_encode($fee_info) ?>;
    var indow_shipping_address = <?= isset($order->shipping_address_id) ? $order->shipping_address_id : 'null' ?>;
    var indow_user_name = <?= json_encode($user_name) ?>;
    var indow_disabled_status = <?= json_encode($disabled_status) ?>;
    var orders_mode = 'add/edit';
    var indow_module = 'orders';
    <? if (isset($dealer_address->credit)) { ?>
        var indow_credit_limit = <?= json_encode($dealer_address->credit) ?>;
    <? } ?>
</script>

<script src="/assets/theme/default/js/jquery.json.js"></script>
<script src="/assets/theme/default/js/shared.js"></script>
<script src="/assets/theme/default/js/order_package.js"></script>

<!-- ### HIDDEN FORM DATA ### -->
<div class="container">
<form id="orderform" method="post">
    <input id="orderdata" name="orderdata" type="hidden">
</form>
<? /*
<div id="clonecont">
    <?= $this->load->view('themes/default/partials/product_checkboxes'); ?>
    <?= draw_fees_list($fees_sorted); ?>
</div>
 */ ?>
<input type="hidden" id="customer_id" value="0">
<!-- ### /HIDDEN FORM DATA ### -->

<!-- ### DEALER INFO ###  -->
<div class="row">
    <div class="col-xs-6">
        <h3>Indow Order Package</h3>
        <h4><?=$customer['company_name']?></h4>
    </div>
    <div class="col-xs-3 col-xs-offset-3">
<? if ( isset($dealer->id)
   &&   isset($dealer->logo)
   &&   is_file( '.' . $dealer->logo)) { ?>
   <img src="<?php echo base_url($dealer->logo); ?>"/>
<? } else { ?>
No Logo Available
<? } ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-4">
        <?=@$dealer->username?><br/>
        <?=@$dealer->address_1?><br/>
        <?=@$dealer->address_1_ext?><br/>
        <?=@$dealer->city_1 . ', ' . @$dealer->state_1 . ' ' . @$dealer->zipcode_1?><br/>
    </div>
    <div class="col-xs-4">
        <?=@$dealer->phone_1?><br/>
        <?=@$dealer->email_1?><br/>
        <?=@$dealer->phone_2?><br/>
        <?=@$dealer->email_2?><br/>
        <?=@$dealer->phone_3?><br/>
        <?=@$dealer->email_3?>
    </div>
</div>

<!-- ### /DEALER INFO ### -->
<div class="bluebox">
    <div class="row show-grid">
        <div class="col-xs-8">
            <h4>Order Details</h4>
        </div>
        <div class="col-xs-4">
            <h4>Site Details</h4>
        </div>
    </div>
    <div class="row">
        <!-- ### CUSTOMER INFO ###  -->
        <div class="col-xs-4">
            <?=@$customer['company_name']?><br/>
            <?=@$customer['name']?><br/>
            <?=@$customer_user->address_1?><br/>
            <?=@$customer_user->address_1_ext?><br/>
            <?=@$customer_user->city_1 . ', ' . @$customer_user->state_1 . ' ' . @$customer_user->zipcode_1?><br/>
        </div>
        <div class="col-xs-4">
            <?=@$dealer->phone_1?><br/>
            <?=@$dealer->email_1?><br/>
            <?=@$dealer->phone_2?><br/>
            <?=@$dealer->email_2?><br/>
            <?=@$dealer->phone_3?><br/>
            <?=@$dealer->email_3?>
        </div>
        <!-- ### /CUSTOMER INFO ### -->
        <!-- ### SITE INFO ###  -->
        <div class="col-xs-4">
            <?=@$site['address']?><br/>
            <?=@$site['address_ext']?><br/>
            <?=@$site['city_state_zipcode']?><br/>
        </div>
        <!-- ### /SITE INFO ### -->
    </div>
</div>

<!-- ### ITEMSTABLE INFO ### -->
<div class="row">
    <div class="col-xs-12">
        <table id="itemsTable" class="display table table-hover condensed" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th width="115x">Room</th>
                    <th width="115x">Location</th>
                    <th>Product</th>
                    <th>Product Type</th>
                    <th>Tubing</th>
                    <th width="24px"><span title="Special Geometry" class="special-geo">&#x22BE;</span></th>
                    <th>Retail</th>
                </tr>
            </thead>
            <?php if (empty($items)) { ?>
            <tbody>
                <tr>
                    <td colspan="12">
                        No orders have been added.
                    </td>
                </tr>
            </tbody>
            <?php } ?>
        </table>
        <?= form_open('new_user_form', array('id'=>'new_user_form','class'=>'new_user_form')); ?>
        <?= form_close(); ?>
    </div>
</div>
<!-- ### /ITEMSTABLE INFO ### -->

<!-- ### TOTALS ### -->
<br style="clear: both"><br>
<div class="bluebox">
<div class="indow_totals row show-grid">
    <div class="col-xs-4 col-xs-offset-5">
        <?= $this->load->view('themes/default/partials/totals.php'); ?>
    </div>
    <div class="col-xs-3">
        <div class="row">
            <div class="total_header col-xs-7">Total Sq. Feet</div>
            <div class="col-xs-5" id="total_sqft">0</div>
        </div>
        <div class="row">
            <div class="total_header col-xs-7">Windows</div>
            <div class="col-xs-5" id="total_windows">0</div>
        </div>
        <div class="row">
            <div class="total_header col-md-7">Products</div>
            <div class="col-md-5" id="total_products">0</div>
        </div>
    </div>
</div>
</div>
<br><br>
<!-- ### /TOTALS ### -->

<!-- ### PRODUCT KEY ### -->
<?= $this->load->view('themes/default/partials/package_key'); ?><br><br>
<!-- ### /PRODUCT KEY ### -->

<!-- ### FOOTER ### -->
<h5>This order is exclusively for indow products. This order is valid for 21 days and are subject to review at signing due to delays or material price increases beyond our control.</h5>
<div id="package-footer">www.indowwindows.com</div>
<!-- ### /FOOTER ### -->
</div>
