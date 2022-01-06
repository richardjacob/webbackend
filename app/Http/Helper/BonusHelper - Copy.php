<?php

namespace App\Http\Helper;

use App\Models\ReferralSetting;
use App\Models\Bonus;
use App\Models\Request;
use App\Models\Trips;
use App\Models\Payment;
use App\Models\User;
use App\Models\Activity;
use App\Models\DriverOnlineTime;
use App\Models\Wallet;
use App\Models\DriverBalance;
use App\Models\PeakHour;
use App\Models\WalletHistory;
use App\Models\DiscountOfferUsed;
use App\Http\Controllers\CustomLog;
use DB;


class BonusHelper
{
	public function transfer_to_balance($user, $bonus)
	{
		if ($bonus->withdrawal_method == 'Balance') { // wallet
			// $request = new Request;
			// $request->user_id = $user->id;
			// $request->pickup_latitude = 0;
			// $request->pickup_longitude = 0;
			// $request->drop_latitude = 0;
			// $request->drop_longitude = 0;
			// $request->pickup_location = 0;
			// $request->pickup_location = 0;
			// $request->drop_location = 0;
			// $request->car_id = 3; //Normal
			// $request->driver_id  = $user->id;
			// $request->payment_mode = $bonus->withdrawal_method;
			// $request->location_id = 1;
			// $request->additional_fare = 0;
			// $request->peak_fare = 0;
			// $request->additional_rider = 0;
			// $request->timezone = Config('app.timezone');
			// $request->trip_path = 0;
			// $request->status = 'Accepted';
			// $request->save();

			// $trips = new Trips;
			// $trips->driver_id = $user->id;
			// $trips->driver_payout = $bonus->bonus_amount;
			// $trips->currency_code = $bonus->currency_code;
			// $trips->status = 'Completed';

			// $trips->user_id = $user->id;
			// $trips->pool_id = 0;
			// $trips->pickup_latitude = 0;
			// $trips->pickup_longitude = 0;
			// $trips->drop_latitude = 0;
			// $trips->drop_longitude = 0;
			// $trips->pickup_location = 0;
			// $trips->drop_location = 0;
			// $trips->car_id = 3; //Normal
			// $trips->request_id = $request->id;
			// $trips->map_image = 0;
			// $trips->total_time = 0;
			// $trips->total_km = 0;
			// $trips->time_fare = 0;
			// $trips->distance_fare = 0;
			// $trips->base_fare = 0;
			// $trips->additional_rider = 0;
			// $trips->additional_rider_amount = 0;
			// $trips->peak_fare = 0;
			// $trips->peak_amount = 0;
			// $trips->driver_peak_amount = 0;
			// $trips->schedule_fare = 0;
			// $trips->access_fee = 0;
			// $trips->promo_amount = 0;
			// $trips->subtotal_fare = 0;
			// $trips->total_fare = 0;
			// $trips->driver_or_company_commission = 0;
			// $trips->owe_amount = 0;
			// $trips->remaining_owe_amount = 0;
			// $trips->applied_owe_amount = 0;
			// $trips->to_trip_id = 0;
			// $trips->paykey = 0;
			// $trips->fare_estimation = 0;
			// $trips->created_at = date('Y-m-d H:i:s');
			// $trips->created_at = date('Y-m-d H:i:s');
			// $trips->save();

			// $payment = new Payment;
			// $payment->trip_id = $trips->id;
			// $payment->save();


			$exist_driver_bonus = DriverBalance::where('bonus_id', $bonus->id)->first();

			if (!$exist_driver_bonus) {
				$bonus_update = Bonus::find($bonus->id);
				$bonus_update->status = 'Paid';
				$bonus_update->save();

				$driver_bonus = new DriverBalance;
				$driver_bonus->user_id = $user->id;
				$driver_bonus->bonus_id = $bonus->id;
				$driver_bonus->amount = $bonus->bonus_amount;
				$driver_bonus->status = 'pending';
				if ($driver_bonus->save()) return true;
			}
		}
	}

	public function transfer_to_balance_trip_bonus($user_id, $bonus_id, $amount)
	{
		$exist_driver_bonus = DriverBalance::where('bonus_id', $bonus_id)->first();

		if (!$exist_driver_bonus) {
			$bonus_update = Bonus::find($bonus_id);
			$bonus_update->status = 'Paid';
			$bonus_update->save();

			$driver_bonus = new DriverBalance;
			$driver_bonus->user_id = $user_id;
			$driver_bonus->bonus_id = $bonus_id;
			$driver_bonus->amount = $amount;
			$driver_bonus->status = 'pending';
			if ($driver_bonus->save()) return true;
		}
	}



	public function transfer_to_balance_rider($user, $bonus)
	{
		$wallet_history = WalletHistory::where('bonus_id', $bonus['id'])
			->where('user_id', $user->id)
			->first();
		if ($wallet_history == '') {
			if ($bonus['withdrawal_method'] == 'Balance' or $bonus['withdrawal_method'] == 'Next Trip') { // wallet
				$wallet = Wallet::where('user_id', $user->id)->first();

				if (is_object($wallet)) {
					if ($wallet->last_bonus_id != $bonus['id']) {
						$pre_wallet_amount = $wallet->amount;
						$wallet->amount = $pre_wallet_amount + $bonus['bonus_amount'];
						$wallet->last_bonus_id = $bonus['id'];
						$wallet->updated_at = now();
						$wallet->save();
					}
				} else { //add wallet
					$balance = new Wallet;
					$balance->user_type = $user->user_type;
					$balance->user_id = $user->id;
					$balance->amount = $bonus['bonus_amount'];
					$balance->currency_code = $bonus['currency_code'];
					$balance->last_bonus_id = $bonus['id'];
					$balance->created_at = now();
					$balance->updated_at = now();
					$balance->save();
				}

				$bonus_update = Bonus::find($bonus['id']);
				$bonus_update->status = 'Paid';
				$bonus_update->save();

				$history = new WalletHistory;
				$history->user_id = $user->id;
				$history->user_type = $user->user_type;
				$history->bonus_type = $bonus['bonus_type'];
				$history->bonus_id = $bonus['id'];
				$history->amount = $bonus['bonus_amount'];
				$history->created_at = now();
				$history->updated_at = now();
				$history->save();
			}
		}
	}

	public function driver_signup_bonus($user) //checked
	{
		$referralSetting = ReferralSetting::DriverSignupBonus()->get()->pluck('value', 'name')->toArray();

		if ($referralSetting['apply_referral'] == '1' and $referralSetting['amount'] > 0 and $referralSetting['start_time'] <= now() and $referralSetting['end_time'] >= now()) {

			$exists_data_bonus = Bonus::where('user_id', $user->id)
				->where('user_type', $user->user_type)
				->where('bonus_type', 'DriverSignupBonus')
				->first();
			if (!$exists_data_bonus) {
				$bonus = new Bonus;
				$bonus->user_id = $user->id;
				$bonus->user_type = $user->user_type;
				$bonus->bonus_type = 'DriverSignupBonus';
				$bonus->number_of_trips = $referralSetting['number_of_trips'];
				$bonus->currency_code = $referralSetting['currency_code'];
				$bonus->bonus_amount = $referralSetting['amount'];
				$bonus->terms_condition = $referralSetting['terms_condition'];
				$bonus->withdrawal_method = $referralSetting['withdrawal_method'];
				$bonus->unique_id = strtotime("now") . rand(10000, 100000);
				$bonus->bonus_date = date('Y-m-d');
				$bonus->save();
			}
		}
		CustomLog::info("driver_signup_bonus");
	}

	public function adjust_driver_signup_bonus($user) //checked
	{	//when trips completed

		$exists_data_bonus = Bonus::where('user_id', $user->id)
			->where('user_type', $user->user_type)
			->where('bonus_type', 'DriverSignupBonus')
			->whereRaw('number_of_trips > completed_trips')
			->first();

		if ($exists_data_bonus) {
			if ($exists_data_bonus->completed_trips == '') $completed_trips = 1;
			else $completed_trips = $exists_data_bonus->completed_trips + 1;

			$updated_bonus = Bonus::find($exists_data_bonus->id);
			$updated_bonus->completed_trips = $completed_trips;

			if ($updated_bonus->save()) {
				if ($completed_trips == $exists_data_bonus->number_of_trips) {
					self::transfer_to_balance($user, $updated_bonus);
				}
				return '1';
			}
		}
	}

	public function driver_trip_bonus($user) //checked
	{
		$start_date = date('Y-m-d');
		$end_date = date("Y-m-d", strtotime("6 days"));
		$referralSetting = ReferralSetting::DriverTripBonus()->get()->pluck('value', 'name')->toArray();
		if ($referralSetting['apply_referral'] == '1' and $referralSetting['amount'] > 0 and $referralSetting['start_time'] <= now() and $referralSetting['end_time'] >= now()) {
			$exists_data_bonus = Bonus::where('user_id', $user->id)
				->where('user_type', $user->user_type)
				->where('bonus_type', 'DriverTripBonus')
				->first();
			if (!$exists_data_bonus) {
				$trip_bonus = new Bonus;
				$trip_bonus->user_id = $user->id;
				$trip_bonus->user_type = $user->user_type;
				$trip_bonus->bonus_type = 'DriverTripBonus';
				$trip_bonus->number_of_trips = $referralSetting['number_of_trips'];
				$trip_bonus->number_of_days = $referralSetting['number_of_days'];
				$trip_bonus->currency_code = $referralSetting['currency_code'];
				$trip_bonus->bonus_amount = $referralSetting['amount'];
				$trip_bonus->terms_condition = $referralSetting['terms_condition'];
				$trip_bonus->withdrawal_method = $referralSetting['withdrawal_method'];
				$trip_bonus->unique_id = strtotime("now") . rand(10000, 100000);
				$trip_bonus->start_date = $start_date;
				$trip_bonus->end_date = $end_date;
				$trip_bonus->online_bonus_date = $start_date;
				$trip_bonus->save();
			}
		}
	}

