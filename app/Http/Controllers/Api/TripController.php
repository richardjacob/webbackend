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
use Illuminate\Http\Request;
use App\Services\PushNotificationService;
use App\Models\Cancel;
use App\Models\DriverLocation;
use App\Models\ScheduleRide;
use App\Models\PoolTrip;
use App\Models\Trips;
use App\Models\CarType;
use App\Models\User;
use App\Models\Rating;
use App\Models\ManageFare;
use App\Models\Request as RideRequest;
use App\Models\UsersPromoCode;
use App\Models\CancelReason;
use App\Models\TollReason;
use App\Models\ApiCredentials;
use App\Models\TripTollReason;
use App\Models\Fees;
use App\Models\Vehicle;
use App\Models\Complain;
use DateTime;
use DB;
use File;
use JWTAuth;
use Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CustomLog;

class TripController extends Controller
{
	/**
	 * @var PushNotificationService
	 */
	private $pushNotificationService;

	public function __construct(PushNotificationService $pushNotificationService)
	{
		DB::enableQueryLog();
		$this->request_helper = resolve('App\Http\Helper\RequestHelper');
		$this->invoice_helper = resolve('App\Http\Helper\InvoiceHelper');
		$this->bonus_helper = resolve('App\Http\Helper\BonusHelper');
		$this->rating_controller = resolve('App\Http\Controllers\Api\RatingController');
		$this->paginate_limit = 5;
		$this->pushNotificationService = $pushNotificationService;
	}

	protected function checkPendingTrips($user)
	{
		Log::info("checkPendingTrips :" . json_encode($user));

		if ($user->user_type == 'Rider') {
			$incomplete_trips = Trips::where('user_id', $user->id)->whereIn('status', ['Scheduled', 'Begin trip', 'End trip'])->orderBy('id', 'desc')->first();
		} else {
			$incomplete_trips = Trips::where('driver_id', $user->id)->whereIn('status', ['Scheduled', 'Begin trip', 'End trip'])->orderBy('id', 'desc')->first();
		}

		CustomLog::info("checkPendingTrips :");

		return $incomplete_trips->id ?? 0;
	}

	protected function getTripDetails($trip_id, $user, $is_trip_id = false, $response = true, $riders_only = false)
	{
		
		Log::info("getTripDetails Stp:1 :");

		if (!is_object($trip_id)) {
			// $trip = Cache()->remember('trip_id'.$trip_id, Config('cache.one_day'), function() use($trip_id){
			//                  return Trips::with('car_type.manage_fare','users','driver.driver_location')->where('id', $trip_id)->first();
			//              });

			$trip = Trips::with('car_type.manage_fare', 'users', 'driver.driver_location')->where('id', $trip_id)->first();
		} else {
			// $trip = Cache()->remember('trip_id'.$trip_id, Config('cache.one_day'), function() use($trip_id){
			//                  return ScheduleRide::with('users','driver.driver_location')->where('id', $trip_id->id)->first();
			//              });

			$trip = ScheduleRide::with('users', 'driver.driver_location')->where('id', $trip_id->id)->first();
		}

		$driver = $trip->driver ?? '';
		$driver_location = $driver->driver_location ?? '';
		$arrival_time = -1;

		$invoice_data = array('user_id' => $user->id, 'user_type' => $user->user_type);

		$vehicle_number = $driver->driver_documents->vehicle_number;
		if ($vehicle_number != '') {
			$vehicle_number_in_english = '0';
			if ($user->user_type == 'Rider') $vehicle_number_in_english = '1';
			else if ($user->user_type == 'Driver' and $user->language != 'bn') $vehicle_number_in_english = '1';
			if ($vehicle_number_in_english == '1') $vehicle_number = vehicle_number_en($vehicle_number);
		} else $vehicle_number = '';


		$trip_data = array(
			'driver_latitude' 	=> $driver->driver_location->latitude ?? '',
			'driver_longitude' 	=> $driver->driver_location->longitude ?? '',
			'vehicle_number' 	=>  $vehicle_number,
			'vehicle_number_bn' =>  $driver->driver_documents->vehicle_number ?? '',
			'vehicle_name' 		=> $driver->driver_documents->vehicle_name ?? '',
			'arrival_time' 		=> $arrival_time,
		);

		if (!isset($trip->booking_type)) {
			$trip_data['pool_id'] = $trip->pool_id;
			$trip_data['is_pool'] = $trip->pool_id > 0 ? true : false;
		} else {
			$trip_data['pool_id'] = '';
			$trip_data['is_pool'] = false;
		}

		// Set Waiting time and charge based on car type when trip is not completed
		if (in_array($trip->status, ['Completed', 'Rating'])) {
			$trip_data['waiting_charge'] = $trip->waiting_charge;
		}

		if ($user->user_type == 'Rider') {
			$driver_rating = getRiderRating($trip->driver_id);
			$final_promo_details = $this->invoice_helper->getUserPromoDetails($user->id);
			$user_data = array(
				'driver_id' 		=> $driver->id ?? '',
				'driver_name' 		=> $driver->first_name ?? '',
				'mobile_number' 	=> substr($driver->phone_number, 3, 11) ?? '',
				'driver_thumb_image' => @$driver->profile_picture->src ?? url('images/user.jpeg'),
				'rating'	 		=> $driver_rating,
				'promo_details' 	=> $final_promo_details,
			);
		} else {
			$user_data = array();
		}

		$other_data = array(
			'paypal_mode' 	=> PAYPAL_MODE,
			'paypal_app_id' => PAYPAL_CLIENT_ID,
		);

		$trip_id = $trip->id;
		if ($trip->pool_id > 0 && !$is_trip_id) {
			$pool_trip = PoolTrip::with(['trips' => function ($query) use ($user) {
				if ($user->user_type == 'Rider') {
					$query->where('user_id', $user->id);
				} else {
					$query->with('driver_location')->select(
						\DB::raw(
							'*,
							(CASE 
							WHEN status="Scheduled" OR status="Begin trip" THEN ( 6371 * acos( cos( radians(' . $query->get()[0]->driver_location->latitude . ') ) * cos( radians( pickup_latitude ) ) * cos(radians( pickup_longitude ) - radians(' . $query->get()[0]->driver_location->longitude . ') ) + sin( radians(' . $query->get()[0]->driver_location->latitude . ') ) * sin( radians( pickup_latitude ) ) ) )
							ELSE ( 6371 * acos( cos( radians(' . $query->get()[0]->driver_location->latitude . ') ) * cos( radians( drop_latitude ) ) * cos(radians( drop_longitude ) - radians(' . $query->get()[0]->driver_location->longitude . ') ) + sin( radians(' . $query->get()[0]->driver_location->latitude . ') ) * sin( radians( drop_latitude ) ) ) ) END) as distance'
						)
					)
						->orderBy('distance', 'asc');
				}
			}])->find($trip->pool_id);

			$trips = $pool_trip->trips->whereIn('status', ['Scheduled', 'Begin trip', 'End trip']);
		} else {
			$trips[] = $trip;
		}

		$data = array();
		if (count($trips)) {
			foreach ($trips as $tkey => $trip) {
				$complain = Complain::where('trip_id', $trip->id)->first();
				if(is_object($complain)) $complain_id = (string) ($complain->id);
				else $complain_id = '';

				// $data[$tkey]['distance']     = $trip->distance;
				$data[$tkey]['id']     = $trip->users->id;

				//$data[$tkey]['trip_start_date'] = $trip->created_at->format('d M');
				$data[$tkey]['trip_start_time'] = $trip->created_at->format("d/m/y  H:i A");

				$data[$tkey]['name']   = $trip->users->first_name . ' ' . $trip->users->last_name;
				$data[$tkey]['image']  = $trip->users->profile_picture->src ?? url('images/user.jpeg');
				$data[$tkey]['mobile_number'] = $trip->users->phone_number;
				$data[$tkey]['otp'] 	= $trip->otp;
				$data[$tkey]['status'] = $trip->status ?? '';
				$data[$tkey]['trip_id'] = $trip->id;
				$data[$tkey]['complain_id'] = $complain_id;				
				$data[$tkey]['pickup'] = $trip->pickup_location;
				$data[$tkey]['drop']   = $trip->drop_location;

				$data[$tkey]['pickup_lat'] = $trip->pickup_latitude;
				$data[$tkey]['pickup_lng'] = $trip->pickup_longitude;
				$data[$tkey]['drop_lat']   = $trip->drop_latitude;
				$data[$tkey]['drop_lng']   = $trip->drop_longitude;
				$data[$tkey]['request_id'] = $trip->request_id;
				$data[$tkey]['trip_path'] = $trip->trip_path;
				$data[$tkey]['map_image'] = $trip->map_image;

				if (!isset($trip->booking_type)) {
					$car_type = $trip->car_type;
				} else {
					$car_type = CarType::find($trip->car_id);
				}

				$data[$tkey]['car_type'] = $car_type->car_name;
				$data[$tkey]['car_active_image'] = $car_type->active_image;
				$data[$tkey]['waiting_time'] = strval(@$car_type->manage_fare->waiting_time);
				$data[$tkey]['waiting_charge'] = @$car_type->manage_fare->waiting_charge;

				$data[$tkey]['total_time'] 	= $trip->total_time;
				$data[$tkey]['total_km'] 	= $trip->total_km;
				$data[$tkey]['begin_trip'] 	= $trip->begin_trip;
				$data[$tkey]['end_trip'] 	= $trip->end_trip;
				$data[$tkey]['payment_mode'] = $trip->payment_mode;
				$data[$tkey]['payment_status'] = $trip->payment_status;
				$data[$tkey]['estimate_fare'] = $trip->estimate_fare;

				if (!isset($trip->booking_type)) {
					$symbol = html_entity_decode($trip->currency->symbol);
				} else {
					$symbol = html_entity_decode($trip->currency_symbol);
				}

				$data[$tkey]['currency_symbol'] = $symbol;

				$subtotal_fare = checkIsCashTrip($trip->payment_mode) ? $trip->total_fare : $trip->subtotal_fare;
				$data[$tkey]['sub_total_fare'] = $subtotal_fare;

				if (!isset($trip->booking_type)) {
					$total_fare = $trip->admin_total_amount;
					if (isset($trip->driver) && $trip->driver->company_id != 1 && checkIsCashTrip($trip->payment_mode) && $trip->total_fare == 0) {
						$total_fare = $trip->company_driver_earnings;
					}
				} else {
					$total_fare = $trip->fare_estimation ?? '0';
				}
				$driver_earnings = $symbol . number_format($trip->company_driver_earnings, 2);
				if ($trip->status == "Scheduled" || $trip->status == "Begin trip" || $trip->status == "End trip") {
					$data[$tkey]['total_fare'] = (string)$trip->estimate_fare;
					$data[$tkey]['driver_earnings'] = (string) $trip->estimate_fare;
				} else if ($trip->status == "Payment") {
					$trip_data_payment = [
						'trip_id' 	=> $trip->id,
						'user_type' => $user->user_type,
						'user_id' 	=> $user->id,
						'save_to_trip_table' => 0,
					];
					//$trip_calculation_payment = $this->invoice_helper->calculation($trip_data_payment);

					$trip_calculation_payment = $this->invoice_helper->calculation_v2($trip_data_payment);

					$totalFare = json_decode(json_encode($this->invoice_helper->getInvoice($trip_calculation_payment, $trip_data_payment)));
					$data[$tkey]['total_fare'] = (string)$totalFare->original->total_fare;
					$data[$tkey]['driver_earnings'] = (string)$totalFare->original->total_fare;
				} else {
					$data[$tkey]['total_fare'] = (string)$total_fare;
					$data[$tkey]['driver_earnings'] = (string)$driver_earnings;
				}

				$data[$tkey]['driver_payout'] = $trip->driver_payout;

				if (!isset($trip->booking_type)) {
					$data[$tkey]['booking_type'] = @$trip->ride_request->schedule_ride->booking_type ?? '';
				} else {
					$data[$tkey]['booking_type'] = 'Manual Booking';
				}

				$data[$tkey]['created_at'] 	= $trip->created_at ? $trip->created_at->format('Y-m-d H:i:s') : '';
				$data[$tkey]['is_current_trip'] = $trip->id == $trip_id;
				// $data[$tkey]['rating'] 	= getDriverRating($trip->users->id);
				$driver_rating_data = Rating::select(\DB::raw('IFNULL(rider_rating,0) as rider_rating'))->where('trip_id', $trip_id)->first();
				$rider_rating_data = Rating::select(\DB::raw('IFNULL(driver_rating,0) as driver_rating'))->where('trip_id', $trip_id)->first();

				// $data[$tkey]['driver_rating'] 	=  $driver_rating_data['rider_rating'] ?? '0';
				// $data[$tkey]['rider_rating'] 	=  $rider_rating_data['driver_rating'] ?? '0';

				//if(isset($driver_rating_data) AND array_key_exists('rider_rating', $driver_rating_data)){
				//if (!is_null($driver_rating_data['rider_rating'])){
				if (is_object($driver_rating_data)) {
					//$driver_rating=floatval($driver_rating_data['rider_rating']);
					$driver_rating = number_format($driver_rating_data->rider_rating, 2, '.', '');
				} else {
					$driver_rating = number_format(0, 2, '.', ''); //round(0, 2);
				}

				//if(isset($rider_rating_data) AND array_key_exists('driver_rating', $rider_rating_data)){
				//if (!is_null($rider_rating_data['driver_rating'])){
				if (is_object($rider_rating_data)) {
					//$rider_rating=floatval($rider_rating_data['driver_rating']);
					$rider_rating = number_format($rider_rating_data->driver_rating, 2, '.', '');
				} else {
					$rider_rating =  number_format(0, 2, '.', ''); //round(0, 2);
				}


				// $data[$tkey]['driver_rating'] 	=  floatval($driver_rating_data['rider_rating'])  ?? round(0, 2);
				// $data[$tkey]['rider_rating'] 	=  floatval($rider_rating_data['driver_rating']) ?? round(0, 2);

				$data[$tkey]['driver_rating'] 	=  (float)($driver_rating);
				$data[$tkey]['rider_rating'] 	=  (float)($rider_rating);

				$data[$tkey]['schedule_display_date'] = $trip->schedule_display_date;

				if (isset($trip->driver))
					$invoice = $this->invoice_helper->formatInvoice_v2($trip, $invoice_data);
				else
					$invoice = array();

				$data[$tkey]['invoice'] = $invoice;
				$data[$tkey]['otp_enabled'] = site_settings('otp_verification') == '1' ? true : false;
			}
		}

		$trip_data['riders'] = array_values($data);

		if ($riders_only)
			return $trip_data['riders'];

		if ($response == false)
			return array_merge($trip_data, $user_data, $other_data);

		CustomLog::info("getTripDetails Stp:2 :");

		return [
			'status' => true,
			'data' => array_merge($trip_data, $user_data, $other_data),
		];
	}

