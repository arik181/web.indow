
<script>


    $(function () {
        var permcount = 0;
        var groupcount = 0;
        <? if (isset($pre_perms)) { ?>
            var preperms = <?= json_encode($pre_perms) ?>;
        <? } else { ?>
            var preperms = [];
        <? } ?>
        <? if (isset($pre_groups)) { ?>
            var pregroups = <?= json_encode($pre_groups) ?>;
        <? } else { ?>
            var pregroups = [];
        <? } ?>

        indow.preperms = preperms;

        function addperm() {

            var selected_feature = $("select#permoption option:selected").text();
            var selected_feature_id = $("select#permoption option:selected").val();

            var selected_permval= $("select#permval option:selected").text();
            var selected_permval_id = $("select#permval option:selected").val();

            var duplicate_check = _.where(indow.preperms , {feature_id:selected_feature_id, permission_level_id:selected_permval_id});

            if(duplicate_check.length == 0){

                $('#permcont').append('<tr class="permrow">' +
                    '<td><i class="deleteperm icon fa fa-times"></i></td>' +
                    '<td><input type="hidden" class="invis toolname" value="' + selected_feature_id + '"readonly="readonly">' + selected_feature + '</td>' +
                    '<td><input type="hidden" class="invis permlevel" value="' + selected_permval_id + '"readonly="readonly">' + selected_permval + '</td>' + '</tr>');

                indow.preperms.push({
                    feature_id:selected_feature_id,
                    feature_name:selected_feature,
                    permission_level_id:selected_permval_id,
                    level_name:selected_permval
                });

                if (permcount === 0) {
                    $('#noperm').hide();
                    $('#permheader').removeClass('hide').show();
                }

                permcount++;

            }
            else
            {
                alert('Permission already assigned for this user.')
            }



        }

        $('#addperm').click(function () {
            addperm();
        });


        function addExistingPerm() {
            var features = <?= json_encode($perm_options_flat) ?>;

            var levels = indow.users.permissionLevels;

            var prePermissions = [];

            $.each(indow.preperms, function(index,perm) {


                _.each(features,function(feature,key,value){
                    if(key == perm.feature_id){
                        perm.feature_name = feature;
                    }

                });

                _.each(levels,function(level,key,value){
                    if(level.id == perm.permission_level_id){
                        perm.level_name = level.name;
                    }
                });

                prePermissions.push(perm);

            });

            $.each(prePermissions, function(index,perm) {
                $('#permcont').append('<tr class="permrow">' +
                    '<td><i class="deleteperm icon fa fa-times"></i></td>' +
                    '<td><input type="hidden" class="invis toolname" value="' + perm.feature_id + '"readonly="readonly">' + perm.feature_name  + '</td>' +
                    '<td><input type="hidden" class="invis permlevel" value="' + perm.permission_level_id + '"readonly="readonly">' + perm.level_name +  '</td>' + '</tr>'
                );
                if (prePermissions.length) {
                    $('#noperm').hide();
                }
            });

        }

        addExistingPerm();

        $.each(indow.users.permissionLevels,function(index, level){
            $('select#permval').append('<option value="' + level.id +'">' + level.name + '</option>');
        });

        $('#groupname').keydown(function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                getgroup();
            }
        });


        $('#permcont').on('click', '.deleteperm', function () {
            $(this).parents('tr').remove();
            permcount--;
            if (permcount === 0) {
                $('#noperm').show();
                $('#permheader').hide();
            }
        });


        function getgroup() {
            var name = $('#groupname').val();
            $('#groupname').val('');
            $.getJSON('/groups/simple_group/' + encodeURIComponent(name), function (group) {
                if (!group.exists) {
                    alert('That group does not exist.');
                } else {
                    addgroup(group);
                }
            });
        }

        function addgroup(group, auto) {

            var groups = $('input.groupid');
            var group_ids = [];
            $.each(groups, function(index,value) {
                var group_id = $(value).val();
                if(group_id != ""){
                    group_ids.push(group_id);
                }
            });

            if ($.inArray(group.id,group_ids) === -1) {
                if (group_ids.length > 0 && !auto) {
                    alert('Users may only be assigned to one group. Please remove them from that group before adding another.');
                    return;
                }
                var row = $('#clonecont .grouprow').clone();
                row.find('.groupid').val(group.id);
                row.find('.groupname').text(group.name);
                if (group.permname) {
                    row.find('.permissionname').text(group.permname);
                }
                $('#groupcont').append(row);
                if (groupcount === 0) {
                    $('#nogroup').hide();
                    $('#groupheader').removeClass('hide').show();
                }
                groupcount++;
            }
            else
            {
                alert('The user is already assigned to this group.')
            }

        }

        $('#addgroup').click(getgroup);

        $('#groupcont').on('click', '.delgroup', function () {
            $(this).parents('tr').remove();
            groupcount--;
            if (groupcount === 0) {
                $('#nogroup').show();
                $('#groupheader').hide();
            }
        });

        $('#user_form').submit(function (e) {

            $('.toolname').each(function (i, e) {
                $(this).attr('name', 'toolname_' + i);
            });

            $('.permlevel').each(function (i, e) {
                $(this).attr('name', 'permlevel_' + i);
            });

            $('.groupid').each(function (i, e) {
                $(this).attr('name', 'groupid[]');
            });

        });

        // add users pre existing groups



        $.each(pregroups, function(i, group) {
            addgroup(group, true);
        });

    });

