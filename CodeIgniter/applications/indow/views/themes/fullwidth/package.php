<div class="container">
    <div class="row show-grid-sm">
        <div class="col-xs-3">
            <h3>Order# <?= $order->order_number . '-' . $order->order_number_type_sequence ?></h3>
            <?= $order->po_num && $order->dealer_id ? 'PO# ' . $order->po_num : '' ?>
        </div>
        <div class="col-xs-6">
            <?php if ($submitted_date): ?>
                <strong>Order Submitted</strong> <?= date('F d, Y g:i a', strtotime($submitted_date)) ?><br />
            <?php endif; ?>
            <?php if ($confirmed_date): ?>
                <strong>Order Confirmed</strong> <?= date('F d, Y g:i a', strtotime($confirmed_date)) ?> by (<?= $order->confirmation_sig ?>)
            <?php endif; ?>
        </div>
        <div class="col-xs-3">
            <button class="btn btn-default">Print</button>
        </div>
    </div>
</div>
<div class="greybar">
    <div class="container">
        <div class="row">
            <? if ($order->dealer_id) { ?>
                <div class="col-md-4 data-chunk">
                    <h2>Dealer Location</h2>
                    <?= $dealer_address->name ?><br />
                    <?= $dealer_address->rep_name ?><br />
                    <?= display_address($dealer_address) ?><br />
                    <?php if($dealer_address->phone_1): ?>
                        <strong><?= $dealer_address->phone_1 ?></strong><br />
                    <?php endif; ?>
                </div>
                <div class="col-md-4 data-chunk">
                    <h2>Ship To</h2>
                    <?= display_address($shipping_address) ?>
                </div>
                <div class="col-md-4 data-chunk">
                    <h2>Customer Info</h2>
                    <?= $customer ? render_contact_info($customer, isset($customer->addr) ? $customer->addr : null) : 'No Customer'; ?>
                </div>
            <? } else { ?>
                <div class="col-md-4 data-chunk">
                    <h2>Customer Info</h2>
                    <?= $customer ? render_contact_info($customer, isset($customer->addr) ? $customer->addr : null) : 'No Customer'; ?>
                </div>
                <div class="col-md-4 data-chunk">
                    <h2>Job Site</h2>
                    <?= display_address($job_site) ?>
                </div>
            <? } ?>
        </div>
    </div>
</div>

<div class="bluebar show-grid">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h2 class="barheader">Order Summary</h2>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <table id="itemtable" class="display table table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Item #</th>
                        <th>Room</th>
                        <th>Location</th>
                        <th>Width</th>
                        <th>Height</th>
                        <th>Product</th>
                        <th>Product Type</th>
                        <th>Special Geometry</th>
                        <th>Tubing</th>
                        <th>Retail</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-offset-8 col-xs-4">
            <dl class="dl-horizontal dl-totals">
                <dt>Order Subtotal</dt>
                <dd>$ <?= money_format('%i', $totals['subtotal']) ?></dd>

                <? if (!empty($totals['wholesale_discount'])) { ?>
                    <dt>WS Discount</dt>
                    <dd>$ <?= money_format('%i', $totals['wholesale_discount']) ?></dd>
                <? } ?>

                <? foreach ($totals['mods'] as $mod) { ?>
                    <dt><?= htmlspecialchars($mod['name']) ?></dt>
                    <dd>$ <?= money_format('%i', $mod['amount']) ?></dd>
                <? } ?>

                <? if ($totals['special_geom']) { ?>
                <dt>Special Geometry Fee x<?= $totals['special_geom_count'] ?></dt>
                <dd>$ <?= money_format('%i', $totals['special_geom']) ?></dd>
                <? } ?>

                <!--
                <dt>Tax</dt>
                <dd>$<?= money_format('%i', $totals['taxes']) ?></dd>
                -->

                <dt style="font-size: 1.1em">Order Total</dt>
                <dd style="font-size: 1.1em" id="grand_total">$ <?= money_format('%i', $totals['total']) ?></dd><br>

                <dt>Prepayment</dt>
                <dd>$ <?= money_format('%i', round($totals['total'] / 2)) ?></dd>

                <dt>Payments</dt>
                <dd>$ <?= money_format('%i', $totals['payments']) ?></dd>

                <dt>Balance Due</dt>
                <dd>$ <?= money_format('%i', $totals['due']) ?></dd>
            </dl>
        </div>
    </div>
    <div class="row" id="package-footer">
        <div class="col-xs-12">
        </div>
    </div>
</div>
