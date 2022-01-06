<?php

/**
 * Company Payouts DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Company Payouts
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Trips;
use Yajra\DataTables\Services\DataTable;
use DB;
use App\Http\Controllers\CustomLog;
use Illuminate\Support\Facades\Log;

class CompanyTransactionHistoryBalanceWithdrawDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->of($query)                             
            ->filterColumn('driver_name', function ($query, $keyword) {
                $keywords = trim($keyword);
                $query->whereRaw("CONCAT(users.first_name, users.last_name) like ?", ["%{$keywords}%"]);
            })                         
            ->filterColumn('payout_id', function ($query, $keyword) {
                $keywords = trim($keyword);
                $query->whereRaw("CONCAT('0', bonus_transactions.payout_id) like ?", ["%{$keywords}%"]);
            })
           ->rawcolumns(['created_at','driver_name'])           
           ->addIndexColumn();
    }


    public function query()
    {
       $list = DB::table('bonus_transactions')
                    ->select(
                        'bonus_transactions.id as id',
                        'bonus_transactions.driver_balance_id as driver_balance_id',
                        'bonus_transactions.user_id as user_id',
                        'bonus_transactions.amount as amount',
                        'bonus_transactions.transaction_id as transaction_id',
                        'bonus_transactions.payout_type as payout_type',

                        DB::raw("(
                            CASE
                            WHEN bonuses.bonus_type='DriverTripBonus' THEN 'Trip Bonus'
                            WHEN bonuses.bonus_type='DriverReferralBonus' THEN 'Driver Referral Bonus'
                            WHEN bonuses.bonus_type='Driver' THEN 'Driver Referral Bonus'
                            WHEN bonuses.bonus_type='DriverSignupBonus' THEN 'Driver Signup Bonus'
                            WHEN bonuses.bonus_type='DriverOnlineBonus' THEN 'Driver Online Bonus'
                            WHEN bonuses.bonus_type='Rider' THEN 'Rider Referral Bonus'
                            WHEN bonuses.bonus_type='RiderCashback1' THEN 'Rider First Cashback'
                            WHEN bonuses.bonus_type='RiderCashback2' THEN 'Rider Second Cashback'
                            WHEN bonuses.bonus_type='RiderDiscountOffer1' THEN 'Rider first Discount'
                            WHEN bonuses.bonus_type='DriverJoiningBonus' THEN 'Driver Joining Bonus'
                            ELSE bonuses.bonus_type 
                            END
                         ) AS bonus_type"),


                       //'bonuses.bonus_type as bonus_type',
                        DB::raw('CONCAT(\'0\', bonus_transactions.payout_id) AS payout_id'),
                        DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                        DB::raw('DATE_FORMAT(bonus_transactions.transaction_date, "%d-%b-%Y") as transaction_date'),
                        DB::raw('DATE_FORMAT(bonus_transactions.created_at, "%d-%b-%Y<br>%h:%i %p") as created_at'),
                        DB::raw('DATE_FORMAT(bonuses.bonus_date, "%d-%b-%Y") as bonus_date')
                    )
                    ->leftJoin('users', function($join) {
                        $join->on('bonus_transactions.user_id', '=', 'users.id');
                    })
                    ->leftJoin('companies', function($join) {
                        $join->on('users.company_id', '=', 'companies.id');
                    })

                    ->leftJoin('driver_balances', function($join) {
                        $join->on('bonus_transactions.driver_balance_id', '=', 'driver_balances.id');
                    })
                    ->leftJoin('bonuses', function($join) {
                        $join->on('driver_balances.bonus_id', '=', 'bonuses.id');
                    })

                    ->where('users.company_id', $this->company_id);
        
        if($this->driver_id !='') $list = $list->where('bonus_transactions.user_id', $this->driver_id);
        else{
            if($this->start_date !='')  {
                list($d, $m, $y) = explode('-', $this->start_date);
                $date1 =  $y.'-'.$m.'-'.$d;
                $list = $list->whereDate('bonus_transactions.created_at', '>=', $date1);
            }
            if($this->end_date !='')  {
                list($d, $m, $y) = explode('-', $this->end_date);
                $date2 =  $y.'-'.$m.'-'.$d;
                $list = $list->whereDate('bonus_transactions.created_at', '<=', $date2);
            }
        }

       return $list;
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
                    ->minifiedAjax()
                    ->dom('lBfr<"table-responsive"t>ip')                    
                    ->parameters(['order' => [1, 'DESC']])
                    ->buttons(
                        ['excel', 'print']
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false], 
            ['data' => 'id', 'name' => 'id', 'title' => 'Transaction ID'],
            ['data' => 'driver_balance_id', 'name' => 'driver_balance_id', 'title' => 'Balance ID'],
            ['data' => 'bonus_type', 'name' => 'bonus_type', 'title' => 'Bonus Type'],
            ['data' => 'bonus_date', 'name' => 'bonus_date', 'title' => 'Bonus Date'],
            ['data' => 'user_id', 'name' => 'user_id', 'title' => 'Driver ID'],
            ['data' => 'driver_name', 'name' => 'driver_name', 'title' => 'Driver Name'],
            ['data' => 'transaction_id', 'name' => 'transaction_id', 'title' => 'Reference ID'],
            ['data' => 'payout_type', 'name' => 'payout_type', 'title' => 'Payout Type'],
            ['data' => 'payout_id', 'name' => 'payout_id', 'title' => 'Payout ID'],
            ['data' => 'amount', 'name' => 'amount', 'title' => 'Amount'],
            ['data' => 'transaction_date', 'name' => 'transaction_date', 'title' => 'Transaction Date'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created at'],
        ];

        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'company_transaction_history_' . date('YmdHis');
    }
}