	public function adjust_driver_trip_bonus($user) //checked
	{	//when trips completed
		$exists_data_bonus = Bonus::where('user_id', $user->id)
			->where('user_type', $user->user_type)
			->where('bonus_type', 'DriverTripBonus')
			->whereRaw('number_of_trips > completed_trips')
			->first();

		if ($exists_data_bonus) {
			if ($exists_data_bonus->completed_trips == '') $completed_trips = 1;
			else $completed_trips = $exists_data_bonus->completed_trips + 1;

			$updated_bonus = Bonus::find($exists_data_bonus->id);
			$updated_bonus->completed_trips = $completed_trips;
			$updated_bonus->save();
		}
	}

	

	public function driver_referral_bonus($user) //checked
	{
		// previous using at App\Providers\StartServiceProvider
		$referralSetting = ReferralSetting::DriverReferral()->get()->pluck('value', 'name')->toArray();
		$today = date('Y-m-d');

		if ($referralSetting['apply_referral'] == '1' and $referralSetting['amount'] > 0 and $referralSetting['start_time'] <= now() and $referralSetting['end_time'] >= now()) {

			if ($user->used_referral_code != '') {
				$referrer_id = User::where('referral_code', $user->used_referral_code)->pluck('id')->first();
			} else $referrer_id = '';

			$exists_data_bonus = Bonus::where('user_id', $user->id)
				->where('user_type', $user->user_type)
				->where('bonus_type', 'Driver')
				->first();
			if (!$exists_data_bonus) {

				if ($referralSetting['who_get_bonus'] == 'Referral') {
					$bonus_referral = new Bonus;
					$bonus_referral->user_id = $user->id;
					$bonus_referral->referred_by = $referrer_id;
					$bonus_referral->user_type = $user->user_type;
					$bonus_referral->bonus_type = 'Driver';
					$bonus_referral->who_get_bonus = $referralSetting['who_get_bonus'];
					$bonus_referral->number_of_days = $referralSetting['number_of_days'];
					$bonus_referral->number_of_trips = $referralSetting['number_of_trips'];
					$bonus_referral->currency_code = $referralSetting['currency_code'];
					$bonus_referral->bonus_amount = $referralSetting['amount'];
					$bonus_referral->terms_condition = $referralSetting['terms_condition'];
					$bonus_referral->withdrawal_method = $referralSetting['withdrawal_method'];
					$bonus_referral->unique_id = strtotime("now") . rand(10000, 100000);
					$bonus_referral->bonus_date = $today;
					$bonus_referral->save();
				} else if ($referralSetting['who_get_bonus'] == 'Referrer') {
					$bonus_referrer = new Bonus;
					$bonus_referrer->user_id = $referrer_id;
					$bonus_referrer->referral_to = $user->id;
					$bonus_referrer->user_type = $user->user_type;
					$bonus_referrer->bonus_type = 'Driver';
					$bonus_referrer->who_get_bonus = $referralSetting['who_get_bonus'];
					$bonus_referrer->number_of_days = $referralSetting['number_of_days'];
					$bonus_referrer->number_of_trips = $referralSetting['number_of_trips'];
					$bonus_referrer->currency_code = $referralSetting['currency_code'];
					$bonus_referrer->bonus_amount = $referralSetting['amount'];
					$bonus_referrer->terms_condition = $referralSetting['terms_condition'];
					$bonus_referrer->withdrawal_method = $referralSetting['withdrawal_method'];
					$bonus_referrer->unique_id = strtotime("now") . rand(10000, 100000);
					$bonus_referrer->bonus_date = $today;
					$bonus_referrer->save();
				} else if ($referralSetting['who_get_bonus'] == 'Both') { //Referral
					$bonus_referral = new Bonus;
					$bonus_referral->user_id = $user->id;
					$bonus_referral->referred_by = $referrer_id;
					$bonus_referral->user_type = $user->user_type;
					$bonus_referral->bonus_type = 'Driver';
					$bonus_referral->who_get_bonus = $referralSetting['who_get_bonus'];
					$bonus_referral->number_of_days = $referralSetting['number_of_days'];
					$bonus_referral->number_of_trips = $referralSetting['number_of_trips'];
					$bonus_referral->currency_code = $referralSetting['currency_code'];
					$bonus_referral->bonus_amount = $referralSetting['amount'];
					$bonus_referral->terms_condition = $referralSetting['terms_condition'];
					$bonus_referral->withdrawal_method = $referralSetting['withdrawal_method'];
					$bonus_referral->unique_id = strtotime("now") . rand(10000, 100000);
					$bonus_referral->bonus_date = $today;
					$bonus_referral->save();

					// Referrer
					$bonus_referrer = new Bonus;
					$bonus_referrer->user_id = $referrer_id;
					$bonus_referrer->referral_to = $user->id;
					$bonus_referrer->user_type = $user->user_type;
					$bonus_referrer->bonus_type = 'Driver';
					$bonus_referrer->who_get_bonus = $referralSetting['who_get_bonus'];
					$bonus_referrer->number_of_days = $referralSetting['number_of_days'];
					$bonus_referrer->number_of_trips = $referralSetting['number_of_trips'];
					$bonus_referrer->currency_code = $referralSetting['currency_code'];
					$bonus_referrer->bonus_amount = $referralSetting['amount'];
					$bonus_referrer->terms_condition = $referralSetting['terms_condition'];
					$bonus_referrer->withdrawal_method = $referralSetting['withdrawal_method'];
					$bonus_referrer->unique_id = strtotime("now") . rand(10000, 100000);
					$bonus_referrer->bonus_date = $today;
					$bonus_referrer->save();
				}
			}
		}
	}

	public function adjust_driver_referral_bonus($user) //checked
	{	//when trips completed
		$exists_data_bonus = Bonus::where('referral_to', $user->id) //user_id > referral_to
			->where('user_type', $user->user_type)
			->where('bonus_type', 'Driver')
			->whereRaw('number_of_trips > completed_trips')
			->first();

		if ($exists_data_bonus) {
			if ($exists_data_bonus->completed_trips == '') $completed_trips = 1;
			else $completed_trips = $exists_data_bonus->completed_trips + 1;

			$updated_bonus = Bonus::find($exists_data_bonus->id);
			$updated_bonus->completed_trips = $completed_trips;

			if ($updated_bonus->save()) {
				$referral_data_bonus = Bonus::where('user_id', $user->id) //user_id > referral_to
					->where('user_type', $user->user_type)
					->where('referred_by', $exists_data_bonus->user_id)
					->where('bonus_type', 'Driver')
					->whereRaw('number_of_trips > completed_trips')
					->first();

				if ($referral_data_bonus) {
					if ($referral_data_bonus->completed_trips == '') $completed_trips2 = 1;
					else $completed_trips2 = $referral_data_bonus->completed_trips + 1;

					$updated_bonus2 = Bonus::find($referral_data_bonus->id);
					$updated_bonus2->completed_trips = $completed_trips2;

					$updated_bonus2->save();
				}

				if ($completed_trips == $exists_data_bonus->number_of_trips and $completed_trips > 0) {
					//reffered by 
					$referred_user = User::where('id', $exists_data_bonus->user_id)->first();
					self::transfer_to_balance($referred_user, $exists_data_bonus);
				}

				if ($completed_trips2 == $referral_data_bonus->number_of_trips and $completed_trips2 > 0) {
					//reffaral to 
					self::transfer_to_balance($user, $referral_data_bonus);
					return '1';
				}
			}
		}
	}


	public function set_driver_online_bonus($user) // weekly bonus
	{ // by Cron setup
		$today_name = date('l');
		$referral = ReferralSetting::DriverTripBonus()
			->get()
			->pluck('value', 'name')
			->toArray();

		if ($referral['apply_referral'] == '1' and $referral['amount'] > 0 and $referral['start_time'] <= now() and $referral['end_time'] >= now()) {
			if ($referral['day_name'] == $today_name) {
				$start_date = date('Y-m-d');
				$end_date = date("Y-m-d", strtotime("6 days"));
			} else {
				$start_date = date('Y-m-d', strtotime('last ' . $referral['day_name'], strtotime(now())));
				$end_date = date('Y-m-d', strtotime($start_date . '+6 day'));
			}

			$current_bonus = Bonus::where('user_id', $user->id)
				->where('user_type', 'Driver')
				->where('bonus_type', 'DriverTripBonus')
				->where('start_date', $start_date)
				->where('end_date', $end_date)
				->orderBy('id', 'DESC')
				->first();
			if ($current_bonus == '') {
				$trip_bonus = new Bonus;
				$trip_bonus->user_id = $user->id;
				$trip_bonus->user_type = $user->user_type;
				$trip_bonus->bonus_type = 'DriverTripBonus';
				$trip_bonus->number_of_trips = $referral['number_of_trips'];
				$trip_bonus->number_of_days = $referral['number_of_days'];
				$trip_bonus->currency_code = $referral['currency_code'];
				$trip_bonus->bonus_amount = $referral['amount'];
				$trip_bonus->terms_condition = $referral['terms_condition'];
				$trip_bonus->withdrawal_method = $referral['withdrawal_method'];
				$trip_bonus->unique_id = strtotime("now") . rand(10000, 100000);
				$trip_bonus->start_date = $start_date;
				$trip_bonus->end_date = $end_date;
				$trip_bonus->bonus_date = $start_date;
				$trip_bonus->save();
			}
		}
	}

	// public function adjust_driver_online_bonus($driver_id, $online_date, $online_time_in_second, $peak_time_in_second)
	// { // by Cron setup

	// 	$online_time_hour = $online_time_in_second / (60 * 60);
	// 	$peak_time_hour = $peak_time_in_second / (60 * 60);

