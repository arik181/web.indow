<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function draw_fees_list($fees_sorted, $show_taxes=false) {
    ob_start();
    ?>
    <div id="fees_discounts_cont">
        <? if (count($fees_sorted['fees'])) { ?>
            <h4>Fees</h4>
            <? foreach ($fees_sorted['fees'] as $fee) { ?>
                <input type="checkbox" value="<?= $fee->id ?>"> <label class="glabel"><?= @$fee->description ?></label><br>
            <? } ?>
            <br>
        <? } ?>
        <? if (count($fees_sorted['discounts'])) { ?>
            <h4>Discounts</h4>
            <? foreach ($fees_sorted['discounts'] as $discount) { ?>
                <input type="checkbox" value="<?= $discount->id ?>"> <label class="glabel"><?= @$discount->description ?></label><br>
            <? } ?>
            <br>
        <? } ?>
        <? if (count($fees_sorted['taxes']) && $show_taxes) { ?>
            <h4>Taxes</h4>
            <? foreach ($fees_sorted['taxes'] as $tax) { ?>
                <input type="checkbox" value="<?= $tax->id ?>"> <label class="glabel"><?= @$tax->description ?></label><br>
            <? } ?>
            <br>
        <? } ?>
        <? if (!count($fees_sorted['discounts']) && !count($fees_sorted['fees']) && (!$show_taxes || !count($fees_sorted['taxes']))) {
            if ($show_taxes) {
                echo '<h4>No fees, discounts, or taxes available.</h4>';
            } else {
                echo '<h4>No fees or discounts available.</h4>';
            }
        } else { ?>
            <div class="text-center">
                <button style="margin: 0px auto" class="fees_apply center btn btn-blue btn-sm">Apply</button>
            </div>
        <? } ?>
    </div>
    <?
    return ob_get_clean();
}   

?>
