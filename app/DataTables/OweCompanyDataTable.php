<?php

/**
 * OWE DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    OWE
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\User;
use App\Models\Trips;
use App\Models\DriverOweAmount;
use Yajra\DataTables\Services\DataTable;
use DB;

class OweCompanyDataTable extends DataTable
{
    protected $filter_type;

    // Set the value for User Type 
    public function setFilterType($filter_type){
        $this->filter_type = $filter_type;
        return $this;
    }

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
            ->addColumn('trip_ids', function ($owe) {
                $trips_ids = $owe->driver_trips->whereIn('payment_mode',['Cash & Wallet','Cash'])
                                                ->whereIn('status',['Payment','Completed'])
                                                ->where('owe_amount', '>', 0)
                                                ->pluck('id')
                                                ->toArray();

                return '<a href="owe_trip_list/driver/'.$owe->id.'">'.count($trips_ids).'</a>';
            })
            ->addColumn('owe_amount', function ($owe) {
                $owe_amount = $owe->driver_trips->sum('owe_amount');
                return currency_symbol().number_format($owe_amount,2,'.','');
            })
            ->addColumn('applied_owe_amount', function ($owe) {
                $applied_owe_amount = $owe->driver_trips->sum('applied_owe_amount');
                return currency_symbol().number_format($applied_owe_amount,2,'.','');
            })
            ->addColumn('remaining_owe_amount', function ($owe) {
                $remaining_owe_amount = $owe->driver_trips->sum('owe_amount') - $owe->driver_trips->sum('applied_owe_amount');
                return currency_symbol().number_format($remaining_owe_amount,2,'.','');
            })
            ->addColumn('pay_via', function ($owe) {
                // $remaining_owe_amount = DriverOweAmount::whereHas('user',function($q) use ($owe) {
                //     $q->where('company_id',$owe->company_id);
                // })->get()->sum('amount');

                $remaining_owe_amount = $owe->driver_trips->sum('owe_amount') - $owe->driver_trips->sum('applied_owe_amount');
                $remaining_owe_amount = number_format($remaining_owe_amount,2,'.','');
                        
                //$remaining_owe_amount=1;
                $redirect_url = str_replace(array('.', '/'), array('_','-'),  env('ADMIN_PANEL_SUB_DOMAIN').'.'.env('DOMAIN').'/company/dues'); 
                
                $paid_btn='';
                foreach( Config('payment_gateway') as $provider => $pg){
                    if($pg['is_enabled'] == '1') {
                        $paid_btn.=  '<a href="//'.env('PAYMENT_DOMAIN') . '.' . env('DOMAIN').'/payment_by_web/'.$provider.'/driver_owe_amount/'.$owe->id.'/'.$remaining_owe_amount.'/0/'.$redirect_url.'"  target="_blank">'.ucwords($provider).'</a> &nbsp;';
                    }
                }
                
                //$paid_btn =  '<a href="//'.env('PAYMENT_DOMAIN') . '.' . env('DOMAIN').'/payment_by_web/nagad/driver_owe_amount/'.$owe->id.'/'.$remaining_owe_amount.'/0/'.$redirect_url.'" >Pay</a>';

                return $remaining_owe_amount == 0 ? '' : $paid_btn;
            })
            ->filterColumn('driver_name', function ($query, $keyword) {
                $keywords = trim($keyword);
                $query->whereRaw("CONCAT(first_name, last_name) like ?", ["%{$keywords}%"]);
             })
            ->rawcolumns(['trip_ids','pay_via'])
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
        $owe = $model->with('company')
                ->where(function($query)  {
                    if(LOGIN_USER_TYPE=='company') {
                        //If login user is company then get that company drivers only
                        $query->where('users.company_id',auth('company')->user()->id);
                    }
                })
                ->join('trips', function($join) {
                    $join->on('users.id', '=', 'trips.driver_id');
                })
                ->leftJoin('companies', function($join) {
                    $join->on('users.company_id', '=', 'companies.id');
                })
                ->select('trips.id as trip_id','users.id As id', DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'), 'users.email', 'users.gender', 'trips.currency_code as currency_code',DB::raw("GROUP_CONCAT(trips.id) as trip_ids"),DB::raw('SUM(trips.owe_amount) as owe_amount'),DB::raw('SUM(trips.remaining_owe_amount) as remaining_owe_amount'),DB::raw('SUM(trips.applied_owe_amount) as applied_owe_amount'),'companies.name as driver_company_name','companies.id as company_id');
        if($this->filter_type == 'applied') {
            $owe = $owe->where('applied_owe_amount','>','0');
        }
        else {
            $owe = $owe->where('owe_amount','>','0');
        }

        if(LOGIN_USER_TYPE=='company') {
            $owe = $owe->groupBy('id');
        }
        else {
            $owe = $owe->groupBy('company_id');
        }
        return $owe;
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
                    //->addAction()
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
        $owe_columns = array();
        
        $columns = array(
            ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false], 
            ['data' => 'id', 'name' => 'users.id', 'title' => 'Driver Id'],
            ['data' => 'driver_name', 'name' => 'driver_name', 'title' => 'Driver Name'],
            ['data' => 'trip_ids', 'name' => 'trip_ids', 'title' => 'Total Trips','orderable' => false, 'searchable' => false],
        );
        if($this->filter_type != 'applied') {
            $owe_columns = array(
                ['data' => 'owe_amount', 'name' => 'owe_amount', 'title' => 'Owe Amount', 'orderable' => false],
                ['data' => 'pay_via', 'name' => 'pay_via', 'title' => 'Pay Via', 'orderable' => false, 'searchable' => false],
            );
            
        }
            
        return array_merge($columns, $owe_columns);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'owe_' . date('YmdHis');
    }
}
