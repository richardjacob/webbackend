<?php

namespace App\Http\Controllers\PaymentApi;
use App\Http\Controllers\Controller;
use App\Models\TempTransaction;
use App\Models\PaymentGateway;
use App\Models\Trips;
use DB;

use App\Http\Controllers\PaymentApi\NagadController;

class PaymentByWebController extends Controller
{
    public function __construct()
    {
        $this->invoice = resolve('App\Http\Controllers\Invoice');
    }
    public function index($payment_gateway, $payment_type, $user_id, $amount, $applied_referral_amount='', $redirect_url=''){
        //echo "payment_gateway, $payment_type, $user_id, $amount, $applied_referral_amount";

        //pre params $mobile_number,$amount,$applied_referral_amount=''
        
        //payment_type=driver_owe_amount/company_owe_amount/rider_fare 
        //payment_gateway = Nagad/bKash/Rocket etc, 

        date_default_timezone_set('Asia/Dhaka');

        //$paymentGateway = PaymentGateway::where('site', $payment_gateway)->get();        

        // $is_enabled = '';
        // $mode = '';
        // $merchant_id = '';
        // $account_number = '';
        // $public_key = '';
        // $private_key = '';
        $trip_id = '';

        // foreach ($paymentGateway as $row) {
        //     //nagad
        //     if($row->name == 'is_enabled') $is_enabled = $row->value;
        //     if($row->name == 'mode') $mode = $row->value;
        //     if($row->name == 'merchant_id') $merchant_id = $row->value;
        //     if($row->name == 'account_number') $account_number = $row->value;
        //     if($row->name == 'public_key') $public_key = $row->value;
        //     if($row->name == 'private_key') $private_key = $row->value;
        // }

        switch ($payment_type) {
            case 'driver_owe_amount': $user_type = 'driver'; break;
            case 'company_owe_amount': $user_type = 'company'; break;
            case 'rider_fare': $user_type = 'rider'; break;            
            default: $user_type = ''; break;
        }

        //echo $is_enabled.'<br>'.$mode.'<br>'.$merchant_id.'<br>'.$account_number.'<br>'.$public_key.'<br>'.$private_key;

        if($user_type == 'rider'){
            $trip_id = $user_id;
            $user_id = Trips::where('id', $user_id)->pluck('user_id')->first();
            //App\Http\Controllers\Invoice::invoice_email($user_id, $trip_id);
            $this->invoice->invoice_email($user_id, $trip_id);
        }
       
        $OrderId = 'ALESHA'.strtotime("now").rand(1000, 10000);

        $table = new TempTransaction;
        $table->payment_type            = $payment_type;
        $table->order_id                = $OrderId;
        $table->user_id                 = $user_id;
        $table->trip_id                 = $trip_id;
        $table->user_type               = $user_type;        
        $table->amount                  = $amount;
        $table->applied_referral_amount = $applied_referral_amount;       
        $table->redirect_url            = $redirect_url;
        $table->created_at              = date('Y-m-d H:i:s');
        $table->updated_at              = date('Y-m-d H:i:s');


        if($user_id !='' AND $user_type !=''){    
            if($table->save()){ 
                if($payment_gateway == 'nagad'){
                    NagadController::index($OrderId,$amount);//$mode,$merchant_id,$account_number,$public_key,$private_key,$OrderId,$amount
                }
                else if($payment_gateway == 'bkash'){
                    //BkashController::index($mode,$merchant_id,$account_number,$public_key,$private_key,$OrderId,$amount);
                }
                // bkash, Rocket ...

            }else echo "Not added";
        }else echo "User ID or trip id or user type did not matched.";
    }

    
}
