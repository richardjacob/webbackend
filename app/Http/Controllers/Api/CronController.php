<?php

/**
 * Cron Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Cron
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ScheduleRide;
use App\Models\PeakFareDetail;
use App\Models\User;
use App\Models\ReferralUser;
use App\Models\DriverLocation;
use App\Models\Currency;
use App\Models\Trips;
use App\Models\Payment;
use App\Models\Activity;
use App\Models\DriverOnlineTime;
use App\Models\ReferralSetting;
use App\Models\Bonus;
use App\Models\DriverDocuments;
use App\Models\PasswordResets; //temp
use App\Models\DriverBalance;

use App\Models\VehicleCity;
use App\Models\VehicleRegistrationLetter;
use App\Models\VehicleClass;
use App\Models\PeakHour;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
//Added By Nishat
use Stichoza\GoogleTranslate\GoogleTranslate;
use SoapClient;
use DB;
//Added By Nishat End
use App\Http\Controllers\CustomLog;


class CronController extends Controller
{
	public function __construct()
	{
		DB::enableQueryLog();
		$this->request_helper = resolve('App\Http\Helper\RequestHelper');
		$this->bonus_helper = resolve('App\Http\Helper\BonusHelper');
		$this->sms_helper = resolve('App\Http\Helper\SmsHelper');
	}

	public function test()
	{
		self::setOnlineUsersActivity();
	}

	public static function pick_time()
	{
		$peak_hour = PeakHour::where('day_name', date('l'))
								->where('status', '1')
								->select('start_time', 'end_time')
								->orderBy('type', 'DESC')
								->get();

		$start_time = array();
		$end_time = array();
		foreach ($peak_hour as $data) {
			$start_time[] = $data->start_time;
			$end_time[] = $data->end_time;
		}
		return array('start_time' => $start_time, 'end_time' => $end_time);
	}


	/**
	 * Cron request to cars for scheduled ride
	 * @param
	 * @return Response Json
	 */
	public function requestCars()
	{
		Log::info("request_cars(CronController) Stp:1 :");
		// before 5 min from schedule time
		$ride = ScheduleRide::where('status', 'Pending')->get();

		if ($ride->count() == 0) {
			return '';
		}
		//	$sms_gateway = resolve("App\Contracts\SMSInterface");

		/*Added By Nishat*/
		// $Onnorokom_api_key_get = DB::table('api_credentials')
		// 	->where('name', 'token')
		// 	->where('site', 'Onnorokom')
		// 	->first()->value;
		/*Added By Nishat End*/

		foreach ($ride as $request_val) {
			if ($request_val->timezone) {
				date_default_timezone_set($request_val->timezone);
			}

			$current_date = date('Y-m-d');
			$current_time = date('H:i');

			if (strtotime($request_val->schedule_date) == strtotime($current_date) && strtotime($request_val->schedule_time) == (strtotime($current_time) + 300)) {
				$additional_fare = "";
				$peak_price = 0;

				if (isset($request_val->peak_id) != '') {
					$fare = PeakFareDetail::find($request_val->peak_id);
					if ($fare) {
						$peak_price = $fare->price;
						$additional_fare = "Peak";
					}
				}

				$schedule_id = $request_val->id;
				$payment_mode = $request_val->payment_method;
				$is_wallet = $request_val->is_wallet;

				$data = [
					'rider_id' => $request_val->user_id,
					'pickup_latitude' => $request_val->pickup_latitude,
					'pickup_longitude' => $request_val->pickup_longitude,
					'drop_latitude' => $request_val->drop_latitude,
					'drop_longitude' => $request_val->drop_longitude,
					'user_type' => 'rider',
					'car_id' => $request_val->car_id,
					'driver_group_id' => null,
					'pickup_location' => $request_val->pickup_location,
					'drop_location' => $request_val->drop_location,
					'payment_method' => $payment_mode,
					'is_wallet' => $is_wallet,
					'timezone' => $request_val->timezone,
					'schedule_id' => $schedule_id,
					'additional_fare'  => $additional_fare,
					'location_id' => $request_val->location_id,
					'peak_price'  => $peak_price,
					'booking_type'  => $request_val->booking_type,
					'driver_id'  => $request_val->driver_id,
				];
				if ($request_val->driver_id == 0) {
					$car_details = $this->request_helper->find_driver($data);
				} else {
					$car_details = $this->request_helper->trip_assign($data);
				}
			} elseif (strtotime($request_val->schedule_date . ' ' . $request_val->schedule_time) == strtotime(date('Y-m-d H:i')) + 1800) {
				$rider = User::find($request_val->user_id);
				if ($request_val->booking_type == 'Manual Booking' && $request_val->driver_id != 0) {
					$driver_details = User::find($request_val->driver_id);
					$push_data['push_title'] = __('messages.api.schedule_remainder');
					$push_data['data'] = array(
						'manual_booking_trip_reminder' => array(
							'date' 	=> $request_val->schedule_date,
							'time'	=> $request_val->schedule_time,
							'pickup_location' 		=> $request_val->pickup_location,
							'pickup_latitude' 		=> $request_val->pickup_latitude,
							'pickup_longitude' 		=> $request_val->pickup_longitude,
							'rider_first_name'		=> $rider->first_name,
							'rider_last_name'		=> $rider->last_name,
							'rider_mobile_number'	=> $rider->mobile_number,
							'rider_country_code'	=> $rider->country_code
						)
					);

					$this->request_helper->SendPushNotification($rider, $push_data);

					$text = trans('messages.trip_booked_driver_remainder', ['date' => $request_val->schedule_date . ' ' . $request_val->schedule_time, 'pickup_location' => $request_val->pickup_location, 'drop_location' => $request_val->drop_location]);

					$text = GoogleTranslate::trans($text, 'bn');
					$to = '0' . $driver_details->phone_number;

					try {
						// $soapClient = new SoapClient("https://api2.onnorokomSMS.com/sendSMS.asmx?wsdl");
						// $paramArray = array(
						// 	'apiKey' =>  $Onnorokom_api_key_get,
						// 	'messageText' => $text,
						// 	'numberList' => $to,
						// 	'smsType' => "TEXT",
						// 	'maskName' => '',
						// 	'campaignName' => '',
						// );
						// $value = $soapClient->__call("NumberSms", array($paramArray));
						// if (explode('||', $value->NumberSmsResult)[0] == 1900) {
						// 	return ['status_code' => '1', 'status_message' => 'Success'];
						// } else {
						// 	return ['status_code' => '2', 'status_message' => 'Error'];
						// }

						// $sms_result = $this->sms_helper->send($to, $text);
						// if ($sms_result['0'] == 0) {
						// 	return ['status_code' => '1', 'status_message' => 'Success'];
						// } else {
						// 	return ['status_code' => '2', 'status_message' => 'Error'];
						// }

						$sms_result = $this->sms_helper->send($to, $text);
						$sms_result =  json_decode($sms_result, true);
						if ($sms_result['Status'] == 0) {
							$sms_check = $this->sms_helper->send_message_check($sms_result['Message_ID']);
							$message_result =  json_decode($sms_check, true);
							//if ($message_result['Status'] == 0) {
							if ($message_result['Status'] == 0 || $message_result['Status'] == 2 || $message_result['Status'] == 4) {
								return ['status_code' => '1', 'status_message' => 'Success'];
							} else {
								return ['status_code' => '2', 'status_message' => 'Error'];
							}
						} else {
							return ['status_code' => '2', 'status_message' => 'Error'];
						}
					} catch (\Exception $e) {
						// return response()->json([
						// 	'status_code'    => '2',
						// 	'status_message' => $e->getMessage(),
						// ]);
						return ['status_code' => '2', 'status_message' => 'Error'];
					}


					/*Commented By Nishat*/

					//   $to = $driver_details->phone_number;
					//   $sms_responce = $sms_gateway->send($to,$text);

					/*Comment End By Nishat*/
				}

				//booking message to user
				$text = trans('messages.trip_booked_user_remainder', ['date' => $request_val->schedule_date . ' ' . $request_val->schedule_time]);
				if ($request_val->booking_type == 'Manual Booking' && $request_val->driver_id != 0) {
					$driver = User::find($request_val->driver_id);
					$text = $text . trans('messages.trip_booked_driver_detail', ['first_name' => $driver->first_name, 'phone_number' => $driver->mobile_number]);
					$text = $text . trans('messages.trip_booked_vehicle_detail', ['name' => $driver->driver_documents->vehicle_name, 'number' => $driver->driver_documents->vehicle_number]);
				}

				$to = '0' . $rider->phone_number;
				$text = GoogleTranslate::trans($text, 'bn');

				CustomLog::info("request_cars(CronController) Stp:2 :");

				try {
					// $soapClient = new SoapClient("https://api2.onnorokomSMS.com/sendSMS.asmx?wsdl");
					// $paramArray = array(
					// 	'apiKey' =>  $Onnorokom_api_key_get,
					// 	'messageText' => $text,
					// 	'numberList' => $to,
					// 	'smsType' => "TEXT",
					// 	'maskName' => '',
					// 	'campaignName' => '',
					// );
					// $value = $soapClient->__call("NumberSms", array($paramArray));
					// if (explode('||', $value->NumberSmsResult)[0] == 1900) {
					// 	return ['status_code' => '1', 'status_message' => 'Success'];
					// } else {
					// 	return ['status_code' => '2', 'status_message' => 'Error'];
					// }

					// $sms_result = $this->sms_helper->send($to, $text);
					// if ($sms_result['0'] == 0) {
					// 	return ['status_code' => '1', 'status_message' => 'Success'];
					// } else {
					// 	return ['status_code' => '2', 'status_message' => 'Error'];
					// }

					$sms_result = $this->sms_helper->send($to, $text);
					$sms_result =  json_decode($sms_result, true);
					if ($sms_result['Status'] == 0) {
						$sms_check = $this->sms_helper->send_message_check($sms_result['Message_ID']);
						$message_result =  json_decode($sms_check, true);
						//if ($message_result['Status'] == 0) {
						if ($message_result['Status'] == 0 || $message_result['Status'] == 2 || $message_result['Status'] == 4) {
							return ['status_code' => '1', 'status_message' => 'Success'];
						} else {
							return ['status_code' => '2', 'status_message' => 'Error'];
						}
					} else {
						return ['status_code' => '2', 'status_message' => 'Error'];
					}
				} catch (\Exception $e) {
					// return response()->json([
					// 	'status_code'    => '2',
					// 	'status_message' => $e->getMessage(),
					// ]);
					return ['status_code' => '2', 'status_message' => 'Error'];
				}




				/*Commented By Nishat*/
				// $to = $rider->phone_number;
				// $sms_responce = $sms_gateway->send($to,$text);
				/*Comment End By Nishat*/
			} else {
				if (strtotime($request_val->schedule_date) < strtotime($current_date)) {
					$update_ride = ScheduleRide::find($request_val->id);
					$update_ride->status = 'Cancelled';
					$update_ride->save();
				}
			}
		}
	}

	/** 
	 * Update Referral Status
	 * 
	 **/
	public function updateReferralStatus()
	{
		Log::info("updateReferralStatus(CronController) Stp:1 :");

		ReferralUser::where('end_date', '<', date('Y-m-d'))->where('payment_status', 'Pending')->update(['payment_status' => 'Expired']);

		CustomLog::info("updateReferralStatus(CronController) Stp:2 :");

		return response()->json(['status' => true, 'status_message' => 'updated successfully']);
	}

	/** 
	 * Update User Offline status
	 * 
	 **/
	public function updateOfflineUsers()
	{
		Log::info("updateOfflineUsers (CronController) Stp:1 :");

		$offline_hours = site_settings('offline_hours');
		$minimumTimestamp = Carbon::now()->subHours($offline_hours);

		\DB::table('driver_location')->where('status', 'Online')->where('updated_at', '<', $minimumTimestamp)->update(['status' => 'Offline']);

		CustomLog::info("updateOfflineUsers (CronController) Stp:2 :");

		return response()->json(['status' => true, 'status_message' => 'updated successfully']);
	}

	/** 
	 * Update Currency rate
	 * 
	 **/
	public function updateCurrency()
	{
		Log::info("updateCurrency (CronController) Stp:1 :");


		$return_data = array();
		$result = Currency::all();
		$result->each(function ($row) use (&$return_data) {
			$rate = 1;
			try {
				if ($row->code != 'USD') {
					$rate = \Swap::latest('USD/' . $row->code);
					$rate = $rate->getValue();
				}
				Currency::where('code', $row->code)->update(['rate' => $rate]);
				$return_data[] = ['status' => true, 'status_message' => 'updated successfully', 'target' => $row->code, 'value' => $rate];
			} catch (\Exception $e) {
				$return_data[] = ['status' => false, 'status_message' => $e->getMessage(), 'target' => $row->code];
			}
		});

		CustomLog::info("updateCurrency (CronController) Stp:2 :");

		return response()->json($return_data);
	}

	/** 
	 * Update Paypal Payout Status
	 * 
	 **/
	public function updatePaypalPayouts()
	{
		Log::info("updatePaypalPayouts (CronController) Stp:1 :");

		$pending_payments = Payment::where('driver_payout_status', 'Processing')->orWhere('admin_payout_status', 'Processing')->get();
		if ($pending_payments->count() == 0) {
			return response()->json(['status' => false, 'status_message' => 'No Pending Payouts found']);
		}

		$paypal_payout = resolve("App\Services\Payouts\PaypalPayout");
		$pending_payments->each(function ($pending_payment) use ($paypal_payout) {
			$batch_id = $pending_payment->correlation_id;
			$payment_data = $paypal_payout->fetchPayoutViaBatchId($batch_id);
			if ($payment_data['status']) {
				$payout_data = $paypal_payout->getPayoutStatus($payment_data['data']);
				$trip = Trips::find($pending_payment->trip_id);

				if ($payout_data['status']) {
					if ($payout_data['payout_status'] == 'SUCCESS') {
						if ($trip->driver->company_id == '1') {
							$pending_payment->driver_payout_status = "Paid";
							$pending_payment->driver_transaction_id = $payout_data['transaction_id'];
						} else {
							$pending_payment->admin_payout_status = "Paid";
							$pending_payment->admin_transaction_id = $payout_data['transaction_id'];
						}
					}

					if (in_array($payout_data['payout_status'], ['FAILED', 'RETURNED', 'BLOCKED'])) {
						if ($trip->driver->company_id == '1') {
							$pending_payment->driver_payout_status = "Pending";
						} else {
							$pending_payment->admin_payout_status = "Pending";
						}
					}

					$pending_payment->save();
				}
			}
		});

		CustomLog::info("updatePaypalPayouts (CronController) Stp:2 :");

		return response()->json(['status' => true, 'status_message' => 'updated successfully']);
	}

	// public function set_driver_online_time()
	// {
	// 	//daily bonus
	// 	//generate and send to driver balance
	// 	$yesterday = date('Y-m-d', strtotime("-1 days"));

	// 	$list = User::where('user_type', 'Driver')
	// 		->where('status', 'Active')
	// 		->get();

	// 	foreach ($list as $data) {
	// 		$this->bonus_helper->add_update_online_bonus($data->id, date('Y-m-d'));
	// 		$this->bonus_helper->adjust_driver_online_bonus_v2($data->id, $yesterday);
	// 	}
	// }

	public function set_driver_online_time_v2()
	{
		$list = DB::table('users')
					->select('id')
					->where('user_type', 'Driver')
					->where('status', 'Active')
					->get();

		foreach ($list as $data) {
			$this->bonus_helper->adjust_driver_online_bonus_v2($data->id);
			$this->bonus_helper->add_online_bonus_v2($data->id);
		}
	}


	public function set_driver_weekly_bonus()
	{
		//script run daily but update weekly
		$today_name = date('l');
		$lastWeek = date("Y-m-d", strtotime("-7 days"));
		$yesterday = date("Y-m-d", strtotime("-1 days"));
		$start_date = date('Y-m-d');
		$end_date = date("Y-m-d", strtotime("6 days"));

		$referral = ReferralSetting::DriverTripBonus()
			->get()
			->pluck('value', 'name')
			->toArray();

		if ($referral['day_name'] == $today_name) {
			$user_list = User::where('user_type', 'Driver')
				->where('status', 'Active')
				->get();

			foreach ($user_list as $user) {
				$bonus = Bonus::where('user_id', $user->id)
					->where('user_type', 'Driver')
					->where('bonus_type', 'DriverTripBonus')
					->where('start_date', '>=', $lastWeek)
					->where('end_date', '<=', $yesterday)
					->orderBy('id', 'DESC')
					->first();

				if (is_object($bonus)) {
					if ($bonus->status == 'Due') {
						if ($bonus->completed_trips >= $referral['number_of_trips']) {
							if ($this->bonus_helper->transfer_to_balance($user, $bonus)) {
								echo "p" . $user->id . ",";
							}
						} else {
							$bonus_update = Bonus::find($bonus->id);
							$bonus_update->status   = 'Cancel';
							$bonus_update->save();
						}
					}
				} //if($bonus != ''){

				//set invoice if not exist current
				$current_bonus = Bonus::where('user_id', $user->id)
					->where('user_type', 'Driver')
					->where('bonus_type', 'DriverTripBonus')
					->where('start_date', $start_date)
					->where('end_date', $end_date)
					->orderBy('id', 'DESC')
					->first();

				if (!is_object($current_bonus)) {
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
					//if ($trip_bonus->save()) echo "c" . $trip_bonus->id . ',';

					try {
						$trip_bonus->save();
					} catch (\Exception $e) {
						return false;
					}
				} // if($current_bonus == '')
			} // foreach
		} // $referral['day_name'] 


	}

	public function set_driver_weekly_bonus_v2()
	{
		//script run daily but update weekly
		$today_name = date('l');
		$today 		= date('Y-m-d');
		$lastWeek 	= date("Y-m-d", strtotime("-7 days"));
		$end_date 	= date("Y-m-d", strtotime("6 days"));

		$referral = ReferralSetting::DriverTripBonus()
			->get()
			->pluck('value', 'name')
			->toArray();

		if ($referral['day_name'] == $today_name) {
			$active_date 	= date("Y-m-d", strtotime('-' . $referral['bonus_start_after_month'] . " months"));

			$user_list = DB::table('users')
				->select('id')
				->where('user_type', 'Driver')
				->where('status', 'Active')
				->whereDate('active_time', '<=', $active_date)
				->get();

			foreach ($user_list as $user) {
				$user_id = $user->id;
				$bonus = Bonus::where('user_id', $user_id)
					->where('user_type', 'Driver')
					->where('bonus_type', 'DriverTripBonus')
					->where('bonus_date', $lastWeek)
					->orderBy('id', 'DESC')
					->first();

				if (is_object($bonus)) {
					if ($bonus->status == 'Due') {
						$bonus_id = $bonus->id;
						$completed_trips = $bonus->completed_trips;
						$amount = '';

						if ($completed_trips >= $bonus->third_stage_min_trips) $amount = $bonus->third_stage_amount;
						else if ($completed_trips >= $bonus->second_stage_min_trips) $amount = $bonus->second_stage_amount;
						else if ($completed_trips >= $bonus->first_stage_min_trips) $amount = $bonus->first_stage_amount;

						if ($amount != '' and $amount > 0) {
							if ($this->bonus_helper->transfer_to_balance_trip_bonus($user_id, $bonus_id, $amount)) {
								echo "p" . $user_id . ",";
							}
						} else {
							$bonus_update = Bonus::find($bonus_id);
							$bonus_update->status   = 'Cancel';
							$bonus_update->save();
						}
					}
				} //if($bonus != ''){
				else {
					//set invoice if not exist current
					$bonus_exist = Bonus::where('user_id', $user_id)
						->where('user_type', 'Driver')
						->where('bonus_type', 'DriverTripBonus')
						->where('bonus_date', $today)
						->orderBy('id', 'DESC')
						->first();

					if (!is_object($bonus_exist)) {
						$trip_bonus = new Bonus;
						$trip_bonus->user_id = $user_id;
						$trip_bonus->user_type = 'Driver';
						$trip_bonus->bonus_type = 'DriverTripBonus';
						$trip_bonus->number_of_days = $referral['number_of_days'];
						$trip_bonus->currency_code = $referral['currency_code'];
						$trip_bonus->terms_condition = $referral['terms_condition'];
						$trip_bonus->withdrawal_method = $referral['withdrawal_method'];
						$trip_bonus->bonus_date = $today;
						$trip_bonus->start_date = $today;
						$trip_bonus->end_date = $end_date;
						$trip_bonus->unique_id = $user_id . '_' . date('Ymd') . '_DriverTripBonus';

						$trip_bonus->bonus_amount = $referral['third_stage_amount'];
						$trip_bonus->number_of_trips = $referral['third_stage_min_trips'];
						$trip_bonus->payment_after_days = $referral['number_of_days'];

						$trip_bonus->first_stage_min_trips = $referral['first_stage_min_trips'];
						$trip_bonus->first_stage_max_trips = $referral['first_stage_max_trips'];
						$trip_bonus->first_stage_amount = $referral['first_stage_amount'];

						$trip_bonus->second_stage_min_trips = $referral['second_stage_min_trips'];
						$trip_bonus->second_stage_max_trips = $referral['second_stage_max_trips'];
						$trip_bonus->second_stage_amount = $referral['second_stage_amount'];

						$trip_bonus->third_stage_min_trips = $referral['third_stage_min_trips'];
						//$trip_bonus->third_stage_max_trips = $referral['third_stage_max_trips'];
						$trip_bonus->third_stage_amount = $referral['third_stage_amount'];

						$trip_bonus->allow_same_user = $referral['allow_same_user'];
						$trip_bonus->trip_distance = $referral['trip_distance'];
						$trip_bonus->bonus_start_after_month = $referral['bonus_start_after_month'];
						$trip_bonus->create_method = 'CronController::set_driver_weekly_bonus_v2';


						try {
							if ($trip_bonus->save()) echo "c" . $user_id . ',';
						} catch (\Exception $e) {
							return false;
						}
					}
				}
			} // foreach
		} // $referral['day_name'] 


	}

	public function test_nid()
	{
		$key = "9663be8d-e355-417d-ab48-3db7acb4b298";
		$PostURL = 	 "https://api.porichoybd.com/api/Kyc/nid-person-values";
					

		$PostData = array(
			'national_id' => '6443559809',
			'team_tx_id' => '',
			'english_output' => false,
			'person_dob' => '',
		);

		$url = curl_init($PostURL);
		$postToken = json_encode($PostData);
		$header = array(
			'Content-Type:application/json',
			'x-api-key:' . $key
		);

		curl_setopt($url, CURLOPT_HTTPHEADER, $header);
		curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($url, CURLOPT_POSTFIELDS, $postToken);
		curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false);

		$resultData = curl_exec($url);
		$ResultArray = json_decode($resultData, true);
		curl_close($url);
		print_r($ResultArray);
	}

	public function adjust_driver_joining_bonus()
	{
		$referralSetting = ReferralSetting::DriverJoiningBonus()->get()->pluck('value', 'name')->toArray();
		$payment_after_days = $referralSetting['payment_after_days'];
		//$payment_after_days = 1;

		$bonus_date = date('Y-m-d', strtotime(date('Y-m-d') . ' - ' . $payment_after_days . ' days'));

		$bonus = Bonus::where('user_type', 'Driver')
			//->where('user_id', '14545') // test purpose should be off
			->where('bonus_type', 'DriverJoiningBonus')
			->where('completed_trips', '>', 0)
			->where('status', 'Due')
			->whereDate('created_at', $bonus_date)
			->get();

		foreach ($bonus as $data) {
			if ($data->number_of_trips == $data->completed_trips) {
				//balance transfer

				$balance = new DriverBalance;
				$balance->user_id = $data->user_id;
				$balance->bonus_id = $data->id;
				$balance->amount = $data->bonus_amount;
				$balance->status = 'pending';
				$balance->unique_id = $data->user_id . '-' . $data->id;

				try {
					if ($balance->save()) {
						$bonus_update = Bonus::find($data->id);
						$bonus_update->status = 'Paid';
						$bonus_update->save();
					}
				} catch (\Exception $e) {
				}
			} else {
				//cancel
				$updated_bonus = Bonus::find($data->id);
				$updated_bonus->status = 'Cancel';
				$updated_bonus->save();
			}
		}
		Bonus::where('user_type', 'Driver')
			->where('bonus_type', 'DriverJoiningBonus')
			->whereRaw('number_of_trips > completed_trips')
			->where('status', 'Due')
			->whereDate('created_at', $bonus_date)
			->update(array('status' => 'Cancel'));
	}

	public function adjust_driver_referral_bonus()
	{
		$referralSetting = ReferralSetting::DriverReferralBonus()->get()->pluck('value', 'name')->toArray();
		$payment_after_days = $referralSetting['payment_after_days'];
		//$payment_after_days = 1;

		$bonus_date = date('Y-m-d', strtotime(date('Y-m-d') . ' - ' . $payment_after_days . ' days'));

		$bonus = Bonus::where('user_type', 'Driver')
			->where('bonus_type', 'DriverReferralBonus')
			//->where('user_id', '14545') // test purpose should be off
			->where('completed_trips', '>', 0)
			->where('status', 'Due')
			->whereDate('created_at', $bonus_date)
			->get();

		foreach ($bonus as $data) {
			if ($data->number_of_trips == $data->completed_trips) {
				//balance transfer

				$balance = new DriverBalance;
				$balance->user_id = $data->user_id;
				$balance->bonus_id = $data->id;
				$balance->amount = $data->bonus_amount;
				$balance->status = 'pending';
				$balance->unique_id = $data->user_id . '-' . $data->id;

				try {
					if ($balance->save()) {
						$bonus_update = Bonus::find($data->id);
						$bonus_update->status = 'Paid';
						$bonus_update->save();
					}
				} catch (\Exception $e) {
				}
			} else {
				//cancel
				$updated_bonus = Bonus::find($data->id);
				$updated_bonus->status = 'Cancel';
				$updated_bonus->save();
			}
		}

		Bonus::where('user_type', 'Driver')
			->where('bonus_type', 'DriverReferralBonus')
			->whereRaw('number_of_trips > completed_trips')
			->where('status', 'Due')
			->whereDate('created_at', $bonus_date)
			->update(array('status' => 'Cancel'));
	}

	public static function setOnlineUsersActivity()
	{ 
		// last update before half an hour
		DriverLocation::where('status', 'Online')
						->where('updated_at', '<', date("Y-m-d H:i:s", time() - 1800))
						->update(array('status' => 'Offline'));

		$now = time();
		$start_end_time = self::pick_time();
		$start_time_array = $start_end_time['start_time'];
		$end_time_array = $start_end_time['end_time'];

		$peak_time_created_at = '';
		$peak_time_updated_at = '';
		$new_entry = false;

		if (!empty($start_time_array)) {
			for ($i = 0; $i < count($start_time_array); $i++) {
				$start = strtotime(date('Y-m-d').' '.$start_time_array[$i]);
				$end = strtotime(date('Y-m-d').' '.$end_time_array[$i]);

				if ($now >= $start and $now <= $end) {
					$peak_time_created_at = now();
					$peak_time_updated_at = now();
					break;
				}
			}
		}

		$users = DriverLocation::where('status', 'Online')->select('user_id')->get();

		
		foreach ($users as $user) {
			$user_id = $user->user_id;	

			$table = Activity::where('user_id', $user_id)->orderBy('id', 'DESC')->first();
			if(is_object($table)){
				$table->hit_time = $table->hit_time.','.date('H:i:s');
				$table->updated_at = now();

				if (
					$table->peak_time_created_at == '' OR
					$table->peak_time_created_at == '0000-00-00 00:00:00' OR
					$table->peak_time_created_at == NULL){
					if($peak_time_created_at !=''){

						//Peaktime started after created_at
						if(strtotime($table->created_at) <=  $start) {
							$peak_time_created_at = date("Y-m-d H:i:s", $start);
						}
						//Peaktime started before created_at
						else{
							$peak_time_created_at = $table->created_at;
						}

						$table->peak_time_created_at = $peak_time_created_at;
					}
				}

				if (
					$table->peak_time_created_at != '' AND
					$table->peak_time_created_at != '0000-00-00 00:00:00' AND
					$table->peak_time_created_at != NULL
				) {
					if (!empty($end_time_array)) {
						for ($i = 0; $i < count($end_time_array); $i++) {
							$start = strtotime(date('Y-m-d').' '.$start_time_array[$i]);
							$end = strtotime(date('Y-m-d').' '.$end_time_array[$i]);

							if ($start <= $now and $now <= $end) {
								$table->peak_time_updated_at = now();
								break;
							}else{
								if(strtotime($table->peak_time_created_at) >= $start AND $now >= $end){
									$update_time = date('Y-m-d').' '.$end_time_array[$i];
									$table->peak_time_updated_at = $update_time;
									$table->updated_at = $update_time;
									$new_entry = true;
									break;
								}
							}
						}
					}
				}
				$table->save();
				if($new_entry){					
					$table_new = new Activity;
					$table_new->user_id = $user_id;
					$table_new->created_at = $update_time;
					$table_new->updated_at = $update_time;
					$table_new->save();
				} // if($new_entry){
			} // if(is_object($table)){


		} //end foreach		
	}


}
