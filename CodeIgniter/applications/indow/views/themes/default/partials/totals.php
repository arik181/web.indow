<?
if (!isset($idprefix)) {
    $idprefix = '';
}
if (!isset($module)) {
    $module = null;
}
?>

<style>
    .wholesale_discount_toggle {
        display: none;
    }
</style>

<dl id="<?= $idprefix ?>indow_totals" class="indow_totals dl-horizontal dl-totals">
    <dt><?= $module == 'orders' ? 'Order Subtotal' : 'Subtotal' ?></dt>
    <dd id="<?= $idprefix ?>subtotal">$0.00</dd>

    <dt class="spec_geom_toggle">Special Geometry (<span id="<?= $idprefix ?>spec_geom_count">0</span>)</dt>
    <dd class="spec_geom_toggle" id="<?= $idprefix ?>spec_geom_fee">$0.00</dd>

    <dt id="<?= $idprefix ?>fees_end" class="wholesale_discount_toggle">WS Discount</dt>
    <dd class="wholesale_discount_toggle" id="<?= $idprefix ?>wholesale_discount">$0.00</dd>

    <dt style="font-size: 1.1em"><?= $module == 'orders' ? 'Order Total' : 'Total' ?></dt>
    <dd style="font-size: 1.1em" id="<?= $idprefix ?>grand_total">$0.00</dd><br>

    <dt class="paymenttoggle">Payments</dt>
    <dd class="paymenttoggle" id="<?= $idprefix ?>payments">$0.00</dd>

    <dt class="paymenttoggle">Balance Due</dt>
    <dd class="paymenttoggle" id="<?= $idprefix ?>balance">$0.00</dd>            
</dl>
