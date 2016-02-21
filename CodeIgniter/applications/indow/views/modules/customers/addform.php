
<script>
    $(function() {
        $('#othercheck').click(function() {
            if ($(this).prop('checked')) {
                $('#otheraddress').show();
            } else {
                $('#otheraddress').hide();
            }
        });
        if ($('#othercheck').prop('checked')) {
            $('#otheraddress').show();
        } else {
            $('#otheraddress').hide();
        }
    });
</script>
<?
    $phone_options = array('Home', 'Work', 'Mobile');
    $email_options = array('Home', 'Work');
?>
<form id="newcustform" method="post">
    <div class="row show-grid-md">
        <div class="col-xs-6 form-horizontal">
            <h3>Contact Info</h3>
            <div class="form-group">
                <label for="first" class="col-xs-3 control-label">First Name</label>
                <div class="col-xs-9">
                    <?= form_input('first_name', @$customer['first_name'], 'class="form-control input-sm"'); ?>
                </div>
            </div>
            <div class="form-group">
                <label for="last" class="col-xs-3 control-label">Last Name</label>
                <div class="col-xs-9">
                    <?= form_input('last_name', @$customer['last_name'], 'class="form-control input-sm"'); ?>
                </div>
            </div>
            <div class="form-group">
                <label for="company" class="col-xs-3 control-label">Company</label>
                <div class="col-xs-9">
                    <?= form_input('organization_name', @$customer['organization_name'], 'class="form-control input-sm"'); ?>
                </div>
            </div>
            <div class='form-group'>
                <label for='email_1' class='control-label col-xs-3'>Email</label>
                <div class='col-xs-6'>
                    <input type='email' class='form-control input-sm' id='email_1' name='email_1'  value='<?= @$customer['email_1'] ?>'>
                </div>
                <div class='col-xs-3 text-right'>
                    <?= form_dropdown('email_type_1',$email_options,'', "class='form-control input-sm'");?>
                </div>
            </div>
            <div class='form-group'>
                <label for='email_2' class='control-label col-xs-3'>Email</label>
                <div class='col-xs-6'>
                    <input type='email' class='form-control input-sm' id='email_2' name='email_2'  value='<?= @$customer['email_2'] ?>'>
                </div>
                <div class='col-xs-3 text-right'>
                    <?= form_dropdown('email_type_2',$email_options,'', "class='form-control input-sm'");?>
                </div>
            </div>
            <div class='form-group'>
                <label for='phone1' class='control-label col-xs-3'>Phone</label>
                <div class='col-xs-6'>
                    <input class='form-control input-sm' id='phone1' name='phone_1'  value='<?= @$customer['phone_1'] ?>'>
                </div>
                <div class='col-xs-3 text-right'>
                    <?= form_dropdown('phone_type_1',$phone_options,'', "class='form-control input-sm'");?>
                </div>
            </div>
            <div class='form-group'>
                <label for='phone2' class='control-label col-xs-3'>Phone</label>
                <div class='col-xs-6'>
                    <input class='form-control input-sm' id='phone2' name='phone_2'  value='<?= @$customer['phone_2'] ?>'>
                </div>
                <div class='col-xs-3 text-right'>
                    <?= form_dropdown('phone_type_2',$phone_options,'', "class='form-control input-sm'");?>
                </div>
            </div>
            <div class='form-group'>
                <label for='phone3' class='control-label col-xs-3'>Phone</label>
                <div class='col-xs-6'>
                    <input class='form-control input-sm' id='phone3' name='phone_3'  value='<?= @$customer['phone_3'] ?>'>
                </div>
                <div class='col-xs-3 text-right'>
                    <?= form_dropdown('phone_type_3',$phone_options,'', "class='form-control input-sm'");?>
                </div>
            </div>

            <div class='form-group'>
                <label for='company_id' class='control-label col-xs-4'>Company/Group</label>
                <div class='col-xs-8'>
                    <?
                    $selected_group = count($user->groups) ? $user->groups[0]->group_id : '';
                    $selected_group = empty($customer['company_id']) ? $selected_group : $customer['company_id'];
                    echo form_dropdown('company_id',$groups, $selected_group, 'class="form-control input-sm"');
                    ?>
                </div>
            </div>
            <div class='form-group'>
                <label for='rep' class='control-label col-xs-4'>Assoc. Rep.</label>
                <div class='col-xs-8'>
                    <?= form_dropdown('customer_preferred_contact',$users,empty($customer['customer_preferred_contact']) ? $user->id : $customer['customer_preferred_contact'], 'class="form-control input-sm"'); ?>
                </div>
            </div>
        </div>
        <div class="col-xs-6 form-horizontal">
            <h4>Address</h4>
            <div class="form-group">
                <label style="position: relative; top: 20px;" class="col-xs-3">Address Type</label>
                <div class="col-xs-9 form-inline">
                    <div class="checkbox">
                        <label>
                            <?= form_checkbox('address[address_type][]', 'billing'); ?> Billing &nbsp; &nbsp;
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <?= form_checkbox('address[address_type][]', 'shipping'); ?> Shipping &nbsp; &nbsp;
                        </label>
                    </div>
                    <? /*
                    <div class="checkbox">
                        <label>
                            <?= form_checkbox('address[address_type][]', 'drop ship'); ?> Drop Ship
                        </label>
                    </div>
                    */ ?>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <label class="col-xs-offset-3 col-xs-3 control-label">
                            <?= form_checkbox('address[address_type][]', 'other'); ?> Other
                        </label>
                        <div class="col-xs-6">
                            <input class="form-control input-sm" name="address[address_type_other]" value="<?= @$address['address_type_other'] ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_address">Address</label></div>
                <div class="col-xs-9"><input id="contact_form_address" name="address[address]" class="form-control input-sm"></div>
            </div>
            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_address_ext">Address Ext</label></div>
                <div class="col-xs-9"><input id="contact_form_address_ext" name="address[address_ext]" class="form-control input-sm"></div>
            </div>
            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_country">Country</label></div>
                <div class="col-xs-9"><input id="contact_form_country" name="address[country]" class="form-control input-sm"></div>
            </div>
            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_city">City</label></div>
                <div class="col-xs-9"><input id="contact_form_city" name="address[city]" class="form-control input-sm"></div>
            </div>
            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_state">State</label></div>
                <div class="col-xs-9"><?= state_select('address[state]', '', true, array('class' => 'form-control input-sm')) ?></div>
            </div>
            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_zip">Zip</label></div>
                <div class="col-xs-9"><input id="contact_form_zip" name="address[zipcode]" class="form-control input-sm"></div>
            </div>
            <label id="create_jobsite_cont" class="col-xs-12"><input type="checkbox" name="createSite" value="1"> Also create a Job Site with this address</label>

        </div>
    </div>

    <?php if( $mode != "add" ) : ?>
    <div class="row show-grid-md">
        <div class="col-xs-12">
            <?= @$addresses ?>
        </div>
    </div>
    <div class="row show-grid-md">
        <div class="col-xs-12">
            <?= @$job_sites ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-xs-12">
            <hr />
            <div class="inline">
                <input id="savecustomer" type="submit" name="submit" value="Save and Continue" class="pull-right btn btn-blue btn-sm" />
                <a href="/customers" class="btn btn-gray btn-sm">Cancel</a>
            </div>
        </div>
    </div>
</form>
