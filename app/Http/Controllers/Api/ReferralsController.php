<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helper\RequestHelper;
use App\Http\Start\Helpers;
use App\Models\User;
use App\Models\ProfilePicture;
use App\Models\ReferralSetting;
use App\Models\ReferralUser;
use App\Models\Bonus;
use JWTAuth;
use App;
use Illuminate\Support\Facades\Log;
use DB;
use App\Http\Controllers\CustomLog;

class ReferralsController extends Controller
{
	// Global variable for Helpers instance
	protected $request_helper;

	public function __construct(RequestHelper $request)
	{
		DB::enableQueryLog();

		$this->request_helper = $request;
		$this->helper = new Helpers;
	}

	public function get_referral_details(Request $request)
	{
		Log::info("get_referral_details Api Stp:1 :", $request->all());

		$user_details = JWTAuth::parseToken()->authenticate();

		$user = User::where('id', $user_details->id)->first();

		if ($user == '') {
			return response()->json([
				'status_code'		=> '0',
				'status_message'	=> trans('messages.invalid_credentials'),
			]);
		}

		$user_type = $user->user_type; //echo $user->id;

		$admin_referral_settings = ReferralSetting::whereUserType($user_type)->where('name', 'apply_referral')->first();


		$referral_amount = 0;
		if ($admin_referral_settings->value) {
			$referral_amount = $admin_referral_settings->get_referral_amount($user_type);
		}

		//$referral_users = ReferralUser::where('user_id',$user_details->id)->get();

		$referral_users = DB::Table('bonuses')->select(
			'bonuses.id as id',
			'bonuses.referred_by as referred_by',
			'bonuses.referral_to as referral_to',
			'bonuses.status',
			'bonuses.bonus_date as start_date',
			'bonuses.end_date as end_date',
			'bonuses.bonus_date as bonus_date',
			'bonuses.number_of_trips as number_of_trips',
			'bonuses.completed_trips as completed_trips',
			'bonuses.bonus_amount as bonus_amount',
			'bonuses.number_of_days as number_of_days',
			
			//'profile_picture.src as referred_user_profile_picture_src',
			//DB::raw('CONCAT(users.first_name,\' \',users.last_name) AS referred_user_name')
		)
			// ->leftJoin('users', function ($join) {
			// 	$join->on('bonuses.referral_to', '=', 'users.id');
			// })
			// ->leftJoin('profile_picture', function ($join) {
			// 	$join->on('bonuses.referral_to', '=', 'profile_picture.user_id');
			// })
			->where('bonuses.user_id', $user->id)
			->where('bonuses.user_type', $user_type)
			//->where('bonuses.bonus_type', ucwords(strtolower($user_type)))
			->where(function ($query) use ($user_type) {
				$query->where('bonuses.bonus_type', ucwords(strtolower($user_type)))
						->orWhere('bonuses.bonus_type', 'DriverReferralBonus');
			})
			->get();


			

		$pending_referral_amount = Bonus::select(DB::raw('SUM(bonus_amount) as total'))
			->where('user_id', $user->id)
			->where('user_type', $user_type)
			->where('bonus_type', ucwords(strtolower($user_type)))
			->where('status', 'Due')
			->first()
			->total;

		$total_referral_earnings = Bonus::select(DB::raw('SUM(bonus_amount) as total'))
			->where('user_id', $user->id)
			->where('user_type', $user_type)
			->where('bonus_type', ucwords(strtolower($user_type)))
			->where('status', 'Paid')
			->first()
			->total;

		$pending_referrals = array();
		$completed_referrals = array();

		foreach ($referral_users as $referral_user) {
			$number_of_trips = $referral_user->number_of_trips;
			$completed_trips = $referral_user->completed_trips;
			if ($completed_trips > $number_of_trips) $completed_trips = $number_of_trips;
			$remaining_trips = $number_of_trips - $completed_trips;

			$temp_details['id'] 			= $referral_user->id;

			if($referral_user->referred_by !=''){
				$referred_by = User::where('id', $referral_user->referred_by)
					->select(DB::raw('CONCAT(users.first_name,\' \',users.last_name) AS name'))->first();

				$profile_image = DB::table('profile_picture')->where('user_id', $referral_user->referred_by)->pluck('src')->first();

				$temp_details['name'] = $referred_by->name.' (Refer by)';				
				$temp_details['profile_image'] 	= $profile_image;
			}

			if($referral_user->referral_to !=''){
				$referral_to = User::where('id', $referral_user->referral_to)
					->select(DB::raw('CONCAT(users.first_name,\' \',users.last_name) AS name'))->first();

				$profile_image = DB::table('profile_picture')->where('user_id', $referral_user->referral_to)->pluck('src')->first();

				$temp_details['name'] = $referral_to->name.' (Refer to)';
				$temp_details['profile_image'] 	= $profile_image;
			}

			$temp_details['start_date'] 	= $referral_user->bonus_date;

			//$temp_details['end_date'] 		= $referral_user->end_date;
			// $temp_details['days'] 			= $referral_user->days;
			// $temp_details['remaining_days'] = $referral_user->remaining_days;
			$bonus_end_date = date('Y-m-d', strtotime($referral_user->bonus_date . '+'.$referral_user->number_of_days.' day'));
			$temp_details['end_date'] 	= $bonus_end_date;
			$bonus_end_date = strtotime($bonus_end_date);
		  //  $current_time=	date('Y-m-d');

			$current_time = strtotime(date('Y-m-d')); 
			if ($current_time>$bonus_end_date){
				$remaining_days=0;
			}else {
				$datediff =  $bonus_end_date-$current_time;
				$temp_details['datediff'] 	= $datediff;
				$remaining_days =round($datediff / (60 * 60 * 24));
			}
			$temp_details['current_time'] 	= $current_time;
			$temp_details['bonus_end_t'] 	= $bonus_end_date;

			$temp_details['remaining_days'] = $remaining_days;

			$temp_details['trips'] 			= $completed_trips;
			$temp_details['remaining_trips'] = $remaining_trips;
			$temp_details['earnable_amount'] = $referral_user->bonus_amount;
			$temp_details['status'] 		= $referral_user->status;

			if ($referral_user->status == 'Due') {
				array_push($pending_referrals, $temp_details);
			} else {
				array_push($completed_referrals, $temp_details);
			}
		}

		CustomLog::info("get_referral_details Api Stp:2 :");

		return response()->json([
			'status_code' 			=> '1',
			'status_message' 		=> trans('messages.success'),
			'apply_referral' 		=> $admin_referral_settings->value,
			'referral_link' 		=> route('redirect_to_app', ['type' => strtolower($user_type)]),
			'referral_code'  		=> $user->referral_code,
			'referral_amount' 		=> $referral_amount,
			'pending_amount' 		=> (string) $pending_referral_amount,
			'total_earning'  		=> (string) $total_referral_earnings,
			'pending_referrals' 	=> $pending_referrals,
			'completed_referrals' 	=> $completed_referrals,
		]);
	}


