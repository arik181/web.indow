<?php if (isset($message)): ?>
    <div class="well" id="flash_data">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?= form_open($path, array('id' => 'user_form', 'class'=>'edit_user_form'));?>

<div class="row form-horizontal show-grid">
    <div class="col-xs-5">
        <div class="form-group form-group-sm">
            <h4 class="col-xs-12 form-group-sm-head">User Info</h4>
        </div>

        <div class="form-group">
            <?= form_label('First Name', 'first_name', array('class' => 'col-xs-3 control-label no-wrap')); ?>
            <div class="col-xs-9">
                <?= form_input('first_name',@$user->first_name,'class="form-control input-sm"');?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('Last Name', 'last_name', array('class' => 'col-xs-3 control-label no-wrap')); ?>
            <div class="col-xs-9">
                <?= form_input('last_name',@$user->last_name,'class="form-control input-sm"');?>
            </div>
        </div>
    </div>

    <div class="col-xs-offset-2 col-xs-5">

        <div class="form-group form-group-sm"></div>

        <div class="form-group">
            <?= form_label('Password', 'password', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_password('password','','class="form-control input-sm"');?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('Confirm Password', 'confirm_password', array('class' => 'col-xs-3 control-label multi-line-label')); ?>
            <div class="col-xs-9">
                <?= form_password('confirm_password','','class="form-control input-sm"');?>
            </div>
        </div>

    </div>

</div>

<div class="row form-horizontal show-grid">
    <div class="col-md-5">
        <div class="form-group form-group-sm">
            <h4 class="col-xs-12 form-group-sm-head">Address 1</h4>
        </div>

        <div class="form-group">
            <?= form_label('Address', 'address_1', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'address_1','id'=>'address_1','class'=>'form-control input-sm','value'=>@$user->address_1));?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('Address Ext', 'address_1_ext', array('class' => 'col-xs-3 control-label multi-line-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'address_1_ext','id'=>'address_1_ext','class'=>'form-control input-sm','value'=>@$user->address_1_ext));?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('City', 'city_1', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'city_1','id'=>'city_1','class'=>'form-control input-sm','value'=>@$user->city_1));?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('State', 'state_1', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?= state_select('state_1', @$user->state_1, true, array('class' => 'form-control input-sm')) ?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('Zipcode', 'zipcode_1', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'zipcode_1','id'=>'zipcode_1','class'=>'form-control input-sm','value'=>@$user->zipcode_1));?>
            </div>
        </div>

        <div class="form-group">
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Email', 'email_1', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-6">
                <?= form_input(array('name'=>'email_1','id'=>'email_1','class'=>'form-control input-sm','value'=>@$user->email_1));?>
            </div>
            <div class="col-xs-3">
                <?= form_dropdown('email_type_1',$options,@$user->email_type_1, 'class="form-control input-sm"');?>
            </div>
        </div>

        <div class="form-group">
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Phone', 'phone', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-6">
                <?= form_input(array('name'=>'phone_1','id'=>'phone_1','class'=>'form-control input-sm','value'=>@$user->phone_1));?>
            </div>
            <div class="col-xs-3">
                <?= form_dropdown('phone_type_1',$options,@$user->phone_type_1,'class="form-control input-sm"');?>
            </div>
        </div>
    </div>

    <div class="col-xs-offset-2 col-xs-5">
        <div class="form-group form-group-sm">
            <h4 class="col-xs-12 form-group-sm-head">Address 2</h4>
        </div>

        <div class="form-group">
            <?= form_label('Address', 'address_2', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'address_2','id'=>'address_2','class'=>'form-control input-sm','value'=>@$user->address_2));?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('Address Ext', 'address_2_ext', array('class' => 'col-xs-3 control-label multi-line-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'address_2_ext','id'=>'address_2_ext','class'=>'form-control input-sm','value'=>@$user->address_2_ext));?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('City', 'city_2', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'city_2','id'=>'city_2','class'=>'form-control input-sm','value'=>@$user->city_2));?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('State', 'state_2', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?= state_select('state_2', @$user->state_2, true, array('class' => 'form-control input-sm')) ?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('Zipcode', 'zipcode_2', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'zipcode_2','id'=>'zipcode_2','class'=>'form-control input-sm','value'=>@$user->zipcode_2));?>
            </div>
        </div>

        <div class="form-group">
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Email', 'email_2', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-6">
                <?= form_input(array('name'=>'email_2','id'=>'email_2','class'=>'form-control input-sm','value'=>@$user->email_2));?>
            </div>
            <div class="col-xs-3">
                <? echo form_dropdown('email_type_2',$options,@$user->email_type_2, 'class="form-control input-sm"');?>
            </div>
        </div>

        <div class="form-group">
            <?= form_label('Phone', 'phone_2', array('class' => 'col-xs-3 control-label')); ?>
            <div class="col-xs-6">
                <?= form_input(array('name'=>'phone_2','id'=>'phone_2','class'=>'form-control input-sm','value'=>@$user->phone_2));?>
            </div>
            <div class="col-xs-3">
                <? $options = array('0' => 'Home','1' => 'Work'); ?>
                <?= form_dropdown('phone_type_2',$options,@$user->phone_type_2, 'class="form-control input-sm"');?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <hr />
        <div class="inline">
            <input type="submit" name="submit" value="Save" class="btn btn-default btn-sm"/>
            <a href="/" class="btn btn-default btn-sm">Cancel</a>
        </div>
    </div>
</div>

<?= form_close(); ?>