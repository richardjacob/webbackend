<?php
namespace App\DataTables;

use App\Models\Hub;
use Yajra\DataTables\Services\DataTable;
use DB;

class HubDataTable extends DataTable
{
	public function dataTable($query)
	    {
	        return datatables()
	            ->of($query)
                ->addColumn('created_at', function ($hubs) {
                     
                     return $hubs->created_at->format('d-M-Y');
                })
	            ->addColumn('action', function ($hubs) {
	                 $edit = '<a href="'.url('admin/edit_hub/'.$hubs->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;';
	                 $delete = '<a data-href="'.url('admin/delete_hub/'.$hubs->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';

	                 return $edit.$delete;
	            })
                ->addIndexColumn();
	    }

    public function query()
    {
        $hubs = Hub::all();
        return $hubs;
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
	            ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
	            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
	            ['data' => 'address', 'name' => 'address', 'title' => 'Address'],
	            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
	        ];
    	}
    protected function filename()
    {
        return 'hub_' . date('YmdHis');
    }
}