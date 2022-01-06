<?php



namespace App\Http\Controllers;



class TestController extends Controller
{
	public function index()
    {
   
        function generateRandomString($length = 40)
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
        
        function EncryptDataWithPublicKey($data)
        {
            $pgPublicKey = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiCWvxDZZesS1g1lQfilVt8l3X5aMbXg5WOCYdG7q5C+Qevw0upm3tyYiKIwzXbqexnPNTHwRU7Ul7t8jP6nNVS/jLm35WFy6G9qRyXqMc1dHlwjpYwRNovLc12iTn1C5lCqIfiT+B/O/py1eIwNXgqQf39GDMJ3SesonowWioMJNXm3o80wscLMwjeezYGsyHcrnyYI2LnwfIMTSVN4T92Yy77SmE8xPydcdkgUaFxhK16qCGXMV3mF/VFx67LpZm8Sw3v135hxYX8wG1tCBKlL4psJF4+9vSy4W+8R5ieeqhrvRH+2MKLiKbDnewzKonFLbn2aKNrJefXYY7klaawIDAQAB";
            $public_key = "-----BEGIN PUBLIC KEY-----\n" . $pgPublicKey . "\n-----END PUBLIC KEY-----";
            // echo $public_key; 
            // exit();
            $key_resource = openssl_get_publickey($public_key);
            openssl_public_encrypt($data, $cryptText, $key_resource);
            return base64_encode($cryptText);
        }
        
        function SignatureGenerate($data)
        {
            $merchantPrivateKey = "MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCA7zyJ65jo7aDRPJcZ6u+LahGD7uVRg9LFHcjJ8zloNro0ywhRm91ASTpbc/8ovIcmQc2sn2tj5S3aI0mvlOqowl9Y1J/qCp7UdLVO9Ac574ORbEqxvBT/S4o2gjd8rVRDlSsd1iNFmZ1WFdX/m9o+vPldqq+oH6+yFx33EfPJz65gCpbyzfrVA6NXSoRAct3R9sl0vMiPpPxFjqsfHrpnwHeReYrMwA/RbVWS3UsGWOFB7F6rMFjk6oB4po8JFJ5p9pCCdWW6dTkLMcGDFGhh4HRdycevkpzJWrCk+pY9NpyjsSR4+Pt+jeXJAZsVfcbeBFBpTLA5hZ/QHS8BMnZ3AgMBAAECggEAYV/MkcDrl6a3WK5w03MZ/Glb1w8aA0kSaioHVXWqemkykeJwYuna280yFJVzM/nF+/+gbGltumEAEwmpgcBuxIsfZUAXkbL5pyyJLZqgxoF8FNz7QGXyWahcNrR7YV6qD+xdRshNlSfhqn0NRGEZH16q2bGpvchIcbCBwfG980AhqWAA7Cyl8NV2RjiC9WkA9QbntcpSaGjamjX9fFQs/pUF5ir/Kdj65wOE4cFCgM0Bmg7rGMllmByOwWmT9H0QSjqFkGcw6zAvp2eMa+smFDGtwvRTPN8YxxLYimxMfXZz2hqnoyaPcCEFvWkDZX+C9Ye2pPJP72nZZMBCSGuWCQKBgQDDOGzSa5taCasjdCqKsgWcT7hccpVVZ9IhHNzrci/8eE/l/eeXTVvxYaD7SAU83IYwtQjst6kPWlFJVTsF1ticUuGopL8kUBfATR7yGBmmEKYHwF4+sF/ogU7Ec+Y1x65zeVXtsy9RARfASi0w3VfTw17RRiDON0eP85s9H08e4wKBgQCpE6c17bVnBJW1EIxyNFTsfMG+wy0/M6lb8TVqMosrYuZH+ssFGs+cE1uZMhq6tX8n6OezeYiRaYbC8wbgh3Zkv8Z1nenjacpbw7hzvM58FOkrKtQh4dAWQh8Cm1gWR7NyFgLiP7ZBtmPVDfUNgRkVeoQAE/HPPsuCGPZocoEqXQKBgF/8O01OiUjA3juFX3FoTGfpro0N7azbai7LwTemoj9xbF958seqyp0pUnyakbC2AOir6mJxfxdEYhk7ZdTeKQzbl4ZS0oRpOPRdNuzzupCzON8wz2XlVJK+GVtwXO1ua7DtJLnF02rxrZrnHccb3ZYKRnGBGJosBeyaa2anIUDfAoGAN7b+NssqJ9Re8NePMGMGSRejUJVIZ7dCa8XEcEZPjfB9KeL+26PGHgxS9tyH6la8nA4nrAC0fBZmDf+/mGWLIg//+eoblJDb2flY4uqosELDwzHNzYx34Z3QUi+Wi2D9RO7z7FJKYMUViLOcuOJ9vZItxUjNOhnbPfez2x9AaRECgYA/MZ4DPG2LsHkvsyTWhMj4XIIyBbeySCsQgOedM0ilUkB2PPcvLDGuu+XY/Qp8xKu9aDLzXlJdj7Jx7boIsWTZpBbBxyKF6DO16qFNYtA0sJl4MDLK6z+zZkQuorhriRyjzjmModeZvQxCjRSYmRNWrc8ugQgxwAaH6ip1ahun1Q==";
            $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
            // echo $private_key; 
            // exit();
            openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);
            return base64_encode($signature);
        }
        
