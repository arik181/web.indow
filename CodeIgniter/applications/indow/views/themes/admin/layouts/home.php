<!doctype html>
<html>
    <head profile="http://www.w3.org/2005/10/profile">
        <?php $this->load->view($this->config->item('admin_theme_header')); ?>
    </head>
        <body>
<br>
<br>
            <div class="row">
                <div class="">
                    <div class="col-md-2 sidebar col-md-offset-1">
                        <div class="col-md-12">
                            <?php $this->load->view('themes/admin/blocks/logo'); ?>
                        </div>
                        <?php
                        if($this->data['auth']){
                            $this->load->view($this->config->item('admin_theme_left_sidebar'));
                        }
                        ?>
                    </div>
                    <div class="col-md-8">
                        <h2 class="pull-left"><?php echo $manager;?></h2>
                        <span class="pull-left section-heading"><?php echo $section;?></span>
                        <?php
                            if($this->data['auth']){
                                $this->load->view($this->config->item('admin_theme_header_navigation'));
                            }
                        ?>
                    </div>
                    <div class="col-md-8 content" style="">
                        <?php $this->load->view($content); ?>
                    </div>
                </div><!-- End container -->
            </div><!-- End row -->
        <?php $this->load->view($this->config->item('admin_theme_footer')); ?>

<?php

    /**
     * Used for module specific JS plugins ( examples: image uploading, ckedit etc...)
     */

//    if(!empty($this->data['js_footer'])){
//        foreach($this->data['js_footer'] as $value){
//            foreach($value as $link){
//                echo "<script src='" .base_url($link) . "'></script>";
//            }
//        }
//    }

?>
    </body>
</html>
