<?php
$tubing = array();
foreach ($items as $item) {
    $tubing[$item->edging ? $item->edging : 'Unspecified'][] = $item;
}
ksort($tubing);
?>
<div class="page_cont">
<div class="row">
    <div class="col-xs-12">
        <div class="pull-right">
            <div class="order_num_box"><?= $order->id ?></div>
        </div>
        <h3 class="text-center">TUBING CUT LIST</h3>
    </div>
</div>

<? foreach ($tubing as $color => $titems) { ?>
    <div class="no_page_break row">
        <div class="col-xs-11">
            <h3><?= strtoupper($color) ?></h3>
            <table class="tubing_table">
                <tr>
                    <th>Item #</th>
                    <th>Product</th>
                    <th>Shape</th>
                    <th>B</th>
                    <th>T</th>
                    <th>L</th>
                    <th>R</th>
                    <th>Thickness</th>
                    <th>Clip Location</th>
                    <th>&#x2713;</th>
                </tr>
                <? foreach ($titems as $item) { ?>
                    <tr class="traveler_cont">
                        <td>
							<div class="cut_image_cont" data-sheet-width="<?= $item->sheet_width ?>" data-sheet-height="<?= $item->sheet_height ?>" data-cuts="<?= $item->cuts ?>" style="display: none;"></div>
							<?= $item->unit_num ?>
						</td>
                        <td><?= $item->product ?></td>
                        <td><?= $item->shape ?></td>
                        <td class="trav_b_len"></td>
                        <td class="trav_t_len"></td>
                        <td class="trav_l_len"></td>
                        <td class="trav_r_len"></td>
                        <td><?= $item->acrylic_panel_thickness ?></td>
                        <td><?= !empty($item->pull_location) ? $item->pull_location : '' ?>"</td>
                    </tr>
                <? } ?>
            </table>
        </div>
    </div>
    <? } ?>

</div>
