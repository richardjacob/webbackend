<?php

namespace App\Services;

use Cache;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CustomLog;

class GoogleAPIService
{
	/**
	 * Constructor
	 * 
	 */
	public function __construct()
	{
		$this->base_url = "https://maps.googleapis.com/maps/api";
		$this->map_key = MAP_KEY;
		$this->map_server_key = MAP_SERVER_KEY;
	}

	/**
	 * Get Static Map
	 *
	 * @param Float $lat1
	 * @param Float $long1
	 * @param Float $lat2
	 * @param Float $long2
	 * @param String $trip_path
	 * @return array $distance_data
	 */
	public function GetStaticMap($lat1, $long1, $lat2, $long2, $trip_path)
	{
		return $this->base_url . "/staticmap?size=640x480&zoom=14&path=color:0x000000ff%7Cweight:4%7Cenc:" . $trip_path . "&markers=size:mid|icon:" . url('images/pickup.png') . "|" . $lat1 . "," . $long1 . "&markers=size:mid|icon:" . url('images/drop.png') . "|" . $lat2 . "," . $long2 . "&sensor=false&key=" . $this->map_key;
	}

	/**
	 * Get Driving Distance
	 *
	 * @param Float $lat1
	 * @param Float $lat2
	 * @param Float $long1
	 * @param Float $long2
	 * @return array $distance_data
	 */
	public function GetDrivingDistance($lat1, $lat2, $long1, $long2)
	{
		//$url = $this->base_url."/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=pl-PL&key=" . MAP_SERVER_KEY;

		//https://maps.googleapis.com/maps/api/distancematrix/json?departure_time=now&destinations=23.79472999797148, 90.40442814265931&origins=23.770297528644445,90.40340723932255&key=AIzaSyDgBWsIv-JCQvAsSXchFSQ8r2SsQi_xNQA

		$url = $this->base_url . "/distancematrix/json?departure_time=now&destinations=" . $lat2 . "," . $long2 . "&origins=" . $lat1 . "," . $long1 . "&key=" . MAP_SERVER_KEY;


		$geocode = file_get_contents_curl($url);
		if (!$geocode) {
			return array('status' => "fail", 'msg' => trans('messages.api.something_went_wrong'), 'time' => '0', 'distance' => "0");
		}
		$response_a = json_decode($geocode);
		if ($response_a->status == "REQUEST_DENIED" || $response_a->status == "OVER_QUERY_LIMIT") {
			return array('status' => "fail", 'msg' => $response_a->error_message, 'time' => '0', 'distance' => "0");
		} elseif (isset($response_a->rows[0]->elements[0]->status) && $response_a->rows[0]->elements[0]->status == 'ZERO_RESULTS') {
			return array('status' => "fail", 'msg' => 'No Route Found', 'time' => '0', 'distance' => "0");
		} elseif ($response_a->status == "OK") {
			$dist_find = $response_a->rows[0]->elements[0]->distance->value;
			//$time_find = $response_a->rows[0]->elements[0]->duration->value;
			$time_find = $response_a->rows[0]->elements[0]->duration_in_traffic->value;

			$dist = @$dist_find != '' ? $dist_find : '';
			$time = @$time_find != '' ? $time_find : '';
			//	$time = @$time_find != '' ? $time_find : '';
			$return_data = array(
				'status' => 'success',
				'distance' => $dist,
				'time' => (int) $time,
			);

			return $return_data;
		} else {
			return array(
				'status' => 'success',
				'distance' => "1",
				'time' => "1",
			);
		}
	}


