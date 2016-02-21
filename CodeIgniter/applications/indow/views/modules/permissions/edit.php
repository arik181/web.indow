<?php if (isset($message)): ?>
    <div class="well" id="flash_data">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?= form_open($path, array('id'=>'edit_perm_form'));?>

<div class="form-group form-inline">
    <?= form_label('Permission Name', 'permission_name',array('class'=>'extrawidth')); ?><br>
    <?= form_input(array('name'=>'name','id'=>'permission_name','class'=>'form-control input-sm','value'=>@$permission->name, 'required'=>'required'));?>
    <br><br>
    <div class="row">
        <div class="col-md-4">
            <h3>Assoc. Permissions</h3><br><br>
        </div>
        <div id="addNewPermissionRegion" class="col-md-8" style="margin-left: -50px;">
            <button type="button" id="addperm" class="btn btn-default">Add</button>
        </div>
    </div>
</div>

<div class="row" id="permissions_options_values_region"></div>
<div id="addNewPermissionInputsRegion" class="col-md-8" style="display: none;">
    <button type="button" id="render_inputs">render</button>
</div>
<div id="hidden_values_region" class="row" style="display: none;"></div>

<div class="info_view">
<?php if ($mode == 'edit'):?>
    <input type="hidden" value="<?=@$permission->id?>" name="permission_id">
<?php endif;?>
    <a class="btn btn-gray" href="/permissions">Cancel</a>
    <?php if ($mode == 'edit'):?>
        <a class="delete pull-left btn btn-gray" style="margin-right: 20px;" href="/permissions/delete/<?=@$permission->id ?>">Delete Permission</a>
    <?php endif;?>
	<input id="submit" type="submit" name="submit" value="Save" class="btn btn-default pull-right"/>
</div>

<?= form_close(); ?>
<?php $this->data['js_views'][] = 'modules/permissions/scripts';?>