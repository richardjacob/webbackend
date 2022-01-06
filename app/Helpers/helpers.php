<?php

/**
 * Helpers
 *
 * @package     Gofer
 * @subpackage  Helpers
 * @category    Helpers
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

use App\Models\Currency;
use App\Models\Rating;
use App\Models\Wallet;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\CompactSerializer;
use App\Models\Vehicle;
use App\Models\ManageFare;
use App\Models\Location;
use App\Models\CarType;
use App\Models\Documents;
use App\Models\DriverDocuments;
use App\Models\CompanyDocuments;
use App\Models\DriverLocation;
use App\Models\HubEmployee;
use App\Models\Hub;
use App\Models\User;
use App\Models\MonitorCamera;
use App\Models\ProfilePicture;
use App\Models\DriverRemarks;
use App\Models\Optional;
use App\Models\Admin;
use App\Models\VehicleCity;
use App\Models\VehicleRegistrationLetter;
use App\Models\Otp;
use App\Models\VehicleModel;
use App\Models\VehicleMake;


/**
 * Convert String to htmlable instance
 *
 * @param  string $type      Type of the image
 * @return instance of \Illuminate\Contracts\Support\Htmlable
 */
if (!function_exists('html_string')) {

	function html_string($str)
	{
		return new HtmlString($str);
	}
}

/**
 * File Get Content by using CURL
 *
 * @param  string $url  Url
 * @return string $data Response of URL
 */
if (!function_exists('file_get_contents_curl')) {

	function file_get_contents_curl($url)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}
}

/**
 * Do CURL With POST
 *
 * @param  String $url  Url
 * @param  Array $params  Url Parameters
 * @return string $data Response of URL
 */
if (!function_exists('curlPost')) {

	function curlPost($url, $params)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Accept: application/json',
			'User-Agent: curl',
		]);
		$output = curl_exec($ch);

		curl_close($ch);
		return json_decode($output, true);
	}
}

/**
 * Convert Given Array To Object
 * 
 * @return Object
 */
if (!function_exists('arrayToObject')) {
	function arrayToObject($arr)
	{
		$arr = Arr::wrap($arr);
		return json_decode(json_encode($arr));
	}
}

/**
 * Convert Given Float To Nearest Half Integer
 *
 * @return Int
 */
if (!function_exists('roundHalfInteger')) {
	function roundHalfInteger($value)
	{
		return floor($value * 2) / 2;
	}
}

/**
 * Format Invoice Item
 * 
 * @param [Array] $[item]
 * @return [Array] [formated invoice item]
 */
if (!function_exists('formatInvoiceItem')) {
	function formatInvoiceItem($item)
	{
		return array(
			'key' 		=> $item['key'],
			'value' 	=> strval($item['value']),
			'bar'		=> $item['bar'] ?? 0,
			'colour'	=> $item['colour'] ?? '',
			'comment' 	=> $item['comment'] ?? '',
		);
	}
}

/**
 * Format Driver Statement Item
 * 
 * @param [Array] $[item]
 * @param [String] $[type]
 * @return [Array] [formated invoice item]
 */
if (!function_exists('formatStatementItem')) {
	function formatStatementItem($item, $type = '')
	{
		return array(
			'key' 		=> $item['key'],
			'value' 	=> strval($item['value']),
			'bar'		=> $item['bar'] ?? false,
			'colour'	=> $item['colour'] ?? '',
			'tooltip' 	=> $item['tooltip'] ?? '',
		);
	}
}

/**
 * Currency Convert
 *
 * @param int $from   Currency Code From
 * @param int $to     Currency Code To
 * @param int $price  Price Amount
 * @return int Converted amount
 */
if (!function_exists('currencyConvert')) {
	function currencyConvert($from, $to, $price = 0)
	{
		$price = floatval($price);
		if ($from == $to) {
			return number_format($price, 2, '.', '');
		}

		if ($price == 0) {
			return number_format(0, 2, '.', '');
		}

		$rate = Currency::whereCode($from)->first()->rate;
		$session_rate = Currency::whereCode($to)->first()->rate;

		$usd_amount = $price / $rate;
		return number_format($usd_amount * $session_rate, 2, '.', '');
	}
}

/**
 * Check if a string is a valid timezone
 *
 * @param string $timezone
 * @return bool
 */
if (!function_exists('isValidTimezone')) {
	function isValidTimezone($timezone)
	{
		return in_array($timezone, timezone_identifiers_list());
	}
}

/**
 * Get Given Rider Rating
 *
 * @param String $driver_id
 * @return String $rider_rating
 */
if (!function_exists('getRiderRating')) {
	function getRiderRating($driver_id)
	{
		$total_rating = \DB::table('rating')->select(DB::raw('sum(rider_rating) as rating'))
			->where('driver_id', $driver_id)->where('rider_rating', '>', 0)->first()->rating;

		$total_rating_count = Rating::where('driver_id', $driver_id)->where('rider_rating', '>', 0)->count();

		$rider_rating = '0.0';
		if ($total_rating_count != 0) {
			$rider_rating = round(($total_rating / $total_rating_count), 2);
		}
		return strval($rider_rating);
	}
}

/**
 * Get Given Driver Rating
 *
 * @param String $rider_id
 * @return String $driver_rating
 */
if (!function_exists('getDriverRating')) {
	function getDriverRating($rider_id)
	{
		$total_rating = Cache()->remember('rider_total_rating-' . $rider_id, Config('cache.one_hour'), function () use ($rider_id) {
			return \DB::table('rating')->select(DB::raw('sum(driver_rating) as rating'))->where('user_id', $rider_id)->where('driver_rating', '>', 0)->first()->rating;
		});

		//$total_rating = \DB::table('rating')->select(DB::raw('sum(driver_rating) as rating'))->where('user_id', $rider_id)->where('driver_rating', '>', 0)->first()->rating;

		$total_rating_count = Cache()->remember('rider_rating-' . $rider_id, Config('cache.one_hour'), function () use ($rider_id) {
			return Rating::where('user_id', $rider_id)->where('driver_rating', '>', 0)->count();
		});

		//$total_rating_count = Rating::where('user_id', $rider_id)->where('driver_rating', '>', 0)->count();

		$driver_rating = '0.0';
		if ($total_rating_count != 0) {
			$driver_rating = round(($total_rating / $total_rating_count), 2);
		}
		return strval($driver_rating);
	}
}

