<?php
namespace App\DataTables;

use App\Models\AuditLog;
use Yajra\DataTables\Services\DataTable;
use DB;

class AuditLogDataTable extends DataTable
{
    public function dataTable($query)
        {
            return datatables()
                ->of($query)
                ->addIndexColumn();
        //         ->addColumn('created_at', function ($logs) {
        //              return $logs->created_at->format('h:i:s A').'<br>'.$logs->created_at->format('d-M-Y');
        //         })
        //         ->addColumn('auditable_type', function ($logs) {
        //             $array = explode('\\',$logs->auditable_type);
        //             return $array[count($array)-1];
        //         })
        //         ->addColumn('new_values', function ($logs) {
        //             $data = trim(trim(str_replace(',"', ',<br>"', $logs->new_values), '{'),'}');
        //             return str_replace('"','', $data);
        //         })
        //         ->addColumn('old_values', function ($logs) {
        //             $data = trim(trim(str_replace(',"', ',<br>"', $logs->old_values), '{'),'}');
        //             return str_replace('"','', $data);
        //         })
        //         ->rawcolumns(['created_at','new_values','old_values']);
        }

        public function query(AuditLog $model)
        {
            $logs = DB::table('audits')->select(
                'audits.id as id',
                'audits.user_type',
                'audits.user_id',
                'audits.event',
                'audits.auditable_type',
                'audits.auditable_id',
                'audits.created_at',
                'audits.old_values',
                'audits.new_values',
                'audits.url',
                'audits.ip_address',
                'audits.user_agent',
                'audits.tags',
                'audits.auth_id',
                'audits.auditable_table',
                'admins.username as username'
            )
            ->leftJoin('admins', function($join) {
                $join->on('audits.auth_id', '=', 'admins.id');
            });

            if($this->vehicle_id != ''){
                $logs = $logs->where('audits.user_id', $this->vehicle_id)
                             ->where('audits.user_type', 'LIKE', '%Vehicle%');
            }
            elseif($this->user_id != ''){
                $logs = $logs->where('audits.user_id', $this->user_id)
                             ->where('audits.user_type', 'LIKE', '%User%');
            }

            $logs = $logs->skip(0)->take(500);
            return $logs;
        }
    
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->parameters(['order' => [0, 'desc']])
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
                ['data' => 'id', 'name' => 'audits.id', 'title' => 'Id'],
                ['data' => 'auditable_type', 'name' => 'auditable_type', 'title' => 'Model'],
                ['data' => 'username', 'name' => 'username', 'title' => 'Modify By'],
                ['data' => 'user_id', 'name' => 'user_id', 'title' => 'User ID'],
                ['data' => 'event', 'name' => 'event', 'title' => 'Event'],
                ['data' => 'new_values', 'name' => 'new_values', 'title' => 'Updated Data'],
                ['data' => 'old_values', 'name' => 'old_values', 'title' => 'Old Data'],
                ['data' => 'url', 'name' => 'url', 'title' => 'URL'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
            ];
        }
    protected function filename()
    {
        return 'activity_log_' . date('YmdHis');
    }
}