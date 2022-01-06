<?php

/**
 * Driver Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Driver
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use App\Http\Helper\RequestHelper;
use App\Services\PushNotificationService;
use App\Http\Start\Helpers;
use App\Models\DriverLocation;
use App\Models\Payment;
use App\Models\DriverOweAmountPayment;
use App\Models\DriverOweAmount;
use App\Models\Rating;
use App\Models\Request as RideRequest;
use App\Models\ScheduleRide;
use App\Models\PaymentMethod;
use App\Models\Trips;
use App\Models\User;
use App\Models\UsersPromoCode;
use App\Models\Country;
use App\Models\BankDetail;
use App\Models\AppliedReferrals;
use App\Models\ReferralUser;
use App\Models\Fees;
use App\Models\MakeVehicle;
use App\Models\PoolTrip;
use App\Models\VehicleModel;
use App\Models\ProfilePicture;
use App\Models\PeakHour;
use Auth;
use DB;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use File;
use App\Http\Helper\InvoiceHelper;
use App\Models\Vehicle;
use App\Models\CarType;
use App\Models\DriverDocuments;
use App\Models\Documents;
use App\Models\FilterOption;
use App\Models\FilterObject;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CustomLog;
use Carbon\Carbon;

class DriverController extends Controller
{
	protected $request_helper; // Global variable for Helpers instance

	/**
	 * @var PushNotificationService
	 */
	private $pushNotificationService;

	public function __construct(RequestHelper $request, InvoiceHelper $invoice_helper, PushNotificationService $pushNotificationService)
	{
		DB::enableQueryLog();
		$this->request_helper = $request;
		$this->helper = new Helpers;
		$this->invoice_helper = $invoice_helper;
		$this->bonus_helper = resolve("App\Http\Helper\BonusHelper");
		$this->invoice = resolve("App\Http\Controllers\Invoice");
		$this->pushNotificationService = $pushNotificationService;
	}

	/**
	 * Update Location of Driver & calculate the trip distance while trip
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */

	public function updateLocation(Request $request)
	{
		Log::info("updateLocation Api Stp:1 : " . time() . " ", $request->all());
		$user_details = JWTAuth::parseToken()->authenticate();



		$rules = array(
			'latitude' 	=> 'required',
			'longitude' => 'required',
			'user_type' => 'required|in:Driver,driver',
			'status' 	=> 'required|in:Online,Offline,online,offline,Trip,trip',
		);

		if ($request->trip_id) {
			$rules['trip_id'] = 'required|exists:trips,id';
			$rules['total_km'] = 'required';
		}

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		$user = User::where('id', $user_details->id)->where('user_type', $request->user_type)->first();

		if ($user == '') {
			return response()->json([
				'status_code'	 => '0',
				'status_message' => __('messages.invalid_credentials'),
			]);
		}
		$driver_location = DriverLocation::where('user_id', $user_details->id)->first();
		if ($request->status == "Online") {
			$this->driver_activity($user_details->id);
		} else {
			$this->driver_activity_offline($user_details->id);
		}


		if ($request->trip_id) {

			$old_km = Trips::where('id', $request->trip_id)->first()->total_km;
			$user_id = Trips::where('id', $request->trip_id)->first()->user_id;

			$user_rider = User::where('id', $user_id)->first();

			$device_type = $user_rider->device_type;

			$device_id = $user_rider->device_id;
			$user_type = $user_rider->user_type;
			$push_title = "Live Tracking";
			$data = array('live_tracking' => array('trip_id' => $request->trip_id, 'driver_latitude' => @$request->latitude, 'driver_longitude' => @$request->longitude));

			if ($user->device_type == 3) {
				$old_latitude = $driver_location->latitude;
				$old_longitude = $driver_location->longitude;

				$earthRadius = 6371000;
				$latFrom = deg2rad($old_latitude);
				$lonFrom = deg2rad($old_longitude);
				$latTo = deg2rad($request->latitude);
				$lonTo = deg2rad($request->longitude);

				$latDelta = $latTo - $latFrom;
				$lonDelta = $lonTo - $lonFrom;

				$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

				$meter = number_format((($angle * $earthRadius)), 2);

				$km = (($meter) / 1000);
			} else {
				$km = $request->total_km;
			}

			$new_km = $old_km + $km;

			/* json file */
			$trip_id = $request->trip_id;

			$file = $trip_id . '_file.json';
			$destinationPath = public_path() . "/trip_file/";

			if (!is_dir($destinationPath)) {
				mkdir($destinationPath, 0777, true);
			}

			$old_path = base_path('public/trip_file/' . $trip_id . '_file.json');

			if (file_exists($old_path)) {
				$jsonString = file_get_contents($old_path);
				$datas = json_decode($jsonString, true);
			}


			$datas[] = array(
				'latitude' => $request->latitude,
				'longitude' => $request->longitude,
				'current_km' =>  $km,
				'old_km' => $old_km,
				'new_km' => (string)$new_km,
				'time' => date('H:i:s')
			);

			$data = json_encode($datas, JSON_PRETTY_PRINT);
			File::put($destinationPath . $file, $data);
			/* json file */

			Trips::where('id', $request->trip_id)->update(['total_km' => $new_km]);

			$data = [
				'user_id' => $user_details->id,
				'latitude' => $request->latitude,
				'longitude' => $request->longitude,
			];

			DriverLocation::updateOrCreate(['user_id' => $user_details->id], $data);

			return response()->json([
				'status_code' => '1',
				'status_message' => "updated successfully",
			]);
		}

		if ($driver_location != '' && $driver_location->status == 'Trip') {
			return response()->json([
				'status_code' => '0',
				'status_message' => trans('messages.please_complete_your_current_trip'),
			]);
		}

		$data['user_id'] = $user_details->id;
		$data['latitude'] = $request->latitude;
		$data['longitude'] = $request->longitude;

		if ($driver_location && $driver_location->status == "Pool Trip") {
			$data['pool_trip_id'] = $driver_location->pool_trip_id;
			$data['status'] = "Pool Trip";
		} else if ($request->status == "Online" || $request->status == "Offline") {
			$data['status'] = $request->status;
		}

		$vehicle_list = array();
		if (isset($user_details->vehicle->vehicle_id)) {
			$vehicle_list = $user_details->vehicle->vehicle_id;
			$vehicle_list = explode(',', $vehicle_list);

			foreach ($vehicle_list as $vehicle) {
				$data['car_id'] = $vehicle;
				DriverLocation::updateOrCreate(['user_id' => $user_details->id, 'car_id' => $vehicle], $data);
			}
		}

		DriverLocation::where('user_id', $user_details->id)->whereNotIn('car_id', $vehicle_list)->delete();


		CustomLog::info("updateLocation Api Stp:2");

		$this->bonus_helper->update_online_bonus_runtime($user_details->id);

		return response()->json([
			'status_code' => '1',
			'status_message' => "updated successfully",
		]);
	}

	public function customUpdateLocation(Request $request)
	{
		Log::info("customUpdateLocation Api Stp:1 : " . time() . " ", $request->all());

		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'latitude' 	=> 'required',
			'longitude' => 'required',
			'user_type' => 'required|in:Driver,driver',
			'status' 	=> 'required|in:Online,Offline,online,offline,Trip,trip',
		);

		if ($request->trip_id) {
			$rules['trip_id'] = 'required|exists:trips,id';
			$rules['total_km'] = 'required';
		}

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		$user = User::where('id', $user_details->id)->where('user_type', $request->user_type)->first();

		if ($user == '') {
			return response()->json([
				'status_code'	 => '0',
				'status_message' => __('messages.invalid_credentials'),
			]);
		}
		$driver_location = DriverLocation::where('user_id', $user_details->id)->first();
		if ($request->status == "Online") {
			$this->driver_activity($user_details->id);
		} else {
			$this->driver_activity_offline($user_details->id);
		}



		if ($request->trip_id) {

			$old_km = Trips::where('id', $request->trip_id)->first()->total_km;
			$user_id = Trips::where('id', $request->trip_id)->first()->user_id;

			$user_rider = User::where('id', $user_id)->first();

			$device_type = $user_rider->device_type;

			$device_id = $user_rider->device_id;
			$user_type = $user_rider->user_type;
			$push_title = "Live Tracking";
			$data = array('live_tracking' => array('trip_id' => $request->trip_id, 'driver_latitude' => @$request->latitude, 'driver_longitude' => @$request->longitude));


			/* json file */
			$trip_id = $request->trip_id;
			$file = $trip_id . '_file.json';
			$destinationPath = public_path() . "/trip_file/";

			if (!is_dir($destinationPath)) {
				mkdir($destinationPath, 0777, true);
			}


			$old_path = base_path('public/trip_file/' . $trip_id . '_file.json');

			if (file_exists($old_path)) {
				$jsonString = file_get_contents($old_path);
				$datas = json_decode($jsonString, true);
			}

			$km = 0;

			if ($user->device_type == 3) {
				$old_latitude = $driver_location->latitude;
				$old_longitude = $driver_location->longitude;

				$earthRadius = 6371000;
				$latFrom = deg2rad($old_latitude);
				$lonFrom = deg2rad($old_longitude);

				//loop for array latitude, longitude

				// $lat_request = array();
				// $long_request = array();
				// $lat_request[] = $request->latitude;
				// $long_request[] = $request->longitude;

				if (is_array($request->latitude)) $lat_request = $request->latitude;
				else $lat_request[] = $request->latitude;

				if (is_array($request->longitude)) $long_request = $request->longitude;
				else $long_request[] = $request->longitude;

				// $lat_request = $request->latitude;
				// $long_request = $request->longitude;

				for ($i = 0; $i < count($lat_request); $i++) {
					$latTo = deg2rad($lat_request[$i]);
					$lonTo = deg2rad($long_request[$i]);

					$latDelta = $latTo - $latFrom;
					$lonDelta = $lonTo - $lonFrom;

					$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

					$meter = number_format((($angle * $earthRadius)), 2);

					$km = $km + (($meter) / 1000);
					$new_km = $old_km + $km;

					$datas[] = array(
						'latitude' => $latTo,
						'longitude' => $lonTo,
						'current_km' => (($meter) / 1000),
						'old_km' => $old_km,
						'new_km' => (string)$new_km,
						'time' => date('H:i:s')
					);

					$latFrom = $latTo;
					$lonFrom = $lonTo;
				}
			} else {
				$km = $request->total_km;
			}







			$data = json_encode($datas, JSON_PRETTY_PRINT);
			File::put($destinationPath . $file, $data);
			/* json file */

			Trips::where('id', $request->trip_id)->update(['total_km' => $new_km]);

			$data = [
				'user_id' => $user_details->id,
				'latitude' => $request->latitude,
				'longitude' => $request->longitude,
			];

			DriverLocation::updateOrCreate(['user_id' => $user_details->id], $data);

			return response()->json([
				'status_code' => '1',
				'status_message' => "updated successfully",
			]);
		}

		if ($driver_location != '' && $driver_location->status == 'Trip') {
			return response()->json([
				'status_code' => '0',
				'status_message' => trans('messages.please_complete_your_current_trip'),
			]);
		}

		$data['user_id'] = $user_details->id;
		$data['latitude'] = $request->latitude;
		$data['longitude'] = $request->longitude;

		if ($driver_location && $driver_location->status == "Pool Trip") {
			$data['pool_trip_id'] = $driver_location->pool_trip_id;
			$data['status'] = "Pool Trip";
		} else if ($request->status == "Online" || $request->status == "Offline") {
			$data['status'] = $request->status;
		}

		$vehicle_list = array();
		if (isset($user_details->vehicle->vehicle_id)) {
			$vehicle_list = $user_details->vehicle->vehicle_id;
			$vehicle_list = explode(',', $vehicle_list);

			foreach ($vehicle_list as $vehicle) {
				$data['car_id'] = $vehicle;
				DriverLocation::updateOrCreate(['user_id' => $user_details->id, 'car_id' => $vehicle], $data);
			}
		}

		DriverLocation::where('user_id', $user_details->id)->whereNotIn('car_id', $vehicle_list)->delete();


		CustomLog::info("customUpdateLocation Api Stp:2");

		return response()->json([
			'status_code' => '1',
			'status_message' => "updated successfully",
		]);
	}

	public function updateLocation_without_trips(Request $request)
	{
		Log::info("updateLocation_without_trips Api Stp:1 : " . time() . " ", $request->all());

		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'latitude' 	=> 'required',
			'longitude' => 'required',
			'user_type' => 'required|in:Driver,driver',
			'status' 	=> 'required|in:Online,Offline,online,offline,Trip,trip',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		$user = User::where('id', $user_details->id)->where('user_type', $request->user_type)->first();

		if ($user == '') {
			return response()->json([
				'status_code'	 => '0',
				'status_message' => __('messages.invalid_credentials'),
			]);
		}
		$driver_location = DriverLocation::where('user_id', $user_details->id)->first();

		if ($request->status == "Online") $this->driver_activity($user_details->id);

		if ($driver_location != '' && $driver_location->status == 'Trip') {
			return response()->json([
				'status_code' => '0',
				'status_message' => trans('messages.please_complete_your_current_trip'),
			]);
		}

		$data['user_id'] = $user_details->id;
		$data['latitude'] = $request->latitude;
		$data['longitude'] = $request->longitude;

		if ($driver_location && $driver_location->status == "Pool Trip") {
			$data['pool_trip_id'] = $driver_location->pool_trip_id;
			$data['status'] = "Pool Trip";
		} else if ($request->status == "Online" || $request->status == "Offline") {
			$data['status'] = $request->status;
		}

		$vehicle_list = array();
		if (isset($user_details->vehicle->vehicle_id)) {
			$vehicle_list = $user_details->vehicle->vehicle_id;
			$vehicle_list = explode(',', $vehicle_list);

			foreach ($vehicle_list as $vehicle) {
				$data['car_id'] = $vehicle;
				DriverLocation::updateOrCreate(['user_id' => $user_details->id, 'car_id' => $vehicle], $data);
			}
		}

		DriverLocation::where('user_id', $user_details->id)->whereNotIn('car_id', $vehicle_list)->delete();


		CustomLog::info("updateLocation_without_trips Api Stp:2");

		return response()->json([
			'status_code' => '1',
			'status_message' => "updated successfully",
		]);
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

	public static function driver_activity($user_id)
	{
		$now = strtotime(now());
		$start_end_time = self::pick_time();
		$start_time_array = $start_end_time['start_time'];
		$end_time_array = $start_end_time['end_time'];

		$last_activity = Activity::select('id', 'updated_at')
			->where('user_id', $user_id)
			->orderBy('id', 'DESC')
			->first();

		$peak_time_created_at = '';
		$peak_time_updated_at = '';
		$new_entry = false;

		if (!empty($start_time_array)) {

			for ($i = 0; $i < count($start_time_array); $i++) {
				$start = strtotime(date('Y-m-d') . ' ' . $start_time_array[$i]);
				$end = strtotime(date('Y-m-d') . ' ' . $end_time_array[$i]);

				if ($now >= $start and $now <= $end) {
					$peak_time_created_at = now();
					$peak_time_updated_at = now();
					//CustomLog::info("okok".date('Y-m-d')." ".$start_time_array[$i].'/'.$end_time_array[$i]);
					break;
				}
			}
		}

		if (!is_object($last_activity)) {
			$table = new Activity;
			$table->user_id = $user_id;
			$table->hit_time = date('H:i:s');

			if ($peak_time_created_at != '') {
				$table->peak_time_created_at = $peak_time_created_at;
				$table->peak_time_updated_at = $peak_time_updated_at;
			}
			$table->save();
		} else {
			$difference = time() - strtotime($last_activity->updated_at);

			if ($difference > 180) { // 3 minutes 
				$table = new Activity;
				$table->user_id = $user_id;
				$table->hit_time = date('H:i:s');
				if ($peak_time_created_at != '') $table->peak_time_created_at = $peak_time_created_at;
				if ($peak_time_updated_at != '') 	$table->peak_time_updated_at = $peak_time_updated_at;
				$table->save();
			} else {
				$table = Activity::find($last_activity->id);
				$table->hit_time = $table->hit_time . ',' . date('H:i:s');
				$table->updated_at = now();
				if (
					$table->peak_time_created_at == '' or
					$table->peak_time_created_at == '0000-00-00 00:00:00' or
					$table->peak_time_created_at == NULL
				) {
					if ($peak_time_created_at != '') {
						if (strtotime($table->created_at) <  $start) $peak_time_created_at = date("Y-m-d H:i:s", $start);
						$table->peak_time_created_at = $peak_time_created_at;
					}
				}

				if (
					$table->peak_time_created_at != '' and
					$table->peak_time_created_at != '0000-00-00 00:00:00' and
					$table->peak_time_created_at != NULL
				) {
					if (!empty($end_time_array)) {
						for ($i = 0; $i < count($end_time_array); $i++) {
							$start = strtotime(date('Y-m-d') . ' ' . $start_time_array[$i]);
							$end = strtotime(date('Y-m-d') . ' ' . $end_time_array[$i]);

							if ($start <= $now and $now <= $end) {
								$table->peak_time_updated_at = now();
								break;
							} else {
								if (strtotime($table->peak_time_created_at) >= $start and $now >= $end) {
									$update_time = date('Y-m-d') . ' ' . $end_time_array[$i];
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
				if ($new_entry) {
					$table_new = new Activity;
					$table_new->user_id = $user_id;
					$table_new->created_at = $update_time;
					$table_new->updated_at = $update_time;
					$table_new->save();
				}
			}
		}
		CustomLog::info("driver_activity " . $user_id . " " . date('d-m-Y H:i:s'));
	}

	public static function driver_activity_offline($user_id)
	{
		$now = strtotime(now());
		$start_end_time = self::pick_time();
		$start_time_array = $start_end_time['start_time'];
		$end_time_array = $start_end_time['end_time'];

		$last_activity = Activity::where('user_id', $user_id)
			->orderBy('id', 'DESC')
			->first();

		if (is_object($last_activity)) {
			if ($last_activity->peak_time_created_at != '') {
				if (!empty($end_time_array)) {
					for ($i = 0; $i < count($end_time_array); $i++) {
						$start = strtotime(date('Y-m-d') . ' ' . $start_time_array[$i]);
						$end = strtotime(date('Y-m-d') . ' ' . $end_time_array[$i]);

						if ($start <= $now and $now <= $end) {
							$last_activity->peak_time_updated_at = now();
							$last_activity->updated_at =  now();
							break;
						} else {
							if (strtotime($last_activity->peak_time_created_at) >= $start and $now >= $end) {
								$update_time = date('Y-m-d') . ' ' . $end_time_array[$i];
								$last_activity->peak_time_updated_at = $update_time;
								$last_activity->updated_at = $update_time;
								break;
							}
						}
					}
				} else {
					$last_activity->updated_at = now();
				}
			} else {
				$last_activity->updated_at = now();
			}


			$last_activity->hit_time = date('H:i:s');
			$last_activity->save();



			Log::info("Offline Activity Updated:", ['user_id' => $user_id, 'hit_time' => now()]);
		}
	}

	/**
	 * Check the Document status from driver side
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */
	public function checkStatus(Request $request)
	{
		Log::info("check_status Api Stp:1 :", $request->all());

		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'user_type' => 'required|in:Driver,driver,Rider,rider',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return [
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			];
		}

		$user = User::where('id', $user_details->id)->where('user_type', $request->user_type)->first();

		if ($user == '') {
			return response()->json([
				'status_code' 		=> '0',
				'status_message'	=> trans('messages.api.invalid_credentials'),
			]);
		}

		$profile_picture = ProfilePicture::where('user_id', $user->id)
			->where('src', '=', '')
			->first();
		$profile_picture_2 = ProfilePicture::where('user_id', $user->id)
			->where('src', '=', 'https://driverapi.alesharide.com/images/user.jpeg')
			->first();

		if (is_object($profile_picture)) {
			$user->status = "profile_picture";
			$message = trans('messages.user.profile_picture_message');
		} else if (is_object($profile_picture_2)) {
			$user->status = "profile_picture";
			$message = trans('messages.user.profile_picture_message');
		} else if ($user->status == "Active") {
			$vehicle_documents = $user->driver_documents('Vehicle')->count();
			$driver_documents = $user->driver_documents('Driver')->count();

			if (!$user->vehicles->count()) {
				$user->status = "Car_details";
				$message = trans('messages.user.car_details_message1');
			} elseif (!$vehicle_documents) {
				$user->status = "Car_details";
				$message = trans('messages.user.car_details_message2');
			} elseif (!$driver_documents) {
				$user->status = "Document_details";
				$message = trans('messages.user.document_details_message');
			} else {
				$message = trans('messages.user.active_message');
			}
		} else if ($user->status == "Pending") {
			$message = trans('messages.user.pending_message');
		} else if ($user->status == "Document_details") {
			$message = trans('messages.user.document_details_message');
		} else if ($user->status == "Car_details") {
			if (!$user->vehicles->count())
				$message = trans('messages.user.car_details_message1');
			else
				$message = trans('messages.user.car_details_message2');
		} else {
			$message = trans('messages.user.inactive_message');
		}

		if ($user->status == "Active") {
			$status = 1;
		} else {
			$status = 0;
		}

		CustomLog::info("check_status Api Stp:2 :");

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> trans('messages.success'),
			'driver_status' 	=> $status,
			'driver_status_message' => $message,
		]);
	}

	public function cash_collected(Request $request)
	{
		Log::info("cash_collected Api Stp:1 :", $request->all());

		$user_details = JWTAuth::parseToken()->authenticate();
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
		if ($trip->status != 'Payment') {
			return response()->json([
				'status_code' => '0',
				'status_message' => __('messages.api.something_went_wrong'),
			]);
		}
		if ($trip->is_calculation == 0) {

			if ($request->confirmed_by_driver == "yes") {

				$payment_mode_change = [
					'payment_mode' => "Cash",
				];
				Trips::updateOrCreate(['id' => $request->trip_id], $payment_mode_change);
			}

			$data = [
				'trip_id' => $request->trip_id,
				'user_id' => $user_details->id,
				'save_to_trip_table' => 1,
			];
			// $confirmed_by_driver = false;
			// if ($request->confirmed_by_driver == "yes") {
			// 	$confirmed_by_driver = true;
			// }

			//$this->invoice_helper->calculation($data);
			$this->invoice_helper->calculation_v2($data);
			$trip = Trips::where('id', $request->trip_id)->first();
		}

		$trip_save = Trips::where('id', $request->trip_id)->first();
		$trip_save->status = 'Completed';
		// $trip_save->status = 'Rating';
		$trip_save->paykey = @$request->paykey;
		$trip_save->payment_status = 'Completed';
		$trip_save->save();



		// if ($confirmed_by_driver == true) {
		// 	if ($promo_amount > 0) {
		// 		$trips->payment_mode = 'Cash & Wallet';
		// 	}
		// 	// Check total Fare less than commission for promo applied

		// 	if ($total_fare < $driver_payout) {
		// 		$owe_amount = 0;
		// 		$driver_payout = abs($total_fare - $driver_payout);
		// 	} else {
		// 		$owe_amount = abs($total_fare - $driver_payout);
		// 		$driver_payout = 0;
		// 	}
		// 	$amount = $total_fare;
		// }else{}


		if ($trip->pool_id > 0) {

			$pool_trip = PoolTrip::with('trips')->find($trip->pool_id);
			$trips = $pool_trip->trips->whereIn('status', ['Scheduled', 'Begin trip', 'End trip', 'Rating', 'Payment'])->count();

			if (!$trips) {
				// update status
				$pool_trip->status = 'Rating';
				// $pool_trip->status = 'Completed';
				$pool_trip->save();
			}
		}

		$data = [
			'trip_id' => $request->trip_id,
			'correlation_id' => @$request->paykey,
			'driver_payout_status' => ($trip->driver_payout) ? 'Pending' : 'Completed',
			'payout_amount' => $trip->driver_payout,
			'driver_id' => $trip->driver_id,

		];

		Payment::updateOrCreate(['trip_id' => $request->trip_id], $data);
		$rider = User::where('id', $trip->user_id)->first();
		$driver_thumb_image = @$trip->driver_thumb_image != '' ? $trip->driver_thumb_image : url('images/user.jpeg');

		$push_data['push_title'] = __('messages.dashboard.cash_collect');
		$push_data['data'] = array(
			'trip_payment' => array(
				'status' 	=> __('messages.dashboard.cash_collect'),
				'trip_id' 	=> $request->trip_id,
				'driver_thumb_image' => $driver_thumb_image
			)
		);
		$this->request_helper->SendPushNotification($rider, $push_data);

		$schedule_ride = ScheduleRide::find($trip->ride_request->schedule_id);
		if (isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {

			$push_title = __('messages.trip_cash_collected');
			$text 		= __('messages.api.trip_total_fare', ['total_fare' => $trip->total_fare, 'currency' => $trip->currency_code]);

			$push_data['push_title'] = $push_title;
			$push_data['data'] = array(
				'custom_message' => array(
					'title' => $push_title,
					'message_data' => $text,
				)
			);

			$text = $push_title . $text;

			$this->request_helper->checkAndSendMessage($rider, $text, $push_data);
		}

		$invoice_helper = resolve('App\Http\Helper\InvoiceHelper');
		$promo_details = $invoice_helper->getUserPromoDetails($trip->user_id);

		CustomLog::info("cash_collected Api Stp:2 :");
		//App\Http\Controllers\Invoice::invoice_email($trip->user_id, $trip->id);
		$this->invoice->invoice_email(base64_encode($trip->user_id), base64_encode($trip->id));
		$user = User::where('id', $user_details->id)->first();
		// $this->bonus_helper->adjust_driver_signup_bonus($user);
		// $this->bonus_helper->adjust_driver_trip_bonus($user);
		// $this->bonus_helper->adjust_driver_referral_bonus($user);

		$this->bonus_helper->discount_offer_save($trip, $rider);

		$this->bonus_helper->update_bonus($user, "DriverJoiningBonus", $trip);
		$this->bonus_helper->update_bonus($user, "DriverReferralBonus", $trip);
		$this->bonus_helper->update_bonus($user, "DriverTripBonus", $trip);


		$rider_user = $trip->users;
		$pushData['driver_name'] = $user_details->first_name;
		$pushData['device_id'] = $rider_user->device_id;
		$pushData['device_type'] = $rider_user->device_type;
		$pushData['message_index'] = 'provide_rating';
		$this->pushNotificationService->tripPushNotificationMessage($pushData);

		$pushData['rider_name'] = $rider_user->first_name;
		$pushData['device_id'] = $rider_user->device_id;
		$pushData['device_type'] = $rider_user->device_type;
		$pushData['message_index'] = 'rating_finish';
		$this->pushNotificationService->tripPushNotificationMessage($pushData);


		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> "Cash Collected Successfully",
			'trip_id' 			=> $trip->id,
			'promo_details' 	=> $promo_details,
			'rider_thumb_image' => $trip->rider_thumb_image,
		]);
	}

	/**
	 * Display Country List
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */
	public function country_list(Request $request)
	{
		Log::info("country_list Api Stp:1 :", $request->all());

		$data = Country::select(
			'id as country_id',
			'long_name as country_name',
			'short_name as country_code'
		)->get();

		CustomLog::info("country_list Api Stp:2 :");

		return response()->json([
			'status_code' => '1',
			'status_message' => 'Country Listed Successfully',
			'country_list' => $data,
		]);
	}

	/**
	 * Driver Bank Details if company is private
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */
	public function driver_bank_details(Request $request)
	{
		Log::info("driver_bank_details Api Stp:1 :", $request->all());

		$user = JWTAuth::toUser($request->token);

		if (!$request) {
			$bank_detail = BankDetail::where('user_id', $user->id)->first();
			if (isset($bank_detail)) {
				$bank_detail = (object)[];
			}
		} else {
			$rules = array(
				'account_holder_name' => 'required',
				'account_number' => 'required',
				'bank_name' => 'required',
				'bank_location' => 'required',
				'bank_code' => 'required',
			);

			$attributes = array(
				'account_holder_name'  => trans('messages.account.holder_name'),
				'account_number'  => trans('messages.account.account_number'),
				'bank_name'  => trans('messages.account.bank_name'),
				'bank_location'  => trans('messages.account.bank_location'),
				'bank_code'  => trans('messages.account.bank_code'),
			);

			$messages   = array('required' => ':attribute ' . trans('messages.home.field_is_required') . '',);
			$validator = Validator::make($request->all(), $rules, $messages, $attributes);

			if ($validator->fails()) {
				return response()->json([
					'status_code' => '0',
					'status_message' => $validator->messages()->first()
				]);
			}

			$bank_detail = BankDetail::firstOrNew(['user_id' => $user->id]);

			$bank_detail->user_id = $user->id;
			$bank_detail->holder_name = $request->account_holder_name;
			$bank_detail->account_number = $request->account_number;
			$bank_detail->bank_name = $request->bank_name;
			$bank_detail->bank_location = $request->bank_location;
			$bank_detail->code = $request->bank_code;
			$bank_detail->save();
		}
		CustomLog::info("driver_bank_details Api Stp:2 :");
		return response()->json([
			'status_code' => '1',
			'status_message' => 'Listed Successfully',
			'bank_detail' =>  $bank_detail,
		]);
	}

	public function pay_to_admin(Request $request)
	{
		Log::info("pay_to_admin Api Stp:1 :", $request->all());

		$user 	= JWTAuth::toUser($request->token);

		//validation started
		$rules = array(
			'applied_referral_amount' => 'In:0,1',
			'amount'	=> 'numeric|min:0',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

		$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
		if ($owe_amount && $owe_amount->amount > 0) {
			//applying referral amount start
			if ($request->has('applied_referral_amount') && $request->applied_referral_amount == 1) {

				$total_referral_amount = ReferralUser::where('user_id', $user->id)
					->where('payment_status', 'Completed')
					->where('pending_amount', '>', 0)
					->get()
					->sum('pending_amount');

				if ($owe_amount->amount < $total_referral_amount) {
					$total_referral_amount = $owe_amount->amount;
				}

				if ($total_referral_amount > 0) {
					$applied_referrals = new AppliedReferrals;
					$applied_referrals->amount = $total_referral_amount;
					$applied_referrals->user_id = $user->id;
					$applied_referrals->currency_code = $user->currency->code;
					$applied_referrals->save();

					$this->invoice_helper->referralUpdate($user->id, $total_referral_amount, $user->currency->code);

					//owe amount
					$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
					$currency_code = $owe_amount->currency_code;
					$owe_amount->amount = $owe_amount->amount - $total_referral_amount;
					$owe_amount->currency_code = $currency_code;
					$owe_amount->save();

					$payment = new DriverOweAmountPayment;
					$payment->user_id = $user->id;
					$payment->transaction_id = "";
					$payment->amount = $total_referral_amount;
					$payment->currency_code = $currency_code;
					$payment->status = 1;
					$payment->save();

					$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
				}
			}
			//applying referral amount

			//pay to admin from payout preference start
			$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
			if ($owe_amount->amount < $request->amount) {
				$request->amount = $owe_amount->amount;
			}
			$amount = $request->amount;

			if ($request->has('amount') && $request->amount > 0) {
				if ($owe_amount->amount < $request->amount) {
					return response()->json([
						'status_code' => '0',
						'status_message' => trans('messages.api.invalid'),
					]);
				}

				$rules = array(
					'payment_type' 	=> 'required|in:paypal,stripe,braintree',
				);

				if ($request->payment_type != "stripe") {
					$rules['pay_key'] = 'required';
				}

				$validator = Validator::make($request->all(), $rules);

				if ($validator->fails()) {
					return response()->json([
						'status_code' => '0',
						'status_message' => $validator->messages()->first()
					]);
				}

				$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
				$total_owe_amount = $owe_amount->amount;
				$currency_code = $owe_amount->currency_code;
				$remaining_amount = $total_owe_amount - $amount;

				$payment_data['currency_code'] = $user->currency_code;
				$payment_data['amount'] = $amount;
				$payment_data['user_id'] = $user->id;

				if ($request->payment_type == 'stripe') {
					$payment_method = PaymentMethod::where('user_id', $user->id)->first();
					if ($payment_method == '') {
						return response()->json([
							'status_code' => '0',
							'status_message' => __('messages.api.please_add_card_details'),
						]);
					}

					$payment_data = array(
						"amount" 		=> $amount * 100,
						'currency' 		=> $user->currency_code,
						'description' 	=> 'Owe Payment By ' . $user->first_name,
						"customer" 		=> $payment_method->customer_id,
						'payment_method' => $payment_method->payment_method_id,
						'confirm' 		=> true,
						'off_session' 	=> true,
					);
				}

				try {
					$service = 'App\Services\Payments\\' . ucfirst($request->payment_type) . "Payment";
					$payment_service = resolve($service);
					$pay_result = $payment_service->makePayment($payment_data, $request->pay_key);

					if (!$pay_result->status) {
						return response()->json([
							'status_code' => '0',
							'status_message' => $pay_result->status_message,
						]);
					}

					if ($pay_result->is_two_step) {
						return response()->json([
							'status_code' => '2',
							'status_message' => $pay_result->status_message,
							'two_step_id' => $pay_result->two_step_id,
						]);
					}
				} catch (\Exception $e) {
					return response()->json([
						'status_code' => '0',
						'status_message' => $e->getMessage(),
					]);
				}

				//owe amount
				$owe_amount->amount = $remaining_amount;
				$owe_amount->currency_code = $currency_code;
				$owe_amount->save();

				$payment = new DriverOweAmountPayment;
				$payment->user_id = $user->id;
				$payment->transaction_id = $pay_result->transaction_id;
				$payment->amount = $amount;
				$payment->status = 1;
				$payment->currency_code = $currency_code;
				$payment->save();

				$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
			}

			$referral_amount = ReferralUser::where('user_id', $user->id)->where('payment_status', 'Completed')->where('pending_amount', '>', 0)->get();
			$referral_amount = number_format($referral_amount->sum('pending_amount'), 2, '.', '');

			return response()->json([
				'status_code' 	=> '1',
				'status_message' => __('messages.api.payout_successfully'),
				'referral_amount' => $referral_amount,
				'owe_amount' 	=> $owe_amount->amount,
				'currency_code' => $owe_amount->currency_code
			]);
		}
		CustomLog::info("pay_to_admin Api Stp:2 :");
		return response()->json([
			'status_code' => '0',
			'status_message' => __('messages.api.not_generate_amount'),
		]);
	}


	public function updateVehicle(Request $request)
	{
		Log::info("update_vehicle Api Stp:1 :", $request->all());
		$user = JWTAuth::toUser($request->token);

		$rules = array(
			'vehicle_type' 	=> 'required',
			'make_id' 		=> 'required',
			'model_id'		=> 'required',
			'year'			=> 'required',
			'color'			=> 'required',

			//'sticker_mode'  => 'required',

		);

		if ($request->id) {
			$rules['license_no'] = 'required|unique:vehicle,vehicle_number,' . $request->id;
		} else {
			$rules['license_no'] = 'required|unique:vehicle,vehicle_number';
		}

		$attributes = array(
			'license_no' 	=> trans('messages.account.license_no'),
			'vehicle_type'	=> trans('messages.account.vehicle_type'),
			'make_id'		=> trans('messages.account.make_id'),
			'model_id'		=> trans('messages.account.model_id'),
			'year'			=> trans('messages.account.year'),
		);

		$validator = Validator::make($request->all(), $rules, $attributes);
		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first(),
			]);
		}

		$other_update = 0;

		if (!$request->id) {
			$vehicles =  new Vehicle;
			$other_update = 1;
		} else {
			$vehicles = Vehicle::find($request->id);
			if (!$vehicles) {
				return response()->json([
					'status_code' => '0',
					'status_message' => trans('messages.account.invalid_id'),
				]);
			}

			if ($request->license_no != $vehicles->vehicle_number || $request->make_id != $vehicles->vehicle_make_id || $request->model_id != $vehicles->vehicle_model_id || $request->year != $vehicles->year || $request->color != $vehicles->color) {
				//|| $request->sticker_mode!=$vehicles->sticker_mode

				$other_update = 1;
			}
		}

		$make_name = MakeVehicle::whereId($request->make_id)->value('make_vehicle_name');
		$model_name = VehicleModel::whereId($request->model_id)->value('model_name');

		$vehicles->vehicle_name = $make_name . ' ' . $model_name;
		$vehicles->company_id   = 1;

		$type_name = '';
		$vehicle_types_id = $request->vehicle_type;
		$types_id = explode(',', $vehicle_types_id);
		foreach ($types_id as $type_id) {
			// for vehicle type name
			if ($type_name != '') {
				$delimeter = ',';
			} else {
				$delimeter = '';
			}
			$car_name = CarType::find($type_id)->car_name;
			$type_name .= $delimeter . $car_name;
		}

		$vehicles->vehicle_number   = $request->license_no;
		$vehicles->vehicle_id     	= $vehicle_types_id;
		$vehicles->vehicle_type     = $type_name;
		$vehicles->vehicle_make_id  = $request->make_id;
		$vehicles->vehicle_model_id = $request->model_id;
		$vehicles->user_id   		= $user->id;
		$vehicles->year   			= $request->year;
		$vehicles->color   			= $request->color;
		//$vehicles->sticker_mode   	= $request->sticker_mode;


		if ($other_update == 1) {

			$user = User::find($user->id);

			if (!$user->vehicle || $vehicles->default_type == '1') {
				$user->status = UserStatusUpdate($user);
				$user->save();
			}

			$vehicles->is_active = '0';
			$vehicles->status = "Inactive";
			$vehicles->default_type = '0';

			if (isLiveEnv()) {

				if ($user->vehicles->count() == 1 && $request->id) {

					$vehicle_documents = $user->driver_documents('Vehicle')->count();
					$required_documents = UserDocuments('Vehicle', $user, $request->id);

					if ($user->vehicles->count() == 1 && $vehicle_documents == count($required_documents)) {
						$vehicles->is_active = '1';
						$vehicles->status = "Active";
						$vehicles->default_type = '1';
					}
				}
			}
		}
		$vehicles->save();

		// for default selection update car type in driver location
		if ($vehicles->default_type == '1') {

			$driver_location = DriverLocation::where('user_id', $vehicles->user_id)->first();

			if ($driver_location) {
				$dr_location['user_id']     = $vehicles->user_id;
				$dr_location['latitude']    = $driver_location->latitude;
				$dr_location['longitude']   = $driver_location->longitude;
				$dr_location['status']      = $driver_location->status;
				$dr_location['pool_trip_id'] = $driver_location->pool_trip_id;

				$vehicle_types = explode(',', $vehicles->vehicle_id);
				foreach ($vehicle_types as $vehicle_type) {
					$dr_location['car_id'] = $vehicle_type;
					DriverLocation::updateOrCreate(['user_id' => $vehicles->user_id, 'car_id' => $vehicle_type], $dr_location);
				}
				DriverLocation::where('user_id', $vehicles->user_id)->whereNotIn('car_id', $vehicle_types)->delete();
			}
		}

		// save filter options
		$options = explode(',', $request->options);
		$filter_insert = FilterObject::optionsInsert('vehicle', $vehicles->id, $options);

		// get vehicles
		$vehicles_detail = Vehicle::where('user_id', $user->id)->get();
		$vehicles_details = [];
		foreach ($vehicles_detail as $key => $value) {
			$vehicles_details[$key]['id'] 			= $value->id;
			$vehicles_details[$key]['vehicle_name'] = $value->vehicle_name;
			$vehicles_details[$key]['make'] 		= $value->makeWithSelected;
			$vehicles_details[$key]['model'] 		= $value->modelWithSelected;
			$vehicles_details[$key]['license_number'] = $value->vehicle_number;
			$vehicles_details[$key]['year'] 		= $value->year;
			$vehicles_details[$key]['color'] 		= $value->color;

			// 	Added By Nishat 4-12-2021 Start
			//$vehicles_details[$key]['sticker_mode'] = $value->sticker_mode;
			// Added By Nishat 4-12-2021 End


			$vehicles_details[$key]['vehicleImageURL'] = url('static/Driving-Licence.jpg');
			$vehicles_details[$key]['status'] 		= $value->trans_status;
			$vehicles_details[$key]['is_active'] 	= $value->is_active;
			$vehicles_details[$key]['is_default'] 	= $value->default_type;

			$vehicle_types = explode(',', $value->vehicle_id);
			$vehicles_details[$key]['vehicle_types'] = getVehicleType($vehicle_types, $value->default_type);
			$vehicles_details[$key]['vechile_documents'] = UserDocuments('Vehicle', $user, $value->id);

			// get filter options
			$female_riders = FilterObject::exist('vehicle', $value->id, 1) ? true : false;
			$handicap = FilterObject::exist('vehicle', $value->id, 2) ? true : false;
			$child_seat = FilterObject::exist('vehicle', $value->id, 3) ? true : false;
			$skip = $user->gender == '1' ? true : false;
			$request_options = FilterOption::options($skip, $female_riders, $handicap, $child_seat);

			$vehicles_details[$key]['request_options'] = $request_options;
		}

		if (!$request->id) {
			$message = trans('messages.user.add_success');
		} else {
			$message = trans('messages.user.update_success');
		}
		CustomLog::info("update_vehicle Api Stp:2 :");
		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> $message,
			'vehicles_details'  => $vehicles_details
		]);
	}

	public function deleteVehicle(Request $request)
	{

		Log::info("delete_vehicle Api Stp:1 :", $request->all());

		$user = JWTAuth::toUser($request->token);

		$rules['id'] = 'required';

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first(),
			]);
		}

		$vehicle = Vehicle::find($request->id);

		if (!$vehicle) {
			$status = '0';
			$message = trans('messages.account.invalid_id');
		} else if ($vehicle->default_type == '1') {
			$status = '0';
			$message = trans('messages.user.default_vehicle_delete_msg');
		} else {
			try {
				// delete vehicle
				$vehicle->delete();
				$filters_delete = FilterObject::whereObjectId($request->id)->delete();
				// update status if there is no active vehicles
				if (!$user->vehicles()->active()->count()) {
					User::where('id', $user->id)->update(['status' => 'Car_details']);
				}
				$status = '1';
				$message = trans('messages.user.delete_success');
			} catch (\Exception $e) {
				$status = '0';
				$message = $e->getMessage();
			}
		}

		CustomLog::info("delete_vehicle Api Stp:2 :");

		return response()->json([
			'status_code' => $status,
			'status_message' => $message,
		]);
	}

	public function updateDefaultVehicle(Request $request)
	{
		Log::info("update_default_vehicle Api Stp:1 :", $request->all());
		$rules['vehicle_id'] = 'required';

		$validator = Validator::make($request->all(), $rules);
		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first(),
			]);
		}

		$user = JWTAuth::toUser($request->token);
		$user_id = $user->id;
		$vehicle_id = $request->vehicle_id;
		$vehicle_exists = Vehicle::findVehicleExist($vehicle_id, $user_id);

		if (!$vehicle_exists) {
			return response()->json([
				'status_code' => '0',
				'status_message' => trans('messages.account.invalid_id'),
			]);
		}

		if ($vehicle_exists->status != 'Active') {
			return response()->json([
				'status_code' => '0',
				'status_message' => trans('messages.user.vehicle_not_activated'),
			]);
		}

		// Check pre default vehicle is in ride or not
		$driver_status = checkDefault($user_id, $vehicle_id, '1');
		if ($driver_status == '1') {
			return response()->json([
				'status_code' => '0',
				'status_message' => trans('messages.user.default_vehicle_trip_error'),
			]);
		}

		// check pre default vehicle to update non default
		$pre_default_vehicle = Vehicle::getPreDefaultVehicle($user_id);

		if ($pre_default_vehicle) {
			// set as non default vehicle
			$pre_default_vehicle = Vehicle::find($pre_default_vehicle->id);
			$pre_default_vehicle->default_type = '0';
			$pre_default_vehicle->save();
		}

		// update default vehicle
		$default_vehicle = Vehicle::find($vehicle_id);
		$default_vehicle->default_type = '1';
		$default_vehicle->save();

		// update vehicle types in driver location
		$driver_location = DriverLocation::where('user_id', $user_id)->first();

		$dr_location['latitude'] = $user_id;
		$dr_location['latitude'] = $driver_location->latitude;
		$dr_location['longitude'] = $driver_location->longitude;
		$dr_location['status'] 	 = $driver_location->status;

		$vehicle_list = $default_vehicle->vehicle_id;
		$vehicle_list = explode(',', $vehicle_list);

		foreach ($vehicle_list as $vehicle) {
			$dr_location['car_id'] = $vehicle;
			DriverLocation::updateOrCreate(['user_id' => $user_id, 'car_id' => $vehicle], $dr_location);
		}

		DriverLocation::where('user_id', $user_id)->whereNotIn('car_id', $vehicle_list)->delete();
		CustomLog::info("update_default_vehicle Api Stp:2 :");
		return response()->json([
			'status_code' => '1',
			'status_message' => trans('messages.user.update_success'),
		]);
	}

	public function update_document(Request $request)
	{
		Log::info("update_document Api Stp:1 :", $request->all());

		$user_details = JWTAuth::parseToken()->authenticate();
		$user_id = $user_details->id;
		$rules = array(
			'type' 				=> 'required|in:Driver,Vehicle',
			'document_id'      	=> 'required|exists:documents,id',
		);

		if ($request->type == "Vehicle") {
			$rules['vehicle_id'] = 'required';
		}

		if ($request->document_id) {
			$documents = Documents::find($request->document_id);

			if ($documents) {
				$vehicleID = ($request->type == 'Vehicle') ? $request->vehicle_id : 0;
				$checkDoc = DriverDocuments::where('type', $request->type)->where('vehicle_id', $vehicleID)->where('user_id', $user_id)->where('document_id', $documents->id)->first();

				if ($documents->document_name != "Enlistment Certificate") {
					if ($documents->expire_on_date == 'Yes' && !$checkDoc) {
						$rules['expired_date'] = 'required|date|date_format:Y-m-d';
					}
				}

				if (!$checkDoc) {
					$rules['document_image'] = 'required|mimes:jpg,jpeg,png,gif';
				}
			}
		}

		$messages = [
			'required' 	=> ':attribute ' . trans('messages.field_is_required'),
			'exists' 	=> trans('messages.document_select'),
		];

		$attributes = array(
			'type'  => trans('messages.account.type'),
			'document_id'  => trans('messages.account.document_id'),
			'vehicle_id'  => trans('messages.account.vehicle_id'),
			'expired_date'  => trans('messages.account.expired_date'),
			'document_image'  => trans('messages.account.document_image'),
		);

		$validator = Validator::make($request->all(), $rules, $messages, $attributes);
		if ($validator->fails()) {
			return response()->json([
				'status_code'     => '0',
				'status_message' => $validator->messages()->first(),
			]);
		}

		if ($checkDoc == '') {
			$driver_document = new DriverDocuments;
		} else {
			$driver_document = DriverDocuments::find($checkDoc->id);
		}

		$driver_document->user_id 		= $user_id;
		$driver_document->type 			= $request->type;
		$driver_document->document_type = $request->document_type;
		$driver_document->vehicle_id 	= $vehicleID;
		$driver_document->document_id 	= $documents->id;

		if ($request->hasFile('document_image')) {
			$image_uploader = resolve('App\Contracts\ImageHandlerInterface');
			$target_dir 	= '/images/users/' . $user_details->id;
			$image 			= $request->file('document_image');
			$extension 		= $image->getClientOriginalExtension();
			$file_name 		= $documents->doc_name . "_" . time() . "." . $extension;
			$options 		= compact('target_dir', 'file_name');

			$upload_result 	= $image_uploader->upload($image, $options);

			if (!$upload_result['status']) {
				return response()->json([
					'status_code' 		=> "0",
					'status_message' 	=> $upload_result['status_message'],
				]);
			}

			$filename = asset($target_dir . '/' . $upload_result['file_name']);
			$driver_document->document = $filename;
		}

		if ($request->expired_date) {
			$driver_document->expired_date 	= $request->expired_date;
		}
		$driver_document->status = '0';
		$driver_document->save();

		$user = User::find($user_id);

		if ($request->type == 'Vehicle') {
			$vehicle = Vehicle::find($vehicleID);

			if ($vehicle->default_type == '1' || !$user->vehicle) {
				$user->status = UserStatusUpdate($user);
				$user->save();
			}

			$vehicle->is_active = '0';
			$vehicle->status = "Inactive";
			$vehicle->default_type = '0';

			if (isLiveEnv()) {
				$vehicle_documents = $user->driver_documents('Vehicle')->count();
				$required_documents = UserDocuments('Vehicle', $user, $vehicleID);
				if ($user->vehicles->count() == 1 && $vehicle_documents == count($required_documents)) {
					$vehicle->is_active = '1';
					$vehicle->status = "Active";
					$vehicle->default_type = '1';
				}
			}

			$vehicle->save();
		} else {
			$user->status = UserStatusUpdate($user);
			$user->save();
		}

		CustomLog::info("update_document Api Stp:2 :");

		return response()->json([
			'status_code' 			=> "1",
			'status_message' 		=> "Upload Successfully",
			'document_url' 			=> $driver_document->document ?? '',
			'document_status'		=> 0,
		]);
	}



	public function check_driver_balance()
	{
		Log::info("check_driver_balance Api Stp:1 :");
		$balances = DB::table('driver_balances')
			->where('user_id', auth()->user()->id)
			->where('status', 'pending') //->where('status', '!=', 'paid')
			->get();
		$total_bonus = 0;
		CustomLog::info("check_driver_balance Api Stp:2 :");
		if ($balances) {
			foreach ($balances as $single_balances) {
				$total_bonus += $single_balances->amount;
			}
			return response()->json([
				'status_code' 			=> "1",
				'status_message' 		=> "Total Driver Balance Amount",
				'total_balance' 		=> $total_bonus,
			]);
		} else {
			return response()->json([
				'status_code' 			=> "0",
				'status_message' 		=> "No Balance Found",
				'total_balance' 		=> $total_bonus,
			]);
		}
	}
	public function check_driver_balance_v2()
	{
		Log::info("v2/check_driver_balance Api Stp:1 :");
		$balances = DB::table('driver_balances')
			->where('user_id', auth()->user()->id)
			->where('status', 'pending') //->where('status', '!=', 'paid')
			->get();

		$requsted_balances = DB::table('driver_balances')
			->where('user_id', auth()->user()->id)
			->where('status', 'requested')
			->get();
		$requsted_balances_to_array = json_decode(json_encode($requsted_balances), true);
		$store_requsted_balances_by_request_date = array();
		for ($i = 0; $i < count($requsted_balances_to_array); $i++) {
			$index = -1;
			for ($j = 0; $j < count($store_requsted_balances_by_request_date); $j++) {
				if ($requsted_balances_to_array[$i]['request_date'] == $store_requsted_balances_by_request_date[$j]['request_date']) {
					$index = $j;
					break;
				}
			}
			if ($index == -1) {
				array_push($store_requsted_balances_by_request_date, $requsted_balances_to_array[$i]);
			} else {
				$store_requsted_balances_by_request_date[$index]['amount'] += $requsted_balances_to_array[$i]['amount'];
			}
		}

		$total_bonus = 0;
		if ($balances) {
			foreach ($balances as $single_balances) {
				$total_bonus += $single_balances->amount;
			}
			CustomLog::info("v2/check_driver_balance Api Stp:2 :");
			return response()->json([
				'status_code'             => "1",
				'status_message'         => "Total Driver Balance Amount",
				'total_balance'         => $total_bonus,
				'requsted_balances_by_request_date' => $store_requsted_balances_by_request_date,
			]);
		} else {
			CustomLog::info("v2/check_driver_balance Api Stp:3 :");
			return response()->json([
				'status_code'             => "0",
				'status_message'         => "No Balance Found",
				'total_balance'         => $total_bonus,
				'requsted_balances_by_date' => $store_requsted_balances_by_request_date,
			]);
		}
	}

	public function driver_balance_withdraw()
	{
		Log::info("driver_balance_withdraw_req Api Stp:1 :");
		$user_id = auth()->user()->id;
		$balances = DB::table('driver_balances')->where('user_id', $user_id)->where('status', 'pending')->get();
		$total_bonus = 0;
		if ($balances) {
			$current_time = Carbon::now();
			$update_driver_balances = DB::table('driver_balances')
				->where('user_id', $user_id)
				->where('status', 'pending')
				->update([
					'status' => 'requested',
					'request_date' => $current_time,
				]);

			$balances = DB::table('driver_balances')
				->where('user_id', $user_id)
				//->where('status', '!=', 'paid')
				->where('status', 'pending')
				->get();

			foreach ($balances as $single_balances) {
				$total_bonus += $single_balances->amount;
			}
			if ($update_driver_balances) {
				CustomLog::info("driver_balance_withdraw_req Api Stp:2 :");
				return response()->json([
					'status_code' 			=> "1",
					'status_message' 		=> "Withdraw Request Successful",
					'balance' 				=> $total_bonus,
				]);
			} else {
				return response()->json([
					'status_code' 			=> "0",
					'status_message' 		=> "Withdraw Request Unsuccessful",
					'balance' 				=> $total_bonus,
				]);
			}
		} else {
			return response()->json([
				'status_code' 			=> "0",
				'status_message' 		=> "No Balance Found",
				'balance' 				=> $total_bonus,
			]);
		}
	}





	public function payout_transaction_history()
	{

		$transaction_history = DB::table('payment')
			->where('driver_id', 10471)
			->where('status', 'paid')
			->get();
		$transaction_history_to_array = json_decode(json_encode($transaction_history), true);
		$store_transaction_history_by_transaction_date = array();
		for ($i = 0; $i < count($transaction_history_to_array); $i++) {
			$index = -1;
			for ($j = 0; $j < count($store_transaction_history_by_transaction_date); $j++) {
				if ($transaction_history_to_array[$i]['payment_date'] == $store_transaction_history_by_transaction_date[$j]['request_date']) {
					$index = $j;
					break;
				}
			}
			if ($index == -1) {
				array_push($store_requsted_balances_by_request_date, $transaction_history_to_array[$i]);
			} else {
				$store_transaction_history_by_transaction_date[$index]['payout_amount'] += $transaction_history_to_array[$i]['payout_amount'];
			}
		}

		if ($transaction_history) {
			return response()->json([
				'status_code'             => "1",
				'status_message'         => "Total Driver Balance Amount",
				'requsted_balances_by_request_date' => $store_transaction_history_by_transaction_date,
			]);
		} else {
			return response()->json([
				'status_code'             => "0",
				'status_message'         => "No Transaction History Found",
			]);
		}
	}


	public function get_driver_document(Request $request)
	{
		Log::info("get_driver_document Stp:1 :", $request->all());
		$user_details = JWTAuth::parseToken()->authenticate();
		$user_id = $user_details->id;
		$rules = array(
			'type' 				=> 'required|in:Driver,Vehicle',
		);
		$messages = [
			'required' 	=> ':attribute ' . trans('messages.field_is_required'),
		];
		$validator = Validator::make($request->all(), $rules, $messages);
		if ($validator->fails()) {
			return response()->json([
				'status_code'     => '0',
				'status_message' => $validator->messages()->first(),
			]);
		}
		if ($request->type == "Vehicle") {
			if ($request->vehicle_id != "") {
				$VehicleDocuments = DB::table('driver_documents')
					->select(
						'vehicle.id as vehicle_id',


						'documents.document_name as name',
						'documents.expire_on_date as expiry_required',

						'driver_documents.document',
						'driver_documents.id as document_id',
						'driver_documents.document_type',
						'driver_documents.document_id as document_identityfire_id',
						'driver_documents.status', //document status
						'driver_documents.expired_date',


						'vehicle.vehicle_type',
						'vehicle.vehicle_name',
						'vehicle.vehicle_number',
						'vehicle.year',
						'vehicle.color',
						'vehicle.default_type',
						'vehicle.status as vehicle_status',


					)
					->leftJoin('documents', function ($join) {
						$join->on('driver_documents.document_id', '=', 'documents.id');
					})
					->leftJoin('vehicle', function ($join) {
						$join->on('driver_documents.vehicle_id', '=', 'vehicle.id');
					})
					->where('driver_documents.vehicle_id', $request->vehicle_id)
					->where('driver_documents.user_id', $user_id)->get();

				if (is_object($VehicleDocuments)) {
					$notfound_document_id_array = array();
					$exist_document_id_array = array();
					$all_document_id_array = DB::table('documents')->where('type', 'Vehicle')->get()->pluck('id')->toArray();
					foreach ($VehicleDocuments as $VehicleDocument1) $exist_document_id_array[] = $VehicleDocument1->document_identityfire_id;
					$notfound_document_id_array = array_diff($all_document_id_array, $exist_document_id_array);
					$notfoundDocuments = DB::table('documents')->where('type', 'Vehicle')->where('documents.status', "Active")->whereIn('id', $notfound_document_id_array)->get();
					$arr1 = array();
					$arr2 = array(
						"vehicle_id" => "",
						"name" => "",
						"expiry_required" => "",


						"document" => "",
						"document_id" => "",
						"document_type" => "", //slip/nid
						"document_identityfire_id" => "",
						"status" => "", //document status
						"expired_date" => "",


						"vehicle_type" => "",
						"vehicle_name" => "",
						"vehicle_number" => "",
						"year" => "",
						"color" => "",
						"default_type" => "",
						"vehicle_status" => "",


					);
					$vehicle_data = DB::table('vehicle')->where('id', $request->vehicle_id)->first();

					foreach ($notfoundDocuments as $notfoundDocument) {
						$arr2['vehicle_id'] = $request->vehicle_id;


						$arr2['name'] = $notfoundDocument->document_name;
						$arr2['expiry_required'] = $notfoundDocument->expire_on_date == "Yes" ? '1' : '0';
						$arr2['document_identityfire_id'] = $notfoundDocument->id;

						$arr2['vehicle_type'] = $vehicle_data->vehicle_type;
						$arr2['vehicle_name'] = $vehicle_data->vehicle_name;
						$arr2['vehicle_number'] = $vehicle_data->vehicle_number;
						$arr2['year'] = $vehicle_data->year;
						$arr2['color'] = $vehicle_data->color;
						$arr2['default_type'] = $vehicle_data->default_type;
						$arr2['vehicle_status'] = $vehicle_data->status;

						array_push($arr1, $arr2);
					}
					foreach ($VehicleDocuments as $b) {
						$arr2['vehicle_id'] = $b->vehicle_id;

						$arr2['name'] = $b->name;
						$arr2['expiry_required'] = $b->expiry_required == "Yes" ? '1' : '0';

						$arr2['document'] = $b->document;
						$arr2['document_id'] = $b->document_id;
						$arr2['document_type'] = $b->document_type == null ? '' : $b->document_type;
						$arr2['document_identityfire_id'] = $b->document_identityfire_id;
						$arr2['status'] = $b->status;
						$arr2['expired_date'] = $b->expired_date;


						$arr2['vehicle_type'] = $b->vehicle_type;
						$arr2['vehicle_name'] = $b->vehicle_name;
						$arr2['vehicle_number'] = $b->vehicle_number;
						$arr2['year'] = $b->year;
						$arr2['color'] = $b->color;
						$arr2['default_type'] = $b->default_type;
						$arr2['vehicle_status'] = $b->vehicle_status;

						array_push($arr1, $arr2);
					}
					return response()->json([
						'status_code'     => '1',
						'vehicle_documents' => $arr1,
					]);
				} else {

					$notfoundDocuments = DB::table('documents')->where('type', 'Vehicle')->where('status', 'Active')->get();
					$arr1 = array();
					$arr2 = array(
						"vehicle_id" => $request->vehicle_id,

						"name" => "",
						"expiry_required" => "",

						"document" => "",
						"document_id" => "",
						"document_type" => "", //slip/nid
						"document_identityfire_id" => "",
						"status" => "", //document status
						"expired_date" => "",


						"vehicle_type" => "",
						"vehicle_name" => "",
						"vehicle_number" => "",
						"year" => "",
						"color" => "",
						"default_type" => "",
						"vehicle_status" => "",

					);

					$vehicle_data = DB::table('vehicle')->where('id', $request->vehicle_id)->where('user_id', $user_id)->first();

					if (is_object($VehicleDocuments)) {
						foreach ($notfoundDocuments as $notfoundDocument) {

							$arr2['vehicle_id'] = $request->vehicle_id;

							$arr2['name'] = $notfoundDocument->document_name;
							$arr2['expiry_required'] = $notfoundDocument->expire_on_date == "Yes" ? '1' : '0';
							$arr2['document_identityfire_id'] = $notfoundDocument->id;

							$arr2['vehicle_type'] = $vehicle_data->vehicle_type;
							$arr2['vehicle_name'] = $vehicle_data->vehicle_name;
							$arr2['vehicle_number'] = $vehicle_data->vehicle_number;
							$arr2['year'] = $vehicle_data->year;
							$arr2['color'] = $vehicle_data->color;
							$arr2['default_type'] = $vehicle_data->default_type;
							$arr2['vehicle_status'] = $vehicle_data->status;

							array_push($arr1, $arr2);
						}

						return response()->json([
							'status_code'     => '1',
							'vehicle_documents' => $arr1,
						]);
					}
				}
			} else {
				$VehicleDocuments = DB::table('driver_documents')
					->select(
						'vehicle.id as vehicle_id',
						'documents.document_name',
						'driver_documents.document as document_url',
						'driver_documents.document_type',
						'driver_documents.status',
						'driver_documents.expired_date',
						'vehicle.vehicle_type',
						'vehicle.vehicle_name',
						'vehicle.vehicle_number',
						'vehicle.is_active',
						'vehicle.year',
						'vehicle.color',
						'vehicle.default_type',
						'vehicle.status',
					)
					->leftJoin('documents', function ($join) {
						$join->on('driver_documents.document_id', '=', 'documents.id');
					})
					->leftJoin('vehicle', function ($join) {
						$join->on('driver_documents.vehicle_id', '=', 'vehicle.id');
					})
					->where('driver_documents.type', $request->type)->where('driver_documents.user_id', $user_id)
					->get();
				if (is_object($VehicleDocuments)) {
					return response()->json([
						'status_code'     => '1',
						'vehicle_documents' => $VehicleDocuments,
					]);
				}
			}
		} else if ($request->type == "Driver") {
			$DriverDocuments = DB::table('driver_documents')
				->select(
					'documents.id',
					'documents.document_name',
					'driver_documents.document as document_url',
					'driver_documents.document_type',
					'driver_documents.status',
					'driver_documents.expired_date',
				)
				->leftJoin('documents', function ($join) {
					$join->on('driver_documents.document_id', '=', 'documents.id');
				})
				->where('driver_documents.type', $request->type)->where('driver_documents.user_id', $user_id)
				->get();
			if (is_object($DriverDocuments)) {
				CustomLog::info("get_driver_document Api Stp:2 :");
				return response()->json([
					'status_code'     => '1',
					'driver_documents' => $DriverDocuments,
				]);
			}
		}
	}
}