	// 	$referralSetting = ReferralSetting::DriverOnlineBonus()->get()->pluck('value', 'name')->toArray();
	// 	$total_trips = Trips::where('driver_id', $driver_id)
	// 							->where('status', 'Completed')
	// 							->where('subtotal_fare', '>', 0)
	// 							->whereDate('created_at', '=', $online_date)
	// 							->count();

	// 	if (
	// 		$referralSetting['apply_referral'] == '1' and
	// 		$referralSetting['amount'] > 0  and
	// 		$peak_time_hour >= $referralSetting['peak_hour'] and
	// 		$online_time_hour >= $referralSetting['min_hour'] and
	// 		$total_trips >= $referralSetting['min_trip']
	// 	) {
	// 		$user_bonus_exist = Bonus::where('user_type', 'Driver')
	// 									->where('bonus_type', 'DriverOnlineBonus')
	// 									->where('user_id', $driver_id)
	// 									->where('bonus_date', $online_date)
	// 									->first();
	// 		if (!is_object($user_bonus_exist)) {
	// 			$user = User::where('id', $driver_id)->first();
	// 			$online_bonus = new Bonus;
	// 			$online_bonus->user_id = $driver_id;
	// 			$online_bonus->user_type = 'Driver';
	// 			$online_bonus->bonus_type = 'DriverOnlineBonus';
	// 			$online_bonus->completed_trips = $total_trips;
	// 			$online_bonus->peak_hour = $referralSetting['peak_hour'];
	// 			$online_bonus->min_hour = $referralSetting['min_hour'];
	// 			$online_bonus->number_of_trips = $referralSetting['min_trip'];
	// 			$online_bonus->currency_code = $referralSetting['currency_code'];
	// 			$online_bonus->bonus_amount = $referralSetting['amount'];
	// 			$online_bonus->terms_condition = $referralSetting['terms_condition'];
	// 			$online_bonus->withdrawal_method = $referralSetting['withdrawal_method'];
	// 			$online_bonus->bonus_date = $online_date;
	// 			$online_bonus->unique_id = strtotime("now") . rand(10000, 100000);
	// 			$online_bonus->create_method = 'adjust_driver_online_bonus';
	// 			if ($online_bonus->save()) self::transfer_to_balance($user, $online_bonus);
	// 		}
	// 	}
	// }
	
	/*
	public function adjust_driver_online_bonus_v2($driver_id, $online_date)
	{ // by Cron setup
		$bonus = Bonus::where('user_type', 'Driver')
			->where('bonus_type', 'DriverOnlineBonus')
			->where('user_id', $driver_id)
			->where('bonus_date', $online_date)
			->where('status', '!=', 'Paid')
			->first();

		if (is_object($bonus)) {
			if (
				$bonus->completed_peak_hour >= $bonus->peak_hour and
				$bonus->completed_min_hour >= $bonus->min_hour and
				$bonus->completed_trips >= $bonus->min_trip
			) {
				$user = User::where('id', $driver_id)->first();
				self::transfer_to_balance($user, $bonus);
			} else {
				$bonus->status = 'Cancel';
				$bonus->save();
			}
		}
	}*/

	

	public function add_update_online_bonus($driver_id, $today = '')
	{ // daily bonus
		//$today = '2021-08-01';	//date('Y-m-d');
		if ($today == '') $today = date('Y-m-d');
		$activity = Activity::whereDate('created_at', '=', $today)
			->where('user_id', $driver_id)
			->select(
				'user_id',
				DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as online_date"),
				DB::raw("SUM(TIME_TO_SEC(TIMEDIFF(updated_at, created_at))) AS online_time"),
				DB::raw("SUM(TIME_TO_SEC(TIMEDIFF(peak_time_updated_at, peak_time_created_at))) AS peak_time")
			)
			->first();

		$user_exist = DriverOnlineTime::where('driver_id', $driver_id)
			->where('online_date', $today)
			->first();

		if (is_object($user_exist)) { //if exist	
			$table = DriverOnlineTime::findOrFail($user_exist->id);
			$table->online_time = $activity->online_time ?? 0;
			$table->peak_time = $activity->peak_time ?? 0;
			$table->save();
		} else {
			$table = new DriverOnlineTime;
			$table->driver_id = $driver_id;
			$table->online_date = $today;
			$table->online_time = $activity->online_time ?? 0;
			$table->peak_time = $activity->peak_time ?? 0;
			$table->save();
		}

		$total_trips = Trips::where('driver_id', $driver_id)
			->where('status', 'Completed')
			->where('subtotal_fare', '>', 0)
			->whereDate('created_at', '=', $today)
			->count();

		$user_bonus_exist = Bonus::where('user_type', 'Driver')
			->where('bonus_type', 'DriverOnlineBonus')
			->where('user_id', $driver_id)
			->where('bonus_date', $today)
			->first();

		$online_time_hour = number_format($table->online_time / (60 * 60), 2, '.', '');
		$peak_time_hour = number_format($table->peak_time / (60 * 60), 2, '.', '');

		$referralSetting = ReferralSetting::DriverOnlineBonus()->get()->pluck('value', 'name')->toArray();

		if ($referralSetting['apply_referral'] == '1' and $referralSetting['amount'] > 0 and $referralSetting['start_time'] <= now() and $referralSetting['end_time'] >= now()) {

			$user = User::where('id', $driver_id)->first();

			if (is_object($user_bonus_exist)) { //update
				$bonus_update = Bonus::findOrFail($user_bonus_exist->id);
				$bonus_update->completed_trips = $total_trips;
				$bonus_update->completed_peak_hour = $peak_time_hour;
				$bonus_update->completed_min_hour = $online_time_hour;
				$bonus_update->save();
			} else { // add
				$online_bonus = new Bonus;
				$online_bonus->user_id = $driver_id;
				$online_bonus->user_type = 'Driver';
				$online_bonus->bonus_type = 'DriverOnlineBonus';
				$online_bonus->peak_hour = $referralSetting['peak_hour'];
				$online_bonus->min_hour = $referralSetting['min_hour'];
				$online_bonus->number_of_trips = $referralSetting['min_trip'];
				$online_bonus->currency_code = $referralSetting['currency_code'];
				$online_bonus->bonus_amount = $referralSetting['amount'];
				$online_bonus->terms_condition = $referralSetting['terms_condition'];
				$online_bonus->withdrawal_method = $referralSetting['withdrawal_method'];
				$online_bonus->number_of_days = '1';
				$online_bonus->completed_trips = $total_trips;
				$online_bonus->completed_peak_hour = $peak_time_hour;
				$online_bonus->completed_min_hour = $online_time_hour;
				$online_bonus->bonus_date = $today;
				//$online_bonus->unique_id = strtotime("now") . rand(10000, 100000);
				$online_bonus->unique_id = $today . "_" . $driver_id . "_OnlineBonus";
				$online_bonus->create_method = 'add_update_online_bonus';

				try {
					$online_bonus->save();
				} catch (\Exception $e) {
					return false;
				}
			}
		}
	}

	public function rider_referral_bonus($user) //checked
	{
		// previous using at App\Providers\StartServiceProvider
		$referralSetting = ReferralSetting::RiderReferral()->get()->pluck('value', 'name')->toArray();

		if ($referralSetting['apply_referral'] == '1' and $referralSetting['amount'] > 0 and $referralSetting['start_time'] <= now() and $referralSetting['end_time'] >= now()) {
			if ($user->used_referral_code != '') {
				$referrer_id = User::where('referral_code', $user->used_referral_code)->pluck('id')->first();
			} else $referrer_id = '';

			if ($referrer_id != '') {
				if ($referralSetting['who_get_bonus'] == 'Referral') {
					$bonus_referral = new Bonus;
					$bonus_referral->user_id = $user->id;
					$bonus_referral->referred_by = $referrer_id;
					$bonus_referral->user_type = $user->user_type;
					$bonus_referral->bonus_type = 'Rider';
					$bonus_referral->who_get_bonus = $referralSetting['who_get_bonus'];
					$bonus_referral->number_of_days = $referralSetting['number_of_days'];
					$bonus_referral->number_of_trips = $referralSetting['number_of_trips'];
					$bonus_referral->currency_code = $referralSetting['currency_code'];
					$bonus_referral->bonus_amount = $referralSetting['amount'];
					$bonus_referral->terms_condition = $referralSetting['terms_condition'];
					$bonus_referral->withdrawal_method = $referralSetting['withdrawal_method'];
					$bonus_referral->unique_id = strtotime("now") . rand(10000, 100000);
					$bonus_referral->bonus_date = date('Y-m-d');
					$bonus_referral->save();
				} else if ($referralSetting['who_get_bonus'] == 'Referrer') {
					$bonus_referrer = new Bonus;
					$bonus_referrer->user_id = $referrer_id;
					$bonus_referrer->referral_to = $user->id;
					$bonus_referrer->user_type = $user->user_type;
					$bonus_referrer->bonus_type = 'Rider';
					$bonus_referrer->who_get_bonus = $referralSetting['who_get_bonus'];
					$bonus_referrer->number_of_days = $referralSetting['number_of_days'];
					$bonus_referrer->number_of_trips = $referralSetting['number_of_trips'];
					$bonus_referrer->currency_code = $referralSetting['currency_code'];
					$bonus_referrer->bonus_amount = $referralSetting['amount'];
					$bonus_referrer->terms_condition = $referralSetting['terms_condition'];
					$bonus_referrer->withdrawal_method = $referralSetting['withdrawal_method'];
					$bonus_referrer->unique_id = strtotime("now") . rand(10000, 100000);
					$bonus_referrer->bonus_date = date('Y-m-d');
					$bonus_referrer->save();
				} else if ($referralSetting['who_get_bonus'] == 'Both') {
					//Referral
					$bonus_referral = new Bonus;
					$bonus_referral->user_id = $user->id;
					$bonus_referral->referred_by = $referrer_id;
					$bonus_referral->user_type = $user->user_type;
					$bonus_referral->bonus_type = 'Rider';
					$bonus_referral->who_get_bonus = $referralSetting['who_get_bonus'];
					$bonus_referral->number_of_days = $referralSetting['number_of_days'];
					$bonus_referral->number_of_trips = $referralSetting['number_of_trips'];
					$bonus_referral->currency_code = $referralSetting['currency_code'];
					$bonus_referral->bonus_amount = $referralSetting['amount'];
					$bonus_referral->terms_condition = $referralSetting['terms_condition'];
					$bonus_referral->withdrawal_method = $referralSetting['withdrawal_method'];
					$bonus_referral->unique_id = strtotime("now") . rand(10000, 100000);
					$bonus_referral->bonus_date = date('Y-m-d');
					$bonus_referral->save();

					//Referrer
					$bonus_referrer = new Bonus;
					$bonus_referrer->user_id = $referrer_id;
					$bonus_referrer->referral_to = $user->id;
					$bonus_referrer->user_type = $user->user_type;
					$bonus_referrer->bonus_type = 'Rider';
					$bonus_referrer->who_get_bonus = $referralSetting['who_get_bonus'];
					$bonus_referrer->number_of_days = $referralSetting['number_of_days'];
					$bonus_referrer->number_of_trips = $referralSetting['number_of_trips'];
					$bonus_referrer->currency_code = $referralSetting['currency_code'];
					$bonus_referrer->bonus_amount = $referralSetting['amount'];
					$bonus_referrer->terms_condition = $referralSetting['terms_condition'];
					$bonus_referrer->withdrawal_method = $referralSetting['withdrawal_method'];
					$bonus_referrer->unique_id = strtotime("now") . rand(10000, 100000);
					$bonus_referrer->bonus_date = date('Y-m-d');
					$bonus_referrer->save();
				}
			}
		}
	}

