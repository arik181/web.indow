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
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/plugins/anythingslider/carousel.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/plugins/font-awesome/css/font-awesome.min.css');?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/css/bootstrap-theme.css');?>"/>
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.0/css/jquery.dataTables.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/theme/default/css/style.css');?>"/>
<script src="<?php echo base_url('assets/theme/default/js/jquery.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/bootstrap.min.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/jquery-ui.min.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/tooltip.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/js/bootstrap.js');?>"></script>
<script src="<?php echo base_url('assets/theme/default/plugins/anythingslider/carousel-plugin.js');?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/theme/default/js/training.js');?>"></script>
<script type="text/javascript" src="//cdn.sublimevideo.net/js/86u7lty9.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>

<!-- <script type="text/javascript" src="assets/js/flot/jquery-1.8.3.min.js"></script>       -->
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/js/flot/excanvas.min.js"></script><![endif]-->   
<script type="text/javascript" src="<?php echo base_url('assets/js/flot/jquery.flot.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/flot/jquery.flot.pie.js');?>" ></script>

<!--<link href="//vjs.zencdn.net/4.5/video-js.css" rel="stylesheet">-->
<!--<script src="//vjs.zencdn.net/4.5/video.js"></script>-->
