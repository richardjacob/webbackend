<?php

/**
 * OTP Helper
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Helper
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Helper;

//Added by Nishat Start
use Illuminate\Support\Facades\DB;
use SoapClient;
//Added by Nishat End

class OtpHelper
{
	/**
	 * Send OTP
	 *
	 * @param integer $country_code
	 * @param integer $mobile_number
	 * @return Array $response
	 */

	public function __construct()
	{
		$this->sms_helper = resolve('App\Http\Helper\SmsHelper');
	}


	//Added by Nishat Start
	public static $bn_numbers = ["১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০"];
	//public static $bn_numbers = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];
	public static $en_numbers = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "0"];
	//Added by Nishat End




	public function sendOtp($mobile_number, $country_code)
	{

		// Commented By Nishat Start	    

		// 		$otp = rand(1000,9999);
		//         $text = 'Your OTP number is '.$otp;
		//         $to = '+'.$country_code.$mobile_number;
		//         $sms_gateway = resolve("App\Contracts\SMSInterface");
		//         $response = $sms_gateway->send($to,$text);
		//         if($response['status_code']==1) {
		//         	session([
		// 				'signup_mobile' => $mobile_number,
		// 				'signup_country_code' => $country_code,
		// 				'signup_otp' => $otp,
		// 			]);
		//         }
		// 		return $response;

		// Commented By Nishat End


		//Added by Nishat Start

		$otp = rand(1000, 9999);
		//	$otp_bn = str_replace(self::$en_numbers, self::$bn_numbers, strval($otp));
		//$otp_message = 'আপনার আলেশা রাইড এর OTP ' . $otp_bn;
		$otp_message = 'আপনার আলেশা রাইড এর OTP ' . strval($otp);
		$to = $country_code . $mobile_number;
		if (substr($to, 0, 4) == "8800") {
			$to = substr($to, 3);
		}
		if (substr($to, 0, 4) == "8801") {
			$to = substr($to, 2);
		}
		save_otp($to, $otp);

		// $Onnorokom_api_key_get = DB::table('api_credentials')
		// 	->where('name', 'token')
		// 	->where('site', 'Onnorokom')
		// 	->first()->value;

		try {
			// $soapClient = new SoapClient("https://api2.onnorokomSMS.com/sendSMS.asmx?wsdl");
			// $paramArray = array(
			// 	'apiKey' =>  $Onnorokom_api_key_get,
			// 	'messageText' => $otp_message,
			// 	'numberList' => $to,
			// 	'smsType' => "TEXT",
			// 	'maskName' => '',
			// 	'campaignName' => '',
			// );
			// $value = $soapClient->__call("NumberSms", array($paramArray));
			// if (explode('||', $value->NumberSmsResult)[0] == 1900) {

			// 	session([
			// 		'signup_mobile' => $mobile_number,
			// 		'signup_country_code' => $country_code,
			// 		'signup_otp' => $otp,
			// 	]);

			// 	$response = [
			// 		'status_code' => 1,
			// 		'message' => 'Success'
			// 	];

			// 	return $response;
			// } else {
			// 	$response = [
			// 		'status_code' => 0,
			// 		'message' => 'Please Enter a Valid Number'
			// 	];
			// 	return $response;
			// }




			// $sms_result = $this->sms_helper->send($to, $otp_message);
			// if ($sms_result['0'] == 0) {
			// 	session([
			// 		'signup_mobile' => $mobile_number,
			// 		'signup_country_code' => $country_code,
			// 		'signup_otp' => $otp,
			// 	]);

			// 	$response = [
			// 		'status_code' => 1,
			// 		'message' => 'Success'
			// 	];

			// 	return $response;
			// } else {
			// 	$response = [
			// 		'status_code' => 0,
			// 		'message' => 'Please Enter a Valid Number'
			// 	];
			// 	return $response;
			// }



			$sms_result = $this->sms_helper->send($to, $otp_message,$api_or_web="web_OTP");
			$sms_result =  json_decode($sms_result, true);
			if ($sms_result['Status'] == 0) {
				$sms_check = $this->sms_helper->send_message_check($sms_result['Message_ID']);
				$message_result =  json_decode($sms_check, true);
				if ($message_result['Status'] == 0 || $message_result['Status'] == 2 || $message_result['Status'] == 4) {
					session([
						'signup_mobile' => $mobile_number,
						'signup_country_code' => $country_code,
						'signup_otp' => $otp,
					]);

					$response = [
						'status_code' => 1,
						'message' => 'Success'
					];

					return $response;
				} else {
					$response = [
						'status_code' => 0,
						'message' => ' Failed To Sent OTP'
					];
					return $response;
				}
			} else {
				$response = [
					'status_code' => 0,
					'message' => ' Failed To Sent OTP'
				];
				return $response;
			}
		} catch (\Exception $e) {
			$response = [
				'status_code' => 0,
				'message' => 'Something Went Wrong. Failed To Sent OTP',
				// 'message' => $e->getMessage(),
			];

			return $response;
		}


		//Added by Nishat End














	}

	/**
	 * Resend OTP
	 *
	 * @return Array $response
	 */
	public function resendOtp()
	{
		// 		$otp = rand(1000,9999);
		//         $text = 'Your OTP number is '.$otp;
		//         $to = '+'.session('signup_country_code').session('signup_mobile');
		//         $sms_gateway = resolve("App\Contracts\SMSInterface");
		//         $response = $sms_gateway->send($to,$text);

		//         if($response['status_code']==1) {
		//             session(['signup_otp' => $otp]);
		//             $response['message'] = trans('messages.signup.otp_resended');
		//         }

		// 		return $response;


		//Added by Nishat Start
		$otp = rand(1000, 9999);
		// $otp_bn = str_replace(self::$en_numbers, self::$bn_numbers, strval($otp));
		// $otp_message = 'আপনার আলেশা রাইড এর OTP ' . $otp_bn;
		$otp_message = 'আপনার আলেশা রাইড এর OTP ' . strval($otp);
		$to = session('signup_mobile');
		if (substr($to, 0, 4) == "8800") {
			$to = substr($to, 3);
		}
		if (substr($to, 0, 4) == "8801") {
			$to = substr($to, 2);
		}
		save_otp($to, $otp);
		// $Onnorokom_api_key_get = DB::table('api_credentials')
		// 	->where('name', 'token')
		// 	->where('site', 'Onnorokom')
		// 	->first()->value;

		try {
			// $soapClient = new SoapClient("https://api2.onnorokomSMS.com/sendSMS.asmx?wsdl");
			// $paramArray = array(
			// 	'apiKey' =>  $Onnorokom_api_key_get,
			// 	'messageText' => $otp_message,
			// 	'numberList' => $to,
			// 	'smsType' => "TEXT",
			// 	'maskName' => '',
			// 	'campaignName' => '',
			// );
			// $value = $soapClient->__call("NumberSms", array($paramArray));
			// if (explode('||', $value->NumberSmsResult)[0] == 1900) {
			// 	session(['signup_otp' => $otp]);
			// 	$response = [
			// 		'message' => trans('messages.signup.otp_resended')
			// 	];
			// 	return $response;
			// } else {
			// 	$response = [
			// 		'message' => 'Please Enter a Valid Number'
			// 	];
			// 	return $response;
			// }

			// $sms_result = $this->sms_helper->send($to, $otp_message);
			// if ($sms_result['0'] == 0) {
			// 	session(['signup_otp' => $otp]);
			// 	$response = [
			// 		'message' => trans('messages.signup.otp_resended')
			// 	];
			// 	return $response;
			// } else {
			// 	$response = [
			// 		'message' => 'Please Enter a Valid Number'
			// 	];
			// 	return $response;
			// }



			$sms_result = $this->sms_helper->send($to, $otp_message,$api_or_web="web_OTP");
			$sms_result =  json_decode($sms_result, true);
			if ($sms_result['Status'] == 0) {
				$sms_check = $this->sms_helper->send_message_check($sms_result['Message_ID']);
				$message_result =  json_decode($sms_check, true);
				//if ($message_result['Status'] == 0) {
				if ($message_result['Status'] == 0 || $message_result['Status'] == 2 || $message_result['Status'] == 4) {
					session(['signup_otp' => $otp]);
					$response = [
						'message' => trans('messages.signup.otp_resended')
					];
					return $response;
				} else {
					$response = [
						'message' => 'Failed To Sent OTP'
					];
					return $response;
				}
			} else {
				$response = [
					'message' => 'Failed To Sent OTP'
				];
				return $response;
			}
		} catch (\Exception $e) {
			$response = [
				'message' => 'Something Went Wrong. Failed To Sent OTP',
				//'message' => $e->getMessage()
			];

			return $response;
		}


		//Added by Nishat End








	}

	/**
	 * Check Given OTP
	 *
	 * @param integer $otp
	 * @param integer $mobile_number
	 * @return Array $response
	 */
	public function checkOtp($otp, $mobile_number = null, $country_code = null)
	{
		$data = ['status_code' => 0, 'message' => trans('messages.signup.wrong_otp')];

		if (!site_settings('otp_verification')) {
			session([
				'signup_mobile' => $mobile_number,
				'signup_country_code' => $country_code,
			]);
			$data = ['status_code' => 1, 'message' => 'success'];
		} elseif ($otp == session('signup_otp') && ($mobile_number == null || $mobile_number == session('signup_mobile'))) {
			$data = ['status_code' => 1, 'message' => 'success'];
		}
		return $data;
	}
}