	public function adjust_rider_referral_bonus($user)
	{	//when trips completed
		$exists_data_bonus = Bonus::where('referral_to', $user->id) //user_id > referral_to
			->where('user_type', $user->user_type)
			->where('bonus_type', 'Rider')
			->whereRaw('number_of_trips > completed_trips')
			->first();

		if ($exists_data_bonus) {
			if ($exists_data_bonus->completed_trips == '') $completed_trips = 1;
			else $completed_trips = $exists_data_bonus->completed_trips + 1;

			$updated_bonus = Bonus::find($exists_data_bonus->id);
			$updated_bonus->completed_trips = $completed_trips;

			if ($updated_bonus->save()) {
				if ($completed_trips == $exists_data_bonus->number_of_trips and $completed_trips > 0) {
					//reffered by 
					$referred_user = User::where('id', $exists_data_bonus->user_id)->first();
					self::transfer_to_balance_rider($referred_user, $exists_data_bonus->toArray());
				}

				$referral_data_bonus = Bonus::where('user_id', $user->id) //user_id > referral_to
					->where('user_type', $user->user_type)
					->where('referred_by', $exists_data_bonus->user_id)
					->where('bonus_type', 'Rider')
					->whereRaw('number_of_trips > completed_trips')
					->first();

				if ($referral_data_bonus) {
					if ($referral_data_bonus->completed_trips == '') $completed_trips2 = 1;
					else $completed_trips2 = $referral_data_bonus->completed_trips + 1;

					$updated_bonus2 = Bonus::find($referral_data_bonus->id);
					$updated_bonus2->completed_trips = $completed_trips2;

					$updated_bonus2->save();

					if ($completed_trips2 == $referral_data_bonus->number_of_trips and $completed_trips2 > 0) {
						//reffaral to 
						self::transfer_to_balance_rider($user, $referral_data_bonus->toArray());
						return '1';
					}
				}
			}
		}
	}

	public function adjust_rider_referral_bonus_v1($user) //checked
	{	//when trips completed
		$exists_data_bonus = Bonus::where('referral_to', $user->id) //user_id > referral_to
			->where('user_type', $user->user_type)
			->where('bonus_type', 'Rider')
			->whereRaw('number_of_trips > completed_trips')
			->first();

		if ($exists_data_bonus) {
			if ($exists_data_bonus->completed_trips == '') $completed_trips = 1;
			else $completed_trips = $exists_data_bonus->completed_trips + 1;

			$updated_bonus = Bonus::find($exists_data_bonus->id);
			$updated_bonus->completed_trips = $completed_trips;

			if ($updated_bonus->save()) {

				if ($completed_trips == $exists_data_bonus->number_of_trips and $completed_trips > 0) {
					//self::transfer_to_balance($user, $updated_bonus);

					//reffered by 
					$referred_user = User::where('id', $exists_data_bonus->user_id)->first();
					self::transfer_to_balance($referred_user, $exists_data_bonus);

					//referral to
					$referral_data_bonus = Bonus::where('user_id', $user->id) //user_id > referral_to
						->where('user_type', $user->user_type)
						->where('referred_by', $referred_user->user_id)
						->where('bonus_type', 'Rider')
						->whereRaw('number_of_trips > completed_trips')
						->first();

					if ($referral_data_bonus) {
						if ($referral_data_bonus->completed_trips == '') $completed_trips2 = 1;
						else $completed_trips2 = $referral_data_bonus->completed_trips + 1;

						$updated_bonus2 = Bonus::find($referral_data_bonus->id);
						$updated_bonus2->completed_trips = $completed_trips2;

						if ($updated_bonus2->save()) {

							if ($completed_trips2 == $referral_data_bonus->number_of_trips and $completed_trips2 > 0) {

								self::transfer_to_balance($user, $referral_data_bonus);
							}
						}
					}

					return '1';
				}
			}
		}
	}



	public function rider_cashback1($user)
	{ // transfer to adjust_rider_referral_bonus
		$referral_cashback = ReferralSetting::RiderCashback1()->get()->pluck('value', 'name')->toArray();

		if ($referral_cashback['apply_referral'] == '1' and $referral_cashback['amount'] > 0 and $referral_cashback['start_time'] <= now() and $referral_cashback['end_time'] >= now()) {
			$bonus_cashback = new Bonus;
			$bonus_cashback->user_id = $user->id;
			$bonus_cashback->user_type = $user->user_type;
			$bonus_cashback->bonus_type = 'RiderCashback1';
			$bonus_cashback->number_of_trips = $referral_cashback['number_of_trips'];
			$bonus_cashback->currency_code = $referral_cashback['currency_code'];
			$bonus_cashback->bonus_amount = $referral_cashback['amount'];
			$bonus_cashback->withdrawal_method = $referral_cashback['withdrawal_method'];
			$bonus_cashback->terms_condition = $referral_cashback['terms_condition'];
			$bonus_cashback->unique_id = strtotime("now") . rand(10000, 100000);
			$bonus_cashback->bonus_date = date('Y-m-d');
			$bonus_cashback->save();
		}
	}

	public function adjust_rider_cashback1($user)
	{ // transfer to adjust_rider_referral_bonus
		$exists_data_bonus = Bonus::where('user_id', $user->id)
			->where('user_type', $user->user_type)
			->where('bonus_type', 'RiderCashback1')
			->whereRaw('number_of_trips > completed_trips')
			->first();

		if ($exists_data_bonus) {
			if ($exists_data_bonus->completed_trips == '') $completed_trips = 1;
			else $completed_trips = $exists_data_bonus->completed_trips + 1;

			$updated_bonus = Bonus::find($exists_data_bonus->id);
			$updated_bonus->completed_trips = $completed_trips;

			if ($updated_bonus->save()) {
				if ($completed_trips == $exists_data_bonus->number_of_trips) {
					self::transfer_to_balance_rider($user, $updated_bonus->toArray());
					self::rider_cashback2($user);
					return '1';
				}
			}
		}
	}

	public function rider_cashback2($user)
	{
		$referral_cashback = ReferralSetting::RiderCashback2()->get()->pluck('value', 'name')->toArray();

		if ($referral_cashback['apply_referral'] == '1' and $referral_cashback['amount'] > 0 and $referral_cashback['start_time'] <= now() and $referral_cashback['end_time'] >= now()) {

			$exist_bonus = Bonus::where('user_id', $user->id)->where('bonus_type', 'RiderCashback2')->first();
			if ($exist_bonus == '') {
				$bonus_cashback = new Bonus;
				$bonus_cashback->user_id = $user->id;
				$bonus_cashback->user_type = $user->user_type;
				$bonus_cashback->bonus_type = 'RiderCashback2';
				$bonus_cashback->number_of_trips = $referral_cashback['number_of_trips'];
				$bonus_cashback->currency_code = $referral_cashback['currency_code'];
				$bonus_cashback->bonus_amount = $referral_cashback['amount'];
				$bonus_cashback->withdrawal_method = $referral_cashback['withdrawal_method'];
				$bonus_cashback->terms_condition = $referral_cashback['terms_condition'];
				$bonus_cashback->unique_id = strtotime("now") . rand(10000, 100000);
				$bonus_cashback->bonus_date = date('Y-m-d');
				$bonus_cashback->save();
			}
		}
	}

