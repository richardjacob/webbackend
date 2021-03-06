<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware gro transferFileup. Now create something great!
|
 */

use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Http\Controllers\Admin\LogsController;

Route::get('clear_cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    Cache::flush();
});

//$domains = array('www.'.env('DOMAIN'), env('DOMAIN'));
if (env('ROOT_DOMAIN_SUB_DOMAIN') != '') {
    $domains = array('www.' . env('ROOT_DOMAIN_SUB_DOMAIN') . '.' . env('DOMAIN'), env('ROOT_DOMAIN_SUB_DOMAIN') . '.' . env('DOMAIN'));
} else {
    $domains = array('www.' . env('DOMAIN'), env('DOMAIN'));
}


foreach ($domains as $domain) {
    Route::domain($domain)->group(function () {
        //Previous use   //Route::domain(env('DOMAIN'))->group(function () {

        // Copy image to another subdomain
        Route::get('getImage/{from}/{to}/{user_id}/{image_name}/{extension}', 'GetImageController@driver_doc');
        Route::get('getVehicleImage/{from}/{to}/{vehicle_id}/{image_name}/{extension}', 'GetImageController@vehicle_doc');
        Route::get('users/{user_id}/{image_name}/{extension}', 'GetImageController@driver_doc');
        Route::get('delete_from_admin_log_file', [LogsController::class, 'delete_from_admin_log_file']);
        Route::get('sys_log_from_api', [LogsController::class, 'sys_log_from_api']);





        Route::get('cache_clear', function () {
            Artisan::call('cache:clear');
            Artisan::call('optimize:clear');
            Cache::flush();
            echo "Cache Cleared";
        });


        Route::get('ocr_test', function () {
            $url = 'images/nid2.jpg';
            echo (new TesseractOCR($url))
                ->lang('eng', 'ben')
                ->run();
        });




        Route::post('pop_up_email', 'EmailController@pop_up_email');

        Route::get('oweAmount', 'Api\RatingController@oweAmount');
        Route::get('driver_invoice', 'DriverDashboardController@driver_invoice');
        Route::match(array('GET', 'POST'), 'apple_callback', 'UserController@apple_callback');
        Route::get('app/{type}', 'HomeController@redirect_to_app')->name('redirect_to_app');

        Route::group(['middleware' => 'canInstall'], function () {
            Route::group(['middleware' => 'locale'], function () {
                Route::get('/', 'HomeController@index');
            });
        });

        Route::get('/logout', function () {
            Auth::guard('company')->logout();                  
        });

        Route::get('import_csv', 'DashboardController@importCsvData');

        Route::get('clear_cache', function () {
            Artisan::call('cache:clear');
            $data = Artisan::output() . '<br>';
            Artisan::call('config:clear');
            $data .= Artisan::output() . '<br>';
            Artisan::call('view:clear');
            $data .= Artisan::output() . '<br>';
            Artisan::call('route:clear');
            $data .= Artisan::output() . '<br>';
            Artisan::call('telescope:clear');
            $data .= Artisan::output() . '<br>';
            return $data;
        });

        Route::get('add_index', function (Request $request) {
            if ((request()->has('table') && request()->table == 'schedule_ride') || !request()->has('table')) {
                \DB::statement('CREATE INDEX statusIndex ON schedule_ride(status);');
            }
            if ((request()->has('table') && request()->table == 'driver_location') || !request()->has('table')) {
                \DB::statement('CREATE INDEX latitudeIndex ON driver_location(latitude);');
                \DB::statement('CREATE INDEX longitudeIndex ON driver_location(longitude);');
                \DB::statement('CREATE INDEX statusIndex ON driver_location(status);');
            }
            if ((request()->has('table') && request()->table == 'car_type') || !request()->has('table')) {
                \DB::statement('CREATE INDEX statusIndex ON car_type(status);');
            }
            if ((request()->has('table') && request()->table == 'companies') || !request()->has('table')) {
                \DB::statement('CREATE INDEX statusIndex ON companies(status);');
            }
            if ((request()->has('table') && request()->table == 'vehicle') || !request()->has('table')) {
                \DB::statement('CREATE INDEX statusIndex ON vehicle(status);');
            }
            if ((request()->has('table') && request()->table == 'users') || !request()->has('table')) {
                \DB::statement('CREATE INDEX statusIndex ON users(status);');
                \DB::statement('CREATE INDEX user_typeIndex ON users(user_type);');
            }
            if ((request()->has('table') && request()->table == 'peak_fare_details') || !request()->has('table')) {
                \DB::statement('CREATE INDEX dayIndex ON peak_fare_details(day);');
                \DB::statement('CREATE INDEX start_timeIndex ON peak_fare_details(start_time);');
                \DB::statement('CREATE INDEX end_timeIndex ON peak_fare_details(end_time);');
            }
            if ((request()->has('table') && request()->table == 'request') || !request()->has('table')) {
                \DB::statement('CREATE INDEX statusIndex ON request(status);');
            }
            if ((request()->has('table') && request()->table == 'currency') || !request()->has('table')) {
                \DB::statement('CREATE INDEX statusIndex ON currency(status);');
                \DB::statement('CREATE INDEX default_currencyIndex ON currency(default_currency);');
                \DB::statement('CREATE INDEX paypal_currencyIndex ON currency(paypal_currency);');
            }
            if ((request()->has('table') && request()->table == 'payout_credentials') || !request()->has('table')) {
                \DB::statement('CREATE INDEX typeIndex ON payout_credentials(type);');
            }
            if ((request()->has('table') && request()->table == 'language') || !request()->has('table')) {
                \DB::statement('CREATE INDEX valueIndex ON language(value);');
            }
            if ((request()->has('table') && request()->table == 'documents') || !request()->has('table')) {
                \DB::statement('CREATE INDEX statusIndex ON documents(status);');
            }
            if ((request()->has('table') && request()->table == 'driver_documents') || !request()->has('table')) {
                \DB::statement('CREATE INDEX typeIndex ON driver_documents(type);');
                \DB::statement('CREATE INDEX vehicle_idIndex ON driver_documents(vehicle_id);');
                \DB::statement('CREATE INDEX user_idIndex ON driver_documents(user_id);');
            }
            if ((request()->has('table') && request()->table == 'supports') || !request()->has('table')) {
                \DB::statement('CREATE INDEX statusIndex ON supports(status);');
            }
        });



        Route::get('remove_index', function (Request $request) {
            if ((request()->has('table') && request()->table == 'schedule_ride') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `schedule_ride` DROP INDEX `statusIndex`;');
            }
            if ((request()->has('table') && request()->table == 'driver_location') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `driver_location` DROP INDEX `latitudeIndex`;');
                \DB::statement('ALTER TABLE `driver_location` DROP INDEX `longitudeIndex`;');
                \DB::statement('ALTER TABLE `driver_location` DROP INDEX `statusIndex`;');
            }
            if ((request()->has('table') && request()->table == 'car_type') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `car_type` DROP INDEX `statusIndex`;');
            }
            if ((request()->has('table') && request()->table == 'companies') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `companies` DROP INDEX `statusIndex`;');
            }
            if ((request()->has('table') && request()->table == 'vehicle') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `vehicle` DROP INDEX `statusIndex`;');
            }
            if ((request()->has('table') && request()->table == 'users') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `users` DROP INDEX `statusIndex`;');
                \DB::statement('ALTER TABLE `users` DROP INDEX `user_typeIndex`;');
            }
            if ((request()->has('table') && request()->table == 'peak_fare_details') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `peak_fare_details` DROP INDEX `dayIndex`;');
                \DB::statement('ALTER TABLE `peak_fare_details` DROP INDEX `start_timeIndex`;');
                \DB::statement('ALTER TABLE `peak_fare_details` DROP INDEX `end_timeIndex`;');
            }
            if ((request()->has('table') && request()->table == 'request') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `request` DROP INDEX `statusIndex`;');
            }
            if ((request()->has('table') && request()->table == 'currency') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `currency` DROP INDEX `statusIndex`;');
                \DB::statement('ALTER TABLE `currency` DROP INDEX `default_currencyIndex`;');
                \DB::statement('ALTER TABLE `currency` DROP INDEX `paypal_currencyIndex`;');
            }
            if ((request()->has('table') && request()->table == 'payout_credentials') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `payout_credentials` DROP INDEX `typeIndex`;');
            }
            if ((request()->has('table') && request()->table == 'language') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `language` DROP INDEX `valueIndex`;');
            }
            if ((request()->has('table') && request()->table == 'documents') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `documents` DROP INDEX `statusIndex`;');
            }
            if ((request()->has('table') && request()->table == 'driver_documents') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `driver_documents` DROP INDEX `typeIndex`;');
                \DB::statement('ALTER TABLE `driver_documents` DROP INDEX `vehicle_idIndex`;');
                \DB::statement('ALTER TABLE `driver_documents` DROP INDEX `user_idIndex`;');
            }
            if ((request()->has('table') && request()->table == 'supports') || !request()->has('table')) {
                \DB::statement('ALTER TABLE `supports` DROP INDEX `statusIndex`;');
            }
        });



        // Route::any('payment/callback', 'TestController@callback');



        Route::group(['middleware' => 'locale'], function () {
            // Route::get('pay_to_admin/{token}/{amount}', 'HomeController@pay_to_admin');

            // Route::get('pay_to_admin_test', 'TestController@index');

            Route::get('help', 'HomeController@help');
            Route::get('help/topic/{id}/{category}', 'HomeController@help');
            Route::get('help/article/{id}/{question}', 'HomeController@help');
            Route::get('ajax_help_search', 'HomeController@ajax_help_search');

            Route::post('set_session', 'HomeController@set_session');
            Route::get('user_disabled', 'UserController@user_disabled');

            Route::match(array('GET', 'POST'), 'signin_driver', 'UserController@signin_driver');
            Route::match(array('GET', 'POST'), 'signin_rider', 'UserController@signin_rider')->name('rider.signin');
             Route::match(array('GET', 'POST'), 'signin_company', 'UserController@signin_company');
            Route::get('facebook_login', 'UserController@facebook_login');
            Route::get('forgot_password_driver', 'UserController@forgot_password');
            Route::get('forgot_password_rider', 'UserController@forgot_password');
            Route::get('forgot_password_company', 'UserController@forgot_password');
            Route::post('forgotpassword', 'UserController@forgotpassword');
            Route::match(array('GET', 'POST'), 'reset_password', 'UserController@reset_password');
            Route::match(array('GET', 'POST'), 'company/reset_password', 'UserController@company_reset_password');
            Route::get('forgot_password_link/{id}', 'EmailController@forgot_password_link');
            Route::match(array('GET', 'POST'), 'signup_rider', 'UserController@signup_rider');
            Route::match(array('GET', 'POST'), 'signup_driver', 'UserController@signup_driver');
            Route::match(array('GET', 'POST'), 'signup_company', 'UserController@signup_company');

            Route::get('facebookAuthenticate', 'UserController@facebookAuthenticate');
            Route::get('googleAuthenticate', 'UserController@googleAuthenticate');

            Route::view('signin', 'user.signin');
            Route::view('signup', 'user.signup');

            Route::view('safety', 'ride.safety');
            Route::view('ride', 'ride.ride');

            Route::view('drive', 'drive.drive');
            Route::view('requirements', 'drive.requirements');
            Route::view('driver_app', 'drive.driver_app');
            Route::view('drive_safety', 'drive.drive_safety');

            // signup functionality
            Route::post('rider_register', 'UserController@rider_register');
            Route::post('driver_register', 'UserController@driver_register');
            Route::post('company_register', 'UserController@company_register');
            Route::post('login', 'UserController@login');
            Route::post('login_driver', 'UserController@login_driver');
            Route::post('ajax_trips/{id}', 'DashboardController@ajax_trips');

            Route::post('change_mobile_number', 'DriverDashboardController@change_mobile_number');
            Route::post('profile_upload', 'DriverDashboardController@profile_upload');
            Route::get('download_invoice/{id}', 'DriverDashboardController@download_invoice');
            Route::get('download_rider_invoice/{id}', 'DashboardController@download_rider_invoice');
        });

        // Rider Routes..
        Route::group(['middleware' => ['locale', 'rider_guest']], function () {
            Route::get('trip', 'DashboardController@trip')->name('rider.trips');
            Route::get('profile', 'DashboardController@profile');
            Route::view('payment', 'dashboard.payment');
            Route::get('trip_detail/{id}', 'DashboardController@trip_detail');
            Route::post('rider_rating/{rating}/{trip_id}', 'DashboardController@rider_rating');
            Route::post('trip_detail/rider_rating/{rating}/{trip_id}', 'DashboardController@rider_rating');
            Route::get('trip_invoice/{id}', 'DashboardController@trip_invoice');
            Route::get('invoice_download/{id}', 'DashboardController@invoice_download');
            Route::post('rider_update_profile/{id}', 'DashboardController@update_profile');
            Route::get('referral', 'DashboardController@referral')->name('referral');
            Route::post('ajax_referral_data/{id}', 'DashboardController@ajax_referral_data');
        });

        // Driver Routes..
        Route::group(['middleware' => ['locale', 'driver_guest']], function () {
            Route::get('driver_profile', 'DriverDashboardController@driver_profile');
            Route::get('documents/{id}', 'DriverDashboardController@documents')->name('documents');
            Route::get('vehicle/{id}', 'DriverDashboardController@showvehicle')->name('vehicle');
            Route::post('driver_document', 'DriverDashboardController@driver_document_upload');
            Route::get('add_vehicle', 'DriverDashboardController@add_vehicle')->name('add_vehicle');
            Route::get('edit_vehicle/{id}', 'DriverDashboardController@edit_vehicle')->name('edit_vehicle');
            Route::get('delete_vehicle/{id}', 'DriverDashboardController@delete_vehicle');
            Route::get('default_vehicle/{id}', 'DriverDashboardController@default_vehicle');
            Route::post('makelist', 'DriverDashboardController@makeListValue');
            Route::post('update_vehicle', 'DriverDashboardController@update_vehicle');
            Route::get('driver_payment', 'DriverDashboardController@driver_payment');

            Route::get('paid_to_alesha', 'DriverDashboardController@paid_to_alesha');
            Route::get('received_from_alesha/{type}', 'DriverDashboardController@received_from_alesha');

            Route::get('driver_invoice/{id}', 'DriverDashboardController@driver_invoice');
            Route::view('driver_banking', 'driver_dashboard.driver_banking');
            Route::view('driver_trip', 'driver_dashboard.driver_trip');
            Route::get('driver_trip_detail/{id}', 'DriverDashboardController@driver_trip_detail');

            Route::post('ajax_payment', 'DriverDashboardController@ajax_payment');
            Route::get('driver_referral', 'DashboardController@driver_referral')->name('driver_referral');

            // profile update
            Route::post('driver_update_profile/{id}', 'DriverDashboardController@driver_update_profile');
            Route::get('driver_invoice', 'DriverDashboardController@show_invoice');
            Route::get('print_invoice/{id}', 'DriverDashboardController@print_invoice');

            // Payout Preferences
            Route::get('payout_preferences', 'UserController@payoutPreferences')->name('driver_payout_preference');
            Route::post('update_payout_preference', 'UserController@updatePayoutPreference')->name('update_payout_preference');
            Route::get('payout_delete/{id}', 'UserController@payoutDelete')->where('id', '[0-9]+')->name('payout_delete');
            Route::get('payout_default/{id}', 'UserController@payoutDefault')->where('id', '[0-9]+')->name('payout_default');
        });

        Route::get('sign_out', function () {
            $user_type = @Auth::user()->user_type;
            Auth::logout();
            if (@$user_type == 'Rider') {
                return redirect('signin_rider');
            } else {
                return redirect('signin_driver');
            }
        });

        Route::group(['prefix' => (LOGIN_USER_TYPE == 'company') ? 'company' : 'admin', 'middleware' => 'admin_guest'], function () {

            if (LOGIN_USER_TYPE == 'company') {
                Route::get('logout', function () {
                    Auth::guard('company')->logout();
                    return redirect('signin_company');
                });
                Route::get('profile', function () {
                    return redirect('company/edit_company/' . auth('company')->id());
                });

                Route::match(['get', 'post'], 'payout_preferences', 'CompanyController@payout_preferences')->name('company_payout_preference');
                Route::post('update_payout_preference', 'CompanyController@updatePayoutPreference')->name('company.update_preference');
                Route::get('update_payout_settings', 'CompanyController@payoutUpdate')->name('company.update_payout_settings');
                Route::post('set_session', 'HomeController@set_session');
            }
        });

        Route::match(array('GET', 'POST'), 'admin/logs_login', function (\Illuminate\Http\Request $r) { 
            if(!Auth::guard('admin')->user()){
                if(Auth::guard('admin')->attempt(['username' => $r->username, 'password' => $r->password])){
                    return redirect('admin/all_logs');
                }else{
                    return redirect('/');
                }
            }  else{
                return redirect('admin/all_logs');
            }   
        });

        

        //Route::group(['middleware' => ['locale', 'driver_guest']], function () {
        Route::group(['prefix' => '', 'middleware' => 'admin_can:all_log'], function () {
            //For logviewer
            Route::prefix('admin/all_logs')
            ->namespace('\Arcanedev\LogViewer\Http\Controllers')
            ->group(function () {
                Route::get('/', 'LogViewerController@index')
                    ->name('log-viewer::dashboard');

                    Route::get('logout', function () {
                        if(Auth::guard('admin')->user()){
                            Auth::guard('admin')->logout();
                            return redirect('/');
                        }
                    });

        
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



        Route::get('clear__l--log', 'HomeController@clearLog');
        Route::get('show__l--log', 'HomeController@showLog');
        Route::get('update__env--content', 'HomeController@updateEnv');
        Route::get('clear__distance--log', 'HomeController@clearDistanceLog');
        Route::get('show__distance--log', 'HomeController@showDistanceLog');

        Route::get('test', 'UserController@test');

        Route::get('test_email', 'EmailTestController@index'); //for test purpose
        Route::get('invoice/{rider_id_base64}/{trip_id_base64}', 'Invoice@index');
        Route::get('invoice_email/{rider_id}/{trip_id}', 'Invoice@invoice_email');
        Route::get('user/verify/{user_id_base64}', 'Invoice@verify_user');
        Route::match(['get', 'post'], 'contact_us', 'HomeController@contact_us');

        //bottom route
        Route::get('{name}', 'HomeController@static_pages');
        Route::get('fare_estimation/test', 'PaymentHelperTest@fare_estimation');
        /*localhost/fare_estimation/test?mobile_number=10003&pickup_latitude=23.7938275&drop_latitude=23.7940879&&pickup_longitude=90.40437631&drop_longitude=90.404407&manual_booking_id=1*/
        // Route::get('getImage/{user_id}/{image_name}/{extension}', 'GetImageController@driver_doc');


    }); // domain
}


// Route::get('log_list', 'HomeController@log_list');
// Route::get('delete_log_file/{file_name}', 'HomeController@delete_log_file');

// Static page route /{$applied_referral_amount?}
//https://www.alesharide.com/nagad/15212087/1
//Route::get('nagad/{mobile_number}/{amount}/{applied_referral_amount?}','NagadController@index');

// payment_type=driver_owe_amount/company_owe_amount/rider_fare 
//payment_gateway = nagad/bkash/rocket etc, 

//http://localhost/payment_by_web/nagad/driver_owe_amount/10071/1/0
//http://localhost/payment_by_web/nagad/rider_fare/99/66




$domains = array('www.' . env('PAYMENT_DOMAIN') . '.' . env('DOMAIN'), env('PAYMENT_DOMAIN') . '.' . env('DOMAIN'));

foreach ($domains as $domain) {
    Route::domain($domain)->group(function () {



        //Route::domain(env('PAYMENT_DOMAIN', 'payment') . '.' . env('DOMAIN'))->group(function () {
        Route::get('payment_by_web/{payment_gateway}/{payment_type}/{user_id_or_trip_id}/{amount}/{applied_referral_amount?}/{redirect_url?}', 'PaymentApi\PaymentByWebController@index');
        //call back url for payment gaeway
        Route::get('nagad/callback', 'PaymentApi\NagadController@callback');

        Route::get('clear_cache', function () {
            Artisan::call('cache:clear');
            Artisan::call('optimize:clear');
            Cache::flush();
            echo "Cache Cleared";
        });
    });
}
