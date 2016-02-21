<?php if (isset($message)): ?>
<div class="well" id="flash_data">
    <?php echo $message; ?>
</div>
<?php endif; ?>

<script>
    var estimates_product_options = <?= json_encode($product_options) ?>;
    var estimates_product_type_options =  <?= json_encode($product_type_options) ?>;
    var estimates_edging_options =  <?= json_encode($edging_options) ?>;
    var estimates_items = <?= json_encode($items) ?>;
    var indow_fee_info = <?= json_encode($fee_info) ?>;
    var indow_active_fees = <?= json_encode($fees) ?>;
    var products_info = <?= json_encode($product_info) ?>;        
    var estimates_parent_id = <?= $parent_id ?>;
    var estimates_customers = <?= json_encode($customers) ?>;
    var estimates_site_id = <?= json_encode($site_id) ?>;
    var indow_user_fees = <?= json_encode($user_fees) ?>;
</script>
<script src="/assets/theme/default/js/bootbox.min.js"></script>
<script src="/assets/theme/default/js/jquery.json.js"></script>
<script src="/assets/theme/default/js/shared.js"></script>
<script src="/assets/theme/default/js/estimates_ecc.js"></script><!-- initializeDataTable - data table is loaded in this javascript. -->

<form action="/estimates/add" id="estimateform" method="post">
    <input id="estimatedata" name="estimatedata" type="hidden">
</form>

<div class="row">
    <div class="col-xs-12">
        <ul class="list-inline text-right">
            <li><a href="/estimates/edit/<?= $estimate_id ?>" class="btn btn-blue btn-sm">Go Back to Estimate View</a></li>
        </ul>
    </div>
</div>


Estimate Name
<input id="estimate_name" class="form-control input-sm" style="width: 200px;">
<br><br>
<h2 id="itable1_title">Selected Products</h2>
<table id="itable1" class="display table table-hover" cellspacing="0">
    <thead>
        <tr>
            <th><input class="checkall" type="checkbox"></th>
            <th>Room</th>
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
<div class="row">
    <div class="col-xs-6">
        With Selected<br>
        <button id="moveitemdown" type="button" class="nomargin btn btn-blue btn-sm">Remove from Saved View</button><br><br>
    </div>
    <div class="col-xs-6">
    <?= $this->load->view('/themes/default/partials/totals') ?>
    </div>
</div>
<br><br>
<h2 id="itable2_title">Available Products</h2>
<h3 id="itable2_subtitle">Select from below to add to your new estimate configuration.</h3>
<table id="itable2" class="display table table-hover" cellspacing="0">
    <thead>
        <tr>
            <th><input class="checkall" type="checkbox"></th>
            <th>Room</th>
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
With Selected<br>
<button id="moveitemup" type="button" class="nomargin btn btn-blue btn-sm">Add to Estimate Configuration</button>

<br><br>
<hr>
<button id="submit_page" class="pull-right btn btn-blue">Save</button>
<a href="/estimates/edit/<?= $estimate_id ?>" class="btn btn-gray pull-left">Cancel</a>
