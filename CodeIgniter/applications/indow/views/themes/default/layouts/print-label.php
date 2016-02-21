<!doctype html>
<html>
    <head profile="http://www.w3.org/2005/10/profile">
        <? $this->load->view($this->config->item('theme_header_print')); ?>
    </head>
    <body>
        <main class="container">
            <div class="row">
                <div class="col-xs-12">
                    <? $this->load->view($content); ?>
                </div>
            </div>
        </main>
    </body>
</html>

