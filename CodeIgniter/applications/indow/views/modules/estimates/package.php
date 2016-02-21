<?
$title = $type === 'estimate' ? 'Indow Estimate Package' : 'Indow Quote';
?>
<!doctype html>
<html>
<head>
<title><?= $title ?></title>
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
<script src="<?php echo base_url('assets/theme/default/js/bootstrap.min.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/jquery-ui.min.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/bootstrap.js');?>"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>

<script>
    <? if ($type === 'estimate') { ?>
    var estimates_subitems = <?= json_encode($subitems) ?>;
    <? } else { ?>
    var estimates_subitems = {};
    <? } ?>
    var indow_active_fees = <?= json_encode($active_fees) ?>;
    var indow_user_fees = <?= json_encode($user_fees) ?>;
    var indow_fee_info = <?= json_encode($fee_info) ?>;
    var products_info = <?= json_encode($product_info) ?>;
    var entity_id = <?= $entity_id ?>;
    var entity_type = <?= json_encode($type) ?>;
</script>

<style>
    tr table {
        table-layout: fixed;
    }
    tr tr td:nth-child(3) {
        width: 128px !important;
        text-align: right;
        
    }
    tr tr td:nth-child(3) span {
        margin-right: 50px;
    }
    tr[role="row"] > td:nth-child(9) {
        text-align: right;
    }
    tr[role="row"] > td:nth-child(9) > span {
        margin-right: 50px;
    }
    .retail_head {
        text-align: right;
    }
    .retail_head span {
        margin-right: 50px;
    }
    #logo-black {
        display: none;
    }
</style>
<style type="text/css" media="print">
    #logo-black {
        display: inline !important;
    }
    #package-footer {
        padding-left: 0px !important;
    }
</style>

<script src="/assets/theme/default/js/jquery.json.js"></script>
<script src="/assets/theme/default/js/shared.js"></script>
<script src="/assets/theme/default/js/package.js"></script><!-- initializeDataTable - data table is loaded in this javascript. -->
</head>
<body>

<!-- ### HIDDEN FORM DATA ### -->
<div class="container">
<form id="estimateform" method="post">
    <input id="estimatedata" name="estimatedata" type="hidden">
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
        <h2><?= $title ?></h2>
    </div>
    <div class="col-xs-3 col-xs-offset-3">
    <? if ( isset($dealer->id)
       &&   isset($dealer->logo)
       &&   is_file( '.' . $dealer->logo)) { ?>
       <img src="<?php echo base_url($dealer->logo); ?>"/>
    <? } else { ?>
    No Logo Available
    <? } ?>
        <h3>Prepared On <?= @$estimate->created ?></h3>
    </div>
</div>
<div class="row">
    <div class="col-xs-4">
    </div>
    <div class="col-xs-4">
    </div>
</div>
<!-- ### /DEALER INFO ### -->

<div class="bluebox">
    <div class="row show-grid">
        <div class="col-xs-4">
            <?php /* Formerly Dealer Location */ ?>
            <h2>Dealer</h2>
        </div>
        <div class="col-xs-4">
            <h2>Ship To</h2>
        </div>
        <div class="col-xs-4">
            <h2>Customer</h2>
        </div>
    </div>
    <div class="row">
        <!-- ### DEALER LOCATION ###  -->
        <div class="col-xs-4">
        <h4><?=@$dealer->name?></h4>
        <?= $creator->first_name . ' ' . $creator->last_name ?><br/>
        <?=@$creator->phone_1?><br/>
        <?=@$creator->email_1?><br/>
        <?=@$creator->phone_2?><br/>
        <?=@$creator->email_2?><br/>
        <?=@$creator->phone_3?><br/>
        <?=@$creator->email_3?><br/><br/>
        <?= display_address($dealer_address) ?>
        </div>
        <!-- ### /DEALER LOCATION ### -->
        <?php /* Not Currently Required.
        <!-- ### SHIP TO ###  -->
        <div class="col-xs-4 col-xs-offset-4">
            <?=@$site['address']?><br/>
            <?=@$site['address_ext']?><br/>
            <?=@$site['city_state_zipcode']?><br/>
        </div>
        <!-- ### /SHIP TO ### -->
         */ ?>
        <!-- ### CUSTOMER INFO ###  -->
        <div class="col-xs-4 col-xs-offset-8">
            <?= !empty($customer['company_name']) ? $customer['company_name'] . '<br/><br/>' : '' ?>
            <?=@$customer['name']?><br/>
            <? /*<?= !empty($customer_address) ? display_address($customer_address) : '' ?> */ ?>
            <?
                if (!empty($customer['phone_1'])) {
                    echo $customer['phone_1'] . '<br>';
                } else if (!empty($customer['phone_2'])) {
                    echo $customer['phone_2'] . '<br>';
                } else if (!empty($customer['phone_3'])) {
                    echo $customer['phone_3'] . '<br>';
                }
                if (!empty($customer['email_1'])) {
                    echo $customer['email_1'] . '<br>';
                } else if (!empty($customer['email_2'])) {
                    echo $customer['email_2'] . '<br>';
                } else if (!empty($customer['email_3'])) {
                    echo $customer['email_3'] . '<br>';
                }
            ?>
        </div>
        <!-- ### /CUSTOMER INFO ### -->
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
                    <th width="50px">Width</th>
                    <th width="50px">Height</th>
                    <th>Product</th>
                    <th>Product Type</th>
                    <th>Tubing</th>
                    <th width="24px"><span title="Special Geometry" class="special-geo">&#x22BE;</span></th>
                    <th class="retail_head"><span>Price</span></th>
                    <th style="display: none">Sq. Ft</th>
                </tr>
            </thead>
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
    <div class="col-xs-5 col-xs-offset-3">
        <div class="row">
            <div class="total_header col-xs-7">Windows</div>
            <div class="col-xs-5" id="total_windows">0</div>
        </div>
        <div class="row">
            <div class="total_header col-md-7">Products</div>
            <div class="col-md-5" id="total_products">0</div>
        </div>
    </div>
    <div class="col-xs-4">
        <?= $this->load->view('themes/default/partials/totals.php'); ?>
    </div>
</div>
</div>
<br><br>
<!-- ### /TOTALS ### -->

<? if ($type === 'estimate') { ?>
<!-- ### PRODUCT KEY ### -->
<?= $this->load->view('themes/default/partials/package_key'); ?><br><br>
<!-- ### /PRODUCT KEY ### -->

<!-- ### FOOTER ### -->

<h5>This estimate is exclusively for Indow products. This estimate is valid for 21 days and is subject to review at signing due to delays or material price increases beyond our control.</h5>
<br><br>
<?= $this->load->view('modules/estimates/warranty') ?>
<br><br>
<?
    if (!empty($extra_html)) { ?>
        <div style="page-break-before: always">
        <?= $extra_html ?>
        </div>
    <? }
} ?>
<div id="package-footer"><img id="logo-black" src="/assets/theme/default/img/iw_logo_black.png" width="100" style="margin-right: 30px;">www.indowwindows.com</div>

<!-- ### /FOOTER ### -->
</div>
</body>
</html>