	public function adjust_rider_cashback2($user)
	{ // transfer to adjust_rider_referral_bonus
		// $cashback_bonus2 = Bonus::where('user_id', $user->id)
		// 	->where('user_type', $user->user_type)
		// 	->where('bonus_type', 'RiderCashback2')
		// 	->where('status', 'Due')
		// 	->first();

		// if (is_object($cashback_bonus2)) {
		// 	$total_trips = Trips::where('user_id', $user->id)
		// 		->where('status', 'Completed')
		// 		->where('subtotal_fare', '>', 0)
		// 		->where('created_at', '>=', $cashback_bonus2->created_at)
		// 		->count();

		// 	if ($total_trips == $cashback_bonus2['number_of_trips']) { // - 1
		// 		self::transfer_to_balance_rider($user, $cashback_bonus2->toArray());
		// 	}
		// }



		$exists_data_bonus = Bonus::where('user_id', $user->id)
			->where('user_type', $user->user_type)
			->where('bonus_type', 'RiderCashback2')
			->whereRaw('number_of_trips > completed_trips')
			->first();

		if ($exists_data_bonus) {
			if ($exists_data_bonus->completed_trips == '') $completed_trips = 1;
			else $completed_trips = $exists_data_bonus->completed_trips + 1;

			$updated_bonus = Bonus::find($exists_data_bonus->id);
			$updated_bonus->completed_trips = $completed_trips;

			if ($updated_bonus->save()) {
				if ($completed_trips == $exists_data_bonus->number_of_trips) {
					self::transfer_to_balance_rider($user, $updated_bonus->toArray());
					return '1';
				}
			}
		}
	}

	function discount_offer_save($trip, $rider)
	{
		if ($trip->discount > 0) {
			$rider_discount_offer = ReferralSetting::RiderDiscountOffer1()->get()->pluck('value', 'name')->toArray();

			$rate_sign = "";
			if ($rider_discount_offer['rate_fixed'] == 'rate') $rate_sign = '%';

			$table = new DiscountOfferUsed;
			$table->user_type 		= $rider->user_type;
			$table->user_id 		= $rider->id;
			$table->trip_id 		= $trip->id;
			$table->type 			= 'RiderDiscountOffer1';
			$table->start_time 		= $rider_discount_offer['start_time'];
			$table->end_time 		= $rider_discount_offer['end_time'];
			$table->amount 			= $rider_discount_offer['amount'] . $rate_sign;
			$table->discount_amount = $trip->discount;
			$table->save();
		}
	}

	private function bonus_calculation($trips, $amount, $rider_discount_offer)
	{
		$offer_appliable = false;
		$discount_amount = 0;
		$user_id = $trips->user_id;
		$user = User::where('id', $user_id)->first();
		$invoice_amount = $amount;
		if ($rider_discount_offer['offer_for'] == 'our_employee') {
			if ($user->is_our_employee == '1') $offer_appliable = true;
		} else {
			$offer_appliable = true;
		}

		$rate_sign = "";

		if ($offer_appliable) {
			if ($rider_discount_offer['rate_fixed'] == 'rate') {
				$discount_amount =  ($amount * $rider_discount_offer['amount']) / 100;
				$invoice_amount = $amount - $discount_amount;
				$rate_sign = '%';
			} else if ($rider_discount_offer['rate_fixed'] == 'fixed') {
				if ($amount >= $rider_discount_offer['amount']) {
					$discount_amount =  $rider_discount_offer['amount'];
					$invoice_amount = $amount - $discount_amount;
				} else {
					$discount_amount =  $amount;
					$invoice_amount = 0;
				}
			}

			// $table = new DiscountOfferUsed;
			// $table->user_type 		= $user->user_type;
			// $table->user_id 			= $user_id;
			// $table->trip_id 			= $trips->id;
			// $table->type 			= 'RiderDiscountOffer1';
			// $table->start_time 		= $rider_discount_offer['start_time'];
			// $table->end_time 		= $rider_discount_offer['end_time'];
			// $table->amount 			= $rider_discount_offer['amount'].$rate_sign;
			// $table->discount_amount = $discount_amount;
			// $table->save();
		}

		$discount_amount = number_format($discount_amount, 2, '.', '');
		$invoice_amount = number_format($invoice_amount, 2, '.', '');
		return array('discount_amount' => $discount_amount, 'invoice_amount' => $invoice_amount);
	}

	public function rider_discount_offer1($amount, $trips)
	{
		$rider_discount_offer = ReferralSetting::RiderDiscountOffer1()->get()->pluck('value', 'name')->toArray();
		if (
			$rider_discount_offer['apply_referral'] == '1' and
			$rider_discount_offer['amount'] > 0 and
			$rider_discount_offer['start_time'] <= now() and
			$rider_discount_offer['end_time'] >= now()
		) {
			$user_id = $trips->user_id;

			$used = DiscountOfferUsed::where('user_id', $user_id)
				->where('start_time', $rider_discount_offer['start_time'])
				->where('end_time', $rider_discount_offer['end_time'])
				->count();

			if ($used < $rider_discount_offer['max_trip']) { // add offer
				return $this->bonus_calculation($trips, $amount, $rider_discount_offer);
			} else {
				$discount_amount 	= 0;
				$invoice_amount 	= $amount;
				$discount_amount 	= number_format($discount_amount, 2, '.', '');
				$invoice_amount 	= number_format($invoice_amount, 2, '.', '');
				return array('discount_amount' => $discount_amount, 'invoice_amount' => $invoice_amount);
			}
		}
	}

	public function end_trip_bonus_update_driver($user)
	{
		//self::adjust_driver_signup_bonus($user);
		self::adjust_driver_referral_bonus($user);
		self::adjust_driver_trip_bonus($user);
	}
	public function end_trip_bonus_update_rider($user)
	{
		self::adjust_rider_referral_bonus($user);
		$adjust_rider_cashback1 = self::adjust_rider_cashback1($user);
		if ($adjust_rider_cashback1 != '1') self::adjust_rider_cashback2($user);
	}



	public function bonus_status($user_id)
	{
		$user = User::where('id', $user_id)->first();
		$bonuses = Bonus::where('user_id', $user_id)
			->where('user_type', $user->user_type)
			->orderBy('id')
			->get();

		$rate = "";
		$total_required_trips = 0;
		$return = array();

		if ($user->user_type == 'Rider') {
			//$completed_trips = Trips::where('user_id', $user->id)->where('status', 'Completed')->count();
			foreach ($bonuses as $bonus) {
				$number_of_trips = $bonus->number_of_trips;
				$completed_trips = $bonus->completed_trips; //added

				//$total_required_trips+= $number_of_trips;

				if ($number_of_trips == $completed_trips) {
					$rate = 100;
				} else {
					$rate = number_format(($completed_trips / $number_of_trips) * 100, 0); // last $total_required_trips -> $number_of_trips
				}

				$return[] = array(
					'bonus_id' => $bonus->id,
					'status' => $bonus->status,
					'completed_trips' => $completed_trips,
					'number_of_trips' => $number_of_trips,
					'withdrawal_method' => $bonus->withdrawal_method,
					'created_at' => date("d M Y", strtotime($bonus->created_at)),
					'bonus_type' => preg_replace('/(?<!^)([A-Z])/', ' \\1', $bonus->bonus_type),
					'rate' => $rate,
					'terms' => $bonus->terms_condition,
				);
			}
		} else if ($user->user_type == 'Driver') {
			//$completed_trips = Trips::where('driver_id', $user->id)->where('status', 'Completed')->count();

			foreach ($bonuses as $bonus) {
				$number_of_trips = $bonus->number_of_trips;
				$completed_trips = $bonus->completed_trips; //added
				//$total_required_trips += $number_of_trips;

				if ($number_of_trips == $completed_trips) {
					$rate = 100;
				} else {
					$rate = number_format(($completed_trips / $number_of_trips) * 100, 0);
				}

				$return[] = array(
					'bonus_id' => $bonus->id,
					'status' => $bonus->status,
					'completed_trips' => $completed_trips,
					'number_of_trips' => $number_of_trips,
					'withdrawal_method' => $bonus->withdrawal_method,
					'created_at' => date("d M Y", strtotime($bonus->created_at)),
					'bonus_type' => preg_replace('/(?<!^)([A-Z])/', ' \\1', $bonus->bonus_type),
					'rate' => $rate,
					'terms' => $bonus->terms_condition,
				);
			}
		}
		return $return;
	}


	public function bonus_status_rider_v2($user, $bonus_type)	//, $bonus_type
	{
		$bonuses = Bonus::where('user_id', $user->id)
			->where('user_type', $user->user_type)
			->where('bonus_type', '!=', 'Rider');

		if ($bonus_type == 'bonus_list') {
			$bonuses = $bonuses->where('status', 'Due');
		} else {
			$bonuses = $bonuses->where('status', '!=', 'Due');
		}

		$bonuses = $bonuses->orderBy('id', 'DESC')->get();

		$array = array();

		foreach ($bonuses as $bonus) {
			$number_of_trips = $bonus->number_of_trips;
			$completed_trips = $bonus->completed_trips;

			if ($number_of_trips <= $completed_trips) {
				$rate = 100;
				$completed_trips = $number_of_trips;
			} else {
				$rate = number_format(($completed_trips / $number_of_trips) * 100, 0); // last $total_required_trips -> $number_of_trips
			}

			switch ($bonus->bonus_type) {
				case 'Rider':
					$bonus_type = "Referral Bonus";
					break;
				case 'RiderCashback1':
					$bonus_type = "First Cashback Bonus";
					break;
				case 'RiderCashback2':
					$bonus_type = "Second Cashback Bonus";
					break;
				default:
					$bonus_type = "";
					break;
			}

			$array[] = array(
				'bonus_id' => $bonus->id,
				'status' => $bonus->status,
				'completed_trips' => $completed_trips,
				'number_of_trips' => $number_of_trips,
				'withdrawal_method' => $bonus->withdrawal_method,
				'bonus_date' => date("d M Y", strtotime($bonus->bonus_date)),
				'bonus_type' => $bonus_type,
				'rate' => (int)$rate,
				'terms' => $bonus->terms_condition,
			);
		}
		return $array;
	}

