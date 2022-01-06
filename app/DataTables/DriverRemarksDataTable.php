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

class DriverRemarksDataTable extends DataTable
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
            ->addColumn('conversation_by', function ($users) {
                if($users->employee_name !='') return $users->employee_name.' ('.$users->hub_mobile.')'; 
                else if($users->admin_name !='') return $users->admin_name.' ('.$users->admin_mobile.')';                   
                else if($users->company_name !='') return $users->company_name.' ('.$users->company_mobile.')'; 
            })
            ->addColumn('status', function ($users) {
                return driver_status($users->driver_id);
            })
            ->addColumn('remarks_status', function ($users) {
                if($users->remarks_status =='1') return "Completed";
                else return "Inprocessing";
            })        
            ->addColumn('action', function ($users) {
                $add = (LOGIN_USER_TYPE=='company' || LOGIN_USER_TYPE=='hub' || auth('admin')->user()->can('add_drivers_remarks')) ? '<a href="'.url(LOGIN_USER_TYPE.'/add_drivers_remarks/'.$users->driver_id).'" class="btn btn-xs btn-success" style="margin-bottom:5px;"><i class="glyphicon glyphicon-plus"></i></a>&nbsp;' : '';

                $view = (LOGIN_USER_TYPE=='company' || LOGIN_USER_TYPE=='hub' || auth('admin')->user()->can('view_drivers_remarks')) ? '<a href="'.url(LOGIN_USER_TYPE.'/view_drivers_remarks/'.$users->driver_id).'" class="btn btn-xs btn-info" style="margin-bottom:5px;"><i class="fa fa-eye"></i></a>&nbsp;' : '';

                $edit = (LOGIN_USER_TYPE=='company' || LOGIN_USER_TYPE=='hub' || auth('admin')->user()->can('edit_drivers_remarks')) ? '<a href="'.url(LOGIN_USER_TYPE.'/edit_drivers_remarks/'.$users->id).'" class="btn btn-xs btn-primary" style="margin-bottom:5px;"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';

                // $delete ='<a data-href="'.url(LOGIN_USER_TYPE.'/delete_drivers_remarks/'.$users->id).'" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#confirm-delete" style="margin-bottom:5px;"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;';
                return $add.$view.$edit;
            })
            ->addColumn('driver_name', function ($users) {
                $add = (auth('admin')->user()->can('view_driver_profile')) ? '<a href="'.url(LOGIN_USER_TYPE.'/driver/profile/'.$users->driver_id).'" target="_blank">'.$users->driver_name.'</a>&nbsp;' : $users->driver_name;
                return $add;
            })
            ->filterColumn('driver_name', function ($query, $keyword) {
                $keywords = trim($keyword);
                $query->whereRaw("CONCAT(first_name, last_name) like ?", ["%{$keywords}%"]);
             })
            ->rawcolumns(['conversation_date', 'followup_date', 'action', 'driver_name'])
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
        $users = DB::Table('driver_remarks')->select(
            'driver_remarks.id as id',
            'driver_remarks.conversation as conversation',
            'driver_remarks.remarks',

            'driver_remarks.remarks_status as remarks_status',
            
            'hub_employees.employee_name as employee_name', 
            'hub_employees.mobile_number as hub_mobile', 

            'admins.username as admin_name',
            'admins.mobile_number as admin_mobile', 

            'companies.name as company_name', 
            'companies.mobile_number as company_mobile',            

            'users.id as driver_id',
            'users.status',  
            DB::raw('CONCAT(\'0\', users.mobile_number) AS mobile_number'), 
            DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'), 
            DB::raw('DATE_FORMAT(driver_remarks.conversation_date, "%d-%b-%Y<br>%h:%i %p") as conversation_date'),
            DB::raw('DATE_FORMAT(driver_remarks.followup_date, "%d-%b-%Y<br>%h:%i %p") as followup_date')
        )
        ->leftJoin('companies', function($join) {
            $join->on('driver_remarks.company_id', '=', 'companies.id');
        })
        ->leftJoin('hub_employees', function($join) {
            $join->on('driver_remarks.hub_employee_id', '=', 'hub_employees.id');
        })
        ->leftJoin('admins', function($join) {
            $join->on('driver_remarks.admin_id', '=', 'admins.id');
        })
        ->leftJoin('users', function($join) {
            $join->on('driver_remarks.driver_id', '=', 'users.id');
        })
        ->where('users.user_type', 'Driver')
        ->whereRaw('driver_remarks.id IN (select MAX(driver_remarks.id) FROM driver_remarks GROUP BY driver_remarks.driver_id)');
        
        

        if($this->driver_id !='') $users = $users->where('driver_remarks.driver_id', $this->driver_id);
        else{
            if($this->id !='')  $users = $users->where('driver_remarks.remarks_status', $this->id);
            else $users = $users->where('driver_remarks.remarks_status', '0');

            if($this->start_date !='')  {
                list($d, $m, $y) = explode('-', $this->start_date);
                $date1 =  $y.'-'.$m.'-'.$d;
                $users = $users->where('users.created_at', '>=', $date1);
            }
            if($this->end_date !='')  {
                list($d, $m, $y) = explode('-', $this->end_date);
                $date2 =  $y.'-'.$m.'-'.$d.' 23:59:59';
                $users = $users->where('users.created_at', '<=', $date2);
            }
        }

        //If login user is company then get that company drivers only
        if (LOGIN_USER_TYPE=='company') {
            $users = $users->where('company_id',auth()->guard('company')->user()->id);
        }
        // if (LOGIN_USER_TYPE=='hub') {
        //     $users = $users->where('driver_remarks.hub_employee_id',auth()->guard('hub')->user()->id);
        // }
        if (LOGIN_USER_TYPE=='hub') {
            $role_id = auth()->guard('hub')->user()->role_id;
            if($role_id == '4'){ // manager
                $users = $users->where('driver_remarks.hub_id',  auth()->guard('hub')->user()->hub_id);
            }
            else if($role_id == '5'){ // aquisition staff
                $users = $users->where('driver_remarks.hub_employee_id',auth()->guard('hub')->user()->id);
            }            
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
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('lBfr<"table-responsive"t>ip')
                    ->parameters(['order' => [1, 'DESC']])
                    ->buttons(
                        ['csv', 'excel', 'print']
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
            ['data' => 'driver_id', 'name' => 'driver_id', 'title' => 'Driver ID'],
            ['data' => 'driver_name', 'name' => 'driver_name', 'title' => 'Driver Name'],
            //['data' => 'last_name', 'name' => 'users.last_name', 'title' => 'Last Name'],
            
            ['data' => 'mobile_number', 'name' => 'users.mobile_number', 'title' => 'Mobile Number'],
            ['data' => 'status', 'name' => 'users.status', 'title' => 'Status'],

            ['data' => 'conversation', 'name' => 'conversation', 'title' => 'Conversation'],
            ['data' => 'remarks', 'name' => 'driver_remarks.remarks', 'title' => 'Remarks'],
            

            ['data' => 'remarks_status', 'name' => 'remarks_status', 'title' => 'Remarks Status'],

            ['data' => 'conversation_date', 'name' => 'conversation_date', 'title' => 'Conversation Date'],
            ['data' => 'followup_date', 'name' => 'followup_date', 'title' => 'Followup Date'],


            ['data' => 'conversation_by', 'name' => 'conversation_by', 'title' => 'Conversation by'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'exportable' => false],
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
        return 'drivers_' . date('YmdHis');
    }
    

}
