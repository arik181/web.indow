<script>
    $(function () {
        $('.edit-linked').click(function () {
            $('.edit-linked').prop('checked', $(this).prop('checked'))
        });
        $('#mass_edit_product').change(function () {
            filter_select($('#mass_edit_product_type'), $(this).val());
        }).change();
        $('#mass_edit_edging').val(1); //make starting state consistent for pages that have a blank option
    });
</script>
<div class="form-group">
    <input type="checkbox" id="mass-edit-product" class="edit-linked mass-edit-checkbox">
    <label class="sr-only" for="product">Product Options</label>
    <?= form_dropdown('',$product_options,'','data-field="product_id" data-target=".product_options" class="mass-edit form-control input-sm" id="mass_edit_product"') ?>
</div>
<div class="form-group">
    <label class="sr-only" for="product_type">Product Type</label>
    <input type="checkbox" id="mass-edit-ptype" class="edit-linked mass-edit-checkbox">
    <?= form_dropdown('product_types_id',$product_type_options,'','data-field="product_types_id" data-target=".product_type_options" class="mass-edit form-control input-sm" id="mass_edit_product_type"') ?>
</div>
<div class="form-group">
    <label class="sr-only" for="edging">Tubing</label>
    <input type="checkbox" id="mass-edit-tubing" class="mass-edit-checkbox">
    <?= form_dropdown('edging_id',$edging_options,'','data-field="edging_id" data-target=".edging_options" class="mass-edit form-control input-sm" id="mass_edit_edging"') ?>
</div>
<div class="form-group">
    <button id="mass_edit_submit" type="button" class="btn btn-default btn-black btn-sm">Apply</button>
</div>