/**
 * Get User Wallet Amount
 *
 * @param String $user_id
 * @return String $wallet_amount
 */
if (!function_exists('getUserWalletAmount')) {
	function getUserWalletAmount($user_id)
	{
		$wallet = Wallet::whereUserId($user_id)->first();
		$wallet_amount = $wallet->original_amount ?? "0";

		return strval($wallet_amount);
	}
}

/**
 * Checks if a value exists in an array in a case-insensitive manner
 *
 * @param string $key The searched value
 * 
 * @return if key found, return particular value of key.
 */
if (!function_exists('site_settings')) {

	function site_settings($key)
	{
		$site_settings = resolve('site_settings');
		$site_setting = $site_settings->where('name', $key)->first();

		return $site_setting->value ?? '';
	}
}

/**
 * Checks if a value exists in an array in a case-insensitive manner
 *
 * @param string $key The searched value
 * 
 * @return if key found, return particular value of key.
 */
if (!function_exists('email_settings')) {

	function email_settings($key)
	{
		$email_settings = resolve('email_settings');
		$email_setting = $email_settings->where('name', $key)->first();

		return $email_setting->value ?? '';
	}
}

/**
 * Checks if a value exists in an array in a case-insensitive manner
 *
 * @param string $key The searched value
 * 
 * @return if key found, return particular value of key.
 */
if (!function_exists('payment_gateway')) {

	function payment_gateway($key, $site)
	{
		$payment_gateway = resolve('payment_gateway');
		$gateway = $payment_gateway->where('name', $key)->where('site', $site)->first();

		return $gateway->value ?? '';
	}
}

/**
 * Checks if a value exists in an array in a case-insensitive manner
 *
 * @param string $key The searched value
 * 
 * @return if key found, return particular value of key.
 */
if (!function_exists('api_credentials')) {

	function api_credentials($key, $site)
	{
		$api_credentials = resolve('api_credentials');
		$credentials = $api_credentials->where('name', $key)->where('site', $site)->first();

		return $credentials->value ?? '';
	}
}

/**
 * Checks if a value exists in an array in a case-insensitive manner
 *
 * @param string $key The searched value
 * 
 * @return if key found, return particular value of key.
 */
if (!function_exists('fees')) {

	function fees($key)
	{
		$fees = resolve('fees');
		$fee = $fees->where('name', $key)->first();

		return $fee->value ?? '';
	}
}

/**
 * Set Flash Message function
 *
 * @param  string $class     Type of the class ['danger','success','warning']
 * @param  string $message   message to be displayed
 */
if (!function_exists('flashMessage')) {

	function flashMessage($class, $message)
	{
		Session::flash('alert-class', 'alert-' . $class);
		Session::flash('message', $message);
	}
}

/**
 * Get Admin default Currency Symbole
 *
 * @return currency symbol
 */
if (!function_exists('currency_symbol')) {
	function currency_symbol()
	{
		$default_currency = view()->shared('default_currency');
		if (LOGIN_USER_TYPE == 'company' && session('currency') != null) {
			$default_currency = Currency::whereCode(session('currency'))->first();
		}
		return html_entity_decode($default_currency->symbol);
	}
}

/**
 * Get a Facebook Login URL
 *
 * @return URL from Facebook API
 */
if (!function_exists('getAppleLoginUrl')) {
	function getAppleLoginUrl()
	{
		$params = [
			'response_type' 	=> 'code',
			'response_mode' 	=> 'form_post',
			'client_id' 		=> api_credentials('service_id', 'Apple'),
			'redirect_uri' 		=> url('apple_callback'),
			'state' 			=> bin2hex(random_bytes(5)),
			'scope' 			=> 'name email',
		];
		$authorize_url = 'https://appleid.apple.com/auth/authorize?' . http_build_query($params);

		return $authorize_url;
	}
}

/**
 * Generate Apple Client Secret
 *
 * @return String $token
 */
if (!function_exists('getAppleClientSecret')) {
	function getAppleClientSecret()
	{
		$key_file = base_path() . api_credentials("key_file", "Apple");

		$algorithmManager = new AlgorithmManager([new ES256()]);
		$jwsBuilder = new JWSBuilder($algorithmManager);
		$jws = $jwsBuilder
			->create()
			->withPayload(json_encode([
				'iat' => time(),
				'exp' => time() + 86400 * 180,
				'iss' => api_credentials('team_id', 'Apple'),
				'aud' => 'https://appleid.apple.com',
				'sub' => api_credentials('service_id', 'Apple'),
			]))
			->addSignature(JWKFactory::createFromKeyFile($key_file), [
				'alg' => 'ES256',
				'kid' => api_credentials('key_id', 'Apple')
			])
			->build();

		$serializer = new CompactSerializer();
		$token = $serializer->serialize($jws, 0);

		return $token;
	}
}

/**
 * Get Currency Code From IP address
 *
 * @param  $ip_address [current IP]
 * @return String $currency_code
 */
if (!function_exists('get_currency_from_ip')) {
	function get_currency_from_ip($ip_address = '')
	{
		$ip_address = $ip_address ?: request()->getClientIp();
		$default_currency = Currency::active()->defaultCurrency()->first();
		$currency_code    = $default_currency->code;
		if (session('currency_code')) {
			$currency_code = session('currency_code');
		} else if ($ip_address != '') {
			try {
				$result = unserialize(file_get_contents_curl('http://www.geoplugin.net/php.gp?ip=' . $ip_address));
			} catch (\Exception $e) {
				// 
			}
			// Default Currency code for footer
			if (isset($result['geoplugin_currencyCode'])) {
				$check_currency = Currency::whereCode($result['geoplugin_currencyCode'])->count();
				if ($check_currency) {
					$currency_code =  $result['geoplugin_currencyCode'];
				}
			}
			session(['currency_code' => $currency_code]);
		}
		return $currency_code;
	}
}

/**
 * Get Currency Code From IP address
 *
 * @param  $date_obj [Carbon date object]
 * @param  $format [Return Date Format]
 * @return String $currency_code
 */
