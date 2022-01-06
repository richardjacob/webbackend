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
use App\DataTables\PayoutDriverBalanceDataTable;
use App\Models\Bonus;
use App\Models\DriverBalance;
use App\Models\PayoutCredentials;
use App\Models\CompanyPayoutCredentials;
use App\Models\BonusTransaction;
use DB;

class PayoutDriverBalanceController extends Controller
{
    public function view(PayoutDriverBalanceDataTable $dataTable)
    {
        $data['driver_balance'] = 'driver_balance';
        return $dataTable->render('admin.payouts.view', $data);
    }


    public function payout_to_driver(Request $request)
    {
    	$balance_id_array 	= explode(',', $request->balance_id);
        $total_balance = count($balance_id_array);

        $user_id        = $request->user_id;   
        $redirect_url   = $request->redirect_url;        
        $tr_no          = $request->tr_no;
        $amount         = $request->amount;
        $trip_currency  = view()->shared('default_currency'); 
        $trip_currency  = $trip_currency->code;


        $company_id = DB::table('users')
                            ->where('id', $user_id)
                            ->where('user_type', 'Driver')
                            ->pluck('company_id')
                            ->first();
                          
        if($company_id == '1'){
            $payout_details     = PayoutCredentials::where('user_id', $user_id)->where('default', 'yes')->first();  
        }
        else{
            $payout_details     = CompanyPayoutCredentials::where('company_id', $company_id)->where('default', 'yes')->first();  
        }
 
        
        if(is_object($payout_details)){
            $total_updated = 0;
            foreach($balance_id_array as $balance_id){
                $transaction_exist = BonusTransaction::where('driver_balance_id', $balance_id)->first();
                
                if($transaction_exist !=''){
                    // flashMessage('danger','Payout Failed : Transaction exists');
                    // return redirect($redirect_url);
                }
                else{
                    $driver_balance = DriverBalance::find($balance_id);
                    if(is_object($driver_balance)){
                        $bonus = Bonus::find($driver_balance->bonus_id);
                        if(is_object($bonus)){
                            $bonus_transactions = new BonusTransaction;
                            $bonus_transactions->driver_balance_id  = $balance_id;
                            $bonus_transactions->user_id            = $user_id;
                            $bonus_transactions->amount             = $driver_balance->amount;
                            $bonus_transactions->transaction_date   = date('Y-m-d');
                
                            if($payout_details->type == 'nagad' || $payout_details->type == 'bkash' || $payout_details->type == 'rocket') {
                                $bonus_transactions->transaction_id = $tr_no;
                                $bonus_transactions->payout_type    = $payout_details->type;
                                $bonus_transactions->payout_id      = $payout_details->payout_id;
                            }
            
                            if($bonus_transactions->save() !=''){                
                                
                                $driver_balance->status = 'paid';
                
                                if($driver_balance->save() !=''){
                                    $bonus = Bonus::findOrFail($driver_balance->bonus_id);
                                    $bonus->payment_status = '1';
                                    
                                    if($bonus->save() !=''){
                                        $total_updated++;
                                        // flashMessage('success', 'Transaction added successfully.');
                                        // return redirect($redirect_url);
                                    }
                                }                
                            }
                        }
                    }
                }  
            }    
            
            if($total_updated > 0){
                flashMessage('success', 'Transaction added successfully.');
                return redirect($redirect_url);
            }else{
                flashMessage('danger','Payout Failed : Transaction exists');
                return redirect($redirect_url);
            }
        }
        
	}
}