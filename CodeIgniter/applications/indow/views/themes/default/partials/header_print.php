<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="follow, index">
<title> <?php echo $this->config->item('site_name'); ?></title>

<?php if(isset($page)):?>

<meta name="description" content="<?php echo $page->description;?>">
<meta name="keywords" content="<?php echo $page->keywords;?>">

<?php else:?>

<meta name="description" content="<?php echo $this->config->item('meta_description'); ?>">
<meta name="keywords" content="<?php echo $this->config->item('meta_keywords'); ?>">

<meta itemprop="description" content="<?php echo $this->config->item('meta_description'); ?>"/>

<?php endif;?>

<link rel="icon" type="image/png" href="<?php echo base_url('favicon.ico'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/css/jquery-ui.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/css/bootstrap.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/plugins/font-awesome/css/font-awesome.min.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/css/bootstrap-theme.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/css/print.css');?>"/>
<script src="<?php echo base_url('assets/theme/default/js/jquery.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/bootstrap.min.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/jquery-ui.min.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/tooltip.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/bootstrap.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/cut_image.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/fulfillment_print.js');?>"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>
<script src="<?php echo base_url('assets/theme/default/js/JsBarcode/EAN_UPC.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/JsBarcode/CODE128.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/JsBarcode/JsBarcode.js');?>"></script>