<div class="modal fade" id="addToOrder" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-full" style="width: 500px;">
        <div class="modal-content">
            <div class="modal-body">

                <div class="row">
                    <h2 class="col-xs-12">Add Items to Order</h2>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <table id="review_itemtable" class="display table table-hover condensed dataTable no-footer" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Add</th>
                                </tr>
                            </thead>
                            <tbody>
                            <? if (@$site_orders) {
                                foreach ($site_orders as $order) { ?>
                                    <tr>
                                        <td><a href="/orders/edit/<?= $order->id ?>"><?= $order->id ?></a></td>
                                        <td><?= $order->customer ?></td>
                                        <td><button class="btn btn-blue btn-sm add_items_to_order" data-id="<?= $order->id ?>" type="button">Add</button></td>
                                    </tr>
                                <? }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
