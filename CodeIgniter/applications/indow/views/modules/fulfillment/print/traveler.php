<div class="page_cont traveler_cont">
<div style="margin-top: 10px;" class="row">
    <div class="col-xs-3">
        ORDER #
        <span class="box-value"><?= $order->id ?></span>
    </div>
    <div class="col-xs-3">
        ITEM #
        <span class="box-value"><?= $item->unit_num ?></span>
    </div>
    <div class="col-xs-3">
        SERIAL #
        <span class="box-value"><?= $item->id ?></span>
    </div>
    <div class="col-xs-3 text-right" style="white-space: nowrap;">
        BUILD DATE
        <span style="margin-right: 2px;" class="box-value"><?= !empty($order->build_date) ? date('m/d/y', strtotime($order->build_date)) : '' ?></span>
    </div>
</div><br>
<div class="row text-right">
    <div class="col-xs-4 col-xs-offset-8" style="white-space: nowrap;">
        COMMIT DATE
        <span style="margin-right: 2px;" class="box-value"><?= !empty($order->commit_date) ? date('m/d/y', strtotime($order->commit_date)) : '' ?></span>
    </div>
</div>
<div class="row text-center">
<?=@$item->room?>, <?=@$item->location?>
</div>
<div class="row">
    <div class="col-xs-6">
        <div class="row">
            <div class="col-xs-6">
                <div class="plus_image <?= $item->horizontal ? 'horizontal' : '' ?>">
                    <div class="horiz"></div>
                    <div class="vert"></div>
                </div>
            </div>
            <div class="col-xs-6">
                <p><?= $item->product ?></p>
                <p style="white-space: nowrap;"><?= $item->product_type ?> <?= $item->acrylic_panel_thickness ?></p>
                <p><?= $item->edging ?></p>
                <p><?= $item->shape ?></p>
            </div>
        </div>
        <div class="row large_space">
            <div class="tubing_cut_dims col-xs-10">
                <h4>Tubing Cut Dims</h4>
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <th>B</th>
                        <th>T</th>
                        <th>L</th>
                        <th>R</th>
                    </tr>
                    <tr>
                        <td class="trav_b_len"></td>
                        <td class="trav_t_len"></td>
                        <td class="trav_l_len"></td>
                        <td class="trav_r_len"></td>
                    </tr>                    
                </table>
            </div>
        </div><br>
        <div class="row" style="margin-top: 35px;">
            <div class="col-xs-12">
                <h4>Glazing Cut Dims</h4>
                <div class="cut_image_cont" data-sheet-width="<?= $item->sheet_width ?>" data-sheet-height="<?= $item->sheet_height ?>" data-cuts="<?= $item->cuts ?>">
                    <div class="force-center">Image calculations not yet run.</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="row large_space">
            <div class="col-xs-7">
                <table class="checklist_table" cellspacing="0" cellpadding="0">
                    <tr><th>Tubing Cut</th><td></td></tr>
                    <tr><th>Glazing Cut</th><td></td></tr>
                    <tr><th>Peel Trim/Spine</th><td></td></tr>
                    <tr><th>Hardware</th><td></td></tr>
                    <tr><th>Prime</th><td></td></tr>
                    <tr><th>Assembly</th><td></td></tr>
                    <tr><th>QC</th><td></td></tr>
                </table>
            </div>
            <div class="col-xs-5">
                <p style="margin-top: 80px;">
                <b>Spines: </b>
                <?
                    if ($item->top_spine && $item->side_spines) {
                        echo 'Top + Side';
                    } elseif ($item->top_spine) {
                        echo 'Top';
                    } elseif ($item->side_spines) {
                        echo 'Side';
                    } else {
                        echo 'None';
                    }
                ?>
                </p>
                <p style="margin-top: 50px;">
                    <b>Clip Location: </b> <?= $item->pull_location ?>"
                </p>
                <p style="margin-top: 50px;">
                    <b><?= $item->sqft > 7.99 ? 'Blowout' : '' ?></b>
                </p>
            </div>
        </div>
        <div class="row large_space">
            <div class="col-xs-12">
                <h4>Special Order Notes</h4>
                <textarea rows="5" class="input-sm form-control"><?= htmlspecialchars($item->notes) ?></textarea>
            </div>
        </div>
        <div class="row large_space">
            <div class="col-xs-12">
                <h4>Window Opening Dims</h4>
                <table class="opening_dims_table" cellspacing="0" cellpadding="0">
                    <tr>
                        <th style="border: 0px;"></th>
                        <th>WT</th>
                        <th>WB</th>
                        <th>HL</th>
                        <th>HR</th>
                    </tr>
                    <tr>
                        <th>Actual</th>
                        <td><?= !empty($item->measurements['A']) ? $item->measurements['A'] : '' ?></td>
                        <td><?= !empty($item->measurements['B']) ? $item->measurements['B'] : '' ?></td>
                        <td><?= !empty($item->measurements['C']) ? $item->measurements['C'] : '' ?></td>
                        <td><?= !empty($item->measurements['D']) ? $item->measurements['D'] : '' ?></td>
                    </tr>
                    <tr>
                        <th>Measured</th>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row text-center"><br><h4>Expedite: <input disabled type="checkbox" <?= $order->expedite ? 'checked="checked"' : '' ?>> &nbsp; Shipping: <?= $order->ship_method ?></h4></div>
</div> <? // Close Traveler Container ?>
