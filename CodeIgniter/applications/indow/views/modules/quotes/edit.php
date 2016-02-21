<?php if (isset($message)): ?>
<div class="well" id="flash_data">
    <?php echo $message; ?>
</div>
<?php endif; ?>
<script>
    var indow_product_options = '<?= addslashes(str_replace("\n", '', form_dropdown('',$product_options,'','class="form-control input-sm product_options"'))) ?>';
    var indow_active_fees = <?= json_encode($active_fees) ?>;
    var indow_fee_info = <?= json_encode($fee_info) ?>;
    var indow_delete_items = [];
    var indow_products_json = <?= json_encode($product_options) ?>;
    var products_info = <?= json_encode($product_info) ?>;
    var site_id = <?= isset($site->id) ? $site->id : 0 ?>;
    var quotes_id = <?= isset($quote->id) ? $quote->id : 0 ?>;
    var indow_module = 'quotes';
    var indow_products_json = <?= json_encode($product_options) ?>;
    var indow_js_edging = <?= json_encode($edging_options) ?>;
    <? if (isset($product_info_msrp)) { ?>
        var products_info_msrp = <?= json_encode($product_info_msrp) ?>;
    <? } ?>
    <? if (isset($review_wholesale_discount)) { ?>
        var indow_review_wholesale_discount = <?= json_encode($review_wholesale_discount) ?>;
    <? } ?>
</script>
<script src="/assets/theme/default/js/bootbox.min.js"></script>
<script src="/assets/theme/default/js/jquery.json.js"></script>
<script src="/assets/theme/default/js/shared.js"></script>
<script src="/assets/theme/default/js/quotes.js"></script>
<script src="/assets/theme/default/js/z9170.js"></script>
<script src="/assets/theme/default/js/quotedata.js"></script>

<form id="quoteform" method="post">
    <input id="quotedata" name="quotedata" type="hidden">
</form>

<?= $this->load->view('themes/default/partials/order_review.php'); ?>

<div id="clonecont">
    <?= draw_fees_list($fees_sorted); ?>
    <div id="productcheckboxes">
        <? foreach ($product_info as $product) {
            if ($product->product_id == 3) { ?>
            <div class="cboxrow" data-id="<?= $product->id ?>">
                <input type="checkbox" value="<?= $product->id ?>">
                <label class="glabel"><?= $product->product_type ?></label>
            </div>
            <? }
        } ?>
        <br style="clear: both;"><button class="pull-right btn btn-blue addproductssubmit" type="button">Add</button><br><br>
    </div>
    <?= $this->load->view('themes/default/partials/windowoptions'); ?>
</div>

<style>
    #customer_manager {
        display: none;
    }
    html {
           overflow-y: scroll; <? /* forcing scrollbar makes expansion of customer box not make the content shift. remove if you dont like; */ ?>
    }
    <? /*
    #windowoptions input, #windowoptions textarea {
        background-color: #ffffff;
        border: 0px;
        -webkit-box-shadow: none;
        box-shadow: none;
    } */ ?>
</style>

<div class="row">
    <h3><?=$subtitle?></h3>
    <div class="inline-block col-xs-5">
            <?= $contact_info ?>
    </div>
    <div class="inline-block col-xs-4 site-edit-sm">
            <?= $site_info ?>
    </div>
    <div id="topbuttons" class="inline-block col-xs-3">
    </div>
</div>
<div class="row">
    <br style="clear: both;"><?= $this->load->view('modules/customers/customer_manager', array('tab' => isset($quote->id) ? 1 : 0)); ?>
</div>

<div class="row">
    <table id="quoteitems" class="display table table-hover condensed" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Room</th>
                <th>Location</th>
                <th>Width</th>
                <th>Height</th>
                <th>Product</th>
                <th>Product Type</th>
                <th>Tubing</th>
                <th><span title="Special Geometry" class="special-geo">&#x22BE;</span></th>
                <th>Retail</th>
            </tr>
        </thead>
    </table>
</div>
<div class="row">
    <? /*
    <div class="col-xs-4">
        <button class="btn btn-sm btn-blue" id="additem">Add Window</button><br><br>
        With Selected<br>
        <button class="btn btn-sm btn-blue" id="withselected_delete">Delete</button>
    </div> */ ?>
    <div class="col-xs-5 col-xs-offset-7">
        <?= $this->load->view('/themes/default/partials/totals', array('idprefix' => '')) ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-4">
        <? if (!empty($quote->id)) { ?>
            <br><a target="_blank" class="btn btn-blue btn-sm nomargin" href="/quotes/print/<?= $quote->id ?>">Print</a>
        <? } ?>
    </div>
    <div class="col-xs-12" style="postion: relative; left: 45px;">
        <?= $this->load->view('themes/default/partials/single_use_fees'); ?>
    </div>
</div>
<div class="row">
    <br><div class="pull-right info_view"><button id="fees_discounts" class="btn btn-sm btn-blue">Fees & Discounts</button></div>
</div>


<br style="clear: both"><br>
<div class="row">
    <hr>
    <div class="inline">
        <? if (isset($quote->id) && $this->permissionslibrary->has_edit_permission(6)) { ?>
            <form id="promote_to_order" method="post" action="/orders/from_quote/<?= $quote->id ?>">
                <input id="review_data" name="review_data" type="hidden">
                <input type="submit" class="btn btn-blue btn-content pull-left" value="Promote to Order">
            </form>
        <? } ?>
        <button id="submitpage" class="btn btn-blue btn-content pull-right">Save</button>
    </div>
    <label class="inline pull-right">
        <input id="follow_up" type="checkbox" <?= isset($quote->followup) && $quote->followup ? 'checked="checked"' : ''?>> Follow up
    </label>
	<?php if (isset($quote->id) && $this->permissionslibrary->has_edit_permission(3, $quote->id)) { ?>
		<a href="/quotes/delete/<?= $quote->id ?>" class="btn btn-gray btn-content delete pull-left">Delete Quote</a>
	<? } else { ?>
		<a href="/quotes" class="btn btn-gray pull-left">Cancel</a>
	<? } ?>
</div>

<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js');?>"></script>
