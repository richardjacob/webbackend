<?php

/**
 * Token Auth Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Token Auth
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProfilePicture;
use App\Models\DriverLocation;
use App\Models\DriverAddress;
use App\Models\CarType;
use App\Models\Currency;
use App\Models\Trips;
use App\Models\Language;
use App\Models\Country;
use App\Models\PaymentMethod;
use App\Models\Nid;
use App\Models\Request as RideRequest;
use Validator;
use Session;
use App;
use JWTAuth;
use Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CustomLog;
use Mail;

//Added by Nishat Start
use DB;
use SoapClient;
//Added by Nishat End

class TokenAuthController extends Controller
{
    /**
     * Constructor
     * 
     */
    public function __construct()
    {
        DB::enableQueryLog();
        $this->request_helper = resolve('App\Http\Helper\RequestHelper');
        $this->bonus_helper = resolve('App\Http\Helper\BonusHelper');
        $this->doc_helper = resolve('App\Http\Helper\DocumentVerificationHelper');
        $this->sms_helper = resolve('App\Http\Helper\SmsHelper');
    }



    // Numbers
    //public static $bn_numbers = ["১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০"];
    public static $bn_numbers = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];
    public static $en_numbers = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];


    /**
     * Get User Details
     * 
     * @param Collection User
     *
     * @return Response Array
     */
    protected function getUserDetails($user)
    {
        Log::info("getUserDetails Api Stp:1 :" . json_encode($user));

        $invoice_helper = resolve('App\Http\Helper\InvoiceHelper');
        $promo_details = $invoice_helper->getUserPromoDetails($user->id);

        $user_data = array(
            'user_id'           => $user->id,
            'first_name'        => $user->first_name,
            'last_name'         => $user->last_name,
            'mobile_number'     => $user->mobile_number,
            'country_code'      => $user->country_code,
            'email_id'          => $user->email ?? '',
            'user_status'       => $user->status,
            'user_thumb_image'  => @$user->profile_picture->src ?? url('images/user.jpeg'),
            'currency_symbol'   => $user->currency->symbol,
            'currency_code'     => $user->currency->code,
            'payout_id'         => $user->payout_id ?? '',
            'wallet_amount'     => getUserWalletAmount($user->id),
            'promo_details'     => $promo_details,
        );

        // Also sent for rider because mobile team also handle these parameters in rider

        $rider_details = array();
        if ($user->user_type == 'Rider' || true) {
            $user->load('rider_location');
            $rider_location = $user->rider_location;
            $rider_details = array(
                'home'          => optional($rider_location)->home ?? '',
                'work'          => optional($rider_location)->work ?? '',
                'home_latitude' => optional($rider_location)->home_latitude ?? '',
                'home_longitude' => optional($rider_location)->home_longitude ?? '',
                'work_latitude' => optional($rider_location)->work_latitude ?? '',
                'work_longitude' => optional($rider_location)->work_longitude ?? '',
            );
        }

        $driver_details = array();
        if ($user->user_type == 'Driver' || true) {
            $user->load(['driver_documents', 'driver_address']);
            $driver_documents = $user->driver_documents;
            $driver_address = $user->driver_address;
            $driver_details = array(
                'car_details'       => CarType::active()->get(),
                'license_front'     => optional($driver_documents)->license_front ?? '',
                'license_back'      => optional($driver_documents)->license_back ?? '',
                'insurance'         => optional($driver_documents)->insurance ?? '',
                'rc'                => optional($driver_documents)->rc ?? '',
                'permit'            => optional($driver_documents)->permit ?? '',
                'vehicle_id'        => optional($driver_documents)->vehicle_id ?? '',
                'vehicle_type'      => optional($driver_documents)->vehicle_type ?? '',
                'vehicle_number'    => optional($driver_documents)->vehicle_number ?? '',
                'address_line1'     => optional($driver_address)->address_line1 ?? '',
                'address_line2'     => optional($driver_address)->address_line2 ?? '',
                'state'             => optional($driver_address)->state ?? '',
                'postal_code'       => optional($driver_address)->postal_code ?? '',
                'company_name'      => $user->company_name,
                'company_id'        => $user->company_id ?? '',
            );
        }

        CustomLog::info("getUserDetails Api Stp:2 :");

        return array_merge($user_data, $rider_details, $driver_details);
    }

    /**
     * User Resister
     *@param  Get method request inputs
     *
     * @return Response Json 
     */
    public function register(Request $request)
    {
        Log::info("register Api Stp:1 :", $request->all());

        $language = $request->language ?? 'en';
        App::setLocale($language);

        try {
            $auth_method = "App\Services\Auth\AuthVia" . ucfirst($request->auth_type);
            $auth_service = resolve($auth_method);
        } catch (\Exception $e) {
            $auth_service = resolve("App\Services\Auth\AuthViaEmail");
        }

        $validate = $auth_service->validate($request);

        if ($validate) {
            return $validate;
        }
        //  $unique_id = strtotime("now") . rand(10000, 100000);
        //  $user = $auth_service->create($request, $unique_id);
        $unique_id = $request->mobile_number . "_" . strtolower($request->user_type);
        $user = $auth_service->create($request, $unique_id);

        if ($user == false) {
            return response()->json([
                'status_code'     => '0',
                'status_message' =>  trans('messages.already_have_account'),
            ]);
        } else {

            $request['country_id'] = $user->country_id;
            $credentials = $request->only('mobile_number', 'country_id', 'password', 'user_type');

            if ($request->auth_type == 'email') {
                $return_data = $auth_service->login($credentials);
            } else {
                $token = JWTAuth::fromUser($user);

                $return_data = array(
                    'status_code'       => '1',
                    'status_message'    => __('messages.user.register_successfully'),
                    'access_token'      => $token,
                );
            }

            if (is_object($return_data)) {
                return $return_data = array(
                    'status_code'       => '1',
                    'status_message'    => __('messages.user.register_successfully'),
                    'access_token'      => $token,
                );
            } else {
                if (!isset($return_data['status_code'])) {
                    return $return_data;
                }
            }


            $user_data = $this->getUserDetails($user);

            if ($user->user_type == 'driver') {
               // $this->bonus_helper->driver_signup_bonus($user);
              //  $this->bonus_helper->set_driver_online_bonus($user);

                if ($user->used_referral_code != '') {
                    $referred_by = User::where('referral_code', $user->used_referral_code)->first();
                    if ($referred_by != '') {
                        $this->bonus_helper->driver_referral_bonus($user);
                    }
                }
            } else if ($user->user_type == 'rider') {
                $this->bonus_helper->rider_cashback1($user);
                if ($user->used_referral_code != '') {
                    $referred_by = User::where('referral_code', $user->used_referral_code)->first();
                    if ($referred_by != '') {
                        $this->bonus_helper->rider_referral_bonus($user);
                    }
                }
            }

            if ($user->email != '') {
                $data = array(
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'id' => $user->id,
                    'gender' => $user->gender,
                    'email' => $user->email,
                    'country_code' => $user->country_code,
                    'mobile_number' => $user->mobile_number,
                );

                try {
                    Mail::send('emails.user_account_create', $data, function ($message) use ($user) {
                        $message->to($user->email, $user->first_name . ' ' . $user->last_name)->subject('Verify Your Email');
                        $message->from('noreply@alesharide.com', 'ALESHA RIDE');
                    });
                } catch (\Exception $e) {
                    // Never reached
                    // echo "xx";
                }
            }

            CustomLog::info("register Api Stp:2 :");

            return response()->json(array_merge($return_data, $user_data));
        }
    }

    /**
     * User Socail media Resister & Login 
     * @param Get method request inputs
     *
     * @return Response Json 
     */
    public function apple_callback(Request $request)
    {
        Log::info("apple_callback Api Stp:1 :", $request->all());

        $client_id = api_credentials('service_id', 'Apple');

        $client_secret = getAppleClientSecret();

        $params = array(
            'grant_type'     => 'authorization_code',
            'code'              => $request->code,
            'redirect_uri'  => url('api/apple_callback'),
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
        );

        $curl_result = curlPost("https://appleid.apple.com/auth/token", $params);

        if (!isset($curl_result['id_token'])) {
            $return_data = array(
                'status_code'       => '0',
                'status_message'    => $curl_result['error'],
            );

            return response()->json($return_data);
        }

        $claims = explode('.', $curl_result['id_token'])[1];
        $user_data = json_decode(base64_decode($claims));

        $user = User::where('apple_id', $user_data->sub)->first();

        if ($user == '') {
            $return_data = array(
                'status_code'       => '1',
                'status_message'    => 'New User',
                'email_id'          => optional($user_data)->email ?? '',
                'apple_id'          => $user_data->sub,
            );

            return response()->json($return_data);
        }

        $token = JWTAuth::fromUser($user);

        $user_details = $this->getUserDetails($user);

        $return_data = array(
            'status_code'       => '2',
            'status_message'    => 'Login Successfully',
            'apple_email'       => optional($user_data)->email ?? '',
            'apple_id'          => $user_data->sub,
            'access_token'      => $token,
        );

        CustomLog::info("apple_callback Api Stp:2 :");

        return response()->json(array_merge($return_data, $user_details));
    }

    /**
     * User Socail media Resister & Login 
     * @param Get method request inputs
     *
     * @return Response Json 
     */
    public function socialsignup(Request $request)
    {

        Log::info("socialsignup Api Stp:1 :", $request->all());

        $rules = array(
            'auth_type'   => 'required|in:facebook,google,apple',
            'auth_id'     => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        if ($request->auth_type == 'facebook') {
            $auth_column = 'fb_id';
        } else if ($request->auth_type == 'google') {
            $auth_column = 'google_id';
        } else {
            $auth_column = 'apple_id';
        }

        $user_count = User::where($auth_column, $request->auth_id)->count();

        // Social Login Flow
        if ($user_count == 0) {
            return response()->json([
                'status_code'   => '2',
                'status_message' => 'New User',
            ]);
        }

        $rules =  array(
            'device_type'  => 'required',
            'device_id'    => 'required'
        );

        $messages = array('required' => ':attribute is required.');
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $user = User::where($auth_column, $request->auth_id)->first();
        $user->device_id    = $request->device_id;
        $user->device_type  = $request->device_type;
        $user->language     = $request->language;
        $user->currency_code = get_currency_from_ip();
        $user->firebase_token = null;
        $user->save();

        $token = JWTAuth::fromUser($user);

        $return_data = array(
            'status_code'       => '1',
            'status_message'    => 'Login Success',
            'access_token'      => $token,
        );

        $user_data = $this->getUserDetails($user);

        CustomLog::info("socialsignup Api Stp:2 :");

        return response()->json(array_merge($return_data, $user_data));
    }

    /**
     * User Login
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function login(Request $request)
    {
        Log::info("login Api Stp:1 :", $request->all());

        $user_id = $request->mobile_number;
        $auth_column = 'mobile_number';

        $rules = array(
            'mobile_number'   => 'required|regex:/^[0-9]+$/|min:10',
            'user_type'       => 'required|in:Rider,Driver,rider,driver',
            // 'password'        => 'required|min:8|regex:/[^A-Za-z0-9]+/',
            'password' =>  [
                'required',
                'string',
                'min:8',             // must be at least 10 characters in length
                //  'regex:/[a-z]/',      // must contain at least one lowercase letter
                //  'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',      // must contain at least one digit
            ],
            'country_code'    => 'required',
            'device_type'     => 'required',
            'device_id'       => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $language = $request->language ?? 'en';
        App::setLocale($language);

        $country_id = Cache()->remember('country_id', Config('cache.one_year'), function () use ($request) {
            return Country::whereShortName($request->country_code)->value('id');
        });
        //$country_id = Country::whereShortName($request->country_code)->value('id');

        $attempt = Auth::attempt([$auth_column => $user_id, 'password' => $request->password, 'user_type' => $request->user_type, 'country_id' => $country_id]);

        if (!$attempt) {
            return response()->json([
                'status_code'    => '0',
                'status_message' => __('messages.credentials'),
            ]);
        }

        $credentials = $request->only($auth_column, 'password', 'user_type');
        $credentials['country_id'] = $country_id;

        try {
            $token = JWTAuth::attempt($credentials);
            if (!$token) {
                return response()->json([
                    'status_code'    => '0',
                    'status_message' => __('messages.credentials'),
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'status_code'    => '0',
                'status_message' => 'could_not_create_token',
            ]);
        }

        $user = User::with('company')->where($auth_column, $user_id)->whereUserType($request->user_type)->whereCountryId($country_id)->first();

        if ($user->status == 'Inactive') {
            return response()->json([
                'status_code'     => '0',
                'status_message' => __('messages.inactive_admin'),
            ]);
        }

        if (isset($user->company) && $user->company->status == 'Inactive') {
            return response()->json([
                'status_code'     => '0',
                'status_message' => __('messages.inactive_company'),
            ]);
        }

        $currency_code = get_currency_from_ip();
        User::whereId($user->id)->update([
            'device_id'     => $request->device_id,
            'device_type'   => $request->device_type,
            'currency_code' => $currency_code,
            'language'      => $request->language,
            'firebase_token' => null
        ]);

        $user = User::where('id', $user->id)->first();
        auth()->setUser($user);

        if (strtolower($request->user_type) != 'rider') {
            $first_car = CarType::active()->first();

            $data['user_id'] = $user->id;
            $data['status'] = 'Offline';

            if (isset(optional($user->driver_documents)->vehicle_id) && optional($user->driver_documents)->vehicle_id) {
                $car_id = optional($user->driver_documents)->vehicle_id;
                $car_id = explode(',', $car_id);
                $car_id = $car_id[0];
            } else {
                $car_id = $first_car->id;
            }

            $data['car_id'] = $car_id;

            DriverLocation::updateOrCreate(['user_id' => $user->id], $data);
            RideRequest::where('driver_id', $user->id)->where('status', 'Pending')->update(['status' => 'Cancelled']);
        }

        $language = $user->language ?? 'en';
        App::setLocale($language);

        $return_data = array(
            'status_code'       => '1',
            'status_message'    => __('messages.login_success'),
            'access_token'      => $token,
        );

        $user = $this->getUserDetails($user);

        CustomLog::info("login Api Stp:2 :");

        return response()->json(array_merge($return_data, $user));
    }

    public function language(Request $request)
    {
        Log::info("language Api Stp:1 :", $request->all());

        // $user_details = JWTAuth::parseToken()->authenticate();

        // $user = User::find($user_details->id);

        // if ($user == '') {
        //     return response()->json([
        //         'status_code'    => '0',
        //         'status_message' => __('messages.invalid_credentials'),
        //     ]);
        // }
        // $user->language = $request->language;
        // $user->save();

        // $language = $user->language ?? 'en';

        User::where('id', auth()->user()->id)->update(['language' => $request->language]);
        $language = auth()->user()->language ?? 'en';
        App::setLocale($language);

        CustomLog::info("language Api Stp:2 :");

        return response()->json([
            'status_code'       => '1',
            'status_message'    => trans('messages.update_success'),
        ]);
    }

    /**
     * User Email Validation
     *
     * @return Response in Json
     */
    public function emailvalidation(Request $request)
    {
        Log::info("emailvalidation Api Stp:1 :", $request->all());

        $rules = array('email' => 'required|max:255|email_id|unique:users');

        // Email signup validation custom messages
        $messages = array('required' => ':attribute is required.');

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status_code'   => '0',
                'status_message' => 'Email Already exist',
            ]);
        }

        CustomLog::info("emailvalidation Api Stp:2 :");

        return response()->json([
            'status_code'   => '1',
            'status_message' => 'Email validation Success',
        ]);
    }

    /**
     * Forgot Password
     * 
     * @return Response in Json
     */
    public function forgotpassword(Request $request)
    {
        Log::info("forgotpassword Api Stp:1 :", $request->all());

        $rules = array(
            'mobile_number'   => 'required|regex:/^[0-9]+$/|min:10',
            'user_type'       => 'required|in:Rider,Driver,rider,driver',
            // 'password'        => 'required|min:8|regex:/[^A-Za-z0-9]+/',
            'password' =>  [
                'required',
                'string',
                'min:8',             // must be at least 10 characters in length
                // 'regex:/[a-z]/',      // must contain at least one lowercase letter
                // 'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',      // must contain at least one digit
            ],
            'country_code'    => 'required',
            'device_type'     => 'required',
            'device_id'       => 'required'
        );
        $attributes = array(
            'mobile_number'   => 'Mobile Number',
        );

        $validator = Validator::make($request->all(), $rules, $attributes);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $country_id = Cache()->remember('country_id', Config('cache.one_year'), function () use ($request) {
            return Country::whereShortName($request->country_code)->value('id');
        });

        //$country_id = Country::whereShortName($request->country_code)->value('id');

        $user_check = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->whereCountryId($country_id)->first();

        if ($user_check == '') {
            return response()->json([
                'status_code'    => '0',
                'status_message' => __('messages.invalid_credentials'),
            ]);
        }

        $user = User::whereId($user_check->id)->first();
        $user->password = $request->password;
        $user->device_id = $request->device_id;
        $user->device_type = $request->device_type;
        $user->currency_code = $request->currency_code;
        $user->firebase_token = null;
        $user->save();

        $user = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->first();

        // $token = JWTAuth::fromUser($user);

        auth()->setUser($user);

        if (strtolower($request->user_type) != 'rider') {
            $first_car = CarType::active()->first();
            $data = [
                'user_id'  => $user->id,
                'status'   => 'Offline',
                'car_id'   => optional($user->driver_documents)->vehicle_id ?? $first_car->id,
            ];
            //DriverLocation::updateOrCreate(['user_id' => $user->id], $data);
            RideRequest::where('driver_id', $user->id)->where('status', 'Pending')->update(['status' => 'Cancelled']);
        }

        $return_data = array(
            'status_code'       => '1',
            'user_status'       => $user->status,
            'status_message'    => __('messages.password_reset_success'),
            // 'access_token'      => $token, //changed for new system( after reset user need to login so token is not required)
        );

        //$user_data = $this->getUserDetails($user);

        CustomLog::info("forgotpassword Api Stp:2 :");

        // return response()->json(array_merge($return_data, $user_data));
        return response()->json(array_merge($return_data));
    }

    /**
     * Mobile number verification
     * 
     * @return Response in Json
     */
    public function numbervalidation(Request $request)
    {
        Log::info("numbervalidation Api Stp:1 :", $request->all());

        if (isset($request->language)) {
            $language = $request->language;
        } else {
            $language = 'en';
        }
        App::setLocale($language);

        $rules = array(
            'mobile_number'   => 'required|regex:/^[0-9]+$/|min:6',
            'user_type'       => 'required|in:Rider,Driver,rider,driver',
            'country_code'    => 'required',
        );

        if ($request->forgotpassword == 1) {
            $rules['mobile_number'] = 'required|regex:/^[0-9]+$/|min:6|exists:users,mobile_number';
        }

        $messages = array(
            'mobile_number.required' => trans('messages.mobile_num_required'),
            'mobile_number.exists'   => trans('messages.enter_registered_number'),
        );

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $mobile_number = $request->mobile_number;

        $country_id = Country::whereShortName($request->country_code)->value('id');

        $user = User::where('mobile_number', $mobile_number)->where('user_type', $request->user_type)->whereCountryId($country_id)->get();
        if ($user->count() && $request->forgotpassword != 1) {
            return response()->json([
                'status_message'  => trans('messages.mobile_number_exist'),
                'status_code'     => '0',
            ]);
        }

        if ($user->count() <= 0 && $request->forgotpassword == 1) {
            return response()->json([
                'status_message'  => trans('messages.number_does_not_exists'),
                'status_code'     => '0',
            ]);
        }

        $otp = rand(1000, 9999);
        $otp_message = 'আপনার আলেশা রাইড এর OTP ' . strval($otp);

        $to = '0' . $request->mobile_number;
        //$otp_messag =  str_replace(self::$en_numbers, self::$bn_numbers, $otp_message) . strval($otp);
        // $Onnorokom_api_key_get = DB::table('api_credentials')
        //     ->where('name', 'token')
        //     ->where('site', 'Onnorokom')
        //     ->first()->value;

        try {


            // $soapClient = new SoapClient("https://api2.onnorokomSMS.com/sendSMS.asmx?wsdl");
            // $paramArray = array(
            //     'apiKey' =>  $Onnorokom_api_key_get,
            //     'messageText' => str_replace(self::$en_numbers, self::$bn_numbers, $otp_message) . strval($otp),
            //     'numberList' => $to,
            //     'smsType' => "TEXT",
            //     'maskName' => '',
            //     'campaignName' => '',
            // );
            // $value = $soapClient->__call("NumberSms", array($paramArray));

            // CustomLog::info("numbervalidation Api Stp:2 :");

            // if (explode('||', $value->NumberSmsResult)[0] == 1900) {
            //     return response()->json([
            //         'status_code'    => '1',
            //         'status_message' => 'Success',
            //         'otp'           => strval($otp),
            //     ]);
            // } else {
            //     return response()->json([
            //         'status_code'    => '2',
            //         'status_message' => 'Otp Send Failed',
            //         'otp'           => '',
            //     ]);
            // }

            $sms_result = $this->sms_helper->send($to, $otp_message,$api_or_web="api");
            //  print_r($sms_result);
            // exit;
            //$sms_result =  json_decode($sms_result, true);

            if ($sms_result['0'] == 0) {
                return response()->json([
                    'status_code'    => '1',
                    'status_message' => 'Success',
                    'otp'           => strval($otp),
                ]);
            } else {
                return response()->json([
                    'status_code'    => '2',
                    'status_message' => 'Failed to send Otp',
                    'otp'           => '',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status_code'    => '2',
                'status_message' => "Failed to send Otp",
                'otp'            => '',
            ]);

            //echo $e->getMessage();
        }









        //******** Commentded By Nishat********//

        // $text = __('messages.api.your_otp_is').$otp;
        // $phone_code = Country::whereShortName($request->country_code)->value('phone_code');
        // $to = '+'.$phone_code.$request->mobile_number;
        // $sms_gateway = resolve("App\Contracts\SMSInterface");
        // $sms_responce = $sms_gateway->send($to,$text);

        //  if(!isLiveEnv()) {
        //              return response()->json([
        //                  'status_code'    => '1',
        //                  'status_message' => 'Success',
        //                  'otp'           => strval($otp),
        //              ]);
        //          }

        // if($sms_responce['status_code'] == 0) {
        //     return response()->json([
        //         'status_message' => $sms_responce['message'],
        //         'status_code' => '0',
        //         'otp' => '',
        //     ]);
        // }

        // return response()->json([
        //     'status_code'    => '1',
        //     'status_message' => 'Success',
        //     'otp'           => strval($otp),
        // ]);

        //******** Comment End By Nishat********//









    }

    /**
     * Updat Device ID and Device Type
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function updateDevice(Request $request)
    {
        Log::info("updateDevice Api Stp:1 :", $request->all());

        $user_details = JWTAuth::parseToken()->authenticate();

        $rules = array(
            'user_type'    => 'required|in:Rider,Driver,rider,driver',
            'device_type'  => 'required',
            'device_id'    => 'required'
        );
        $attributes = array(
            'mobile_number'   => 'Mobile Number',
        );
        $validator = Validator::make($request->all(), $rules, $attributes);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $user = User::where('id', $user_details->id)->first();

        if ($user == '') {
            return response()->json([
                'status_code'       => '0',
                'status_message'    => trans('messages.api.invalid_credentials'),
            ]);
        }

        User::whereId($user_details->id)->update(['device_id' => $request->device_id, 'device_type' => $request->device_type]);

        CustomLog::info("updateDevice Api Stp:2 :");

        return response()->json([
            'status_code'     => '1',
            'status_message'  => __('messages.api.updated_successfully'),
        ]);
    }

    public function logout(Request $request)
    {
        Log::info("logout Api Stp:1 :", $request->all());

        $user_details = JWTAuth::parseToken()->authenticate();

        $user = User::where('id', $user_details->id)->first();

        if ($user == '') {
            return response()->json([
                'status_code'       => '0',
                'status_message'    => __('messages.api.invalid_credentials'),
            ]);
        }

        if ($user->user_type == 'Driver') {

            $trips_count = Trips::where('driver_id', $user_details->id)->whereNotIn('status', ['Completed', 'Cancelled'])->count();

            $driver_location = DriverLocation::where('user_id', $user_details->id)->first();

            if (optional($driver_location)->status == 'Trip' || $trips_count > 0) {
                return response()->json([
                    'status_code'    => '0',
                    'status_message' => __('messages.complete_your_trips'),
                ]);
            }

            DriverLocation::where('user_id', $user_details->id)->update(['status' => 'Offline']);
            JWTAuth::invalidate($request->token);
            Session::flush();

            $user->device_type = Null;
            $user->device_id = '';
            $user->save();

            return response()->json([
                'status_code'     => '1',
                'status_message'  => "Logout Successfully",
            ]);
        }

        // $trips_count = Trips::where('user_id', $user_details->id)->whereNotIn('status', ['Completed', 'Cancelled'])->count();
        // if ($trips_count) {
        //     return response()->json([
        //         'status_code'    => '0',
        //         'status_message' => __('messages.complete_your_trips'),
        //     ]);
        // }
        //Deactive the Access Token

        $trips_count = Trips::where('user_id', $user_details->id)->whereNotIn('status', ['Completed', 'Cancelled'])->count();

        if ($trips_count > 0) {
            $trips_count_cash_wallet_check = Trips::where('user_id', $user_details->id)->whereNotIn('payment_mode', ['Cash', 'Cash & Wallet'])->count();
            if ($trips_count_cash_wallet_check > 0) {
                return response()->json([
                    'status_code'    => '0',
                    'status_message' => __('messages.complete_your_trips'),
                ]);
            }
        }

        JWTAuth::invalidate($request->token);

        Session::flush();

        $user->device_type = Null;
        $user->device_id = '';
        $user->save();

        CustomLog::info("logout Api Stp:2 :");

        return response()->json([
            'status_code'     => '1',
            'status_message'  => "Logout Successfully",
        ]);
    }

    public function currency_conversion(Request $request)
    {
        Log::info("currency_conversion Api Stp:1 :", $request->all());

        $user_details   = JWTAuth::parseToken()->authenticate();

        $payment_methods = collect(PAYMENT_METHODS);
        $payment_methods = $payment_methods->reject(function ($value) {
            $is_enabled = payment_gateway('is_enabled', ucfirst($value['key']));
            return ($is_enabled != '1');
        });
        $payment_types = $payment_methods->pluck('key')->implode(',');

        $request['payment_type'] = $request->payment_type;

        $rules  = [
            'amount' => 'required|numeric|min:0',
            'payment_type'  => 'required|in:' . $payment_types,
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $currency_code  = $user_details->currency->code;
        $payment_currency = site_settings('payment_currency');

        $price = floatval($request->amount);

        $converted_amount = currencyConvert($currency_code, $payment_currency, $price);

        $gateway = ($request->payment_type == "braintree") ? resolve('braintree') : resolve('braintree_paypal');

        $customer_id = $user_details->id . $user_details->mobile_number;
        try {
            $customer = $gateway->customer()->find($customer_id);
        } catch (\Exception $e) {
            try {
                $newCustomer = $gateway->customer()->create([
                    'id'        => $customer_id,
                    'firstName' => $user_details->first_name,
                    'lastName'  => $user_details->last_name,
                    'email'     => $user_details->email,
                    'phone'     => $user_details->phone_number,
                ]);

                if (!$newCustomer->success) {
                    return response()->json([
                        'status_code' => '0',
                        'status_message' => $newCustomer->message,
                    ]);
                }
                $customer = $newCustomer->customer;
            } catch (\Exception $e) {
                if ($e instanceof \Braintree\Exception\Authentication) {
                    return response()->json([
                        'status_code' => '0',
                        'status_message' => __('messages.api.authentication_failed'),
                    ]);
                }
                return response()->json([
                    'status_code' => '0',
                    'status_message' => $e->getMessage(),
                ]);
            }
        }

        $bt_clientToken = $gateway->clientToken()->generate([
            "customerId" => $customer->id
        ]);

        CustomLog::info("currency_conversion Api Stp:2 :");

        return response()->json([
            'status_code'    => '1',
            'status_message' => 'Amount converted successfully',
            'currency_code'  => $payment_currency,
            'amount'         => $converted_amount,
            'braintree_clientToken' => $bt_clientToken,
        ]);
    }

    public function getSessionOrDefaultCode()
    {
        Log::info("getSessionOrDefaultCode Api Stp:1 :");

        $currency_code = Currency::defaultCurrency()->first()->code;

        CustomLog::info("getSessionOrDefaultCode Api Stp:2 :");
    }

    public function currency_list()
    {
        Log::info("currency_list Api Stp:1 :");

        $currency_list = Currency::active()->orderBy('code')->get();
        $curreny_list_keys = ['code', 'symbol'];

        $currency_list = $currency_list->map(function ($item, $key) use ($curreny_list_keys) {
            return array_combine($curreny_list_keys, [$item->code, $item->symbol]);
        })->all();

        CustomLog::info("currency_list Api Stp:2 :");

        if (!empty($currency_list)) {
            return response()->json([
                'status_message' => 'Currency Details Listed Successfully',
                'status_code'     => '1',
                'currency_list'   => $currency_list
            ]);
        }
        return response()->json([
            'status_code'     => '0',
            'status_message' => 'Currency Details Not Found',
        ]);
    }

    public function language_list()
    {
        Log::info("language_list Api Stp:1 :");

        $languages = Cache()->remember('language', Config('cache.one_year'), function () {
            $languages = Language::active()->get();
            return $languages = $languages->map(function ($item, $key) {
                return $item->value;
            })->all();
        });

        // $languages = Language::active()->get();

        // $languages = $languages->map(function ($item, $key) {
        //     return $item->value;
        // })->all();

        CustomLog::info("language_list Api Stp:2 :");

        if (!empty($languages)) {
            return response()->json([
                'status_code'   => '1',
                'status_message' => 'Successfully',
                'language_list' => $languages,
            ]);
        }
        return response()->json([
            'status_code'     => '0',
            'status_message' => 'language Details Not Found',
        ]);
    }

    public function base64_to_jpeg($base64_string, $output_file)
    {
        // open the output file for writing
        $ifp = fopen($output_file, 'wb');

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $base64_string);

        // we could add validation here with ensuring count( $data ) > 1
        fwrite($ifp, base64_decode($data[0]));

        // clean up the file resource
        fclose($ifp);

        return $output_file;
    }

    public function verify_nid(Request $request)
    {
        $auth_type = $request->auth_type;
        $country_id = $request->country_id;
        $mobile_number = $request->mobile_number;
        $nid = $request->nid;
        //$nid = '6443559809'; 

        //$photo = Config('test.voter.photo'); 

        $destinationPath = public_path() . "/images/nid_photo/";
        if (!is_dir($destinationPath)) mkdir($destinationPath, 0777, true);

        if (!is_object(Nid::where('nid', $nid)->first())) {
            $nid_response = $this->doc_helper->nid($nid);
            //$nid_response = Config('test'); //temp
            print_r($nid_response);
            // if (array_key_exists('voter', $nid_response)) {                
            //     $voter = $nid_response['voter'];

            //     $nameEn = $voter['nameEn'];
            //     $fatherEn = $voter['fatherEn'];
            //     $motherEn = $voter['motherEn'];
            //     $spouseEn = $voter['spouseEn'];
            //     $presentAddressEn = $voter['presentAddressEn'];
            //     $permanentAddressEn = $voter['permanentAddressEn'];

            //     $name = $voter['name'];
            //     $father = $voter['father'];
            //     $mother = $voter['mother'];
            //     $spouse = $voter['spouse'];
            //     $presentAddress = $voter['presentAddress'];
            //     $permanentAddress = $voter['permanentAddress'];

            //     $gender = $voter['gender'];
            //     $dob = $voter['dob'];
            //     $photo = $voter['photo'];

            //     if($gender == 'male') $gender ='1';
            //     else if($gender == 'female') $gender ='1';
            //     else $gender ='3';

            //     list($m,$d,$y) = explode("/", $dob);
            //     $dob = $y.'-'.$m.'-'.$d;

            //     $table = new Nid;
            //     $table->nid = $nid;
            //     $table->name_en = $nameEn;
            //     $table->father_en = $fatherEn;
            //     $table->mother_en = $motherEn;
            //     $table->spouse_en = $spouseEn;
            //     $table->present_address_en = $presentAddressEn;
            //     $table->permanent_address_en = $permanentAddressEn;

            //     $table->name = $name;
            //     $table->father = $father;
            //     $table->mother = $mother;
            //     $table->spouse = $spouse;
            //     $table->present_address = $presentAddress;
            //     $table->permanent_address = $permanentAddress;

            //     $table->gender = $gender;
            //     $table->dob = $dob;
            //     $table->photo = $photo;
            //     $table->save();
            //     //$link = self::base64_to_jpeg($photo, 'images/nid_photo/'. $nid.'.png');
            //     //echo "<a href='".$link."' target='_blank'>".$link."</a>";
            // }   
        }
    }
}
