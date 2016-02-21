<!doctype html>
<html>
    <head profile="http://www.w3.org/2005/10/profile">
        <? $this->load->view($this->config->item('theme_header')); ?>
    </head>
    <body>
        <header class="site-header">
            <div class="container">
                <div class="row">
                    <div class="col-xs-2 site-logo">
                        <? $this->load->view('themes/default/blocks/logo'); ?>
                    </div>
                    <div class="col-xs-5">
                        <p class="pull-left">by Indow</p>
                    </div>
                    <div class="col-xs-5">
                        <? $this->load->view('themes/default/blocks/header-links'); ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="container">
            <div class="row">
                <div id="side_nav" class="col-xs-2">
                    <? $this->load->view('themes/default/blocks/left-sidebar-links'); ?>
                </div>
                <div id="dashboard_view" class="col-xs-10">
                    <? $this->load->view('themes/default/blocks/page-title'); ?>
                    <div class="content">
                        <!-- <div class="content-row">
                            <? if (isset($subtitle)): ?>
                            <h3 class="inline subtitle"><?= $subtitle ?></h3>
                            <? endif; ?>
                        </div> -->

                        <div class="content-row">
                        <?php if (isset($message)): ?>
                            <div class="well" id="flash_data">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        </div>
                        <? $this->load->view($content); ?>
                    </div>
                    <div class="content-row"></div>
                </div>
            </div>
        </div>
        <? $this->load->view($this->config->item('theme_footer')); ?>
    </body>
</html>

