<?php

/**
 * Trips Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Trips
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\PayoutCompanyBalanceDataTable;
use App\Models\Bonus;
use App\Models\DriverBalance;
use App\Models\PayoutCredentials;
use App\Models\BonusTransaction;

class PayoutCompanyBalanceController extends Controller
{
    public function view(PayoutCompanyBalanceDataTable $dataTable)
    {
        return $dataTable->render('admin.payouts.company_view');
    }


    public function payout_to_driver(Request $request)
    {
        dd($request);
    	$balance_id 	= $request->balance_id;
        $user_id        = $request->user_id;   
        $redirect_url   = $request->redirect_url;        
        $tr_no          = $request->tr_no;
        $amount         = $request->amount;
        $trip_currency  = view()->shared('default_currency'); 
        $trip_currency  = $trip_currency->code;
        
        $payout_details     = PayoutCredentials::where('user_id', $user_id)
                                                    ->where('default', 'yes')
                                                    ->first();        

        $transaction_exist = BonusTransaction::where('driver_balance_id', $balance_id)
                                                ->first();

        if($transaction_exist !=''){
            flashMessage('danger','Payout Failed : Transaction exists');
            return redirect($redirect_url);
        }
        else{
            $bonus_transactions = new BonusTransaction;
            $bonus_transactions->driver_balance_id  = $balance_id;
            $bonus_transactions->user_id            = $user_id;
            $bonus_transactions->amount             = $amount;
            $bonus_transactions->transaction_date   = date('Y-m-d');

            if($payout_details->type == 'nagad' || $payout_details->type == 'bkash' || $payout_details->type == 'rocket') {
                $bonus_transactions->transaction_id = $tr_no;
                $bonus_transactions->payout_type    = $payout_details->type;
                $bonus_transactions->payout_id      = $payout_details->payout_id;
            }

            if($bonus_transactions->save() !=''){

                $driver_balance = DriverBalance::findOrFail($balance_id);
                $driver_balance->status = 'paid';

                if($driver_balance->save() !=''){
                    $bonus = Bonus::findOrFail($driver_balance->bonus_id);
                    $bonus->payment_status = '1';
                    
                    if($bonus->save() !=''){
                        flashMessage('success', 'Transaction added successfully.');
                        return redirect($redirect_url);
                    }
                }                
            }
        }        
	}
}