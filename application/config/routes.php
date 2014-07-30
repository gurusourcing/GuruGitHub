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

$route['default_controller'] = "home";
$route['404_override'] = 'shorturl/generate';


/**
* Admin Section
*/
$route['admin']='admin/home';

//testing backoffice
$route['backoffice/cron/insert_user_friend_list_instant/(:any)']='admin/cron/insert_user_friend_list_instant/$1';
$route['backoffice/(:any)']='admin/$1';
$route['backoffice']='admin/home';  

$route['cms/(:any)']='home/cms/$1';


/**
* FE
*/
//cannot access user_profile/encID, dontknow why
//this url is working "user_profile?TlRZallXTjE=" 
//$route['user_profile/(:any)']='user_profile/index/$1';

$route['report_abuse/(:any)']='report_abuse/index/$1';
$route['recommendation/addRecommendation/(:any)']='recommendation/addRecommendation/$1';
$route['recommendation/general_recommendation/(:any)']='recommendation/general_recommendation/$1';
$route['recommendation/deleteRecommendation/(:any)']='recommendation/deleteRecommendation/$1';
$route['recommendation/approveRecommendation/(:any)']='recommendation/approveRecommendation/$1';
$route['recommendation/(:any)']='recommendation/index/$1';

////Service profile//

$route['service_profile/place_order_paypal']='service_profile/place_order_paypal';
$route['service_profile/payment_success']='service_profile/payment_success';
$route['service_profile/payment_failure']='service_profile/payment_failure';

//$route['service_profile/']='user_profile/index/$1';
$route['service_profile/add_service_once']='service_profile/add_service_once';
$route['service_profile/add_service']='service_profile/add_service';

$route['service_profile/ajax_operation']='service_profile/ajax_operation';
$route['service_profile/ajax_employee_certificate_license_operation']='service_profile/ajax_employee_certificate_license_operation';

$route['service_profile/change_short_url']='service_profile/change_short_url';
$route['service_profile/(:any)']='service_profile/index/$1';

////Company profile//
$route['company_profile/ajax_operation']='company_profile/ajax_operation';
$route['company_profile/ajax_certificate_operation']='company_profile/ajax_certificate_operation';
$route['company_profile/ajax_license_operation']='company_profile/ajax_license_operation';
$route['company_profile/change_short_url']='company_profile/change_short_url';

$route['company_profile/(:any)']='company_profile/index/$1';


// company employee///
$route['company_employee/add_company_employee']='company_employee/add_company_employee';
$route['company_employee/other_company_employee/(:any)']='company_employee/other_company_employee/$1';
$route['company_employee/edit_company_employee/(:any)']='company_employee/edit_company_employee/$1';
$route['company_employee/ajax_change_status']='company_employee/ajax_change_status';
$route['company_employee/ajaxDeleteEmployee']='company_employee/ajaxDeleteEmployee';
$route['company_employee/ajaxChangerole']='company_employee/ajaxChangerole';   
$route['company_employee/ajaxChangeservice']='company_employee/ajaxChangeservice';
$route['company_employee/(:any)']='company_employee/index/$1';

// saved search //
$route['save_search/gotosearchresult/(:any)']='save_search/gotosearchresult/$1';

// make service featured//
//$route['make_service_featured/(:any)']='make_service_featured/index/$1';
/* End of file routes.php */
/* Location: ./application/config/routes.php */
