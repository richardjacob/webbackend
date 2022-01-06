<?php

/**
 * Paypal Payment Service
 *
 * @package     Gofer
 * @subpackage  Services\Payments
 * @category    Paypal
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
*/

namespace App\Services\Payments;

use App\Contracts\PaymentInterface;

class PaypalPayment implements PaymentInterface
{
	/**
	 * Make Paypal Payment
	 *
	 * @param Array $payment_data [payment_data includes currency, amount]
	 * @param String $[nonce] [nonce get it from Braintree gateway]
	 * @return Boolean
	 */
	public function makePayment($payment_data,$nonce)
	{
		$payment_currency = site_settings('payment_currency');
		$payment_amount = currencyConvert($payment_data['currency_code'],$payment_currency,$payment_data['amount']);
		try {
			/*
			Old PayPal Payment Method
			$gateway = resolve('paypal');
			$purchase_response = $gateway->fetchPurchase(['transactionReference' => $pay_key])->send();
			$transaction_id = $purchase_response->getTransactionReference() ?: '';*/
			$gateway = resolve('braintree_paypal');
			$result = $gateway->transaction()->sale([
				'amount' => $payment_amount,
				'paymentMethodNonce' => $nonce,
				'options' => [
					'submitForSettlement' => True
				]
			]);
		}
		catch (\Exception $exception) {
			return arrayToObject([
				'status' => false,
				'status_message' => $exception->getMessage(),
			]);
		}

		$return_data['status'] = $result->success;
		$return_data['is_two_step'] = false;
		if($result->success) {
			$return_data['transaction_id'] = $result->transaction->id;
		}
		else {
			$return_data['status_message'] = $result->message;
		}
		return arrayToObject($return_data);
	}
}