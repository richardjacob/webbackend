<?php
namespace App\DataTables;

use App\Models\User;
use App\Models\HubEmployee;
use Yajra\DataTables\Services\DataTable;
use DB;
use Auth;
use Session;
// use Carbon\Carbon;

class HubDriverAcquisitionDataTable extends DataTable
{
	public function dataTable($query)
	    {
	        return datatables()
	            ->of($query)
	            ->addColumn('created_at', function ($driver_acquisition_list) {	            	
                    return \Carbon\Carbon::parse($driver_acquisition_list->created_at)->format('d-M-Y');
                });
	    }

	    public function query(User $model)
	    {
	    	

	        $driver_acquisition_list =  DB::Table('users')->select(
	            'users.id as id',
	            'users.first_name',
	            'users.last_name',
	            'users.hub_employee_id',
	            'users.mobile_number',
	            'users.status',
	            'users.created_at',
	            'hub_employees.employee_name',
	        )
	        ->leftJoin('hub_employees', function($join) {
	            $join->on('users.hub_employee_id', '=', 'hub_employees.id');
	        })
	        ->where('users.user_type', 'driver')
	        ->where('users.hub_id', '!=', '');

	        if($this->start_date !='') {
	        	$driver_acquisition_list = $driver_acquisition_list->whereDate('users.created_at', '>=', $this->start_date);
	        }
	        if($this->end_date !='') {
	        	$driver_acquisition_list = $driver_acquisition_list->whereDate('users.created_at', '<=', $this->end_date);
	        }


	        if (LOGIN_USER_TYPE=='hub') {
	            $driver_acquisition_list = $driver_acquisition_list->where('users.hub_id',  auth()->guard('hub')->user()->hub_id);

	            if($this->id !='') { // aquisition staff
	                $driver_acquisition_list = $driver_acquisition_list->where('users.hub_employee_id',$this->id);
	            }    
	            else{	
	            	$role_id = auth()->guard('hub')->user()->role_id;

		            if($role_id == '5'){ // aquisition staff
		                $driver_acquisition_list = $driver_acquisition_list->where('users.hub_employee_id',auth()->guard('hub')->user()->id);
		            } 
		        }
	        }
	        return $driver_acquisition_list;
	    }

	    public function html()
	    {
	        return $this->builder()
	                    ->columns($this->getColumns())
	                    // ->addAction(["printable" => false])
	                    ->minifiedAjax()
	                    ->dom('lBfr<"table-responsive"t>ip')
	                    ->orderBy(0,'DESC')
	                    ->buttons(
	                        ['csv', 'excel', 'print', 'reset']
	                    );
	    }

	    protected function getColumns()
    	{


    	return [
    		    ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
	            ['data' => 'first_name', 'name' => 'users.first_name', 'title' => 'First Name'],
	            ['data' => 'last_name', 'name' => 'users.first_name', 'title' => 'Last Name'],
	            ['data' => 'mobile_number', 'name' => 'users.mobile_number', 'title' => 'Mobile Number'],
	            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
	            ['data' => 'employee_name', 'name' => 'hub_employees.employee_name', 'title' => 'Acquisition by'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
	        ];
    	}
	    protected function filename()
	    {
	        return 'driver_acquisition_list_' . date('YmdHis');
	    }


}