	public function bonus_status_v2($user_id, $bonus_type, $page = 1, $history = '')
	{
		$take = 15;
		$skip = ($take * $page) - $take;

		$lastWeek = date("Y-m-d", strtotime("-7 days"));
		$yesterday = date("Y-m-d", strtotime("-1 days"));
		$array = array();


		$user = User::where('id', $user_id)->first();
		if ($user->user_type == 'Rider') {
			return self::bonus_status_rider_v2($user, $bonus_type); //, $bonus_type
		} else {
			$bonuses = Bonus::where('user_id', $user_id)
				->where('user_type', $user->user_type)
				->where('bonus_type', $bonus_type);

			if ($history != '') {
				if ($bonus_type == 'DriverTripBonus') { //weekly
					$bonuses = $bonuses->where('bonus_date', '<', $lastWeek);
				} else if ($bonus_type == 'DriverOnlineBonus' || $bonus_type == 'Driver') {
					$bonuses = $bonuses->where('bonus_date', '<', date('Y-m-d'));
				} else if ($bonus_type == 'DriverSignupBonus' or $bonus_type == 'DriverJoiningBonus') {
					$bonuses = $bonuses->where('status', '!=', 'Due');
				}
			} else {
				if ($bonus_type != '') {
					if ($bonus_type == 'DriverSignupBonus' or $bonus_type == 'DriverJoiningBonus') {
						//$bonuses = $bonuses->whereDate('bonus_date', '=', date('Y-m-d'));
						$bonuses = $bonuses->where('status', 'Due');
					} else if ($bonus_type == 'Driver'  or $bonus_type == 'DriverReferralBonus') { //  || $bonus_type == 'Rider'
						$bonuses = $bonuses->whereDate('bonus_date', '<=', date('Y-m-d'));
					}
					// else if ($bonus_type == 'RiderCashback1' || $bonus_type == 'RiderCashback2') {
					// 	$bonuses = $bonuses->whereDate('bonus_date', '<=', date('Y-m-d'));
					// } 
					else if ($bonus_type == 'DriverOnlineBonus') {
						$bonuses = $bonuses->whereDate('bonus_date', '=', date('Y-m-d'));
					} else if ($bonus_type == 'DriverTripBonus') {
						$bonuses = $bonuses->whereDate('bonus_date', '>=', $lastWeek);
					}
				}
			}
			$bonuses = $bonuses->skip($skip)
				->take($take)
				->orderBy('id', 'DESC')
				->get();
			$rate = 0;
			$total_required_trips = 0;
			$return = array();

			if ($user->user_type == 'Rider') {
				//$completed_trips = Trips::where('user_id', $user->id)->where('status', 'Completed')->count();
				foreach ($bonuses as $bonus) {
					$number_of_trips = $bonus->number_of_trips;
					$completed_trips = $bonus->completed_trips; //added

					//$total_required_trips+= $number_of_trips;

					if ($number_of_trips <= $completed_trips) {
						$rate = 100;
					} else {
						$rate = number_format(($completed_trips / $number_of_trips) * 100, 0); // last $total_required_trips -> $number_of_trips
					}

					switch ($bonus->bonus_type) {
							// case 'Rider':
							// 	$bonus_type = "Referral Bonus";
							// 	break;
						case 'RiderCashback1':
							$bonus_type = "First Cashback Bonus";
							break;
						case 'RiderCashback2':
							$bonus_type = "Second Cashback Bonus";
							break;
						default:
							$bonus_type = "";
							break;
					}


					$return[] = array(
						'bonus_id' => $bonus->id,
						'status' => $bonus->status,
						'completed_trips' => $completed_trips,
						'number_of_trips' => $number_of_trips,
						'withdrawal_method' => $bonus->withdrawal_method,
						'bonus_date' => date("d M Y", strtotime($bonus->bonus_date)),
						'bonus_type' => $bonus_type, //preg_replace('/(?<!^)([A-Z])/', ' \\1', $bonus->bonus_type),
						'rate' => (int)$rate,
						'terms' => $bonus->terms_condition,
					);
				}
			} else if ($user->user_type == 'Driver') {
				//$completed_trips = Trips::where('driver_id', $user->id)->where('status', 'Completed')->count();

				foreach ($bonuses as $bonus) {
					$number_of_trips = $bonus->number_of_trips;
					$completed_trips = $bonus->completed_trips; //added
					//$total_required_trips += $number_of_trips;

					if ($number_of_trips <= $completed_trips) {
						$rate = 100;
						$completed_trips = $number_of_trips;
					} else {
						$rate = number_format(($completed_trips / $number_of_trips) * 100, 0);
					}

					switch ($bonus->bonus_type) {
						case 'DriverSignupBonus':
							$bonus_type = "Signup Bonus";
							break;
						case 'DriverJoiningBonus':
							$bonus_type = "Joining Bonus";
							break;
							// case 'Driver':
							// 	$bonus_type = "Referral Bonus";
							// 	break;
							// case 'DriverReferralBonus':
							// 	$bonus_type = "Referral Bonus";
							// 	break;
						case 'DriverTripBonus':
							$bonus_type = "Weekly Bonus";
							break;
						case 'DriverOnlineBonus':
							$bonus_type = "Daily Bonus";
							break;
						default:
							$bonus_type = "";
							break;
					}

					$array = array(
						'bonus_id' => $bonus->id,
						'bonus_type' => $bonus_type, //preg_replace('/(?<!^)([A-Z])/', ' \\1', $bonus->bonus_type),	
						'terms' => $bonus->terms_condition,

						'completed' => $completed_trips,
						'required' => $number_of_trips,
						'completed_rate' => (int)$rate,
						'title' => 'Trips',
					);

					if ($bonus->bonus_type == 'DriverOnlineBonus') { //pending
						$peak_hour = (float)$bonus->peak_hour;
						$completed_peak_hour = (float)$bonus->completed_peak_hour;
						$min_hour = (float)$bonus->min_hour;
						$completed_min_hour = (float)$bonus->completed_min_hour;

						if ($completed_peak_hour > 0 and  $peak_hour <= $completed_peak_hour) {
							$peak_rate = 100;
							$completed_peak_hour = $peak_hour;
						} else {
							if ($peak_hour > 0) $peak_rate = number_format(($completed_peak_hour / $peak_hour) * 100, 0);
							else $peak_rate = 0;
						}

						if ($completed_min_hour > 0 and $min_hour <= $completed_min_hour) {
							$online_rate = 100;
							$completed_min_hour = $min_hour;
						} else {
							if ($min_hour > 0) $online_rate = number_format(($completed_min_hour / $min_hour) * 100, 0);
							else $online_rate = 0;
						}

						$peak_hour_data = PeakHour::where('day_name', date('l'))->get();

						$peak_hour_details = '';
						foreach ($peak_hour_data as $peakData) {
							$peak_hour_details .= date("h a", strtotime($peakData->start_time));
							$peak_hour_details .= " to " . date("h a", strtotime($peakData->end_time));
							$peak_hour_details .= " and ";
						}
						$peak_hour_details = rtrim($peak_hour_details, ' and ');
						//$array

						$array1 =  array(
							'bonus_id' => $bonus->id,
							'bonus_date' => date("d M Y", strtotime($bonus->bonus_date)),
							'bonus_type' => $bonus_type, //preg_replace('/(?<!^)([A-Z])/', ' \\1', $bonus->bonus_type),	
							'bonus_time' => '12:00 AM - 11:59 PM',
							'terms' => $bonus->terms_condition,

							'completed' => $completed_trips,
							'required' => $number_of_trips,
							'completed_rate' => (int)$rate,
							'title' => 'Trips',
							'for' => 'Completed Trips'
						);

						$array2 =  array(
							'bonus_id' => $bonus->id,
							'bonus_date' => "",
							'bonus_type' => "",
							'bonus_time' => $peak_hour_details,
							'terms' => $bonus->terms_condition,

							'completed' => $completed_peak_hour,
							'required' => $peak_hour,
							'completed_rate' => (int)$peak_rate,
							'title' => 'Hours',
							'for' => 'Stay Online (Only Peak)'
						);

						$array3 = array(
							'bonus_id' => $bonus->id,
							'bonus_date' => "",
							'bonus_type' => "",
							'bonus_time' => "12:00 AM - 11:59 PM (Without Peak Hour)",
							'terms' => $bonus->terms_condition,

							'completed' => $completed_min_hour,
							'required' => $min_hour,
							'completed_rate' => (int)$online_rate,
							'title' => 'Hours',
							'for' => 'Stay Online (Without Peak)',
						);
					} else if ($bonus->bonus_type == 'DriverTripBonus') {
						$array = array_merge(
							$array,
							array(
								'bonus_date' =>  date("d M Y", strtotime($bonus->start_date)) . " - " . date("d M Y", strtotime($bonus->end_date)),
								'bonus_time' => "",
								'for' => 'Weekly',
							)
						);
					} else if ($bonus->bonus_type == 'DriverSignupBonus' or $bonus->bonus_type == 'DriverJoiningBonus') {
						$array = array_merge(
							$array,
							array(
								'bonus_date' =>  "",
								'bonus_time' => "",
								'for' => 'Signup',
							)
						);
					}
					// else if ($bonus->bonus_type == 'Driver' or $bonus->bonus_type == 'Rider') {
					// 	if ($bonus->referral_to != '') {
					// 		$user = User::where('id', $bonus->referral_to)->first();
					// 		$array = array_merge(
					// 			$array,
					// 			array(
					// 				'referral' => $user->first_name . ' ' . $user->last_name
					// 			)
					// 		);
					// 	}
					// }


					if ($bonus->bonus_type == 'DriverOnlineBonus') {
						if (isset($array1) and $array1 != '') $return[] = $array1;
						if (isset($array2) and $array2 != '') $return[] = $array2;
						if (isset($array3) and $array3 != '') $return[] = $array3;
					} else {
						if (isset($array) and $array != '') $return[] = $array;
					}





					//$return[] = $array;
				}
			}

			return $return;	//response()->json($return);
		}
	}




