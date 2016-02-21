<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function generate_ship_to($customer_id, $data, $customer, $keys, $address_options, $with_customer_manager=false, &$mode='default')
{
    $html = '';
    ob_start();
    ?>
    <div id="contact_info_view_" class="info_view">
        <?
        $html = ob_get_clean();
        ob_start();
        ?>
        <form id="editSiptoForm" method="post">
            <input type='hidden' id='customer_id' name='customer_id' value='<?= $customer_id ?>'>
            <div class='form-group'>
                <label for='address' class='control-label'>Select Shipping Address</label>
                <?= form_dropdown('address',$address_options, false, "id='shipping_addresses' class='form-control input-sm'");?>
            </div>
            <div class='form-group text-center'>
                <input id="save_ship_to" type='submit' name='submit' value='Save' class='btn btn-blue input-sm'>
            </div>
        </form>
        <?
        $edit_form = str_replace('"', "'", ob_get_clean());
        if ( isset($data['edit_link']))
        {
            ob_start();
            ?>
            <?php if($mode != 'view'): ?>
                <a id="customer_edit_btn" href="<?= $data['edit_link'] ?>" class="btn btn-blue btn-sm pull-right" data-toggle="popover" data-placement="right" data-content="<?= $edit_form ?>">Edit</a>
            <?php endif; ?>
            <script>
                var indow_shipping_address = <?= !empty($customer['id']) ? json_encode($customer['id']) : 'null' ?>;
                var indow_ship_to_list = {};
                var shipping_dealer_id = <?= !empty($data['dealer_id']) ? $data['dealer_id'] : 0 ?>;
                var popoverOptions = {
                    html: true,
                    title: function () {
                        return $(this).parent().find(".head").html();
                    },
                    content: function () {
                        return $(this).parent().find(".content").html();
                    }
                };
                <? if ($with_customer_manager) { ?>
                popoverOptions.callback = function() {
                    var ids = [];
                    if (window.customer_manager_get_customers) {
                        $.each(customer_manager_get_customers(), function (i, e) {
                            ids.push(e.id)
                        });
                    } else if (window.indow_customer_ids) {
                        ids = indow_customer_ids;
                    }
                    var ship_list = $('#shipping_addresses').html('').prop('disabled', 1);
                    if (window.customer_manager_get_primary) {
                        var primary = customer_manager_get_primary() ? customer_manager_get_primary() : 0;
                    }
                    $.get('/customers/getAddresses/?ids=' + ids.join() + '&dealer=' + shipping_dealer_id, function (addresses) {
                        ship_list.prop('disabled', 0);
                        $.each(addresses, function (i, e) {
                            ship_list.append('<option value="' + e.id + '">' + e.address + ' ' + e.address_ext + ', ' + e.city + ', ' + e.state + ' ' + e.zipcode + '</option>');
                            indow_ship_to_list[e.id] = e;
                        });
                        ship_list.val(indow_shipping_address);
                    });
                    $('#editSiptoForm').submit(function (e) {
                        e.preventDefault();
                        if (ship_list.val() !== null) {
                            var address = indow_ship_to_list[ship_list.val()];
                            indow_shipping_address = ship_list.val();
                            $('#ship_to_line1').text(address.address + ' ' + address.address_ext);
                            $('#ship_to_line2').text(address.city + ', ' + address.state + ' ' + address.zipcode);
                            if (window.indow_module === 'freebird') {
                                $.post('/orders/update_shipping_addres/' + order_id + '/' + indow_shipping_address);
                            }
                        }
                        $(this).closest('.popover').prev().click() // close popover
                    });
                };
                <? } ?>
                $("a[data-toggle=popover]").popover(popoverOptions);
            </script>
            <?
            $edit_form = ob_get_clean();
        } else {
            $edit_form = '';
        }

        ob_start();
        ?>
        <div>
            <? if (!empty($data['order_review'])) { ?>
                <h2 class="inline"><?= $data['title'] ?></h2>
            <? } else { ?>
                <h4 class="inline"><?= $data['title'] ?></h4>
            <? } ?>
            <?= $edit_form ?>
        </div>
        <div class='show-grid'>
            <div id="ship_to_line1"><?= @$customer['address'] ?> <?= @$customer['address_ext'] ?></div>
            <div id="ship_to_line2"><?= $customer ? $customer['city'] . ', ' . $customer['state'] . ' ' . $customer['zipcode'] : '' ?></div>
        </div>
    </div>
    <?
    $html .= ob_get_clean();
    return $html;
}

?>