	public function GetDrivingDistance_v2($lat1, $lat2, $long1, $long2)
	{
		//$url = $this->base_url."/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=pl-PL&key=" . MAP_SERVER_KEY;

		//https://maps.googleapis.com/maps/api/distancematrix/json?departure_time=now&destinations=23.79472999797148, 90.40442814265931&origins=23.770297528644445,90.40340723932255&key=AIzaSyDgBWsIv-JCQvAsSXchFSQ8r2SsQi_xNQA

		$url = $this->base_url . "/distancematrix/json?departure_time=now&destinations=" . $lat2 . "," . $long2 . "&origins=" . $lat1 . "," . $long1 . "&key=" . MAP_SERVER_KEY;
		CustomLog::info(
			"map_API_Url_Log=" .
				$this->base_url .
				"/distancematrix/json?departure_time=now&destinations=" . $lat2 .
				"," . $long2 .
				"&origins=" .  $lat1 .
				"," . $long1 .
				"&key=" . MAP_SERVER_KEY
		);


		$geocode = file_get_contents_curl($url);
		if (!$geocode) {
			return array('status' => "fail", 'msg' => trans('messages.api.something_went_wrong'), 'time' => '0', 'distance' => "0");
		}
		$response_a = json_decode($geocode);
		if ($response_a->status == "REQUEST_DENIED" || $response_a->status == "OVER_QUERY_LIMIT") {
			return array('status' => "fail", 'msg' => $response_a->error_message, 'time' => '0', 'distance' => "0");
		} elseif (isset($response_a->rows[0]->elements[0]->status) && $response_a->rows[0]->elements[0]->status == 'ZERO_RESULTS') {
			return array('status' => "fail", 'msg' => 'No Route Found', 'time' => '0', 'distance' => "0");
		} elseif ($response_a->status == "OK") {
			$dist_find = $response_a->rows[0]->elements[0]->distance->value;
			//$time_find = $response_a->rows[0]->elements[0]->duration->value;
			$time_find = $response_a->rows[0]->elements[0]->duration_in_traffic->value;

			$dist = @$dist_find != '' ? $dist_find : 0;
			//$time = @$time_find != '' ? $time_find : '';
			$time = @$time_find != '' ? $time_find : 0;

			$minutes = 0;
			$ex_m = 0;
			$minutes = round(floor(round($time / 60)));
			if ($dist != 0) {
				$km = round(floor($dist / 1000) . '.' . floor($dist % 1000));
			} else {
				$km = 0;
			}

			date_default_timezone_set('Asia/Dhaka');
			$time_now = new DateTime();
			$time1 = new DateTime('03:00:00');
			$time2 = new DateTime('07:00:00');
			$time3 = new DateTime('11:00:00');
			$time4 = new DateTime('16:00:00');
			$time5 = new DateTime('21:00:00');
			$time6 = new DateTime('00:00:00');
			//$interval = $time2->diff($now);
			//echo ($interval->format("%a") * 24) + $interval->format("%h") . " hours" . $interval->format(" %i minutes ");
			// print_r($current_time->format('h:i a'));
			if ($time_now > $time1 && $time_now < $time2) {
				if ($km > 0 && $km <= 2) {
					$minutes = $minutes + 5;
					$ex_m = 5;
				} else if ($km > 2 && $km <= 4) {
					$minutes = $minutes + 7;
					$ex_m = 7;
				} else if ($km > 4 && $km <= 5) {
					$minutes = $minutes + 9;
					$ex_m = 9;
				} else if ($km > 5 && $km <= 7) {
					$minutes = $minutes + 11;
					$ex_m = 11;
				} else if ($km > 7 && $km <= 9) {
					$minutes = $minutes + 13;
					$ex_m = 13;
				} else if ($km > 9 && $km <= 11) {
					$minutes = $minutes + 15;
					$ex_m = 15;
				} else if ($km > 11 && $km <= 15) {
					$minutes = $minutes + 17;
					$ex_m = 17;
				} else if ($km > 15 && $km <= 18) {
					$minutes = $minutes + 19;
					$ex_m = 19;
				} else if ($km > 18) {
					$minutes = $minutes + 20;
					$ex_m = 20;
				}
			} else if ($time_now > $time2 && $time_now < $time3) {
				if ($km > 0 && $km <= 2) {
					$minutes = $minutes + 7;
					$ex_m = 7;
				} else if ($km > 2 && $km <= 4) {
					$minutes = $minutes + 10;
					$ex_m = 10;
				} else if ($km > 4 && $km <= 5) {
					$minutes = $minutes + 14;
					$ex_m = 14;
				} else if ($km > 5 && $km <= 7) {
					$minutes = $minutes + 17;
					$ex_m = 17;
				} else if ($km > 7 && $km <= 9) {
					$minutes = $minutes + 20;
					$ex_m = 20;
				} else if ($km > 9 && $km <= 11) {
					$minutes = $minutes + 23;
					$ex_m = 23;
				} else if ($km > 11 && $km <= 15) {
					$minutes = $minutes + 27;
					$ex_m = 27;
				} else if ($km > 15 && $km <= 18) {
					$minutes = $minutes + 32;
					$ex_m = 32;
				} else if ($km > 18) {
					$minutes = $minutes + 35;
					$ex_m = 35;
				}
			} else if ($time_now > $time3 && $time_now < $time4) {
				if ($km > 0 && $km <= 2) {
					$minutes = $minutes + 6;
					$ex_m = 6;
				} else if ($km > 2 && $km <= 4) {
					$minutes = $minutes + 8;
					$ex_m = 8;
				} else if ($km > 4 && $km <= 5) {
					$minutes = $minutes + 10;
					$ex_m = 10;
				} else if ($km > 5 && $km <= 7) {
					$minutes = $minutes + 12;
					$ex_m = 12;
				} else if ($km > 7 && $km <= 9) {
					$minutes = $minutes + 15;
					$ex_m = 15;
				} else if ($km > 9 && $km <= 11) {
					$minutes = $minutes + 18;
					$ex_m = 18;
				} else if ($km > 11 && $km <= 15) {
					$minutes = $minutes + 21;
					$ex_m = 21;
				} else if ($km > 15 && $km <= 18) {
					$minutes = $minutes + 24;
					$ex_m = 24;
				} else if ($km > 18) {
					$minutes = $minutes + 27;
					$ex_m = 27;
				}
			} else if ($time_now > $time4 && $time_now < $time5) {
				if ($km > 0 && $km <= 2) {
					$minutes = $minutes + 7;
					$ex_m = 7;
				} else if ($km > 2 && $km <= 4) {
					$minutes = $minutes + 10;
					$ex_m = 10;
				} else if ($km > 4 && $km <= 5) {
					$minutes = $minutes + 14;
					$ex_m = 14;
				} else if ($km > 5 && $km <= 7) {
					$minutes = $minutes + 17;
					$ex_m = 17;
				} else if ($km > 7 && $km <= 9) {
					$minutes = $minutes + 20;
					$ex_m = 20;
				} else if ($km > 9 && $km <= 11) {
					$minutes = $minutes + 23;
					$ex_m = 23;
				} else if ($km > 11 && $km <= 15) {
					$minutes = $minutes + 27;
					$ex_m = 27;
				} else if ($km > 15 && $km <= 18) {
					$minutes = $minutes + 32;
					$ex_m = 32;
				} else if ($km > 18) {
					$minutes = $minutes + 35;
					$ex_m = 35;
				}
			} else if ($time_now > $time5 && $time_now < $time6) {
				if ($km > 0 && $km <= 2) {
					$minutes = $minutes + 6;
					$ex_m = 6;
				} else if ($km > 2 && $km <= 4) {
					$minutes = $minutes + 8;
					$ex_m = 8;
				} else if ($km > 4 && $km <= 5) {
					$minutes = $minutes + 10;
					$ex_m = 10;
				} else if ($km > 5 && $km <= 7) {
					$minutes = $minutes + 12;
					$ex_m = 12;
				} else if ($km > 7 && $km <= 9) {
					$minutes = $minutes + 15;
					$ex_m = 15;
				} else if ($km > 9 && $km <= 11) {
					$minutes = $minutes + 18;
					$ex_m = 18;
				} else if ($km > 11 && $km <= 15) {
					$minutes = $minutes + 21;
					$ex_m = 21;
				} else if ($km > 15 && $km <= 18) {
					$minutes = $minutes + 24;
					$ex_m = 24;
				} else if ($km > 18) {
					$minutes = $minutes + 27;
					$ex_m = 27;
				}
			} else {
				if ($km > 0 && $km <= 2) {
					$minutes = $minutes + 5;
					$ex_m = 5;
				} else if ($km > 2 && $km <= 4) {
					$minutes = $minutes + 7;
					$ex_m = 7;
				} else if ($km > 4 && $km <= 5) {
					$minutes = $minutes + 9;
					$ex_m = 9;
				} else if ($km > 5 && $km <= 7) {
					$minutes = $minutes + 11;
					$ex_m = 11;
				} else if ($km > 7 && $km <= 9) {
					$minutes = $minutes + 13;
					$ex_m = 13;
				} else if ($km > 9 && $km <= 11) {
					$minutes = $minutes + 15;
					$ex_m = 15;
				} else if ($km > 11 && $km <= 15) {
					$minutes = $minutes + 17;
					$ex_m = 17;
				} else if ($km > 15 && $km <= 18) {
					$minutes = $minutes + 19;
					$ex_m = 19;
				} else if ($km > 18) {
					$minutes = $minutes + 20;
					$ex_m = 20;
				}
			}


			$time =	$minutes * 60;

			$return_data = array(
				'status' => 'success',
				'distance' => $dist,
				'time' => (int) $time,
				'ex_minutes' => (int) $ex_m,
			);

			return $return_data;
		} else {
			return array(
				'status' => 'success',
				'distance' => "1",
				'time' => "1",
			);
		}
	}




