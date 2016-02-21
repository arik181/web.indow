<header>
    <div class="container">
        <div class="row">
            <div class="col-xs-3 site-logo">
                <a href="<?php echo base_url();?>"><img src="<?php echo base_url('assets/theme/default/img/logo-indow.png');?>" class="img-responsive" alt="MODI Logo"/></a>
            </div>
            <div class="col-xs-4 text-right">
                <h1>Packing List</h1>
            </div>
            <div class="col-xs-5">
                <div class="pull-right" style="width: 120px;">Order #</div><br>
                <div id="order_number">
                    <span class="pull-right"><?= $order->id ?></span>
                </div>
                <p class="address text-right">
                    2267 N Interstate Ave | Portland OR | 97227<br />
                    503-284-2260 | www.indowwindows.com
                </p>
            </div>
        </div>
    </div>
</header>