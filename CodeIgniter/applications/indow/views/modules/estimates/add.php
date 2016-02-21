<?php if (isset($message)): ?>
    <div class="well" id="flash_data">
        <?php echo $message; ?>
    </div>
<?php endif; ?>
<script type="text/javascript">
    $(document).ready(function(){
        var calcSquareFt = function(){
            var pricePerSqft = 25 //TODO, use a non made-up number here
            width = $(".width_input[data-row=" + $(this).data('row') + "]");
            height = $(".height_input[data-row=" + $(this).data('row') + "]");
            sqft = $(".squareFeet[data-row=" + $(this).data('row') + "]");
            price = $(".price[data-row=" + $(this).data('row') + "]");
            sqft_val = parseFloat(width.val()/12 * height.val() /12).toFixed(2);
            price_val = parseFloat(sqft_val * pricePerSqft).toFixed(2);
            sqft.html(sqft_val);
            price.html("$" + price_val);
        }
        $('.width_input').keyup(calcSquareFt);
        $('.height_input').keyup(calcSquareFt);
        $('#newcust').popover({
            html: true,
            content: $('#newcustdiv'),
            placement: 'bottom'
        });
        $('#cancelnewcust').click(function (e) {
            e.preventDefault();
            $('#newcust').popover('hide');
            $('#newcustform')[0].reset();
        });
        $('#newcustform').submit(function (e) {
            var form = this;
            e.preventDefault();
            var formdata = $(this).serialize() + '&ajax=1';
            $.post('/customers/add', formdata, function(data) {
                if (data.success) {
                    form.reset();
                    $('#newcust').popover('hide');
                    contactinfo_updatedata(data.userid);
                } else {
                }
                run_flash(data.message);
            }, 'json');
        });

    });
</script>
<div class="row">
    <div class="col-xs-12">
        <ul class="list-inline text-right">
            <li><a href="/mapp" class="btn btn-blue btn-sm">Measure Job</a></li>
            <li><a href="/discounts" class="btn btn-blue btn-sm">Fees &amp; Discounts</a></li>
            <li><a href="/export" class="btn btn-blue btn-smt">Estimate Pkg</a></li>
        </ul>
    </div>
</div>
<div class="row form-inline">
    <div class="col-xs-12 text-right form-group">
        <label class="control-label" for="goTo">Go to &nbsp;</label>
        <select class="form-control input-sm" id="goTo" name="goTo">
            <option>Saved Estimates</option>
        </select>
    </div>
</div>
<div id="clonecont">
    <div id="newcustdiv">
        <?= $this->load->view('modules/customers/addform'); ?>
    </div>
</div>

<div class="row form-inline">
    <div class="col-xs-12" id="selectcustomer">
        <h3>Customer Info</h3>
        <?= $this->load->view('/modules/estimates/ajax_search'); ?>
        OR
        <a id="newcust" href="#" class="btn btn-blue btn-sm btn-content">New Customer</a>
        <div id="contact_info_view"></div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 text-right">
        <button type="button" class="btn btn-blue btn-sm">Sort</button>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <table id="itemsTable" class="table table-hover">
            <thead>
                <tr>
                    <th><input type="checkbox" name="all"></th>
                    <th>Room</th>
                    <th>Location</th>
                    <th>Width</th>
                    <th>Height</th>
                    <th>Product</th>
                    <th>Product Type</th>
                    <th>Edging</th>
                    <th>Special Geom</th>
                    <th>Retail</th>
                    <th>Sq. Ft</th>
                    <th>Product</th>
                </tr>
            </thead>
            <tbody>

            <tr id="prime">
                <td content="item_id"><input type="checkbox" name="items[]" value=""/></td>
                <td content="room"><input type="text" name="room"/></td>
                <td content="location"><input type="text" name="location[]"/></td>
                <td content="width"><input type="text" class="width_input small_input" name="width[]" data-row="0" size="4" />
                </td>
                <td content="height"><input type="text" class="height_input small_input" name="height[]" data-row="0" size="4" /></td>
                <td content="product">
                    <select name='product[]'>
                        <?php
                        foreach ($products as $product) {
                            ?>
                            <option value='<?= $product->id ?>'><?= $product->name ?></option>
                        <?
                        }
                        ?>
                    </select>
                </td>
                <td content="product_type">
                    <select name='product_type[]' class='product_type'>
                        <?php
                        unset($product);
                        foreach ($product_types as $product) {
                            ?>
                            <option value='<?= $product->id ?>'
                                    data-cost='<?= $product->cost ?>'><?= $product->name ?></option>
                        <?
                        }
                        ?>
                    </select>
                </td>
                <td content="edging">
                    <select name='edging[]'>
                        <?php
                        unset($product);
                        foreach ($edging as $product) {
                            ?>
                            <option value='<?= $product->id ?>'><?= $product->name ?></option>
                        <?
                        }
                        ?>
                    </select>
                </td>
                <td content="geometry"><input type="checkbox" name="spec_geo"></td>
                <td content="retail" class="price" data-row="0">$0</td>
                <td content="sqft" class="squareFeet" data-row="0">0</td>
                <td content="product"><input type="button" name="submit" value="+PD" class="btn btn-blue btn-sm"/></td>
            </tr>

            </tbody>
        </table>

        <div class="row">
            <div class="col-xs-7">
                <input type="button" name="submit" value="Add Window" class="btn btn-blue btn-sm"/>

                <h5>With Selected</h5>
                <input type="button" name="submit" value="Delete" class="btn btn-blue btn-sm"/>
                <input type="button" name="submit" value="Create New Estimate" class="btn btn-blue btn-sm"/>

                <h5>Mass Editing</h5>

                <div class="form-inline">
                    <div class="form-group">
                        <label class="sr-only" for="product">Email address</label>
                        <select class="form-control input-sm" id="product" name='product[]'>
                            <?php
                            foreach ($products as $product) {
                                ?>
                                <option value='<?= $product->id ?>'><?= $product->name ?></option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="product_type">Email address</label>
                        <select class="form-control input-sm" id="product_type" name='product_type[]'>
                            <?php
                            unset($product);
                            foreach ($product_types as $product) {
                                ?>
                                <option value='<?= $product->id ?>'
                                        data-cost='<?= $product->cost ?>'><?= $product->name ?></option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="sr-only" for="edging">Email address</label>
                        <select class="form-control input-sm" id="edging" name='edging[]'>
                            <?php
                            unset($product);
                            foreach ($edging as $product) {
                                ?>
                                <option value='<?= $product->id ?>'><?= $product->name ?></option>
                            <?
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-blue btn-sm">Submit</button>
                    </div>
                </div>
            </div>
            <div class="col-xs-5">
                <dl class="dl-horizontal">
                    <dt>Subtotal</dt>
                    <dd>$00.00</dd>

                    <dt>Fees</dt>
                    <dd>$00.00</dd>

                    <dt>Grand Total</dt>
                    <dd>$00.00</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <hr />
        <div class="inline">
            <input type="submit" name="submit" value="Save" class="btn btn-blue btn-sm"/>
        </div>
        <label class="inline">
            <input type="checkbox"> Follow Up
        </label>
        <a href="/estimates" class="btn btn-blue btn-sm pull-right">Cancel</a>
    </div>
</div>

<script src="<?php echo base_url('assets/theme/default/js/delete_confirmer.js'); ?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/main.js'); ?>"></script>
