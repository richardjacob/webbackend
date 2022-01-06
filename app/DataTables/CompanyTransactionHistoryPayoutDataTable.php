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

class CompanyTransactionHistoryPayoutDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->of($query)                             
            // ->filterColumn('driver_name', function ($query, $keyword) {
            //     $keywords = trim($keyword);
            //     $query->whereRaw("CONCAT(users.first_name, users.last_name) like ?", ["%{$keywords}%"]);
            // })
           ->rawcolumns(['transaction_date'])           
           ->addIndexColumn();
    }


    public function query()
    {
       $list = DB::table('payout_transaction_history')
                    ->select(
                        'payout_transaction_history.id as id',
                        //'payout_transaction_history.driver_id as user_id',
                        'payout_transaction_history.transaction_id as transaction_id',
                        'payout_transaction_history.amount as amount',
                        //DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                        DB::raw('DATE_FORMAT(payout_transaction_history.transaction_date, "%d-%b-%Y<br>%h:%i %p") as transaction_date')
                    )
                    // ->leftJoin('users', function($join) {
                    //     $join->on('payout_transaction_history.driver_id', '=', 'users.id');
                    // })
                    // ->leftJoin('companies', function($join) {
                    //     $join->on('users.company_id', '=', 'companies.id');
                    // })
                    ->where('payout_transaction_history.company_id',$this->company_id);
        
        //if($this->driver_id !='') $list = $list->where('payout_transaction_history.driver_id', $this->driver_id);
        //else{
            if($this->start_date !='')  {
                list($d, $m, $y) = explode('-', $this->start_date);
                $date1 =  $y.'-'.$m.'-'.$d;
                $list = $list->whereDate('payout_transaction_history.transaction_date', '>=', $date1);
            }
            if($this->end_date !='')  {
                list($d, $m, $y) = explode('-', $this->end_date);
                $date2 =  $y.'-'.$m.'-'.$d;
                $list = $list->whereDate('payout_transaction_history.transaction_date', '<=', $date2);
            }
        //}

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
            //['data' => 'user_id', 'name' => 'user_id', 'title' => 'Driver ID'],
            //['data' => 'driver_name', 'name' => 'driver_name', 'title' => 'Driver Name'],
            ['data' => 'transaction_id', 'name' => 'transaction_id', 'title' => 'Reference ID'],
            ['data' => 'amount', 'name' => 'amount', 'title' => 'Amount'],
            ['data' => 'transaction_date', 'name' => 'transaction_date', 'title' => 'Transaction Date'],
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