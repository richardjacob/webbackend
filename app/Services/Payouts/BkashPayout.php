<?php

/**
 * Paypal Payout Service
 *
 * @package     Gofer
 * @subpackage  Services\Payouts
 * @category    Paypal
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
*/

namespace App\Services\Payouts;

use App\Models\Country;

class BkashPayout
{
    /**
     * validate Request
     *
     * @param Request $[request]
     * @return Mixed
     */
    public function validateRequest($request)
    {
        $rules = array(
            'account_number'=> 'required',
        );

        $messages = array(
            'required' => ':attribute '.trans('messages.home.field_is_required'),
        );
        $validator = \Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if(isApiRequest()) {
                return response()->json([
                    'status_code' => '0',
                    'status_message' => $validator->messages()->first(),
                ]);
            }
            return back()->withErrors($validator)->withInput();
        }
        return false;
    }

    public function createPayoutPreference($request)
    {
        $recipient['email'] = $request->account_number;
        $recipient['id'] = $request->account_number;
        return array(
            'status'            => true,
            'recipient'         => arrayToObject($recipient),
        );
    }

    public function makePayout($payout_account,$pay_data)
    {
        return array(
            'status' => true,
            'status_message' => 'Payout status updated successfully',
            //'transaction_id' => $pay_data['driver_transaction_id'],
            'transaction_id' => '',
        );
    }
}