if (!function_exists('getWeekStartEnd')) {
	function getWeekStartEnd($date_obj, $format = 'd M')
	{
		$result['start'] = $date_obj->startOfWeek()->format($format);
		$result['end'] = $date_obj->endOfWeek()->format($format);

		return $result;
	}
}

/**
 * Check Cash trip or not
 *
 * @return Boolean true or false
 */
if (!function_exists('checkIsCashTrip')) {
	function checkIsCashTrip($payment_mode)
	{
		return in_array($payment_mode, ['Cash & Wallet', 'Cash']);
	}
}

/**
 * Check Current Environment
 *
 * @return Boolean true or false
 */
if (!function_exists('isLiveEnv')) {
	function isLiveEnv($environments = [])
	{
		if (count($environments) > 0) {
			array_push($environments, 'live');
			return in_array(env('APP_ENV'), $environments);
		}
		return env('APP_ENV') == 'live';
	}
}

/**
 * Check Can display credentials or not
 *
 * @return Boolean true or false
 */
if (!function_exists('canDisplayCredentials')) {
	function canDisplayCredentials()
	{
		return env('SHOW_CREDENTIALS', 'false') == 'true';
	}
}

/**
 * Convert underscore_strings to camelCase (medial capitals).
 *
 * @param {string} $str
 *
 * @return {string}
 */
if (!function_exists('snakeToCamel')) {

	function snakeToCamel($str, $removeSpace = false)
	{
		// Remove underscores, capitalize words, squash.
		$camelCaseStr =  ucwords(str_replace('_', ' ', $str));
		if ($removeSpace) {
			$camelCaseStr =  str_replace(' ', '', $camelCaseStr);
		}
		return $camelCaseStr;
	}
}

/**
 * get protected String or normal based on env
 *
 * @param {string} $str
 *
 * @return {string}
 */
if (!function_exists('protectedString')) {

	function protectedString($str)
	{
		if (isLiveEnv()) {
			return substr($str, 0, 1) . '****' . substr($str,  -4);
		}
		return $str;
	}
}

if (!function_exists('updateEnvConfig')) {
	function updateEnvConfig($envKey, $envValue)
	{
		$envFile = app()->environmentFilePath();
		$str = file_get_contents($envFile);

		try {
			$str .= "\n";
			$keyPosition = strpos($str, "{$envKey}=");
			$endOfLinePosition = strpos($str, "\n", $keyPosition);
			$oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

			if (!$keyPosition || !$endOfLinePosition || !$oldLine) {
				$str .= "{$envKey}={$envValue}\n";
			} else {

				if ($envKey == 'IP_ADDRESS') {
					if ($envValue == 'delete') {
						$envValue = '';
					} else {

						$oldValue = substr(strrchr($oldLine, '='), 1);

						if (!$oldValue) {
							$oldValues = array();
						} else {
							$oldValues = explode(',', $oldValue);
						}

						$envValue_delete = explode('_', $envValue);

						if (count($envValue_delete) > 1 && $envValue_delete[1] == 'delete') {
							$oldKey = array_search($envValue_delete[0], $oldValues);
							if ($oldKey !== false) {
								unset($oldValues[$oldKey]);
							}
							if (count($oldValues)) {
								$envValue = implode(',', $oldValues);
							} else {
								$envValue = '';
							}
						} else {

							$envValue = filter_var($envValue, FILTER_VALIDATE_IP);

							if ($envValue && !in_array($envValue, $oldValues)) {
								$oldValues[count($oldValues)] = $envValue;
								if (count($oldValues) > 1) {
									$envValue = implode(',', $oldValues);
								}
							} else {
								$envValue = $oldValue;
							}
						}
					}
				}
				logger($envKey . ' - ' . $envValue);
				$str = str_replace($oldLine, "{$envKey}={$envValue}", $str);
			}
			$str = substr($str, 0, -1);
			file_put_contents($envFile, $str);
		} catch (\Exception $e) {
			\Log::error($e->getMessage());
		}
	}
}

/**
 * Check Given Request is from API or not
 *
 * @return Boolean
 */
if (!function_exists('isApiRequest')) {

	function isApiRequest()
	{
		return request()->segment(1) == 'api';
	}
}

/**
 * Check Given Request is from API or not
 *
 * @return Boolean
 */
if (!function_exists('camelCaseToString')) {

	function camelCaseToString($string)
	{
		$pieces = preg_split('/(?=[A-Z])/', $string);
		$word = implode(" ", $pieces);
		return ucwords($word);
	}
}

/**
 * Check Given Request is from API or not
 *
 * @return Boolean
 */
if (!function_exists('getPayoutMethods')) {

	function getPayoutMethods($company_id = 1)
	{
		if ($company_id != 1) {
			$payout_methods = ['bank_transfer'];
		} else {
			$payout_methods = payment_gateway('payout_methods', 'Common');
			$payout_methods = explode(',', $payout_methods);
		}
		return $payout_methods;
	}
}

/**
 * Check Given Request is from API or not
 *
 * @return Boolean
 */
if (!function_exists('isPayoutEnabled')) {

	function isPayoutEnabled($method)
	{
		$payout_methods = getPayoutMethods();
		return in_array($method, $payout_methods);
	}
}

if (!function_exists('isAdmin')) {
	function isAdmin()
	{
		return request()->segment(1) == 'admin';
	}
}

/**
 * numberFormat Function
 *
 * @param {Float} $value
 *
 * @return {string}
 */
if (!function_exists('numberFormat')) {

	function numberFormat($value, $precision = 2)
	{
		return number_format($value, $precision, '.', '');
	}
}

/**
 * Calculates the distance between two points, given their 
 * latitude and longitude, and returns an array of values 
 * of the most common distance units
 *
 * @param  {coord} $lat1 Latitude of the first point
 * @param  {coord} $lon1 Longitude of the first point
 * @param  {coord} $lat2 Latitude of the second point
 * @param  {coord} $lon2 Longitude of the second point
 * @return {string} value in given distance unit
 */
if (!function_exists('getDistanceBetweenPoints')) {
	function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2, $unit = "K")
	{
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);

		if ($unit == "M") {
			return numberFormat($miles);
		}
		if ($unit == "K") {
			return numberFormat($miles * 1.609344);
		}
		if ($unit == "N") {
			return numberFormat($miles * 0.8684);
		}
	}
}

