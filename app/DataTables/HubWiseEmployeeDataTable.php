<?php
namespace App\DataTables;

use App\Models\HubEmployee;
use App\Models\User;

use Yajra\DataTables\Services\DataTable;
use DB;
use Auth;

class HubWiseEmployeeDataTable extends DataTable
{
	public function dataTable($query)
	    {
	    	$user = Auth::guard('hub')->user();
	        return datatables()
	            ->of($query)
	            ->addColumn('created_at', function ($driver_acquisition_list) {
	            	
                    return \Carbon\Carbon::parse($driver_acquisition_list->created_at)->format('d-M-Y');
                })
	            ->addColumn('userType', function ($hub_manage_employee) {
                     
                     return ucfirst($hub_manage_employee->user_type);
                })
                ->addColumn('number_of_driver', function ($hub_manage_employee) {
                    // $hub_driver_list=DB::table('hub_employees')
                    // ->join('users','hub_employees.refaral_id', '=', 'users.used_referral_code')
                    // ->where('users.used_referral_code',$hub_manage_employee->refaral_id)
                    // ->count();

                    $today_hub_driver_list = DB::table('users')
	                    					->where('hub_employee_id', $hub_manage_employee->id)
	                    					->whereDate('created_at', '=', date('Y-m-d'))
	                    					->count();

                    $hub_driver_list = DB::table('users')
                    					->where('hub_employee_id', $hub_manage_employee->id)
                    					->count();

                    if($today_hub_driver_list > 0){
	                    $today_hub_driver_details_link = '<a href="'.url('hub/hub_acquisition_driver/'.$hub_manage_employee->id).'" class="btn btn-xs btn-success" title="Today\'s Acquisition">'.$today_hub_driver_list.'</a>&nbsp;';
	                }
	                else $today_hub_driver_details_link = "";

	                if($hub_driver_list > 0){
	                    $hub_driver_details_link = '<a href="'.url('hub/hub_acquisition_driver/'.$hub_manage_employee->id.'/all').'" class="btn btn-xs btn-primary" title="All Acquisition">'.$hub_driver_list.'</a>&nbsp;';
	             		return $today_hub_driver_details_link.$hub_driver_details_link;
	             	}
	             	else $hub_driver_details_link = "";

                })
                ->rawColumns(['number_of_driver']);
	    }

	    public function query(HubEmployee $model)
	    {
	    	$user = Auth::guard('hub')->user();
	        $hub_manage_employee =  DB::Table('hub_employees')->select(
	            'hub_employees.id as id',
	            'hub_employees.employee_name',
	            'hub_employees.refaral_id',
	            'hub_employees.mobile_number',
	            'hub_employees.user_type',
	            'hub_employees.created_at',
	            'hub_employees.status',
	            'hubs.name as hub_name',
	            'roles.name as role_name',
	        )
	        ->leftJoin('hubs', function($join) {
	            $join->on('hub_employees.hub_id', '=', 'hubs.id');
	        })
	        ->leftJoin('roles', function($join) {
	            $join->on('hub_employees.role_id', '=', 'roles.id');
	        })
	        ->where('hub_id',$user->hub_id);
	        return $hub_manage_employee;
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
	            ['data' => 'employee_name', 'name' => 'hub_employees.employee_name', 'title' => 'Name'],
	            ['data' => 'refaral_id', 'name' => 'hub_employees.refaral_id', 'title' => 'Refaral ID'],
	            ['data' => 'role_name', 'name' => 'role_name', 'title' => 'Role'],
	            ['data' => 'hub_name', 'name' => 'hub_name', 'title' => 'Hub Name'],
	            ['data' => 'mobile_number', 'name' => 'hub_employees.mobile_number', 'title' => 'Phone'],
	            ['data' => 'number_of_driver', 'name' => 'number_of_driver', 'title' => 'Number of Drivers'],

	            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
	            
	        ];
    	}
	    protected function filename()
	    {
	        return 'hub_manage_employee_' . date('YmdHis');
	    }


}