	public function GetDrivingDistance_v3($lat1, $lat2, $long1, $long2)
	{
		$url = $this->base_url . "/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=pl-PL&key=" . MAP_SERVER_KEY;

		//https://maps.googleapis.com/maps/api/distancematrix/json?departure_time=now&destinations=23.79472999797148, 90.40442814265931&origins=23.770297528644445,90.40340723932255&key=AIzaSyDgBWsIv-JCQvAsSXchFSQ8r2SsQi_xNQA

		//$url = $this->base_url . "/distancematrix/json?departure_time=now&destinations=" . $lat2 . "," . $long2 . "&origins=" . $lat1 . "," . $long1 . "&key=" . MAP_SERVER_KEY;


		$geocode = file_get_contents_curl($url);
		if (!$geocode) {
			return array('status' => "fail", 'msg' => trans('messages.api.something_went_wrong'), 'time' => '0', 'distance' => "0");
		}
		$response_a = json_decode($geocode);
		if ($response_a->status == "REQUEST_DENIED" || $response_a->status == "OVER_QUERY_LIMIT") {
			return array('status' => "fail", 'msg' => $response_a->error_message, 'time' => '0', 'distance' => "0");
		} elseif (isset($response_a->rows[0]->elements[0]->status) && $response_a->rows[0]->elements[0]->status == 'ZERO_RESULTS') {
			return array('status' => "fail", 'msg' => 'No Route Found', 'time' => '0', 'distance' => "0");
		} elseif ($response_a->status == "OK") {
			$dist_find = $response_a->rows[0]->elements[0]->distance->value;
			$time_find = $response_a->rows[0]->elements[0]->duration->value;
			//$time_find = $response_a->rows[0]->elements[0]->duration_in_traffic->value;

			$dist = @$dist_find != '' ? $dist_find : '';
			//$time = @$time_find != '' ? $time_find : '';
			$time = @$time_find != '' ? $time_find : '';
			$return_data = array(
				'status' => 'success',
				'distance' => $dist,
				'time' => (int) $time,
				'ex_minutes' => 0,
			);

			return $return_data;
		} else {
			return array(
				'status' => 'success',
				'distance' => "1",
				'time' => "1",
			);
		}
	}













