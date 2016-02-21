<!doctype html>
<html>
<head profile="http://www.w3.org/2005/10/profile">
    <?php $this->load->view($this->config->item('theme_header')); ?>
</head>
<body>

<div class="container">

    <div class="row">
        <div class="container">
            <div class="col-xs-12 site-header">
                <div class="col-xs-3 site-logo">
                    <?php $this->load->view('themes/default/blocks/logo'); ?>
                </div>
                <div class="col-xs-8 col-xs-offset-1">
                    <?php $this->load->view('themes/default/blocks/header-links'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="container">
            <div class="col-xs-12 site-main-nav">
                <div class="col-xs-12">
                    <?php $this->load->view('themes/default/blocks/main-links'); ?>
                </div>
            </div>
        </div>
    </div>

    <br />

    <div class="row">
        <div class="container">
            <div class="col-xs-4 site-left-sidebar well">
                <?php $this->load->view('themes/default/partials/sidebar'); ?>
            </div>
            <div class="col-xs-8">
                <?php if (isset($message)): ?>
                    <div class="well" id="flash_data">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                <div class="panel panel-default sight-right-col-xs-content">
                    <?php $this->load->view($content); ?>
                </div>
            </div>
         </div>
    </div>


</div>

<footer>
    <div class="container">
        <nav id="footer_nav" class="row">
            <? $this->load->view('themes/default/blocks/footer-nav');?>
        </nav>
    </div>
    <? $this->load->view($this->config->item('theme_footer')); ?>
</footer>

</body>
</html>
