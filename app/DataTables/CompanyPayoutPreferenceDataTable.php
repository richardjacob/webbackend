<?php

/**
 * Company Payouts DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Company Payouts
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use App\Models\CompanyPayoutPreference;
use DB;
use Auth;

class CompanyPayoutPreferenceDataTable extends DataTable
{
    
    public function dataTable($query)
    {
        return datatables()
                    ->of($query)
                    ->addColumn('payout_method', function ($data) {
                        if($data->payout_method == "banktransfer") return 'Bank Transfer';
                        else return ucwords($data->payout_method);
                    })
                    ->addColumn('default', function ($data) {
                        if($data->default == "yes") return '<i class="fa fa-check text-success" aria-hidden="true"></i>';
                        else return '<i class="fa fa-times text-danger" aria-hidden="true"></i>';
                    })
                    ->addColumn('action', function ($data) {
                        $view = (auth()->guard('company')->user()!=null) ? '<a href="'.url(LOGIN_USER_TYPE.'/view_payout_preference/'.$data->id).'" class="btn btn-xs btn-success" style="margin-bottom:5px;"><i class="glyphicon glyphicon-eye-open"></i></a>&nbsp;' : '';
                        $edit = (auth()->guard('company')->user()!=null) ? '<a href="'.url(LOGIN_USER_TYPE.'/edit_payout_preference/'.$data->id).'" class="btn btn-xs btn-primary" style="margin-bottom:5px;"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                        $delete = (auth()->guard('company')->user()!=null) ? '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_payout_preference/'.$data->id).'" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#confirm-delete" style="margin-bottom:5px;"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;':'';
        
                        return $view.$edit.$delete;                        
                    })
                    ->rawcolumns(['default','action'])
                   ->addIndexColumn();
    }

    public function query(CompanyPayoutPreference $modal)
    {
        $list = DB::table('company_payout_preference')
                         ->select(
                             'company_payout_preference.id as id',
                             'company_payout_preference.payout_method as payout_method', 
                             'company_payout_preference.account_number as account_number',
                             'company_payout_preference.currency_code as currency_code',
                             'company_payout_preference.bank_name as bank_name',
                             'company_payout_credentials.default as default',
                             DB::raw('DATE_FORMAT(company_payout_preference.created_at, "%d-%b-%Y<br>%h:%i %p") as created_at')
                         )
                        ->leftJoin('company_payout_credentials', function($join) {
                            $join->on('company_payout_preference.id', '=', 'company_payout_credentials.preference_id');
                        })                      
                        ->where('company_payout_preference.company_id',  Auth::guard('company')->user()->id);
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
                    ->buttons(
                        ['csv', 'excel', 'print']
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        
        return array(
           ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false], 
           ['data' => 'payout_method', 'name' => 'payout_method', 'title' => 'Payout Method'],
           ['data' => 'account_number', 'name' => 'account_number', 'title' => 'Account Number'],
           ['data' => 'currency_code', 'name' => 'currency_code', 'title' => 'Currency Code'],
           ['data' => 'bank_name', 'name' => 'bank_name', 'title' => 'Bank Name'],
           ['data' => 'default', 'name' => 'default', 'title' => 'Default'],
           ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'exportable' => false],
        );
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'company_payouts_preference_' . date('YmdHis');
    }
}