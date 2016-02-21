<style>
    #create_jobsite_cont, #estimatesTable_info, #ordersTable_info {
        display: none;
    }
	.dl-totals dd {
		position: relative;
		left: -10px;
	}
	.total_header {
		position: relative;
		left: 30px;
	}
    table.dataTable.display tbody tr > .sorting_2, table.dataTable.order-column.stripe tbody tr > .sorting_2 {
        background-color: transparent !important;
    }
</style>
<?php if ($mode != "add") { ?>
<style>
    #customer_manager {
        display: none;
    }
</style>
<?php } ?>

<script>
    <? if ($this->session->flashdata('open_quote')) { ?>
        window.open('/quotes/edit/<?= $this->session->flashdata('open_quote') ?>', '_blank');
    <? } ?>
    var site_id = <?= $siteid ?>;
    var site_mode = 'add/edit';
    var primary_customer_id = <?= json_encode($primary) ?>;
    var indow_user_name = <?= json_encode($user_name) ?>;
    var products_info = <?= json_encode($product_info) ?>;
    <? if (isset($product_info_msrp)) { ?>
        var products_info_msrp = <?= json_encode($product_info_msrp) ?>;
    <? } ?>
    <? if (isset($review_wholesale_discount)) { ?>
        var indow_review_wholesale_discount = <?= json_encode($review_wholesale_discount) ?>;
    <? } ?>
    var indow_product_options = '<?= addslashes(str_replace("\n", '', form_dropdown('',$product_options,'','class="form-control input-sm product_options"'))) ?>';
    var indow_edging_options = '<?= addslashes(str_replace("\n", '',form_dropdown('edging_id',$edging_options, '','class="form-control input-sm changedata"'))) ?>';
    var indow_products_json = <?= json_encode($product_options) ?>;
    var indow_js_edging = <?= json_encode($edging_options) ?>;
    var indow_module = 'jobsites';
    var indow_delete_items = [];
    var indow_measurements_editable = <?= !empty($measurements_editable) ? 'true' : 'false' ?>;

    // var indow_items = <?//= json_encode($items) ?>;
</script>
<?
    if ($this->permissionslibrary->has_edit_permission(6)) {
        echo $this->load->view('themes/default/partials/order_review.php');
    }
    if ($this->permissionslibrary->_user->in_admin_group) {
        echo $this->load->view('modules/sites/add_items_to_order.php');
    }
?>
<div id="clonecont">
<?= $this->load->view('themes/default/partials/product_checkboxes'); ?>
    <?= $this->load->view('themes/default/partials/windowoptions', array('max_w' => $product->max_width, 'max_h' => $product->max_height)); ?>
    <div id="preordercont">
        Select an estimate from the table below to create a preorder from it, or <a href="/orders/create_preorder/<?= $site_id ?>" class="btn btn-sm btn-blue">Create Blank Preorder</a>
        <table id="estimateSelectTable" class="display table table-hover" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th></th>
                    <th>Customer</th>
                    <th>Windows</th>
                    <th>Est.Cost</th>
                    <th>Created</th>
                    <th>Created By</th>
                    <th>Dealer</th>
                </tr>
            </thead>
        </table>
    </div>
	<div id="edit_tech_form">
		<form method="post" action="/sites/reassign_tech/<?= $site_id ?>">
			<h3>Change/Un-assign Tech</h3><br>
			<? $change_tech_users[''] = 'Unassigned'; ?>
			<?= form_dropdown('tech_id', $change_tech_users, $tech ? $tech->id : null, "class='input-sm form-control' id='change_owner'"); ?><br><br>
			<input style="margin-left: 0px;" class="btn btn-blue btn-sm" type="submit" value="Change / Un-assign Tech">
		</form>
	</div>
</div>
<script src="/assets/theme/default/js/bootbox.min.js"></script>
<script src="/assets/theme/default/js/jquery.json.js"></script>
<script src="/assets/theme/default/js/z9170.js"></script>
<script src="/assets/theme/default/js/sitedata.js"></script>

