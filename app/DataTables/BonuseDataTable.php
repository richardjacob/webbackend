<?php
namespace App\DataTables;

use App\Models\Bonus;
use Yajra\DataTables\Services\DataTable;
use DB;

class BonuseDataTable extends DataTable
{
    public function dataTable($query)
        {
            return datatables()
                ->of($query)
                ->addColumn('created_at', function ($bonus) {
                     
                     return $bonus->created_at->format('d-M-Y');
                })

                ->addColumn('action', function ($bonus) {
                     $edit = '<a href="'.url('admin/edit_bonus/'.$bonus->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;';
                     $delete = '<a data-href="'.url('admin/delete_bonus/'.$bonus->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';

                     return $edit.$delete;
                });
        }

    public function query()
    {
        return Bonus::all();
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
                ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
                ['data' => 'user_type', 'name' => 'user_type', 'title' => 'User Type'],
                ['data' => 'bonus_type', 'name' => 'bonus_type', 'title' => 'Bonus Type'],
                ['data' => 'withdrawal_method', 'name' => 'withdrawal_method', 'title' => 'Withdrawal Method'],
                ['data' => 'bonus_amount', 'name' => 'bonus_amount', 'title' => 'Bonus Amount'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
            ];
        }
    protected function filename()
    {
        return 'bonus_' . date('YmdHis');
    }
}