	/**
	 * Get Polyline
	 *
	 * @param Float $lat1
	 * @param Float $lat2
	 * @param Float $long1
	 * @param Float $long2
	 * @return String $polyline
	 */
	public function GetPolyline($lat1, $lat2, $long1, $long2)
	{
		$cache_key = 'polyline_' . $lat1 . '_' . $lat2 . '_' . $long1 . '_' . $long2;
		$cacheExpireAt = Carbon::now()->addHours(CACHE_HOURS);

		if (Cache::has($cache_key)) {
			return Cache::get($cache_key);
		}

		$url = $this->base_url . "/directions/json?origin=" . $lat1 . "," . $long1 . "&destination=" . $lat2 . "," . $long2 . "&mode=driving&units=metric&sensor=true&&language=pl-PL&key=" . MAP_SERVER_KEY;

		$geocode = @file_get_contents($url);
		$response_a = json_decode($geocode);

		$polyline_find = $response_a->routes[0]->overview_polyline->points;

		$polyline = @$polyline_find != '' ? $polyline_find : '';

		Cache::put($cache_key, $polyline, $cacheExpireAt);

		return $polyline;
	}

	/**
	 * Get Country
	 *
	 * @param Float $lat1
	 * @param Float $long1
	 * @return String $country
	 */
	public function GetCountry($lat1, $long1)
	{
		$cache_key = 'location_' . numberFormat($lat1, 3) . '_' . numberFormat($long1, 3);
		$cacheExpireAt = Carbon::now()->addHours(CACHE_HOURS);

		if (Cache::has($cache_key)) {
			return Cache::get($cache_key);
		}

		$pickup_geocode = file_get_contents($this->base_url . '/geocode/json?latlng=' . $lat1 . ',' . $long1 . '&key=' . MAP_SERVER_KEY);

		$pickup_check = json_decode($pickup_geocode);

		$country = '';

		if (@$pickup_check->results) {
			foreach ($pickup_check->results as $result) {
				foreach ($result->address_components as $addressPart) {

					if ((in_array('country', $addressPart->types)) && (in_array('political', $addressPart->types))) {
						$country = $addressPart->long_name;
					}
				}
			}
		}

		Cache::put($cache_key, $country, $cacheExpireAt);

		return $country;
	}

