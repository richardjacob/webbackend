<?php


// cron request for schedule ride
Route::get('cron_request_car', 'CronController@requestCars');
Route::get('cron_offline', 'CronController@updateOfflineUsers');
Route::get('currency_cron', 'CronController@updateCurrency');
Route::get('update_referral_cron', 'CronController@updateReferralStatus');
Route::match(['get', 'post'], 'paypal_payout', 'CronController@updatePaypalPayouts');
Route::get('set_driver_online_time', 'CronController@set_driver_online_time');

//cron Test
Route::get('test', 'CronController@test');


// Common APIs Start

Route::domain(env('COMMON_API_SUB_DOMAIN', 'commonapi') . '.' . env('DOMAIN'))->group(function () {

    Route::get('cache_clear', function () {
        Artisan::call('cache:clear');
        Artisan::call('optimize:clear');
        Cache::flush();
        echo "Cache Cleared";
    });

    Route::get('domain_session_post', 'HomeController@domain_session_post');
    Route::post('domain_session_set', 'HomeController@domain_session_set');

    //No Auth
    Route::get('check_version', 'RiderController@check_version'); //N
    Route::get('numbervalidation', 'TokenAuthController@numbervalidation');
    Route::get('registration_user', 'TokenAuthController@register');
    Route::get('socialsignup', 'TokenAuthController@socialsignup');
    Route::match(array('GET', 'POST'), 'apple_callback', 'TokenAuthController@apple_callback');
    Route::get('login', 'TokenAuthController@login');
    Route::get('emailvalidation', 'TokenAuthController@emailvalidation'); //N
    Route::get('forgotpassword', 'TokenAuthController@forgotpassword');
    Route::get('language_list', 'TokenAuthController@language_list');
    Route::get('currency_list', 'TokenAuthController@currency_list');
    Route::get('verify_nid', 'TokenAuthController@verify_nid');

    Route::get('app_log_save', 'AppLogController@app_log_save');
    Route::get('app_log', 'AppLogController@app_log');
    Route::get('app_log_search', 'AppLogController@app_log_search');
    Route::get('send_verify_email', 'TokenAuthController@send_verify_email');

    Route::match(array('GET', 'POST'), 'ComplainFieldsWithCategory', 'ComplainController@ComplainFieldsWithCategory');
    Route::match(array('GET', 'POST'), 'addComplain', 'ComplainController@addComplain');
    Route::match(array('GET', 'POST'), 'complainHistory', 'ComplainController@complainHistory');
    Route::match(array('GET', 'POST'), 'messagePCR', 'ComplainController@messagePCR');
    

    // Common API for Both Driver & Rider
    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::get('clear_cache', function () {
            Artisan::call('cache:clear');
            Artisan::call('optimize:clear');
            Cache::flush();
            //return redirect('admin/dashboard');
        });

        Route::match(array('GET', 'POST'), 'common_data', 'HomeController@commonData');
        Route::post('get_payment_list', 'HomeController@getPaymentList');
        Route::get('logout', 'TokenAuthController@logout');
        Route::get('language', 'TokenAuthController@language');
        Route::match(array('GET', 'POST'), 'upload_profile_image', 'ProfileController@upload_profile_image');
        Route::match(['GET', 'POST'], 'currency_conversion', 'TokenAuthController@currency_conversion');
        Route::get('update_device', 'TokenAuthController@updateDevice');
        Route::get('update_user_currency', 'ProfileController@update_user_currency');
        Route::get('trip_comment', 'RatingController@trip_comment');
        Route::get('toll_reasons', 'TripController@toll_reasons');
        Route::get('cancel_reasons', 'TripController@cancel_reasons');
        Route::get('cancel_trip', 'TripController@cancel_trip');
        Route::get('schedule_ride_cancel', 'RiderController@schedule_ride_cancel');
        Route::get('get_trip_details', 'TripController@get_trip_details');
        Route::get('get_referral_details', 'ReferralsController@get_referral_details');



        Route::get('send_message', 'TripController@send_message'); //N
        Route::get('trip_rating', 'RatingController@trip_rating');
        Route::get('get_past_trips', 'TripController@get_past_trips'); //Move To riderapi Delete
        Route::get('country_list', 'DriverController@country_list'); //N
        Route::get('get_caller_detail', 'ProfileController@get_caller_detail');
        Route::get('get_invoice', 'RatingController@getinvoice');

        // Route::group(['prefix' => 'v2',  'middleware' => 'jwt.verify'], function () {
        // 	Route::get('get_past_trips', 'TripController@get_past_trips_v2');
        // });

    });

    Route::group(['prefix' => 'v3',  'middleware' => 'jwt.verify'], function () {
        Route::get('bonus_list', 'BonusController@bonus_list');
    });
    Route::get('test_email', 'EmailTestController@index');
});