</script>
<script>
    <? if (isset($start_customers)) { ?>
    var customer_manager_customers = <?= json_encode($start_customers) ?>;
    <? } else { ?>
    var customer_manager_customers = [];
    <? } ?>
</script>
<script src="/assets/theme/default/js/users_manager.js"></script>
<div id="clonecont">
    <table>
        <tr class="permrow">
            <td><i class="deleteperm icon fa fa-times"></i></td>
            <td><input readonly="readonly" class="invis toolname"></td>
            <td><input readonly="readonly" class="invis permlevel"></td>
        </tr>
        <tr class="grouprow">
            <td><i class="delgroup icon fa fa-times"></i><input type="hidden" class="groupid"></td>
            <td class="groupname"></td>
            <td class="permissionname"></td>
        </tr>
    </table>
</div>

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
        <? if ($_user->in_admin_group) { ?>
            <div class="form-group form-group-sm">
                <div class="col-xs-12 text-right">
                    <?= form_label('Disable Account', 'disabled', array('class' => 'control-label')); ?> &nbsp;
                    <?= form_checkbox('disabled','1', @$user->disabled);?>
                </div>
            </div>

            <div class="form-group">
                <?= form_label('Username', 'username', array('class' => 'col-xs-3 control-label')); ?>
                <div class="col-xs-9">
                    <?= form_input('username',@$user->username,'class="form-control input-sm"');?>
                </div>
            </div>
        <? } else { ?>
            <div style="height: 45px;"></div>
        <? } ?>

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
<? if ($_user->in_admin_group) { ?>
    <div class="row show-grid">

        <div class="col-md-6">
            <div class="row show-grid-sm">
                <h4 class="col-xs-3 form-group-sm-head no-wrap">Groups</h4>
                <div class="col-xs-9 form-inline text-right">
                    <div class="form-group">
                        <table class="display table dataTable no-footer" id="group_search_results" style="z-index: 9000"></table>
                        <input id="groupname" class="form-control input-sm" size="30" placeholder="Search group" />
                    </div>
                    <div class="form-group">
                        <button id="addgroup" class="btn btn-default btn-sm" type="button">Add</button>
                    </div>
                </div>
            </div>

            <table id="groupcont" class="display table table-hover dataTable no-footer">
                <thead>
                    <tr class="hide" id="groupheader">
                        <th>Actions</th>
                        <th>Group Name</th>
                        <th>Permission Name</th>
                    </tr>
                </thead>
                <tr id="nogroup"><td colspan="3">No Associated Groups</td></tr>
            </table>
        </div>

        <div class="col-md-6">
            <div class="row show-grid-sm">
                <h4 class="col-xs-3 form-group-sm-head no-wrap">Permissions</h4>
                <div class="col-xs-9 form-inline text-right">
                    <div class="form-group">
                        <select id="permoption" class="input-sm form-control">
                            <? foreach ($perm_options as $option) { ?>
                                <option value="<?= $option->id ?>"><?= $option->feature_display_name ?></option>
                            <? } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <select style="width: 165px;" id="permval" class="form-control input-sm"></select>
                    </div>
                    <div class="form-group">
                        <button id="addperm" class="btn btn-default btn-sm" type="button">Add</button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table id="permcont" class="display table table-hover dataTable no-footer">
                        <thead>
                            <tr class="hide" id="permheader">
                                <th>Actions</th>
                                <th>Tool Name</th>
                                <th>Permission Level</th>
                            </tr>
                        </thead>
                        <tr id="noperm"><td colspan="3">No Associated Permissions</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-6 col-xs-offset-6">
            <div class="form-group">
                <?= form_label('Permission Set', 'permission_set', array('class' => 'col-xs-4 control-label')); ?>
                <div class="col-xs-8">
                <?= form_dropdown('permission_set', $perm_set_options, @$user->permission_set, "id='permission_set' class='input-sm form-control'") ?>
                </div>
            </div>
        </div>
<? } ?>

<div class="row">
    <div class="col-xs-12">
        <hr />
        <div class="inline">
            <input type="submit" name="submit" value="Save" class="btn btn-default pull-right"/>
            <?php if (isset($user->id) && $_user->in_admin_group): ?>
                <a href="/users/delete/<?= $user->id ?>" style="margin-right: 20px;" class="btn delete btn-gray btn-content btn-sm pull-left">Delete User</a>
                <a href="/users/list" class="btn btn-gray btn-sm">Cancel</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= form_close(); ?>

<?php $this->data['js_views'][] = 'modules/users/scripts';?>
