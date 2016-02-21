<?
if (!isset($product_info)) {
    $product_info = array();
}
?>
<div id="productcheckboxes">
    <? foreach ($product_info as $product) {
        if ($product->product_id == 3 && $product->opening_specific) { ?>
        <div class="cboxrow" data-id="<?= $product->id ?>">
            <input class="assoc_product_checkbox" type="checkbox" value="<?= $product->id ?>" <?= (!isset($is_indow) || $is_indow)? '':'disabled'; ?>>
            <label class="glabel"><?= $product->product_type ?></label>
        </div>
        <? }
    } ?>
    <?php if(!isset($is_indow) || $is_indow) { ?>
        <br style="clear: both;"><button class="pull-right btn btn-blue btn-sm addproductssubmit" type="button">Add</button><br><br>
    <?php } ?>
</div>