	public function bonus_status_v3($user_id, $bonus_type, $page = 1)
	{
		$take = 15;
		$skip = ($take * $page) - $take;
		$lastWeek = date("Y-m-d", strtotime("-7 days"));
		$yesterday = date("Y-m-d", strtotime("-1 days"));
		$array = array();
		$user = User::where('id', $user_id)->first();
		// echo $user_id;
		// exit;
		// echo $bonus_type;
		// exit;
		$bonuses = Bonus::where('user_id', $user_id)
			->where('user_type', $user->user_type)
			->where('bonus_type', $bonus_type)
			->skip($skip)
			->take($take)
			->orderBy('id', 'DESC')
			->get();

		$rate = 0;
		$return = array();
		if ($bonus_type == 'DriverOnlineBonus') {
			self::update_online_bonus_runtime($user_id);
		}

		foreach ($bonuses as $bonus) {
			$number_of_trips = $bonus->number_of_trips;
			$completed_trips = $bonus->completed_trips; 
			$curent_date= date("d M Y");
			$bonus_date=date("d M Y", strtotime($bonus->bonus_date));
			$is_current_date="false";
			if($curent_date == $bonus_date){$is_current_date= "true";}
			
			if ($number_of_trips <= $completed_trips) {
				$rate = 100;
				$completed_trips = $number_of_trips;
			} else {
				$rate = number_format(($completed_trips / $number_of_trips) * 100, 0);
			}

			
			if ($bonus->bonus_type == 'DriverOnlineBonus') { 
				$peak_hour = (float)$bonus->peak_hour;
				$completed_peak_hour = (float)$bonus->completed_peak_hour;
				
				if ($completed_peak_hour > 0 and  $peak_hour <= $completed_peak_hour) {
					$peak_rate = 100;
					$completed_peak_hour = $peak_hour;
				} else {
					if ($peak_hour > 0) $peak_rate = number_format(($completed_peak_hour / $peak_hour) * 100, 0);
					else $peak_rate = 0;
				}
				$peak_hour_data = PeakHour::where('day_name', date('l'))->get();
				$peak_hour_time=array();
				foreach ($peak_hour_data as $index => $peakData) {
					$index++;
					$peak_hour_time['slot'.$index]  = date("h a", strtotime($peakData->start_time)) . " to " . date("h a", strtotime($peakData->end_time)); 
				}

				$your_progress = array();
				$earning=0;
				if ($peak_rate ==100 AND $rate>=80){
					$earning=$bonus->bonus_amount;
				}

				$your_progress[] =  array(
					'title' => "Online Hour",
					'progress'=>"$completed_peak_hour"."/"."$peak_hour",
	       			'progress_bar_status'=>(int)$peak_rate,
               		'is_progress_bar'=>"true"
				
				);
				$your_progress[] =  array(
					'title' => "Completation Rate",
					//'progress'=>"$rate"." % of "."$bonus->trip_complete_percent". " % ",
					'progress'=>"$rate"." %",
	       			'progress_bar_status'=>0,
               		'is_progress_bar'=>"false"
				
				);

				$your_progress[] =  array(
					'title' => "Your Earning",
					'progress'=>"$earning",
	       			'progress_bar_status'=>0,
               		'is_progress_bar'=>"false"
				
				);
		
				$bonus_array =  array(
					'bonus_id' => $bonus->id,
					'bonus_date' => $bonus_date,
					'is_current_date' => $is_current_date,	
					'bonus_time' => (object) $peak_hour_time,
					'terms' => $bonus->terms_condition,
					"your_progress" =>$your_progress	
				);
			} 

			if (isset($bonus_array) and $bonus_array != '') $return[] = $bonus_array;
			
		}
	

		return $return;	
		
	}






	//DriverJoiningBonus

	public function driver_joining_bonus($user) //checked
	{
		$referralSetting = ReferralSetting::DriverJoiningBonus()->get()->pluck('value', 'name')->toArray();

		if ($referralSetting['apply_referral'] == '1' and $referralSetting['amount'] > 0 and $referralSetting['start_time'] <= now() and $referralSetting['end_time'] >= now()) {

			$exists_data_bonus = Bonus::where('user_id', $user->id)
				->where('user_type', $user->user_type)
				->where('bonus_type', 'DriverJoiningBonus')
				->first();

			if (!$exists_data_bonus) {
				$bonus = new Bonus;
				$bonus->user_id = $user->id;
				$bonus->user_type = $user->user_type;
				$bonus->bonus_type = 'DriverJoiningBonus';
				$bonus->number_of_trips = $referralSetting['number_of_trips'];
				$bonus->number_of_days = $referralSetting['number_of_days'];
				$bonus->payment_after_days = $referralSetting['payment_after_days'];
				$bonus->allow_same_user = $referralSetting['allow_same_user'];
				$bonus->trip_distance = $referralSetting['trip_distance'];
				$bonus->currency_code = $referralSetting['currency_code'];
				$bonus->bonus_amount = $referralSetting['amount'];
				$bonus->terms_condition = $referralSetting['terms_condition'];
				$bonus->withdrawal_method = $referralSetting['withdrawal_method'];
				$bonus->create_method = 'driver_joining_bonus';
				$bonus->bonus_date = date('Y-m-d');
				$bonus->unique_id = $user->id . "_DriverJoiningBonus";

				try {
					$bonus->save();
				} catch (\Exception $th) {
					//throw $th;
				}
			}
		}
		CustomLog::info("driver_signup_bonus");
	}


	public function driver_referral_bonus_new($user) //checked
	{
		// previous using at App\Providers\StartServiceProvider
		$referralSetting = ReferralSetting::DriverReferralBonus()->get()->pluck('value', 'name')->toArray();
		$today = date('Y-m-d');

		if ($referralSetting['apply_referral'] == '1' and $referralSetting['amount'] > 0 and $referralSetting['start_time'] <= now() and $referralSetting['end_time'] >= now()) {

			if ($user->used_referral_code != '' and $user->used_referral_code != NULL) {
				$referrer_id = User::where('referral_code', $user->used_referral_code)->pluck('id')->first();

				if ($referrer_id != '' and $referrer_id != 0) {

					$exists_data_bonus = Bonus::where('user_id', $referrer_id)
						->where('user_type', $user->user_type)
						->where('bonus_type', 'DriverReferralBonus')
						->where('referral_to', $user->id)
						->first();

					if (!is_object($exists_data_bonus)) {
						$bonus_referrer = new Bonus;
						$bonus_referrer->user_id = $referrer_id;
						$bonus_referrer->referral_to = $user->id;
						$bonus_referrer->user_type = $user->user_type;
						$bonus_referrer->bonus_type = 'DriverReferralBonus';
						$bonus_referrer->who_get_bonus = $referralSetting['who_get_bonus'];
						$bonus_referrer->number_of_days = $referralSetting['number_of_days'];
						$bonus_referrer->number_of_trips = $referralSetting['number_of_trips'];
						$bonus_referrer->currency_code = $referralSetting['currency_code'];
						$bonus_referrer->bonus_amount = $referralSetting['amount'];
						$bonus_referrer->terms_condition = $referralSetting['terms_condition'];
						$bonus_referrer->payment_after_days = $referralSetting['payment_after_days'];
						$bonus_referrer->withdrawal_method = $referralSetting['withdrawal_method'];
						$bonus_referrer->unique_id = $referrer_id . "_" . $user->id . "_DriverReferralBonus";
						$bonus_referrer->bonus_date = $today;
						$bonus_referrer->create_method = 'driver_referral_bonus_new';

						try {
							$bonus_referrer->save();
						} catch (\Exception $th) {
							//throw $th;
						}
					}
				}
			}
		}
	}

	public function update_bonus($driver, $bonus_type, $trips)
	{
		$today = date('Y-m-d');
		if ($bonus_type == "DriverJoiningBonus") {

			$exists_bonus = Bonus::where('user_id', $driver->id)
				->where('user_type', $driver->user_type)
				->where('bonus_type', $bonus_type)
				->whereRaw('number_of_trips > completed_trips')
				->where('status', 'Due')
				->first();

			if (is_object($exists_bonus)) {
				//$active_date = date('Y-m-d', strtotime($driver->active_time. ' + '.$exists_bonus->number_of_days.' days'));
				//if(strtotime($today) > strtotime($active_date)){
				$trip_distance = $exists_bonus->trip_distance;
				$allow_same_user = $exists_bonus->allow_same_user;
				$total_km = $trips->total_km;

				if ($total_km >= $trip_distance) {
					$rider_id = $trips->user_id;
					if($allow_same_user == '0'){
						$total_trips = Trips::where('user_id', $rider_id)
							->where('driver_id', $driver->id)
							->whereDate('end_trip', $today)
							->where('id', '!=', $trips->id)
							->count();

						if ($total_trips == 0) {
							if ($exists_bonus->completed_trips == '') $completed_trips = 1;
							else $completed_trips = $exists_bonus->completed_trips + 1;

							$updated_bonus = Bonus::find($exists_bonus->id);
							$updated_bonus->completed_trips = $completed_trips;
							$updated_bonus->save();
						}
					}else{
						if ($exists_bonus->completed_trips == '') $completed_trips = 1;
						else $completed_trips = $exists_bonus->completed_trips + 1;

						$updated_bonus = Bonus::find($exists_bonus->id);
						$updated_bonus->completed_trips = $completed_trips;
						$updated_bonus->save();
					}
				} // if($total_km

				//} // if(strtotime($today) 
			}
		} elseif ($bonus_type == "DriverReferralBonus") {
			$exists_bonus = Bonus::where('referral_to', $driver->id)
								->where('user_type', $driver->user_type)
								->where('bonus_type', $bonus_type)
								->whereRaw('number_of_trips > completed_trips')
								->where('status', 'Due')
								->first();

			if (is_object($exists_bonus)) {
				//$active_date = date('Y-m-d', strtotime($driver->active_time. ' + '.$exists_bonus->number_of_days.' days'));
				//if(strtotime($today) > strtotime($active_date)){
				if ($exists_bonus->completed_trips == '') $completed_trips = 1;
				else $completed_trips = $exists_bonus->completed_trips + 1;

				$updated_bonus = Bonus::find($exists_bonus->id);
				$updated_bonus->completed_trips = $completed_trips;
				$updated_bonus->save();

				//} // if(strtotime($today) 
			}
		}
		elseif ($bonus_type == "DriverTripBonus") {
			$exists_bonus = Bonus::where('user_id', $driver->id)
								->where('user_type', $driver->user_type)
								->where('bonus_type', $bonus_type)
								->where('status', 'Due')
								->orderBy('id', 'DESC')
								->first();
			if(is_object($exists_bonus)){
				$allow_same_user = $exists_bonus->allow_same_user;
				$trip_distance = $exists_bonus->trip_distance;
				$total_km = $trips->total_km;

				if ($total_km >= $trip_distance) {
					if($allow_same_user == '0'){
						$rider_id = $trips->user_id;

						$total_trips = Trips::where('user_id', $rider_id)
												->where('driver_id', $driver->id)
												->whereDate('end_trip', $today)
												->where('id', '!=', $trips->id)
												->count();

						if ($total_trips == 0) {
							if ($exists_bonus->completed_trips == '') $completed_trips = 1;
							else $completed_trips = $exists_bonus->completed_trips + 1;

							$updated_bonus = Bonus::find($exists_bonus->id);
							$updated_bonus->completed_trips = $completed_trips;
							$updated_bonus->save();
						}
					}else{
						if ($exists_bonus->completed_trips == '') $completed_trips = 1;
						else $completed_trips = $exists_bonus->completed_trips + 1;

						$updated_bonus = Bonus::find($exists_bonus->id);
						$updated_bonus->completed_trips = $completed_trips;
						$updated_bonus->save();
					}
					
				} // if($total_km
			}
		}		
	}

