<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="follow, index">
<title> <?php echo $this->config->item('site_name'); ?></title>
<meta name="description" content="<?php echo $this->config->item('meta_description'); ?>">
<meta name="keywords" content="<?php echo $this->config->item('meta_keywords'); ?>">
<link rel="icon" type="image/png" href="<?php echo base_url('favicon.ico?v=2'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/admin/css/jquery-ui.css');?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/admin/css/bootstrap.min.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/admin/css/bootstrap-theme.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/mfmh/plugins/font-awesome/css/font-awesome.min.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/admin/css/style.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css');?>"/>

<script src="<?php echo base_url('assets/theme/admin/js/jquery.js');?>"></script>
<script src="<?php echo base_url('assets/theme/admin/js/jquery-ui.min.js');?>"></script>
<script src="<?php echo base_url('assets/theme/admin/js/bootstrap.min.js');?>"></script>
<script src="<?php echo base_url('assets/theme/admin/plugins/type-ahead/js/typeahead.min.js');?>"></script>
<script src="<?php echo base_url('assets/theme/admin/plugins/type-ahead/js/hogan-2.0.0.min.mustache.js');?>"></script>
<script type="text/javascript" src="//cdn.sublimevideo.net/js/86u7lty9.js"></script>
<script src="<?php echo base_url('assets/plugins/jquery-sortable.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootbox.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js'); ?>"></script>

<?php

    /**
     * Used for module specific JS plugins ( examples: image uploading, ckedit etc...)
     */

    if(!empty($this->data['js_header'])){
        foreach($this->data['js_header'] as $value){
            foreach($value as $link){
                echo "<script src='" .base_url($link) . "'></script>";
            }
        }
    }

?>
