<?php if (isset($message)): ?>
    <div class="well" id="flash_data">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<style>
    .checkboxes input {
        margin-left: 10px;
        margin-right: 3px;
        margin-top: 0px;
    }
    .checkboxes * {
        vertical-align: middle;
    }
    label.checklabel {
        width: auto;
        margin: 0px;
        font-weight: normal;
    }
</style>
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
$address_type = array(
    'billing' => 'Billing',
    'shipping' => 'Shipping',
    'drop' => 'Drop Site',
    'other' => 'Other',
);
function checkboxes($keys, $values) {
    foreach ($keys as $key => $value) {
        $checked = "";
        if (in_array($value, $values)) {
            $checked = "checked='checked'";
        }
        ?>
            <input id="<?= $key ?>check" type="checkbox" name="address_type[]" value="<?= $value ?>" <?= $checked ?>><label class="checklabel" for="<?= $key ?>check"><?= $value ?></label>
        <?
    }
}
?>

<form method="post" class="form-horizontal row" role="form">
    <div class="col-xs-5">
        <input type="hidden" name="user_id" value="<?= $customerid ?>">
        <input type="hidden" name="id" value="<?= empty($address['id']) ? '' : $address['id'] ?>">

        <div class="form-group">
            <label class="col-xs-3">Address Type</label>
            <div class="col-xs-9 form-inline">
                <div class="checkbox">
                    <label>
                        <?php $billing = (in_array('billing', @$address['address_type']))? TRUE: FALSE; ?>
                        <?= form_checkbox('address_type[]', 'billing', $billing); ?> Billing &nbsp;
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <?php $shipping = (in_array('shipping', @$address['address_type']))? TRUE: FALSE; ?>
                        <?= form_checkbox('address_type[]', 'shipping', $shipping); ?> Shipping &nbsp;
                    </label>
                </div>
                <? /*
                <div class="checkbox">
                    <label>
                        <?php $drop = (in_array('drop ship', @$address['address_type']))? TRUE: FALSE; ?>
                        <?= form_checkbox('address_type[]', 'drop ship', $drop); ?> Drop Ship
                    </label>
                </div>
                */ ?>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <label for="address_type_other" class="col-xs-offset-3 col-xs-3 control-label">
                        <?php $other = (in_array('other', @$address['address_type']))? TRUE: FALSE; ?>
                        <?= form_checkbox('address_type[]', 'other', $other, ' id="othercheck" '); ?> Other
                    </label>
                    <div class="col-xs-6">
                        <input class="form-control input-sm" name="address_type_other" id="otheraddress" value="<?= @$address['address_type_other'] ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="address" class="col-xs-3 control-label">Address</label>
            <div class="col-xs-9">
                <input class="form-control input-sm" id="address" name="address" value="<?= @$address['address'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="ext" class="col-xs-3 multi-line-label">Address Ext</label>
            <div class="col-xs-9">
                <input class="form-control input-sm" id="ext" name="address_ext" value="<?= @$address['address_ext'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="country" class="col-xs-3 control-label">Country</label>
            <div class="col-xs-9">
                <select class="form-control input-sm" id="country" name="country"><?= $countries ?></select>
            </div>
        </div>
        <div class="form-group">
            <label for="city" class="col-xs-3 control-label">City</label>
            <div class="col-xs-9">
                <input class="form-control input-sm" id="city" name="city" value="<?= @$address['city'] ?>">
            </div>
        </div>
        <div class="form-group">
            <?= form_label('State/<br />Province', 'state', array('class' => 'col-xs-3 multi-line-label')); ?>
            <div class="col-sm-9">
                <?= state_select('state', @$address['state'], true, array('class' => 'form-control input-sm')) ?>
            </div>
        </div>

        <div class="form-group show-grid">
            <label for="zip" class="col-xs-3 multi-line-label">Zip/Postal Code</label>
            <div class="col-xs-9">
                <input class="form-control input-sm" id="zip" name="zipcode" value="<?= @$address['zipcode'] ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12"><?= form_checkbox('createSite', 1); ?> Also create a Job Site with this address</label>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <hr />
            <div class="inline">
                <input type="submit" name="save" value="Save" class="btn btn-blue btn-sm pull-right" />
                <a href="/customers/edit/<?= $customerid ?>" class="btn btn-gray btn-sm pull-left">Cancel</a>
            </div>

            <?php if (isset($address['id'])): ?>
                <a href="/customers/deleteaddress/<?= $customerid ?>/<?= $address['id'] ?>" class="btn btn-blue btn-sm delete pull-right">Delete Address</a>
            <?php endif; ?>
        </div>
    </div>
</form>
