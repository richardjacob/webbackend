<?php

/**
 * Braintree Payment Service
 *
 * @package     Gofer
 * @subpackage  Services\Payments
 * @category    Braintree
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
*/

namespace App\Services\Payments;

use App\Contracts\PaymentInterface;

class BraintreePayment implements PaymentInterface
{
	/**
	 * Make Braintree Payment
	 *
	 * @param Array $payment_data [payment_data includes currency, amount]
	 * @param String $[nonce] [nonce get it from braintree gateway]
	 * @return Boolean
	 */
	public function makePayment($payment_data,$nonce)
	{
		$payment_currency = site_settings('payment_currency');
		$payment_amount = currencyConvert($payment_data['currency_code'],$payment_currency,$payment_data['amount']);
		try {
			$gateway = resolve('braintree');
			$result = $gateway->transaction()->sale([
				'amount' => $payment_amount,
				'paymentMethodNonce' => $nonce,
				'options' => [
					'submitForSettlement' => True
				]
			]);
		}
		catch (\Exception $e) {
			return arrayToObject([
				'status' => false,
				'status_message' => $e->getMessage(),
			]);
		}

		$return_data['status'] = $result->success;
		$return_data['is_two_step'] = false;
		if($result->success) {
			$return_data['transaction_id'] = $result->transaction->id;
		}
		else {
			$return_data['status_message'] = $result->message;
			logger($result->errors);
		}
		return arrayToObject($return_data);
	}
}