	/**
	 * Get Location data with Geocode
	 *
	 * @param Float $lat1
	 * @param Float $long1
	 * @return String $address
	 */
	public function GetLocation($lat1, $long1)
	{
		$cache_key = 'location_' . $lat1 . '_' . $long1;
		$cacheExpireAt = Carbon::now()->addHours(CACHE_HOURS);

		if (Cache::has($cache_key)) {
			return Cache::get($cache_key);
		}

		$drop_geocode = file_get_contents_curl($this->base_url . '/geocode/json?latlng=' . $lat1 . ',' . $long1 . '&key=' . MAP_SERVER_KEY);

		$drop_check = json_decode($drop_geocode);
		$location = '';
		if (@$drop_check->results) {
			$location = @$drop_check->results[0]->formatted_address;
		}

		Cache::put($cache_key, $location, $cacheExpireAt);
		return $location;
	}

	/**
	 * Get Timezone
	 *
	 * @param Float $lat1
	 * @param Float $long1
	 * @return String $timezone
	 */
	public function getTimeZone($lat1, $long1)
	{
		$cache_key = 'timezone_' . numberFormat($lat1, 5) . '_' . numberFormat($long1, 5);
		$cacheExpireAt = Carbon::now()->addHours(CACHE_HOURS);

		if (Cache::has($cache_key)) {
			return Cache::get($cache_key);
		}

		$timestamp = strtotime(date('Y-m-d H:i:s'));

		$geo_timezone = file_get_contents_curl($this->base_url . '/timezone/json?location=' . @$lat1 . ',' . @$long1 . '&timestamp=' . $timestamp . '&key=' . MAP_SERVER_KEY);

		$timezone = json_decode($geo_timezone);

		if ($timezone->status == 'OK') {
			Cache::put($cache_key, $timezone->timeZoneId, $cacheExpireAt);
			return $timezone->timeZoneId;
		}
		return 'Asia/Kolkata';
	}
}
