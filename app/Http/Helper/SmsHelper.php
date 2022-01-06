<?php

namespace App\Http\Helper;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CustomLog;

class SmsHelper
{
	public function send($mobile_number, $sms_content,$api_or_web)
	{
		$conf = Config('sms_provider.reve');
		$url = $conf['url'];
		$apikey = $conf['apikey'];
		$secretkey = $conf['secretkey'];
		$callerID = $conf['callerID'];

		CustomLog::info($url . "sendtext?apikey=" . $apikey . "&secretkey=" . $secretkey . "&callerID=" . $callerID . "&toUser=" . $mobile_number . "&messageContent=" . $sms_content.$api_or_web);
		return $result = self::file_post_contents($url . "sendtext?apikey=" . $apikey . "&secretkey=" . $secretkey . "&callerID=" . $callerID . "&toUser=" . $mobile_number . "&messageContent=" . $sms_content);
		//	echo $result;


	}


	public function send_message_check($message_id)
	{
		$conf = Config('sms_provider.reve');
		$url = $conf['url'];
		$apikey = $conf['apikey'];
		$secretkey = $conf['secretkey'];

		CustomLog::info($url . "getstatus?apikey=" . $apikey . "&secretkey=" . $secretkey  . "&messageid=" . $message_id);
		return self::file_post_contents($url . "getstatus?apikey=" . $apikey . "&secretkey=" . $secretkey . "&messageid=" . $message_id);
	}

	function file_post_contents($url, &$headers = false)
	{ // v0.2 Updated 17/06/08 by HM2K
		$url = parse_url($url);
		$url['protocol'] = $url['scheme'] . '://';
		$url['query'] = isset($url['query']) ? $url['query'] : '';
		$eol = "\r\n"; //end of line
		$send =     'POST ' . $url['path'] . ' HTTP/1.0' . $eol .
			'Host: ' . $url['host'] . $eol .
			'User-Agent: ' . ini_get('user_agent') . $eol .
			'Referer: ' . $url['protocol'] . $url['host'] . $url['path'] . $eol .
			'Content-Type: application/x-www-form-urlencoded' . $eol .
			'Content-Length: ' . strlen($url['query']) . $eol .
			$eol . $url['query'] . $eol;
		if (!isset($url['port'])) {
			$url['port'] = getservbyname($url['scheme'], 'tcp');
		}
		if ($url['scheme'] == 'https') {
			$url['host'] = 'ssl://' . $url['host'];
		}
		$fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
		if ($fp) {
			fputs($fp, $send);
			$result = '';
			while (!feof($fp)) {
				$result .= fgets($fp, 128);
			}
			fclose($fp);
			$pattern = "/^(.+?)\r\n\r\n(.+)/s"; //as per RFC
			$result = preg_match($pattern, $result, $matches);
			if (!empty($matches[1])) $headers = $matches[1];
			if (!empty($matches[2])) return $matches[2];
		}
	}

    public function sendBulk($data)
    {
        $conf = Config('sms_provider.reve');
        $url = $conf['url'].'send';
        $apikey = $conf['apikey'];
        $secretkey = $conf['secretkey'];
        $callerID = $conf['callerID'];

        CustomLog::info($url . "?apikey=" . $apikey . "&secretkey=" . $secretkey . "&content=[{\"callerID\":\"". $callerID ."\",\"toUser\":\"". $data['mobile_number'] ."\",\"messageContent\":\"".$data['sms_content']."\"}]");

        //dd($data);
        $result = self::file_post_contents($url . "?apikey=" . $apikey . "&secretkey=" . $secretkey . "&content=[{\"callerID\":\"". $callerID ."\",\"toUser\":\"". $data['mobile_number'] ."\",\"messageContent\":\"".$data['sms_content']."\"}]");
        return $result;
    }
}
