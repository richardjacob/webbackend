<?php

namespace App\Http\Controllers\PaymentApi;

use App\Http\Controllers\Controller;
use App\Models\TempTransaction;
use App\Models\User;
use App\Models\DriverOweAmount;
use App\Models\DriverOweAmountPayment;
use App\Models\ReferralUser;
use App\Models\AppliedReferrals;
use App\Models\Company;
use App\Models\Trips;
use App\Models\PaymentMethod;
use App\Models\PoolTrip;
use App\Models\Payment;
use DB;
use App\Http\Helper\InvoiceHelper;
use App\Http\Helper\RequestHelper;

class PayToAdmin extends Controller
{
	protected $invoice_helper, $request_helper;

	public function __construct()
	{
		$this->invoice_helper = resolve('App\Http\Helper\InvoiceHelper');
		$this->request_helper = resolve('App\Http\Helper\RequestHelper');
		$this->bonus_helper = resolve('App\Http\Helper\BonusHelper');
		$this->invoice = resolve("App\Http\Controllers\Invoice");
	}

	public function index($response_array, $payment_gateway_name)
	{
		$request = (object) $response_array;

		//nagad
		$order_id = $request->orderId;
		$request_amount = $request->amount;
		$tr_id = $request->issuerPaymentRefNo;
		$payeer_number = $request->clientMobileNo;
		$payment_gateway_number = $request->merchantMobileNo;
		$transaction_time = $request->issuerPaymentDateTime;

		$transaction = DB::table('temp_transactions')->where('order_id', $order_id)->first();

		if (is_object($transaction)) {
			if (strpos($transaction->user_type, 'company') !== false) {
				$user = Company::find($transaction->user_id);
			} else $user = User::find($transaction->user_id);

			$tempTrtable = TempTransaction::find($transaction->id);
			$redirect_url = str_replace(array('_', '-'), array('.', '/'),  $tempTrtable->redirect_url);

			if ($request->status == 'Success') {
				$tempTrtable->status = '1';
				$tempTrtable->mb_tr_id = $tr_id;
				$tempTrtable->payeer_number = $payeer_number;
				$tempTrtable->payment_gateway_name = $payment_gateway_name;
				$tempTrtable->payment_gateway_number = $payment_gateway_number;
				$tempTrtable->transaction_time = $transaction_time;
				$tempTrtable->updated_at = date('Y-m-d H:i:s');
				$tempTrtable->save();


				if ($transaction->payment_type == 'driver_owe_amount') {
					$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();

					if ($owe_amount && $owe_amount->amount > 0) {
						//applying referral amount start
						if ($transaction->applied_referral_amount == '1' or $transaction->applied_referral_amount == '1.00') {

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

								InvoiceHelper::referralUpdate($user->id, $total_referral_amount, $user->currency->code);

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

						if ($request_amount > 0) {

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
				} // if($transaction->payment_type

				else if ($transaction->payment_type == 'rider_fare') {
					//trip_id, nonce, amount

					$trip_id = $transaction->trip_id;
					$user_details = User::where('id', $transaction->user_id)->first();

					$payment_methods = collect(PAYMENT_METHODS);
					$payment_types = $payment_methods->pluck('key')->implode(',');

					$user = User::where('id', $user_details->id)->first();

					if ($user == '') {
						echo __('messages.invalid_credentials');
					}

					$trip = Trips::find($trip_id);
					$rider = User::find($trip->user_id);

					// should on
					// if($trip->status != 'Payment') {
					// 	echo __('messages.api.something_went_wrong');
					// }

					//not calculation
					if ($trip->is_calculation == 0) {
						$data = [
							'trip_id' => $trip_id,
							'user_id' => $user_details->id,
							'save_to_trip_table' => 1,
						];

						//$trip = $this->invoice_helper->calculation($data);

						$trip = $this->invoice_helper->calculation_v2($data);
					}


					//if($trip->total_fare > 0) {
					$trip = Trips::where('id', $trip_id)->first();
					$trip->status = 'Completed';
					$trip->paykey = $transaction->id;
					$trip->payment_status = 'Completed';
					$trip->save();
					//}

					if ($trip->pool_id > 0) {
						$pool_trip = PoolTrip::with('trips')->find($trip->pool_id);
						$trips = $pool_trip->trips->whereIn('status', ['Scheduled', 'Begin trip', 'End trip', 'Rating', 'Payment'])->count();

						if (!$trips) {
							// update status
							$pool_trip->status = 'Completed';
							$pool_trip->save();
						}
					}

					$data = [
						'trip_id' => $trip->id,
						'correlation_id' => $transaction->id,
						'driver_payout_status' => ($trip->driver_payout) ? 'Pending' : 'Completed',
						'payout_amount' => $trip->driver_payout,
						'driver_id' => $trip->driver_id,
					];

					Payment::updateOrCreate(['trip_id' => $trip->id], $data);

					$driver = User::where('id', $trip->driver_id)->first();

					$push_title = "Payment Completed";

					$push_data['push_title'] = $push_title;
					$push_data['data'] = array(
						'trip_payment' => array(
							'status' => 'Paid',
							'trip_id' => $trip->id,
							'rider_thumb_image' => $trip->rider_profile_picture
						)
					);

					$this->request_helper->SendPushNotification($driver, $push_data);

					// echo response()->json([
					// 	'status_code' 		=> '1',
					// 	'status_message' 	=> "Paid Successfully",
					// 	'currency_code' 	=> $trip->currency_code ?? '',
					// 	'total_time' 		=> $trip->total_time ?? '0.00',
					// 	'total_km' 			=> $trip->total_km ?? '0.00',
					// 	'total_time_fare' 	=> $trip->time_fare ?? '0.00',
					// 	'total_km_fare' 	=> $trip->distance_fare ?? '0.00',
					// 	'base_fare' 		=> $trip->base_fare ?? '0.00',
					// 	'total_fare' 		=> $trip->total_fare ?? '0.00',
					// 	'access_fee' 		=> $trip->access_fee ?? '0.00',
					// 	'pickup_location' 	=> $trip->pickup_location ?? '',
					// 	'drop_location' 	=> $trip->drop_location ?? '',
					// 	'driver_payout' 	=> $trip->driver_payout ?? '0.00',
					// 	'trip_status'		=> $trip->status,
					// 	'driver_thumb_image'=> $driver->profile_picture->src ?? url('images/user.jpeg'),
					// ]);
					$this->invoice->invoice_email(base64_encode($trip->user_id), base64_encode($trip->id));
					$this->bonus_helper->discount_offer_save($trip, $rider);
					$this->bonus_helper->update_bonus($user, "DriverJoiningBonus", $trip);
					$this->bonus_helper->update_bonus($user, "DriverReferralBonus", $trip);
					$this->bonus_helper->adjust_driver_trip_bonus($user);
				}

				//$this->bonus_helper->adjust_driver_signup_bonus($user);

				//	$this->bonus_helper->adjust_driver_referral_bonus($user);

				if ($redirect_url != '') {
					$redirect_url = '//' . $redirect_url;
					echo '<br /><br /><br />
						<center><h1>Transaction completed successfully.</h1></center>
						<script>
                            setTimeout(function () {
                                window.location.href = "' . $redirect_url . '";
                            }, 4000);                            
                        </script>
                    ';
				} else {
					echo " <!DOCTYPE html>
						<html>
						<head>
						</head>
						<body onLoad=\"showAndroidToast('Payment Successful')\">Payment Successful
						<script type='text/javascript'>
							function showAndroidToast(toast) {
								Android.showToast(toast);
							}
						</script>
						</body>
						</html> ";
				}
			} else {
				if ($redirect_url != '') {
					$redirect_url = '//' . $redirect_url;
					echo '<br /><br /><br />
						<center><h1>Transaction is not completed</h1></center>
						<script>
                            setTimeout(function () {
                                window.location.href = "' . $redirect_url . '";
                            }, 4000);                            
                        </script>
                    ';
				} else {
					echo " <!DOCTYPE html>
						<html>
						<head>
						</head>
						<body onLoad=\"showAndroidToast('Transaction is not completed.')\">Transaction is not completed.
						<script type='text/javascript'>
							function showAndroidToast(toast) {
								Android.showToast(toast);
							}
						</script>
						</body>
						</html> ";
				}
			}
		} else {
			echo "Transaction ID (" . $order_id . ") is not found.";
		}
	}
}
