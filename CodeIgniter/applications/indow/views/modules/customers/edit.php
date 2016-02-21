<?php if (isset($message)): ?>
    <div class="well" id="flash_data">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<script>
    $(function () {
        $('.togglecont').click(function() {
            $(this).toggleClass('fa-chevron-down fa-chevron-left').parent().toggleClass('collapsed'); //make site-lists collapse
        });
        $('#savecompanyform').click(function () {
            $('#companyform').submit();
        });
        $.ajaxSetup({
            error: function (x, status, error) {
                if (x.status == 403) {
                    $('#customer_edit_btn').popover('hide');
                    alert("You do not have permission to edit that customer.");
                }
            }
        });
        var ctrl_s = $('.ctrl_s');
        if (ctrl_s.length) {
            $(document).keydown(function (e) {
                if (e.ctrlKey && e.keyCode === 83) {
                    e.preventDefault();
                    ctrl_s.click();
                }
            });
        }
    });
</script>
<div class="row">
    <h3 class="col-xs-12"><?=$subtitle?></h3>
</div>
<div class="row show-grid">
    <div class="col-xs-7 popover-md">
        <?= $contact_info ?>
    </div>
    <div class="col-xs-5">
        <form id="companyform" class="form-horizontal" method="post">
            <div class="form-group">
                <label class="col-xs-4" for="comp">Company/<br />Group</label>
                <div class="col-xs-8">
                    <?= form_dropdown('company_id', $group_options, @$customer->company_id, "id='company' class='input-sm form-control'") ?>
                </div>
            </div>

            <?php if($this->permissionslibrary->has_permission(1)):?>

                <div class="form-group">
                    <label class="col-xs-4 control-label" for="rep">Assoc. Rep</label>
                    <div class="col-xs-8">
                        <?= form_dropdown('customer_preferred_contact', $user_options, @$customer->customer_preferred_contact, "id='rep' class='input-sm form-control'") ?>
                    </div>
                </div>

            <?php endif;?>

        </form>
    </div>
</div>

<?php if (isset($customer->user_id)): ?>
<?= form_open("/customers/edit/" . $customer->user_id, array('class'=>'edit_customer_form')); ?>
<?php else: ?>
<?= form_open("/customers/add", array('class'=>'add_customer_form')); ?>
<?php endif; ?>

<div class="row">
    <div class="col-xs-12">
        <?= $addresses ?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <?= $job_sites ?>
    </div>
</div>

<div class="row content-row" class="form-group">
    <?php if (isset($customer->user_id)): ?>
        <a href="/customers/delete/<?= $customer->user_id ?>" class="btn btn-gray btn-content delete">Delete Customer</a>
    <?php endif; ?>
    <button type="button" id="savecompanyform" class="ctrl_s btn btn-default pull-right btn-content">Save</button>
</div>

<?= form_close(); ?>
<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js'); ?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js'); ?>"></script>
