<?
    if (empty($module)) {
        $module = null;
    }
    global $tmodule;
    $tmodule = $module;
    if (empty($mode)) {
        $mode = null;
    }
    if (isset($module) && $module === 'orders') {
        $mfg_status_options = array('1' => 'Include', '3' => 'Back Order', '4' => 'Re-Order');
    } else {
        $mfg_status_options = array('1' => 'Include', '2' => 'Hold');
    }

    $disabled = ($mode == 'view')? 'disabled="disabled"': '';
    function fheader($text) {
            global $tmodule;
            if ($tmodule === 'freebird') { ?>
                <h3 class="fheader"><?= $text ?></h3>
            <? }
    }
    function ftooltip($text) {
        global $tmodule;
        if ($tmodule === 'freebird') {
            return 'data-trigger="focus" data-placement="right" title="' . $text . '"';
        }
        return "";
    }

$mode = (isset($mode)) ? $mode : 'edit';
?>
<script>
    $('body').on('click', '.close-popover', function () {
        $(this).closest('.popover').prev().click();
    });
</script>
<div id="windowoptions">
<button type="button" class="close-popover close" aria-hidden="true" style="position: absolute; right: 12px; top: 8px;">&times;</button>
    <div class="row">
        <div class="col-md-6 form-horizontal">
            <? fheader('1. Product & Frame Details') ?>
            <div class="shape_cont form-group">
                <div class="col-xs-4"><label class="fglabel">Shape</label></div>
                <? /* if ($module === 'freebird') { ?>
                    <div class="col-xs-7"><?= form_dropdown('window_shape_id',$window_shapes,'',$ftooltip . ' class="form-control input-sm inpopover window_shape_options" '.$disabled) ?></div>
                    <div class="col-xs-1"><i style="margin-left: -6px; margin-top: 7px;" class="has_tooltip icon fa fa-info-circle" title="Text to be defined by indow"></i></div>
                <? } */ ?>
                <? $ftooltip = ftooltip('Select custom for any opening that isn\'t a parallelogram.'); ?>
                <div class="col-xs-8"><?= form_dropdown('window_shape_id',$window_shapes,'',$ftooltip . ' class="form-control input-sm inpopover window_shape_options" '.$disabled) ?></div>
            </div>
            <div class="form-group">
                <div class="col-xs-4"><label class="fglabel">Room</label></div>
                <? $ftooltip = ftooltip('Input room name.'); ?>
                <div class="col-xs-8"><input name="room" class="input-sm form-control" <?= $disabled ?> <?= $ftooltip ?>></div>
            </div>
            <div class="form-group">
                <div class="col-xs-4"><label class="fglabel">Location</label></div>
                <? $ftooltip = ftooltip('Useful to make sure the insert is placed in the correct window opening.   Examples include Left, West, 1, etc...'); ?>
                <div class="col-xs-8"><input name="location" class="input-sm form-control" <?= $disabled ?> <?= $ftooltip ?>></div>
            </div>
            <div class="form-group">
                <div class="col-xs-4"><label class="fglabel">Floor</label></div>
                <? $ftooltip = ftooltip('Which floor of the building is the room located in?'); ?>
                <div class="col-xs-8"><input name="floor" class="input-sm form-control" <?= $disabled ?>  <?= $ftooltip ?>></div>
            </div>
            <div class="form-group">
                <div class="col-xs-4"><label class="fglabel">Status</label></div>
                <? $ftooltip = ftooltip('You may measure as many inserts as you would like.  Mark them \'hold\' if you do not plan on ordering at this time.'); ?>
                <div class="col-xs-8"><?= form_dropdown('manufacturing_status',$mfg_status_options, '',$ftooltip . ' class="mfg_status_pop form-control input-sm" '.$disabled) ?></div>
            </div>
            <div class="form-group">
                <div class="col-xs-4"><label class="fglabel">Product</label></div>
                <? $ftooltip = ftooltip('Select the product type.'); ?>
                <div class="col-xs-8"><?= form_dropdown('product_id',$product_options,'',$ftooltip . ' class="form-control input-sm product_options inpopover" '.$disabled) ?></div>
            </div>
            <div class="form-group">
                <div class="col-xs-4"><label class="fglabel">Product Type</label></div>
                <? $ftooltip = ftooltip('Select the type of glazing.'); ?>
                <div class="col-xs-8"><select id="product_types_id" name="product_types_id" class="input-sm form-control product_type_options" <?= $disabled ?> <?= $ftooltip ?>></select></div>
            </div>
            <div class="form-group">
                <div class="col-xs-4"><label class="fglabel">Tubing</label></div>
                <? $ftooltip = ftooltip('Select the tubing color.'); ?>
                <div class="col-xs-8"><?= form_dropdown('edging_id',$edging_options, '',$ftooltip . ' class="edging form-control input-sm" '.$disabled) ?></div>
            </div>
            <div class="form-group">
                <div class="col-xs-4"><label class="fglabel">Frame Step</label></div>
                <? $ftooltip = ftooltip('Indicate which frame step will be used (counted out from the existing window).'); ?>
                <div class="col-xs-8"><?= form_dropdown('frame_step',$frame_step_options, '',$ftooltip . ' class="form-control frame_step input-sm" '.$disabled) ?></div>
            </div>
            <div class="form-group">
                <div class="col-xs-4"><label class="fglabel">Frame Depth</label></div>
                <? $ftooltip = ftooltip('Select the narrowest width of the frame step the insert will be installed on.'); ?>
                <div class="col-xs-8"><?= form_dropdown('frame_depth_id',$module === 'freebird' ? array('Select') + $frame_depth_options : $frame_depth_options, '',$ftooltip . ' class="form-control frame_depth input-sm" '.$disabled) ?></div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <? $ftooltip = ftooltip('Capture additional information in this block.'); ?>
                    <textarea class="form-control" name="notes" <?= $disabled ?> <?= $ftooltip ?>></textarea><br><br>
                    <label>Notes</label>
                </div>
            </div>
            <!--input type="hidden" id="extension_state" value="<?//$extension_state?>"></input-->
        </div>
        <div class="measurements_cont col-md-6 form-horizontal">
            <? fheader('2. Measure Window Frame') ?>
            <? if ($module === 'freebird') { ?>
                <div class="red_subtext">*Record values as they appear on your laser.</div>
                <div class="pull-right custom_instructions" style="display: none"><a target="_blank" href="/assets/custom_instructions.pdf">Review custom measurement instructions.</a><br><br></div>
            <? } ?>
            <? $field_messages = array(
                'A' => 'Measure between the top left and top right corner',
                'B' => 'Measure between the bottom left and bottom right corner',
                'C' => 'Measure between the bottom left and top left corner',
                'D' => 'Measure between the bottom right and top right corner',
                'E' => 'Measure between the bottom left and top right corner',
                'F' => 'Measure between the top left and bottom right corner'
            ); ?>
            <? foreach(array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J') as $key) {
                $hidden = in_array($key, array('A', 'B', 'C', 'D', 'E', 'F')) ? '' : 'style="display: none;"';
                if ($module === 'freebird') {
                    $tooltip = empty($field_messages[$key]) ? '' : 'data-trigger="focus" data-placement="right" title="' . $field_messages[$key] . '"';
                } else {
                    $tooltip = '';
                }
                ?>
                <div <?= $hidden ?> class="form-group">
                    <div class="col-xs-4"><label class="fglabel"><?= $key ?></label></div>
                    <div class="col-xs-8">
                        <input id="popover-<?=$key?>" name="<?= $key ?>" class="input-sm measurement form-control" <?= $tooltip ?> data-mode="<?= $mode ?>">
                    </div>
                </div>
            <? } ?>
            <div class="row">
                <div class="col-xs-12">
                    <? if ($module === "orders" && !empty($this->data['user']->in_admin_group)) { ?>
                        <div class="form-group">
                            <div class="col-xs-4"><label class="fglabel">Spines:</label></div>
                            <div class="col-xs-8">
                                <select class="input-sm form-control spines_edit">
                                    <option value="None">None</option>
                                    <option value="Top">Top</option>
                                    <option value="Side">Side</option>
                                    <option value="Both">Both</option>
                                </select>
                            </div>
                        </div>
                    <? } elseif ($module !== 'freebird') { ?>
                        <label>Spines: <span class="spines_display"></span></label><br><br>
                    <? } ?>
                </div>
                <div class="col-xs-12" <?= $module !== 'freebird' && ($module !== 'orders' || empty($order->freebird)) ? "style='display: none;'" : "" ?>>
                    <label>
                        <? if (!empty($legacy)) { ?>
                            <input type="checkbox" class="plus_one" name="freebird_laser" <?= $module === 'orders' ? "disabled='disabled'" : ''?>>
                            <?= $module === 'orders' ? 'Self Measure Laser' : 'I used the laser provided and I did not change the laser settings.' ?>
                        <? } else if ($module === 'freebird') { ?>
                            <input type="checkbox" class="own_tools" name="own_tools" <?= $module === 'orders' ? "disabled='disabled'" : ''?>>
                            I used my own tools.
                        <? } ?>
                    </label>
                </div>
                <input name="side_spines" type="hidden">
                <input name="top_spine" type="hidden">
                <? if ($module === 'freebird') { ?>
                    <? if (!empty($legacy)) { ?>
                    <div class="col-xs-12 extension_cont">
                        <input type="checkbox" class="freebird" name="extension"><label class="glabel"> I used the extension tool for my diagonal measurements.</label><br>
                    </div>
                    <? } ?>
                    <br style="clear: both"><br>
                <? } else { ?>
                    <div class="col-xs-12">
                        <? if (!empty($legacy)) { ?>
                            <input type="checkbox" name="extension" disabled="disabled"> <label class="glabel">Extension Tool</label>
                        <? } else { ?>
                            <input type="checkbox" name="own_tools" disabled="disabled"> <label class="glabel">Own Tools</label>
                        <? } ?>
                    </div>
                    <div class="col-xs-12">
                        <input type="checkbox" name="drafty" <?= $disabled ?>> <label class="glabel">Drafty Window</label>
                    </div>
                    <div class="col-xs-12">
                        <input type="checkbox" name="special_geom" <?= $disabled ?>> <label class="glabel">Special Geometry</label>
                    </div>
                    <div class="col-xs-12">
                    <? if (!empty($this->data['user']->in_admin_group)) { ?>
                        <br><br><a href='#' class="cut_image_link">View Cut Image</a>
                    <? } ?>
                <? } ?>
                </div>
                <? /*
                <div class="col-xs-12">
                    <input type="checkbox" name="extension" <?= $disabled ?>> <label class="glabel">Extension Tool</label>
                </div>
                */ ?>
                <? fheader('3. Verify Your Measurements') ?>
                <? if ($module === 'freebird') { ?>
                    <input type="hidden" name="valid" value="0">
                    <input type="hidden" name="measured" value="1">
                    <input type="hidden" name="special_geom" value="0">
                    <div class="col-xs-12">
                        <button style="margin-left: 0px;" class="pull-right windowoptionscheck btn btn-blue btn-sm">Check</button>
                    </div>
                <? } else if ($module === 'orders') { ?>
                    <input type="hidden" name="valid" value="0">
                    <input type="hidden" name="measured" value="1">
                <? } ?>
            <br style="clear: both"><br><div class="col-xs-12" id="validation_response"><p></p></div>
            <? fheader('4. Save your information') ?>
            <? if ($module === 'freebird') { ?>
                <div class="col-xs-12">
                    <button class="pull-right windowoptionssave measurenext btn btn-blue btn-sm" disabled="disabled" style="margin-left: 5px">Save & Measure Next</button>
                    <button class="pull-right windowoptionssave btn btn-blue btn-sm" disabled="disabled" style="margin-left: 5px">Save</button>
                </div>
            <? } ?>
            </div>
        </div>
    </div>
    <?php if($mode != 'view'): ?>
    <div class="row">
        <div class="col-xs-6 pull-right">
            <div class="pull-right">
            <? if ($module !== 'freebird') { ?>
                <button class="windowoptionscheck btn btn-blue btn-sm">Check</button>
                <button class="windowoptionsrevert btn btn-blue btn-sm" disabled>Revert</button>
                <button <?= $module === 'orders' ? 'disabled="disabled"' : '' ?> class="windowoptionssave btn btn-blue btn-sm" style="margin-left: 5px">Save</button>
            <? } ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