if (!function_exists('LogDistanceMatrix')) {
	function LogDistanceMatrix($functionName, $notes = '')
	{
		$destinationPath = storage_path('logs/distance.json');
		try {
			$jsonString = file_get_contents($destinationPath);
		} catch (\Exception $e) {
			$jsonString = "";
		}

		$prev_log = json_decode($jsonString, true);

		$dateString = \Carbon\carbon::now()->setTimezone('Asia/Kolkata');

		$prev_log[] = array(
			'url' 		=> request()->fullUrl(),
			'function' 	=> $functionName,
			'date'		=> $dateString->format('Y-m-d'),
			'time' 		=> $dateString->format('H:i:s'),
			'notes'		=> $notes,
		);
		$log_data = json_encode($prev_log, JSON_PRETTY_PRINT);
		\File::put($destinationPath, $log_data);
	}

	if (!function_exists('getVehicleLocation')) {
		function getVehicleLocation($vehicle)
		{
			$location = ManageFare::where('vehicle_id', $vehicle)->get();
			$result = [];
			foreach ($location as $key => $value) {
				$locationValue = Location::where('id', $value->location_id)->select('name')->first();
				// array_push($result[$key], $locationValue->name);
				$result[$key]['location_name'] = $locationValue->name;
			}
			$location_Value = array_column($result, 'location_name');
			return implode(', ', $location_Value);
		}
	}

	if (!function_exists('getPoolValue')) {
		function getPoolValue($poolValue)
		{
			if ($poolValue == 'No') {
				return False;
			} else if ($poolValue == 'Yes') {
				return True;
			}
		}
	}

	if (!function_exists('getVehicleDetails')) {
		function getVehicleDetails($vehicle)
		{
			if ($poolValue == 'No') {
				return False;
			} else if ($poolValue == 'Yes') {
				return True;
			}
		}
	}

	if (!function_exists('getVehicleType')) {
		function getVehicleType($vehicle)
		{
			$cartype = CarType::select('car_name as type', 'id', 'is_pool')->whereIn('id', $vehicle)->active()->get();
			foreach ($cartype as $key => $value) {
				$vehicle_type[$key]['id'] = $value->id;
				$vehicle_type[$key]['type'] = $value->type;
				$vehicle_type[$key]['isPooled'] = getPoolValue($value->is_pool);
				$vehicle_type[$key]['location'] = getVehicleLocation($value->id);
			}
			return $vehicle_type;
		}
	}

	if (!function_exists('CheckDocument')) {
		function CheckDocument($document_for, $country_code)
		{
			$data = Documents::Active()->DocumentCheck($document_for, $country_code)->get();
			if ($data->count() == 0) {
				$data = Documents::Active()->DocumentCheck($document_for, 'all')->get();
			}
			foreach ($data as $key => $value) {
				$data[$key]['status'] = 0;
				if ($value->expire_on_date == 'Yes') {
					$data[$key]['expiry_required'] = 1;
				} else {
					$data[$key]['expiry_required'] = 0;
				}
			}
			return $data;
		}
	}

	if (!function_exists('UserStatusUpdate')) {
		function UserStatusUpdate($user)
		{

			$vehicle_documents = $user->driver_documents('Vehicle')->count();
			$driver_documents = $user->driver_documents('Driver')->count();

			if (!$user->vehicles->count()) {
				$user_status = 'Car_details';
			} elseif (!$vehicle_documents) {
				$user_status = 'Car_details';
			} elseif (!$driver_documents) {
				$user_status = 'Document_details';
			} else {
				$user_status = isLiveEnv() ? 'Active' : 'Pending';
			}
			return $user_status;
		}
	}

	if (!function_exists('UserDocuments')) {
		function UserDocuments($type, $user, $vehicle_id = 0)
		{

			if ($type == 'Company') {
				$docs = CompanyDocuments::whereHas('documents', function ($q) {
					$q->active();
				})->where('company_id', $user->id)->get();
			} else {
				$docs = DriverDocuments::whereHas('documents', function ($q) {
					$q->active();
				})->where('type', $type)->where('user_id', $user->id)->where('vehicle_id', $vehicle_id)->get();
			}

			$docArr = array();
			if ($docs->count() > 0) {
				foreach ($docs as $key => $value) {
					$docArr[$key]['id'] = $value->document_id;
					$docArr[$key]['name'] = $value->document_name;
					$docArr[$key]['document'] = $value->document;
					$docArr[$key]['document_type'] = $value->document_type;
					$docArr[$key]['expired_date'] = $value->expired_date;


					$document = Documents::find($value->document_id);
					if ($document->expire_on_date == 'Yes') {
						$docArr[$key]['expiry_required'] = 1;
					} else {
						$docArr[$key]['expiry_required'] = 0;
					}

					$docArr[$key]['country_code'] = $document->country_code;
					$docArr[$key]['status'] = $value->status;
					if (!isApiRequest()) {
						$docArr[$key]['document_name'] = $value->document_name;
						$docArr[$key]['doc_name'] = $value->doc_name;
					}
				}
				$upload_document_count = $docs->count();
				$documents = Documents::Active()->DocumentCheck($type, $user->country_code)->get();
				if ($documents->count() == 0) {
					$documents = Documents::Active()->DocumentCheck($type, 'all')->get();
				}
				if ($upload_document_count != $documents->count()) {
					$remain_doc = array_diff($documents->pluck('id')->toArray(), $docs->pluck('document_id')->toArray());
					$RemainDoc = Documents::whereIn('id', $remain_doc)->get();
					$RemainDocArr = array();
					foreach ($RemainDoc as $key => $value) {
						$RemainDocArr[$key]['id'] = $value->id;
						$RemainDocArr[$key]['name'] = $value->document_name;
						$RemainDocArr[$key]['document'] = '';
						$RemainDocArr[$key]['document_type'] = '';
						$RemainDocArr[$key]['expired_date'] = '';
						if ($value->expire_on_date == 'Yes') {
							$RemainDocArr[$key]['expiry_required'] = 1;
						} else {
							$RemainDocArr[$key]['expiry_required'] = 0;
						}
						$RemainDocArr[$key]['country_code'] = $value->country_code;
						$RemainDocArr[$key]['status'] = 0;
						if (!isApiRequest()) {
							$RemainDocArr[$key]['document_name'] = $value->document_name;
							$RemainDocArr[$key]['doc_name'] = $value->doc_name;
						}
					}
					$docArr = array_merge($docArr, $RemainDocArr);

					if (isApiRequest()) {
						$inactive_doc = array_diff($docs->pluck('document_id')->toArray(), $documents->pluck('id')->toArray());
						if (count($inactive_doc)) {
							foreach ($inactive_doc as $inactive_id) {
								$inactive_key = array_search($inactive_id, array_column($docArr, 'id'));
								unset($docArr[$inactive_key]);
							}
							$docArr = array_values($docArr);
						}
					}
				}
			} else {
				$data = Documents::Active()->DocumentCheck($type, $user->country_code)->get();
				if ($data->count() == 0) {
					$data = Documents::Active()->DocumentCheck($type, 'all')->get();
				}
				foreach ($data as $key => $value) {
					$docArr[$key]['id'] = $value->id;
					$docArr[$key]['name'] = $value->document_name;
					$docArr[$key]['document'] = '';
					$docArr[$key]['expired_date'] = '';
					if ($value->expire_on_date == 'Yes') {
						$docArr[$key]['expiry_required'] = 1;
					} else {
						$docArr[$key]['expiry_required'] = 0;
					}
					$docArr[$key]['country_code'] = $value->country_code;
					$docArr[$key]['status'] = 0;
					if (!isApiRequest()) {
						$docArr[$key]['document_name'] = $value->document_name;
						$docArr[$key]['doc_name'] = $value->doc_name;
					}
				}
			}
			return isApiRequest() ? $docArr : json_encode($docArr);
		}
	}
}