	/**
	 * To Get the referral Users Details
	 * @param  Request $request Get values
	 * @return Response Json
	 */
	// public function get_referral_details(Request $request)
	// {
	// 	Log::info("get_referral_details Api Stp:1 :",$request->all());

	// 	$user_details = JWTAuth::parseToken()->authenticate();

	// 	$user = User::where('id', $user_details->id)->first();

	// 	if ($user == '') {
	// 		return response()->json([
	// 			'status_code'		=> '0',
	// 			'status_message'	=> trans('messages.invalid_credentials'),
	// 		]);
	// 	}

	// 	$user_type = $user->user_type;

	// 	$admin_referral_settings = ReferralSetting::whereUserType($user_type)->where('name','apply_referral')->first();

	// 	$referral_amount = 0;
	//    	if($admin_referral_settings->value) {
	//        	$referral_amount = $admin_referral_settings->get_referral_amount($user_type);
	// 	}

	// 	$referral_users = ReferralUser::where('user_id',$user_details->id)->get();

	// 	$pending_referrals = array();
	// 	$completed_referrals = array();

	// 	foreach ($referral_users as $referral_user) {
	// 		$temp_details['id'] 			= $referral_user->id;
	// 		$temp_details['name'] 			= $referral_user->referred_user_name;
	// 		$temp_details['profile_image'] 	= $referral_user->referred_user_profile_picture_src;
	// 		$temp_details['start_date'] 	= $referral_user->start_date;
	// 		$temp_details['end_date'] 		= $referral_user->end_date;
	// 		$temp_details['days'] 			= $referral_user->days;
	// 		$temp_details['remaining_days'] = $referral_user->remaining_days;
	// 		$temp_details['trips'] 			= $referral_user->trips;
	// 		$temp_details['remaining_trips']= $referral_user->remaining_trips;
	// 		$temp_details['earnable_amount']= $referral_user->earnable_amount;
	// 		$temp_details['status'] 		= $referral_user->payment_status;

	// 		if($referral_user->payment_status == 'Pending') {
	// 			array_push($pending_referrals,$temp_details);
	// 		}
	// 		else {
	// 			array_push($completed_referrals,$temp_details);
	// 		}
	// 	}

	// 	CustomLog::info("get_referral_details Api Stp:2 :");

	// 	return response()->json([
	// 		'status_code' 			=> '1',
	// 		'status_message' 		=> trans('messages.success'),
	// 		'apply_referral' 		=> $admin_referral_settings->value,
	// 		'referral_link' 		=> route('redirect_to_app',['type' => strtolower($user_type)]),
	// 		'referral_code'  		=> $user->referral_code,
	// 		'referral_amount' 		=> $referral_amount,
	// 		'pending_amount' 		=> $user->pending_referral_amount,
	// 		'total_earning'  		=> $user->total_referral_earnings,
	// 		'pending_referrals' 	=> $pending_referrals,
	// 		'completed_referrals' 	=> $completed_referrals,
	// 	]);
	// }
}
