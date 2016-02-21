<?php

// Site Name - Used for Templates

$config['title']                   = 'IndowWindows - MODI';
$config['site_name']               = 'IndowWindows - MODI';
$config['meta_author']             = 'Kelly';
$config['meta_description']        = 'IndowWindows - MODI';
$config['meta_keywords']           = 'IndowWindows - MODI';

/**  SET THEME **/

$config['theme']                   = 'default';

/** PAGE TYPES **/

$config['theme_home']              = 'themes/' . $config['theme'] . '/layouts/home';
$config['theme_stub']              = 'themes/' . $config['theme'] . '/layouts/stub';
$config['theme_print']             = 'themes/' . $config['theme'] . '/layouts/print';
$config['theme_print_label']       = 'themes/' . $config['theme'] . '/layouts/print-label';
$config['theme_print_product_label'] = 'themes/' . $config['theme'] . '/layouts/print-product-label';

/** PARTIALS **/

$config['theme_header']            = 'themes/' . $config['theme'] . '/partials/header';
$config['theme_header_print']      = 'themes/' . $config['theme'] . '/partials/header_print';
$config['theme_footer']            = 'themes/' . $config['theme'] . '/partials/footer';
$config['theme_list']              = 'themes/' . $config['theme'] . '/partials/list';
$config['theme_dashboard']         = 'themes/' . $config['theme'] . '/partials/dashboard';
$config['theme_header_navigation'] = 'themes/' . $config['theme'] . '/partials/navigation';
$config['theme_left_sidebar']      = 'themes/' . $config['theme'] . '/partials/left-sidebar';
$config['theme_right_sidebar']     = 'themes/' . $config['theme'] . '/partials/left-sidebar';
