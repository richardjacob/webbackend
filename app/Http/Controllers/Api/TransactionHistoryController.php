<?php

/**
 * Trip Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Trip
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BonusTransaction;
use App\Models\DriverOweAmountPayment as ModelsDriverOweAmountPayment;
use JWTAuth;
use DB;


class TransactionHistoryController extends Controller
{

	public function index(Request $r)
	{
		$user_details = JWTAuth::parseToken()->authenticate();
		$user = User::where('id', $user_details->id)->first();

		if ($user == '') {
			return response()->json([
				'status_code'		=> '0',
				'status_message'	=> trans('messages.invalid_credentials'),
			]);
		}

		$transactionType = $r->transaction_type;
		$received_type = $r->received_type;
		$page = $r->page ?? 1;

		$take = 15;
		$skip = ($take * $page) - $take;

		$array = array();

		$common_array = array(
			'status_code' => '1',
			'status_message' => trans('messages.api.transaction_history'),
			'tab' => array(
				array(
					'key' => 'paid',
					'value' => trans('messages.api.paid_to_alesha'),
				),
				array(
					'key' => 'received',
					'value' => trans('messages.api.received_from_alesha'),
				)
			),
		);


		if ($transactionType == '' or $transactionType == 'paid') {
			///paid data
			$paid_history = ModelsDriverOweAmountPayment::where('user_id', $user->id)
				->skip($skip)
				->take($take)
				->orderBy('id', 'DESC')
				->get();
			if ($paid_history) {
				foreach ($paid_history as $single_paid_history) {
					$array[] = array(
						'Transaction Id' => $single_paid_history->transaction_id,
						'Amount' => $single_paid_history->amount,
						'Date' => date("d-m-Y", strtotime($single_paid_history->created_at))
					);
				}
			} else {
				$array[] = array(
					'status_message'         => "No Transaction History Found",
				);
			}
		} else if ($transactionType == 'received') {
			$common_array = array_merge(
				$common_array,
				array(
					'received_menu' => array(
						array(
							'key' => 'balance',
							'value' => trans('messages.api.balance_withdraw'),
						),
						array(
							'key' => 'payout',
							'value' => trans('messages.api.payout'),
						)
					)
				)
			);

			if ($received_type == 'balance') {
				$bonuses = BonusTransaction::where('user_id', $user->id)
					->skip($skip)
					->take($take)
					->orderBy('id', 'DESC')
					->get();

				if ($bonuses) {
					foreach ($bonuses as $bonus) {
						$array[] = array(
							'Transaction Id' => $bonus->transaction_id,
							'Amount' => $bonus->amount,
							'Date' => date("d-m-Y", strtotime($bonus->transaction_date))
						);
					}
				} else {
					$array[] = array(
						'status_message'         => "No Transaction History Found",
					);
				}
			} else if ($received_type == 'payout') { //online

				//Previous by nishat start
				// $transaction_history = DB::table('payment')
				// 	->where('driver_id', 10681)
				// 	->where('driver_payout_status', 'paid')
				// 	->select('driver_transaction_id as Transaction Id', 'payout_amount as Amount', 'payment_date as Date')
				// 	->get();

				// $transaction_history_to_array = json_decode(json_encode($transaction_history), true);
				// $store_transaction_history_by_transaction_date = array();
				// for ($i = 0; $i < count($transaction_history_to_array); $i++) {
				// 	$index = -1;
				// 	for ($j = 0; $j < count($store_transaction_history_by_transaction_date); $j++) {
				// 		if ($transaction_history_to_array[$i]['Transaction Id'] == $store_transaction_history_by_transaction_date[$j]['Transaction Id']) {
				// 			$index = $j;
				// 			break;
				// 		}
				// 	}
				// 	if ($index == -1) {
				// 		array_push($store_transaction_history_by_transaction_date, $transaction_history_to_array[$i]);
				// 	} else {
				// 		$store_transaction_history_by_transaction_date[$index]['Amount'] += $transaction_history_to_array[$i]['Amount'];
				// 	}
				// }

				// if (count($store_transaction_history_by_transaction_date) != 0) {
				// 	$array[] = $store_transaction_history_by_transaction_date;

				// } else {
				// 	$array[] = array(
				// 		'status_code'             => "0",
				// 		'status_message'         => "No Transaction History Found",
				// 	);
				// }
				//Previous by nishat end


				//Now by nishat start
				$payout_transaction_history = DB::table('payout_transaction_history')->where('driver_id', $user->id)
					->skip($skip)
					->take($take)
					->orderBy('id', 'DESC')
					->get();
				if ($payout_transaction_history) {
					foreach ($payout_transaction_history as $single_payout_transaction_history) {
						$array[] = array(
							'Transaction Id' => $single_payout_transaction_history->transaction_id,
							'Amount' => $single_payout_transaction_history->amount,
							'Date' => date("d-m-Y", strtotime($single_payout_transaction_history->transaction_date))
						);
					}
				} else {
					$array[] = array(
						'status_message'         => "No Transaction History Found",
					);
				}



				//Now by nishat end
			}
		}



		if (isset($array)) {
			$final_array = array_merge($common_array, array('result' => $array));
		} else $final_array = $common_array;

		return response()->json($final_array);
	}
}
