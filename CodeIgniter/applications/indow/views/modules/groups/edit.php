<script src='/assets/js/ckeditor/ckeditor.js'></script>
<script>
    $(function () {

        <? if (isset($group->users) && count($group->users)) { ?>
            var preusers = <?= json_encode($group->users) ?>;
        <? } else { ?>
            var preusers = [];
        <? } ?>
        var usercount = 0;
        function addUserRow(user) {
            var row = $('#clonecont .userrow').clone();
            row.find('.useridcheck').val(user.id);
            row.find('.username').html(user.username);
            row.find('.fullname').html($('<a href="/users/edit/' + user.id + '"></a>').text(user.full_name));
            row.find('.edituser').data('user', user.id);
            row.find('.uactive').html(user.active);
            $('#usercont').append(row);
            if (usercount === 0) {
                $('#nousers').hide();
                $('#userheader').removeClass('hide');
            }
			row.click(function () {
			//	window.location = '/users/edit/' + user.id;
			});
            usercount++;
        }
        function addUser(name) {
            $.getJSON('/users/user_json/' + name, function(user) {
                if (!user.user_exists) {
                    alert('That user does not exist.');
                } else {
                    addUserRow(user);
                }
            });
        }
        $('#adduser').click(function () {
            addUser($('#username').val());
            $('#username').val('');
        });
        $('#username').keydown(function(e) {
            if (e.keyCode === 13) { //enter
                e.preventDefault();
                addUser($(this).val());
                $(this).val('');
            }
        });
        $('#usercont').on('click', '.deluser', function () {
            $(this).parents('tr').remove();
            usercount--;
            if (!usercount) {
                $('#nousers').show();
                $('#userheader').addClass('hide');
            }
        })
        .on('click', '.edituser', function() {
            var userid = $(this).data('user');
            window.open('/users/edit/' + userid);
        });
        $.each(preusers, function (i, user) {
            addUserRow(user);
        });
    });
</script>
<div id="clonecont">
    <table>
        <tr class="userrow">
            <!--td class="tableactions">
                <i class="sprite-icons view"></i>
                <i title="Remove from Group" class="deluser icon fa fa-times"></i>
                <input checked="checked" name="userid[]" type="checkbox" class="hide useridcheck">
            </td-->
            <td class="username"></td>
            <td class="fullname"></td>
            <td class="uactive"></td>
        </tr>
    </table>
</div>
<?php if (isset($message)): ?>
    <div class="well" id="flash_data">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<?php if (isset($group->id)): ?>
