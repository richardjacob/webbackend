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

namespace App\DataTables\DriverOffer;

use App\Models\Bonus;
use App\Models\Trips;
use Yajra\DataTables\Services\DataTable;
use DB;

class SigningBonusDataTable extends DataTable
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
            ->addColumn('completed_trips', function ($bonus) {
                return Trips::where('driver_id', $bonus->user_id)
                    ->where('status', 'Completed')
                    ->where('subtotal_fare', '>', '0')
                    ->count();
            })
            ->addColumn('name', function ($bonus) {
                return $bonus->first_name . ' ' . $bonus->last_name;
            })
            ->addColumn('created_at', function ($bonus) {
                return date("d M Y", strtotime($bonus->created_at));
            })
            ->addIndexColumn();
    }


    public function query(Bonus $model)
    {
        // return $model->all();
        return $model->where('bonuses.user_type', 'Driver')
            ->where('bonuses.bonus_type', 'DriverSignupBonus')
            ->join('users', function ($join) {
                // $join->on('users.id', '=', 'bonuses.user_id');
                $query = $join->on('users.id', '=', 'bonuses.user_id');
                if (LOGIN_USER_TYPE == 'company') {
                    $query = $query->where('users.company_id', auth()->guard('company')->user()->id);
                }
            })
            ->select(['bonuses.*', 'users.first_name as first_name', 'users.last_name as last_name']);
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
            //->addAction()
            ->minifiedAjax()
            ->dom('lBfr<"table-responsive"t>ip')
            ->orderBy(0, 'DESC')
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
            ['data' => 'user_id', 'name' => 'user_id', 'title' => 'User ID'],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
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
