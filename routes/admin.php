<?php

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;
use App\DataTables\DriverRemarksDataTable;

$domains = array(
    'www.' . env('ADMIN_PANEL_SUB_DOMAIN') . '.' . env('DOMAIN'), 
    env('ADMIN_PANEL_SUB_DOMAIN') . '.' . env('DOMAIN'),
    env('DOMAIN'), // added for without admin subdomain
    'www.'.env('DOMAIN') // added for without admin subdomain
);

foreach ($domains as $domain) {
    Route::domain($domain)->group(function () {
        
        
        Route::match(array('GET', 'POST'), 'admin/cross_site_login', function () {    
            echo session('username_from_main_domain');
        });



        //Route::domain(env('ADMIN_PANEL_SUB_DOMAIN', 'admin') . '.' . env('DOMAIN'))->group(function () {
        
        // for company login from web and UserController taken from web start 
        Route::match(array('GET', 'POST'), 'signin_company', 'UserController@signin_company');
        Route::post('login', 'UserController@login');
        // for company login from web end

        Route::get(env('ADMIN_PANEL_SUB_DOMAIN').'/session_set_from_another_domain', 'AdminController@session_set_from_another_domain');

        Route::get('/activity-log', function () {    
            return Activity::all(); 
        });

        Route::get('dashboard', function () {
            return redirect('admin/login');
        });
        Route::get('admin', function () { // added for without admin subdomain   -> ''        to   admin
            return redirect('admin/login');
        });

        Route::group(['prefix' => 'admin', 'middleware' => 'admin_auth'], function () {
            Route::get('login', 'AdminController@login')->name('admin_login');
        });

        Route::match(['get', 'post'], 'admin/authenticate', 'AdminController@authenticate');
        Route::match(['get', 'post'], 'admin/authenticate_no_csrf', 'AdminController@authenticate_no_csrf');
        


        $login_user_type = LOGIN_USER_TYPE;
        $prefix = "admin";
        if ($login_user_type == "admin") {
            $prefix = "admin";
        } else if ($login_user_type == "company") {
            $prefix = "company";
        } else if ($login_user_type == "hub") {
            $prefix = "hub";
        }



        //Route::group(['prefix' => (LOGIN_USER_TYPE=='company')?'company': (LOGIN_USER_TYPE=='hub')?'hub':'admin', 'middleware' =>'admin_guest'], function () {

        Route::group(['prefix' => $prefix, 'middleware' => 'admin_guest'], function () {           

            //Ajax
            Route::post('ajax/driver_status_update', 'AjaxController@driver_status_update')->middleware('admin_can:driver_status');
            Route::post('ajax/update_driver_info', 'AjaxController@update_driver_info')->middleware('admin_can:update_driver_info');
            Route::post('ajax/set_partner', 'AjaxController@set_partner')->middleware('admin_can:set_partner');

            Route::post('ajax/driver_expired_date_update', 'AjaxController@driver_expired_date_update')->middleware('admin_can:driver_status');
            Route::any('ajax/driver_status_uncheck', 'AjaxController@driver_status_uncheck');//->middleware('admin_can:driver_status');
            
            Route::get('ajax/suggestion', 'AjaxController@suggestion');
            Route::any('ajax/verify_nid', 'AjaxController@verify_nid');
            Route::post('ajax/verify_invalid', 'AjaxController@verify_invalid');
            Route::get('ajax/suggestion_partner', 'AjaxController@suggestion_partner');
			Route::post('ajax/suggestion_company', 'AjaxController@suggestion_company');
			Route::post('ajax/suggestion_company_driver', 'AjaxController@suggestion_company_driver');
            Route::any('ajax/change_vehicle_type', 'AjaxController@change_vehicle_type');
            Route::get('ajax/suggestion_for_user', 'AjaxController@suggestion_for_user');
            Route::get('ajax/complain_sub_category_option/{cat_id?}', 'AjaxController@complain_sub_category_option');
            Route::get('ajax/suggestion_contact', 'AjaxController@suggestion_contact');
            
            
            



            // /: (LOGIN_USER_TYPE=='hub')?'hub'
            //Route::group(['prefix' => LOGIN_USER_TYPE, 'middleware' =>'admin_guest'], function () {

            Route::redirect('/', LOGIN_USER_TYPE . '/dashboard');
            //Route::get('dashboard', 'AdminController@index');
            Route::match(['get', 'post'], 'dashboard', 'AdminController@index');

            if (LOGIN_USER_TYPE == 'admin' || LOGIN_USER_TYPE == 'company') {
                Route::get('logout', 'AdminController@logout');
            }
            // Admin Users and permission routes
            Route::group(['middleware' => 'admin_can:manage_admin'], function () {
                Route::get('admin_user', 'AdminController@view');
                Route::match(array('GET', 'POST'), 'add_admin_user', 'AdminController@add');
                Route::match(array('GET', 'POST'), 'edit_admin_users/{id}', 'AdminController@update');
                Route::match(array('GET', 'POST'), 'delete_admin_user/{id}', 'AdminController@delete');

                Route::get('roles', 'RolesController@index');
                Route::match(array('GET', 'POST'), 'add_role', 'RolesController@add');
                Route::match(array('GET', 'POST'), 'edit_role/{id}', 'RolesController@update')->where('id', '[0-9]+');
                Route::get('delete_role/{id}', 'RolesController@delete')->where('id', '[0-9]+');

                Route::get('clear_cache', function () {
                    Artisan::call('cache:clear');
                    Artisan::call('optimize:clear');
                    Cache::flush();
                    return redirect('admin/dashboard');
                });
            });

            // Manage Help Routes
            Route::group(['middleware' => 'admin_can:manage_help'], function () {
                Route::get('help_category', 'HelpCategoryController@index');
                Route::match(array('GET', 'POST'), 'add_help_category', 'HelpCategoryController@add');
                Route::match(array('GET', 'POST'), 'edit_help_category/{id}', 'HelpCategoryController@update')->where('id', '[0-9]+');
                Route::get('delete_help_category/{id}', 'HelpCategoryController@delete')->where('id', '[0-9]+');
                Route::get('help_subcategory', 'HelpSubCategoryController@index');
                Route::match(array('GET', 'POST'), 'add_help_subcategory', 'HelpSubCategoryController@add');
                Route::match(array('GET', 'POST'), 'edit_help_subcategory/{id}', 'HelpSubCategoryController@update')->where('id', '[0-9]+');
                Route::get('delete_help_subcategory/{id}', 'HelpSubCategoryController@delete')->where('id', '[0-9]+');
                Route::get('help', 'HelpController@index');
                Route::match(array('GET', 'POST'), 'add_help', 'HelpController@add');
                Route::match(array('GET', 'POST'), 'edit_help/{id}', 'HelpController@update')->where('id', '[0-9]+');
                Route::get('delete_help/{id}', 'HelpController@delete')->where('id', '[0-9]+');
                Route::post('ajax_help_subcategory/{id}', 'HelpController@ajax_help_subcategory')->where('id', '[0-9]+');
            });

            // Manage Hub start
            Route::group(['middleware' => 'admin_can:hub_management'], function () {
                Route::get('manage_hub', 'HubManage@index');
                Route::match(array('GET', 'POST'), 'add_hub', 'HubManage@add');
                Route::match(array('GET', 'POST'), 'edit_hub/{id}', 'HubManage@update');
                Route::get('delete_hub/{id}', 'HubManage@delete');

                Route::get('manage_employee', 'HubEmployeeController@index');
                Route::match(array('GET', 'POST'), 'add_employee', 'HubEmployeeController@add');
                Route::match(array('GET', 'POST'), 'edit_hub_employee/{id}', 'HubEmployeeController@update');
                Route::get('delete_hub_employee/{id}', 'HubEmployeeController@delete');
            });
            Route::post('hub_employee_ajax', 'CarAcquisition@hub_employee_ajax');

            Route::get('car_acquisition', 'CarAcquisition@index')->middleware('admin_can:car_acquisition');

            Route::group(['middleware' => 'admin_can:complain_category'], function () {
                Route::get('complain_category', 'ComplainCategoryController@index');
                Route::match(array('GET', 'POST'), 'add_complain_category', 'ComplainCategoryController@add')->middleware('admin_can:add_complain_category');
                Route::match(array('GET', 'POST'), 'edit_complain_category/{id}', 'ComplainCategoryController@edit')->middleware('admin_can:edit_complain_category');
                Route::get('delete_complain_category/{id}', 'ComplainCategoryController@delete')->middleware('admin_can:delete_complain_category');
            });

            Route::group(['middleware' => 'admin_can:complain_sub_category'], function () {
                Route::get('complain_sub_category', 'ComplainSubCategoryController@index');
                Route::match(array('GET', 'POST'), 'add_complain_sub_category', 'ComplainSubCategoryController@add')->middleware('admin_can:add_complain_sub_category');
                Route::match(array('GET', 'POST'), 'edit_complain_sub_category/{id}', 'ComplainSubCategoryController@edit')->middleware('admin_can:edit_complain_sub_category');
                Route::get('delete_complain_sub_category/{id}', 'ComplainSubCategoryController@delete')->middleware('admin_can:delete_complain_sub_category');
            });

            Route::group(['middleware' => 'admin_can:complain_list'], function () {
                Route::get('complain_list', 'ComplainListController@index');
                // Route::match(array('GET', 'POST'), 'add_complain', 'ComplainListController@add')->middleware('admin_can:add_complain');
                // Route::match(array('GET', 'POST'), 'edit_complain/{id}', 'ComplainListController@edit')->middleware('admin_can:edit_complain');
                Route::match(array('GET', 'POST'), 'movement_complain/{id}', 'ComplainListController@movement_complain')->middleware('admin_can:movement_complain');
                Route::post('edit_movement_complain', 'ComplainListController@edit_movement_complain')->middleware('admin_can:edit_movement_complain');
                Route::get('tracking_movement_complain/{id}', 'ComplainListController@tracking_movement_complain')->middleware('admin_can:tracking_movement_complain');
            });


            Route::group(['middleware' => 'admin_can:contact_list'], function () {
                Route::get('contact_list', 'ContactListController@index');
                Route::match(array('GET', 'POST'), 'movement_contact/{id}', 'ContactListController@movement_contact')->middleware('admin_can:movement_contact');
                Route::post('edit_movement_contact', 'ContactListController@edit_movement_contact')->middleware('admin_can:edit_movement_contact');
                Route::get('tracking_movement_contact/{id}', 'ContactListController@tracking_movement_contact')->middleware('admin_can:tracking_movement_contact');
            });

            

            //Route::get('complain_category', 'ComplainCategory@index')->middleware('admin_can:complain_category');
            // Route::get('complain_sub_category', 'Complain@index')->middleware('admin_can:complain_sub_category');
            // Route::get('complain_list', 'Complain@index')->middleware('admin_can:complain_list');



            Route::get('drivers_documents', 'DriversDocumentsController@index')->middleware('admin_can:drivers_documents');
            Route::get('drivers_documents/only_checked_documents', 'DriversDocumentsController@only_checked_documents')->middleware('admin_can:drivers_documents');

            Route::get('driver_status/all_documents', 'DriverStatusController@index')->middleware('admin_can:driver_status_all_documents');
            Route::get('driver_status/checked', 'DriverStatusController@index')->middleware('admin_can:driver_status_checked');
            Route::get('driver_status/verified', 'DriverStatusController@index')->middleware('admin_can:driver_status_verified');
            Route::get('driver_status/trained', 'DriverStatusController@index')->middleware('admin_can:driver_status_trained');
            Route::get('driver_status/active', 'DriverStatusController@index')->middleware('admin_can:driver_status_active');
            Route::get('driver_status/owner', 'DriverStatusController@index')->middleware('admin_can:driver_cum_owner');
            Route::get('driver_status/partner', 'DriverStatusController@index')->middleware('admin_can:driver_status_partner');
            Route::get('driver_status/drivers_under_partner', 'DriverStatusController@index')->middleware('admin_can:drivers_under_partner');
            Route::get('driver_status/uncheck_owner_driver', 'DriverStatusController@index')->middleware('admin_can:uncheck_owner_driver');
            Route::get('driver_status/nid', 'DriverStatusController@index')->middleware('admin_can:driver_status_nid');
            
            Route::match(array('GET', 'POST'), 'otp', 'OtpReport@index')->middleware('admin_can:otp');
            

            Route::get('driver_status_print/all_documents', 'DriverStatusController@index')->middleware('admin_can:driver_status_all_documents_print');
            Route::get('driver_status_print/checked', 'DriverStatusController@index')->middleware('admin_can:driver_status_checked_print');
            Route::get('driver_status_print/verified', 'DriverStatusController@index')->middleware('admin_can:driver_status_verified_print');
            Route::get('driver_status_print/trained', 'DriverStatusController@index')->middleware('admin_can:driver_status_trained_print');
            Route::get('driver_status_print/active', 'DriverStatusController@index')->middleware('admin_can:driver_status_active_print');





            Route::get('driver_status_print/{type}', 'DriverStatusController@index')->middleware('admin_can:driver_status_print');




            Route::get('sos_messages', 'SosMessagesList@index')->middleware('admin_can:sos_messages');


            //->middleware('admin_can:view_rider');
            // Hub Employee Manage start
            // Route::group(['middleware' => 'admin_can:employee_list'],function () {


            Route::get('employee_list', 'HubEmployeeController@employeelist');
            Route::get('acquisition_list', 'HubEmployeeController@acquisitionList');
            Route::get('hub_acquisition_driver/{id}/{all?}', 'DataList@hub_acquisition_driver');
            Route::get('hub_acquisition_driver', 'DataList@hub_acquisition_driver_search');


            // });
            // Hub Employee Manage end
            // Manage Hub end

            // Bonuse start
            Route::get('bonuse', 'BonuseController@index');
            // Bonuse end


            // Send message
            Route::group(['middleware' => 'admin_can:manage_send_message'], function () {
                if(LOGIN_USER_TYPE == 'admin'){
                    Route::match(array('GET', 'POST'), 'send_message', 'SendmessageController@index')->name('admin.send_message');
                    Route::post('get_send_users', 'SendmessageController@get_send_users');
                    Route::post('send-bulk-sms', 'SendmessageController@sendMessage')->name('admin.send-bulk-sms');
                    Route::get('send-specific-bulk-sms-push', 'SendmessageController@sendSpecificSMSPUSH')->name('admin.send-specific-bulk-sms-push');
                }
            });

            // Manage Rider
            Route::get('rider', 'RiderController@index')->middleware('admin_can:view_rider');
            Route::get('rider_group', 'RiderGroupController@rider_group')->middleware('admin_can:rider_group');
            Route::post('rider_group', 'RiderGroupController@assign_rider_group');
            Route::post('add_rider_group', 'RiderGroupController@add_rider_group')->middleware('admin_can:add_rider_group');
			Route::post('add_rider_group_submit', 'RiderGroupController@add_rider_group_submit')->middleware('admin_can:add_rider_group');

			Route::get('add_rider_in_group/{id}', 'RiderGroupController@add_rider_in_group')->middleware('admin_can:add_rider_in_group');
            
			Route::get('view_rider_group_list/{id}', 'RiderGroupController@view_rider_group_list')->middleware('admin_can:view_rider_group_list');
			Route::get('edit_rider_group_list/{id}', 'RiderGroupController@edit_rider_group_list')->middleware('admin_can:edit_rider_group_list');
			// Route::post('search_rider', 'RiderGroupController@search_rider');
            Route::post('add_rider_in_group/{id}', 'RiderGroupController@search_rider')->middleware('admin_can:add_rider_in_group');
            Route::post('remove_rider_from_group', 'RiderGroupController@remove_rider_from_group');


            Route::match(array('GET', 'POST'), 'add_rider', 'RiderController@add')->middleware('admin_can:create_rider');
            Route::match(array('GET', 'POST'), 'edit_rider/{id}', 'RiderController@update')->middleware('admin_can:update_rider');
            Route::match(array('GET', 'POST'), 'delete_rider/{id}', 'RiderController@delete')->middleware('admin_can:delete_rider');

            // Manage Driver
            Route::get('driver', 'DriverController@index')->middleware('admin_can:view_driver');
            Route::match(array('GET', 'POST'), 'add_driver', 'DriverController@add')->middleware('admin_can:create_driver');
            Route::match(array('GET', 'POST'), 'edit_driver/{id}', 'DriverController@update')->middleware('admin_can:update_driver');
            Route::match(array('GET', 'POST'), 'delete_driver/{id}', 'DriverController@delete')->middleware('admin_can:delete_driver');
            Route::get('driver/profile/{id}', 'DriverController@profile')->middleware('admin_can:view_driver_profile');

         

             Route::match(array('GET', 'POST'), 'change_partner/{driver_id}', 'DriverController@change_partner')->middleware('admin_can:change_partner');

            



            Route::match(array('GET', 'POST'), 'drivers_remarks/{remarks_status?}', 'DriverRemarksController@index')->middleware('admin_can:drivers_remarks');

            // Route::get('drivers_remarks/{remarks_status?}', function(DriverRemarksDataTable $dataTable, $remarks_status){
            //    return $dataTable->with('id', $remarks_status)
            //            ->with([
            //                 'key2' => 'value2',
            //                 'key3' => 'value3',
            //            ])
            //            ->render('admin.drivers_remarks.view');
            // })->middleware('admin_can:drivers_remarks');


            Route::match(array('GET', 'POST'), 'add_drivers_remarks/{id?}', 'DriverRemarksController@add')->middleware('admin_can:add_drivers_remarks');
            Route::get('view_drivers_remarks/{id}', 'DriverRemarksController@view')->middleware('admin_can:view_drivers_remarks');
            Route::match(array('GET', 'POST'), 'edit_drivers_remarks/{id}', 'DriverRemarksController@update')->middleware('admin_can:edit_drivers_remarks');

            //Monitor AndCamera
            Route::get('monitor_camera', 'MonitorCameraController@index')->middleware('admin_can:view_monitor_camera');
            Route::match(array('GET', 'POST'), 'add_monitor_camera', 'MonitorCameraController@add')->middleware('admin_can:add_monitor_camera');
            Route::match(array('GET', 'POST'), 'edit_monitor_camera/{id}', 'MonitorCameraController@update')->middleware('admin_can:edit_monitor_camera');

            Route::match(array('GET', 'POST'), 'monitor_camera_suggestion', 'MonitorCameraController@suggestion');



            Route::post('get_documents', 'DriverController@get_documents');




            // Best Driver
            Route::get('best_driver', 'BestDriverController@index')->middleware('admin_can:best_driver');

            // Manage Company
            Route::get('company', 'CompanyController@index')->middleware('admin_can:view_company');
            Route::match(array('GET', 'POST'), 'add_company', 'CompanyController@add')->middleware('admin_can:create_company');
            Route::match(array('GET', 'POST'), 'edit_company/{id}', 'CompanyController@update')->middleware('admin_can:update_company');
            Route::match(array('GET', 'POST'), 'delete_company/{id}', 'CompanyController@delete')->middleware('admin_can:delete_company');

            Route::match(array('GET', 'POST'), 'company/transaction_history/paid_to_alesha/{company_id}', 'CompanyTransactionHistory@paid_to_alesha')->middleware('admin_can:paid_to_alesha');
            Route::match(array('GET', 'POST'), 'company/transaction_history/balance_withdraw/{company_id}', 'CompanyTransactionHistory@balance_withdraw')->middleware('admin_can:balance_withdraw');
            Route::match(array('GET', 'POST'), 'company/transaction_history/payout/{company_id}', 'CompanyTransactionHistory@payout')->middleware('admin_can:payout');

            // Manage Statements
            Route::group(['middleware' =>  'admin_can:manage_statements'], function () {
                Route::post('get_statement_counts', 'StatementController@get_statement_counts');
                Route::match(array('GET', 'POST'), 'statements/{type}', 'StatementController@index');
                Route::get('view_driver_statement/{driver_id}', 'StatementController@view_driver_statement');
                Route::post('driver_statement', 'StatementController@driver_statement');
                Route::post('statement_all', 'StatementController@custom_statement');
            });

            // Manage Location
            Route::group(['middleware' => 'admin_can:manage_locations'], function () {
                Route::get('locations', 'LocationsController@index');
                Route::match(array('GET', 'POST'), 'add_location', 'LocationsController@add')->name('admin.add_location');
                Route::match(array('GET', 'POST'), 'edit_location/{id}', 'LocationsController@update')->name('admin.edit_location');
                Route::get('delete_location/{id}', 'LocationsController@delete');
            });

            // Manage Peak Based Fare Details
            Route::group(['middleware' => 'admin_can:manage_peak_based_fare'], function () {
                Route::get('manage_fare', 'ManageFareController@index');
                Route::match(array('GET', 'POST'), 'add_manage_fare', 'ManageFareController@add')->name('admin.add_manage_fare');
                Route::match(array('GET', 'POST'), 'edit_manage_fare/{id}', 'ManageFareController@update')->name('admin.edit_manage_fare');
                Route::get('delete_manage_fare/{id}', 'ManageFareController@delete');
            });

            Route::group(['middleware' => 'admin_can:manage_peak_hour'], function () {
                Route::get('manage_peak_hour', 'ManagePeakHourController@index');
                Route::match(array('GET', 'POST'), 'edit_peak_hour/{id}', 'ManagePeakHourController@update')->name('admin.edit_peak_hour');
            });

            // Manage Toll fare Details
            Route::get('additional-reasons', 'TollReasonController@index')->middleware('admin_can:view_additional_reason');
            Route::match(array('GET', 'POST'), 'add-additional-reason', 'TollReasonController@add')->middleware('admin_can:create_additional_reason');
            Route::match(array('GET', 'POST'), 'edit-additional-reason/{id}', 'TollReasonController@update')->middleware('admin_can:update_additional_reason');
            Route::get('delete-additional-reason/{id}', 'TollReasonController@delete')->middleware('admin_can:delete_additional_reason');

            // Map
            Route::group(['middleware' =>  'admin_can:manage_map'], function () {
                if(LOGIN_USER_TYPE == 'admin'){
                    Route::match(array('GET', 'POST'), 'map', 'MapController@index');
                    Route::match(array('GET', 'POST'), 'mapdata', 'MapController@mapdata');
                }
            });
            Route::group(['middleware' =>  'admin_can:manage_heat_map'], function () {
                if(LOGIN_USER_TYPE == 'admin'){
                    Route::match(array('GET', 'POST'), 'heat-map', 'MapController@heat_map');
                    Route::match(array('GET', 'POST'), 'heat-map-data', 'MapController@heat_map_data');
                }
            });
           
            // Manage Vehicle Type
            Route::group(['middleware' =>  'admin_can:manage_vehicle_type'], function () {
                Route::get('vehicle_type', 'VehicleTypeController@index');
                Route::match(array('GET', 'POST'), 'add_vehicle_type', 'VehicleTypeController@add');
                Route::match(array('GET', 'POST'), 'edit_vehicle_type/{id}', 'VehicleTypeController@update');
                Route::match(array('GET', 'POST'), 'delete_vehicle_type/{id}', 'VehicleTypeController@delete');
            });

            // Manage Referrals Routes
            Route::group(['prefix' => 'referrals'], function () {
                Route::get('rider', 'ReferralsController@index')->middleware('admin_can:manage_rider_referrals');
                Route::get('driver', 'ReferralsController@index')->middleware('admin_can:manage_driver_referrals');
                Route::get('{id}', 'ReferralsController@referral_details');
            });
            if(LOGIN_USER_TYPE == 'company'){
                Route::get('vehicle', 'VehicleController@index');
                Route::match(array('GET', 'POST'), 'add_vehicle', 'VehicleController@add');
                Route::match(array('GET', 'POST'), 'edit_vehicle/{id}', 'VehicleController@update');
                Route::match(array('GET', 'POST'), 'validate_vehicle_number', 'VehicleController@validate_vehicle_number');
                Route::match(array('GET', 'POST'), 'check_default', 'VehicleController@check_default');
                Route::match(array('GET', 'POST'), 'vehicle/change_vehicle/{vehicle_id}', 'VehicleController@change_vehicle');
            }else{
                // Manage Vehicle
                Route::group(['middleware' =>  'admin_can:manage_vehicle'], function () {
                    //Route::get('vehicle', 'VehicleController@index');
                    Route::match(array('GET', 'POST'), 'vehicle', 'VehicleController@index');
                    Route::match(array('GET', 'POST'), 'add_vehicle', 'VehicleController@add');

                    Route::post('manage_vehicle/{company_id}/get_driver', 'VehicleController@get_driver')->name('admin.get_driver');

                    Route::match(array('GET', 'POST'), 'edit_vehicle/{id}', 'VehicleController@update')->middleware('admin_can:update_vehicle');
                    Route::match(array('GET', 'POST'), 'delete_vehicle/{id}', 'VehicleController@delete')->middleware('admin_can:delete_vehicle');
                    Route::match(array('GET', 'POST'), 'validate_vehicle_number', 'VehicleController@validate_vehicle_number');
                    Route::match(array('GET', 'POST'), 'check_default', 'VehicleController@check_default');
                    Route::match(array('GET', 'POST'), 'view_vehicle/{id}', 'VehicleController@view')->middleware('admin_can:view_vehicle_make');
                });
                Route::match(array('GET', 'POST'), 'vehicle/change_vehicle/{vehicle_id}', 'VehicleController@change_vehicle')->middleware('admin_can:change_vehicle');
            }
                
            

            // Trips
            Route::group(['middleware' =>  'admin_can:manage_trips'], function () {
                Route::match(array('GET', 'POST'), 'trips', 'TripsController@index');
                Route::get('view_trips/{id}', 'TripsController@view');
                Route::get('print_trips/{id}', 'TripsController@print');
                Route::post('trips/payout/{id}', 'TripsController@payout');
                Route::get('trips/export/{from}/{to}', 'TripsController@export');
            });

            // Manage Company Payout Routes
            Route::group(['middleware' =>  'admin_can:manage_company_payment'], function () {
                Route::get('payout/company/overall', 'CompanyPayoutController@overall_payout');
                Route::get('weekly_payout/company/{company_id}', 'CompanyPayoutController@weekly_payout');
                Route::get('per_week_report/company/{company_id}/{start_date}/{end_date}', 'CompanyPayoutController@payout_per_week_report');
                Route::get('per_day_report/company/{company_id}/{date}', 'CompanyPayoutController@payout_per_day_report');
                Route::post('make_payout/company', 'CompanyPayoutController@payout_to_company');
            });

            // Manage Driver Payout Routes
            Route::group(['middleware' =>  'admin_can:manage_driver_payments'], function () {
                Route::get('payout/overall', 'PayoutController@overall_payout');
                Route::get('weekly_payout/{driver_id}', 'PayoutController@weekly_payout');
                Route::get('per_week_report/{driver_id}/{start_date}/{end_date}', 'PayoutController@payout_per_week_report');
                Route::get('per_day_report/{driver_id}/{date}', 'PayoutController@payout_per_day_report');
                Route::post('make_payout', 'PayoutController@payout_to_driver');
            });

            Route::group(['middleware' =>  'admin_can:driver_balance_payout'], function () {
                Route::get('payout/driver_balance', 'PayoutDriverBalanceController@view');
                Route::post('driver_balance_paout', 'PayoutDriverBalanceController@payout_to_driver');
            });

                Route::group(['middleware' =>  'admin_can:company_balance_payout'], function () {
                Route::get('payout/company_balance', 'PayoutCompanyBalanceController@view');
                Route::post('company_balance_paout', 'PayouttCompanyBalanceController@payout_to_driver');
            });

            // Manage Wallet
            Route::group(['prefix' => 'wallet', 'middleware' =>  'admin_can:manage_wallet'], function () {
                Route::get('{user_type}', 'WalletController@index')->name('wallet');
                Route::match(array('GET', 'POST'), 'add/{user_type}', 'WalletController@add')->name('add_wallet');
                Route::match(array('GET', 'POST'), 'edit/{user_type}/{id}', 'WalletController@update')->where('id', '[0-9]+')->name('edit_wallet');
                Route::get('delete/{user_type}/{id}', 'WalletController@delete')->where('id', '[0-9]+')->name('delete_wallet');
            });
            Route::match(array('GET', 'POST'), 'ballance_suggestion', 'MonitorCameraController@suggestion_rider');

            // Owe Amount
            Route::group(['middleware' =>  'admin_can:manage_owe_amount'], function () {
                Route::match(array('GET', 'POST'), 'owe', 'OweController@index')->name('owe');
                Route::match(array('GET', 'POST'), 'company_owe/{id}', 'OweController@company_index')->name('owe');
                Route::get('details/{type}', 'OweController@owe_details')->name('owe_details');
                Route::get('update_driver_payment', 'OweController@update_payment')->name('update_payment');
                Route::post('update_owe_payment', 'OweController@updateOwePayment')->name('update_owe_payment');
                Route::post('update_company_payment', 'OweController@update_company_payment')->name('update_company_payment');
            });

            // Company Owe amount
            Route::get('driver_payment', 'OweController@driver_payment')->name('driver_payment');

            // Manage Promo Code
            Route::group(['middleware' =>  'admin_can:manage_promo_code'], function () {
                Route::get('promo_code', 'PromocodeController@index');
                Route::match(array('GET', 'POST'), 'add_promo_code', 'PromocodeController@add');
                Route::match(array('GET', 'POST'), 'edit_promo_code/{id}', 'PromocodeController@update')->where('id', '[0-9]+');
                Route::get('delete_promo_code/{id}', 'PromocodeController@delete');
            });

            // Payments
            Route::group(['middleware' =>  'admin_can:manage_payments'], function () {
                Route::match(array('GET', 'POST'), 'payments', 'PaymentsController@index');
                Route::get('view_payments/{id}', 'PaymentsController@view');
                Route::get('payments/export/{from}/{to}', 'PaymentsController@export');
            });

            // Cancelled Trips
            Route::group(['middleware' =>  'admin_can:manage_cancel_trips'], function () {
                Route::get('cancel_trips', 'TripsController@cancel_trips');
            });

            // Manage Cancel reasons
            Route::get('cancel-reason', 'CancelReasonController@index')->middleware('admin_can:view_manage_reason');
            Route::match(array('GET', 'POST'), 'add-cancel-reason', 'CancelReasonController@add')->middleware('admin_can:create_manage_reason');
            Route::match(array('GET', 'POST'), 'edit-cancel-reason/{id}', 'CancelReasonController@update')->where('id', '[0-9]+')->middleware('admin_can:update_manage_reason');
            Route::get('delete-cancel-reason/{id}', 'CancelReasonController@delete')->middleware('admin_can:delete_manage_reason');

            // Manage Rating
            Route::group(['middleware' =>  'admin_can:manage_rating'], function () {
                Route::get('rating', 'RatingController@index');
                Route::get('delete_rating/{id}', 'RatingController@delete');
            });

            // Manage fees
            Route::group(['middleware' =>  'admin_can:manage_fees'], function () {
                Route::match(array('GET', 'POST'), 'fees', 'FeesController@index');
            });

            // Manage Referral Settings
            Route::group(['middleware' =>  'admin_can:manage_referral_settings'], function () {
                Route::get('referral_settings', 'ReferralSettingsController@index');
                Route::post('update_referral_settings', 'ReferralSettingsController@update');
            });

            // SiteSetting
            Route::match(array('GET', 'POST'), 'site_setting', 'SiteSettingsController@index')->middleware('admin_can:manage_site_settings');

            // Manage Api credentials
            Route::match(array('GET', 'POST'), 'api_credentials', 'ApiCredentialsController@index')->middleware('admin_can:manage_api_credentials');

            // Manage Payment Gateway
            Route::group(['middleware' =>  'admin_can:manage_payment_gateway'], function () {
                Route::match(array('GET', 'POST'), 'payment_gateway', 'PaymentGatewayController@index');
            });

            // Request
            Route::group(['middleware' =>  'admin_can:manage_requests'], function () {
                Route::get('detail_request/{id}', 'RequestController@detail_request');
                Route::match(array('GET', 'POST'), 'request', 'RequestController@index');
            });

            // Join us management
            Route::group(['middleware' =>  'admin_can:manage_join_us'], function () {
                Route::match(array('GET', 'POST'), 'join_us', 'JoinUsController@index');
            });

            // Manage Static pages
            Route::group(['middleware' =>  'admin_can:manage_static_pages'], function () {
                Route::get('pages', 'PagesController@index');
                Route::match(array('GET', 'POST'), 'add_page', 'PagesController@add');
                Route::match(array('GET', 'POST'), 'edit_page/{id}', 'PagesController@update')->where('id', '[0-9]+');
                Route::get('delete_page/{id}', 'PagesController@delete')->where('id', '[0-9]+');
            });

            // Manage Meta
            Route::group(['middleware' =>  'admin_can:manage_metas'], function () {
                Route::match(array('GET', 'POST'), 'metas', 'MetasController@index');
                Route::match(array('GET', 'POST'), 'edit_meta/{id}', 'MetasController@update')->where('id', '[0-9]+');
            });

            // Manage Currency Routes
            Route::group(['middleware' =>  'admin_can:manage_currency'], function () {
                Route::get('currency', 'CurrencyController@index');
                Route::match(array('GET', 'POST'), 'add_currency', 'CurrencyController@add');
                Route::match(array('GET', 'POST'), 'edit_currency/{id}', 'CurrencyController@update')->where('id', '[0-9]+');
                Route::get('delete_currency/{id}', 'CurrencyController@delete')->where('id', '[0-9]+');
            });

            // Manage Document Routes
            Route::get('documents', 'DocumentsController@index')->middleware('admin_can:view_documents');
            Route::match(array('GET', 'POST'), 'add_document', 'DocumentsController@add')->middleware('admin_can:create_documents');
            Route::get('edit_document/{id}', 'DocumentsController@edit')->where('id', '[0-9]+')->middleware('admin_can:update_documents');
            Route::get('delete_document/{id}', 'DocumentsController@delete')->where('id', '[0-9]+')->middleware('admin_can:delete_documents');

            // Manage Language Routes
            Route::group(['middleware' =>  'admin_can:manage_language'], function () {
                Route::get('language', 'LanguageController@index');
                Route::match(array('GET', 'POST'), 'add_language', 'LanguageController@add');
                Route::match(array('GET', 'POST'), 'edit_language/{id}', 'LanguageController@update')->where('id', '[0-9]+');
                Route::get('delete_language/{id}', 'LanguageController@delete')->where('id', '[0-9]+');
            });

            // Manage Country
            Route::group(['middleware' => 'admin_can:manage_country'], function () {
                Route::get('country', 'CountryController@index');
                Route::match(array('GET', 'POST'), 'add_country', 'CountryController@add');
                Route::match(array('GET', 'POST'), 'edit_country/{id}', 'CountryController@update')->where('id', '[0-9]+');
                Route::get('delete_country/{id}', 'CountryController@delete')->where('id', '[0-9]+');
            });

            // Manual Booking
            Route::group(['middleware' => 'admin_can:manage_manual_booking'], function () {
                Route::get('manual_booking/{id?}', 'ManualBookingController@index');
                Route::post('manual_booking/store', 'ManualBookingController@store');
                Route::post('search_phone', 'ManualBookingController@search_phone');
                Route::post('search_cars', 'ManualBookingController@search_cars');
                Route::post('get_driver', 'ManualBookingController@get_driver');
                Route::post('driver_list', 'ManualBookingController@driver_list');
                Route::get('later_booking', 'LaterBookingController@index');
                Route::post('immediate_request', 'LaterBookingController@immediate_request');
                Route::post('manual_booking/cancel', 'LaterBookingController@cancel');
            });

            // Manage Support
            Route::group(['middleware' => 'admin_can:manage_support'], function () {
                Route::get('support', 'SupportController@index');
                Route::match(array('GET', 'POST'), 'add_support', 'SupportController@add');
                Route::match(array('GET', 'POST'), 'edit_support/{id}', 'SupportController@update')->where('id', '[0-9]+')->name('edit');
                Route::get('delete_support/{id}', 'SupportController@delete')->where('id', '[0-9]+')->name('delete');
            });

            // Manage Email Settings Routes
            Route::match(array('GET', 'POST'), 'email_settings', 'EmailController@index')->middleware(['admin_can:manage_email_settings']);
            Route::match(array('GET', 'POST'), 'send_email', 'EmailController@send_email')->middleware(['admin_can:manage_send_email']);

            // Manage Make  Vehicle reasons
            Route::get('vehicle_make', 'MakeVehicleController@index')->middleware('admin_can:view_vehicle_make');
            Route::match(array('GET', 'POST'), 'add-vehicle-make', 'MakeVehicleController@add')->middleware('admin_can:create_vehicle_make');
            Route::match(array('GET', 'POST'), 'edit-vehicle-make/{id}', 'MakeVehicleController@update')->where('id', '[0-9]+')->middleware('admin_can:update_vehicle_make');
            Route::get('delete-vehicle_make/{id}', 'MakeVehicleController@delete')->middleware('admin_can:delete_vehicle_make');

            Route::get('vehicle_model', 'VehicleModelController@index')->middleware('admin_can:view_vehicle_model');
            Route::match(array('GET', 'POST'), 'add-vehicle_model', 'VehicleModelController@add')->middleware('admin_can:create_vehicle_model');
            Route::match(array('GET', 'POST'), 'edit-vehicle_model/{id}', 'VehicleModelController@update')->where('id', '[0-9]+')->middleware('admin_can:update_vehicle_model');
            Route::get('delete_vehicle_model/{id}', 'VehicleModelController@delete')->middleware('admin_can:delete_vehicle_make');
            Route::post('makelist', 'VehicleModelController@makeListValue');
            //Route::get('owe_trip_list/{company_or_driver}/{id}', 'ModalDataList@owe_trip')->middleware('admin_can:manage_owe_amount');

            Route::get('owe_trip_list/{company_or_driver}/{id}', 'DataList@owe_trip')->middleware('admin_can:manage_owe_amount');

            Route::get('company_driver_list/{id}', 'DataList@company_driver')->middleware('admin_can:view_company');

            Route::group(['middleware' =>  'admin_can:driver_offer'], function () {
                Route::get('driver_offer/signing_bonus', 'DriverOfferController@signing_bonus');
                Route::get('driver_offer/trip_bonus', 'DriverOfferController@trip_bonus');
                Route::get('driver_offer/online_bonus', 'DriverOfferController@online_bonus');
                Route::get('driver_offer/referral_bonus', 'DriverOfferController@referral_bonus');
            });
            Route::group(['middleware' =>  'admin_can:rider_offer'], function () {
                Route::get('rider_offer/referral_bonus', 'RiderOfferController@referral_bonus');
                Route::get('rider_offer/cash_back', 'RiderOfferController@cash_back');
                ///....
            });

            Route::group(['middleware' => 'admin_can:activity_log'], function () {
                Route::get('activity_log', 'LogsController@activity_log');
                Route::get('audit_log', 'LogsController@audit_log');
                Route::get('sys_log', 'LogsController@sys_log')->name('sys_log');
                Route::get('delete_log_file/{file_name}', 'LogsController@delete_log_file');
                Route::get('delete_api_log_file/{file_name}', 'LogsController@delete_api_log_file');
            });

            if (LOGIN_USER_TYPE == 'company') {
                Route::get('payout_preference', 'CompanyPayoutPreferenceController@index');
                Route::match(array('GET', 'POST'), 'add_payout_preference', 'CompanyPayoutPreferenceController@add');
                Route::match(array('GET', 'POST'), 'edit_payout_preference/{id}', 'CompanyPayoutPreferenceController@update');
                Route::get('delete_payout_preference/{id}', 'CompanyPayoutPreferenceController@delete');
                Route::get('view_payout_preference/{id}', 'CompanyPayoutPreferenceController@view');
                Route::get('dues', 'OweController@dues');
                //Route::match(array('GET', 'POST'), 'owe', 'OweController@index')->name('owe');

                // Route::get('dues', 'CompanyDuesController@view');
                // Route::match(array('GET', 'POST'), 'owe', 'OweController@index')->name('owe');
                Route::match(array('GET', 'POST'), 'transaction_history/paid_to_alesha', 'CompanyTransactionHistory@paid_to_alesha');
                Route::match(array('GET', 'POST'), 'transaction_history/balance_withdraw', 'CompanyTransactionHistory@balance_withdraw');
                Route::match(array('GET', 'POST'), 'transaction_history/payout', 'CompanyTransactionHistory@payout');
            }


            // Route::match(['GET','POST'],'get_locale', 'LocaleFileController@get_locale')->name('language.locale');
            // Route::post('update_locale', 'LocaleFileController@update_locale')->name('language.update_locale');
        });

        Route::group(['middleware' =>  'admin_can:rider_offer'], function () {
            Route::get('rider_offer/referral_bonus', 'RiderOfferController@referral_bonus');
            Route::get('rider_offer/cash_back', 'RiderOfferController@cash_back');
            ///....
        });

        Route::group(['middleware' => 'admin_can:activity_log'], function () {
            Route::get('activity_log', 'LogsController@activity_log');
            Route::get('audit_log', 'LogsController@audit_log');
            Route::get('sys_log', 'LogsController@sys_log');           
        });

        Route::group(['prefix' => $prefix, 'middleware' => 'admin_can:all_log'], function () {
            Route::get('api_logs', 'LogsController@api_logs'); 

            //echo getenv('SERVER_NAME');

            //For logviewer
            Route::prefix('all_logs')
            ->namespace('\Arcanedev\LogViewer\Http\Controllers')
            ->group(function () {
                Route::get('/', 'LogViewerController@index')
                    ->name('log-viewer::dashboard');
        
                Route::prefix('logs')
                    ->name('log-viewer::logs.')
                    ->group(function () {
        
                        Route::get('/', 'LogViewerController@listLogs')
                            ->name('list');
                        Route::delete('/delete', 'LogViewerController@delete')
                            ->name('delete');
        
                        Route::get('/{date}', 'LogViewerController@show')
                            ->name('show');
        
                        Route::get('/{date}/download', 'LogViewerController@download')
                            ->name('download');
        
                        Route::get('/{date}/{level}', 'LogViewerController@showByLevel')
                            ->name('filter');
        
                        Route::get('/{date}/{level}/search', 'LogViewerController@search')
                            ->name('search');
                    });
                });
                // logviewer end
        });


       

        Route::get('test_email', 'EmailTestController@index');



        // Route::match(['GET','POST'],'get_locale', 'LocaleFileController@get_locale')->name('language.locale');
        // Route::post('update_locale', 'LocaleFileController@update_locale')->name('language.update_locale');




    });
}
