<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
|    example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|    https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|    $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|    $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|    $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:    my-controller/index    -> my_controller/index
|        my-controller/my-method    -> my_controller/my_method
 */

$route['login']                                    = 'login/auth';
$route['login/verify']['POST']                     = 'login/verify_recovery_data';
$route['login/update']['POST']                     = 'login/update_password';
$route['calls/all']['GET']                         = 'calls/show_all';
$route['users']['GET']                             = 'users/all';
$route['users']['PUT']                             = 'users/edit';
$route['users/(:num)']['GET']                      = 'users/view/$1';
$route['users/(:num)']['DELETE']                   = 'users/delete/$1';
$route['users/(:num)/location']['GET']             = 'users/view_location/$1';
$route['users/(:num)/location']['PUT']             = 'users/edit_location/$1';
$route['users/add']                                = 'users/add';
$route['fields/(:num)']                            = 'fields/get_by_value/$1';
$route['categories/(:num)']                        = 'categories/$1';
$route['discounts/(:any)']                         = 'discounts/$1';
$route['company']                                  = 'company/get_by_id';
$route['company/edit']                             = 'company/edit';
$route['company/detail']                           = 'company/get_detail';
$route['company/socialmedia']                      = 'company/get_social_media';
$route['company/socialmedia/edit']                 = 'company/edit_social_media';
$route['company/category']                         = 'company/get_by_category';
$route['products/(:num)']['DELETE']                = 'products/delete/$1';
$route['products/all']                             = 'products/get_all';
$route['products/all/(:num)']                      = 'products/get_all/$1';
$route['products/user']                            = 'products/get_by_user';
$route['products/get']                             = 'products/get_by_id';
$route['products/update']['POST']                  = 'products/update';
$route['purchases/info/(:any)/user/(:any)']['GET'] = 'purchases/show_info/$1/$2';
$route['purchases/order']['POST']                  = 'purchases/add_order';
$route['purchases/order/(:num)']['GET']            = 'purchases/show_order_detail/$1';
$route['purchases/order/user']['POST']             = 'purchases/show_order';
$route['purchases/traceability']['POST']           = 'purchases/insert_traceability';
$route['purchases/traceability/(:num)']['GET']     = 'purchases/show_traceability/$1';
$route['purchases/status/(:num)']['GET']           = 'purchases/show_status/$1';
$route['ratings']['POST']                          = 'ratings/add';
$route['costs/(:any)']                             = 'costs/$1';
$route['images/(:any)']                            = 'images/$1';
$route['default_controller']                       = 'welcome';
$route['404_override']                             = '';
$route['translate_uri_dashes']                     = FALSE;
