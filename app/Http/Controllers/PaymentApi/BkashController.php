<?php

namespace App\Http\Controllers\PaymentApi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentApi\PayToAdmin;
use App\Models\TempTransaction;
use App\Models\PaymentGateway;
use DB;

class NagadController extends Controller
{
    public static function generateRandomString($length = 40)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function EncryptDataWithPublicKey($data, $pgPublicKey)
    {
        $public_key = "-----BEGIN PUBLIC KEY-----\n" . $pgPublicKey . "\n-----END PUBLIC KEY-----";
        // echo $public_key; 
        // exit();
        $key_resource = openssl_get_publickey($public_key);
        openssl_public_encrypt($data, $cryptText, $key_resource);
        return base64_encode($cryptText);
    }

    public static function SignatureGenerate($data, $merchantPrivateKey)
    {
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
        // echo $private_key; 
        // exit();
        openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    public static function HttpPostMethod($PostURL, $PostData)
    {
        $url = curl_init($PostURL);
        $postToken = json_encode($PostData);
        $header = array(
            'Content-Type:application/json',
            'X-KM-Api-Version:v-0.2.0',
            'X-KM-IP-V4:' . self::get_client_ip(),
            'X-KM-Client-Type:PC_WEB'
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $postToken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_SSL_VERIFYPEER, false);

        $resultData = curl_exec($url);
        $ResultArray = json_decode($resultData, true);
        curl_close($url);
        return $ResultArray;
    }

    public static function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public static function DecryptDataWithPrivateKey($cryptText, $merchantPrivateKey)
    {
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
        openssl_private_decrypt(base64_decode($cryptText), $plain_text, $private_key);
        return $plain_text;
    }

    public static function index($mode, $merchant_id, $account_number, $public_key, $private_key, $OrderId, $amount)
    {

        $DateTime = Date('YmdHis');
        $random = self::generateRandomString();

        if ($mode == 'live') {
            $PostURL = "https://api.mynagad.com/api/dfs/check-out/initialize/" . $merchant_id . "/" . $OrderId;

            $OrderSubmitUrl = "https://api.mynagad.com/api/dfs/check-out/complete/"; //{paymentReferenceId}
        } else if ($mode == 'sandbox') {
            $PostURL = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/check-out/initialize/" . $merchant_id . "/" . $OrderId;

            $OrderSubmitUrl = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/check-out/complete/"; //{paymentReferenceId}
        }

        $merchantCallbackURL = "https://payment.alesharide.com/nagad/callback";

        $SensitiveData = array(
            'merchantId' => $merchant_id,
            'datetime' => $DateTime,
            'orderId' => $OrderId,
            'challenge' => $random
        );

        $PostData = array(
            'accountNumber' => $account_number,
            'dateTime' => $DateTime,
            'sensitiveData' => self::EncryptDataWithPublicKey(json_encode($SensitiveData), $public_key),
            'signature' => self::SignatureGenerate(json_encode($SensitiveData), $private_key)
        );


        $Result_Data = self::HttpPostMethod($PostURL, $PostData);

        if (isset($Result_Data['sensitiveData']) && isset($Result_Data['signature'])) {
            if ($Result_Data['sensitiveData'] != "" && $Result_Data['signature'] != "") {

                $PlainResponse = json_decode(self::DecryptDataWithPrivateKey($Result_Data['sensitiveData'], $private_key), true);

                if (isset($PlainResponse['paymentReferenceId']) && isset($PlainResponse['challenge'])) {

                    $paymentReferenceId = $PlainResponse['paymentReferenceId'];
                    $randomServer = $PlainResponse['challenge'];

                    $SensitiveDataOrder = array(
                        'merchantId' => $merchant_id,
                        'orderId' => $OrderId,
                        'currencyCode' => '050',
                        'amount' => $amount,
                        'challenge' => $randomServer
                    );

                    $merchantAdditionalInfo = '{"Service Name": "alesharide.com"}';

                    $PostDataOrder = array(
                        'sensitiveData' => self::EncryptDataWithPublicKey(json_encode($SensitiveDataOrder), $public_key),
                        'signature' => self::SignatureGenerate(json_encode($SensitiveDataOrder), $private_key),
                        'merchantCallbackURL' => $merchantCallbackURL,
                        'additionalMerchantInfo' => json_decode($merchantAdditionalInfo)
                    );

                    $OrderSubmitUrl = $OrderSubmitUrl . $paymentReferenceId;

                    $Result_Data_Order = self::HttpPostMethod($OrderSubmitUrl, $PostDataOrder);

                    // echo json_encode($Result_Data_Order);

                    if ($Result_Data_Order['status'] == "Success") {
                        $url = json_encode($Result_Data_Order['callBackUrl']);
                        echo "<script>window.open($url, '_self')</script>";
                    } else {
                        echo json_encode($Result_Data_Order);
                    }
                } else {
                    echo json_encode($PlainResponse);
                }
            }
        }
    }

    public function HttpGet($url)
    {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/0 (Windows; U; Windows NT 0; zh-CN; rv:3)");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $file_contents = curl_exec($ch);
        curl_error($ch);
        curl_close($ch);
        return $file_contents;
    }

    public function callback()
    {
        if (isset(explode("?", $_SERVER['REQUEST_URI'])[1])) {

            $mode = PaymentGateway::where('site', 'Nagad')
                ->where('name', 'mode')
                ->pluck('value')
                ->first();

            $Query_String  = explode("&", explode("?", $_SERVER['REQUEST_URI'])[1]);
            $payment_ref_id = substr($Query_String[2], 15);

            if ($mode == 'live') {
                $url = "https://api.mynagad.com/api/dfs/verify/payment/" . $payment_ref_id;
            } else if ($mode == 'sandbox') {
                $url = "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs/check-out/complete/" . $payment_ref_id;
            }

            $json = self::HttpGet($url);
            $arr = json_decode($json, true);


            $response_array = json_decode(json_encode($arr), true);
            //print_r($response_array);


            // $response_array = array(
            //     'merchantId' => '689580971105399',
            //     'orderId' => 'ALESHA16195982747456',
            //     'paymentRefId' => 'MDQyODE0MjQzNDkzMC42ODk1ODA5NzExMDUzOTkuQUxFU0hBMTYxOTU5ODI3NDc0NTYuMTJmOTg4OTQxNjkxZmNjYzI5YTA=',
            //     'amount' => '1',
            //     'clientMobileNo' => '017****8051',
            //     'merchantMobileNo' => '01958097110',
            //     'orderDateTime' => '2021-04-25 12:36:35.0',
            //     'issuerPaymentDateTime' => '2021-04-25 12:37:26.0',
            //     'issuerPaymentRefNo' => '70Q01X23',
            //     'additionalMerchantInfo' => '{"Service Name":"alesharide.com"}',
            //     'status' => 'Success',
            //     'statusCode' => '000',
            //     'cancelIssuerDateTime' => '',
            //     'cancelIssuerRefNo' => ''
            // );

            $payToAdmin = new PayToAdmin();
            $payToAdmin->index($response_array, 'Nagad');

            //PayToAdmin::index($response_array, 'Nagad');
        } else {
            return;
        }
    }
}
