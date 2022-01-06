<?php
namespace App\DataTables;

use App\Models\ActivityLog;
use Yajra\DataTables\Services\DataTable;
use DB;

class ActivityLogDataTable extends DataTable
{
	public function dataTable($query)
	    {
	        return datatables()
	            ->of($query)
                ->addColumn('created_at', function ($logs) {
                     return $logs->created_at->format('h:i:s A').'<br>'.$logs->created_at->format('d-M-Y');
                })
                ->addColumn('subject_type', function ($logs) {
                    $array = explode('\\',$logs->subject_type);
                    return $array[count($array)-1];
                })
                ->addColumn('updated_data', function ($logs) {
                    $data = trim(trim(str_replace(array('{"attributes":', ',"'), array('', ',<br>"'), @explode(',"old":',$logs->properties)[0]), '{'),'}');
                    return str_replace('"','', $data);
                })
                ->addColumn('old_data', function ($logs) {                    
                    $data = trim(trim(str_replace(array(',"'), array(',<br>"'), @explode(',"old":',$logs->properties)[1]), '{'),'}');
                    return str_replace('"','', $data);
                })

                ->rawcolumns(['created_at', 'updated_data', 'old_data'])
                ->addIndexColumn();
	    }

    public function query(ActivityLog $model)
    {
        $logs = $model->all()->sortByDesc('id')->skip(0)->take(500);
        return $logs;
    }
	
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->parameters(['order' => [0, 'desc']])
                    ->minifiedAjax()
                    ->dom('lBfr<"table-responsive"t>ip')
                    ->buttons(
                        ['csv', 'excel', 'print', 'reset']
                    );
                   
    }

	protected function getColumns()
    	{
	        return [
                ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false],
	            ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
	            ['data' => 'subject_type', 'name' => 'subject_type', 'title' => 'Model'],
                ['data' => 'subject_id', 'name' => 'subject_id', 'title' => 'Model Id'],
                ['data' => 'description', 'name' => 'description', 'title' => 'Event'],
                ['data' => 'updated_data', 'name' => 'updated_data', 'title' => 'Updated Data'],
                ['data' => 'old_data', 'name' => 'old_data', 'title' => 'Old Data'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
	        ];
    	}
    protected function filename()
    {
        return 'activity_log_' . date('YmdHis');
    }
}