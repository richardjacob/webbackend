<?php

/**
 * Country DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Country
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables\RiderOffer;

use App\Models\Bonus;
use App\Models\Trips;
use Yajra\DataTables\Services\DataTable;
use DB;

class CashBackDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->of($query)

            ->addColumn('bonus_type', function ($bonus) {
                //return $bonus->first_name.' '.$bonus->last_name;
                return preg_replace('/(?<!^)([A-Z])/', ' \\1', $bonus->bonus_type);
            })
            ->addColumn('user_name', function ($bonus) {
                return $bonus->first_name.' '.$bonus->last_name;
            })
            ->addColumn('created_at', function ($bonus) {
                return date("d M Y", strtotime($bonus->created_at)); 
            })
            ->addColumn('completed_trips', function ($bonus) {
                return Trips::where('user_id', $bonus->user_id)
                              ->where('status', 'Completed')
                              ->where('subtotal_fare', '>', '0')
                              ->count();

            })
            ->addIndexColumn();
    }

   
    public function query(Bonus $model)
    {
       // return $model->all();
       return $model->where('bonuses.user_type','Rider')
                    ->where(function($query){
                         $query->where('bonuses.bonus_type', 'RiderCashback1');
                         $query->orWhere('bonuses.bonus_type', 'RiderCashback2');
                     })
                    ->join('users', function($join) {
                        $join->on('users.id', '=', 'bonuses.user_id');
                    })
                    ->select([
                        'users.first_name as first_name', 
                        'users.last_name as last_name', 
                        'users.mobile_number as mobile_number', 
                        'bonuses.id as id', 
                        'bonuses.user_id as user_id', 
                        'bonuses.bonus_amount as bonus_amount', 
                        'bonuses.number_of_trips as number_of_trips',
                        'bonuses.status as status',
                        'bonuses.created_at as created_at',
                        'bonuses.bonus_type as bonus_type'
                    ]);
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
                    ->orderBy(0,'DESC')
                    ->buttons(
                        ['csv', 'excel', 'print', 'reset']
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
            ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ['data' => 'bonus_type', 'name' => 'bonus_type', 'title' => 'Bonus Type'],
            ['data' => 'user_id', 'name' => 'user_id', 'title' => 'User ID'],
            ['data' => 'user_name', 'name' => 'user_name', 'title' => 'Name'],
            ['data' => 'mobile_number', 'name' => 'mobile_number', 'title' => 'Mobile'],
            ['data' => 'bonus_amount', 'name' => 'bonus_amount', 'title' => 'Bonus Amount'],
            ['data' => 'number_of_trips', 'name' => 'bonus_amount', 'title' => 'Number of Trips'],
            ['data' => 'completed_trips', 'name' => 'completed_trips', 'title' => 'Completed Trips'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
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
        return 'signing_bonus_' . date('YmdHis');
    }
}