if (!function_exists('checkDefault')) {
	/**
	 * Check pre default vehicle is in ride or not
	 *
	 * @param  {driver_id} $driver_id id of the driver
	 * @param  {vehicle_id} $vehicle_id id of the vehicle
	 * @param  {default} $default set default(1) or non-default(0)
	 * @return {numeric} value in given numeric
	 */
	function checkDefault($driver_id, $vehicle_id, $default)
	{

		$vehicle = Vehicle::find($vehicle_id);
		$driver_location = DriverLocation::where('user_id', $driver_id)->first();

		if ($default == '1') {
			// update default to the same vehicle not restricted
			if ($vehicle && $vehicle->default_type == '1') {
				return 0;
			} else {
				// update default to the another vehicle, check driver status in trip or not if trip means avoid to update
				if ($driver_location && ($driver_location->status == 'Pool Trip' || $driver_location->status == 'Trip')) {
					return 1;
				} else {
					return 0;
				}
			}
		} else {
			// update non-default to the default vehicle, check driver status in trip or not if trip means avoid to update
			if ($driver_location && ($driver_location->status == 'Pool Trip' || $driver_location->status == 'Trip'))
				if ($vehicle && $vehicle->default_type == '1')
					return 2;
		}
		return 0;
	}
}

/**
 * Check Current Environment
 *
 * @return Boolean true or false
 */
if (!function_exists('CheckGetInTuchpopup')) {
	function CheckGetInTuchpopup($environments = [])
	{
		return strtolower(env('GET_IN_TUCH_POPUP')) == 'yes';
	}
}

function get_domain()
{
	$parse = parse_url(url()->current());
	$host = $parse['host'];

	$myhost = strtolower(trim($host));
	$count = substr_count($myhost, '.');
	if ($count === 2) {
		if (strlen(explode('.', $myhost)[1]) > 3) $myhost = explode('.', $myhost, 2)[1];
	} else if ($count > 2) {
		$myhost = get_domain(explode('.', $myhost, 2)[1]);
	}
	return $myhost;
}

function moveElementInArray($array, $toMove, $targetIndex) {
    if (is_int($toMove)) {
        $tmp = array_splice($array, $toMove, 1);
        array_splice($array, $targetIndex, 0, $tmp);
        $output = $array;
    }
    elseif (is_string($toMove)) {
        $indexToMove = array_search($toMove, array_keys($array));
        $itemToMove = $array[$toMove];
        array_splice($array, $indexToMove, 1);
        $i = 0;
        $output = Array();
        foreach($array as $key => $item) {
            if ($i == $targetIndex) {
                $output[$toMove] = $itemToMove;
            }
            $output[$key] = $item;
            $i++;
        }
    }
    return $output;
}

function printExtraData(){
    $final_array = array();
    $output_array = array();

    $extraGetData = explode('%3F', http_build_query($_GET));// %3F draw
    // if($extraGetData !=''){
    //     $array = explode('&', $extraGetData);
    //     foreach($array as $getData){
    //         list($key, $val) = explode('=', $getData);
    //         $final_array[$key] = $val;
    //     }
    // }

    if(is_array($extraGetData) and count($extraGetData) > 1){
        if($extraGetData[0] !=''){
            $array = explode('&', $extraGetData[0]);
            foreach($array as $getData){
                list($key, $val) = explode('=', $getData);
                $final_array[$key] = $val;
            }
        }
    }


    if(count($final_array) > 0){
	    if (array_key_exists('start_date', $final_array)){
	    	$final_array = moveElementInArray($final_array, 'start_date', 0);
	    }
	    if (array_key_exists('end_date', $final_array)){
	    	if (array_key_exists('start_date', $final_array)){
	    		$final_array = moveElementInArray($final_array, 'end_date', 1);
	    	}else{
	    		$final_array = moveElementInArray($final_array, 'end_date', 0);
	    	}
	    	
	    } 	
//print_r($final_array);
	    foreach($final_array as $key => $val){
	    	if($val !=''){
		        switch ($key) {
		            case 'employee_id':                
		                $output_array['Employee'] = HubEmployee::find($val)->employee_name;
		                break;

		            case 'hub_id':                
		                $output_array['Hub'] = Hub::find($val)->name;
		                break;

		            case 'code': // hub_employees refaral_id
		            	$employee = HubEmployee::where('refaral_id', $val)->first();
		                $output_array['Refaral Code'] = $val;
		                $output_array['Employee'] = @$employee->employee_name;
		                break;

		            case 'user_id':
		            	$user = User::find($val);              
		                $output_array['User'] = $user->first_name.' '.$user->last_name;
		                break;

		            default:
		            	if(
		            		$key == 'start_date' OR 
		            		$key == 'end_date' 
		            	)
		                $output_array[ucwords(str_replace('_', ' ', $key))] = $val;
		                break;
		        }
		    }
	    }
	}

    return $output_array;
}

