<div class="page_cont">
    <div class="print-label">
        <div style="line-height: 28px; width: 4in; height: 6in; padding: 5px;">
            <div class="row" style="margin-top: .5in;">
                <div class="col-xs-6">
                    <img style="position:relative; top: -8px;" src="<?php echo base_url('assets/theme/default/img/logo-indow.png');?>" class="img-responsive logo-indow" alt="MODI Logo" />
                </div>
                <ul class="list-unstyled col-xs-6 text-right order-unit">
                    <li><strong>Order:</strong> <?= @$order->id ?>  </li>
                    <li><strong>Item: </strong> <?= @$item->unit_num ?>   </li>
                    <li><strong>Unit: </strong> <?= @$item->id ?>   </li>
                    <li><strong>Date: </strong> <?= date('Y/m/d') ?></li>
                </ul>
            </div>
            <div class="row" style="position: relative; top: -38px;">
                <ul class="list-unstyled col-xs-8">
                    <li><strong>Dealer:</strong> <?= @$order->dealer_name ?>  </li>
                    <li><strong>Customer:</strong> <?= $order->first_name . ' ' . $order->last_name ?></li>
                    <li><strong>PO NO:</strong> <?= $order->po_num ?></li>
                    <li><strong>Room:</strong> <?= $item->room ?></li>
                    <li><strong>Location:</strong> <?= $item->location ?></li>
                    <li><strong>Shape:</strong> <?= $item->shape ?></li>
                    <li><strong>Product/Type:</strong> <?= $item->product . ' / ' . $item->product_type ?></li>
                    <li><strong>Color:</strong> <?= $item->edging ?></li>
                    <li><strong>Size:</strong> <?= @$item->measurements['B'] . 'x' . @$item->measurements['D']  ?></li>
                    <li><strong>Floor:</strong> <?= $item->floor ?></li>
                    <li><strong>Frame Step:</strong> <?= $item->frame_step ?></li>
                    <li><strong>Bracket Loc:</strong> <?= $item->bracket_location ?><?= $item->bracket_location === 'N/A' ? '' : '&quot;' ?></li>
                    <li><strong>Notes:</strong> <?= $item->notes ?></li>
                </ul>
                <div class="col-xs-4">
                    <div class="pull-right" style="position: relative; top: 20px;">
                        <img id="barcode_<?= $order->id . '_' . $item->id ?>" class="barcode" />
                        <script>
                            $(function () {
                                $("#barcode_<?= $order->id . '_' . $item->id ?>").JsBarcode("<?= $item->id ?>",{format:"CODE128",displayValue:false,fontSize:20,width:'2'});
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
