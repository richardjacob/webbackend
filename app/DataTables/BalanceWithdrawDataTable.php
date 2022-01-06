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

class BalanceWithdrawDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->of($query)                             
            ->filterColumn('driver_name', function ($query, $keyword) {
                $keywords = trim($keyword);
                $query->whereRaw("CONCAT(users.first_name, users.last_name) like ?", ["%{$keywords}%"]);
            })
           ->rawcolumns(['created_at','driver_name'])           
           ->addIndexColumn();
    }


    public function query()
    {
       $list = DB::table('driver_owe_amount_payments')
                    ->select(
                        'driver_owe_amount_payments.id as id',
                        'driver_owe_amount_payments.user_id as user_id',
                        'driver_owe_amount_payments.transaction_id as transaction_id',
                        'driver_owe_amount_payments.amount as amount',
                        'users.first_name as first_name',
                        'users.last_name as last_name',
                        DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                        DB::raw('DATE_FORMAT(users.created_at, "%d-%b-%Y<br>%h:%i %p") as created_at')
                    )
                    ->leftJoin('users', function($join) {
                        $join->on('driver_owe_amount_payments.user_id', '=', 'users.id');
                    })
                    ->leftJoin('companies', function($join) {
                        $join->on('users.company_id', '=', 'companies.id');
                    })
                    ->where('users.company_id', auth()->guard('company')->user()->id);
        
        if($this->driver_id !='') $list = $list->where('driver_owe_amount_payments.user_id', $this->driver_id);
        else{
            if($this->start_date !='')  {
                list($d, $m, $y) = explode('-', $this->start_date);
                $date1 =  $y.'-'.$m.'-'.$d;
                $list = $list->whereDate('users.created_at', '>=', $date1);
            }
            if($this->end_date !='')  {
                list($d, $m, $y) = explode('-', $this->end_date);
                $date2 =  $y.'-'.$m.'-'.$d;
                $list = $list->whereDate('users.created_at', '<=', $date2);
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
            ['data' => 'user_id', 'name' => 'user_id', 'title' => 'Driver ID'],
            ['data' => 'driver_name', 'name' => 'driver_name', 'title' => 'Driver Name'],
            ['data' => 'transaction_id', 'name' => 'transaction_id', 'title' => 'Reference ID'],
            ['data' => 'amount', 'name' => 'amount', 'title' => 'Amount'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Date'],
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