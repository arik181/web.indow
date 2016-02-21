<? if (!empty($multiple)) { ?>
<main class="page_cont">
<? $this->load->view('/themes/default/partials/packing_list_header') ?>
<? } ?>
<div class="row show-grid-sm address">
    <div class="col-xs-8">
        <h2>Billing:</h2>
        <ul class="list-unstyled">
            <? if ($order->dealer_id == 1 || !$order->dealer_id) { ?>
                <li><?= $order->customer_name ?></li>
                <? if ($order->user_address) { ?>
                    <li><?= $order->user_address . ' ' . $order->user_address_ext ?></li>
                    <li><?= $order->user_city . ', ' . $order->user_state . ' ' . $order->user_zip ?></li>
                    <? if (!empty($order->user_phone)) { ?>
                        <li>Contact: <?= $order->user_phone ?></li>
                    <? } ?>
                <? } ?>
            <? } else { ?>
                <li><?= $order->dealer_name ?></li>
                <? if ($order->group_address) { ?>
                    <li><?= $order->group_address . ' ' . $order->group_address_ext ?></li>
                    <li><?= $order->group_city . ', ' . $order->group_state . ' ' . $order->group_zip ?></li>
                    <? if (!empty($order->group_phone)) { ?>
                        <li>Contact: <?= $order->group_phone ?></li>
                    <? } ?>
                <? } ?>
             <? } ?>
        </ul>
    </div>
    <div class="col-xs-4">
        <h2>Ship To:</h2>
        <ul class="list-unstyled">
            <? if ($order->shipping_address) { ?>
                <li><?= $order->dealer_shipping_address_id ? $order->dealer_name : $order->customer_name ?></li>
                <li><?= $order->shipping_address->address . ' ' . $order->shipping_address->address_ext ?></li>
                <li><?= $order->shipping_address->city . ', ' . $order->shipping_address->state . ' ' . $order->shipping_address->zipcode ?></li>
            <? } ?>
            <?
            $phone = $order->dealer_shipping_address_id ? $order->group_phone : $order->user_phone;
            if ($phone) { ?>
            <li>Contact: <?= $phone ?></li>
            <? unset($phone); } ?>
        </ul>
    </div>
</div>
<div class="row show-grid-sm">
    <div class="col-xs-12">
        <!--script>
            $(document).ready(function() {
                $('#orderTable').DataTable( {
                    paging: false,
                    searching: false,
                    ordering: false,
                    info: false
                } );
            });
        </script-->
        <table id="orderTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>FOB</th>
                    <th>PO NO:</th>
                    <th>Ship Method</th>
                    <th>Carrier</th>
                    <th>Ship Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $order->order_date ? date('m/d/Y', strtotime($order->order_date)) : '' ?></td>
                    <td>Portland, OR</td>
                    <td><?= $order->po_num ?></td>
                    <td><?= $order->ship_method ?></td>
                    <td><?= $order->carrier ?></td>
                    <td><?= $order->ship_date ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row show-grid-sm panels">
    <div class="col-xs-12">
        <h2>Panel</h2>
        <!--script>
            $(document).ready(function() {
                $('#panelsTable').DataTable( {
                    paging: false,
                    searching: false,
                    ordering: false,
                    info: false
                } );
            });
        </script-->
        <table id="panelsTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Room</th>
                    <th>Location</th>
                    <th>Color</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Width</th>
                    <th>Height</th>
                </tr>
            </thead>
            <tbody>
                <? foreach ($items as $item) { ?>
                <tr>
                        <td><?= $item->unit_num ?></td>
                        <td><?= $item->room ?></td>
                        <td><?= $item->location ?></td>
                        <td><?= $item->edging ?></td>
                        <td><?= $item->product ?></td>
                        <td><?= $item->product_type ?></td>
                        <td><?= @$item->measurements['B'] ?></td>
                        <td><?= @$item->measurements['D'] ?></td>
                </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row show-grid-sm panels">
    <div class="col-xs-4">
        <h2>Accessories</h2>
        <!--script>
            $(document).ready(function() {
                $('#accessoriesTable').DataTable( {
                    paging: false,
                    searching: false,
                    ordering: false,
                    info: false
                } );
            });
        </script-->
        <table class="table table-bordered" id="accessoriesTable">
            <thead>
                <tr>
                    <th>Qty</th>
                    <th>Name/Description</th>
                </tr>
            </thead>
            <tbody>
                <? foreach ($assoc_products as $product) { ?>
                    <tr>
                        <td><?= $product->quantity ?></td>
                        <td><?= $product->name ?></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="no_page_break row show-grid-md">
    <div class="col-xs-12">
        <div class="sig-box">
            <div class="sig-item">
                <h2>SPECIAL INSTRUCTIONS:</h2>
                <div class="sig-input"><textarea class="special_instructions"></textarea></div>
            </div>
            <div class="sig-item">
                <h2>ORDER RECEIVED</h2>
                <div class="sig-input">
                    <p>By signing here, I verify that I have received this order in good condition</p>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        Signature
                    </div>
                    <div class="col-xs-6">
                        Date
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<? if (!empty($multiple)) { ?>
</main>
<? } ?>
