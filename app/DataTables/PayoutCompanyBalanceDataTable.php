<?php

/**
 * Payouts DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Payouts
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Trips;
use App\Models\PayoutCredentials;
use App\Models\PayoutPreference;

use App\Models\CompanyPayoutCredentials;
use App\Models\CompanyPayoutPreference;

use App\Models\DriverBalance;
use Yajra\DataTables\Services\DataTable;
use DB;

class PayoutCompanyBalanceDataTable extends DataTable
{

    public function dataTable($query)
        {
            return datatables()
                ->of($query)
                ->addColumn('request_date', function ($driver_balance) {
                    if($driver_balance->request_date !=''  AND $driver_balance->request_date != '0000-00-00 00:00:00'){                                             
                     return date("d M Y", strtotime($driver_balance->request_date));
                    }                
                })
                ->addColumn('payout_method', function ($driver_balance) {
                    if($driver_balance->company_id == '1'){
                        $payout_method = PayoutCredentials::where('user_id', $driver_balance->user_id)
                                                        ->where('default','yes')
                                                        //->pluck('type','payout_id')
                                                        ->first();
                    }
                    else{
                        $payout_method = CompanyPayoutCredentials::where('company_id', $driver_balance->company_id)
                                                                ->where('default','yes')
                                                                ->first();
                    }
                    if(is_object($payout_method)){
                        return "<img src='".url('images/icon/'.$payout_method->type.'.png')."' height='20'> ".ucwords($payout_method->type). ', A/c '.$payout_method->payout_id;
                    }
                
                })                
                ->addColumn('action', function ($driver_balance) {
                            $payout_credentials = CompanyPayoutCredentials::where('company_id', $driver_balance->company_id)
                                                                            ->where('default','yes')
                                                                            ->first();
                            $payout_method = $payout_credentials;


                        $payout_text = 'Make Payout';
                        $payout_data['has_payout_data'] = true;
                        $payout_method_data = '';
                        
                        if(is_object($payout_method)){
                            $preference = CompanyPayoutPreference::where('id', $payout_method->preference_id)->first();
                                         
                            $payout_method_data="<div class=\'row\'>";
                            $payout_method_data.="<div class=\'col-sm-6\'>Method :</div>";
                            $payout_method_data.="<div class=\'col-sm-6\'>";
                            $payout_method_data.="<img src=\'".url('images/icon/'.$payout_method->type.'.png')."\' height=\'20\'> ".ucwords($payout_method->type)."</div>";
                            $payout_method_data.= "</div>";

                            $payout_method_data.="<div class=\'row\'>";
                            $payout_method_data.="<div class=\'col-sm-6\'>Account Number :</div>";
                            $payout_method_data.="<div class=\'col-sm-6\'>".ucwords($payout_method->payout_id)."</div>";
                            $payout_method_data.= "</div>";

                            if($preference->payout_method =='banktransfer'){
                                $payout_method_data.="<div class=\'row\'>";
                                $payout_method_data.="<div class=\'col-sm-6\'>Account Name :</div>";
                                $payout_method_data.="<div class=\'col-sm-6\'>".@$preference->holder_name."</div>";
                                $payout_method_data.= "</div>";

                                $payout_method_data.="<div class=\'row\'>";
                                $payout_method_data.="<div class=\'col-sm-6\'>Account Type :</div>";
                                $payout_method_data.="<div class=\'col-sm-6\'>".@$preference->account_type."</div>";
                                $payout_method_data.= "</div>";

                                $payout_method_data.="<div class=\'row\'>";
                                $payout_method_data.="<div class=\'col-sm-6\'>Bank Name :</div>";
                                $payout_method_data.="<div class=\'col-sm-6\'>".@$preference->bank_name."</div>";
                                $payout_method_data.= "</div>";

                                $payout_method_data.="<div class=\'row\'>";
                                $payout_method_data.="<div class=\'col-sm-6\'>Branch :</div>";
                                $payout_method_data.="<div class=\'col-sm-6\'>".@$preference->branch_name."</div>";
                                $payout_method_data.= "</div>";

                                $payout_method_data.="<div class=\'row\'>";
                                $payout_method_data.="<div class=\'col-sm-6\'>Bank Location :</div>";
                                $payout_method_data.="<div class=\'col-sm-6\'>".@$preference->bank_location."</div>";
                                $payout_method_data.= "</div>";

                                $payout_method_data.="<div class=\'row\'>";
                                $payout_method_data.="<div class=\'col-sm-6\'>Routing Number :</div>";
                                $payout_method_data.="<div class=\'col-sm-6\'>".@$preference->routing_number."</div>";
                                $payout_method_data.= "</div>";
                            }
                        }

                        if(!is_object($payout_credentials)) {
                            $payout_data['has_payout_data'] = false;
                            $payout_data['payout_message'] = "Yet, Driver doesnt enter his Payout details.";
                        }
                        else if($payout_credentials->type == 'banktransfer') {
                            $payout_data['Payout Method'] = 'Bank Transfer';
                            $payout_data['Account Type'] = @$preference->account_type;
                            $payout_data['Payout Account Number'] = $payout_credentials->payout_id;
                            $payout_data['Account Holder Name'] = @$payout_credentials->payout_preference->holder_name;
                            $payout_data['Bank Name'] = @$payout_credentials->payout_preference->bank_name;
                            $payout_data['Bank Location'] = @$payout_credentials->payout_preference->bank_location;
                            $payout_data['Branch'] = @$payout_credentials->payout_preference->branch_name;
                            $payout_data['Routing Number'] = @$payout_credentials->payout_preference->routing_number;
                            // if($payout_credentials->payout_preference->routing_number != '') $payout_data['Routing Number'] = $payout_credentials->payout_preference->routing_number;
                        }
                        else if($payout_credentials->type == 'Stripe') {
                            $payout_data['Payout Account Number'] = $payout_credentials->payout_id;
                        }
                        else if($payout_credentials->type == 'Paypal') {
                            $payout_data['Paypal Email'] = $payout_credentials->payout_id;
                        }
                        else{
                            $payout_data['Payout Method'] = ucwords(@$payout_method->type);
                            $payout_data['Account Number'] = @$payout_method->payout_id;
                        }

                        $driver_payout = '<a data-href="#" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#payout-details" data-payout_details=\''.json_encode($payout_data).'\'><i class="glyphicon glyphicon-list-alt"></i></a>&nbsp;';

                        //$account_number = DB::table('payout_preference')->where('user_id', $driver_balance->user_id)->pluck('account_number')->first();
                        $account_number = @$preference->account_number;
                        
                        $balances_id_array =  DB::Table('driver_balances')
                                                ->where('user_id' , $driver_balance->user_id)
                                                ->where('status', '!=', 'paid')
                                                ->get();

                        $balanceId = array();
                        $bonusId = array();
                        foreach($balances_id_array as $balance){
                            $balanceId[] = $balance->id;
                            $bonusId[] = $balance->bonus_id;
                        }
                        $balanceIdString = implode(',', $balanceId);
                        $bonusIdString = implode(',', $bonusId);
                                                

                        $payment_action='
                                <label class="btn btn-xs btn-primary"                                 
                                    onclick="make_bonus_payout_modal(\''.$account_number.'\', 
                                        \''.$balanceIdString.'\',
                                        \''.$bonusIdString.'\',
                                        \''.$driver_balance->user_id.'\',
                                        \''.$driver_balance->total.'\', 
                                        \''.$payout_method_data.'\',
                                        \''.LOGIN_USER_TYPE.'/payout/company_balance\', 
                                        \''.$payout_text.'\'
                                    )" > '.$payout_text.' </label>';  

                        return '<div>'.$driver_payout.' '.$payment_action.'</div>';
                    //}
                })                   
                ->filterColumn('driver_name', function ($query, $keyword) {
                    $keywords = trim($keyword);
                    $query->whereRaw("CONCAT(first_name, last_name) like ?", ["%{$keywords}%"]);
                })             
                ->rawcolumns(['action','payout_method'])
                ->addIndexColumn();
        }

    /**
     * Get query source of dataTable.
     *
     * @param Trips $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DriverBalance $model)
    {
        $driver_balance =  DB::Table('driver_balances')->select(
                'driver_balances.id as id',
                'driver_balances.bonus_id as bonus_id',
                'driver_balances.user_id as user_id',
                'driver_balances.amount',
                'driver_balances.status',
                'driver_balances.request_date',
                'driver_balances.created_at',
                'driver_balances.updated_at',
                'users.company_id',
                'companies.name AS company', 
                DB::raw("SUM(driver_balances.amount) as total"),              
                DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
            )
            ->leftJoin('users', function($join) {
                $join->on('driver_balances.user_id', '=', 'users.id');
            })
            ->leftJoin('companies', function($join) {
                $join->on('users.company_id', '=', 'companies.id');
            })
            ->where('driver_balances.status', '!=', 'paid')
            ->where('users.company_id', '!=', '1')
            ->groupBy('driver_balances.user_id');

            return $driver_balance;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->addAction()
                    ->minifiedAjax()
                    ->dom('lBfr<"table-responsive"t>ip')
                    ->parameters(['order' => [1,'DESC']])
                    ->buttons(['csv', 'excel', 'print', 'reset'] );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return array(
            ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false], 
            ['data' => 'user_id', 'name' => 'user_id', 'title' => 'Driver Id'],
            ['data' => 'driver_name', 'name' => 'driver_name', 'title' => 'Driver Name'],
            ['data' => 'company', 'name' => 'company', 'title' => 'Company'],
            ['data' => 'payout_method', 'name' => 'payout_method', 'title' => 'Payout Method & Account Number'],            
            ['data' => 'total', 'name' => 'total', 'title' => 'Payout Amount'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'request_date', 'name' => 'request_date', 'title' => 'Request Date'],
        );
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Payout_Driver_Balance_' . date('YmdHis');
    }
}