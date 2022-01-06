<?php

/**
 * Driver DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Driver
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Services\DataTable;
use App\Models\Trips;
use DB;

use App\Models\User;
use Session;

class CompanyDriverListDataTable extends DataTable
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
            ->addColumn('name', function($row){
                return $row->first_name.' '.$row->last_name;
            })
           ->addColumn('created_at', function ($user) {
                return date("d M Y", strtotime($user->created_at)); 
            })
            ->addIndexColumn();           
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        //$id = session('id');

        //if($this->driver_id !='') $users = $users->where('users.id', $this->driver_id);

        $driver = $model->select('id', 'first_name', 'last_name', 'email', 'country_code', 'gender', 'mobile_number', 'password', 'user_type', 'company_id', 'remember_token', 'firebase_token', 'fb_id', 'google_id', 'apple_id', 'status', 'device_type', 'device_id', 'referral_code', 'used_referral_code', 'currency_code', 'language', 'country_id', 'nid_number', 'hub_id', 'hub_employee_id', 'driving_licence_number', 'created_at', 'updated_at', 'deleted_at'); //,  'paid'
        
        if($this->id !='') $driver = $driver->where('company_id', $this->id);
        if($this->driver_id !='') $driver = $driver->where('id', $this->driver_id);
        else{
            if($this->start_date !='')  {
                list($d, $m, $y) = explode('-', $this->start_date);
                $date1 =  $y.'-'.$m.'-'.$d;
                $driver = $driver->where('created_at', '>=', $date1);
            }
            if($this->end_date !='')  {
                list($d, $m, $y) = explode('-', $this->end_date);
                $date2 =  $y.'-'.$m.'-'.$d.' 23:59:59';
                $driver = $driver->where('created_at', '<=', $date2);
            }
        }

        $driver = $driver->where('user_type', 'Driver');
        
        return $driver;
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
                    ->orderBy(0)
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
            ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
            ['data' => 'mobile_number', 'name' => 'mobile_number', 'title' => 'Mobile Number'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'referral_code', 'name' => 'referral_code', 'title' => 'Referral Code'],
            ['data' => 'used_referral_code', 'name' => 'used_referral_code', 'title' => 'Used Referral Code'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created'],
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
        return 'owe_trip_list_' . date('YmdHis');
    }
    

}
