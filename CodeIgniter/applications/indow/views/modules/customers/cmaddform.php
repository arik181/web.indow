<?
    $phone_options = array('Home', 'Work', 'Mobile');
    $email_options = array('Home', 'Work');
    $address_options = array(
        '' => '',
        'billing' => 'Billing',
        'shipping' => 'Shipping',
       // 'drop ship' => 'Drop Ship',
        'other' => 'Other',
    );
?>
<script>
    var indow_phone_options = <?= json_encode($phone_options) ?>;
    var indow_email_options = <?= json_encode($email_options) ?>;
</script>
<form id="contact_form_newcustform" method="post">
    <div class="row in" style="margin-left: 0px; margin-right: 0px">
        <div class="col-md-6 form-horizontal">
            <h4>Contact Info</h4>
            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_first">First Name</label></div>
                <div class="col-xs-9"><input id="contact_form_first" name="first_name" class="form-control input-sm"></div>
            </div>
            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_last">Last Name</label></div>
                <div class="col-xs-9"><input id="contact_form_last" name="last_name" class="form-control input-sm"></div>
            </div>

            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_company">Company</label></div>
                <div class="col-xs-9"><input id="contact_form_company" name="organization_name" class="form-control input-sm"></div>
            </div>

            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_email_1">Email</label></div>
                <div class="col-xs-6">
                    <?= form_input('email_1', '', 'class="form-control input-sm"'); ?>
                </div>
                <div class="col-xs-3"><?= form_dropdown('email_type_1',$email_options,'', 'class="form-control input-sm"');?></div>
            </div>

            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_email_2">Email</label></div>
                <div class="col-xs-6"><input id="contact_form_email2" name="email_2" class="form-control input-sm"></div>
                <div class="col-xs-3"><?= form_dropdown('email_type_2',$email_options,'', 'class="form-control input-sm"');?></div>
            </div>

            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_phone1">Phone</label></div>
                <div class="col-xs-6"><input id="contact_form_phone1" name="phone_1" class="form-control input-sm"></div>
                <div class="col-xs-3"><?= form_dropdown('phone_type_1',$phone_options,'', 'class="form-control input-sm"');?></div>
            </div>

            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_phone2">Phone</label></div>
                <div class="col-xs-6"><input id="contact_form_phone2" name="phone_2" class="form-control input-sm"></div>
                <div class="col-xs-3"><?= form_dropdown('phone_type_2',$phone_options,'', 'class="form-control input-sm"');?></div>
            </div>

            <div class="form-group">
                <div class="col-xs-3"><label for="contact_form_phone3">Phone</label></div>
                <div class="col-xs-6"><input id="contact_form_phone3" name="phone_3" class="form-control input-sm"></div>
                <div class="col-xs-3"><?= form_dropdown('phone_type_3',$phone_options,'', 'class="form-control input-sm"');?></div>
            </div>
        </div>
        <div class="col-md-6 form-horizontal">
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
                <div class="col-xs-9">
                <? $this->load->helper('contact_info');
                $countries = gen_options('countries', 'United States'); ?>
                    <select class="form-control input-sm" id="country" name="address[country]"><?= $countries ?></select>
                </div>
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
    <input id="submit_customer_form" type="submit" value="Add Customer" class="btn btn-sm btn-blue pull-right" style="margin-right: 15px;">
</form>