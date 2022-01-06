<?php

/**
 * ContactListDataTable DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Driver
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Complain;
use Yajra\DataTables\Services\DataTable;
use DB;

class ContactListDataTable extends DataTable
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
                $movement = (auth('admin')->user()->can('movement_contact')) ? '<a href="'.url(LOGIN_USER_TYPE.'/movement_contact/'.$data->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-plus"></i></a>&nbsp;':'';
                $tracking = (auth('admin')->user()->can('tracking_movement_contact')) ? '<a href="'.url(LOGIN_USER_TYPE.'/tracking_movement_contact/'.$data->id).'" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-flash"></i></a>&nbsp;':'';
                            
                return $movement.$tracking;
            })
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Complain $model)
    {
        $list = DB::table('contacts')
                        ->select(
                            'id',
                            'name',
                            'email',
                            'contact_for',
                            'msg',
                            DB::raw("(
                                CASE
                                WHEN status='0' THEN 'Pending'
                                WHEN status='1' THEN 'Completed'
                                END
                             ) AS status"),
                            DB::raw('DATE_FORMAT(created_at, "%d-%b-%Y, %h:%i %p") as created_at')
                        );

        if($this->contact_id !='') $list = $list->where('contact_id', $this->contact_id);
        else{            
            if($this->contact_for !='') $list = $list->where('contact_for', $this->contact_for);
            if($this->status !='') $list = $list->where('status', $this->status);
            
            if($this->start_date !='')  {
                list($d, $m, $y) = explode('-', $this->start_date);
                $date1 =  $y.'-'.$m.'-'.$d;
                $list = $list->whereDate('created_at', '>=', $date1);
            }
            if($this->end_date !='')  {
                list($d, $m, $y) = explode('-', $this->end_date);
                $date2 =  $y.'-'.$m.'-'.$d;
                $list = $list->whereDate('created_at', '<=', $date2);
            }
        }
        return $list;
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
            ['data' => 'name', 'name' => 'name', 'title' => 'Name'],            
            ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            ['data' => 'contact_for', 'name' => 'contact_for', 'title' => 'Contact for'],
            ['data' => 'msg', 'name' => 'msg', 'title' => 'Message'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
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
        return 'contact_list_' . date('YmdHis');
    }
    

}
