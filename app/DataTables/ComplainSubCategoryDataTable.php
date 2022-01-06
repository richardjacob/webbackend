<?php

/**
 * Driver DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Driver
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\ComplainSubCategory;
use Yajra\DataTables\Services\DataTable;
use DB;

class ComplainSubCategoryDataTable extends DataTable
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
            ->addColumn('action', function ($data) {
                $edit = (auth('admin')->user()->can('edit_complain_sub_category')) ? '<a href="'.url(LOGIN_USER_TYPE.'/edit_complain_sub_category/'.$data->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                $delete = (auth('admin')->user()->can('delete_complain_sub_category')) ? '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_complain_sub_category/'.$data->id).'" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;':'';
                return $edit.$delete;
            })
            ->rawcolumns(['action','created_at'])
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ComplainSubCategory $model)
    {
        return DB::table('complain_sub_categories')
                        ->select(
                            'complain_sub_categories.id as id', 
                            'complain_sub_categories.sub_category as sub_category',
                            'complain_sub_categories.sub_category_bn as sub_category_bn',
                            'complain_categories.category as category', 
                            DB::raw('DATE_FORMAT(complain_sub_categories.created_at, "%d-%b-%Y, %h:%i %p") as created_at')
                        )
                        ->leftJoin('complain_categories', function($join) {
                            $join->on('complain_sub_categories.complain_cat_id', '=', 'complain_categories.id');
                        });
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
                    ->parameters(['order' => [1, 'DESC']])
                    ->orderBy(0)
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
        $columns = [
            ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false],
            ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ['data' => 'category', 'name' => 'category', 'title' => 'Category'],
            ['data' => 'sub_category', 'name' => 'sub_category', 'title' => 'Sub Category Name in English'],
            ['data' => 'sub_category_bn', 'name' => 'sub_category_bn', 'title' => 'Sub Category Name in Bangla'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created at'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action'],
        ];
        

        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'complain_sub_category_' . date('YmdHis');
    }
    

}
