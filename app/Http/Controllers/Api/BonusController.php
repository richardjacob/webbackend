<?php

/**
 * Trip Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Trip
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use JWTAuth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CustomLog;
use DB;


class BonusController extends Controller
{
	public function __construct()
	{
		$this->bonus_helper = resolve('App\Http\Helper\BonusHelper');
		DB::enableQueryLog();
	}

	public function bonus_status(Request $r)
	{	
		Log::info("bonus_status Api Stp:1 :", $r->all());
		$user_details = JWTAuth::parseToken()->authenticate();
		$array = $this->bonus_helper->bonus_status($user_details->id);
		CustomLog::info("bonus_status Api Stp:2 :");
		return response()->json($array);
	}

	// public function bonus_status_v2(Request $r)
	// {	
	// 	/*token
	// 	bonus_type=signup or referral or weekly or daily or cashback1 or cashback2
	// 	*/

	// 	$user_details = JWTAuth::parseToken()->authenticate();
	// 	$user_type = User::where('id', $user_details->id)->pluck('user_type')->first();

	// 	$bonusType = $r->bonus_type;
	// 	$page = $r->page ?? 1;
	// 	$history = '';	

	// 	if($bonusType !=''){
	// 		if (str_contains($bonusType, '_history')) {
	// 		    $bonusType = str_replace('_history', '', $bonusType);
	// 		    $history = 1;
	// 		}


	// 		$bonus_type = "";
	// 		if($user_type == 'Driver'){
	// 			switch ($bonusType) {
	// 				case 'signup': $bonus_type = "DriverSignupBonus"; break;
	// 				case 'referral': $bonus_type = "Driver"; break;
	// 				case 'weekly': $bonus_type = "DriverTripBonus"; break;
	// 				case 'daily': $bonus_type = "DriverOnlineBonus"; break;				
	// 				default: $bonus_type = ""; break;
	// 			}
	// 		}
	// 		else if($user_type == 'Rider'){
	// 			switch ($bonusType) {
	// 				case 'referral': $bonus_type = "Rider"; break;
	// 				case 'cashback1': $bonus_type = "RiderCashback1"; break;
	// 				case 'cashback2': $bonus_type = "RiderCashback2"; break;			
	// 				default: $bonus_type = ""; break;
	// 			}
	// 		}
	// 		if($bonusType == 'history'){
	// 			if($user_type == 'Driver'){
	// 				return '[
	// 							{
	// 								"key":"daily_history",
	// 								"value":"Daily Bonus History"
	// 							},
	// 							{
	// 								"key":"weekly_history",
	// 								"value":"Weekly Bonus History"
	// 							},
	// 							{
	// 								"key":"signup_history",
	// 								"value":"Signup Bonus History"
	// 							},
	// 							{
	// 								"key":"referral_history",
	// 								"value":"Referral Bonus History"
	// 							}
	// 						]';
	// 			}
	// 			else if($user_type == 'Rider'){
	// 				return '[
	// 							{
	// 								"key":"referral_history",
	// 								"value":"Referral Bonus History"
	// 							},
	// 							{
	// 								"key":"cashback1_history",
	// 								"value":"First Cashback Bonus History"
	// 							},
	// 							{
	// 								"key":"cashback2_history",
	// 								"value":"Second Cashback Bonus History"
	// 							}
	// 						]';
	// 			}
	// 		}
	// 		else{
	// 			$array = $this->bonus_helper->bonus_status_v2($user_details->id, $bonus_type, $page, $history);
	// 		}

	// 		return response()->json($array);
	// 	}
	// 	else{
	// 		if($user_type == 'Driver'){
	// 			return '[
	// 						{
	// 							"key":"daily",
	// 							"value":"Daily Bonus"
	// 						},
	// 						{
	// 							"key":"weekly",
	// 							"value":"Weekly Bonus"
	// 						},
	// 						{
	// 							"key":"signup",
	// 							"value":"Signup Bonus"
	// 						},
	// 						{
	// 							"key":"referral",
	// 							"value":"Referral Bonus"
	// 						},
	// 						{
	// 							"key":"history",
	// 							"value":"Bonus History"
	// 						}
	// 					]';
	// 		}
	// 		else if($user_type == 'Rider'){
	// 			return '[
	// 						{
	// 							"key":"referral",
	// 							"value":"Referral Bonus"
	// 						},
	// 						{
	// 							"key":"cashback1",
	// 							"value":"First Cashback Bonus"
	// 						},
	// 						{
	// 							"key":"cashback2",
	// 							"value":"Second Cashback Bonus"
	// 						},
	// 						{
	// 							"key":"history",
	// 							"value":"Bonus History"
	// 						}
	// 					]';
	// 		}

	// 	}
	// }


	public function bonus_status_v2(Request $r)
	{
		/*token
		bonus_type=signup or referral or weekly or daily or cashback1 or cashback2
		*/
		Log::info("bonus_status_v2 Api Stp:1 :", $r->all());
		$user_details = JWTAuth::parseToken()->authenticate();
		$user_type = User::where('id', $user_details->id)->pluck('user_type')->first();

		$bonusType = $r->bonus_type;
		$page = $r->page ?? 1;
		$history = '';
		$array = array();
		$common_array = array(
			'status_code' => '1',
			'status_message' => "Bonus List"
		);


		if ($bonusType != '') {
			if (str_contains($bonusType, '_history')) {
				$bonusType = str_replace('_history', '', $bonusType);
				$history = 1;
			}

			$bonus_type = "";
			if ($user_type == 'Driver') {
				switch ($bonusType) {
						//case 'signup': $bonus_type = "DriverSignupBonus"; break; //preavious workable
					case 'signup':
						$bonus_type = "DriverJoiningBonus";
						break;

						//case 'referral': $bonus_type = "Driver"; break; //preavious workable

						// case 'referral':
						// 	$bonus_type = "DriverReferralBonus";
						// 	break;
					case 'weekly':
						$bonus_type = "DriverTripBonus";
						break;
					case 'daily':
						$bonus_type = "DriverOnlineBonus";
						break;
					default:
						$bonus_type = "";
						break;
				}
			} else if ($user_type == 'Rider') {
				switch ($bonusType) {
						// case 'referral':
						// 	$bonus_type = "Rider";
						// 	break;


						//case 'cashback1': $bonus_type = "RiderCashback1"; break;
						//case 'cashback2': $bonus_type = "RiderCashback2"; break;	
					case 'bonus_list':
						$bonus_type = "bonus_list";
						break;
					case 'history':
						$bonus_type = "history";
						break;
					default:
						$bonus_type = "";
						break;
				}
			}

			if ($bonusType == 'history') {
				if ($user_type == 'Driver') {
					$x[] = array(
						'key' => 'daily_history',
						'value' => 'Daily Bonus History'
					);
					$x[] = array(
						'key' => 'weekly_history',
						'value' => 'Weekly Bonus History'
					);
					$x[] = array(
						'key' => 'signup_history',
						'value' => 'Joining Bonus History'
					);
					// $x[]=array(
					// 			'key' => 'referral_history',
					// 			'value' => 'Referral Bonus History'
					// 		);					
				} else if ($user_type == 'Rider') {
					// $x[] = array(
					// 	'key' => 'referral_history',
					// 	'value' => 'Referral Bonus History'
					// );


					$x[] = array(
						'key' => 'cashback1_history',
						'value' => 'First Cashback Bonus History'
					);
					$x[] = array(
						'key' => 'cashback2_history',
						'value' => 'Second Cashback Bonus History'
					);
					$array = array('result' => $this->bonus_helper->bonus_status_v2($user_details->id, $bonus_type, $page, $history));
				}
				if (isset($x)) $array = array('result' => $x);
			} else {
				// $array = $this->bonus_helper-> $user_details->id, $bonus_type, $page, $history);

				$array = array('result' => $this->bonus_helper->bonus_status_v2($user_details->id, $bonus_type, $page, $history));
			}
		} else {
			if ($user_type == 'Driver') {
				$x[] = array(
					'key' => 'daily',
					'value' => 'Daily Bonus'
				);
				$x[] = array(
					'key' => 'weekly',
					'value' => 'Weekly Bonus'
				);
				$x[] = array(
					'key' => 'signup',
					'value' => 'Joining Bonus'
				);
				// $x[]=array(
				// 			'key' => 'referral',
				// 			'value' => 'Referral Bonus'
				// 		);
				$x[] = array(
					'key' => 'history',
					'value' => 'Bonus History'
				);
			} else if ($user_type == 'Rider') {
				// $x[]=array(
				// 			'key' => 'referral',
				// 			'value' => 'Referral'
				// 		);
				$x[] = array(
					'key' => 'bonus_list',
					'value' => 'Bonus List'
				);
				$x[] = array(
					'key' => 'history',
					'value' => 'Bonus History'
				);
			}
			$array = array('bonus_type' => $x);
		}

		$final_array = array_merge($common_array, $array);
		CustomLog::info("bonus_status_v2 Api Stp:2 :");
		return response()->json($final_array);
	}





	public function bonus_list(Request $request)
	{
		Log::info("bonus_list Api Stp:1 :", $request->all());
		$user_details = JWTAuth::parseToken()->authenticate();
		$user_type = User::where('id', $user_details->id)->pluck('user_type')->first();
		$bonusType = $request->bonus_type;
		$page = $request->page ?? 1;
		if ($bonusType != '') {
			if ($user_type == 'Driver') {
				if ($bonusType=="daily"){
					CustomLog::info("bonus_list Api Stp:2 :");
					return self::driver_daily_bonus_list($user_details->id, $page);
				}
				else if ($bonusType=="weekly"){
					return self::driver_weekly_bonus_list($user_details->id, $page);
				}
				else if ($bonusType=="signup"){
					return self::driver_joining_bonus_list($user_details->id, $page);
				}else {
					return response()->json([
						'status_code' 		=> '0',
						'status_message' 	=> "Invalid Bonus Type",
					]);
				}
			}
			else if ($user_type == "Rider") {

			}
			
		}else {
			$bonus = array();
			if ($user_type == 'Driver') {
				$bonus[] = array(
					'key' => 'daily',
					'value' => 'Daily'
				);
				$bonus[] = array(
					'key' => 'weekly',
					'value' => 'Weekly'
				);
				$bonus[] = array(
					'key' => 'signup',
					'value' => 'Joining'
				);
			}
			else if ($user_type == 'Rider') {

			}
			CustomLog::info("bonus_list Api Stp:2 :");
			return response()->json([
				'bonus_list' 		=> $bonus,
			]);
		}

		

	}

	public function driver_daily_bonus_list($user_id, $page)
	{
		
		$array = array();
		$common_array = array(
			'status_code' => '1',
			'status_message' => "Daily Bonus List"
		);
		$bonus_type = "DriverOnlineBonus";

		$array = array('daily' => $this->bonus_helper->bonus_status_v3($user_id, $bonus_type, $page));
		if(!empty($array )){
			$final_array = array_merge($common_array, $array);
			return response()->json($final_array);
		}else {
			// return response()->json([
			// 	'status_code' 		=> '2',
			// 	'status_message' 	=> "No Daily Bonus Found",
			// ]);
			return response()->json($array);
		}
		

	}

	public function driver_weekly_bonus_list($user_id, $page)
	{
		$array = array();
		$common_array = array(
			'status_code' => '1',
			'status_message' => "Weekly Bonus List"
		);
		$bonus_type = "DriverTripBonus";
		$array = array('weekly' => $this->bonus_helper->bonus_status_v3($user_id, $bonus_type, $page));
		if(!empty($array )){
			$final_array = array_merge($common_array, $array);
			return response()->json($final_array);
		}else {
			// return response()->json([
			// 	'status_code' 		=> '2',
			// 	'status_message' 	=> "No Weekly Bonus Found",
			// ]);
			return response()->json($array);
		}

	}
	public function driver_joining_bonus_list($user_id, $page)
	{
		$array = array();
		$common_array = array(
			'status_code' => '1',
			'status_message' => "Joining Bonus List"
		);
		$bonus_type = "DriverSignupBonus";
		$array = array('signup' => $this->bonus_helper->bonus_status_v3($user_id, $bonus_type, $page));
		if(!empty($array )){
			$final_array = array_merge($common_array, $array);
			return response()->json($final_array);
		}else {
			// return response()->json([
			// 	'status_code' 		=> '2',
			// 	'status_message' 	=> "No joining Bonus Found",
			// ]);
			return response()->json($array);
		}

	}




}
