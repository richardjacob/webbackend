<?php

/**
 * Auth Via Google
 *
 * @package     Gofer
 * @subpackage  Services
 * @category    Auth Service
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
*/

namespace App\Services\SMS;

use Illuminate\Http\Request;
use App\Contracts\SMSInterface;

class TwillioSms implements SMSInterface
{
	private $base_url,$token,$sid;

	/**
     * Initialize Twillo credentials
     *
     * @return void
     */
	public function initialize()
	{
		$this->sid    = TWILLO_SID;
		$this->token  = TWILLO_TOKEN;
		$this->from 	= TWILLO_FROM;
		$this->base_url = "https://api.twilio.com/2010-04-01/Accounts/".$this->sid."/SMS/Messages.json";
	}

	/**
     * Send Text message
     *
     * @param Array $[data]
     * @return Array SMS Response
     */
	protected function sendMessage($data)
	{
		$postData = http_build_query($data);
		$ch = curl_init($this->base_url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$this->sid:$this->token");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		$result = curl_exec($ch);

		return json_decode($result,true);
	}

	/**
     * Get Response from restult
     *
     * @param Array $[result]
     * @return Array SMS Response
     */
	protected function getResponse($result)
	{
		if(canDisplayCredentials()) {
			return [
				'status_code' => 1,
				'message' => 'Success'
			];
		}
		if ($result['status'] != 'queued') {
			$response = [
				'status_code' => 0,
				'message' => $result['message']
			];
		}
		else {
			$response = [
				'status_code' => 1,
				'message' => 'Success'
			];
		}

		return $response;
	}

	/**
     * Send Text message to mobile
     *
     * @param String $[to]
     * @param String $[text] [message to be send]
     * @return Array SMS Response
     */
	public function send($to, $text)
	{
		$this->initialize();
		$data = array(
			"Body" => $text,
			"From" => $this->from,
			"To"=> $to
		);

		$result = $this->sendMessage($data);

		return $this->getResponse($result);
	}
}