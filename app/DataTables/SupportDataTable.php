<?php

/**
 * Support DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Support
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Support;
use Yajra\DataTables\Services\DataTable;
use DB;

class SupportDataTable extends DataTable
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
            ->addColumn('action', function ($support) {
                $edit = '<a href="'.url('admin/edit_support/'.$support->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;';

                $delete = '';
                if($support->id!='1'&&$support->id!='2') {
                    $delete = '<a data-href="'.url('admin/delete_support/'.$support->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';
                }
                return $edit.$delete;
            })
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param Support $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Support $model)
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
                    ->addAction()
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
            'id',
            'name',
            'link',
            'status',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'support_' . date('YmdHis');
    }
}
