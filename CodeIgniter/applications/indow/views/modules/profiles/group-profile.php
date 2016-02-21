<?php if (isset($message)): ?>
    <div class="well" id="flash_data">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<div class="row show-grid-sm">

    <?= form_open($path, array('id' => 'user_form', 'class'=>'edit_user_form'));?>

    <div class="col-xs-6 form-group stcont">
        <h4>Group Details</h4>
        <?= form_label('Group Name', 'name'); ?>
        <?= form_input(array('name'=>'name','id'=>'name','class'=>'large_input','value'=>@$group->name));?>
    </div>
    <div class="col-xs-5 form-group stcont">
    <? if (isset($group->id)) { ?>
        <h4>Uploaded Files</h4>
        <table id="filesTable" class="display dataTable table table-hover" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Actions</th>
                <th>File Name</th>
                <th>Type</th>
            </tr>
            </thead>
        </table>
        <a class="btn btn-sm btn-blue" href="/groups/upload_file/<?= $group->id ?>">Upload File</a>
        <?   initializeDataTable($selector       = "#filesTable",
            $ajaxEndPoint   = "/groupprofiles/files_json/" . $group->id,
            $columns        = array('id', 'filename', 'type'),
            $primaryKey     = "id",
            $actionButtons  = array(
                array('class' => 'icon',
                    'title' => 'View',
                    'href'  => '',
                    'innerHtml'  => '<i class="sprite-icons view"></i>'
                ),
                array('class' => 'icon',
                    'title' => 'Remove',
                    'href'  => '/groups/delete_file/' . $group->id . '/',
                    'innerHtml'  => '<i class="icon fa fa-times"></i>'
                )
            ),
            $actionColumn   = 0,
            $emptyString    = "There are no files available.",
            $rowCreationJS = "
                            console.log(row);
                            $('a[title=\"View\"]', row).attr('href', '/uploads/" . $group->id . "/' + encodeURIComponent(data.filename)).attr('target', '_blank');
                        ",
            '"p"'
        );
    } ?>
</div>
<div class="row show-grid-sm">
    <div class="col-xs-6 form-group stcont">

    <h4 class="show-grid-sm">Account Info</h4>
        <h6 class="boldh">Address 1</h6>
            <? $aoptions = array('Home'=>'Home', 'Work'=>'Work', 'Billing'=>'Billing', 'Shipping'=>'Shipping');?>
            <?= form_label('Address Type', 'address_1_type'); ?>
            <?= form_dropdown('address_1_type',$aoptions,@$group->address_1_type, "class='large_input'");?>
            <br>
            <?= form_label('Address', 'address_1'); ?>
            <?= form_input(array('name'=>'address_1','id'=>'address_1','class'=>'large_input','value'=>@$group->address_1));?>
            <br>
            <?= form_label('Address Ext', 'address_1_ext'); ?>
            <?= form_input(array('name'=>'address_1_ext','id'=>'address_1_ext','class'=>'large_input','value'=>@$group->address_1_ext));?>
            <br>
            <?= form_label('City', 'city_1'); ?>
            <?= form_input(array('name'=>'city_1','id'=>'city_1','class'=>'large_input','value'=>@$group->city_1));?>
            <br>
            <?= form_label('State', 'state_1'); ?>
            <select name="state_1" id="state_1" class="large_input">
                <?php $states = getStatesArray();?>
                <?php foreach($states as $state):?>
                    <?php if($state == $group->state_1): ?>
                        <option value="<?=$state?>" selected><?=$state?></option>
                    <?php else:?>
                        <option value="<?=$state?>"><?=$state?></option>
                    <?php endif; ?>
                <?php endforeach;?>
            </select>
            <br>
            <?= form_label('Zipcode', 'zipcode_1'); ?>
            <?= form_input(array('name'=>'zipcode_1','id'=>'zipcode_1','class'=>'large_input','value'=>@$group->zipcode_1));?>
            <br>
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Email', 'email_1'); ?>
            <?= form_input(array('name'=>'email_1','id'=>'email_1','class'=>'small_input','value'=>@$group->email_1));?>
            <? echo form_dropdown('email_type_1',$options,@$group->email_type_1, "class='type_select'");?>
            <br>
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Phone', 'phone'); ?>
            <?= form_input(array('name'=>'phone_1','id'=>'phone_1','class'=>'small_input','value'=>@$group->phone_1));?>
            <?= form_dropdown('phone_type_2',$options,@$group->phone_type_2, "class='type_select'");?>
            <br><br>
     </div>
    <div class="col-xs-5 form-group stcont">

        <h4 class="show-grid-sm"></h4>
        <br>

        <h6 class="boldh">Address 2</h6>
            <?= form_label('Address Type', 'address_2_type'); ?>
            <?= form_dropdown('address_2_type',$aoptions,@$group->address_2_type, "class='large_input'");?>
            <br>
            <?= form_label('Address', 'address_2'); ?>
            <?= form_input(array('name'=>'address_2','id'=>'address_2','class'=>'large_input','value'=>@$group->address_2));?>
            <br>
            <?= form_label('Address Ext', 'address_2_ext'); ?>
            <?= form_input(array('name'=>'address_2_ext','id'=>'address_2_ext','class'=>'large_input','value'=>@$group->address_2_ext));?>
            <br>
            <?= form_label('City', 'city_2'); ?>
            <?= form_input(array('name'=>'city_2','id'=>'city_2','class'=>'large_input','value'=>@$group->city_2));?>
            <br>
            <?= form_label('State', 'state_2'); ?>
            <select name="state_2" id="state_2" class="large_input">
                <?php $states = getStatesArray();?>
                <?php foreach($states as $state):?>
                    <?php if($state == $group->state_2): ?>
                        <option value="<?=$state?>" selected><?=$state?></option>
                    <?php else:?>
                        <option value="<?=$state?>"><?=$state?></option>
                    <?php endif; ?>
                <?php endforeach;?>
            </select>
            <br>
            <?= form_label('Zipcode', 'zipcode_2'); ?>
            <?= form_input(array('name'=>'zipcode_2','id'=>'zipcode_2','class'=>'large_input','value'=>@$group->zipcode_2));?>
            <br>
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Email', 'email_2'); ?>
            <?= form_input(array('name'=>'email_2','id'=>'email_2','class'=>'small_input','value'=>@$group->email_2));?>
            <? echo form_dropdown('email_type_2',$options,@$group->email_type_2, "class='type_select'");?>
            <br>
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Phone', 'phone'); ?>
            <?= form_input(array('name'=>'phone_2','id'=>'phone_2','class'=>'small_input','value'=>@$group->phone_2));?>
            <?= form_dropdown('phone_type_2',$options,@$group->phone_type_2, "class='type_select'");?>
        <br><br>

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
</div>