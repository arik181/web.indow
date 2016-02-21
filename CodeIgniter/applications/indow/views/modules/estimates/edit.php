<?php if (isset($message)): ?>
<div class="well" id="flash_data">
    <?php echo $message; ?>
</div>
<?php endif; ?>
<script>
    var estimates_product_options = '<?= addslashes(str_replace("\n", '', form_dropdown('',$product_options,'','class="form-control input-sm product_options"'))) ?>';
    var estimates_product_type_options = '<select name="product_types_id" class="form-control input-sm product_type_options"></select>';
    var estimates_edging_options = '<?= addslashes(str_replace("\n", '', form_dropdown('edging_id',$edging_options,'','class="form-control input-sm edging_options"'))) ?>';
    var estimates_subitems = <?= json_encode($subitems) ?>;
    var indow_active_fees = <?= json_encode($active_fees) ?>;
    var indow_fee_info = <?= json_encode($fee_info) ?>;
    var products_info = <?= json_encode($product_info) ?>;
    var estimate_id = <?= $estimate_id ?>;
    var estimates_mode = 'add/edit';
    var indow_user_name = <?= json_encode($user_name) ?>;
    var indow_module = 'estimates';

</script>

<script src="/assets/theme/default/js/bootbox.min.js"></script>
<script src="/assets/theme/default/js/jquery.json.js"></script>
<script src="/assets/theme/default/js/shared.js"></script>
<script src="/assets/theme/default/js/estimates.js"></script><!-- initializeDataTable - data table is loaded in this javascript. -->

<form id="estimateform" method="post">
    <input id="estimatedata" name="estimatedata" type="hidden">
</form>

<div id="clonecont">
    <div id="techmodal" class="text-center">
        <? if (count($techs)) { ?>
            Select a tech to measure the job.<br><br>
            <?= form_dropdown('tech', $techs, '', 'class="input-sm form-control" id="tech_id"') ?><br>
            <button id="assign_tech" class="btn btn-blue btn-sm">Measure Job</button>
        <? } else { ?>
            No techs are available.
        <? } ?>
    </div>
    <?= $this->load->view('themes/default/partials/product_checkboxes'); ?>
    <?= draw_fees_list($fees_sorted); ?>
</div>

<div class="row show-grid">
    <div class="col-xs-12">
        <ul id="topbuttons" class="list-inline text-right">
			<? if (!empty($tech)) { ?>
                <li><b>Measure Tech: </b><?= $tech->name ?></li>
			<? } else if ($this->permissionslibrary->has_edit_permission(2, $estimate_id)) { ?>
                <li><button class="btn btn-blue btn-sm" id="measurejob">Measure Job</button></li>
			<? } ?>
        </ul>
    </div>
</div>

<input type="hidden" id="customer_id" value="0">
<div class="row">
    <div class="col-xs-5 popover-md">
        <?= $contact_info ?>
    </div>
    <div class="col-xs-5 popover-md">
        <?= $site_info ?><br>
        <label for="change_owner">Change Owner</label>
        <?= form_dropdown('change_owner', $change_owner_users, $estimate ? $estimate->created_by_id : $current_user, "class='input-sm form-control' id='change_owner' style='width: 150px'"); ?>
    </div>
    <? if ($estimate_id) { ?>
    <div class="col-xs-2">
    <? /*
        <label for="change_owner">Change Owner</label>
        <?= form_dropdown('change_owner', $change_owner_users, $estimate ? $estimate->created_by_id : $current_user, "class='input-sm form-control' id='change_owner'"); ?>
        */ ?>
            <?php if (isset($estimate->id)) { ?>
                <div class="text-right form-group">
                    <label class="control-label" for="goTo">Go to &nbsp;</label>
                    <select id="go_to" class="form-control input-sm" id="goTo" name="goTo">
                        <? if (isset($estimate->id)) { ?>
                            <option selected disabled>Saved Estimate View</option>
                            <option value="createnew">Create New Saved View</option>
                            <?php
                            if (!empty($estimate->parent_estimate_id)) { ?>
                                <option value="<?= $estimate->parent_estimate_id ?>">Parent Estimate</option>
                            <? }
                            if(!empty($se_dropdown))
                            {
                                ?>
                                <option disabled></option>
                                <?
                                foreach($se_dropdown as $option)
                                {
                                    ?>
                                    <option value="<?=$option->id?>" <?= $option->id == $estimate->id ? 'selected="selected"' : ''?>><?=$option->name?></option>
                                <?
                                }
                            }
                            ?>
                        <? } else { ?>
                            <option selected disabled>Create New View</option>
                            <option value="/estimates">Saved Estimates</option>
                        <? } ?>
                    </select>
                </div>
            <? } ?>
    </div>
    <? } ?>
