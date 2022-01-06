<?php

/**
 * Referral Settings Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Referral Settings
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Start\Helpers;
use App\Models\ReferralSetting;
use App\Models\Currency;
use Validator;
use DB;

class ReferralSettingsController extends Controller
{
    public function __construct()
    {
        $this->base_url = $this->view_data['base_url'] = 'admin/referral_settings';
        $this->view_data['main_title']          = 'Referral & Bonus Settings';
        $this->view_data['update_url']          = url('admin/update_referral_settings');
        $this->view_data['yes_no']              = array('1' => 'Yes', '0' => 'No');
        $this->view_data['withdrawal_method']   = array('Balance' => 'Balance');//,'Cash' => 'Cash','Owe' => 'Owe');
        $this->view_data['adjustable']          = array('Next Trip' => 'Next Trip');
        //$this->view_data['beneficiary']         = array('Referrar' => 'Referrar','Referral' => 'Referral', 'Both' => 'Both'); 
        $this->view_data['who_get_bonus_array']         = array('Referrar' => 'Referrar'); 
        $this->view_data['day_name']            = array('Saturday' => 'Saturday','Sunday' => 'Sunday', 'Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday');
        $this->view_data['rate_fixed']          = array('rate' => 'Percentage', 'fixed' => 'Fixed Amount');
        $this->view_data['offer_for']           = array('our_employee' => 'Our Employee', 'all' => 'All Riders');
        $this->view_data['months']              = array('1' => '1 month', '2' => '2 months', '3' => '3 months');
        
        
        $this->helper = new Helpers;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->view_data['driver_referral']     = ReferralSetting::DriverReferral()->get()->pluck('value','name')->toArray();    

        $this->view_data['driver_signup']       = ReferralSetting::DriverSignupBonus()->get()->pluck('value','name')->toArray();
        $this->view_data['driver_online_bonus'] = ReferralSetting::DriverOnlineBonus()->get()->pluck('value','name')->toArray();
        $this->view_data['driver_trip_bonus']   = ReferralSetting::DriverTripBonus()->get()->pluck('value','name')->toArray();
        $this->view_data['rider_referral']      = ReferralSetting::RiderReferral()->get()->pluck('value','name')->toArray();
        $this->view_data['rider_cashback1']     = ReferralSetting::RiderCashback1()->get()->pluck('value','name')->toArray();
        $this->view_data['rider_cashback2']     = ReferralSetting::RiderCashback2()->get()->pluck('value','name')->toArray();
        $this->view_data['rider_discount_offer1']     = ReferralSetting::RiderDiscountOffer1()->get()->pluck('value','name')->toArray();

        $this->view_data['driver_joining_bonus'] = ReferralSetting::DriverJoiningBonus()->get()->pluck('value','name')->toArray();
        $this->view_data['driver_referral_bonus'] = ReferralSetting::DriverReferralBonus()->get()->pluck('value','name')->toArray();

        $this->view_data['currency']       = Currency::codeSelect();
        return view('admin.referral_settings', $this->view_data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user_type = $request->user_type;

    
        $apply_referral     = $user_type.'_apply_referral';
        $start_time         = $user_type.'_start_time';
        $end_time           = $user_type.'_end_time';
        $number_of_trips    = $user_type.'_number_of_trips';
        $currency_code      = $user_type.'_currency_code';
        $amount             = $user_type.'_amount';
        $who_get_bonus      = $user_type.'_who_get_bonus';
        $terms_condition    = $user_type.'_terms_condition';
        $withdrawal_method  = $user_type.'_withdrawal_method';
        $min_hour           = $user_type.'_min_hour';
        $min_trip           = $user_type.'_min_trip';
        $number_of_days     = $user_type.'_number_of_days';
        $payment_after_days = $user_type.'_payment_after_days';
        $day_name           = $user_type.'_day_name';
        $rate_fixed         = $user_type.'_rate_fixed';
        $offer_for          = $user_type.'_offer_for';
        $max_trip           = $user_type.'_max_trip';
        $trip_distance      = $user_type.'_trip_distance';
        $allow_same_user    = $user_type.'_allow_same_user';
        $bonus_start_after_month = $user_type.'_bonus_start_after_month';
        $bonus_start_after_month= $user_type.'_bonus_start_after_month';
        $peak_hour              = $user_type.'_peak_hour';
        $trip_complete_percent  = $user_type.'_trip_complete_percent';

    

        /*if($user_type == 'DriverSignupBonus'){
            $rules = array(
                $apply_referral     => 'required_with:1,0',
                $start_time         => 'required',
                $end_time           => 'required',
                $number_of_trips    => 'required|numeric|min:1',
                $currency_code      => 'required',
                $amount             => 'required|numeric|min:1',
                $terms_condition    => 'required',
                $withdrawal_method  => 'required_with:Balance,Cash,Owe',
                $number_of_days     => 'required|numeric|min:1',
                $trip_distance      => 'required|numeric|min:1',
                $allow_same_user    => 'required_with:1,0',
                $payment_after_days => 'required|numeric|min:1', //additional
            );

            // Fields Validation Custom Names
            $attributes = array(
                $apply_referral     => 'Apply Bonus',
                $number_of_trips    => 'Number of Trips',
                $currency_code      => 'Currency Code',
                $amount             => 'Referral/ Bonus Amount',
                $terms_condition    => 'Terms and condition',
                $withdrawal_method  => 'Withdrawal method',
                $number_of_days     => 'Within Days',
                $trip_distance      => 'Trip Distance',
                $allow_same_user     => 'Allow Same User',
                $payment_after_days  => 'Payment after days',
            );

            $validator = Validator::make($request->all(), $rules, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            ReferralSetting::where(['name' => 'apply_referral', 'user_type' => $user_type])->update(['value' => $request->$apply_referral]);
            ReferralSetting::where(['name' => 'start_time', 'user_type' => $user_type])->update(['value' => $request->$start_time]);
            ReferralSetting::where(['name' => 'end_time', 'user_type' => $user_type])->update(['value' => $request->$end_time]);
            ReferralSetting::where(['name' => 'number_of_trips', 'user_type' => $user_type])->update(['value' => $request->$number_of_trips]);
            ReferralSetting::where(['name' => 'currency_code', 'user_type' => $user_type])->update(['value' => $request->$currency_code]);
            ReferralSetting::where(['name' => 'amount', 'user_type' => $user_type])->update(['value' => $request->$amount]);
            ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
            ReferralSetting::where(['name' => 'withdrawal_method', 'user_type' => $user_type])->update(['value' => $request->$withdrawal_method]);

            ReferralSetting::where(['name' => 'number_of_days', 'user_type' => $user_type])->update(['value' => $request->$number_of_days]);
            ReferralSetting::where(['name' => 'trip_distance', 'user_type' => $user_type])->update(['value' => $request->$trip_distance]);
            ReferralSetting::where(['name' => 'allow_same_user', 'user_type' => $user_type])->update(['value' => $request->$allow_same_user]);

        }*/

       // dd($request);
        if($user_type == 'DriverJoiningBonus'){
            $rules = array(
                $apply_referral     => 'required_with:1,0',
                $start_time         => 'required',
                $end_time           => 'required',
                $number_of_trips    => 'required|numeric|min:1',
                $number_of_days    => 'required|numeric|min:1',
                $payment_after_days => 'required|numeric|min:1',
                $allow_same_user    => 'required_with:1,0',
                $trip_distance      => 'required|numeric|min:1',                
                $currency_code      => 'required',
                $amount             => 'required|numeric|min:1',
                $withdrawal_method  => 'required_with:Balance,Cash,Owe',
                $terms_condition    => 'required',
            );

            // Fields Validation Custom Names
            $attributes = array(
                $apply_referral     => 'Apply Bonus',
                $start_time         => 'Start Time',
                $end_time           => 'End Time',
                $number_of_trips    => 'Number of Trips',
                $number_of_days     => 'Within Days',
                $payment_after_days  => 'Payment after days',
                $allow_same_user     => 'Allow Same User',
                $trip_distance     => 'Trip Distance',
                $currency_code      => 'Currency Code',
                $amount             => 'Referral/ Bonus Amount',
                $withdrawal_method  => 'Withdrawal method',
                $terms_condition    => 'Terms and condition',
            );
           
            $validator = Validator::make($request->all(), $rules, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
                //echo $error = $validator->errors()->first();
            }
            
            ReferralSetting::where(['name' => 'apply_referral', 'user_type' => $user_type])->update(['value' => $request->$apply_referral]);
            ReferralSetting::where(['name' => 'start_time', 'user_type' => $user_type])->update(['value' => $request->$start_time]);
            ReferralSetting::where(['name' => 'end_time', 'user_type' => $user_type])->update(['value' => $request->$end_time]);
            ReferralSetting::where(['name' => 'number_of_trips', 'user_type' => $user_type])->update(['value' => $request->$number_of_trips]);
            ReferralSetting::where(['name' => 'number_of_days', 'user_type' => $user_type])->update(['value' => $request->$number_of_days]);
            ReferralSetting::where(['name' => 'payment_after_days', 'user_type' => $user_type])->update(['value' => $request->$payment_after_days]);
            ReferralSetting::where(['name' => 'allow_same_user', 'user_type' => $user_type])->update(['value' => $request->$allow_same_user]);
            ReferralSetting::where(['name' => 'trip_distance', 'user_type' => $user_type])->update(['value' => $request->$trip_distance]);
            ReferralSetting::where(['name' => 'currency_code', 'user_type' => $user_type])->update(['value' => $request->$currency_code]);
            ReferralSetting::where(['name' => 'amount', 'user_type' => $user_type])->update(['value' => $request->$amount]);
            ReferralSetting::where(['name' => 'withdrawal_method', 'user_type' => $user_type])->update(['value' => $request->$withdrawal_method]);
            ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
         }

        else if($user_type == 'DriverReferralBonus'){            
            $rules = array(
                $apply_referral     => 'required_with:1,0',
                $start_time         => 'required',
                $end_time           => 'required',
                $number_of_trips    => 'required|numeric|min:1',
                $number_of_days    => 'required|numeric|min:1',
                $payment_after_days => 'required|numeric|min:1',
                $currency_code      => 'required',
                $amount             => 'required|numeric|min:1',
                $offer_for          => 'required:our_employee,all',
                $withdrawal_method  => 'required_with:Balance,Cash,Owe',
                $terms_condition    => 'required',
            );

            // Fields Validation Custom Names
            $attributes = array(
                $apply_referral     => 'Apply Offer',
                $start_time         => 'Start Time',
                $end_time           => 'End Time',
                $number_of_trips    => 'Number of Trips',
                $number_of_days     => 'Within Days',
                $payment_after_days  => 'Payment after days',
                $currency_code      => 'Currency Code',
                $amount             => 'Offer Amount',
                $offer_for          => 'Offer for',
                $withdrawal_method  => 'Withdrawal method',
                $terms_condition    => 'Terms and condition',
                
            );
           
            $validator = Validator::make($request->all(), $rules, $attributes);

            if ($validator->fails()) {
                //return back()->withErrors($validator)->withInput();
            }

            ReferralSetting::where(['name' => 'apply_referral', 'user_type' => $user_type])->update(['value' => $request->$apply_referral]);
            ReferralSetting::where(['name' => 'start_time', 'user_type' => $user_type])->update(['value' => $request->$start_time]);
            ReferralSetting::where(['name' => 'end_time', 'user_type' => $user_type])->update(['value' => $request->$end_time]);
            
            ReferralSetting::where(['name' => 'number_of_trips', 'user_type' => $user_type])->update(['value' => $request->$number_of_trips]);
            ReferralSetting::where(['name' => 'number_of_days', 'user_type' => $user_type])->update(['value' => $request->$number_of_days]);
            ReferralSetting::where(['name' => 'payment_after_days', 'user_type' => $user_type])->update(['value' => $request->$payment_after_days]);
            
            //ReferralSetting::where(['name' => 'rate_fixed', 'user_type' => $user_type])->update(['value' => $request->$rate_fixed]);
            ReferralSetting::where(['name' => 'currency_code', 'user_type' => $user_type])->update(['value' => $request->$currency_code]);
            ReferralSetting::where(['name' => 'amount', 'user_type' => $user_type])->update(['value' => $request->$amount]);
            ReferralSetting::where(['name' => 'offer_for', 'user_type' => $user_type])->update(['value' => $request->$offer_for]);   
            //ReferralSetting::where(['name' => 'max_trip', 'user_type' => $user_type])->update(['value' => $request->$max_trip]);
            ReferralSetting::where(['name' => 'withdrawal_method', 'user_type' => $user_type])->update(['value' => $request->$withdrawal_method]);
            ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);   
            
        }

        else if($user_type == 'RiderDiscountOffer1'){
            $rules = array(
                $apply_referral     => 'required_with:1,0',
                $start_time         => 'required',
                $end_time           => 'required',
                $offer_for          => 'required:our_employee,all',
                $rate_fixed         => 'required_with:rate,fixed',
                $currency_code      => 'required',
                $amount             => 'required|numeric|min:1',
                $max_trip           => 'required|numeric|min:1',

            );

            // Fields Validation Custom Names
            $attributes = array(
                $apply_referral     => 'Apply Offer',
                $offer_for          => 'Offer for',
                $rate_fixed          => 'Rate/ Fixed',
                $currency_code      => 'Currency Code',
                $amount             => 'Offer Amount',
            );

            $validator = Validator::make($request->all(), $rules, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            ReferralSetting::where(['name' => 'apply_referral', 'user_type' => $user_type])->update(['value' => $request->$apply_referral]);
            ReferralSetting::where(['name' => 'start_time', 'user_type' => $user_type])->update(['value' => $request->$start_time]);
            ReferralSetting::where(['name' => 'end_time', 'user_type' => $user_type])->update(['value' => $request->$end_time]);
            ReferralSetting::where(['name' => 'rate_fixed', 'user_type' => $user_type])->update(['value' => $request->$rate_fixed]);
            ReferralSetting::where(['name' => 'offer_for', 'user_type' => $user_type])->update(['value' => $request->$offer_for]);   
            ReferralSetting::where(['name' => 'currency_code', 'user_type' => $user_type])->update(['value' => $request->$currency_code]);
            ReferralSetting::where(['name' => 'amount', 'user_type' => $user_type])->update(['value' => $request->$amount]);
            ReferralSetting::where(['name' => 'max_trip', 'user_type' => $user_type])->update(['value' => $request->$max_trip]);
            ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
        }

        else if($user_type == 'DriverTripBonus'){ //Version 2
            $first_stage_min_trips    = $user_type.'_first_stage_min_trips';
            $first_stage_max_trips    = $user_type.'_first_stage_max_trips';
            $first_stage_amount       = $user_type.'_first_stage_amount';

            $second_stage_min_trips    = $user_type.'_second_stage_min_trips';
            $second_stage_max_trips    = $user_type.'_second_stage_max_trips';
            $second_stage_amount       = $user_type.'_second_stage_amount';

            $third_stage_min_trips    = $user_type.'_third_stage_min_trips';
            $third_stage_max_trips    = $user_type.'_third_stage_max_trips';
            $third_stage_amount       = $user_type.'_third_stage_amount';



            $rules = array(
                $apply_referral     => 'required_with:1,0',
                $start_time         => 'required',
                $end_time           => 'required',
                
                $first_stage_min_trips    => 'required|numeric|min:1',
                $first_stage_max_trips    => 'required|numeric|min:1',
                $first_stage_amount       => 'required|numeric|min:1',

                $second_stage_min_trips    => 'required|numeric|min:1',
                $second_stage_max_trips    => 'required|numeric|min:1',
                $second_stage_amount       => 'required|numeric|min:1',

                $third_stage_min_trips    => 'required|numeric|min:1',
                $third_stage_amount       => 'required|numeric|min:1',
                
                $number_of_days    => 'required|numeric|min:1',
                $currency_code      => 'required',
                //$amount             => 'required|numeric|min:1',
                $withdrawal_method  => 'required_with:Balance,Cash,Owe',
                $terms_condition    => 'required',
                $day_name    => 'required',

            );

            // Fields Validation Custom Names
            $attributes = array(
                $apply_referral     => 'Apply Bonus',
                $number_of_trips    => 'Number of Trips',
                $number_of_days     => 'Within Days',
                $currency_code      => 'Currency Code',
                //$amount             => 'Referral/ Bonus Amount',
                $withdrawal_method  => 'Withdrawal method',
                $terms_condition    => 'Terms and condition',
                $day_name    => 'Bonus Day',
            );

            $validator = Validator::make($request->all(), $rules, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            ReferralSetting::where(['name' => 'apply_referral', 'user_type' => $user_type])->update(['value' => $request->$apply_referral]);
            ReferralSetting::where(['name' => 'start_time', 'user_type' => $user_type])->update(['value' => $request->$start_time]);
            ReferralSetting::where(['name' => 'end_time', 'user_type' => $user_type])->update(['value' => $request->$end_time]);
            ReferralSetting::where(['name' => 'number_of_trips', 'user_type' => $user_type])->update(['value' => $request->$number_of_trips]);
            ReferralSetting::where(['name' => 'number_of_days', 'user_type' => $user_type])->update(['value' => $request->$number_of_days]);
            ReferralSetting::where(['name' => 'currency_code', 'user_type' => $user_type])->update(['value' => $request->$currency_code]);
            //ReferralSetting::where(['name' => 'amount', 'user_type' => $user_type])->update(['value' => $request->$amount]);
            ReferralSetting::where(['name' => 'withdrawal_method', 'user_type' => $user_type])->update(['value' => $request->$withdrawal_method]);
            ReferralSetting::where(['name' => 'day_name', 'user_type' => $user_type])->update(['value' => $request->$day_name]);
            ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
            
            ReferralSetting::where(['name' => 'first_stage_min_trips', 'user_type' => $user_type])->update(['value' => $request->$first_stage_min_trips]);
            ReferralSetting::where(['name' => 'first_stage_max_trips', 'user_type' => $user_type])->update(['value' => $request->$first_stage_max_trips]);
            ReferralSetting::where(['name' => 'first_stage_amount', 'user_type' => $user_type])->update(['value' => $request->$first_stage_amount]);
            
            ReferralSetting::where(['name' => 'second_stage_min_trips', 'user_type' => $user_type])->update(['value' => $request->$second_stage_min_trips]);
            ReferralSetting::where(['name' => 'second_stage_max_trips', 'user_type' => $user_type])->update(['value' => $request->$second_stage_max_trips]);
            ReferralSetting::where(['name' => 'second_stage_amount', 'user_type' => $user_type])->update(['value' => $request->$second_stage_amount]);
            
            ReferralSetting::where(['name' => 'third_stage_min_trips', 'user_type' => $user_type])->update(['value' => $request->$third_stage_min_trips]);
            ReferralSetting::where(['name' => 'third_stage_max_trips', 'user_type' => $user_type])->update(['value' => $request->$third_stage_max_trips]);
            ReferralSetting::where(['name' => 'third_stage_amount', 'user_type' => $user_type])->update(['value' => $request->$third_stage_amount]);

            ReferralSetting::where(['name' => 'bonus_start_after_month', 'user_type' => $user_type])->update(['value' => $request->$bonus_start_after_month]);
            ReferralSetting::where(['name' => 'allow_same_user', 'user_type' => $user_type])->update(['value' => $request->$allow_same_user]);
            ReferralSetting::where(['name' => 'trip_distance', 'user_type' => $user_type])->update(['value' => $request->$trip_distance]);            
        }

        else if($user_type == 'DriverOnlineBonus'){ //Version 2
            $rules = array(
                $apply_referral     => 'required_with:1,0',
                $start_time         => 'required',
                $end_time           => 'required',
                $peak_hour           => 'required|numeric|min:1',
                $trip_complete_percent    => 'required|numeric|min:1',
                $currency_code      => 'required',
                $amount             => 'required|numeric|min:1',
                $withdrawal_method  => 'required_with:Balance,Cash,Owe',
                $terms_condition    => 'required',
            );

            // Fields Validation Custom Names
            $attributes = array(
                $apply_referral     => 'Apply Bonus',
                $peak_hour           => 'Online Peak Hours',
                $min_hour           => 'Minimum Online Hours',
                $trip_complete_percent     => 'Minimum Trip',
                $currency_code      => 'Currency Code',
                $amount             => 'Referral/ Bonus Amount',
                $withdrawal_method  => 'Withdrawal method',
                $terms_condition    => 'Terms and condition',
            );

            $validator = Validator::make($request->all(), $rules, $attributes);
            
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
               
            }
            
            ReferralSetting::where(['name' => 'apply_referral', 'user_type' => $user_type])->update(['value' => $request->$apply_referral]);
            ReferralSetting::where(['name' => 'start_time', 'user_type' => $user_type])->update(['value' => $request->$start_time]);
            ReferralSetting::where(['name' => 'end_time', 'user_type' => $user_type])->update(['value' => $request->$end_time]);
            ReferralSetting::where(['name' => 'peak_hour', 'user_type' => $user_type])->update(['value' => $request->$peak_hour]);
            ReferralSetting::where(['name' => 'trip_complete_percent', 'user_type' => $user_type])->update(['value' => $request->$trip_complete_percent]);
            ReferralSetting::where(['name' => 'currency_code', 'user_type' => $user_type])->update(['value' => $request->$currency_code]);
            ReferralSetting::where(['name' => 'amount', 'user_type' => $user_type])->update(['value' => $request->$amount]);
            ReferralSetting::where(['name' => 'withdrawal_method', 'user_type' => $user_type])->update(['value' => $request->$withdrawal_method]);ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
            ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
        }

/*
        else if($user_type == 'DriverTripBonus'){ //PREVIOUS
            $rules = array(
                $apply_referral     => 'required_with:1,0',
                $start_time         => 'required',
                $end_time           => 'required',
                $number_of_trips    => 'required|numeric|min:1',
                $number_of_days    => 'required|numeric|min:1',
                $currency_code      => 'required',
                $amount             => 'required|numeric|min:1',
                $withdrawal_method  => 'required_with:Balance,Cash,Owe',
                $terms_condition    => 'required',
                $day_name    => 'required',

            );

            // Fields Validation Custom Names
            $attributes = array(
                $apply_referral     => 'Apply Bonus',
                $number_of_trips    => 'Number of Trips',
                $number_of_days     => 'Within Days',
                $currency_code      => 'Currency Code',
                $amount             => 'Referral/ Bonus Amount',
                $withdrawal_method  => 'Withdrawal method',
                $terms_condition    => 'Terms and condition',
                $day_name    => 'Bonus Day',
            );

            $validator = Validator::make($request->all(), $rules, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            ReferralSetting::where(['name' => 'apply_referral', 'user_type' => $user_type])->update(['value' => $request->$apply_referral]);
            ReferralSetting::where(['name' => 'start_time', 'user_type' => $user_type])->update(['value' => $request->$start_time]);
            ReferralSetting::where(['name' => 'end_time', 'user_type' => $user_type])->update(['value' => $request->$end_time]);
            ReferralSetting::where(['name' => 'number_of_trips', 'user_type' => $user_type])->update(['value' => $request->$number_of_trips]);
            ReferralSetting::where(['name' => 'number_of_days', 'user_type' => $user_type])->update(['value' => $request->$number_of_days]);
            ReferralSetting::where(['name' => 'currency_code', 'user_type' => $user_type])->update(['value' => $request->$currency_code]);
            ReferralSetting::where(['name' => 'amount', 'user_type' => $user_type])->update(['value' => $request->$amount]);
            ReferralSetting::where(['name' => 'withdrawal_method', 'user_type' => $user_type])->update(['value' => $request->$withdrawal_method]);
            ReferralSetting::where(['name' => 'day_name', 'user_type' => $user_type])->update(['value' => $request->$day_name]);
            ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
        }
        else if($user_type == 'Driver' OR $user_type == 'Rider'){
            $rules = array(
                $apply_referral     => 'required_with:1,0',
                $start_time         => 'required',
                $end_time           => 'required',
                $number_of_trips    => 'required|numeric|min:1',
                $currency_code      => 'required',
                $amount             => 'required|numeric|min:1',
                $who_get_bonus      => 'required_with:Referrer,Referral,Both',
                $terms_condition    => 'required',
                $withdrawal_method  => 'required_with:Balance,Cash,Owe',
            );

            // Fields Validation Custom Names
            $attributes = array(
                $apply_referral     => 'Apply Referral',
                $number_of_trips    => 'Number of Trips',
                $currency_code      => 'Currency Code',
                $amount             => 'Referral/ Bonus Amount',
                $who_get_bonus      => 'Who get the Referral/ Bonus',
                $terms_condition    => 'Terms and condition',
                $withdrawal_method  => 'Withdrawal method',
            );

            $validator = Validator::make($request->all(), $rules, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            ReferralSetting::where(['name' => 'apply_referral', 'user_type' => $user_type])->update(['value' => $request->$apply_referral]);
            ReferralSetting::where(['name' => 'start_time', 'user_type' => $user_type])->update(['value' => $request->$start_time]);
            ReferralSetting::where(['name' => 'end_time', 'user_type' => $user_type])->update(['value' => $request->$end_time]);
            ReferralSetting::where(['name' => 'number_of_trips', 'user_type' => $user_type])->update(['value' => $request->$number_of_trips]);
            ReferralSetting::where(['name' => 'current(array)ency_code', 'user_type' => $user_type])->update(['value' => $request->$currency_code]);
            ReferralSetting::where(['name' => 'amount', 'user_type' => $user_type])->update(['value' => $request->$amount]);
            ReferralSetting::where(['name' => 'who_get_bonus', 'user_type' => $user_type])->update(['value' => $request->$who_get_bonus]);
            ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
            ReferralSetting::where(['name' => 'withdrawal_method', 'user_type' => $user_type])->update(['value' => $request->$withdrawal_method]);
        }
        
        else if($user_type == 'DriverOnlineBonus'){ 
            $rules = array(
                $apply_referral     => 'required_with:1,0',
                $start_time         => 'required',
                $end_time           => 'required',
                $min_hour           => 'required|numeric|min:1',
                $min_trip           => 'required|numeric|min:1',
                $currency_code      => 'required',
                $amount             => 'required|numeric|min:1',
                $withdrawal_method  => 'required_with:Balance,Cash,Owe',
                $terms_condition    => 'required',
            );

            // Fields Validation Custom Names
            $attributes = array(
                $apply_referral     => 'Apply Bonus',
                $min_hour           => 'Minimum Online Hours',
                $min_trip           => 'Minimum Trip',
                $currency_code      => 'Currency Code',
                $amount             => 'Referral/ Bonus Amount',
                $withdrawal_method  => 'Withdrawal method',
                $terms_condition    => 'Terms and condition',
            );

            $validator = Validator::make($request->all(), $rules, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            
            ReferralSetting::where(['name' => 'apply_referral', 'user_type' => $user_type])->update(['value' => $request->$apply_referral]);
            ReferralSetting::where(['name' => 'start_time', 'user_type' => $user_type])->update(['value' => $request->$start_time]);
            ReferralSetting::where(['name' => 'end_time', 'user_type' => $user_type])->update(['value' => $request->$end_time]);
            ReferralSetting::where(['name' => 'min_hour', 'user_type' => $user_type])->update(['value' => $request->$min_hour]);
            ReferralSetting::where(['name' => 'min_trip', 'user_type' => $user_type])->update(['value' => $request->$min_trip]);
            ReferralSetting::where(['name' => 'currency_code', 'user_type' => $user_type])->update(['value' => $request->$currency_code]);
            ReferralSetting::where(['name' => 'amount', 'user_type' => $user_type])->update(['value' => $request->$amount]);
            ReferralSetting::where(['name' => 'withdrawal_method', 'user_type' => $user_type])->update(['value' => $request->$withdrawal_method]);ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
            ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
        }

        else if($user_type == 'RiderCashback1' OR $user_type == 'RiderCashback2'){
            $rules = array(
                $apply_referral     => 'required_with:1,0',
                $start_time         => 'required',
                $end_time           => 'required',
                $number_of_trips    => 'required|numeric|min:1',
                $currency_code      => 'required',
                $amount             => 'required|numeric|min:1',
                $who_get_bonus      => 'required_with',
                $withdrawal_method  => 'required_with:Balance,Cash,Owe',
                $terms_condition    => 'required',
            );

            // Fields Validation Custom Names
            $attributes = array(
                $apply_referral     => 'Apply Bonus',
                $number_of_trips    => 'Minimum Online Hours',
                $currency_code      => 'Currency Code',
                $amount             => 'Referral/ Bonus Amount',
                $who_get_bonus      => 'Adjustable',
                $withdrawal_method  => 'Withdrawal method',
                $terms_condition    => 'Terms and condition',
            );

            $validator = Validator::make($request->all(), $rules, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            
            ReferralSetting::where(['name' => 'apply_referral', 'user_type' => $user_type])->update(['value' => $request->$apply_referral]);
            ReferralSetting::where(['name' => 'start_time', 'user_type' => $user_type])->update(['value' => $request->$start_time]);
            ReferralSetting::where(['name' => 'end_time', 'user_type' => $user_type])->update(['value' => $request->$end_time]);
            ReferralSetting::where(['name' => 'number_of_trips', 'user_type' => $user_type])->update(['value' => $request->$number_of_trips]);   
            ReferralSetting::where(['name' => 'currency_code', 'user_type' => $user_type])->update(['value' => $request->$currency_code]);
            ReferralSetting::where(['name' => 'amount', 'user_type' => $user_type])->update(['value' => $request->$amount]);
            ReferralSetting::where(['name' => 'who_get_bonus', 'user_type' => $user_type])->update(['value' => $request->$who_get_bonus]);
            ReferralSetting::where(['name' => 'withdrawal_method', 'user_type' => $user_type])->update(['value' => $request->$withdrawal_method]);ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
            ReferralSetting::where(['name' => 'terms_condition', 'user_type' => $user_type])->update(['value' => $request->$terms_condition]);
        }

        
*/
        


        
        $this->helper->flash_message('success', 'Updated Successfully');

        return redirect($this->base_url);
    }
}