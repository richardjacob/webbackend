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

use App\Http\Controllers\Controller;
use Yajra\DataTables\Services\DataTable;
use App\Models\Trips;
use DB;

use App\Models\User;
use App\Models\DriverOweAmount;
use Session;

class OweTripListDataTable extends DataTable
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
            ->addColumn('begin_trip_custom', function ($trips) {
                return date("h:i A", strtotime($trips->begin_trip)).'<br>'.date("d/m/Y", strtotime($trips->begin_trip)); 
            })
            ->addColumn('end_trip_custom', function ($trips) {
                return date("h:i A", strtotime($trips->end_trip)).'<br>'.date("d/m/Y", strtotime($trips->end_trip));                
            })
            ->rawColumns(['begin_trip_custom', 'end_trip_custom']);
            
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Trips $model)
    {
        $company_or_driver = session('company_or_driver');
        $id = session('id');

        $owe = $model->select('id', 'car_id', 'driver_id', 'user_id', 'begin_trip', 'end_trip', 'created_at', 'payment_mode','wallet_amount', 'promo_amount', 'base_fare', 'time_fare', 'distance_fare', 'schedule_fare', 'trips.access_fee', 'peak_amount', 'tips', 'waiting_charge', 'toll_fee', 'additional_rider_amount', 'driver_peak_amount', 'driver_or_company_commission', 'total_fare', 'driver_payout', 'applied_owe_amount', 'status', 'subtotal_fare', 'owe_amount', 'remaining_owe_amount','pickup_location','drop_location')
            ;

        if($company_or_driver == 'company'){
            $driver_id_array = array();
            foreach(DB::table('users')->select('id')->where('company_id', $id)->get()->toArray() as $userId){
                $driver_id_array[] = $userId->id;
            }            
            $owe = $owe->whereIn('driver_id', $driver_id_array)->where('owe_amount','>','0');
        }else{ // driver
            return $owe = $owe->where('driver_id', $id)->where('owe_amount','>','0');
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
        $columns = [
            ['data' => 'id', 'name' => 'id', 'title' => 'Trip ID'],
            ['data' => 'driver_id', 'name' => 'driver_id', 'title' => 'Driver ID'],
            ['data' => 'user_id', 'name' => 'user_id', 'title' => 'Rider ID'],
            ['data' => 'pickup_location', 'name' => 'pickup_location', 'title' => 'Pickup Location'],
            ['data' => 'drop_location', 'name' => 'drop_location', 'title' => 'Drop Location'],
            ['data' => 'total_fare', 'name' => 'id', 'title' => 'Total Fare'],
            ['data' => 'owe_amount', 'name' => 'id', 'title' => 'Owe Amount'],
            ['data' => 'remaining_owe_amount', 'name' => 'total_fare', 'title' => 'RemainingOwe Amount'],
            ['data' => 'begin_trip_custom', 'name' => 'begin_trip_custom', 'title' => 'Start Time', 'orderable' => false],
            ['data' => 'end_trip_custom', 'name' => 'end_trip_custom', 'title' => 'End Time', 'orderable' => false],
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
        return 'owe_trip_list_' . date('YmdHis');
    }
    

}