	public function add_online_bonus_v2($driver_id)
	{ 
		$today = date('Y-m-d');	
		$referralSetting = ReferralSetting::DriverOnlineBonus()->get()->pluck('value', 'name')->toArray();

		if ($referralSetting['apply_referral'] == '1' and 
			$referralSetting['amount'] > 0 and 
			$referralSetting['start_time'] <= now() and 
			$referralSetting['end_time'] >= now()
			) {		

			$user_bonus_exist = Bonus::where('user_id', $driver_id)
									->where('bonus_type', 'DriverOnlineBonus')
									->where('bonus_date', $today)
									->first();

			if (!is_object($user_bonus_exist)) { // add
				$online_bonus = new Bonus;
				$online_bonus->user_id = $driver_id;
				$online_bonus->user_type = 'Driver';
				$online_bonus->bonus_type = 'DriverOnlineBonus';
				$online_bonus->peak_hour = $referralSetting['peak_hour'];
				$online_bonus->min_hour = $referralSetting['min_hour'];
				$online_bonus->number_of_trips = $referralSetting['min_trip'];
				$online_bonus->currency_code = $referralSetting['currency_code'];
				$online_bonus->bonus_amount = $referralSetting['amount'];
				$online_bonus->terms_condition = $referralSetting['terms_condition'];
				$online_bonus->withdrawal_method = $referralSetting['withdrawal_method'];
				$online_bonus->trip_complete_percent = $referralSetting['trip_complete_percent'];
				$online_bonus->number_of_days = '1';
				$online_bonus->completed_trips = 0;
				$online_bonus->completed_peak_hour = 0;
				$online_bonus->completed_min_hour = 0;
				$online_bonus->bonus_date = $today;
				$online_bonus->unique_id = $today . "_" . $driver_id . "_OnlineBonus";
				$online_bonus->create_method = 'BonusHelper::add_online_bonus_v2';
				try {
					$online_bonus->save();
				} catch (\Exception $e) {
					return false;
				}
			}
		}
	}

	public function adjust_driver_online_bonus_v2($driver_id)  //2
	{ // by Cron setup
		$online_date = date('Y-m-d', strtotime("-1 days"));
		$bonus = Bonus::where('user_type', 'Driver')
					->where('bonus_type', 'DriverOnlineBonus')
					->where('user_id', $driver_id)
					->where('bonus_date', $online_date)
					->where('status', '!=', 'Paid')
					->first();

		if (is_object($bonus)) {
			$trip_complete_percent = $bonus->trip_complete_percent;

			$total_request_trip = DB::table('request')
										->where('driver_id', $driver_id)
										->whereDate('created_at', '=', $online_date)
										->count();

			$completed_trip = DB::table('trips')
									->where('driver_id', $driver_id)
									->whereDate('created_at', '=', $online_date)
									->where('status', 'Completed')
									->count();
			if($completed_trip > 0) $percent = 	($total_request_trip / $completed_trip)	* 100;	
			else $percent = 0;
			////////////

			$activity = Activity::whereDate('created_at', '=', $online_date)
									->where('user_id', $driver_id)
									->select(
										'user_id',
										DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as online_date"),
										DB::raw("SUM(TIME_TO_SEC(TIMEDIFF(updated_at, created_at))) AS online_time"),
										DB::raw("SUM(TIME_TO_SEC(TIMEDIFF(peak_time_updated_at, peak_time_created_at))) AS peak_time")
									)
									->first();			

			$user_exist = DriverOnlineTime::where('driver_id', $driver_id)
									->where('online_date', $online_date)
									->first();		

			//these lines auto ad user to online
			if (is_object($user_exist)) { //if exist	
				$table = DriverOnlineTime::findOrFail($user_exist->id);
				$table->online_time = $activity->online_time ?? 0;
				$table->peak_time = $activity->peak_time ?? 0;
				$table->save();
			} else {
				$table = new DriverOnlineTime;
				$table->driver_id = $driver_id;
				$table->online_date = $online_date;
				$table->online_time = $activity->online_time ?? 0;
				$table->peak_time = $activity->peak_time ?? 0;
				$table->save();
			}

			$online_time_hour = number_format($table->online_time / (60 * 60), 2, '.', '');
			$peak_time_hour = number_format($table->peak_time / (60 * 60), 2, '.', '');			

			if ($percent >= $trip_complete_percent AND $bonus->completed_peak_hour >= $bonus->peak_hour){				
				$bonus->number_of_trips = $total_request_trip;
				$bonus->completed_trips = $completed_trip;
				$bonus->completed_peak_hour = $peak_time_hour;
				$bonus->completed_min_hour = $online_time_hour;
				$bonus->save();
				$user = DB::table('users')->where('id', $driver_id)->first();
				self::transfer_to_balance($user, $bonus);
			}
			else {
				$bonus->number_of_trips = $total_request_trip;
				$bonus->completed_trips = $completed_trip;
				$bonus->completed_peak_hour = $peak_time_hour;
				$bonus->completed_min_hour = $online_time_hour;
				$bonus->status = 'Cancel';
				$bonus->save();
			}
		}
	}


	public function update_online_bonus_runtime($driver_id)
	{ 
		$today = date('Y-m-d');

		$activity = Activity::whereDate('created_at', '=', $today)
								 ->where('user_id', $driver_id)
								 ->select(
								 	'user_id',
									DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as online_date"),
									DB::raw("SUM(TIME_TO_SEC(TIMEDIFF(updated_at, created_at))) AS online_time"),
									DB::raw("SUM(TIME_TO_SEC(TIMEDIFF(peak_time_updated_at, peak_time_created_at))) AS peak_time")
								 )
								->first();

		$user_exist = DriverOnlineTime::where('driver_id', $driver_id)
								->where('online_date', $today)
								->first();					


		//these lines auto ad user to online
		if (is_object($user_exist)) { //if exist	
			$table = DriverOnlineTime::findOrFail($user_exist->id);
			$table->online_time = $activity->online_time ?? 0;
			$table->peak_time = $activity->peak_time ?? 0;
			$table->save();
		} else {
			$table = new DriverOnlineTime;
			$table->driver_id = $driver_id;
			$table->online_date = $today;
			$table->online_time = $activity->online_time ?? 0;
			$table->peak_time = $activity->peak_time ?? 0;
			$table->save();
		}

		$total_request_trip = DB::table('request')
										->where('driver_id', $driver_id)
										->whereDate('created_at', '=', $today)
										->count();

		$total_trips = Trips::where('driver_id', $driver_id)
							->where('status', 'Completed')
							->where('subtotal_fare', '>', 0)
							->whereDate('created_at', '=', $today)
							->count();

		$user_bonus_exist = Bonus::where('user_type', 'Driver')
									->where('bonus_type', 'DriverOnlineBonus')
									->where('user_id', $driver_id)
									->where('bonus_date', $today)
									->first();

		$online_time_hour = number_format($table->online_time / (60 * 60), 2, '.', '');
		$peak_time_hour = number_format($table->peak_time / (60 * 60), 2, '.', '');

		$referralSetting = ReferralSetting::DriverOnlineBonus()->get()->pluck('value', 'name')->toArray();

		if ($referralSetting['apply_referral'] == '1' and 
			$referralSetting['amount'] > 0 and 
			$referralSetting['start_time'] <= now() and 
			$referralSetting['end_time'] >= now()
			) {
			if (is_object($user_bonus_exist)) { //update
				$bonus_update = Bonus::findOrFail($user_bonus_exist->id);				
				$bonus_update->number_of_trips = $total_request_trip;
				$bonus_update->completed_trips = $total_trips;
				$bonus_update->completed_peak_hour = $peak_time_hour;
				$bonus_update->completed_min_hour = $online_time_hour;
				$bonus_update->save();
			} else{ //add new
				self::add_online_bonus_v2($driver_id);
			}
		}
	}

	
}
