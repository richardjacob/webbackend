<?php
namespace App\Http\Controllers;
use App\Models\TempTransaction;
use App\Models\User;
use App\Models\DriverOweAmount;
use App\Models\DriverOweAmountPayment;
use App\Models\ReferralUser;
use App\Models\AppliedReferrals;
use DB;
use App\Http\Helper\InvoiceHelper;

class PayToAdmin extends Controller
{
    public function index($response_array)
    {
    	//print_r($response_array);
    	
        $request = (object) $response_array;
        $order_id = $request->orderId;
        $request_amount = $request->amount;
        $tr_id = $request->issuerPaymentRefNo; //nagad

        $transaction = DB::table('temp_transactions')->where('order_id', $order_id)->first();
        if(is_object($transaction)){
        	$user 	= User::find($transaction->driver_id);
	        
	        if($request->status == 'Success'){
	        	$tempTrtable = TempTransaction::find($transaction->id);
	            $tempTrtable->status = '1';
	            $tempTrtable->mb_tr_id = $tr_id;
	            $tempTrtable->updated_at = date('Y-m-d H:i:s');
	            $tempTrtable->save();
	            //if($tempTrtable->save()) echo "Updated ".$transaction->id;	        

		        $owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
		       
				if ($owe_amount && $owe_amount->amount > 0) {
					//applying referral amount start
					if ($transaction->applied_referral_amount == '1' OR $transaction->applied_referral_amount == '1.00') {

						$total_referral_amount = ReferralUser::where('user_id',$user->id)
		                        					->where('payment_status','Completed')
		                        					->where('pending_amount','>',0)
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
							
							InvoiceHelper::referralUpdate($user->id,$total_referral_amount,$user->currency->code);

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
					if ($owe_amount->amount < $request_amount) {
						$request_amount = $owe_amount->amount;
					}
					$amount = $request_amount;
					
					if($request_amount > 0) {
					    
						$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
						$total_owe_amount = $owe_amount->amount;
						$currency_code = $owe_amount->currency_code;
						$remaining_amount = $total_owe_amount - $amount;

						//owe amount
						$owe_amount->amount = $remaining_amount;
						$owe_amount->currency_code = $currency_code;
						$owe_amount->save();

						$payment = new DriverOweAmountPayment;
						$payment->user_id = $user->id;
						$payment->transaction_id = $tr_id;
						$payment->amount = $amount;
						$payment->status = 1;
						$payment->currency_code = $currency_code;
						$payment->created_at = date('Y-m-d H:i:s');
						$payment->updated_at = date('Y-m-d H:i:s');
						$payment->save();
						
		
						// $owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
					}

					// $referral_amount = ReferralUser::where('user_id',$user->id)
					// 							->where('payment_status','Completed')
					// 							->where('pending_amount','>',0)
					// 							->get();

					// $referral_amount = number_format($referral_amount->sum('pending_amount'), 2, '.', '');


		            // 			return response()->json([
		            // 				'status_code' 	=> '1',
		            // 				'status_message'=> __('messages.api.payout_successfully'),
		            // 				'referral_amount' => $referral_amount,
		            // 				'owe_amount' 	=> $owe_amount->amount,
		            // 				'currency_code' => $owe_amount->currency_code
		            // 			]);
				}
		        //echo "Success";
					
					echo "<input type=\"button\" value=\"Go Back\" onClick=\"showAndroidToast('Hello Android!')\" />
		            <script type=\"text/javascript\">
		                function showAndroidToast(toast) {
		                    Android.showToast(toast);
		                }
		            </script>";
	        }
	        else echo "Transaction is not completed.";
	    
    	}else{
    		echo "Transaction ID (".$order_id.") is not found.";
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

    public function callback(){
        /*$Query_String  = explode("&", explode("?", $_SERVER['REQUEST_URI'])[1] );
        $payment_ref_id = substr($Query_String[2], 15); 
        $url = "https://api.mynagad.com/api/dfs/verify/payment/".$payment_ref_id;
        $json = self::HttpGet($url);
        $arr = json_decode($json, true);


        $response_array = json_decode(json_encode($arr), true);*/
        $response_array = array(
		    'merchantId' => '689580971105399',
		    'orderId' => 'ALESHA16182003129732',
		    'paymentRefId' => 'MDQxMjEwMDUxMzI4Mi42ODk1ODA5NzExMDUzOTkuQUxFU0hBMTYxODIwMDMxMjk3MzIuMDc0OTlkNGI2NzM3NTRkNzUyNjc=',
		    'amount' => '1',
		    'clientMobileNo' => '017****8051',
		    'merchantMobileNo' => '01958097110',
		    'orderDateTime' => '2021-04-12 10:05:12.0',
		    'issuerPaymentDateTime' => '2021-04-12 10:07:05.0',
		    'issuerPaymentRefNo' => '70PPJTPU',
		    'additionalMerchantInfo' => '{"Service Name":"alesharide.com"}',
		    'status' => 'Success',
		    'statusCode' => '000',
		    'cancelIssuerDateTime' => '',
		    'cancelIssuerRefNo' => ''
		);
        self::index($response_array);
    }

    // public function response($json)
    // {
    //     $response_array = json_decode($json, true);
    //     //print_r($response_array);
    //     self::index($response_array);
        
    // }

    

}
