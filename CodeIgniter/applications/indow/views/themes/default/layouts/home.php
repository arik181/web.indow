<!doctype html>
<html>
<head profile="http://www.w3.org/2005/10/profile">
    <? $this->load->view($this->config->item('theme_header')); ?>
</head>
<body>
    <? if (!$this->permissionslibrary->eula_accepted() && !$this->permissionslibrary->is_customer()) {
        echo $this->load->view('themes/default/partials/eula');
    } ?>

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
            <div id="side_nav" class="col-xs-2 content">
                <? $this->load->view('themes/default/blocks/left-sidebar-links'); ?>
            </div>
            <div id="content_view" class="col-xs-10">
                <? $this->load->view('themes/default/blocks/page-title'); ?>
                <div class="content">
                <? $this->load->view($content); ?>
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
