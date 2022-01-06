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

use App\Models\User;
use App\Models\Rating;
use Yajra\DataTables\Services\DataTable;
use DB;

class DriverRemarksViewDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $i=count((array)$query);
                
        return datatables()
            ->of($query)
            ->addColumn('conversation_by', function ($users) {
                if($users->employee_name !='') return $users->employee_name.' ('.$users->hub_mobile.')'; 
                else if($users->admin_name !='') return $users->admin_name.' ('.$users->admin_mobile.')';                   
                else if($users->company_name !='') return $users->company_name.' ('.$users->company_mobile.')'; 
            })           
            ->addColumn('remarks_status', function ($users) {
                if($users->remarks_status =='1') return "Completed";
                else return "Inprocessing";
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        DB::statement(DB::raw('set @rownum=0'));
        $users = DB::Table('driver_remarks')->select(
            'driver_remarks.id as id',
            'driver_remarks.conversation as conversation',
            'driver_remarks.remarks',
            'driver_remarks.conversation_date as conversation_date',
            'driver_remarks.followup_date as followup_date',
            'driver_remarks.remarks_status as remarks_status',
            
            'hub_employees.employee_name as employee_name', 
            'hub_employees.mobile_number as hub_mobile', 

            'admins.username as admin_name', 
            'admins.mobile_number as admin_mobile', 

            'companies.name as company_name', 
            'companies.mobile_number as company_mobile', 
            //DB::raw('@rownum  := @rownum  + 1 AS rownum')
            //DB::raw('count(*) as user_count')
        )
        ->where('driver_id', $this->driver_id)
        ->leftJoin('companies', function($join) {
            $join->on('driver_remarks.company_id', '=', 'companies.id');
        })
        ->leftJoin('hub_employees', function($join) {
            $join->on('driver_remarks.hub_employee_id', '=', 'hub_employees.id');
        })
        ->leftJoin('admins', function($join) {
            $join->on('driver_remarks.admin_id', '=', 'admins.id');
        });
        //->orderBy('driver_remarks.id', 'DESC');

        //If login user is company then get that company drivers only
        if (LOGIN_USER_TYPE=='company') {
            $users = $users->where('company_id',auth()->guard('company')->user()->id);
        }

        return $users;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->addIndex()
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
            ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ['data' => 'conversation', 'name' => 'conversation', 'title' => 'Conversation'],
            ['data' => 'remarks', 'name' => 'driver_remarks.remarks', 'title' => 'Remarks'],
            
            ['data' => 'remarks_status', 'name' => 'remarks_status', 'title' => 'Remarks Status'],

            ['data' => 'conversation_date', 'name' => 'conversation_date', 'title' => 'Conversation Date'],
            ['data' => 'followup_date', 'name' => 'followup_date', 'title' => 'Followup Date'],

            ['data' => 'conversation_by', 'name' => 'conversation_by', 'title' => 'Conversation by'],
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
        return 'view_drivers_remarks_' . date('YmdHis');
    }
    

}
