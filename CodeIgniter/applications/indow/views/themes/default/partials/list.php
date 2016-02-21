<!doctype html>
<html>
<head profile="http://www.w3.org/2005/10/profile">
    <? $this->load->view($this->config->item('theme_header')); ?>
</head>
<body>

<header class="site-header">
    <div class="container">
        <div class="row">
            <div class="col-md-2 site-logo">
                <? $this->load->view('themes/default/blocks/logo'); ?>
            </div>
            <div class="col-md-5">
                <p class="pull-left">by Indow</p>
            </div>
            <div class="col-md-5">
                <? $this->load->view('themes/default/blocks/header-links'); ?>
            </div>
        </div>
    </div>
</header>

<div class="container">
    <div class="row">
        <nav id="side_nav" class="col-md-2 content">
            <? $this->load->view('themes/default/blocks/left-sidebar-links'); ?>
        </nav>
        <div id="list_view" class="col-md-10 ">
            <? $this->load->view('themes/default/blocks/page-title'); ?>
            <div class="content quick-estimates-btns">
                <ul id="topbuttons" class="list-inline text-right show-grid-md">
                    <? if (isset($qe_button))
                    {
                        ?>
                        <li><a href="<?= $add_path ?>" class="btn btn-blue btn-sm"><?= $add_button ?></a></li>
                        <li id="qecont" class="btn-group" data-toggle="popover" data-placement="bottom" data-content="<?=$quick_estimate?>" data-original-title="" title="">
                            <button id="qe_button" class="btn btn-blue btn-sm">Quick Estimate</button>
                            <button class="btn btn-dark-blue btn-sm"><i class="fa fa-chevron-right"></i></button>
                            <script>
                                $("li[data-toggle=popover]").popover({
                                    html: true,
                                    title: function () {
                                        return $(this).parent().find(".head").html();
                                    },
                                    content: function () {
                                        return $(this).parent().find(".content").html();
                                    }
                                });
                            </script>
                        </li>
                    <?
                    } else {
                        if (isset($add_button)): ?>
                            <li><a href="<?= $add_path ?>" class="btn btn-blue btn-sm"><?= $add_button ?></a></li>
                        <? endif;
                    } ?>
                </ul>
            </div>

            <div class="content-row">
                <?php if (isset($message)): ?>
                    <div class="well" id="flash_data">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <? $this->load->view($content); ?>
            </div>
        </div>
    </div>
</div>
</div>
<div id="qe_dialog"></div>
<script type="text/javascript">
    function update_list()
    {
        /*
         show_option = $("#show_form option:selected").text();
         $.ajax({
         type : "POST",
         url  : "/users/update_list",
         data : { entries : show_option }
         })
         .done(function(data) {
         data = JSON.parse(data);
         rows = $('tr');
         //rows.each(replace_row());
         //console.log( $('tbody').html());
         console.log( data );
         //console.log( data.first_row );
         });

         function replace_row()
         {
         //$(this).find('a').attr();
         $(this).find('#group_name').text();
         $(this).find('#first_name').text();
         $(this).find('#last_name').text();
         $(this).find('#zipcode_1').text();
         $(this).find('#username').text();
         }
         */
        var form = $('#show_form');
        form.submit();

    }

    function qe_open(object)
    {
        $("#qe_dialog").load('/estimates/quick_estimate').dialog(
            {
                modal:true,
                width: 1000,
                height: 400,
                position: { my: "left top", at: "right bottom", of: "#qe_button" }
            }
        );

    }

    function qe_close(element)
    {
        //$('#qe_dialog').dialog('close');
        $(".ui-dialog").hide();
        $(".ui-widget-overlay").hide();
    }

</script>
<footer>
    <div class="container">
        <nav id="footer_nav" class="row">
            <? $this->load->view('themes/default/blocks/footer-nav'); ?>
        </nav>
    </div>
    <? $this->load->view($this->config->item('theme_footer')); ?>
</footer>
</body>
</html>
