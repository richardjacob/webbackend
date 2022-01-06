<?php
namespace App\DataTables;


use App\Models\MonitorCamera;
use Yajra\DataTables\Services\DataTable;
use DB;

class MonitorCameraDataTable extends DataTable
{
	public function dataTable($query)
	    {
	        return datatables()
	            ->of($query)
	            // ->addColumn('created_at', function ($monitor_camera) {
             //         return \Carbon\Carbon::parse($monitor_camera->created_at)->format('d-M-Y');
             //    })
	            ->addColumn('action', function ($monitor_camera) {
	                 $edit = '<a href="'.url('admin/edit_monitor_camera/'.$monitor_camera->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;';
	                 

	                 return $edit;
	            })
	            ->filterColumn('driver_name', function ($query, $keyword) {
	                $keywords = trim($keyword);
	                $query->whereRaw("CONCAT(first_name, last_name) like ?", ["%{$keywords}%"]);
	             })
	            ->rawcolumns(['created_at','action'])
            	->addIndexColumn();
	    }

	    public function query(MonitorCamera $model)
	    {
	        $monitor_camera =  DB::Table('monitor_cameras')->select(
	            'monitor_cameras.id as id',
	            'monitor_cameras.driver_id as driver_id',

	            'monitor_cameras.monitor_sim',
	            'monitor_cameras.monitor_imei',
	            'monitor_cameras.monitor_ip',
	            'monitor_cameras.monitor_status',

	            'monitor_cameras.camera_sim',
	            'monitor_cameras.camera_imei',
	            'monitor_cameras.camera_ip',
	            'monitor_cameras.camera_status',

	            //'monitor_cameras.created_at',

	            //'users.first_name as driver_name',
	            'users.mobile_number as mobile_number',
	            'vehicle.vehicle_number as vehicle_number',
            	DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
            	DB::raw('DATE_FORMAT(monitor_cameras.created_at, "%d-%b-%y<br>%h:%i %p") as created_at')
	        )

	        ->leftJoin('users', function($join) {
	            $join->on('monitor_cameras.driver_id', '=', 'users.id');
	        })
	        ->leftJoin('vehicle', function($join) {
	            $join->on('monitor_cameras.vehicle_id', '=', 'vehicle.id');
	        });
	        return $monitor_camera;
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
	        	['data' => 'driver_id', 'name' => 'driver_id', 'title' => 'Driver Id'],
	        	['data' => 'driver_name', 'name' => 'driver_name', 'title' => 'Driver Name'],
	        	['data' => 'mobile_number', 'name' => 'users.mobile_number', 'title' => 'Mobile Number'],
	        	['data' => 'vehicle_number', 'name' => 'vehicle.vehicle_number', 'title' => 'Vehicle Number'],

	        	['data' => 'monitor_sim', 'name' => 'monitor_sim', 'title' => 'Monitor Sim Number'],
	        	['data' => 'monitor_imei', 'name' => 'monitor_imei', 'title' => 'Monitor IMEI'],
	        	// ['data' => 'monitor_ip', 'name' => 'monitor_ip', 'title' => 'monitor_ip'],
	        	['data' => 'monitor_status', 'name' => 'monitor_status', 'title' => 'Monitor Status'],

	        	['data' => 'camera_sim', 'name' => 'camera_sim', 'title' => 'Camera Sim Number'],
	        	['data' => 'camera_imei', 'name' => 'camera_imei', 'title' => 'Camera IMEI'],
	        	// ['data' => 'camera_ip', 'name' => 'camera_ip', 'title' => 'Camera ip'],
	        	['data' => 'camera_status', 'name' => 'camera_status', 'title' => 'Camera status'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
	        ];

	        
    	}
	    protected function filename()
	    {
	        return 'monitor_camera_' . date('YmdHis');
	    }


}