	/**
	 * Common Function to Map Trips Details
	 * 
	 * @param Collection $trips
	 * @return Collection Formatted Trips
	 */
	protected function mapTripDetails($trips, $user)
	{
		Log::info("mapTripDetails Stp:1 :");
		$return = $trips->map(function ($trip) use ($user) {
			if (isset($trip->booking_type)) {
				return $this->getTripDetails($trip, $user, 1, 0);
			} else {
				return $this->getTripDetails($trip->id, $user, 1, 0);
			}
		});
		CustomLog::info("mapTripDetails Stp:2 :");
		return $return;
	}


	/**
	 * Display the Arrive Now Status
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function arriveNow(Request $request)
	{
		Log::info("arive_now Api Stp:1 :", $request->all());

		//$user_details = JWTAuth::parseToken()->authenticate();
		$user_details = auth()->user();


		$rules = array(
			'trip_id' => 'required|exists:trips,id',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		$trip = Trips::where('id', $request->trip_id)->first();

		$user_timezone = $trip->ride_request->timezone;
		if ($user_timezone != '') {
			date_default_timezone_set($user_timezone);
		}

		$arrive_time = new DateTime(date("Y-m-d H:i:s"));

		Trips::where('id', $request->trip_id)->update(['arrive_time' => $arrive_time, 'status' => 'Begin trip']);

		if ($trip->pool_id > 0) {

			$pool_trip = PoolTrip::with('trips')->find($trip->pool_id);
			$scheduled_trips = $pool_trip->trips->where('status', 'Scheduled')->count();

			if (!$scheduled_trips) {
				// update seats
				$pool_trip->status = 'Begin trip';
				$pool_trip->save();
			}
		}

		$push_data['push_title'] = __('messages.api.driver_arrived');
		$push_data['data'] = array(
			'arrive_now' => array(
				'status' => 'Arrive Now',
				'trip_id' => $request->trip_id,
			)
		);
		$this->request_helper->SendPushNotification($trip->users, $push_data);

		$schedule_ride = ScheduleRide::find($trip->ride_request->schedule_id);

		//if booking is manual booking then send "Driver Arrived" SMS to rider
		if (isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {
			/*$fare_details = ManageFare::where('location_id',$schedule_ride->location_id)->where('vehicle_id',$schedule_ride->car_id)->first();
			$waiting_time = $fare_details->waiting_time;
			$waiting_charge = $fare_details->currency_code.' '.$fare_details->waiting_charge;

			$push_title = __('messages.driver_arrive');
	        $text 		= __('messages.driver_arrived').' '.__('messages.api.waiting_charge_apply_after',['amount' => $waiting_charge,'minutes' => $waiting_time]);

	        $push_data['push_title'] = $push_title;
	        $push_data['data'] = array(
	            'custom_message' => array(
	                'title' => $push_title,
	                'message_data' => $text,
	            )
	        );

	        $this->request_helper->checkAndSendMessage($data->users,$text,$push_data);*/
		}

		$trip_detail = $this->getTripDetails($request->trip_id, $user_details);

		CustomLog::info("arive_now Api Stp:2 :");

		return response()->json(array_merge(array('status_code' => '1', 'status_message' => "Success"), $trip_detail['data']));
	}

	/**
	 * Begin Trip From Driver
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function beginTrip(Request $request)
	{
		Log::info("begin_trip Api Stp:1 :", $request->all());

		//$user_details = JWTAuth::parseToken()->authenticate();
		$user_details = auth()->user();


		$rules = array(
			'trip_id' => 'required|exists:trips,id',
			'begin_latitude' => 'required',
			'begin_longitude' => 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}
		$pickup_location = $this->request_helper->GetLocation($request->begin_latitude, $request->begin_longitude);

		$user_location = DriverLocation::where('user_id', $user_details->id)->first();

		$trip = Trips::where('id', $request->trip_id)->first();

		$user_timezone = $trip->ride_request->timezone;
		if ($user_timezone != '') {
			date_default_timezone_set($user_timezone);
		}

		$begin_time = new DateTime(date("Y-m-d H:i:s"));

		Trips::where('id', $request->trip_id)->update(['status' => 'End trip', 'begin_trip' => $begin_time, 'pickup_latitude' => $request->begin_latitude, 'pickup_longitude' => $request->begin_longitude, 'pickup_location' => $pickup_location, 'otp' => 0]);

		$trip = Trips::where('id', $request->trip_id)->first();

		if ($trip->pool_id > 0) {

			$pool_trip = PoolTrip::with('trips')->find($trip->pool_id);
			$trips = $pool_trip->trips->whereIn('status', ['Scheduled', 'Begin trip'])->count();

			if (!$trips) {
				// update status
				$pool_trip->status = 'End trip';
				$pool_trip->save();
			}
		}
		$vehicle_number = Vehicle::where('user_id', $trip->driver_id)
			->where('vehicle_id', $trip->car_id)->pluck('vehicle_number')->first();

		//vehicle name in english
		$vehicle_number_in_english = '0';
		if ($user_details->user_type == 'Rider') $vehicle_number_in_english = '1';
		else if ($user_details->user_type == 'Driver' and $user_details->language != 'bn') $vehicle_number_in_english = '1';
		if ($vehicle_number_in_english == '1') $vehicle_number = vehicle_number_en($vehicle_number);

		$push_data['push_title'] = __('messages.api.trip_begin_by_driver');
		$push_data['data'] = array(
			'begin_trip' => array(
				'trip_id' => $request->trip_id,
				'vehicle_number' => $vehicle_number
			)
		);

		$this->request_helper->SendPushNotification($trip->users, $push_data);

		$schedule_ride = ScheduleRide::find($trip->ride_request->schedule_id);
		//if booking is manual booking then send "Trip Began" SMS to rider
		if (isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {
			$push_title = __('messages.trip_begined');
			$text 		= __('messages.trip_begined');

			$push_data['push_title'] = $push_title;
			$push_data['data'] = array(
				'custom_message' => array(
					'title' => $push_title,
					'message_data' => $text,
				)
			);

			$this->request_helper->checkAndSendMessage($trip->users, $text, $push_data);
		}
		$trip_detail = $this->getTripDetails($request->trip_id, $user_details);

		$pushData['device_id'] = $trip->users->device_id;
		$pushData['device_type'] = $trip->users->device_type;
		$pushData['message_index'] = 'verify_pin';
		$this->pushNotificationService->tripPushNotificationMessage($pushData);


		CustomLog::info("begin_trip Api Stp:2 :");

		return response()->json(array_merge(array('status_code' => '1', 'status_message' => "Trip Started"), $trip_detail['data']));
	}

	/**
	 * End Trip From Driver
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function end_trip(Request $request)
	{
		Log::info("end_trip Api Stp:1 :", $request->all());

		//$user_details = JWTAuth::parseToken()->authenticate();
		$user_details = auth()->user();


		$rules = array(
			'trip_id' => 'required|exists:trips,id',
			'end_latitude' => 'required',
			'end_longitude' => 'required',
		);

		$fees = resolve('fees');

		$apply_extra_fee = @$fees->where('name', 'additional_fee')->first()->value;
		if ($apply_extra_fee == 'Yes') {
			$rules = array(
				'trip_id' => 'required|exists:trips,id',
				'end_latitude' => 'required',
				'end_longitude' => 'required',
				'toll_reason_id' => 'required_with:toll_fee|exists:toll_reasons,id',
				'toll_reason' => 'required_if:toll_reason_id,1',
				'toll_fee' => 'required_with:toll_reason_id',
			);
		}

		$messages = array(
			'toll_reason.required' => __('messages.api.toll_reason') . ' ' . __('messages.field_is_required'),
		);

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}


		$trip = Trips::find($request->trip_id);

		$toll_fee = 0;
		if ($request->toll_fee) {
			$toll_fee = $request->toll_fee;
		}

		// Final Distance calcualtion
		$driver_location = DriverLocation::where('user_id', $user_details->id)->first();

		$user_timezone = $trip->ride_request->timezone;
		if ($user_timezone != '') {
			date_default_timezone_set($user_timezone);
		}

		if ($request->trip_id) {
			$total_km = number_format($request->total_km, 5, '.', '0');
			$data = [
				'user_id' 	=> $user_details->id,
				'latitude' 	=> $request->end_latitude,
				'longitude' => $request->end_longitude,
			];

			DriverLocation::updateOrCreate(['user_id' => $user_details->id], $data);
		}

		$end_time = new DateTime(date("Y-m-d H:i:s"));
		$drop_location = $this->request_helper->GetLocation($request->end_latitude, $request->end_longitude);

		if ($request->hasFile('image')) {
			$image_uploader = resolve('App\Contracts\ImageHandlerInterface');

			$target_dir = '/images/map/' . $trip->id;
			$image 		= $request->file('image');
			$extension 	= $image->getClientOriginalExtension();
			$file_name 	= substr(md5(uniqid(rand(), true)), 0, 8) . "." . $extension;
			$options 	= compact('target_dir', 'file_name');

			$upload_result = $image_uploader->upload($image, $options);
			if (!$upload_result['status']) {
				return response()->json([
					'status_code' => "0",
					'status_message' => $upload_result['status_message'],
				]);
			}
			$image_url = url('/') . '/images/map/' . $trip->id . '/' . $file_name;
		}

		$status = 'Payment';
		// $status = 'Rating';
		$trip_data = [
			'drop_latitude'	=> $request->end_latitude,
			'drop_longitude' => $request->end_longitude,
			'drop_location' => $drop_location,
			'status' 		=> $status,
			'end_trip' 		=> $end_time,
			'total_km' 		=> $total_km,
			'map_image' 	=> $file_name ?? '',
			'toll_fee'		=> $toll_fee
		];

		if ($request->toll_reason_id) {
			$trip_data['toll_reason_id'] = $request->toll_reason_id;
		}

		Trips::where('id', $request->trip_id)->update($trip_data);
		if ($request->toll_reason_id == 1 && $request->toll_reason) {
			$trip_toll_reason = new TripTollReason();
			$trip_toll_reason->trip_id = $request->trip_id;
			$trip_toll_reason->reason = $request->toll_reason;
			$trip_toll_reason->save();
		}

		$push_title = __('messages.api.trip_ended_by_driver');
		$driver_thumb_image = @$trip->driver_thumb_image != '' ? $trip->driver_thumb_image : url('images/user.jpeg');
		$push_data = array('end_trip' => array('trip_id' => $request->trip_id, 'driver_thumb_image' => $driver_thumb_image));
		$user_type = $trip->users->user_type;
		$device_id = $trip->users->device_id;

		//Send push notification
		if ($trip->users->device_type != null && $trip->users->device_type != '') {
			if ($trip->users->device_type == 1) {
				$this->request_helper->push_notification_ios($push_title, $push_data, $user_type, $device_id);
			} else {
				$this->request_helper->push_notification_android($push_title, $push_data, $user_type, $device_id);
			}
		}

		//if booking is manual booking then send "Trip Ended" SMS to rider
		if (isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {
			$data = [
				'trip_id' 	=> $request->trip_id,
				'user_type' => $request->user_type,
				'user_id' 	=> $user_details->id,
				'save_to_trip_table' => 0,
			];

			$push_title = __('messages.trip_ended');
			$text 		= __('messages.trip_ended');

			$push_data['push_title'] = $push_title;
			$push_data['data'] = array(
				'custom_message' => array(
					'title' => $push_title,
					'message_data' => $text,
				)
			);

			$this->request_helper->checkAndSendMessage($trip->users, $text, $push_data);
		}

		if ($driver_location->status != 'Pool Trip') {
			DriverLocation::where('user_id', $user_details->id)->update(['status' => 'Online']);
		} else {

			$pool_trip = PoolTrip::with('trips')->find($driver_location->pool_trip_id);

			// update seats
			$pool_trip->seats = $pool_trip->seats + $trip->ride_request->seats;

			// get pending pool trips count
			$trips = $pool_trip->trips->whereIn('status', ['Scheduled', 'Begin trip', 'End trip'])->count();
			if (!$trips) {
				// update status
				$pool_trip->status = 'Rating';
				// update driver location once pool trip end
				DriverLocation::where('user_id', $user_details->id)->update(['pool_trip_id' => NULL, 'status' => 'Online']);
			}

			$polyline = $this->request_helper->GetPolyline($pool_trip->pickup_latitude, $pool_trip->drop_latitude, $pool_trip->pickup_longitude, $pool_trip->drop_longitude);

			$pool_trip->trip_path = $polyline;
			$pool_trip->save();
		}

		$trip_detail = $this->getTripDetails($request->trip_id, $user_details);
		//dd($trip_detail['data']['riders']['name']);
		CustomLog::info("end_trip Api Stp:2 :");

		$data = [
			'trip_id' 	=> $request->trip_id,
			'user_type' => 'Rider',
			'user_id' 	=> $trip->user_id,
			'save_to_trip_table' => 0,
		];
		//$trip_calculation = $this->invoice_helper->calculation($data);

		$trip_calculation = $this->invoice_helper->calculation_v2($data);


		$trip_calculation_invoice = json_decode(json_encode($this->invoice_helper->getInvoice($trip_calculation, $data)));
		$pushData['amount'] = $trip_calculation_invoice->original->total_fare;
		$pushData['device_id'] = $trip->users->device_id;
		$pushData['device_type'] = $trip->users->device_type;
		$pushData['message_index'] = 'trip_end';
		$this->pushNotificationService->tripPushNotificationMessage($pushData);

		// //All Bonus
		$user_driver = User::find($trip->driver_id);
		$this->bonus_helper->end_trip_bonus_update_driver($user_driver);
		// $driver_result = $this->bonus_helper->adjust_driver_signup_bonus($user_driver);
		// if(!$driver_result){
		// 	$driver_result2 = $this->bonus_helper->adjust_driver_referral_bonus($user_driver);
		// 	if(!$driver_result2){
		// 		$this->bonus_helper->adjust_driver_trip_bonus($user_driver);
		// 	}
		// }


		$user_rider = User::find($trip->user_id);
		$this->bonus_helper->end_trip_bonus_update_rider($user_rider);
		// $rider_result = $this->bonus_helper->adjust_rider_referral_bonus($user_rider);
		// if(!$rider_result){
		// 	$rider_result2 = $this->bonus_helper->adjust_rider_cashback1($user_rider);
		// 	if(!$rider_result2){
		// 		$this->bonus_helper->adjust_rider_cashback2($user_rider);
		// 	}
		// }


		return response()->json(array_merge(array('status_code' => '1', 'status_message' => "Trip Completed", 'image_url' => $image_url ?? ''), $trip_detail['data']));
	}

	/**
	 * Display the Past Trips of Rider
	 * @param Get method request inputs
	 *
	 * @return Response Json
	 */
	public function get_past_trips(Request $request)
	{
		Log::info("get_past_trips Api Stp:1 :", $request->all());

		//$user_details = JWTAuth::parseToken()->authenticate();
		$user_details = auth()->user();


		$rules = array(
			'page' => 'required|min:1',
		);

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		//$trips = Trips::with('car_type', 'currency', 'ride_request.schedule_ride')->where('user_id', $user_details->id)->orderBy('id', 'DESC')->paginate($this->paginate_limit)->toJson();
		$trips = DB::table('trips')->where('user_id', $user_details->id)->orderBy('id', 'DESC')->paginate($this->paginate_limit)->toJson();

		$data_result = json_decode($trips);

		if ($data_result->total == 0 || empty($data_result->data)) {
			return response()->json([
				'status_code' 		=> '0',
				'status_message' 	=> trans('messages.api.no_data_found'),
			]);
		}
		$trip_result = collect($data_result->data);
		$result_data = $this->mapTripDetails($trip_result, $user_details);

		CustomLog::info("get_past_trips Api Stp:2 :");

		return response()->json([
			'status_code'		=>	'1',
			'status_message'	=>	__('messages.api.listed_successfully'),
			'current_page'		=>  $data_result->current_page,
			'total_pages'		=>  $data_result->last_page,
			'data'				=>	$result_data,
		]);
	}

	/**
	 * Display the Upcoming Trips of Rider
	 * @param Get method request inputs
	 *
	 * @return Response Json
	 */
	public function get_upcoming_trips(Request $request)
	{
		Log::info("get_upcoming_trips Api Stp:1 :", $request->all());

		//$user_details = JWTAuth::parseToken()->authenticate();
		$user_details = auth()->user();


		$rules = array(
			'page' => 'required|min:1',
		);

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}
		$schedule = ScheduleRide::where('user_id', $user_details->id)->where('status', 'Pending')->orderBy('id', 'DESC')->paginate($this->paginate_limit);
		$schedule->getCollection()->transformWithAppends(['fare_estimation']);
		$schedule = $schedule->toJson();

		$data_result = json_decode($schedule);
		if ($data_result->total == 0 || empty($data_result->data)) {
			return response()->json([
				'status_code' 		=> '0',
				'status_message' 	=> trans('messages.api.no_data_found'),
			]);
		}
		$trip_result = collect($data_result->data);

		$result_data = $this->mapTripDetails($trip_result, $user_details);

		CustomLog::info("get_upcoming_trips Api Stp:2 :");

		return response()->json([
			'status_code'		=>	'1',
			'status_message'	=>	__('messages.api.listed_successfully'),
			'current_page'		=>  $data_result->current_page,
			'total_pages'		=>  $data_result->last_page,
			'data'				=>	$result_data,
		]);
	}

	/**
	 * Display the Pending Trips For the Driver
	 * 
	 * @param Get method request inputs
	 * @return Response Json
	 */
	public function get_pending_trips(Request $request)
	{
		Log::info("get_pending_trips Api Stp:1 :", $request->all());

		//$user_details = JWTAuth::parseToken()->authenticate();
		$user_details = auth()->user();

		$rules = array(
			'page' => 'required|min:1',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		//$trips = Trips::with('car_type', 'currency', 'ride_request.schedule_ride')->where('driver_id', $user_details->id)->orderBy('id', 'DESC')->whereNotIn('status', ['Completed', 'Cancelled'])->get();
		//$trips = DB::table('trips')->where('driver_id', $user_details->id)->orderBy('id', 'DESC')->whereNotIn('status', ['Completed', 'Cancelled'])->get();
		$trips = Trips::with('ride_request.schedule_ride')->where('driver_id', $user_details->id)->orderBy('id', 'DESC')->whereNotIn('status', ['Completed', 'Cancelled'])->get();


		$schedule = ScheduleRide::where('driver_id', $user_details->id)->where('status', 'Pending')->get();
		$trips_data = $schedule->merge($trips);

		$data_result = $trips_data->paginate($this->paginate_limit);

		if ($data_result->isEmpty()) {
			return response()->json([
				'status_code' 		=> '0',
				'status_message' 	=> trans('messages.api.no_data_found'),
			]);
		}

		$trip_result = collect($data_result->items())->values();
		$result_data = $this->mapTripDetails($trip_result, $user_details);

		CustomLog::info("get_pending_trips Api Stp:2 :");

		return response()->json([
			'status_code'		=>	'1',
			'status_message'	=>	__('messages.api.listed_successfully'),
			'current_page'		=>  $data_result->currentPage(),
			'total_pages'		=>  $data_result->lastPage(),
			'data'				=>	$result_data,
		]);
	}

	/**
	 * Display the Completed Trips
	 * 
	 * @param Get method request inputs
	 * @return Response Json
	 */
	public function get_completed_trips(Request $request)
	{
		Log::info("get_completed_trips Api Stp:1 :", $request->all());

		$user_details = JWTAuth::parseToken()->authenticate();
		// $user_details = auth()->user();
		$user_details = DB::table('users')->find($user_details->id);

		$rules = array(
			'page' => 'required|min:1',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		// $trips = Trips::with('car_type', 'currency', 'ride_request.schedule_ride')->where('driver_id', $user_details->id)->orderBy('id', 'DESC')->whereIn('status', ['Completed', 'Cancelled'])->paginate($this->paginate_limit)->toJson();
		$trips = DB::table('trips')->where('driver_id', $user_details->id)->orderBy('id', 'DESC')->whereIn('status', ['Completed', 'Cancelled'])->paginate($this->paginate_limit)->toJson();

		$data_result = json_decode($trips);
		if ($data_result->total == 0 || empty($data_result->data)) {
			return response()->json([
				'status_code' 		=> '0',
				'status_message' 	=> trans('messages.api.no_data_found'),
			]);
		}
		$trip_result = collect($data_result->data);
		$result_data = $this->mapTripDetails($trip_result, $user_details);

		CustomLog::info("get_completed_trips Api Stp:2 :");

		return response()->json([
			'status_code'		=>	'1',
			'status_message'	=>	__('messages.api.listed_successfully'),
			'current_page'		=>  $data_result->current_page,
			'total_pages'		=>  $data_result->last_page,
			'data'				=>	$result_data,
		]);
	}

	/**
	 * Map Image upload
	 * @param  Post method request inputs
	 *
	 * @return Response Json
	 */
	public function map_upload(Request $request)
	{
		Log::info("map_upload Api Stp:1 :", $request->all());

		//$user = JWTAuth::parseToken()->authenticate();
		$user = auth()->user();
		$rules = array(
			'trip_id' => 'required|exists:trips,id',
			'image' => 'required',
			'token' => 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		$user_id = $user->id;
		$trip_id = $request->trip_id;
		$image_uploader = resolve('App\Contracts\ImageHandlerInterface');
		$target_dir = '/images/map/' . $trip_id;

		if ($request->hasFile('image')) {
			$image = $request->file('image');

			$extension = $image->getClientOriginalExtension();
			$file_name = substr(md5(uniqid(rand(), true)), 0, 8) . "." . $extension;

			$options = compact('target_dir', 'file_name');

			$upload_result = $image_uploader->upload($image, $options);
			if (!$upload_result['status']) {
				return response()->json([
					'status_code' 		=> "0",
					'status_message' 	=> $upload_result['status_message'],
				]);
			}

			Trips::where('id', $request->trip_id)->update(['map_image' => $file_name]);

			$image_url = url('/') . '/images/map/' . $trip_id . '/' . $file_name;

			CustomLog::info("map_upload Api Stp:2 :");

			return response()->json([
				'status_code' 		=> "1",
				'status_message' 	=> "Upload Successfully",
				'image_url' 		=> $image_url,
			]);
		}
	}

	/**
	 * Trip Cancel by Driver or Rider
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function cancel_trip(Request $request)
	{
		Log::info("cancel_trip Api Stp:1 :", $request->all());

		// $user_details = Cache()->remember('user_details-' . $request->trip_id, Config('cache.one_day'), function () {
		// 	return JWTAuth::parseToken()->authenticate();
		// });
		//$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'user_type' => 'required|in:Rider,rider,Driver,driver',
			'trip_id' => 'required',
			'cancel_reason_id' => 'required',
		);

		$messages = array(
			'user_type.required' => trans('messages.required.user_type') . ' ' . trans('messages.field_is_required') . '',
			'trip_id.required' => trans('messages.required.trip_id') . ' ' . trans('messages.field_is_required') . '',
			'cancel_reason_id.required' => trans('messages.required.cancel_reason_id') . ' ' . trans('messages.field_is_required') . '',
		);

		$validator = Validator::make($request->all(), $rules, $messages);

		$validator->after(function ($validator) use ($request) {
			$cancelled_by = 'Driver';
			if ($request->user_type == 'Rider' || $request->user_type == 'rider') {
				$cancelled_by = 'Rider';
			}

			$cancel_reason_exists = CancelReason::active()->where('cancelled_by', $cancelled_by)->where('id', $request->cancel_reason_id)->exists();
			if (!$cancel_reason_exists) {
				$validator->errors()->add('cancel_reason_id', __('messages.api.reason_inactive_admin'));
			}
		});

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		// $user = User::where('id', $user_details->id)->where('user_type', $request->user_type)->first();

		// if ($user == '') {
		// 	return response()->json([
		// 		'status_code' 	 => '0',
		// 		'status_message' => "Invalid credentials",
		// 	]);
		// }

		$cancelled_id = Trips::where('id', $request->trip_id)->first();

		if ($cancelled_id->status == 'Cancelled') {
			return response()->json([
				'status_code' 	 => '0',
				'status_message' => 'Trips already cancelled',
			]);
		}

		$user_type = strtolower($request->user_type);
		if ($user_type == 'rider') {
			$data = [
				'trip_id' => $request->trip_id,
				'user_id' => auth()->user()->id,
				'cancel_comments' => @$request->cancel_comments != '' ? $request->cancel_comments : '',
				'cancelled_by' => 'Rider',
				'cancel_reason_id' => @$request->cancel_reason_id,

			];

			Cancel::updateOrCreate(['trip_id' => $request->trip_id], $data);

			$driver_id = $cancelled_id->driver_id;
			$rider = User::where('id', $driver_id)->first();
			$device_id = $rider->device_id;
			$device_type = $rider->device_type;
			$user_type = $rider->user_type;
			$push_title = "Trip Cancelled by Rider";
		} else {
			$data = [
				'trip_id' => $request->trip_id,
				'user_id' => auth()->user()->id,
				'cancel_reason_id' => $request->cancel_reason_id,
				'cancel_comments' => @$request->cancel_comments != '' ? $request->cancel_comments : '',
				'cancelled_by' => 'Driver',
			];

			Cancel::updateOrCreate(['trip_id' => $request->trip_id], $data);
			$user_id = $cancelled_id->user_id;
			$driver_id = $cancelled_id->driver_id;
			$driver = User::where('id', $user_id)->first();
			$device_id = $driver->device_id;
			$device_type = $driver->device_type;
			$user_type = $driver->user_type;
			$push_title = __('messages.api.trip_cancelled_by_driver');
		}

		Trips::where('id', $request->trip_id)->update(['status' => 'Cancelled', 'payment_status' => 'Trip Cancelled']);

		if ($cancelled_id->pool_id > 0) {
			$pool_trip = PoolTrip::find($cancelled_id->pool_id);
			$pool_trip->seats = $pool_trip->seats + $cancelled_id->ride_request->seats;

			$pending_count 	= $pool_trip->trips->whereNotIn('status', ['Payment', 'Rating', 'Completed', 'Cancelled'])->count();
			$completed_count = $pool_trip->trips->whereIn('status', ['Payment', 'Rating', 'Completed'])->count();

			if ($pending_count == 0) {
				DriverLocation::where('user_id', $cancelled_id->driver_id)->update(['status' => 'Online', 'pool_trip_id' => NULL]);
				if (!$completed_count) {
					$pool_trip->status = 'Cancelled';
				}
			}

			$pool_trip->save();
		} else {

			DriverLocation::where('user_id', $cancelled_id->driver_id)->update(['status' => 'Online', 'pool_trip_id' => NULL]);
		}

		$trip_riders = $this->getTripDetails($request->trip_id, auth()->user(), 0, 0, 1);

		// push notification
		$push_data = array(
			'cancel_trip' => array(
				'trip_id' => $request->trip_id,
				'trip_riders' => $trip_riders,
				'status' => 'Cancelled',
			)
		);

		if ($device_type == 1) {
			$this->request_helper->push_notification_ios($push_title, $push_data, $user_type, $device_id);
		} else {
			$this->request_helper->push_notification_android($push_title, $push_data, $user_type, $device_id);
		}

		CustomLog::info("cancel_trip Api Stp:2 :");

		return response()->json([
			'status_code' 	 => '1',
			'status_message' => "Success",
			'trip_riders' => $trip_riders,
		]);
	}

	public function cancel_reasons()
	{
		Log::info("cancel_reasons Api Stp:1 :");
		//	$user_details = JWTAuth::parseToken()->authenticate();
		$cancel_reasons = CancelReason::active()->where('cancelled_by', auth()->user()->user_type)->get();

		CustomLog::info("cancel_reasons Api Stp:2 :");

		return response()->json([
			'status_code' 	 => '1',
			'status_message' => "Success",
			'cancel_reasons' => $cancel_reasons,
		]);
	}

	public function toll_reasons()
	{
		Log::info("toll_reasons Api Stp:1 :");

		//$user_details = JWTAuth::parseToken()->authenticate();
		$toll_reasons = TollReason::where('id', '>', 1)->active()->get();
		$toll_reasons[] = TollReason::where('id', 1)->active()->first();

		foreach ($toll_reasons as $toll_reason) {
			$toll_reason->commendable = 0;
			if ($toll_reason->id == 1) {
				$toll_reason->commendable = 1;
			}
		}
		CustomLog::info("toll_reasons Api Stp:2 :");
		return response()->json([
			'status_message' => "Success",
			'status_code' => '1',
			'toll_reasons' => $toll_reasons
		]);
	}

	/**
	 * Get Trip details Of Given trip id. If trip id not passed then returns incomplete trip details
	 * 
	 * @param  Get method request inputs
	 * @return Response Json
	 */
	public function get_trip_details(Request $request)
	{
		Log::info("get_trip_details Api Stp:1 :", $request->all());

		//	$user_details = JWTAuth::parseToken()->authenticate();
		$user_details = auth()->user();

		$rules = array(
			'trip_id' => 'nullable|exists:trips,id',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}


		//$user_details 	= JWTAuth::parseToken()->authenticate();
		$trip_id 		= $request->trip_id;

		// Check Any Trip Is Incomplete Or Not
		$is_trip_id = true;
		if ($trip_id == '') {
			$trip_id = $this->checkPendingTrips($user_details);

			if ($trip_id == 0) {
				return response()->json([
					'status_code' => '0',
					'status_message' => __('messages.api.no_trips_found'),
				]);
			}

			$is_trip_id = false;
		}

		$trip_detail = $this->getTripDetails($trip_id, $user_details, $is_trip_id);

		CustomLog::info("get_trip_details Api Stp:2 :");

		$return_data = array_merge([
			'status_code' => '1',
			'status_message' => __('messages.api.listed_successfully'),
		], $trip_detail['data']);

		return response()->json($return_data);
	}

	public function get_past_trips_v2(Request $request)
	{
		Log::info("get_past_trips_v2 Api Stp:1 :", $request->all());


		$user_details = auth()->user();


		$rules = array(
			'page' => 'required|min:1',
		);

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		//$trips = Trips::with('car_type', 'currency', 'ride_request.schedule_ride')->where('user_id', $user_details->id)->orderBy('id', 'DESC')->paginate($this->paginate_limit)->toJson();
		$trips = DB::table('trips')->where('user_id', $user_details->id)->orderBy('id', 'DESC')->paginate($this->paginate_limit)->toJson();

		$data_result = json_decode($trips);

		if ($data_result->total == 0 || empty($data_result->data)) {
			return response()->json([
				'status_code' 		=> '0',
				'status_message' 	=> trans('messages.api.no_data_found'),
			]);
		}
		$trip_result = collect($data_result->data);
		$result_data = $this->mapTripDetails($trip_result, $user_details);

		CustomLog::info("get_past_trips_v2 Api Stp:2 :");

		return response()->json([
			'status_code'		=>	'1',
			'status_message'	=>	__('messages.api.listed_successfully'),
			'current_page'		=>  $data_result->current_page,
			'total_pages'		=>  $data_result->last_page,
			'data'				=>	$result_data,
		]);
	}

	/**
	 * Accept the Trip Request
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */
	public function acceptTrip(Request $request)
	{
		$req = RideRequest::where('id', $request->request_id)->first();
		$request_group = $req->group_id;
		$request_status = RideRequest::where('group_id', $request_group)->orderBy('id', 'DESC')->first();
		$driver_location = DriverLocation::where('user_id', auth()->user()->id)->first();

		if (isset($request_status)) :
			if (strtolower($request_status->status) == 'cancelled') :
				$return_data = [
					'status_code' => '0',
					'status_message' => "Request Already Cancelled"
				];
			else :
				Log::info("accept_request Api Stp:1 :", $request->all());

				//$user_details = JWTAuth::parseToken()->authenticate();

				$rules = array(
					'user_type' => 'required|in:Driver,driver',
					'status' 	=> 'required|in:Online,online,Trip,trip',
					'request_id' => 'required|exists:request,id',
				);

				$validator = Validator::make($request->all(), $rules);

				if ($validator->fails()) {
					return response()->json([
						'status_code'     => '0',
						'status_message' => $validator->messages()->first(),
					]);
				}

				//$user = User::where('id', auth()->user()->id)->where('user_type', $request->user_type)->first();

				// if (!$user) {
				// 	return response()->json([
				// 		'status_code'	 => '0',
				// 		'status_message' => __('messages.invalid_credentials'),
				// 	]);
				// }

				$req = RideRequest::where('id', $request->request_id)->first();
				$request_group = $req->group_id;
				$request_status = RideRequest::where('group_id', $request_group)->where('status', 'Accepted')->count();
				if ($request_status != "0") {
					return response()->json([
						'status_code' 	=> '0',
						'status_message' => "Already Accepted",
					]);
				}

				$firbase = resolve("App\Services\FirebaseService");
				$firbase->deleteReference("trip_request/" . auth()->user()->id);

				RideRequest::where('id', $request->request_id)->update(['status' => 'Accepted']);

				$ride_request 	= RideRequest::where('id', $request->request_id)->first();
				$vehicle 		= CarType::find($ride_request->car_id);
				$fare_details 	= ManageFare::where('location_id', $ride_request->location_id)->where('vehicle_id', $ride_request->car_id)->first();

				$status = $vehicle->is_pool == 'Yes' ? 'Pool Trip' : $request->status;
				DriverLocation::where('user_id', auth()->user()->id)->update(['status' => $status]);

				if ($ride_request->schedule_id != '') {
					ScheduleRide::where('id', $ride_request->schedule_id)->update(['status' => 'Completed']);
					$schedule_ride = ScheduleRide::where('id', $ride_request->schedule_id)->first();
				}

				if ($req->timezone != '') {
					date_default_timezone_set($req->timezone);
				} else {
					//$driver_location = DriverLocation::where('user_id', auth()->user()->id)->first();
					//  get user default location
					$user_timezone = $this->request_helper->getTimeZone($driver_location->latitude, $driver_location->longitude);

					if ($user_timezone != '') {
						date_default_timezone_set($user_timezone);
					}
				}

				if ($vehicle->is_pool == 'Yes') {
					$driver_location = DriverLocation::where('user_id', auth()->user()->id)->where('car_id', $ride_request->car_id)->first();
					if (!$driver_location->pool_trip_id) {
						$pool_trip = new PoolTrip;
						$pool_trip->car_id = $ride_request->car_id;
						$pool_trip->driver_id = auth()->user()->id;
						$pool_trip->pickup_latitude = $ride_request->pickup_latitude;
						$pool_trip->pickup_longitude = $ride_request->pickup_longitude;
						$pool_trip->drop_latitude = $ride_request->drop_latitude;
						$pool_trip->drop_longitude = $ride_request->drop_longitude;
						$pool_trip->seats = $fare_details->capacity - $ride_request->seats;
						$pool_trip->pickup_location = $ride_request->pickup_location;
						$pool_trip->drop_location = $ride_request->drop_location;
					} else {
						$pool_trip = PoolTrip::find($driver_location->pool_trip_id);

						// get distance between driver location to last drop location
						$driverToLastDrop = getDistanceBetweenPoints($driver_location->latitude, $driver_location->longitude, $pool_trip->drop_latitude, $pool_trip->drop_longitude);

						// get distance between driver location to request drop location
						$driverToRequestPickup = getDistanceBetweenPoints($driver_location->latitude, $driver_location->longitude, $ride_request->drop_latitude, $ride_request->drop_longitude);

						if ($driverToLastDrop < $driverToRequestPickup) {
							$pool_trip->drop_latitude = $ride_request->drop_latitude;
							$pool_trip->drop_longitude = $ride_request->drop_longitude;
							$pool_trip->drop_location = $ride_request->drop_location;
						}

						$pool_trip->seats = $pool_trip->seats - $ride_request->seats;
					}

					$pool_trip->status = 'Scheduled';
					$pool_trip->currency_code = auth()->user()->currency->code;
					$pool_trip->save();

					$pool_trip_id = $pool_trip->id;
					DriverLocation::where('user_id', auth()->user()->id)->update(['pool_trip_id' => $pool_trip_id]);

					$additional_rider = $ride_request->additional_rider;
				}

				$fare_estimation = $this->request_helper->GetDrivingDistance_v2($ride_request->pickup_latitude, $ride_request->drop_latitude, $ride_request->pickup_longitude, $ride_request->drop_longitude);


				CustomLog::info(
					"pickup_latitude_log_1=" . $ride_request->pickup_latitude .
						"drop_latitude_log_1=" . $ride_request->drop_latitude  .
						"pickup_longitude_log_1=" . $ride_request->pickup_longitude .
						"drop_longitude_log_1=" . $ride_request->drop_longitude
				);
				//Log::info("fare_estimation_new :", $fare_estimation);
				// Create Trip
				$trip = new Trips;
				$trip->user_id 			= $ride_request->user_id;
				$trip->pool_id 			= $pool_trip_id ?? '0';
				$trip->pickup_latitude 	= $ride_request->pickup_latitude;
				$trip->pickup_longitude = $ride_request->pickup_longitude;
				$trip->drop_latitude 	= $ride_request->drop_latitude;
				$trip->drop_longitude 	= $ride_request->drop_longitude;
				$trip->driver_id 		= $ride_request->driver_id;
				$trip->car_id 			= $ride_request->car_id;
				$trip->pickup_location 	= $ride_request->pickup_location;
				$trip->drop_location 	= $ride_request->drop_location;
				$trip->request_id 		= $ride_request->id;
				$trip->trip_path 		= $ride_request->trip_path;
				$trip->payment_mode 	= $ride_request->payment_mode;
				$trip->status 			= 'Scheduled';
				$trip->currency_code 	= auth()->user()->currency->code;
				$trip->peak_fare 		= $ride_request->peak_fare;
				$trip->fare_estimation 	= $fare_estimation;
				$trip->estimate_fare 	= $ride_request->estimate_fare;
				$trip->additional_rider = $additional_rider ?? 0;
				$trip->otp 				= mt_rand(1000, 9999);
				$trip->save();

				try {
					$file = $trip->id . '_file.json';
					$destinationPath = public_path() . "/trip_file/";

					if (!is_dir($destinationPath)) {
						mkdir($destinationPath, 0777, true);
					}

					File::put($destinationPath . $file, '');
				} catch (\Exception $e) {
				}

				$push_data['push_title'] = __('messages.api.request_accepted');
				$push_data['data'] = array(
					'accept_request' => array(
						'trip_id' => $trip->id
					)
				);

				//$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance($driver_location->latitude, $ride_request->pickup_latitude,$driver_location->longitude, $ride_request->pickup_longitude);
				if (is_numeric($driver_location->latitude) and is_numeric($driver_location->longitude)) {

					CustomLog::info(
						"driver_location_latitude_log_1=" . $driver_location->latitude .
							"ride_request_pickup_latitude_log_1=" . $ride_request->pickup_latitude  .
							"driver_location_longitude_log_1=" . $driver_location->longitude .
							"ride_request_pickup_longitude_log_1=" . $ride_request->pickup_longitude
					);

					$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance_v2($driver_location->latitude, $ride_request->pickup_latitude, $driver_location->longitude, $ride_request->pickup_longitude);

					//$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance_v2($driver_location->latitude, $ride_request->pickup_latitude, $driver_location->longitude, $ride_request->pickup_longitude);
					//test2
					//$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance_v2(23.798517, 23.7942306, 90.3752947, 90.4044471);
					//error
					//$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance_v2(23.7985152, 23.798517, 90.3752956, 90.3752947);

					//test
					//$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance_v2($ride_request->pickup_latitude, $ride_request->drop_latitude, $ride_request->pickup_longitude, $ride_request->drop_longitude);

					$distance_to_reach_to_rider_minutes = round(floor(round($distance_to_reach_to_rider['time'] / 60)));
					$distance_to_reach_to_rider_km = round(floor($distance_to_reach_to_rider['distance'] / 1000) . '.' . floor($distance_to_reach_to_rider['distance'] % 1000));
				}

				$total_rating = DB::table('rating')->select(DB::raw('sum(rider_rating) as rating'))
					->where('driver_id', $ride_request->driver_id)->where('rider_rating', '>', 0)->where('driver_id', $ride_request->driver_id)->first()->rating;

				$total_rating_count = Rating::where('driver_id', $ride_request->driver_id)->where('rider_rating', '>', 0)->get()->count();
				$driver_rating = '0.00';
				if ($total_rating_count != 0) {
					$driver_rating = (string) round(($total_rating / $total_rating_count), 2);
				}



				$this->request_helper->SendPushNotification($ride_request->users, $push_data);

				if (isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {
					$push_title = __('messages.request_accepted');
					$text 		= __('messages.api.your_otp_to_begin_trip') . $trip->otp;

					$push_data['push_title'] = $push_title;
					$push_data['data'] = array(
						'custom_message' => array(
							'title' => $push_title,
							'message_data' => $text,
						)
					);

					$text = $push_title . $text;

					$this->request_helper->checkAndSendMessage($ride_request->users, $text, $push_data);
				}

				$trip_detail = $this->getTripDetails($trip->id, auth()->user());
				if (!$trip_detail['status']) {
					return response()->json([
						'status_code' => '0',
						'status_message' => $trip_detail['status_message'],
					]);
				}


				$return_data = array_merge([
					'status_code' => '1',
					'status_message' => __('messages.api.listed_successfully'),
				], $trip_detail['data']);
				$rider_user = $ride_request->users;
				$driver_user = $ride_request->driver;
				$pushData['driver_name'] = $driver_user->first_name;
				$pushData['driver_total_rating'] = $driver_rating;
				$pushData['pick_up_time'] = $distance_to_reach_to_rider_minutes . ' min';
				$pushData['device_id'] = $rider_user->device_id;
				$pushData['device_type'] = $rider_user->device_type;
				$pushData['message_index'] = 'trip_accepted';
				$this->pushNotificationService->tripPushNotificationMessage($pushData);


				$pushData['pin_number'] = $trip->otp;
				$pushData['device_id'] = $rider_user->device_id;
				$pushData['device_type'] = $rider_user->device_type;
				$pushData['message_index'] = 'send_pin';
				$this->pushNotificationService->tripPushNotificationMessage($pushData);

				$pushData['driver_name'] = $driver_user->first_name;
				$pushData['car_type_name'] = $driver_user->vehicles[0]['vehicle_name'];
				$pushData['car_reg_number'] = $driver_user->vehicles[0]['vehicle_number'];
				$pushData['device_id'] = $rider_user->device_id;
				$pushData['device_type'] = $rider_user->device_type;
				$pushData['message_index'] = 'arriving_driver';
				$this->pushNotificationService->tripPushNotificationMessage($pushData);

			endif;
		else :

			$return_data = [
				'status_code' => '0',
				'status_message' => "Request Not Found:)"
			];

		endif;
		CustomLog::info("accept_request Api Stp:2 :");

		return response()->json($return_data);
	}




	public function acceptTrip_v2(Request $request)
	{
		$req = RideRequest::where('id', $request->request_id)->first();
		$request_group = $req->group_id;
		$request_status = RideRequest::where('group_id', $request_group)->orderBy('id', 'DESC')->first();
		$driver_location = DriverLocation::where('user_id', auth()->user()->id)->first();

		if (isset($request_status)) :
			if (strtolower($request_status->status) == 'cancelled') :
				$return_data = [
					'status_code' => '0',
					'status_message' => "Request Already Cancelled"
				];
			else :
				Log::info("accept_request Api Stp:1 :", $request->all());

				//$user_details = JWTAuth::parseToken()->authenticate();

				$rules = array(
					'user_type' => 'required|in:Driver,driver',
					'status' 	=> 'required|in:Online,online,Trip,trip',
					'request_id' => 'required|exists:request,id',
				);

				$validator = Validator::make($request->all(), $rules);

				if ($validator->fails()) {
					return response()->json([
						'status_code'     => '0',
						'status_message' => $validator->messages()->first(),
					]);
				}

				//$user = User::where('id', auth()->user()->id)->where('user_type', $request->user_type)->first();

				// if (!$user) {
				// 	return response()->json([
				// 		'status_code'	 => '0',
				// 		'status_message' => __('messages.invalid_credentials'),
				// 	]);
				// }

				$req = RideRequest::where('id', $request->request_id)->first();
				$request_group = $req->group_id;
				$request_status = RideRequest::where('group_id', $request_group)->where('status', 'Accepted')->count();
				if ($request_status != "0") {
					return response()->json([
						'status_code' 	=> '0',
						'status_message' => "Already Accepted",
					]);
				}

				$firbase = resolve("App\Services\FirebaseService");
				$firbase->deleteReference("trip_request/" . auth()->user()->id);

				RideRequest::where('id', $request->request_id)->update(['status' => 'Accepted']);

				$ride_request 	= RideRequest::where('id', $request->request_id)->first();
				$vehicle 		= CarType::find($ride_request->car_id);
				$fare_details 	= ManageFare::where('location_id', $ride_request->location_id)->where('vehicle_id', $ride_request->car_id)->first();

				$status = $vehicle->is_pool == 'Yes' ? 'Pool Trip' : $request->status;
				DriverLocation::where('user_id', auth()->user()->id)->update(['status' => $status]);

				if ($ride_request->schedule_id != '') {
					ScheduleRide::where('id', $ride_request->schedule_id)->update(['status' => 'Completed']);
					$schedule_ride = ScheduleRide::where('id', $ride_request->schedule_id)->first();
				}

				if ($req->timezone != '') {
					date_default_timezone_set($req->timezone);
				} else {
					//$driver_location = DriverLocation::where('user_id', auth()->user()->id)->first();
					//  get user default location
					$user_timezone = $this->request_helper->getTimeZone($driver_location->latitude, $driver_location->longitude);

					if ($user_timezone != '') {
						date_default_timezone_set($user_timezone);
					}
				}

				if ($vehicle->is_pool == 'Yes') {
					$driver_location = DriverLocation::where('user_id', auth()->user()->id)->where('car_id', $ride_request->car_id)->first();
					if (!$driver_location->pool_trip_id) {
						$pool_trip = new PoolTrip;
						$pool_trip->car_id = $ride_request->car_id;
						$pool_trip->driver_id = auth()->user()->id;
						$pool_trip->pickup_latitude = $ride_request->pickup_latitude;
						$pool_trip->pickup_longitude = $ride_request->pickup_longitude;
						$pool_trip->drop_latitude = $ride_request->drop_latitude;
						$pool_trip->drop_longitude = $ride_request->drop_longitude;
						$pool_trip->seats = $fare_details->capacity - $ride_request->seats;
						$pool_trip->pickup_location = $ride_request->pickup_location;
						$pool_trip->drop_location = $ride_request->drop_location;
					} else {
						$pool_trip = PoolTrip::find($driver_location->pool_trip_id);

						// get distance between driver location to last drop location
						$driverToLastDrop = getDistanceBetweenPoints($driver_location->latitude, $driver_location->longitude, $pool_trip->drop_latitude, $pool_trip->drop_longitude);

						// get distance between driver location to request drop location
						$driverToRequestPickup = getDistanceBetweenPoints($driver_location->latitude, $driver_location->longitude, $ride_request->drop_latitude, $ride_request->drop_longitude);

						if ($driverToLastDrop < $driverToRequestPickup) {
							$pool_trip->drop_latitude = $ride_request->drop_latitude;
							$pool_trip->drop_longitude = $ride_request->drop_longitude;
							$pool_trip->drop_location = $ride_request->drop_location;
						}

						$pool_trip->seats = $pool_trip->seats - $ride_request->seats;
					}

					$pool_trip->status = 'Scheduled';
					$pool_trip->currency_code = auth()->user()->currency->code;
					$pool_trip->save();

					$pool_trip_id = $pool_trip->id;
					DriverLocation::where('user_id', auth()->user()->id)->update(['pool_trip_id' => $pool_trip_id]);

					$additional_rider = $ride_request->additional_rider;
				}

				//$fare_estimation = $this->request_helper->GetDrivingDistance_v2($ride_request->pickup_latitude, $ride_request->drop_latitude, $ride_request->pickup_longitude, $ride_request->drop_longitude);

				$fare_estimation = $this->request_helper->GetDrivingDistance_v3($ride_request->pickup_latitude, $ride_request->drop_latitude, $ride_request->pickup_longitude, $ride_request->drop_longitude);




				CustomLog::info(
					"pickup_latitude_log_1=" . $ride_request->pickup_latitude .
						"drop_latitude_log_1=" . $ride_request->drop_latitude  .
						"pickup_longitude_log_1=" . $ride_request->pickup_longitude .
						"drop_longitude_log_1=" . $ride_request->drop_longitude
				);
				//Log::info("fare_estimation_new :", $fare_estimation);
				// Create Trip
				$trip = new Trips;
				$trip->user_id 			= $ride_request->user_id;
				$trip->pool_id 			= $pool_trip_id ?? '0';
				$trip->pickup_latitude 	= $ride_request->pickup_latitude;
				$trip->pickup_longitude = $ride_request->pickup_longitude;
				$trip->drop_latitude 	= $ride_request->drop_latitude;
				$trip->drop_longitude 	= $ride_request->drop_longitude;
				$trip->driver_id 		= $ride_request->driver_id;
				$trip->car_id 			= $ride_request->car_id;
				$trip->pickup_location 	= $ride_request->pickup_location;
				$trip->drop_location 	= $ride_request->drop_location;
				$trip->request_id 		= $ride_request->id;
				$trip->trip_path 		= $ride_request->trip_path;
				$trip->payment_mode 	= $ride_request->payment_mode;
				$trip->status 			= 'Scheduled';
				$trip->currency_code 	= auth()->user()->currency->code;
				$trip->peak_fare 		= $ride_request->peak_fare;
				$trip->fare_estimation 	= $fare_estimation;
				$trip->estimate_fare 	= $ride_request->estimate_fare;
				$trip->additional_rider = $additional_rider ?? 0;
				$trip->otp 				= mt_rand(1000, 9999);
				$trip->save();

				try {
					$file = $trip->id . '_file.json';
					$destinationPath = public_path() . "/trip_file/";

					if (!is_dir($destinationPath)) {
						mkdir($destinationPath, 0777, true);
					}

					File::put($destinationPath . $file, '');
				} catch (\Exception $e) {
				}

				$push_data['push_title'] = __('messages.api.request_accepted');
				$push_data['data'] = array(
					'accept_request' => array(
						'trip_id' => $trip->id
					)
				);

				//$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance($driver_location->latitude, $ride_request->pickup_latitude,$driver_location->longitude, $ride_request->pickup_longitude);
				if (is_numeric($driver_location->latitude) and is_numeric($driver_location->longitude)) {

					CustomLog::info(
						"driver_location_latitude_log_1=" . $driver_location->latitude .
							"ride_request_pickup_latitude_log_1=" . $ride_request->pickup_latitude  .
							"driver_location_longitude_log_1=" . $driver_location->longitude .
							"ride_request_pickup_longitude_log_1=" . $ride_request->pickup_longitude
					);

					$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance_v2($driver_location->latitude, $ride_request->pickup_latitude, $driver_location->longitude, $ride_request->pickup_longitude);

					//$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance_v2($driver_location->latitude, $ride_request->pickup_latitude, $driver_location->longitude, $ride_request->pickup_longitude);
					//test2
					//$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance_v2(23.798517, 23.7942306, 90.3752947, 90.4044471);
					//error
					//$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance_v2(23.7985152, 23.798517, 90.3752956, 90.3752947);

					//test
					//$distance_to_reach_to_rider = $this->request_helper->GetDrivingDistance_v2($ride_request->pickup_latitude, $ride_request->drop_latitude, $ride_request->pickup_longitude, $ride_request->drop_longitude);

					$distance_to_reach_to_rider_minutes = round(floor(round($distance_to_reach_to_rider['time'] / 60)));
					$distance_to_reach_to_rider_km = round(floor($distance_to_reach_to_rider['distance'] / 1000) . '.' . floor($distance_to_reach_to_rider['distance'] % 1000));
				}

				$total_rating = DB::table('rating')->select(DB::raw('sum(rider_rating) as rating'))
					->where('driver_id', $ride_request->driver_id)->where('rider_rating', '>', 0)->where('driver_id', $ride_request->driver_id)->first()->rating;

				$total_rating_count = Rating::where('driver_id', $ride_request->driver_id)->where('rider_rating', '>', 0)->get()->count();
				$driver_rating = '0.00';
				if ($total_rating_count != 0) {
					$driver_rating = (string) round(($total_rating / $total_rating_count), 2);
				}



				$this->request_helper->SendPushNotification($ride_request->users, $push_data);

				if (isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {
					$push_title = __('messages.request_accepted');
					$text 		= __('messages.api.your_otp_to_begin_trip') . $trip->otp;

					$push_data['push_title'] = $push_title;
					$push_data['data'] = array(
						'custom_message' => array(
							'title' => $push_title,
							'message_data' => $text,
						)
					);

					$text = $push_title . $text;

					$this->request_helper->checkAndSendMessage($ride_request->users, $text, $push_data);
				}

				$trip_detail = $this->getTripDetails($trip->id, auth()->user());
				if (!$trip_detail['status']) {
					return response()->json([
						'status_code' => '0',
						'status_message' => $trip_detail['status_message'],
					]);
				}


				$return_data = array_merge([
					'status_code' => '1',
					'status_message' => __('messages.api.listed_successfully'),
				], $trip_detail['data']);
				$rider_user = $ride_request->users;
				$driver_user = $ride_request->driver;
				$pushData['driver_name'] = $driver_user->first_name;
				$pushData['driver_total_rating'] = $driver_rating;
				$pushData['pick_up_time'] = $distance_to_reach_to_rider_minutes . ' min';
				$pushData['device_id'] = $rider_user->device_id;
				$pushData['device_type'] = $rider_user->device_type;
				$pushData['message_index'] = 'trip_accepted';
				$this->pushNotificationService->tripPushNotificationMessage($pushData);


				$pushData['pin_number'] = $trip->otp;
				$pushData['device_id'] = $rider_user->device_id;
				$pushData['device_type'] = $rider_user->device_type;
				$pushData['message_index'] = 'send_pin';
				$this->pushNotificationService->tripPushNotificationMessage($pushData);

				$pushData['driver_name'] = $driver_user->first_name;
				$pushData['car_type_name'] = $driver_user->vehicles[0]['vehicle_name'];
				$pushData['car_reg_number'] = $driver_user->vehicles[0]['vehicle_number'];
				$pushData['device_id'] = $rider_user->device_id;
				$pushData['device_type'] = $rider_user->device_type;
				$pushData['message_index'] = 'arriving_driver';
				$this->pushNotificationService->tripPushNotificationMessage($pushData);

			endif;
		else :

			$return_data = [
				'status_code' => '0',
				'status_message' => "Request Not Found:)"
			];

		endif;
		CustomLog::info("accept_request Api Stp:2 :");

		return response()->json($return_data);
	}


	/**
	 * Send Message to the User
	 * 
	 * @param  Get method request inputs
	 * @return Response Json
	 */
	public function send_message(Request $request)
	{
		Log::info("send_message Api Stp:1 :", $request->all());

		$user = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'receiver_id' 	=> 'required|exists:users,id',
			'message' 		=> 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		$receiver = User::find($request->receiver_id);

		if ($user->user_type == 'Rider') {
			$rating = getDriverRating($user->id);
		} else {
			$rating = getRiderRating($user->id);
		}

		$push_data['change_title'] = 1;
		$push_data['push_title'] = $user->first_name;
		$push_data['data'] = array(
			'chat_notification' => array(
				'title' 		=> $request->message,
				'message_data' 	=> $request->message,
				'user_name'  	=> $user->first_name,
				'trip_id'  		=> $request->trip_id ?? '',
				'rating'  		=> $rating,
				'user_id'  		=> $user->id,
				'user_image' 	=> $user->profile_picture->src ?? url('images/user.jpeg'),
			)
		);

		$this->request_helper->SendPushNotification($receiver, $push_data);

		CustomLog::info("send_message Api Stp:2 :");

		return response()->json([
			'status_code' => '1',
			'status_message' => __('messages.api.success'),
		]);
	}
}
