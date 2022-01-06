<?php

/**
 * Manage Fare DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Manage Fare
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\PeakHour;
use Yajra\DataTables\Services\DataTable;
use DB;

class ManagePeakHourDataTable extends DataTable
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
            ->addColumn('type', function ($peakHour) {
                if($peakHour->type == '1') return "Morning";
                else return "Evening";
            })
            ->addColumn('start_time', function ($peakHour) {
                $time = date("h:i A ", strtotime($peakHour->start_time));
                return substr_replace($time, '', 0, min(1, strspn($time, '0')));
            })
            ->addColumn('end_time', function ($peakHour) {
                $time = date("h:i A ", strtotime($peakHour->end_time));
                return substr_replace($time, '', 0, min(1, strspn($time, '0')));
            })
            ->addColumn('status', function ($peakHour) {
                if($peakHour->status == '1') return "Active";
                else return "Inactive";
            })
            
            ->addColumn('action', function ($peakHour) {
                $edit = '<a href="'.url('admin/edit_peak_hour/'.$peakHour->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;';

                return $edit;
            })
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param ManageFare $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PeakHour $model)
    {
        
        return $model->all();
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
                    ->orderBy(0,'DESC')
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
        return [
            ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false],
            ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ['data' => 'day_name', 'name' => 'day_name', 'title' => 'Day'],
            ['data' => 'type', 'name' => 'Type', 'title' => 'Type', 'orderable' => false],
            ['data' => 'start_time', 'name' => 'start_time', 'title' => 'Start Time'],
            ['data' => 'end_time', 'name' => 'end_time', 'title' => 'End Time'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'exportable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'fare_details_' . date('YmdHis');
    }
}