function total_driver_doc($vehicle_id){
	$total_required_doc = DB::table('documents')->where('type', 'Vehicle')
											    ->where('status', 'Active') 
											    ->count();

	$totalDriverDoc = DB::table('driver_documents')
							->select('id')
							->where('vehicle_id', $vehicle_id)
							->groupBy('document_id')
							->get();

	$total_driver_doc = count($totalDriverDoc);

	if($total_required_doc <= $total_driver_doc) return "<i class='glyphicon glyphicon-ok text-success' title='All documents submitted'></i>";
	else if($total_required_doc > $total_driver_doc) return '<i><span class="badge" title="Missing Documents" style="padding:1px;">'.($total_required_doc - $total_driver_doc).'</span>';

}

function driver_vehicle($driver_id, $link='', $lang = ''){
	$total_required_doc = DB::table('documents')->where('type', 'Vehicle')
											    ->where('status', 'Active') 
											    ->count();

 	$vehicles = Vehicle::where('user_id', $driver_id)
 						->select('id','vehicle_number')
 						->get();

    $output = "";

    if($link !=''){
    	foreach ($vehicles as $vehicle) {
			if($lang == '' OR $lang == 'bn') $vehicle_number = $vehicle->vehicle_number;
			else $vehicle_number = vehicle_number_en($vehicle->vehicle_number);

			if(LOGIN_USER_TYPE=='company' || auth('admin')->user()->can('update_vehicle')){
				$output.='<a href="'.url(LOGIN_USER_TYPE.'/edit_vehicle/'.$vehicle->id).'" target="_blank">'.$vehicle_number.'</a>'.' '.total_driver_doc($vehicle->id).'<br>';
			}
			else $output.=$vehicle_number.' '.total_driver_doc($vehicle->id).'<br>';	        
	    }
    }
    else{
    	foreach ($vehicles as $vehicle) {
			if($lang == '' OR $lang == 'bn') $vehicle_number = $vehicle->vehicle_number;
			else $vehicle_number = vehicle_number_en($vehicle->vehicle_number);
	        $output.=$vehicle->vehicle_number.' '.total_driver_doc($vehicle->id).'<br>';
	    }
    }
    
    return $output;
}                

function driver_last_remarks($driver_id){
 	return DriverRemarks::where('driver_id', $driver_id)
 						->orderBy('id', 'DESC')
 						->pluck('remarks')
 						->first();
}

function is_exist($user_id, $type, $icon=''){
	$is_exist = 0;
	$expire_date = '';

	if($type == 'camera'){
		$status_array = array('Active','Inactive','Problem','Not Connected');

	 	$is_exist = MonitorCamera::where('driver_id', $user_id)
			 						->whereIn('camera_status', $status_array)
			 						->count();
	}
	else if($type == 'photo'){
	 	$is_exist = ProfilePicture::where('user_id', $user_id)
			 						->where('src', '!=', '')
			 						->count();
	}
	else if($type == 'driving_license'){
	 	$is_exist = DriverDocuments::where('user_id', $user_id)
			 						->where('type', 'Driver')
			 						->where('document_id', '5')
			 						->count();
		if($is_exist > 0){
			$expire_date = DB::table('driver_documents')
									->where('user_id', $user_id)
			 						->where('type', 'Driver')
			 						->where('document_id', '5')
			 						->whereNotNull('expired_date')
			 						->where('expired_date', '<', date('Y-m-d'))
			 						->pluck('expired_date')
			 						->first();
		}			 						 
	}
	else if($type == 'nid'){
	 	$is_exist = DriverDocuments::where('user_id', $user_id)
			 						->where('type', 'Driver')
			 						->where('document_id', '8')
			 						->count();			 						
	}
	else if($type == 'registration_paper'){
	 	$is_exist = DriverDocuments::where('user_id', $user_id)
			 						->where('type', 'Vehicle')
			 						->where('document_id', '6')
			 						->count();
		if($is_exist > 0){
			$expire_date = DB::table('driver_documents')
									->where('user_id', $user_id)
			 						->where('type', 'Vehicle')
			 						->where('document_id', '6')
			 						->whereNotNull('expired_date')
			 						->where('expired_date', '<', date('Y-m-d'))
			 						->pluck('expired_date')
			 						->first();
		}			 						
	}
	else if($type == 'tax_token'){
	 	$is_exist = DriverDocuments::where('user_id', $user_id)
			 						->where('type', 'Vehicle')
			 						->where('document_id', '9')
			 						->count();
		if($is_exist > 0){
			$expire_date = DB::table('driver_documents')
									->where('user_id', $user_id)
			 						->where('type', 'Vehicle')
			 						->where('document_id', '9')
			 						->whereNotNull('expired_date')
			 						->where('expired_date', '<', date('Y-m-d'))
			 						->pluck('expired_date')
			 						->first();
		}			 						
	}
	else if($type == 'enlistment_certificate'){
	 	$is_exist = DriverDocuments::where('user_id', $user_id)
			 						->where('type', 'Vehicle')
			 						->where('document_id', '11')
			 						->count();
		if($is_exist > 0){
			$expire_date = DB::table('driver_documents')
									->where('user_id', $user_id)
			 						->where('type', 'Vehicle')
			 						->where('document_id', '11')
			 						->whereNotNull('expired_date')
			 						->where('expired_date', '<', date('Y-m-d'))
			 						->pluck('expired_date')
			 						->first();
		}
	}
	else if($type == 'fitness_certificate'){
	 	$is_exist = DriverDocuments::where('user_id', $user_id)
			 						->where('type', 'Vehicle')
			 						->where('document_id', '12')
			 						->count();
		if($is_exist > 0){
			$expire_date = DB::table('driver_documents')
									->where('user_id', $user_id)
			 						->where('type', 'Vehicle')
			 						->where('document_id', '12')
			 						->whereNotNull('expired_date')
			 						->where('expired_date', '<', date('Y-m-d'))
			 						->pluck('expired_date')
			 						->first();
		}
	}
	else if($type == 'insurance'){
	 	$is_exist = DriverDocuments::where('user_id', $user_id)
			 						->where('type', 'Vehicle')
			 						->where('document_id', '13')
			 						->count();
		if($is_exist > 0){
			$expire_date = DB::table('driver_documents')
									->where('user_id', $user_id)
			 						->where('type', 'Vehicle')
			 						->where('document_id', '13')
			 						->whereNotNull('expired_date')
			 						->where('expired_date', '<', date('Y-m-d'))
			 						->pluck('expired_date')
			 						->first();
		}
	}
    


    if($icon !=''){
    	if($is_exist > 0) {
    		if($expire_date !='') return "Expired"; //return date("d-m-Y", strtotime($expire_date));
    		else return '<i class="fa fa-check" aria-hidden="true"></i>';
    	}
		else return '<i class="fa fa-times" aria-hidden="true"></i>';
    }else{
    	if($is_exist > 0) return 'Y';
		else return 'N';
    }	
}