// Common APIs End



// Rider  APIs start

Route::domain(env('RIDER_API_SUB_DOMAIN', 'riderapi') . '.' . env('DOMAIN'))->group(function () {
    Route::get('clear_cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('optimize:clear');
        Cache::flush();
        //return redirect('admin/dashboard');
    });

    Route::get('cache_clear', function () {
        Artisan::call('cache:clear');
        Artisan::call('optimize:clear');
        Cache::flush();
        echo "Cache Cleared";
    });

    Route::group(['middleware' => 'jwt.verify'], function () {
        Route::get('get_rider_profile', 'ProfileController@get_rider_profile');
        // Route::post('rider_profile_get', 'ProfileController@get_rider_profile');
        Route::get('update_rider_location', 'ProfileController@update_rider_location');
        Route::get('updateriderlocation', 'RiderController@updateriderlocation');


        // Route::get('search_cars', 'RiderController@searchCars');
        Route::get('search_cars', 'RiderController@searchCars_v2');



        Route::post('request_cars', 'RiderController@requestCars');
        // Added By arif vai
        Route::post('request_cancel', 'RiderController@requestCancel');
        // Added By arif vai end
        Route::post('after_payment', 'EarningController@afterPayment');
        Route::get('track_driver', 'RiderController@track_driver');
        //Route::get('get_past_trips','TripController@get_past_trips'); //EXIST IN COMMON API // DELETE FROM POSTMAN
        Route::get('get_upcoming_trips', 'TripController@get_upcoming_trips');
        Route::post('add_wallet', 'EarningController@add_wallet');
        Route::get('add_card_details', 'ProfileController@add_card_details');
        Route::get('add_promo_code_ap', 'EarningController@add_promo_code');
        Route::get('sos', 'RiderController@sos');
        Route::get('sosalert', 'RiderController@sosalert');  //N
        Route::get('promo_details', 'RiderController@promo_details');
        Route::get('get_nearest_vehicles', 'RiderController@get_nearest_vehicles');
        Route::post('save_schedule_ride', 'RiderController@save_schedule_ride');
        Route::get('get_invoice', 'RatingController@getinvoice'); //Move To commonapi Delete

        Route::get('update_rider_profile', 'ProfileController@update_rider_profile'); // N
        Route::get('bonus_status', 'BonusController@bonus_status');
        Route::get('get_past_trips', 'TripController@get_past_trips');
    });

    Route::group(['prefix' => 'v2',  'middleware' => 'jwt.verify'], function () {
        Route::get('bonus_status', 'BonusController@bonus_status_v2');
        // Route::get('get_past_trips', 'TripController@get_past_trips_v2');
    });

    // Route::group(['prefix' => 'v3',  'middleware' => 'jwt.verify'], function () {
    // 	Route::get('bonus_status', 'BonusController@bonus_status_v3');
});


Route::get('test_email', 'EmailTestController@index');
//});

// Rider  APIs end



// Driver APIs start

