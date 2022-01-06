<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use DB;
use App\Http\Controllers\CustomLog;

class HomeController extends Controller
{
    public function __construct()
    {
        DB::enableQueryLog();
    }

    public function commonData(Request $request)
    {
        Log::info("common_data Api Stp:1 :", $request->all());

        $user_details = JWTAuth::parseToken()->authenticate();

        $site_settings = resolve('site_settings');
        $api_credentials = resolve('api_credentials');
        $payment_gateway = resolve('payment_gateway');
        $fees = resolve('fees');

        $return_data = [
            'status_code'       => '1',
            'status_message'    => __('messages.api.listed_successfully'),
        ];

        $heat_map = $site_settings->where('name', 'heat_map')->first()->value;
        $heat_map = ($heat_map == 'On') ? 1 : 0;

        $sinch_key = $api_credentials->where('name', 'sinch_key')->first()->value;
        $sinch_secret_key = $api_credentials->where('name', 'sinch_secret_key')->first()->value;

        $braintree_env = Cache()->remember('braintree_env', Config('cache.one_year'), function () {
            return payment_gateway('mode', 'Braintree');
        });
        //$braintree_env = payment_gateway('mode', 'Braintree');

        $braintree_public_key = Cache()->remember('braintree_public_key', Config('cache.one_year'), function () {
            return payment_gateway('public_key', 'Braintree');
        });
        //$braintree_public_key = payment_gateway('public_key', 'Braintree');

        $paypal_client = Cache()->remember('paypal_client', Config('cache.one_year'), function () {
            return payment_gateway('client', 'Paypal');
        });
        //$paypal_client = payment_gateway('client', 'Paypal');

        $paypal_mode = Cache()->remember('paypal_mode', Config('cache.one_year'), function () {
            return payment_gateway('mode', 'Paypal');
        });
        //$paypal_mode = payment_gateway('mode', 'Paypal');

        $paypal_mode = ($paypal_mode == 'sandbox') ? 0 : 1;

        $stripe_publish_key = Cache()->remember('stripe_publish_key', Config('cache.one_year'), function () {
            return payment_gateway('publish', 'Stripe');
        });
        //$stripe_publish_key = payment_gateway('publish', 'Stripe');

        $enable_referral = Cache()->remember('enable_referral' . $request->user_type, Config('cache.one_week'), function () use ($request) {
            $referral_settings = resolve('referral_settings');

            $referral_settings = $referral_settings->where('user_type', ucfirst($request->user_type))->where('name', 'apply_referral')->first();

            $enable_referral = (@$referral_settings->value == "1");
        });

        // $referral_settings = resolve('referral_settings');
        // $referral_settings = $referral_settings->where('user_type', ucfirst($request->user_type))->where('name', 'apply_referral')->first();
        //$enable_referral = (@$referral_settings->value == "1");


        $apply_extra_fee = Cache()->remember('apply_extra_fee', Config('cache.one_week'), function () use ($fees) {
            return @$fees->where('name', 'additional_fee')->first()->value;
        });
        //$apply_extra_fee = @$fees->where('name', 'additional_fee')->first()->value;


        $apply_trip_extra_fee = ($apply_extra_fee == 'Yes');

        $admin_contact  = MANUAL_BOOK_CONTACT;
        $google_map_key = MAP_SERVER_KEY;
        $fb_id          = FB_CLIENT_ID;

        $status = $user_details->status ?? 'Inactive';

        $gateway_type = "Stripe";

        $payment_details = PaymentMethod::where('user_id', $user_details->id)->first();
        $brand  = optional($payment_details)->brand ?? '';
        $last4  = (string)optional($payment_details)->last4 ?? '';

        $update_loc_interval = site_settings('update_loc_interval');

        $trip_default = Cache()->remember('trip_default', Config('cache.one_week'), function () {
            return payment_gateway('trip_default', 'Common');
        });
        //$trip_default = payment_gateway('trip_default', 'Common');

        $wallet_default = Cache()->remember('wallet_default', Config('cache.one_year'), function () {
            return payment_gateway('wallet_default', 'Common');
        });
        //$wallet_default = payment_gateway('wallet_default', 'Common');

        $driver_km = Cache()->remember('driver_km', Config('cache.one_year'), function () {
            return site_settings('driver_km');
        });
        //$driver_km = site_settings('driver_km');

        $pickup_km = Cache()->remember('pickup_km', Config('cache.one_year'), function () {
            return site_settings('pickup_km');
        });
        //$pickup_km = site_settings('pickup_km');

        $drop_km = Cache()->remember('drop_km', Config('cache.one_year'), function () {
            return site_settings('drop_km');
        });
        //$drop_km   = site_settings('drop_km');

        $common_data = compact(
            'heat_map',
            'sinch_key',
            'sinch_secret_key',
            'apply_trip_extra_fee',
            'admin_contact',
            'status',
            'braintree_env',
            'braintree_public_key',
            'google_map_key',
            'fb_id',
            'paypal_client',
            'paypal_mode',
            'stripe_publish_key',
            'gateway_type',
            'brand',
            'last4',
            'update_loc_interval',
            'trip_default',
            'wallet_default',
            'driver_km',
            'pickup_km',
            'drop_km',
        );

        Log::info("common_data Api Stp:2 :", $common_data);

        $driver_data = array();
        if ($user_details->user_type == 'Driver') {

            $payout_methods = Cache()->remember('payout_methods-' . $user_details->company_id, Config('cache.one_month'), function () use ($user_details) {
                return getPayoutMethods($user_details->company_id);
            });
            //$payout_methods = getPayoutMethods($user_details->company_id);

            foreach ($payout_methods as $payout_method) {
                $payout_list[] = ["key" => $payout_method, 'value' => snakeToCamel($payout_method)];
            }

            $driver_data = compact('payout_list');
        }

        // get firebase token
        $user = User::find($user_details->id);

        $firebase_token = Cache()->remember('firebase_token-' . $user->id, Config('cache.one_month'), function () use ($user) {
            $firebase = resolve("App\Services\FirebaseService");
            //return $firebase->createCustomToken($user->email . ' - ' . $user->user_type);
            return $firebase->createCustomToken("0" . $user->mobile_number . ' - ' . $user->user_type);
        });
        // $firebase = resolve("App\Services\FirebaseService");
        // $firebase_token = $firebase->createCustomToken($user->email . ' - ' . $user->user_type);


        // save token
        $user->firebase_token = $firebase_token;
        $user->save();

        // return token
        $return_data['firebase_token'] = $firebase_token;

        CustomLog::info("common_data Api Stp:3 :");

        return response()->json(array_merge($return_data, $common_data, $driver_data));
    }

