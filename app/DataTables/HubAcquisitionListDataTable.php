<?php

namespace App\DataTables;

use App\Models\HubEmployee;
use App\Models\User;

use Yajra\DataTables\Services\DataTable;
use DB;
use Auth;

class HubAcquisitionListDataTable extends DataTable
{
    //protected $printPreview = 'admin.hub_employee.print';
    public function dataTable($query)
    {
        return datatables()->of($query);
    }

    public function query(User $model)
    {
        DB::statement(DB::raw('set @serial=0'));
        $data =  DB::Table('users')->select(
            'users.id as id',
            'users.first_name',
            'users.last_name',
            'users.hub_employee_id',
            'users.mobile_number',
            'users.status',
            'users.created_at',
            'hub_employees.employee_name',
            'hubs.name as hub_name',
            DB::raw('DATE_FORMAT(users.created_at, "%d-%b-%Y") as created_at'),
            DB::raw('@serial  := @serial  + 1 AS serial')
        )
            ->leftJoin('hubs', function ($join) {
                $join->on('users.hub_id', '=', 'hubs.id');
            })
            ->leftJoin('hub_employees', function ($join) {
                $join->on('users.hub_employee_id', '=', 'hub_employees.id');
            })
            ->where('users.user_type', 'driver')
            ->where('users.hub_id', '!=', '')
            ->where('users.hub_employee_id', '!=', '');

        if ($this->hub_id != '') {
            $data = $data->where('users.hub_id',  $this->hub_id);
        }
        if ($this->employee_id != '') {
            $data = $data->where('users.hub_employee_id',  $this->employee_id);
        }
        if ($this->start_date != '') {
            $data = $data->whereDate('users.created_at', '>=', $this->start_date);
        }
        if ($this->end_date != '') {
            $data = $data->whereDate('users.created_at', '<=', $this->end_date);
        }

        return $data;
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('lBfr<"table-responsive"t>ip')
            ->orderBy(0, 'DESC')
            ->buttons([
                'csv',
                'excel', [
                    'extend' => 'print',
                    'a' => 'a'
                ]

            ]);
    }

    // public function html()
    // {
    //     return $this->builder()
    //         ->setTableId('users-table')
    //         ->minifiedAjax()
    //         ->orderBy(0,'DESC') 
    //         ->columns($this->getColumns())
    //         ->parameters([
    //             'dom' => 'Blfrtip', 
    //             'buttons' => [
    //                 [
    //                 	'csv',
    //                 	'excel',
    //                 	[
    //                'extend' => 'print',
    //            	'messageTop' => 'Test',
    //            	'message' => 'This print was produced using the Print button for DataTables',
    //            ],
    //             	],

    //             ],

    //         ]);
    // }

    protected function getColumns()
    {


        return [
            ['data' => 'serial', 'name' => 'serial', 'title' => 'Serial'],
            ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ['data' => 'first_name', 'name' => 'users.first_name', 'title' => 'First Name'],
            ['data' => 'last_name', 'name' => 'users.last_name', 'title' => 'Last Name'],
            ['data' => 'mobile_number', 'name' => 'users.mobile_number', 'title' => 'Mobile Number'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'employee_name', 'name' => 'hub_employees.employee_name', 'title' => 'Acquisition by'],
            ['data' => 'hub_name', 'name' => 'hubs.name', 'title' => 'Hub'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
        ];
    }
    protected function filename()
    {
        return 'driver_acquisition_list_' . date('YmdHis');
    }
}
