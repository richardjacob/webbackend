<?php

/**
 * Rider DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Rider
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Services\DataTable;
use DB;

class RiderDataTable extends DataTable
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
            ->filterColumn('gender', function($query, $keyword) {
                if(strpos('male', $keyword)!==false)
                    $search[] = 1;
                if(strpos('female', $keyword)!==false)
                    $search[] = 2;
                    
                if(strpos('other', $keyword)!==false)
                $search[] = 3;

                if(isset($search))
                    $query->whereIn('gender', $search);
            })
            ->addColumn('email', function ($users) {
                return protectedString($users->email);
            })
            ->addColumn('action', function ($users) {
                $edit = (auth('admin')->user()->can('update_rider')) ? '<a href="'.url('admin/edit_rider/'.$users->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                $delete = (auth('admin')->user()->can('delete_rider')) ? '<a data-href="'.url('admin/delete_rider/'.$users->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;':'';

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
    public function query(User $model)
    {
        $users = DB::Table('users')->select(
            'users.id as id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.country_code',            
            DB::raw("(
                CASE
                WHEN users.is_email_valid='1' THEN 'Yes'
                ELSE 'No' 
                END
            ) AS is_email_valid"),          
            DB::raw("(
                CASE
                WHEN users.is_our_employee='0' THEN 'No'
                WHEN users.is_our_employee='1' THEN 'Yes'
                ELSE '' 
                END
            ) AS is_our_employee"),
            DB::raw("(
                CASE
                WHEN users.gender=1 THEN 'Male'
                WHEN users.gender=2 THEN 'Female'
                WHEN users.gender=3 THEN 'Other'
                ELSE '' 
                END
            ) AS gender_name"),
            'users.status',
            DB::raw('CONCAT(\'0\',users.mobile_number) AS mobile_number'),
            DB::raw('DATE_FORMAT(users.created_at, "%d-%b-%Y<br>%h:%i %p") as created_at'),
            //DB::raw('CONCAT("XXXXXX",Right(users.mobile_number,4)) AS hidden_mobile'),
        )->where('user_type','Rider')->groupBy('id');
        return $users;
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
        //$mobile_number_column = (isLiveEnv())?'hidden_mobile':'mobile_number';
        return [
            ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false], 
            ['data' => 'id', 'name' => 'users.id', 'title' => 'Id'],
            ['data' => 'is_our_employee', 'name' => 'users.is_our_employee', 'title' => 'Our Employee?'],
            ['data' => 'first_name', 'name' => 'users.first_name', 'title' => 'First Name'],
            ['data' => 'last_name', 'name' => 'users.last_name', 'title' => 'Last Name'],
            ['data' => 'email', 'name' => 'users.email', 'title' => 'Email'],
            ['data' => 'is_email_valid', 'name' => 'is_email_valid', 'title' => 'Email Verified'],
            ['data' => 'gender_name', 'name' => 'gender', 'title' => 'Gender'],
            ['data' => 'mobile_number', 'name' => 'mobile_number', 'title' => 'Mobile Number'],
            ['data' => 'status', 'name' => 'users.status', 'title' => 'Status'],
            ['data' => 'created_at', 'name' => 'users.created_at', 'title' => 'Created At'],
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
        return 'riders_' . date('YmdHis');
    }
}