<?php if (isset($message)): ?>
<div class="well" id="flash_data">
    <?php echo $message; ?>
</div>
<?php endif; ?>
<div class="confirm-div well" id="flash_data" style="display:none"></div>
<script>
// assumes you're using jQuery
    $(document).ready(function() {
        $('.confirm-div').hide();
<?php if ($this->session->flashdata('message')) { ?>
            $('.confirm-div').html('<?php echo $this->session->flashdata('message'); ?>').show();
    <?php } ?>

    });
</script>
<?php
$attributes = array(
    'class' => 'stlabel',
    'style' => 'font-weight:normal'
);
?>

<?php if ($mode != "add") { ?>
    <?php if (isset($job_site)) { ?>
    <div class="row show-grid-sm">
        <div class="col-xs-12 text-right">
            <?php

                if ($this->permissionslibrary->has_edit_permission(14))
                {
                    echo create_preorder_button($siteid);
                }
            ?>
        </div>
    </div>
    <!-- <div class="row"><h3 class="col-xs-12">Job Site Record</h3></div> -->
    <div class="row show-grid-sm">
        <div class="inline-block col-xs-5">
            <?= $job_site ?>
        </div>
        <div class="inline-block col-xs-4">
            <?= $primary_customer_info ?>
        </div>
		<div class="col-xs-3">
			<div class="info_view">
				<div class="contact_group">
					<h4 class="inline info_header">Measure Tech</h4>
					<? if($this->permissionslibrary->has_edit_permission(5, $site_id)) { ?>
						<button style="font-size: .9em; margin: 0px;" class="inline btn btn-blue btn-smp pull-right" id="edit_tech">Manage</button>
					<? } ?>
					<br><br>
					<b><?= !empty($tech) ? $tech->name : '' ?></b>
				</div>
			</div>
		</div>
    </div>
    <?php } ?>
<?php } ?>
<?= form_open('', array('id' => 'jobsites_form', 'class' => 'jobsites_form')); ?>
<input id="customer_data" name="customer_data" type="hidden">
<?php if ($mode == "add") { ?>
    <div class="row">
        <div class="col-xs-12">
            <h3>Job Site Record</h3>
            <h4><strong>STEP 1:</strong>&nbsp;Enter Job Site Info</h4>
            <h4>Job Site Info</h4>
        </div>
    </div>
    <div id="job-site-info" class="row show-grid form-horizontal">
        <!-- <div class="association-form tab-pane active content-row" id="new_customer_tab1"></div> -->
        <div class="col-xs-4">
            <div class="form-group">
                <?= form_label('Address', 'address', array('class' => 'col-xs-3 control-label')); ?>
                <div class="col-xs-9">
    <?= form_input(array('name' => 'site_address', 'id' => 'site_address_1', 'class' => 'jrequire form-control input-sm', 'value'=>@$post->address, 'data-name' => 'Address')); ?>
                </div>
            </div>

            <div class="form-group">
                <?= form_label('Address Ext', 'address_ext', array('class' => 'col-xs-3 multi-line-label')); ?>
                <div class="col-xs-9">
                    <?= form_input(array('name' => 'site_address_ext', 'id' => 'site_address_1_ext', 'class' => 'form-control input-sm', 'value'=>@$post->address_ext)); ?>
                </div>
            </div>

            <div class="form-group">
                <?= form_label('City', 'city', array('class' => 'col-xs-3 control-label')); ?>
                <div class="col-sm-9">
    <?= form_input(array('name' => 'site_city', 'id' => 'site_city', 'class' => 'form-control input-sm jrequire', 'value'=>@$post->city, 'data-name' => 'City')); ?>
                </div>
            </div>
        </div>
        <div class="col-xs-offset-4 col-xs-4">
            <div class="form-group">
                <?= form_label('State/Province', 'state', array('class' => 'col-xs-3 control-label')); ?>
                <div class="col-sm-9">
                <?= state_select('site_state',@$post->state, false, array('class' => 'form-control input-sm jrequire', 'data-name' => 'State')) ?>
                </div>
            </div>

            <div class="form-group">
                <?= form_label('Zipcode', 'zipcode', array('class' => 'col-xs-3 control-label')); ?>
                <div class="col-sm-9">
    <?= form_input(array('name' => 'site_zipcode', 'id' => 'site_zipcode', 'class' => 'form-control input-sm jrequire', 'value'=>@$post->zipcode, 'data-name' => 'Zipcode')); ?>
                </div>
            </div>

            <div class="form-group">
                <?php $options = array('0' => 'Residential', '1' => 'Commercial'); ?>
                <?= form_label('Type', 'site_address_type', array('class' => 'col-xs-3 control-label')); ?>
                <div class="col-sm-9">
                    <?= form_dropdown('site_address_type', $options, @$post->address_type, 'class="form-control input-sm"'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h4><strong>STEP 2:</strong>&nbsp;Add Customer to Job Site</h4>
        </div>
    </div>
<?php } ?>
<?= form_close(); ?>

<? /* Tab bar */ ?>
<?= $this->load->view('modules/customers/customer_manager', array('tab' => isset($site->id) ? 1 : 0)); ?>
<? /*
<? if ($mode === 'add') { ?>
    <style>
        #user_manager_table { display: none; }
    </style>
    <h4><strong>STEP 3:</strong>&nbsp;Associate Users to the Job Site</h4>
    <div class="row">
    <?= user_assoc(array(), false); ?>
    </div><br><br>
<? } ?>
*/ ?>


<? /*
<div class="row" style="clear: both;">
<?= $this->load->view('themes/default/partials/product_key'); ?><br><br>
</div>
*/ ?>

<div class="row">
    <div class="row">
        <div class="col-xs-12">
            <? $this->load->view('modules/sites/windows_data', array('new' => !$site_id)) ?>
        </div>
    </div>
    <? $this->load->view('themes/default/partials/multi_edit_bar', array('module' => 'jobsites', 'new' => !$site_id)); ?>
    <br style="clear: both"><br>
</div>
<div class="row">
	<div class="row">
		<dl class="dl-horizontal dl-totals col-xs-3 col-xs-offset-6">
			<dt>Subtotal</dt>
			<dd id="subtotal">$0.00</dd>

			<dt class="spec_geom_toggle">Special Geometry Fee (<span id="spec_geom_count">0</span>)</dt>
			<dd class="spec_geom_toggle" id="spec_geom_fee">$0.00</dd>

			<dt style="font-size: 1.1em">Total</dt>
			<dd style="font-size: 1.1em" id="grand_total">$0.00</dd><br>          
		</dl>
		<div class="col-xs-3" style="text-align: right;">
			<div class="row">
				<div class="total_header col-xs-7">Total Sq. Feet</div>
				<div class="col-xs-5" id="total_sqft">0</div>
			</div>
			<div class="row">
				<div class="total_header col-xs-7">Windows</div>
				<div class="col-xs-5" id="total_windows">0</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
    <!-- Quotes and Estimate by Datatable -->
        <? if (isset($customer_notes)) { ?>
            <div class="content-row notes_view">
                <h4 class="inline notes_header">Notes</h4>
                <?= $customer_notes ?>
            </div>
        <? } ?>
        <? if (isset($internal_notes) && $this->permissionslibrary->_user->in_admin_group) { ?>
            <div class="content-row">
                <?= $internal_notes ?>
            </div>
        <? } ?>
        <div class="content-row">
            <label class="control-label" for="tech_notes">Job Notes</label>
            <textarea name="tech_notes" id="tech_notes" class="form-control input-sm"><?= @$site->tech_notes ?></textarea>
            <? if (!empty($site)) { ?>
                <button class="btn btn-sm btn-blue pull-right" id="save_tech_notes" style="margin-top: 15px;">Save Job Notes</button>
            <? } ?>
        </div>
    <?php if ($mode != "add") { ?>
        <?php
        initializeDataTable($selector = "#estimatesTable", $ajaxEndPoint = "/sites/estimates_json/" . $siteid, $columns = array("id",
            "customer_name",
            "jobsite",
            "status",
            "item_count",
            "price",
            "created",
            "created_by",
            "dealer"), $primaryKey = "id", $actionButtons = array(array('class' => 'icon',
                'title' => 'View',
                'href' => '/estimates/edit/',
                'innerHtml' => '<i class="sprite-icons view"></i>'
            )
                ), $actionColumn = 0, $emptyString = "There are no estimates available.",
            $extraCreationJS = "
                $('#estimates_count').html(dataTable.data().length); $('td:eq(5)', row).html('$ ' + data['price']);
                    var ptable = $('#estimateSelectTable').DataTable();
                    ptable.row.add(data).draw();
            ", $dom = '\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'', $extra = 0, $omitscript = false, $filter = true
        );
        ?>
        <a name="estimates-anchor"></a>

        <div id="estimates-section" class="assumption-acc">
            <a style="width:100%;" data-target="#estimatesTable"  href="javascript:void(0);" data-toggle="collapse" class="add-user-opt"><h1><span id="estimates_count">0</span> Estimates<span id="extra_info"></span>  <i style="margin-top: 7px;float: right;margin-right: 10px;" class="fa fa-chevron-down"></i></h1></a>
            <a href="/estimates/from_site/<?= $site->id ?>" class="create-link">Create</a>
            <table id="estimatesTable" class="display table table-hover collapse out" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>Customer</th>
                        <th>Job Site</th>
                        <th>Type</th>
                        <th>Windows</th>
                        <th>Est.Cost</th>
                        <th>Created</th>
                        <th>Created By</th>
                        <th>Dealer</th>

                    </tr>
                </thead>
            </table>
        </div>

        <?php
        initializeDataTable($selector = "#quotesTable", $ajaxEndPoint = "/sites/quotes_json/" . $siteid, $columns = array("id",
            "customer_name",
            "jobsite",
            "status",
            "item_count",
            "price",
            "created",
            "created_by_name",
            "dealer"
                ), $primaryKey = "id", $actionButtons = array(array('class' => 'icon',
                'title' => 'View',
                'href' => '/quotes/edit/',
                'innerHtml' => '<i class="sprite-icons view"></i>'
            )
                ), $actionColumn = 0, $emptyString = "There are no quotes available.", $extraCreationJS = "$('td:eq(5)', row).html('$ ' + data['price']); $('#quotes_count').html(dataTable.data().length);", $dom = '\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'', $extra = 0, $omitscript = false, $filter = true
        );
        ?>
        <a name="quotes-anchor"></a>
        <div id="quotes-section" class="assumption-acc">
            <a style="width:100%;" data-target="#quotesTable"  href="javascript:void(0);" data-toggle="collapse" class="add-user-opt"><h1><span id="quotes_count">0</span> Quotes<span id="extra_info"></span>  <i style="margin-top: 7px;float: right;margin-right: 10px;" class="fa fa-chevron-down"></i></h1> </a>

            <table id="quotesTable" class="display table table-hover collapse out" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>Customer</th>
                        <th>Job Site</th>
                        <th>Type</th>
                        <th>Windows</th>
                        <th>Est. Price</th>
                        <th>Created</th>
                        <th>Created By</th>
                        <th>Dealer</th>
                    </tr>
                </thead>
            </table>

        </div>

        <?php
        initializeDataTable($selector = "#ordersTable", $ajaxEndPoint = "/sites/orders_json/" . $siteid, $columns = array("id",
            "order_name",
            "status",
            "customer",
            "dealer",
            "signed_purchase_order",
            "created_by_name",
            "created"),
            $primaryKey = "id",
            $actionButtons = array(
                array('class' => 'icon',
                    'title' => 'View',
                    'href' => '/orders/edit/',
                    'innerHtml' => '<i class="sprite-icons view"></i>'
                )
            ),
            $actionColumn = 0,
            $emptyString = "There are no orders available.",
            $extraCreationJS = "$('td:eq(8)', row).html(data['check_user']).css('display','none');
            $('#orders_count').html(dataTable.data().length);
            ",
            $dom = '\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'',
            $extra = 0,
            $omitscript = false,
            $filter = true
        );
        ?>
        <a name="orders-anchor"></a>
        <div id="orders-section" class="assumption-acc">
            <a style="width:100%;" data-target="#ordersTable"  href="javascript:void(0);" data-toggle="collapse" class="add-user-opt"><h1><span id="orders_count">0</span> Orders<span id="extra_info"></span>  <i style="margin-top: 7px;float: right;margin-right: 10px;" class="fa fa-chevron-down"></i></h1> </a>
            <table id="ordersTable" class="display table table-hover collapse out" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>Order</th>
                        <th>Current Status</th>
                        <th>Customer</th>
                        <th>Dealer</th>
                        <th>Purchase Order</th>
                        <th>Created By</th>
                        <th>Order Date</th>

                    </tr>
                </thead>
            </table>
        </div>

    <?php } ?>

    <!-- End of the Task -->
<?php if ($mode != "add") { ?>
    <? if (isset($quotes_estimates)) { ?>
        <div class="row">
            <div class="col-xs-12">
                <?= $quotes_estimates ?>
            </div>
        </div>
    <? } ?>
    <!--
    <? if (isset($orders)) { ?>
        <div class="content-row">
            <?= $orders ?>
            </div>
        <? } ?>
        -->

        <? // echo user_assoc($assoc_users) ?>

        <?= customer_history();
        initializeDataTable($selector = "#customerhistorytable", $ajaxEndPoint = "/sites/customer_history/" . $siteid,
                $columns = array("id",
                    "groupname",
                    "type",
                    "first_name",
                    "last_name",
                    "zipcode",
                    "username",
                ),
                $primaryKey = "id",
                $actionButtons = array(
                    array(
                        'class' => 'icon',
                        'title' => 'View',
                        'href' => '/customers/edit/',
                        'innerHtml' => '<i class="sprite-icons view"></i>'
                    )
                ),
                $actionColumn = 0,
                $emptyString = "There is no history available",
                $extraCreationJS = "",
                $dom = '\'<"top"iflp<"clear">>rt<"bottom"p<"clear">>\'',
                $extra = 0,
                $omitscript = false,
                $filter = true
        );
    } ?>
</div>

<div class="row">
    <hr />
    <div class="inline">
        <input id="savesite" type="submit" name="submit" value="Save" class="ctrl_s btn btn-blue btn-sm pull-right" />
    </div>

    <?php if (isset($site->id)): ?>
	    <div class="inline">
			<a href="/sites/delete/<?= $site->id ?>" class="btn btn-gray btn-sm delete pull-left">Delete Site</a>
			<a href="/sites" class="btn btn-gray btn-sm pull-left" style="margin-left: 20px;">Cancel</a>
		</div>
    <?php endif; ?>
</div>
<script src="/assets/theme/default/js/shared.js"></script>
<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js'); ?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js'); ?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/jobsite.js'); ?>"></script>

<style>
    #site_info_form .popover {
        min-width: 300px;
    }
    #site_info_form .popover input, #site_info_form .popover select {
        margin-bottom: 10px;
    }
</style>
<script>
    $('#site_info_form').on('submit', function() {
        var id = $('#site_id').val();
        $.ajax({
            url: (id == 0 ? '/sites/add_post' : '/sites/edit_json/' + id),
            type: 'POST',
            dataType: 'json',
            data: $("#site_info_form").serialize(),
            success: function(response) {
                if (response.Bool) {
                    $('#jsonaddress').html(response.Data.address);
                    $('#jsonaddress_ext').html(response.Data.address_ext);
                    $('#jsoncity').html(response.Data.city);
                    $('#jsonstate').html(response.Data.state);
                    $('#jsonzipcode').html(response.Data.zipcode);
                    $('#jsonaddress_type').html(response.Data.address_type);
                }
                $('[href="#site_info_popup"]').click();
                alert(response.Message);
            }

        });
        return false;
    });
</script>
