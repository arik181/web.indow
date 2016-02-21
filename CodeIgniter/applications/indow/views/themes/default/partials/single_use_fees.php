<style>
    #one_off_fees_form > div {
        padding-left: 3px;
        padding-right: 3px;
    }
    #one_off_fees_form select, #one_off_fees_form input {
        padding-left: 3px;
        padding-right: 3px;
    }
    .delete-modifier {
        display: inline;
    }
</style>
<script>
    var indow_user_fees = <?= !empty($user_fees) ? json_encode($user_fees) : '{}' ?>;
    var indow_delete_user_fees = [];
    function update_user_fees() {
        if (indow_module === 'estimates') {
            est_total_rows();
        } else {
            if (current_total_rows) {
                current_total_rows();
            }
        }
    }
    $(function () {
        $('#submit_one_off_fee').click(function () {
            var item = {};
            var valid = true;
            var timestamp = new Date().getTime();
            var new_id = 'new_' + timestamp;
            var inputs = $('#one_off_fees_form').find('input, select');
            inputs.each(function () {
                var val = $(this).val();
                if (!$(this).val()) {
                    balert('All fields are required.');
                    valid = false;
                    return false;
                }
                item[$(this).attr('name')] = $(this).val();
            });
            if (valid) {
                indow_user_fees[new_id] = item;
                inputs.each(function () {
                    if ($(this).attr('name') === 'quantity') {
                        $(this).val(1);
                    } else {
                        $(this).val('');
                    }
                });
            }
            update_user_fees();
        });
        $('#indow_totals').on('click', '.delete-modifier', function () {
            var id = $(this).data('id');
            delete indow_user_fees[id];
            indow_delete_user_fees.push(id);
            update_user_fees();
            if (window.indow_save_fees && window.indow_module_obj) {
                indow_save_fees.call(indow_module_obj);
            }
        });
        update_user_fees();
    });
</script>
<div class="row">
    Add Fee or Discount
</div>
<div id="one_off_fees_form" class="row">
    <div class="col-xs-2">
        <?= form_dropdown('modifier_type', array('fee' => 'Fee', 'discount' => 'Discount'), '', 'class="form-control input-sm"') ?>
    </div>
    <div class="col-xs-1">
        <?= form_dropdown('modifier', array('dollar' => '$', 'percent' => '%'), '', 'class="form-control input-sm input-modifier-small"') ?>
    </div>
    <div class="col-xs-5">
        <input name="description" placeholder="Description" class="form-control input-sm">
    </div>
    <div class="col-xs-1">
        <input name="quantity" placeholder="qty" class="form-control input-sm">
    </div>
    <div class="col-xs-2">
        <input name="amount" placeholder="amount" class="form-control input-sm">
    </div>
    <div class="col-xs-1">
        <button id="submit_one_off_fee" class="btn btn-black btn-sm btn-default">Apply</button>
    </div>
</div>