// function differences($array1, $array2){
//     return array_merge(array_diff($array1,$array2),array_diff($array2,$array1));
// }


//in a[] but not in b[]
function differences( $a, $b) 
{   
    $n =  count($a);
    $m =  count($b);
    $output = array();

    for ( $i = 0; $i < $n; $i++) 
    { 
        $j; 
        for ($j = 0; $j < $m; $j++) 
            if ($a[$i] == $b[$j]) 
                break; 
  
        if ($j == $m) 
            $output[] = $a[$i]; 
    } 
    return  $output;
} 

function driver_status($driver_id = ''){
	$driver = DB::table('users')
                            ->where('id', $driver_id)
                            ->where('user_type', 'Driver')
                            ->select('status','checked','verified','trained')
                            //->pluck('status')
                            ->first();

    if(is_object($driver))	{						
		$driver_status = $driver->status;

		if($driver_status == 'Active') return $driver_status;
		else{
			// $processing_status = DB::table('driver_remarks')
		//                                    ->where('driver_id', $driver_id)
		//                                    ->whereNotNull('processing_status')
		//                                    ->orderBy('id', 'DESC')
		//                                    ->pluck('processing_status')
		//                                    ->first();
		//    if($processing_status !=''){
		//    	return Optional::where('type', 'processing_status')
			//     			->where('value', $processing_status)
			//                 ->pluck('name_en')
			//                 ->first();
		//    }

			if($driver->trained == '1') return 'Trained';
			else if($driver->verified == '1') return 'Verified';
			else if($driver->checked == '1') return 'Checked';

			if($driver_status == 'Inactive') return $driver_status;
			else{
				$doc_not_completed = "";
				$total = 0;

				if(is_exist($driver_id, 'photo') == 'N'){
					$doc_not_completed.= "Photo, ";
					$total++;
				}

				if(is_exist($driver_id, 'driving_license') == 'N'){
					$doc_not_completed.= "Driving, "; //  License
					$total++;
				}

				if(is_exist($driver_id, 'nid') == 'N'){
					$doc_not_completed.= "NID, ";
					$total++;
				}

				if(is_exist($driver_id, 'registration_paper') == 'N'){
					$doc_not_completed.= "Registration, ";	// Paper
					$total++;
				}

				if(is_exist($driver_id, 'tax_token') == 'N'){
					$doc_not_completed.= "Tax, "; // Token
					$total++;
				}

				if(is_exist($driver_id, 'enlistment_certificate') == 'N'){
					$doc_not_completed.= "Enlistment, ";	// Certificate
					$total++;
				}

				if(is_exist($driver_id, 'fitness_certificate') == 'N'){
					$doc_not_completed.= "Fitness, "; // Certificate
					$total++;
				}

				$doc_not_completed = rtrim($doc_not_completed, ', ');
				
				if($doc_not_completed == '') return "All documents submitted";
				else {
					if($total == 7) return "No document submitted";
					else return $doc_not_completed." not submitted";
				}
			}
		}  
	}  
}

function first_name($full_name){
	$name_array = explode(' ', $full_name);
	if(is_array($name_array)) return $name_array[0];
}

function last_name($full_name){
	$name_array = explode(' ', $full_name);
	$last_name = '';
	foreach ($name_array as $key => $data) {
		if($key != 0){
			$last_name.=$data." ";
		}
	}
	return trim($last_name);
}

function prifile_src_replace($user_id){ //$user_id
	$profile_picture = DB::table('profile_picture')->where('user_id', $user_id)->pluck('src')->first();

	// if($profile_picture !=''){
	// 	$find = array('admin.', 'commonapi.', 'driverapi.', 'riderapi.');
	// 	$replace = array('', '', '', '');
	// 	return str_replace($find, $replace, $profile_picture);
	// }
	// else{
	// 	return asset('images/user.jpeg');
	// }

	if($profile_picture !=''){
		$pathinfo = pathinfo($profile_picture);
		$image = @$pathinfo['filename'].'.'.@$pathinfo['extension'];
		
		$api_link = "https://".env('DRIVER_API_SUB_DOMAIN').".".env('DOMAIN').'/images/users/'.$user_id.'/'.$image;
		return $api_link;
		
		// $admin_domain_link = "https://".env('ADMIN_PANEL_SUB_DOMAIN').".".env('DOMAIN').'/images/users/'.$user_id.'/'.$image;


		// echo $profile_picture;
		// echo "<br>";
		// echo $main_domain_link;
		// echo "<br>";
		// echo $admin_domain_link;

		// if (file_exists($main_domain_link)) return $main_domain_link;
		// else if (file_exists($admin_domain_link)) return $admin_domain_link;
		// else return asset('images/user.jpeg');
	}

}


function document_info($user_id, $type){
	$array = array(
				'driving_license' => '5',
				'nid' => '8',
				'registration_paper' => '6',
				'tax_token' => '9',
				'enlistment_certificate' => '11',
				'fitness_certificate' => '12',
				'insurance' => '13'
			);

	return DriverDocuments::where('user_id', $user_id)
 						->where('document_id', $array[$type])
 						->first();	
}

