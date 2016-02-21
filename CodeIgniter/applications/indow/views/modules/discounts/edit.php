<?php if (isset($message)): ?>
    <div class="well" id="flash_data">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if (isset($modifier->id)): ?>
    <?= form_open("/discounts/edit/" . $modifier->id, array('class' => 'edit_user_form')); ?>
<?php else: ?>
    <?= form_open("/discounts/add", array('class' => 'add_user_form')); ?>
<?php endif; ?>
<?php 
    $attributes = array(
        'class' => 'control-label'
    );    
?>

<div class="row">
    <div class="col-md-4">

        <div class="form-group">
            <?= form_label('Type', 'modifier_type',$attributes); ?>
            <?php $options = array('fee' => 'Fee', 'discount' => 'Discount', 'tax' => 'Tax', 'msrp' => 'MSRP');
                if ($this->permissionslibrary->_user->in_admin_group) {
                    $options['wholesale'] = 'Wholesale';
                }
            ?>
            <?= form_dropdown('modifier_type', $options, @$modifier->modifier_type,'class="modifier_type form-control input-sm"'); ?>
        </div>

        <div class="form-group">
            <?= form_label('Description', 'description',$attributes); ?>
            <?= form_input(array('name' => 'description', 'id' => 'description', 'class' => 'form-control input-sm', 'value' => @$modifier->description)); ?>
        </div>

        <div class="form-horizontal">
            <div class="form-group">
                <?= form_label('Amount', 'amount', array('class'=>'col-xs-12')); ?>
                <div class="col-xs-9">
                    <?= form_input(array('name' => 'amount', 'id' => 'amount', 'class' => 'form-control input-sm', 'value' => @$modifier->amount)); ?>
                </div>
                <div class="col-xs-3 amount-input">
                    <?php $options = array('percent' => '%', 'dollar' => '$'); ?>
                    <?= form_dropdown('modifier', $options, @$modifier->modifier, 'class="form-control input-sm"'); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('Code', 'code',$attributes); ?>
            <?= form_input(array('name' => 'code', 'id' => 'code', 'class' => 'form-control input-sm', 'value' => @$modifier->code)); ?>
        </div>

        <div class="row">
            <div id="startNend" class="form-group">
                <div class="col-xs-5">
                    <?= form_label('Effective', 'start_date',$attributes); ?>
                    <?= form_input(array('name' => 'start_date', 'id' => 'start_date', 'class' => 'form-control input-sm', 'disabled' => 'disabled' ,'value' => @date('m/d/Y', strtotime(if_null($modifier->start_date, date('m/d/Y')))))); ?>
                </div>
                <div class="col-xs-2 split">to</div>
                <div class="col-xs-5">
                    <?= form_label('Expires', 'end_date',$attributes); ?>
                    <?= form_input(array('name' => 'end_date', 'id' => 'end_date', 'class' => 'form-control input-sm', 'disabled' => 'disabled' ,'value' => @date('m/d/Y', strtotime(if_null($modifier->end_date, date('m/d/Y')))))); ?>
                </div>
            </div>
        </div>

        <div class="form-group" id="groupOptions">
            <?= form_label('Groups','groups_id',$attributes); ?>
            <?
                if ($this->_user->in_admin_group) {
                    $group_options_nonfubar = array(null => 'Select Group');
                } else {
                    $group_options_nonfubar = array();
                }
                foreach ($group_options as $k => $v) { //array_merge resets keys
                    $group_options_nonfubar[$k] = $v;
                }
            ?>
            <?= form_dropdown('groups_id', $group_options_nonfubar, !empty($modifier) ? $modifier->groups_id : $this->_user->group_ids[0],'class="groups_id form-control input-sm"'); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <hr />
        <div class="inline">
            <input id="saveestimate" type="submit" name="submit" value="Save" class="btn btn-blue btn-sm pull-right" />
        </div>

        <?php if (isset($modifier->id)): ?>
			<a href="/discounts/delete/<?= $modifier->id ?>" class="btn btn-gray btn-sm delete pull-left" style="margin-right: 20px;" >Delete Discount/Fee</a>
            <a href="/discounts/list" class="btn btn-gray btn-sm pull-left">Cancel</a>
        <?php endif; ?>
    </div>
</div>



 

<?=
form_close();

function if_null($value = NULL, $value2) {
    if ($value == NULL) { // Warning, this could trigger on 0 or false, or ''
        return $value2;
    } else {
        return $value;
    }
}
?>
   
<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js'); ?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js'); ?>"></script>
<script>
$(document).ready(function(){
    
    $('form').on('submit',function(event){
        var d = $('#end_date').val();
        var unix_d = Date.parse(d);
        console.log("d : " + unix_d);
        var date = new Date();
        var datestring = ("0" + (date.getMonth() + 1).toString()).substr(-2) + "/" + ("0" + date.getDate().toString()).substr(-2)  + "/" + (date.getFullYear().toString());
        var unix_datestring = Date.parse(datestring);
        console.log("datestring : " + unix_datestring);
        if($('.modifier_type').val()== "discount"){
                if(unix_d > unix_datestring){
                    return true;
                }else{
                    alert("Discount has Invalid expired date.");
                    //$('#end_date').val(datestring);
                    return false;
                }
        }else{
            return true;
        }
    });

});
</script>
