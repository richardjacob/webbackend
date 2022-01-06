<?php

/**
 * Company DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Company
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Company;
use Yajra\DataTables\Services\DataTable;
use DB;

class CompanyDataTable extends DataTable
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
            ->addColumn('email', function ($companies) {
                return protectedString($companies->email);
            })
            ->addColumn('drivers', function($companies) {
                return '<a href="company_driver_list/'.$companies->id.'">'.total_company_driver($companies->id).'</a>';
            })
            ->addColumn('action', function ($companies) {
                $edit = (auth('admin')->user()->can('update_company')) ? '<a href="'.url('admin/edit_company/'.$companies->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                $delete = (auth('admin')->user()->can('delete_company')) ? '<a data-href="'.url('admin/delete_company/'.$companies->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>':'';

                $action = $edit;
                if($companies->id != 1) {
                    $action .= $delete;
                }
                
                return $action;

            })
            ->addColumn('transaction_history', function ($companies) {
                $paid_to_alesha = (auth('admin')->user()->can('paid_to_alesha')) ? '<a href="'.url('admin/company/transaction_history/paid_to_alesha/'.$companies->id).'" class="btn btn-xs btn-info" title="Paid to Alesha"><i class="glyphicon glyphicon-th-large"></i></a>&nbsp;' : '';
                $balance_withdraw = (auth('admin')->user()->can('balance_withdraw')) ? '<a href="'.url('admin/company/transaction_history/balance_withdraw/'.$companies->id).'" class="btn btn-xs btn-success" title="Balance Withdraw"><i class="glyphicon glyphicon-th"></i></a>&nbsp;' : '';
                $payout = (auth('admin')->user()->can('payout')) ? '<a href="'.url('admin/company/transaction_history/payout/'.$companies->id).'" class="btn btn-xs btn-warning"><i class="glyphicon glyphicon-th-list" title="Payout"></i></a>&nbsp;' : '';
                
                return $paid_to_alesha.$balance_withdraw .$payout;
            })            
            ->rawColumns(['drivers', 'transaction_history', 'action'])            
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param Company $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Company $model)
    {
    //     if(isLiveEnv()) {
    //         $companies = Company::select('companies.id', 'companies.name','companies.email','companies.country_code','companies.mobile_number', 'companies.status',DB::raw('CONCAT("XXXXXX",Right(mobile_number,4)) AS hidden_mobile'))->with('drivers');
    //     }
    //     else {
    //         $companies = Company::select('companies.id', 'companies.name','companies.email','companies.country_code','companies.mobile_number', 'companies.status',DB::raw('CONCAT("+",companies.country_code," ",companies.mobile_number) AS mobile'))->with('drivers');
    //     }
        
    //     return $companies;
            return $companies = DB::table('companies')->select('companies.id', 'companies.name','companies.email','companies.country_code','companies.mobile_number', 'companies.status');
        
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
        $mobile_number_column = (isLiveEnv()) ?'hidden_mobile':'mobile_number';

        return [
            ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false], 
            ['data' => 'id', 'name' => 'companies.id', 'title' => 'Id'],
            ['data' => 'name', 'name' => 'companies.name', 'title' => 'Name'],
            ['data' => 'drivers', 'name' => 'drivers', 'title' => 'Total Drivers','width' => "10%"],
            ['data' => 'email', 'name' => 'companies.email', 'title' => 'Email'],
            ['data' => $mobile_number_column, 'name' => 'companies.mobile_number', 'title' => 'Mobile Number'],
            ['data' => 'status', 'name' => 'companies.status', 'title' => 'Status'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'exportable' => false],
            ['data' => 'transaction_history', 'name' => 'transaction_history', 'title' => 'Transaction History', 'orderable' => false, 'searchable' => false, 'exportable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'companies_' . date('YmdHis');
    }
}