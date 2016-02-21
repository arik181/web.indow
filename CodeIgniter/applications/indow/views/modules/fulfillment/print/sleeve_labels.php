<style>
    .label_cont {
        width: 4in;
        height: 3in;
        margin: 0px auto;
        box-sizing: border-box;
        padding: 10px;
    }
</style>
<div class="page_cont">
    <div class="label_cont">
        <div class="row show-grid-md">
            <div class="col-xs-6">
                <img src="<?php echo base_url('assets/theme/default/img/logo-indow.png');?>" class="img-responsive logo-indow" alt="MODI Logo"/>
            </div>
            <div class="col-xs-6 text-right">
                <div class="order_number">
                    <?= @$order->last_name . '_' . @$order->id ?>
                </div>
            </div>
        </div>

        <div class="row location show-grid-sm">
            <dl class="row">
                <dt class="col-xs-offset-2 col-xs-4">Room Name:</dt>
                <dd class="col-xs-6"><?= @$item->room ?></dd>

                <dt class="col-xs-offset-2 col-xs-4">Location:</dt>
                <dd class="col-xs-6"><?= @$item->location ?></dd>
            </dl>
        </div>

        <div class="row">
            <div class="col-xs-12 text-center">
                <h4>Warning!</h4>
                <p>To avoid danger of suffocation, keep bags out of the
                reach of children</p>
            </div>
        </div>
    </div>
</div>