<?= form_open("/groups/edit/" . $group->id, array('class'=>'edit_group_form form-horizontal'));?>
<?php else: ?>
<?= form_open("/groups/add", array('class'=>'add_group_form form-horizontal'));?>
<?php endif; ?>
<div class="row show-grid-sm">
    <div class="col-xs-5">
        <h4>Group Details</h4><br><br>
        <div class="form-group">
            <?= form_label('Group Name', 'name', array('class'=>'col-sm-3 control-label multi-line-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'name','id'=>'name','class'=>'form-control input-sm','value'=>@$group->name));?>
            </div>
        </div>

        <? if ($this->permissionslibrary->_user->in_admin_group) { ?>
        <div class="form-group">
            <?= form_label('Permissions', 'permissions_id', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_dropdown('permissions_id',$permoptions,@$group->permissions_id, "class='form-control input-sm'");?>
            </div>
        </div>
        <? } ?>
    
        <h4 class="show-grid-sm">Account Info</h4>
        <h6 class="boldh">Address 1</h5>
        <div class="form-group">
            <? $aoptions = array('Home'=>'Home', 'Work'=>'Work', 'Billing'=>'Billing', 'Shipping'=>'Shipping');?>
            <?= form_label('Address Type', 'address_1_type', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_dropdown('address_1_type',$aoptions,@$group->address_1_type, "class='form-control input-sm'");?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Address', 'address_1', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'address_1','id'=>'address_1','class'=>'form-control input-sm','value'=>@$group->address_1));?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Address Ext', 'address_1_ext', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'address_1_ext','id'=>'address_1_ext','class'=>'form-control input-sm','value'=>@$group->address_1_ext));?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('City', 'city_1', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'city_1','id'=>'city_1','class'=>'form-control input-sm','value'=>@$group->city_1));?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('State', 'state_1', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= state_select('state_1', @$group->state_1, true, array('class' => 'form-control input-sm')) ?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Zipcode', 'zipcode_1', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'zipcode_1','id'=>'zipcode_1','class'=>'form-control input-sm','value'=>@$group->zipcode_1));?>
            </div>
        </div>
        <div class="form-group">
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Email', 'email_1', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-6">
                <?= form_input(array('name'=>'email_1','id'=>'email_1','class'=>'form-control input-sm','value'=>@$group->email_1));?>
            </div>
            <div class="col-xs-3">
                <? echo form_dropdown('email_type_1',$options,@$group->email_type_1, "class='form-control input-sm'");?>
            </div>
        </div>
        <div class="form-group">
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Phone', 'phone', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-6">
                <?= form_input(array('name'=>'phone_1','id'=>'phone_1','class'=>'form-control input-sm','value'=>@$group->phone_1));?>
            </div>
            <div class="col-xs-3">
                <?= form_dropdown('phone_type_2',$options,@$group->phone_type_2, "class='form-control input-sm'");?>
            </div>
        </div>

        <h6 class="boldh">Address 2</h6>
        <div class="form-group">
            <?= form_label('Address Type', 'address_2_type', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_dropdown('address_2_type',$aoptions,@$group->address_2_type, "class='form-control input-sm'");?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Address', 'address_2', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'address_2','id'=>'address_2','class'=>'form-control input-sm','value'=>@$group->address_2));?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Address Ext', 'address_2_ext', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'address_2_ext','id'=>'address_2_ext','class'=>'form-control input-sm','value'=>@$group->address_2_ext));?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('City', 'city_2', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'city_2','id'=>'city_2','class'=>'form-control input-sm','value'=>@$group->city_2));?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('State', 'state_2', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= state_select('state_2', @$group->state_2, true, array('class' => 'form-control input-sm')) ?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Zipcode', 'zipcode_2', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'zipcode_2','id'=>'zipcode_2','class'=>'form-control input-sm','value'=>@$group->zipcode_2));?>
            </div>
        </div>
        <div class="form-group">
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Email', 'email_2', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-6">
                <?= form_input(array('name'=>'email_2','id'=>'email_2','class'=>'form-control input-sm','value'=>@$group->email_2));?>
            </div>
            <div class="col-xs-3">
                <?= form_dropdown('email_type_2',$options,@$group->email_type_2, "class='form-control input-sm'");?>
            </div>
        </div>
        <div class="form-group">
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Phone', 'phone', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-6">
                <?= form_input(array('name'=>'phone_2','id'=>'phone_2','class'=>'form-control input-sm','value'=>@$group->phone_2));?>
            </div>
            <div class="col-xs-3">
                <?= form_dropdown('phone_type_2',$options,@$group->phone_type_2, "class='form-control input-sm'");?>
            </div>
        </div>

        <h6 class="boldh">Address 3</h5>
        <div class="form-group">
            <? $aoptions = array('Home'=>'Home', 'Work'=>'Work', 'Billing'=>'Billing', 'Shipping'=>'Shipping');?>
            <?= form_label('Address Type', 'address_3_type', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_dropdown('address_3_type',$aoptions,@$group->address_3_type, "class='form-control input-sm'");?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Address', 'address_3', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'address_3','id'=>'address_1','class'=>'form-control input-sm','value'=>@$group->address_3));?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Address Ext', 'address_3_ext', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'address_3_ext','id'=>'address_3_ext','class'=>'form-control input-sm','value'=>@$group->address_3_ext));?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('City', 'city_3', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'city_3','id'=>'city_3','class'=>'form-control input-sm','value'=>@$group->city_3));?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('State', 'state_3', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= state_select('state_3', @$group->state_3, true, array('class' => 'form-control input-sm')) ?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Zipcode', 'zipcode_3', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'zipcode_3','id'=>'zipcode_1','class'=>'form-control input-sm','value'=>@$group->zipcode_3));?>
            </div>
        </div>
        <div class="form-group">
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Email', 'email_3', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-6">
                <?= form_input(array('name'=>'email_3','id'=>'email_3','class'=>'form-control input-sm','value'=>@$group->email_3));?>
            </div>
            <div class="col-xs-3">
                <? echo form_dropdown('email_type_3',$options,@$group->email_type_3, "class='form-control input-sm'");?>
            </div>
        </div>
        <div class="form-group">
            <? $options = array('0' => 'Home','1' => 'Work'); ?>
            <?= form_label('Phone', 'phone', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-6">
                <?= form_input(array('name'=>'phone_3','id'=>'phone_3','class'=>'form-control input-sm','value'=>@$group->phone_3));?>
            </div>
            <div class="col-xs-3">
                <?= form_dropdown('phone_type_3',$options,@$group->phone_type_3, "class='form-control input-sm'");?>
            </div>
        </div>
    </div>
    <div class="col-xs-offset-2 col-xs-5">
        <? if ($this->_user->in_admin_group) { ?>
        <div class="form-group" style="margin-top: 53px;">
            <?= form_label('Parent Group', 'parent_group_id', array('class'=>'col-sm-3 control-label multi-line-label')); ?>
            <div class="col-xs-9">
                <?= form_dropdown('parent_group_id',$groups,@$group->parent_group_id, "class='form-control input-sm'");?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Indow Rep', 'rep_id', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_dropdown('rep_id',$users,@$group->rep_id, "class='form-control input-sm'");?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Signed Agreement Name', 'signed_agreement_name', array('class'=>'col-sm-4 control-label multi-line-label')); ?>
            <div class="col-xs-8">
                <?= form_input(array('name'=>'signed_agreement_name','id'=>'signed_agreement_name','class'=>'form-control input-sm','value'=>@$group->signed_agreement_name));?>
            </div>
        </div>
        <? if (!empty($group)) { ?>
            <div class="form-group">
            <?= form_label('Wholesale Discount', '', array('class'=>'col-sm-3 control-label multi-line-label')); ?>
            <div style="margin-top: 6px;" class="col-xs-9">
                <? if ($discount) { ?>
                    <a href="/discounts/edit/<?= $discount->id ?>"><?= $discount->modifier == 'dollar' ? '$' : '' ?><?= $discount->amount ?><?= $discount->modifier == 'percent' ? '%' : '' ?></a>
                <? } else { ?>
                    N/A
                <? } ?>
            </div>
        </div>
        <? } ?>

        <h4 class="show-grid-sm">Credit Info</h4>
        <div class="form-group">
            <?= form_label('Credit Line', 'credit_line', array('class'=>'col-sm-3 control-label')); ?>
            <div class="col-xs-9">
                <?= form_input(array('name'=>'credit','id'=>'credit','class'=>'form-control input-sm','value'=>@$group->credit));?>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Credit Hold', 'credit_hold', array('class'=>'col-sm-3')); ?>
            <div class="col-xs-9">
                <input type="checkbox" value="1" name="credit_hold" id="credit_hold" <?= isset($group->credit_hold) && $group->credit_hold ? 'checked="checked"' : '' ?>>
            </div>
        </div>
        <div class="form-group">
            <?= form_label('Assoc. Users', 'credit_line', array('class'=>'col-sm-3 no-wrap control-label')); ?>
            <!--div class="col-xs-9 form-inline text-right">
                <div class="form-group">
                    <?= form_input(array('name'=>'username','id'=>'username','class'=>'form-control input-sm stinput'));?>
                </div>
                <div class="form-group">
                    <button id="adduser" class="btn btn-default btn-sm" type="button">Add User</button>
                </div>
            </div-->
        </div>

        <div class="form-group">
            <div class="col-sm-12">
                <table id="usercont" class="stcont display table table-hover dataTable no-footer">
                    <tr class="hide" id="userheader">
                        <!--th>Actions</th-->
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Active</th>
                    </tr>
                    <tr id="nousers"><td colspan="3">No Associated Users</td></tr>
                </table>
            </div>
        </div>
        <? } ?>

        <? if (!empty($group->subGroups)) { ?>
            <div class="form-group">
                <h4 class="form-group-sm-head col-xs-12">Subgroups</h4>
            </div>
            <table class="display dataTable table table-hover no-footer">
                <? foreach($group->subGroups as $i => $sg) { ?>
                    <tr class="<?= $i % 2 ? 'odd' : 'even' ?>"><td><?= $sg->name ?></td></tr>
                <? } ?>
            </table>
            <br style="clear: both"><br><br>
        <? } ?>
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
                        $ajaxEndPoint   = "/groups/files_json/" . $group->id,
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
        } ?><br><br><br>
        <label for="estimate_package_html">Estimate Package Html</label>
        <textarea id="estimate_package_html" name="estimate_package_html" class="ckeditor form-control input-sm"><?= @$group->estimate_package_html ?></textarea>
    </div>
</div>
<hr>
    <div class="row actions" class="form-group">
        <? if (isset($group->id) && $this->_user->in_admin_group): ?>
            <a href="/groups/delete/<?= $group->id ?>" class="btn btn-gray pull-left btn-content delete">Delete Group</a>
        <? endif; ?>
        <? if ($this->permissionslibrary->has_edit_permission(4)) { ?>
			<input type="submit" name="submit" value="Save" class="btn btn-default pull-right btn-content inline"/>
        <? } ?>
    </div>
<?= form_close(); ?>
