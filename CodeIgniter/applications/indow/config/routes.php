<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

// Defaults
$route['default_controller']                    = 'dashboard';
$route['404_override']                          = 'customerrors/index/404';

// Profile
$route['profile'] = 'users/profile';
$route['group/profile'] = 'GroupProfiles';


//$route['seed/groups'] = 'seeders/groupsseeder/index';
$route['seed/users']     = 'seeders/usersseeder/index';
$route['seed/sites']     = 'seeders/sites_seeder/index';
$route['seed/estimates'] = 'seeders/estimate_seeder/index';
$route['seed/quotes']    = 'seeders/quote_seeder/index';
$route['seed/orders']    = 'seeders/order_seeder/index';
$route['migrate']        = 'migrate/index';
$route['salesforce']     = 'salesforce/refresh_today';

// Login 
$route['login']                                 = 'sessions/create';
$route['reset_pass']                                 = 'sessions/reset_pass';
$route['change_password/(:any)']                                 = 'sessions/change_password/$1';
$route['logout']                                = 'sessions/destroy';
$route['login_contents']                                = 'users/login_contents';
$route['logout/(:num)']                                = 'sessions/destroy/$1';
$route['logout']                                = 'sessions/destroy';
$route['register']                              = 'users/register';


// Specific
$route['saved/(:any)']                          = 'estimates/saved/$1';
$route['saved/(:any)/(:any)']                   = 'estimates/saved/$1/$1';
$route['production']                            = 'fulfillment/production';
$route['billing']                               = 'fulfillment/billing';
$route['shipping']                              = 'fulfillment/shipping';
$route['planning']                              = 'fulfillment/planning';
$route['logistics']                             = 'fulfillment/logistics';
$route['combine_orders']                        = 'fulfillment/combine_orders';
$route['combine_orders']                        = 'fulfillment/combine_orders/$1';
$route['all_combined_orders']                   = 'fulfillment/all_combined_orders';
$route['all_combined_orders/(:any)']            = 'fulfillment/all_combined_orders/$1';
$route['combine_view']                          = 'fulfillment/combine_view';
$route['combine_view/(:any)']                   = 'fulfillment/combine_view/$1';
$route['errors/(:any)']                         = 'customerrors/index/$1';

// Temp
$route['fulfillment/packinglist']               = 'fulfillment/print_packing_list';
$route['fulfillment/sleevecutlist/(:num)']      = 'fulfillment/sleeve_cut_list/$1';
$route['fulfillment/productshiplabel']          = 'fulfillment/product_ship_label';
$route['fulfillment/tubingcutlist/(:num)']      = 'fulfillment/tubing_cut_list/$1';
$route['fulfillment/sleevelabel']               = 'fulfillment/print_sleeve_label';

// MAPP API
$route['mapp/v1/(:any)']                        = 'mapp/$1';

// SalesForce API
$route['sf/(:any)']								= "salesforce/$1";

// Ajax
$route['api/(:any)']                            = 'api_1/$1';
$route['estimates_api/(:any)']                  = 'configuration_calculator_api/$1';
$route['permissions-api']                       = 'permissions/permission_presets';

// General
$route['(:any)/add']                            = '$1/add';
$route['(:any)/edit/(:num)']                    = '$1/edit/$2';
$route['(:any)/view/(:num)']                    = '$1/view/$2';
$route['(:any)/save/(:num)']                    = '$1/save/$2';
$route['(:any)/delete/(:num)']                  = '$1/delete/$2';
$route['(:any)/list/(:num)/(:num)']             = '$1/index/$2/$3';
$route['(:any)/list/(:num)']                    = '$1/index/$2';
$route['(:any)/list']                           = '$1';

// Catch All
$route['(:any)/(:any)']                         = '$1/$2';
$route['(:any)']                                = '$1/index';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
