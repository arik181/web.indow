<!doctype html>
<html>
    <head profile="http://www.w3.org/2005/10/profile">
        <? $this->load->view($this->config->item('theme_header_print')); ?>
    </head>
    <body>
        <header>
            <div class="container">
                <div class="row">
                    <div class="col-xs-3 site-logo">
                        <a href="<?php echo base_url();?>"><img src="<?php echo base_url('assets/theme/default/img/logo-indow.png');?>" class="img-responsive" alt="MODI Logo"/></a>
                    </div>
                    <div class="col-xs-4 text-right">
                        <h1>Packing List</h1>
                    </div>
                    <div class="col-xs-5 text-right">
                        <div id="order_number">
                            <span class="pull-right"><?= $order->id ?></span>
                        </div>
                        <p class="address">
                            2267 N Interstate Ave | Portland OR | 97227<br />
                            503-284-2260 | www.indowwindows.com
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <main class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="content">
                        <div class="content-row">
                        <?php if (isset($message)): ?>
                            <div class="well" id="flash_data">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        </div>
                        <? $this->load->view($content); ?>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>

