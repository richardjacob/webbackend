<?php
namespace App\DataTables;

use App\Models\HubEmployee;
use Yajra\DataTables\Services\DataTable;
use DB;

class HubEmployeeDataTable extends DataTable
{
	public function dataTable($query)
	    {
	        return datatables()
	            ->of($query)
	            ->addColumn('created_at', function ($hub_manage_employee) {
                     return \Carbon\Carbon::parse($hub_manage_employee->created_at)->format('d-M-Y');
                })
	            ->addColumn('userType', function ($hub_manage_employee) {
                     
                     return ucfirst($hub_manage_employee->user_type);
                })
	            ->addColumn('action', function ($hub_manage_employee) {
	                 $edit = '<a href="'.url('admin/edit_hub_employee/'.$hub_manage_employee->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;';
	                 $delete = '<a data-href="'.url('admin/delete_hub_employee/'.$hub_manage_employee->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';

	                 return $edit.$delete;
	            })
                ->addIndexColumn();
	    }

	    public function query(HubEmployee $model)
	    {
	        $hub_manage_employee =  DB::Table('hub_employees')->select(
	            'hub_employees.id',
	            'hub_employees.employee_name',
	            'hub_employees.refaral_id',
	            'hub_employees.mobile_number',
	            'hub_employees.user_type',
	            'hub_employees.status',
	            'hub_employees.created_at',
	            'hubs.name as hub_name',
	            'roles.name as role_name',
	        )
	        ->leftJoin('hubs', function($join) {
	            $join->on('hub_employees.hub_id', '=', 'hubs.id');
	        })
	        ->leftJoin('roles', function($join) {
	            $join->on('hub_employees.role_id', '=', 'roles.id');
	        });
	        return $hub_manage_employee;
	    }

	    public function html()
	    {
	        return $this->builder()
	                    ->columns($this->getColumns())
	                    ->addAction(["printable" => false])
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
                ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false],
	            ['data' => 'id', 'name' => 'hub_employees.id', 'title' => 'Id'],
	            ['data' => 'employee_name', 'name' => 'hub_employees.employee_name', 'title' => 'Name'],
	            ['data' => 'refaral_id', 'name' => 'hub_employees.refaral_id', 'title' => 'Refaral ID'],
	            ['data' => 'role_name', 'name' => 'roles.name', 'title' => 'Role'],
	            ['data' => 'hub_name', 'name' => 'hubs.name', 'title' => 'Hub Name'],
	            ['data' => 'mobile_number', 'name' => 'hub_employees.mobile_number', 'title' => 'Phone'],
	            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
	        ];

	        
    	}
	    protected function filename()
	    {
	        return 'hub_manage_employee_' . date('YmdHis');
	    }


}