function document_info_details($user_id, $type, $col_name, $i){
	$doc = document_info($user_id, $type);
	$output = '';
	if(is_object($doc) AND $doc->document !=''){
		$col_by =  $col_name.'_by';
		$col_time =  $col_name.'_time';
		$expire = '';
		
		if($type !='nid' AND $type !='registration_paper'  AND $type !='enlistment_certificate' AND $type !='fitness_certificate'){
			if(strtotime($doc->expired_date) < strtotime(date('Y-m-d'))) $expire = '<font color="#e00" data-doc_name="'.ucwords(str_replace('_', ' ', $type)).'"  data-id="'.$doc->id.'"  data-date="'. date("d-m-Y", strtotime($doc->expired_date)).'" class="setExpireDate" id="exp_'.$doc->id.'">Expired</font>';
		}

	    if($doc->$col_name == '1'){  
	    	$output = '                             
	        <i class="fa fa-check text-success uncheck" aria-hidden="true" title="'.ucwords($col_name).' by '.admin_user($doc->$col_by).' at '.date('d-M-Y, h:i A',strtotime($doc->$col_time)).'" 
			data-sl="sl_'.$i.'" data-id="'.$doc->id.'" data-user_id="'.$user_id.'" data-col="'.$col_name.'" data-tab="driver_documents"
			></i>
	        <a href="'.$doc->document.'" target="_blank">
	          <img src="'.$doc->document.'" width="18" height="18">
	        </a>';
	    }
	    else{
	    	$output = ' 
		      <span>
		        <input type="checkbox" name="driving_license" class="update" data-sl="sl_'.$i.'" data-id="'.$doc->id.'" data-user_id="'.$user_id.'" data-col="'.$col_name.'" data-tab="driver_documents">
		      </span>
		      <a href="'.$doc->document.'" target="_blank">
		        <img src="'.$doc->document.'" width="18" height="18">
			  </a>
				'.$expire;

			  if($col_name != 'checked' ){
				  if($col_name == 'verified') $c = 'checked';
				$output.= ' 
				<span>
					<i class="fa fa-close text-danger uncheck" aria-hidden="true"  data-id="'.$doc->id.'"  data-user_id="'.$user_id.'" data-col="'.$c.'" data-sl="sl_'.$i.'" data-tab="driver_documents"></i>
				</span>';
			  }				
	  	}
    }
	return $output;	
}

function document_link($user_id, $type){
	$doc = document_info($user_id, $type);
	if(is_object($doc)) return $doc->document;
}
function documentType($user_id, $type){
	$doc = document_info($user_id, $type);
	if(is_object($doc)) return $doc->document_type;
}

function admin_user($id){
	return Admin::where('id', $id)->pluck('username')->first();
}

function company_name($id){
	return DB::table('companies')->where('id', $id)->pluck('name')->first();
}
function user_name($id){
	return DB::table('users')
				->select(DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'))
				->where('id', $id)
				->pluck('driver_name')
				->first();
}

function doc_name_from_url($url){
	$doc_name = '';
	$array = explode('/', $url);
	if(is_array($array)){
		$name_array = explode('_', str_replace('.', '_', $array[count($array)-1]));
		foreach($name_array as $name){
			if(!is_numeric($name)) $doc_name.=$name.' ';
			else break;			
		}
	}
	return strtoupper($doc_name);	
}
function total_company_driver($company_id){
	return DB::table('users')->where('user_type', 'Driver')->where('company_id', $company_id)->count();
}

function number_en_to_bn($text){
	$search = array('1','2','3','4','5','6','7','8','9','0');
	$replace = array('১', '২','৩','৪','৫','৬','৭','৮','৯','০');
	return str_replace($search, $replace, $text);
}
function number_bn_to_en($text){
	$search = array('১', '২','৩','৪','৫','৬','৭','৮','৯','০');
	$replace = array('1','2','3','4','5','6','7','8','9','0');
	return str_replace($search, $replace, $text);
}

function city_en_to_bn($text){
	$search = VehicleCity::pluck('city_en')->toArray();
	$replace = VehicleCity::pluck('city')->toArray();
	return str_replace($search, $replace, $text);
}
function city_bn_to_en($text){
	$search = VehicleCity::pluck('city')->toArray();
	$replace = VehicleCity::pluck('city_en')->toArray(); //city_en
	return str_replace($search, $replace, $text);
}

function letter_en_to_bn($text){
	$search = array_reverse(VehicleRegistrationLetter::pluck('reg_letter_en')->toArray());
	$replace = array_reverse(VehicleRegistrationLetter::pluck('reg_letter')->toArray());
	return str_replace($search, $replace, $text);
}
function letter_bn_to_en($text){
	$search = VehicleRegistrationLetter::pluck('reg_letter')->toArray();
	$replace = VehicleRegistrationLetter::pluck('reg_letter_en')->toArray();
	return str_replace($search, $replace, $text);
}

function vehicle_number_en($text){
	$text = number_bn_to_en($text);
	$text = city_bn_to_en($text);
	return $text = letter_bn_to_en($text);
}

function vehicle_number_bn($text){
	$text = number_en_to_bn($text);
	$text = city_en_to_bn($text);
	return $text = letter_en_to_bn($text);
}

function save_otp($to, $otp){
	$otp_table = new Otp;
	$otp_table->mobile_number = $to;
	$otp_table->otp = strval($otp);
	$otp_table->save();
}

function registration_paper($vehicle_id){
	return DriverDocuments::where('type', 'Vehicle')
                        ->where('document_id', '6')
                        ->where('vehicle_id', $vehicle_id)
                        ->pluck('document')
                        ->first();
}
function vehicle_model($vehicle_model_id){
	$vehicle_model = VehicleModel::find($vehicle_model_id);
	if(is_object($vehicle_model)) return $vehicle_model->model_name;
}
function vehicle_make($vehicle_make_id){
	$vehicle_make = VehicleMake::find($vehicle_make_id);
	if(is_object($vehicle_make)) return $vehicle_make->make_vehicle_name;
}
