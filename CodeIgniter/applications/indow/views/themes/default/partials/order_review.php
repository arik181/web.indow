<script>
    $(function () {
        var itable;
        function total_rows () {
            var data = $('#review_itemtable').DataTable().data();
            var totals = get_totals(data, undefined, 'order_review');
            write_totals(totals, {idprefix: 'order_review', no_allow_fees_remove: true});
        }
        $('.datepicker').datepicker({dateFormat: 'yy-mm-dd'});
        var review_options = {
            "dom": "<rt>",
            "pagingType": "full_numbers",
            "iDisplayLength": 5000,
            "language":{
                "emptyTable": 'There are no items associated with this order.',
            },
            "columnDefs": [{
                "targets": [],
                "orderable": false
            }],
            //"bSort": false,
            "columns": [
                {'data':'room'},
                {'data':'location'},
                {'data':'id'},
                {'data':'id'},
                {'data':'product_id'},
                {'data':'product_types_id'},
                {'data':'edging_id'},
                {'data':'special_geom'},
                {'data':'price'}
            ],
            "createdRow": function (row, data, index) {
                stylizeRow(row, data, index, false, itable, total_rows, 'order_review');
            }
        };
        $('#orderReview .glyphicon-info-sign').tooltip('hide');
        itable = $('#review_itemtable').DataTable(review_options);
    });
</script>
<div class="modal fade" id="orderReview" tabindex="-1" role="dialog" aria-labelledby="orderReviewLbel" aria-hidden="true">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-body">

                <div class="row">
                    <h2 class="col-xs-12">Review Order</h2>
                </div>
                <div class="row show-grid-sm">
                    <div class="col-xs-3 form-horizontal">
                        <div class="form-group">
                            <label class="col-xs-2 control-label" for="orderNumber">PO#</label>
                            <div class="col-xs-8">
                                <input type="text" class="form-control input-sm" id="orderNumber" />
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 form-inline">
                        <strong>Requested Ship Date</strong>
                        <input style="width: 100px;" class="datepicker input-sm form-control" id="review_commit_date">
                        <span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="right" title="Standard lead time is 3-4 weeks from the date the order is CONFIRMED. We will do our best to address orders with expedited date requests"></span>
                    </div>
                </div>

                <div class="row greybar">
                    <div class="col-md-4 data-chunk">
                        <h2>Dealer Location</h2>
                        <?= @$dealer_address->name ?><br />
                        <?= @$dealer_address->rep_name ?><br />
                        <?= display_address(@$dealer_address) ?><br />
                        <?php if(@$dealer_address->phone_1): ?>
                            <strong><?= @$dealer_address->phone_1 ?></strong><br />
                        <?php endif; ?>
<? /* <br><input type="checkbox" id="review_drop_ship"> Drop Ship */ ?>
                    </div>
                    <div class="col-md-4 data-chunk">
                        <? /*
                        <h2>Ship To</h2>
                        <?= display_address($shipping_address) ?> */ ?>
                        <?= @$ship_to ?>
                        <?= $this->load->view('/modules/orders/bundle_tool') ?>
                    </div>
                    <div class="col-md-4 data-chunk">
                        <h2>Customer Info</h2>
                        <div id="order_review_customer">
                        </div>
                    </div>
                </div>

                <div class="row bluebar show-grid">
                    <div class="col-xs-12">
                        <h2 class="barheader">Order Summary</h2>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12">
                        <table id="review_itemtable" class="display table table-hover" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Room</th>
                                    <th>Location</th>
                                    <th>Width</th>
                                    <th>Height</th>
                                    <th>Product</th>
                                    <th>Product Type</th>
                                    <th>Tubing</th>
                                    <th><span title="Special Geometry" class="special-geo">&#x22BE;</span></th>
                                    <th>MSRP</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-offset-8 col-xs-4">
                        <?= $this->load->view('themes/default/partials/totals', array('idprefix' => 'order_review')) ?>
                    </div>
                </div>
                <div class="row show-grid-md">
                    <hr />
                    <div class="col-xs-12">
                        <textarea id="orderNotes" class="form-control" rows="5" placeholder="Order Notes"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <button id="orderReviewSave" type="button" class="btn btn-default">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
