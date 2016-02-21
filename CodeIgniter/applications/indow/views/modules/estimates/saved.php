<form method="POST" action="">
<input type="hidden" name="estimate_id" value="<?= $estimate->id; ?>">
<input type="hidden" name="parent_estimate_id" value="<?= $estimate->parent_estimate_id; ?>">
<div id="estimate_name"><input type="text" value="<?= $estimate->name; ?>" name="name"></div>

<?php if(! ( empty($items) && empty($available_items))):?>
<?php if(!empty($items)) { ?>
<h3>Selected Products</h3>
<table class="table table-hover">
    <thead>
    <tr>
        <th><input type="checkbox" name="all"></th>
        <th>Room</th>
        <th>Location</th>
        <th>Width</th>
        <th>Height</th>
        <th>Product</th>
        <th>Product Type</th>
        <th>Edging</th>
        <th>Special Geometry</th>
        <th>Retail</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    $total = null;
    $fees = 52;
    foreach($items as $item)
    {
    	$total = $total + $item->retail;
    ?>
        <tr>
            <td id="item_id"><input type="checkbox" name="remove_items[]" value="<?= $item->id; ?>"></td>
            <td id="room"><?= $item->room; ?></td>
            <td id="location"><?= $item->location; ?></td>
            <td id="width"><?= $item->width; ?></td>
            <td id="height"><?= $item->height; ?></td>
            <td id="product"><?= $item->product; ?></td>
            <td id="product_type"><?= $item->product_type; ?></td>
            <td id="edging"><?= $item->edging; ?></td>
            <td id="geometry"><?= $item->geometry; ?></td>
            <td id="retail">$<?= number_format($item->retail, 2); ?></td>                        
        </tr>
    <?php } ?>
    </tbody>
</table>
<div>Subtotal:&nbsp;&nbsp;$<?=number_format($total, 2)?><br>
Fees:&nbsp;&nbsp;$<?=number_format($fees, 2)?><br>
Grand Total:&nbsp;&nbsp;$<?=number_format($fees + $total, 2)?></div>
<div>With Selected</div>
<input type="submit" class="btn btn-blue btn-add" value="Remove from Saved View">
<? } 
if(!empty($available_items)) {
?>
<h3>Available Products</h3>
<div>Select from below to add to your new estimate configuration.</div>
<table class="table table-hover">
    <thead>
    <tr>
        <th><input type="checkbox" name="all"></th>
        <th>Room</th>
        <th>Location</th>
        <th>Width</th>
        <th>Height</th>
        <th>Product</th>
        <th>Product Type</th>
        <th>Edging</th>
        <th>Special Geometry</th>
        <th>Retail</th>
    </tr>
    </thead>
    <tbody>
    <?php 
    unset($item);
    foreach($available_items as $item)
    {
    ?>
        <tr>
            <td id="item_id"><input type="checkbox" name="add_items[]" value="<?= $item->id; ?>"></td>
            <td id="room"><?= $item->room; ?></td>
            <td id="location"><?= $item->location; ?></td>
            <td id="width"><?= $item->width; ?></td>
            <td id="height"><?= $item->height; ?></td>
            <td id="product"><?= $item->product; ?></td>
            <td id="product_type"><?= $item->product_type; ?></td>
            <td id="edging"><?= $item->edging; ?></td>
            <td id="geometry"><?= $item->geometry; ?></td>
            <td id="retail">$<?= number_format($item->retail, 2); ?></td>                        
        </tr>
    <?php } ?>
    </tbody>
</table>

<div>With Selected</div>
<div><input type="submit" class="btn btn-blue btn-add" value="Add to Estimate Configuration"></div>

<br><br>
<div style="display: block;"><hr>
<input type="submit" class="btn btn-blue btn-add pull-left" value="Save">
<a href="/estimates/list" class="btn btn-blue btn-add pull-right">Cancel</a>
</div>
<?php } ?>
<?php else:?>
    <div id="list_empty_message">There are no items in the estimate, please go back and add them.</div>
<?php 
endif;?>

</form>