</div>

<br style="clear: both;"><?= $this->load->view('modules/customers/customer_manager', array('tab' => isset($estimate->id) ? 1 : 0)); ?>
<br>

<div class="row">
    <div class="col-xs-12 form-inline">
        <div class="form-group" style="margin-right: 20px;">
            <label>
                Estimate Status<br>
                <?= form_dropdown('',array('Open', 'Closed'), @$estimate->closed, 'class="input-sm form-control" style="width: 180px" id="estimate_status"') ?>
            </label>
        </div>
        <div class="form-group" id="reason_for_closing_cont">
            <label>
                Reason for Closing<br>
                <?= form_input(array('id' => 'reason_for_closing', 'value' => @$estimate->reason_for_closing, 'class' => 'input-sm form-control', 'size' => 50)) ?>
            </label>
        </div>
    </div>
</div><br>

<?= $estimate_notes ?>

<?= $this->load->view('themes/default/partials/product_key'); ?><br><br>
<div class="row">
    <div class="col-xs-12">
        <table id="itemsTable" class="display table table-hover condensed" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th width="20px"><input id="checkall" type="checkbox"></th>
                    <th width="115x">Room<div class="room_req">*required</div></th>
                    <th width="115x">Location</th>
                    <th width="50px">Width</th>
                    <th width="50px">Height</th>
                    <th>Product</th>
                    <th>Product Type</th>
                    <th>Tubing</th>
                    <th width="24px"><span title="Special Geometry" class="special-geo">&#x22BE;</span></th>
                    <th>Sq. Ft</th>
                    <th>Retail</th>
                    <th>Assc. Prd</th>
                </tr>
            </thead>
            <?php if (empty($items)) { ?>
            <tbody>
                <tr>
                    <td colspan="12">
                        No estimates have been added.
                    </td>
                </tr>
            </tbody>
            <?php } ?>
        </table>
        <?= form_open('new_user_form', array('id'=>'new_user_form','class'=>'new_user_form')); ?>
        <?= form_close(); ?>
    </div>
</div>

<?= $this->load->view('themes/default/partials/multi_edit_bar', array('new' => !$estimate_id, 'module' => 'estimates')); ?>

<br style="clear: both"><br>
<div class="indow_totals row">
    <div class="col-xs-5 col-xs-offset-4">
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
<br><br>
<div class="row">
    <div class="col-xs-12">
        <?= $this->load->view('themes/default/partials/single_use_fees'); ?>
    </div>
</div>
<br><br>
<div class="row">
    <div class="col-xs-12">
        <ul class="list-inline pull-right" id="fees_discounts_button_cont">
            <li><button id="fees_discounts" class="btn btn-blue btn-sm">Fees &amp; Discounts</button></li>
            <li><?= $this->load->view('themes/default/partials/estimate_package') ?></li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <hr />
        <div class="inline">
            <input id="saveestimate" type="submit" name="submit" value="Save" class="ctrl_s btn btn-blue btn-sm pull-right" />
        </div>
        <label class="inline pull-right">
            <input id="follow_up" type="checkbox" <? if (isset($estimate->followup) && $estimate->followup == 1) echo "checked='checked'"; ?>> Follow up
        </label>
        <?php if (isset($estimate->id)) { ?>
            <a href="/estimates/delete/<?= $estimate->id ?>" class="btn btn-gray delete pull-left">Delete Estimate</a>
        <?php } else { ?>
            <a href="/estimates" class="btn btn-gray delete pull-left">Cancel</a>
        <?php } ?>
    </div>
</div>

<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js');?>"></script>