Route::domain(env('DRIVER_API_SUB_DOMAIN', 'driverapi') . '.' . env('DOMAIN'))->group(function () {

    Route::get('cache_clear', function () {
        Artisan::call('cache:clear');
        Artisan::call('optimize:clear');
        Cache::flush();
        echo "Cache Cleared";
    });

    Route::group(['middleware' => 'jwt.verify'], function () {

        Route::post('update_vehicle', 'DriverController@updateVehicle');
        Route::get('get_completed_trips', 'TripController@get_completed_trips');
        Route::get('get_pending_trips', 'TripController@get_pending_trips');
        Route::get('get_driver_profile', 'ProfileController@get_driver_profile');
        Route::get('earning_chart', 'EarningController@earning_chart');
        Route::get('updatelocation', 'DriverController@updateLocation');
        Route::get('customUpdateLocation', 'DriverController@customUpdateLocation');
        Route::get('updateLocation_without_trips', 'DriverController@updateLocation_without_trips');

        Route::post('update_document', 'DriverController@update_document');
        Route::get('vehicle_details', 'ProfileController@vehicleDetails');
        Route::get('vehicle_descriptions', 'ProfileController@vehicleDescriptions');


        //  Route::get('accept_request', 'TripController@acceptTrip');
        Route::get('accept_request', 'TripController@acceptTrip_v2');


        //Route::get('cancel_trip', 'TripController@cancel_trip'); //EXIST IN COMMON API // DELETE FROM POSTMAN
        Route::get('arive_now', 'TripController@arriveNow');
        Route::get('begin_trip', 'TripController@beginTrip');
        Route::match(array('GET', 'POST'), 'end_trip', 'TripController@end_trip');
        Route::get('get_invoice', 'RatingController@getinvoice'); //Move To commonapi Delete
        //Route::get('trip_rating', 'RatingController@trip_rating'); //EXIST IN COMMON API // DELETE FROM POSTMAN
        Route::get('update_driver_profile', 'ProfileController@update_driver_profile');
        //Route::get('get_rider_profile', 'ProfileController@get_rider_profile'); //EXIST IN RIDER API // DELETE FROM POSTMAN
        Route::get('driver_rating', 'RatingController@driver_rating');
        Route::get('rider_feedback', 'RatingController@rider_feedback');
        Route::get('check_status', 'DriverController@checkStatus');
        Route::get('cash_collected', 'DriverController@cash_collected');
        Route::get('get_payout_list', 'PayoutDetailController@getPayoutPreference');
        Route::post('update_payout_preference', 'PayoutDetailController@updatePayoutPreference');
        Route::get('weekly_trip', 'PayoutDetailController@weeklyTrip');
        Route::get('weekly_statement', 'PayoutDetailController@weeklyStatement');
        Route::get('daily_statement', 'PayoutDetailController@dailyStatement');
        Route::post('pay_to_admin', 'DriverController@pay_to_admin');


        Route::match(array('GET', 'POST'), 'map_upload', 'TripController@map_upload'); //N
        Route::get('heat_map', 'MapController@heat_map'); //N
        // Route::get('update_vehicle_details', 'ProfileController@updateVehicleDetails'); //N
        Route::get('get_caller_detail', 'ProfileController@get_caller_detail'); //Move To commonapi Delete
        // Route::get('get_card_details', 'ProfileController@get_card_details'); //N
        // Route::get('stripe_supported_country_list', 'PayoutDetailController@stripeSupportedCountryList'); //N
        // Route::get('earning_list', 'PayoutDetailController@earningList'); //N
        Route::post('delete_vehicle', 'DriverController@deleteVehicle'); //N
        Route::get('update_default_vehicle', 'DriverController@updateDefaultVehicle'); //N
        Route::get('bonus_status', 'BonusController@bonus_status'); //N
        Route::get('check_driver_balance', 'DriverController@check_driver_balance'); //N
        Route::get('driver_balance_withdraw_req', 'DriverController@driver_balance_withdraw'); //N
        Route::get('transaction_history', 'TransactionHistoryController@index');
    });

    Route::group(['prefix' => 'v2',  'middleware' => 'jwt.verify'], function () {
        Route::get('check_driver_balance', 'DriverController@check_driver_balance_v2');
        Route::get('bonus_status', 'BonusController@bonus_status_v2');
        Route::get('get_driver_document', 'DriverController@get_driver_document');
    });



    // Route::group(['prefix' => 'v3',  'middleware' => 'jwt.verify'], function () {
    // 	Route::get('bonus_status', 'BonusController@bonus_status_v3');
    // });

    Route::get('test_email', 'EmailTestController@index');
});


// Driver  APIs end
