<?
if (!isset($new)) {
    $new = false;
}
if (!isset($module)) {
    $module = null;
}
$selected_options = array('' => 'Select');
if (!$new && $module === 'estimates') {
    $selected_options['createnew'] = 'New Estimate View';
}
if ($module === 'estimates') {
    $selected_options['duplicate'] = 'Duplicate';
}
$selected_options['delete'] = 'Delete';
if (!$new && $module === 'jobsites') {
    if ($this->permissionslibrary->has_edit_permission(3)) {
        $selected_options['createquote'] = 'Create Quote';
    }
    if (!empty($order_access)) {
        $selected_options['createorder'] = 'Create Order';
    }
    if ($this->permissionslibrary->_user->in_admin_group) {
        $selected_options['addorder'] = 'Add to Existing Order';
    }
}
?>

<div class="col-md-12">
    <div class="row" id="multi_edit_bar">
        <? if ($module !== 'jobsites') { ?>
        <div class="col-md-2">
                <input id="addnewitem" type="button" name="submit" value="Add Window" style="margin-top: 8px;" class="nomargin btn btn-blue"/>
        </div>
        <? } ?>
        <div class="<?= $module !== 'jobsites' ? 'col-md-4' : 'col-md-6' ?> form-inline" <?= $module !== 'jobsites' ? 'style="padding-left: 45px"' : '' ?>>
            With Selected<br>
            <?= form_dropdown('', $selected_options, '', 'id="withselected_option" class="form-control input-sm"') ?>
            <button id="withselected_apply" class="btn btn-sm btn-black btn-default">Apply</button>
        </div>
        <div class="col-md-6">
            <div class="form-inline pull-right">
                Mass Editing<br>
                <? $this->load->view('themes/default/partials/mass_edit'); ?>
            </div>
        </div>
    </div>
</div>