        function HttpPostMethod($PostURL, $PostData)
        {
            $url = curl_init($PostURL);
            $postToken = json_encode($PostData);
            $header = array(
                'Content-Type:application/json',
                'X-KM-Api-Version:v-0.2.0',
                'X-KM-IP-V4:' . get_client_ip(),
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
        
        function get_client_ip()
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
        
        function DecryptDataWithPrivateKey($cryptText)
        {
            $merchantPrivateKey = "MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCA7zyJ65jo7aDRPJcZ6u+LahGD7uVRg9LFHcjJ8zloNro0ywhRm91ASTpbc/8ovIcmQc2sn2tj5S3aI0mvlOqowl9Y1J/qCp7UdLVO9Ac574ORbEqxvBT/S4o2gjd8rVRDlSsd1iNFmZ1WFdX/m9o+vPldqq+oH6+yFx33EfPJz65gCpbyzfrVA6NXSoRAct3R9sl0vMiPpPxFjqsfHrpnwHeReYrMwA/RbVWS3UsGWOFB7F6rMFjk6oB4po8JFJ5p9pCCdWW6dTkLMcGDFGhh4HRdycevkpzJWrCk+pY9NpyjsSR4+Pt+jeXJAZsVfcbeBFBpTLA5hZ/QHS8BMnZ3AgMBAAECggEAYV/MkcDrl6a3WK5w03MZ/Glb1w8aA0kSaioHVXWqemkykeJwYuna280yFJVzM/nF+/+gbGltumEAEwmpgcBuxIsfZUAXkbL5pyyJLZqgxoF8FNz7QGXyWahcNrR7YV6qD+xdRshNlSfhqn0NRGEZH16q2bGpvchIcbCBwfG980AhqWAA7Cyl8NV2RjiC9WkA9QbntcpSaGjamjX9fFQs/pUF5ir/Kdj65wOE4cFCgM0Bmg7rGMllmByOwWmT9H0QSjqFkGcw6zAvp2eMa+smFDGtwvRTPN8YxxLYimxMfXZz2hqnoyaPcCEFvWkDZX+C9Ye2pPJP72nZZMBCSGuWCQKBgQDDOGzSa5taCasjdCqKsgWcT7hccpVVZ9IhHNzrci/8eE/l/eeXTVvxYaD7SAU83IYwtQjst6kPWlFJVTsF1ticUuGopL8kUBfATR7yGBmmEKYHwF4+sF/ogU7Ec+Y1x65zeVXtsy9RARfASi0w3VfTw17RRiDON0eP85s9H08e4wKBgQCpE6c17bVnBJW1EIxyNFTsfMG+wy0/M6lb8TVqMosrYuZH+ssFGs+cE1uZMhq6tX8n6OezeYiRaYbC8wbgh3Zkv8Z1nenjacpbw7hzvM58FOkrKtQh4dAWQh8Cm1gWR7NyFgLiP7ZBtmPVDfUNgRkVeoQAE/HPPsuCGPZocoEqXQKBgF/8O01OiUjA3juFX3FoTGfpro0N7azbai7LwTemoj9xbF958seqyp0pUnyakbC2AOir6mJxfxdEYhk7ZdTeKQzbl4ZS0oRpOPRdNuzzupCzON8wz2XlVJK+GVtwXO1ua7DtJLnF02rxrZrnHccb3ZYKRnGBGJosBeyaa2anIUDfAoGAN7b+NssqJ9Re8NePMGMGSRejUJVIZ7dCa8XEcEZPjfB9KeL+26PGHgxS9tyH6la8nA4nrAC0fBZmDf+/mGWLIg//+eoblJDb2flY4uqosELDwzHNzYx34Z3QUi+Wi2D9RO7z7FJKYMUViLOcuOJ9vZItxUjNOhnbPfez2x9AaRECgYA/MZ4DPG2LsHkvsyTWhMj4XIIyBbeySCsQgOedM0ilUkB2PPcvLDGuu+XY/Qp8xKu9aDLzXlJdj7Jx7boIsWTZpBbBxyKF6DO16qFNYtA0sJl4MDLK6z+zZkQuorhriRyjzjmModeZvQxCjRSYmRNWrc8ugQgxwAaH6ip1ahun1Q==";
            $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
            openssl_private_decrypt(base64_decode($cryptText), $plain_text, $private_key);
            return $plain_text;
        }
        
        date_default_timezone_set('Asia/Dhaka');
        
        $MerchantID = "689580971105399";
        $DateTime = Date('YmdHis');
        $amount = "2";
        $OrderId = 'TEST'.strtotime("now").rand(1000, 10000);
        $random = generateRandomString();    
        
        $PostURL = "https://api.mynagad.com/api/dfs/check-out/initialize/" . $MerchantID . "/" . $OrderId;
        
        
        $merchantCallbackURL = "https://alesharideshare.aleshagroup.com/payment/callback";
        
        $SensitiveData = array(
            'merchantId' => $MerchantID,
            'datetime' => $DateTime,
            'orderId' => $OrderId,
            'challenge' => $random
        );
        
        $PostData = array(
            'accountNumber' => '01958097110', //Replace with Merchant Number
            'dateTime' => $DateTime,
            'sensitiveData' => EncryptDataWithPublicKey(json_encode($SensitiveData)),
            'signature' => SignatureGenerate(json_encode($SensitiveData))
        );
        
        $Result_Data = HttpPostMethod($PostURL, $PostData);
        
        if (isset($Result_Data['sensitiveData']) && isset($Result_Data['signature'])) {
            if ($Result_Data['sensitiveData'] != "" && $Result_Data['signature'] != "") {
        
                $PlainResponse = json_decode(DecryptDataWithPrivateKey($Result_Data['sensitiveData']), true);
        
                if (isset($PlainResponse['paymentReferenceId']) && isset($PlainResponse['challenge'])) {
        
                    $paymentReferenceId = $PlainResponse['paymentReferenceId'];
                    $randomServer = $PlainResponse['challenge'];
        
                    $SensitiveDataOrder = array(
                        'merchantId' => $MerchantID,
                        'orderId' => $OrderId,
                        'currencyCode' => '050',
                        'amount' => $amount,
                        'challenge' => $randomServer
                    );
        
                    $merchantAdditionalInfo = '{"Service Name": "Sheba.xyz"}';
        
                    $PostDataOrder = array(
                        'sensitiveData' => EncryptDataWithPublicKey(json_encode($SensitiveDataOrder)),
                        'signature' => SignatureGenerate(json_encode($SensitiveDataOrder)),
                        'merchantCallbackURL' => $merchantCallbackURL,
                        'additionalMerchantInfo' => json_decode($merchantAdditionalInfo)
                    );
        
                    
                    $OrderSubmitUrl = "https://api.mynagad.com/api/dfs/check-out/complete/" . $paymentReferenceId;
                    $Result_Data_Order = HttpPostMethod($OrderSubmitUrl, $PostDataOrder);
        
                    // echo json_encode($Result_Data_Order);
                    
                        if ($Result_Data_Order['status'] == "Success") {
                            $url = json_encode($Result_Data_Order['callBackUrl']);   
                            echo "<script>window.open($url, '_self')</script>";                      
                        }
                        else {
                            echo json_encode($Result_Data_Order);
                        }
                } else {
                    echo json_encode($PlainResponse);
                }
            }
        }



	}

    
	public function callback(){
	    function HttpGet($url)
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
            echo curl_error($ch);
            curl_close($ch);
            return $file_contents;
        }
        
        $Query_String  = explode("&", explode("?", $_SERVER['REQUEST_URI'])[1] );
        $payment_ref_id = substr($Query_String[2], 15); 
        $url = "https://api.mynagad.com/api/dfs/verify/payment/".$payment_ref_id;
        $json = HttpGet($url);
        $arr = json_decode($json, true);
        
        echo json_encode($arr); 
	}
    
    
    
    
    
}