<html><head profile="http://www.w3.org/2005/10/profile">
    <meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="follow, index">
<title> IndowWindows - MODI</title>


<meta name="description" content="IndowWindows - MODI">
<meta name="keywords" content="IndowWindows - MODI">

<meta itemprop="description" content="IndowWindows - MODI">


<link rel="icon" type="image/png" href="<?php echo base_url('favicon.ico?v=2'); ?>">
<link rel="stylesheet" type="text/css" href="/assets/theme/default/css/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="/assets/theme/default/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="/assets/theme/default/plugins/anythingslider/carousel.css">
<link rel="stylesheet" type="text/css" href="/assets/theme/default/plugins/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/assets/theme/default/css/bootstrap-theme.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.0/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/theme/default/css/style.css">
<script src="/assets/theme/default/js/jquery.js"></script><style type="text/css"></style><style type="text/css"></style>
<script src="/assets/theme/default/js/bootstrap.min.js"></script>
<script src="/assets/theme/default/js/jquery-ui.min.js"></script>
<script src="/assets/theme/default/js/tooltip.js"></script>
<script src="/assets/theme/default/js/bootstrap.js"></script>
<script src="/assets/theme/default/plugins/anythingslider/carousel-plugin.js"></script>
<script type="text/javascript" src="//cdn.sublimevideo.net/js/86u7lty9.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/1.10.0/js/jquery.dataTables.js"></script>

<!-- <script type="text/javascript" src="assets/js/flot/jquery-1.8.3.min.js"></script>       -->
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="/js/flot/excanvas.min.js"></script><![endif]-->   
<script type="text/javascript" src="/assets/js/flot/jquery.flot.js"></script>
<script type="text/javascript" src="/assets/js/flot/jquery.flot.pie.js"></script>

<!--<link href="//vjs.zencdn.net/4.5/video-js.css" rel="stylesheet">-->
<!--<script src="//vjs.zencdn.net/4.5/video.js"></script>-->
<style>
    body {
        background: none;
    }
    .padtop {
        margin-top: 30px;
    }
</style>
</head>
<body>

<header class="site-header">
    <div class="container">
        <div class="row">
            <? if (empty($this->data['customer_header'])) { ?>
            <div class="col-md-2 site-logo">
                <a href="/"><img src="/assets/theme/default/img/logo.png" alt="MODI Logo"></a>
            </div>
            <div class="col-md-5">
                <p class="pull-left">by Indow</p>
            </div>
            
            <div class="col-md-5"></div>
            <? } else { ?>
                <div class="headertext"><span class="header_measure indow_blue">MEASURE</span> by Indow<sup> &reg;</sup></div>
            <? } ?>
        </div>
    </div>
</header>

<div class="container">
    <div class="row padtop">
        <h2 class="fwpagetitle"><?= $title ?></h2>
    </div>
</div>

<? if (!empty($content)) {
    $this->load->view($content);
}?>

<div id="qe_dialog"></div>



<footer>
    <div class="container">
        <nav id="footer_nav" class="row">
                    </nav>
    </div>
    <div class="container">
    </div>
<script src="/assets/theme/default/js/carousel.js"></script>
<script src="/assets/theme/default/js/sidebar.js"></script>
<script src="/assets/theme/default/js/main.js"></script>
<script src="/assets/theme/default/js/notes.js"></script>
<script src="/assets/theme/default/js/associated_customers.js"></script>
<script src="/assets/theme/default/js/tabbar.js"></script>
<script src="/assets/theme/default/js/contactinfo.js"></script>
</footer>


</body></html>
