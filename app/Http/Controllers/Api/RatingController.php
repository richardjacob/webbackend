<?php

/**
 * Rating Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Rating
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helper\RequestHelper;
use App\Http\Start\Helpers;
use App\Models\Fees;
use App\Models\Rating;
use App\Models\Request as RideRequest;
use App\Models\Trips;
use App\Models\ManageFare;
use App\Models\User;
use App\Models\UsersPromoCode;
use App\Models\Wallet;
use App\Models\ScheduleRide;
use App\Models\Company;
use App\Models\DriverOweAmount;
use App\Models\PoolTrip;
//added by riyadul 4-08-2021
use App\Models\Comment;
//added by riyadul 4-08-2021 end
use App\Repositories\DriverOweAmountRepository;
use App\Services\PushNotificationService;
use Auth;
use DateTime;
use DB;
use Illuminate\Http\Request;
use App\Http\Helper\InvoiceHelper;
use JWTAuth;
use Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CustomLog;


class RatingController extends Controller
{
	protected $request_helper; // Global variable for Helpers instance
	/**
	 * @var PushNotificationService
	 */
	private $pushNotificationService;

	public function __construct(RequestHelper $request, DriverOweAmountRepository $driver_owe_amt_repository, InvoiceHelper $invoice_helper, PushNotificationService $pushNotificationService)
	{
		DB::enableQueryLog();
		$this->request_helper = $request;
		$this->helper = new Helpers;
		$this->invoice_helper = $invoice_helper;
		$this->driver_owe_amt_repository = $driver_owe_amt_repository;
		$this->pushNotificationService = $pushNotificationService;
	}

	/**
	 * Display the Diver rating
	 * @param  Post method request inputs
	 *
	 * @return Response Json
	 */
	public function driver_rating(Request $request, $user_details = '')
	{
		Log::info("driver_rating Api Stp:1 :", $request->all());
		if ($user_details == "") {
			$user_details = JWTAuth::parseToken()->authenticate();
		}

		$rules = array(
			'user_type' => 'required|in:Driver,driver',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code'     => '0',
				'status_message' => $validator->messages()->first(),
			]);
		}
		$user = User::where('id', $user_details->id)->first();

		if ($user == '') {
			return response()->json([
				'status_code' 	 => '0',
				'status_message' => "Invalid credentials",
			]);
		}

		$total_rated_trips = DB::table('rating')->select(DB::raw('count(id) as total_rated_trips'))
			->where('driver_id', $user_details->id)->where('rider_rating', '>', 0)->first()->total_rated_trips;

		$total_rating = DB::table('rating')->select(DB::raw('sum(rider_rating) as rating'))
			->where('driver_id', $user_details->id)->where('rider_rating', '>', 0)->where('driver_id', $user_details->id)->first()->rating;

		$total_rating_count = Rating::where('driver_id', $user_details->id)->where('rider_rating', '>', 0)->get()->count();

		$life_time_trips = DB::table('trips')->select(DB::raw('count(id) as total_trips'))
			->where('driver_id', $user_details->id)->first()->total_trips;

		$five_rating_count = Rating::where('driver_id', $user_details->id)->where('rider_rating', 5)->get()->count();

		$driver_rating = '0.00';
		if ($total_rating_count != 0) {
			$driver_rating = (string) round(($total_rating / $total_rating_count), 2);
		}

		CustomLog::info("driver_rating Api Stp:2 :");

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> "Success",
			'total_rating' 		=> @$total_rated_trips != '' ? $total_rated_trips : '0',
			'total_rating_count' => @$life_time_trips != '' ? $life_time_trips : '0',
			'driver_rating' 	=> @$driver_rating != '' ? $driver_rating : '0.00',
			'five_rating_count' => @$five_rating_count != '' ? $five_rating_count : '0',
		]);
	}

	/**
	 * Get The Invoice of the given Trip id
	 *
	 * @param  Get method request inputs
	 * @return Response Json
	 */
	public function getinvoice(Request $request)
	{
		Log::info("get_invoice Api Stp:1 :", $request->all());

		$user_details = JWTAuth::parseToken()->authenticate();

		$request->merge(['payment_mode' => strtolower($request->payment_mode)]); //strtolower()
		$rules = array(
			'user_type' => 'required|in:Rider,rider,Driver,driver',
			'trip_id' => 'required',
			'payment_mode' => 'in:paypal,stripe,cash,braintree,nagad,bKash',
			'is_wallet' => 'in:Yes,No',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code'     => '0',
				'status_message' => $validator->messages()->first(),
			]);
		}

		$request->payment_mode = ($request->payment_mode == 'paypal') ? 'PayPal' : ucfirst($request->payment_mode);

		$user = User::where('id', $user_details->id)->first();

		$trips = Trips::where('id', $request->trip_id)->first();


		$save = 0;
		if ($request->payment_mode && $trips->is_calculation == 0) { //if is_calculation is zero and payment_mode send then update payment mode in table
			$payment_method_store = $request->payment_mode;
			if ($request->is_wallet == 'Yes' && $payment_method_store != 'Wallet') {
				$payment_method_store = $request->payment_mode . ' & Wallet';
			}

			//If user change payment mode then change payment mode in trips,requests & schedule_rides tables also
			Trips::where('id', $request->trip_id)->update(['payment_mode' => $payment_method_store]);
			$rideRequest = RideRequest::where('id', $trips->request_id)->first();
			$rideRequest->payment_mode = $payment_method_store;
			$rideRequest->save();
			ScheduleRide::where('id', $rideRequest->schedule_id)->update(['payment_method' => $rideRequest->payment_mode]);
		}

		if ($trips->status == 'Payment' || $trips->status == 'Rating') {
			$data = [
				'trip_id' 	=> $request->trip_id,
				'user_type' => $request->user_type,
				'user_id' 	=> $user->id,
				'save_to_trip_table' => $save,
			];
			//$trips = $this->invoice_helper->calculation($data);

			$trips = $this->invoice_helper->calculation_v2($data);
			return $this->invoice_helper->getInvoice($trips, $data);
		}

		CustomLog::info("get_invoice Api Stp:2 :");

		return response()->json([
			'status_code' 	 => '2',
			'status_message' => __('messages.api.something_went_wrong'),
		]);
	}

	/**
	 * Update the trip Rating given by Driver or Rider
	 * @param  Post method request inputs
	 *
	 * @return Response Json
	 */
	public function trip_rating(Request $request)
	{
		Log::info("trip_rating Api Stp:1 :", $request->all());
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'user_type' => 'required|in:Rider,rider,Driver,driver',
			'rating' => 'required',
			'trip_id' => 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return [
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			];
		}
		$user = User::where('id', $user_details->id)->first();

		$trips = Trips::where('id', $request->trip_id)->first();

		if ($user == '') {
			return response()->json([
				'status_code'	 => '0',
				'status_message' => __('messages.invalid_credentials'),
			]);
		}

		$rating = Rating::where('trip_id', $request->trip_id)->first();
		$user_type = strtolower($request->user_type);
		$trip = Trips::where('id', $request->trip_id)->first();

		if ($user_type == 'rider') {
			$data = [
				'trip_id' => $request->trip_id,
				'user_id' => $trips->user_id,
				'driver_id' => $trips->driver_id,
				'rider_rating' => $request->rating,
				'rider_comments' => @$request->rating_comments != '' ? $request->rating_comments : '',
			];

			Rating::updateOrCreate(['trip_id' => $request->trip_id], $data);

			$rider_user = $trip->users;
			$pushData['rider_name'] = $rider_user->first_name;
			$pushData['device_id'] = $rider_user->device_id;
			$pushData['device_type'] = $rider_user->device_type;
			$pushData['message_index'] = 'rating_finish';
			$this->pushNotificationService->tripPushNotificationMessage($pushData);
		} else {
			$data = [
				'trip_id' => $request->trip_id,
				'user_id' => $trips->user_id,
				'driver_id' => $trips->driver_id,
				'driver_rating' => $request->rating,
				'driver_comments' => @$request->rating_comments != '' ? $request->rating_comments : '',
			];
			Rating::updateOrCreate(['trip_id' => $request->trip_id], $data);
		}

		if (!in_array($trip->status, ['Rating', 'Payment'])) {
			return response()->json([
				'status_code' => '1',
				// 'status_code' => '2',
				// 'status_message' => __('messages.api.trip_already_completed'),
				'status_message' => __('messages.api.rating_successfully'),

			]);
		}
		// $trip->status = 'Payment';
		// $trip->status = 'Rating'; //Modifided
		$trip->status = 'Completed';

		if ($user_type == 'rider') {
			$currency_code = $user_details->currency->code;
			$tips 		= currencyConvert($currency_code, $trip->getOriginal('currency_code'), $request->tips);
			$trip->tips = $tips;
		}

		$trip->save();

		if ($trip->pool_id > 0) {

			$pool_trip = PoolTrip::with('trips')->find($trip->pool_id);
			$trips = $pool_trip->trips->whereIn('status', ['Scheduled', 'Begin trip', 'End trip', 'Rating'])->count();

			if (!$trips) {
				// update status
				$pool_trip->status = 'Payment';
				$pool_trip->save();
			}
		}

		CustomLog::info("trip_rating Api Stp:2 :");
		return response()->json([
			'status_code' => '1',
			'status_message' => "Rating successfully",
		]);
	}



	public function trip_comment()
	{
		Log::info("trip_comment Api Stp:1 :");
		$user_details = JWTAuth::parseToken()->authenticate();
		$comment = Comment::active()->where('comment_by', $user_details->user_type)->get();

		CustomLog::info("trip_comment Api Stp:2 :");
		return response()->json([
			'status_code' 	 => '1',
			'status_message' => "Success",
			'trip_comment' => $comment,
			'status_message2' => "Success2",
		]);
	}







	/**
	 * Display the Rider Feedback
	 * @param  Post method request inputs
	 *
	 * @return Response Json
	 */
	public function rider_feedback(Request $request)
	{
		Log::info("rider_feedback Api Stp:1 :", $request->all());

		$user_details = JWTAuth::parseToken()->authenticate();

		$page = $r->page ?? 1;
		$take = 15;
		$skip = ($take * $page) - $take;

		$rules = array(
			'user_type' => 'required|in:Driver,driver',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}
		$user = User::where('id', $user_details->id)->first();

		if ($user == '') {
			return response()->json([
				'status_code'	 => '0',
				'status_message' => __('messages.invalid_credentials'),
			]);
		}

		$rider_comments = DB::table('rating')
			->select(DB::raw('DATE_FORMAT(created_at, "%d %M %Y") AS date,rider_rating,rider_comments,trip_id'))
			->where('driver_id', $user_details->id)
			->where('rider_rating', '>', 0)
			->orderBy('trip_id', 'DESC')
			->skip($skip)
			->take($take)
			->get();

		CustomLog::info("rider_feedback Api Stp:2 :");


		return response()->json([
			'status_code' 	 => '1',
			'status_message' => __('messages.api.listed_successfully'),
			'rider_feedback' => $rider_comments,
		]);
	}
}
