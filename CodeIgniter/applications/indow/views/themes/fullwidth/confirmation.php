<? if ($mode === 'print') { ?>
    <style>
        .fwpagetitle, #terms_footer {
            display: none;
        }
    </style>
    <script>
        $(function () {
            window.print();
        });
    </script>
<? } ?>

<script>
    var indow_items = <?= json_encode($items) ?>;
</script>
<script src="/assets/theme/default/js/bootbox.min.js"></script>
<script src="/assets/theme/default/js/jquery.json.js"></script>
<script src="/assets/theme/default/js/shared.js"></script>
<script src="/assets/theme/default/js/confirmation.js"></script>
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
            <!--button class="btn btn-default">Print</button-->
        </div>
    </div>
</div>
<div class="greybar">
    <div class="container">
        <div class="row">
            <? if ($order->dealer_id) { ?>
                <div class="col-md-4 data-chunk">
                    <?php /* formerly Dealer Location */ ?>
                    <h2>Dealer</h2>
                    <?= $creator ?><br /><br />
                    <?php if(isset($dealer_address->phone_1)): ?>
                        <strong><?= $dealer_address->phone_1 ?></strong><br />
                    <?php elseif(isset($dealer_address->phone_2)): ?>
                        <strong><?= $dealer_address->phone_2 ?></strong><br />
                    <?php endif; ?>
                    <?php if(isset($dealer_address->email_1)): ?>
                        <strong><?= $dealer_address->email_1 ?></strong><br />
                    <?php endif; ?>
                    <?= $dealer_address->name ?><br />
                    <?= display_address($dealer_address) ?><br />
                </div>
                <div class="col-md-4 data-chunk">
                    <h2>Ship To</h2>
                    <?= display_address($shipping_address) ?>
                </div>
                <div class="col-md-4 data-chunk">
                    <h2>Customer</h2>
                    <?= $customer ? render_contact_info($customer, isset($customer->addr) ? $customer->addr : null) : 'No Customer'; ?>
                </div>
            <? } else { ?>
                <div class="col-md-4 data-chunk">
                    <h2>Customer</h2>
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
                        <th>Tubing</th>
                        <th>Special Geometry</th>
                        <th>Price</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="row">
        <div id="indow_totals" class="col-xs-offset-8 col-xs-4">
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

                <dt>Payments</dt>
                <dd>$ <?= money_format('%i', $totals['payments']) ?></dd>

                <dt>Balance Due</dt>
                <dd>$ <?= money_format('%i', $totals['due']) ?></dd>
                
		<?php if (isset($creator->group_id) && $creator->group_id != 1 && $creator->group_id != 1000 && $creator->group_id != 1170 ) { ?>
		<br>
                <dt>Prepayment</dt>
                <dd>$ <?= money_format('%i', round($totals['subtotal'] / 2)) ?></dd>
        <?php } ?>

            </dl>
        </div>
    </div>
    <div class="row" id="terms_footer">
        <div class="col-xs-12">
            <hr>
            <? if ($curstatus < 330): ?>
            <p>
                <strong>Terms</strong><br>
                <? if (!$order->freebird) { ?>
Please sign and confirm this order by typing your name in the 'Signature/Authorization' block below and click CONFIRM.
In doing so, you agree that all order details listed above are correct and hereby submit this order to Indow for manufacturing
and agree to abide by the payment terms outlined in the Dealer Agreement. <br/> If there are issues or updates needed with this
order, DO NOT click Confirm and reply to the Order Confirmation email you received. An Indow representative will respond and
re-send your Order Confirmation within 48 business hours.
                <? } else { ?>
Please sign and confirm this order by typing your name in the 'Signature/Authorization' block below and click CONFIRM.
In doing so, you agree that all order details listed above are correct and hereby submit this order to Indow for manufacturing
and agree to the standard terms & conditions of sale, available <a target="_blank" href="http://go.indowwindows.com/termsofsale">here</a>.<br><br>

If there are issues or updates needed with this order, DO NOT click Confirm.  Instead reply to the Order Confirmation email
you received or email <a href="mailto:comfort@indowwindows.com">comfort@indowwindows.com</a>. An Indow representative will respond and make any adjustments needed.
                <? } ?>
            </p>

            <form id="confirmform" method="post" class="row">
                <div class="col-xs-6">
                    <div class="form-inline">
                        <div class="form-group">
                            <label class="control-label" for="confirmation_sig"><strong>Signature/Authorization</strong> &nbsp;</label>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control input-sm" id="confirmation_sig" name="confirmation_sig" size="50" placeholder="First and Last Name">
                        </div>
                    </div>
                </div>
                <div class="col-xs-6">
                    <input id="submitpage" type="submit" value="Confirm" class="btn btn-blue pull-right">
                </div>
            </form>

            <? endif; ?>
        </div>
    </div>
</div>
