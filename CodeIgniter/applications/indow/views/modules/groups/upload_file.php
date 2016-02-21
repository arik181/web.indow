<?php if (isset($message)): ?>
    <div class="well" id="flash_data">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<script>
    $(function () {
        $('#file_type').change(function () {
            if ($(this).val() === 'logo') {
                $('#logotext').show();
            } else {
                $('#logotext').hide();
            }
        });
    });
</script>
<form method="post" enctype="multipart/form-data">
    <span id="logotext">Logo max size is 100x180 pixels.  Allowed filetypes are gif, jpg, and png.</span><br><br>
    <div class="row form-horizontal">
        <div class="col-xs-6">
            <div class="form-group">
                <label for="file" class="col-xs-3 control-label">Select File</label>
                <div class="col-xs-9">
                    <input name="userfile" type="file" class="input-sm form-control">
                </div>
            </div>
            <div class="form-group">
                <label for="file" class="col-xs-3 control-label">Category</label>
                <div class="col-xs-9">
                    <?= form_dropdown('file_type', array('logo' => 'Logo', 'addendum' => 'Estimate Addendum Documents'), @$type, "class='input-sm form-control' id='file_type'") ?>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <input class="btn btn-sm btn-blue" type="submit" value="upload">
    <a class="pull-right btn btn-sm btn-blue" href="/groups/edit/<?= $group_id ?>" type="button">Cancel</a>
</form>