    public function domain_session_post(Request $request){
        //  // API URL
        //  $url = '//commonapi.alesharide.com/api/domain_session_set';

        //  // Create a new cURL resource
        //  $ch = curl_init($url);
 
        //  // Setup request to send json via POST
        //  $data = array(
        //      'username_from_main_domain' => $request->username_from_main_domain,
        //      'password_from_main_domain' => $request->password_from_main_domain,
        //  );
        //  $payload = json_encode(array("user" => $data));
 
        //  // Attach encoded JSON string to the POST fields
        //  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
 
        //  // Set the content type to application/json
        //  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
 
        //  // Return response instead of outputting
        //  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        //  // Execute the POST request
        //  $result = curl_exec($ch);
        //  //echo "aa";

        //  print_r($result);
 
        //  // Close cURL resource
        //  curl_close($ch);





        //$cof_data = Config('verification.nid');
         /*$urll="http://commonapi.localhost/api/domain_session_set";
		
		$PostData = array(
						'username_from_main_domain' => $request->username_from_main_domain, 
					);

		$url = curl_init($urll);
        $postToken = json_encode($PostData);

        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $postToken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false); 
        
        echo $resultData = curl_exec($url);*/
       
        // $ResultArray = json_decode($resultData, true);
        // curl_close($url);
        // return $ResultArray;


       // $url="https://commonapi.alesharide.com/api/domain_session_set";

         $url="https://admin.alesharide.com/admin/session_set_from_another_domain";
      
    
        $PostData = array(
            'username_from_main_domain' => 'cross test session',
            'password_from_main_domain' => 'cross password session',
             
        );

        $url = curl_init($url);
        $postToken = json_encode($PostData);
        $header = array(
        'Content-Type:application/json'
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $postToken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false); 

        $resultData = curl_exec($url);
        print_r($resultData);
        



    }

    public function domain_session_set(Request $request){
       
        // session([
        //     'username_from_main_domain' => $request->username_from_main_domain,
        //     'password_from_main_domain' => $request->password_from_main_domain,
        // ]);

        return $return_data = array(
            'username_from_main_domain' => $request->username_from_main_domain,
        );

        


    }

    /**
     * Get Payment List
     * 
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function getPaymentList(Request $request)
    {
        Log::info("get_payment_list Api Stp:1 :", $request->all());

        $user_details = JWTAuth::parseToken()->authenticate();

        $payment_methods = collect(PAYMENT_METHODS);
        $payment_methods = $payment_methods->reject(function ($value) {
            $is_enabled = payment_gateway('is_enabled', ucfirst($value['key']));
            return ($is_enabled != '1');
        });

        $is_wallet = $request->is_wallet == "1";

        $default_paymode = Cache()->remember('default_paymode', Config('cache.one_year'), function () {
            return payment_gateway('trip_default', 'Common');
        });
        //$default_paymode = payment_gateway('trip_default', 'Common');

        $payment_list = array();

        $payment_methods->each(function ($payment_method) use (&$payment_list, $default_paymode, $user_details, $is_wallet) {

            if ($payment_method['key'] == 'cash' && $is_wallet) {
                $skip_payment = true;
            }
            $payment_method['value'] = \Lang::get('messages.api.' . $payment_method['value']);
            if ($payment_method['key'] == 'stripe') {
                $payment_details = PaymentMethod::where('user_id', $user_details->id)->first();

                if ($payment_details != '') {
                    $last4  = strval($payment_details->last4);
                    $payment_method['value'] = 'xxxx xxxx xxxx ' . $last4;

                    $stripe_card = array(
                        "key"           => "stripe_card",
                        "value"         => \Lang::get('messages.api.change_debit_card'),
                        "is_default"    => false,
                        "icon"          => asset("images/icon/card.png"),
                    );
                } else {
                    $stripe_card = array(
                        "key"           => "stripe_card",
                        "value"         => \Lang::get('messages.api.add_debit_card'),
                        "is_default"    => ($default_paymode == $payment_method['key']),
                        "icon"          => asset("images/icon/card.png"),
                    );
                    $skip_payment = true;
                }
            }

            if (!isset($skip_payment)) {
                $payMethodData = array(
                    "key"       => $payment_method['key'],
                    "value"     => $payment_method['value'],
                    "icon"      => $payment_method['icon'],
                    "is_default" => ($default_paymode == $payment_method['key']),
                );
                array_push($payment_list, $payMethodData);
            }

            if (isset($stripe_card)) {
                array_push($payment_list, $stripe_card);
            }
        });


        $return_data = array(
            'status_code'       => '1',
            'status_message'    => __('messages.api.listed_successfully'),
            'payment_list'    => $payment_list,
        );

        CustomLog::info("get_payment_list Api Stp:2 :");
        return response()->json($return_data);
    }
}
