<?php

/**
 * Vehicle DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Vehicle
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Vehicle;
use Yajra\DataTables\Services\DataTable;
use DB;

class VehicleDataTable extends DataTable
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
            ->addColumn('vehicle_number', function ($query) {
                return vehicle_number_en($query->vehicle_number);
            })
            ->addColumn('vehicle_type', function ($vehicle) {
                return "<div id='vehicle_type".$vehicle->id."' >".$vehicle->vehicle_type."</div>";
            })
            ->addColumn('change', function ($vehicle) {
                if($vehicle->vehicle_type == 'Regular') {
                    return "<span class='btn btn-primary btn-xs change_vehicle_type' id='change_by".$vehicle->id."' onClick=\"change_vehicle_type('".$vehicle->id."','Premier')\" >Premier</span>";
                }
                else if($vehicle->vehicle_type == 'Premier') {
                    return "<span class='btn btn-success btn-xs change_vehicle_type' id='change_by".$vehicle->id."' onClick=\"change_vehicle_type('".$vehicle->id."','Regular')\"
                    >Regular</span>"; 
                }
            })
            ->addColumn('action', function ($vehicle) {
                // $edit = '<a href="'.url(LOGIN_USER_TYPE.'/edit_vehicle/'.$vehicle->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>' ;
                // $delete = '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_vehicle/'.$vehicle->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';

               if (LOGIN_USER_TYPE == 'admin'){
                $view = (auth('admin')->user()->can('view_vehicle_make')) ? '<a href="'.url(LOGIN_USER_TYPE.'/view_vehicle/'.$vehicle->id).'" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-zoom-in" title="View Vehicle"></i></a>' : '';

                $edit = (auth('admin')->user()->can('update_vehicle')) ? '<a href="'.url(LOGIN_USER_TYPE.'/edit_vehicle/'.$vehicle->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit" title="Edit Vehicle"></i></a>' : '';

                $delete = (auth('admin')->user()->can('delete_vehicle')) ? '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_vehicle/'.$vehicle->id).'" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#confirm-delete" title="Delete Vehicle"><i class="glyphicon glyphicon-trash"></i></a>' : '';

                $change_vehicle = (auth('admin')->user()->can('change_vehicle')) ? '<a href="'.url(LOGIN_USER_TYPE.'/vehicle/change_vehicle/'.$vehicle->id).'" class="btn btn-xs btn-info" style="margin-bottom:5px;" title="Change Vehicle"><i class="fa fa fa-car"></i></a>&nbsp;' : '';

                return @$view.'&nbsp'.@$edit.'&nbsp'.@$delete.'&nbsp'.@$change_vehicle;


               } else if (LOGIN_USER_TYPE == 'company') {
                $edit =  '<a href="'.url(LOGIN_USER_TYPE.'/edit_vehicle/'.$vehicle->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>' ;
                //$delete = '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_vehicle/'.$vehicle->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>' ;

                $change_vehicle = '<a href="'.url(LOGIN_USER_TYPE.'/vehicle/change_vehicle/'.$vehicle->id).'" class="btn btn-xs btn-info" style="margin-bottom:5px;" title="Change Vehicle"><i class="fa fa fa-car"></i></a>';

                return @$edit.'&nbsp'.@$delete.'&nbsp'.@$change_vehicle;
               }                
            })            
            ->addColumn('default_type', function ($vehicle) {
                if($vehicle->default_type == '1') return "<i class='glyphicon glyphicon-ok text-success'></i>";
                else return "<i class='glyphicon glyphicon-remove'></i>";
            })          
            ->addColumn('driver_name', function ($vehicle) {
                return '<a href="'.url(LOGIN_USER_TYPE.'/driver/profile/'.$vehicle->user_id).'" target="_blank">'.$vehicle->driver_name.'</a>';
            })         
            ->addColumn('vehicle_number', function ($vehicle) {
                return '<a href="'.url(LOGIN_USER_TYPE.'/edit_vehicle/'.$vehicle->id).'" target="_blank">'.$vehicle->vehicle_number.'</a>';
            })

            
            ->filterColumn('vehicle_number', function ($query, $keyword) {
                $keywords = vehicle_number_bn(trim($keyword));
                $query->whereRaw("vehicle_number like ?", ["%{$keywords}%"]);
            })
            ->filterColumn('driver_name', function ($query, $keyword) {
                $keywords = trim($keyword);
                $query->whereRaw("CONCAT(first_name, last_name) like ?", ["%{$keywords}%"]);
             })
            ->rawcolumns(['vehicle_number','driver_name','action','vehicle_type', 'change', 'default_type','driver_name','vehicle_number'])
            
            ->addIndexColumn();
    }

    /**
     * Get query source of dataTable.
     *
     * @param Vehicle $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Vehicle $model)
    {
        $vehicles = $model->join('users', function ($join) {
                $join->on('users.id', '=', 'vehicle.user_id');
            })
            ->leftJoin('companies', function ($join) {
                $join->on('companies.id', '=', 'vehicle.company_id');
            })
            ->select('vehicle.id as id',
                'vehicle.user_id as user_id',
                'vehicle.status as status',
                'vehicle.year as year',
                'vehicle.vehicle_name as vehicle_name',
                'vehicle.vehicle_number as vehicle_number',
                'vehicle.vehicle_type',
                'vehicle.default_type',
                'companies.name as company_name',
                DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
             );

        //If login user is company then get that company vehicles only
        if (LOGIN_USER_TYPE=='company') {
            $vehicles = $vehicles->where('vehicle.company_id',auth()->guard('company')->user()->id);
        }

        if($this->vehicle_type !='') $vehicles = $vehicles->where('vehicle.vehicle_type', $this->vehicle_type);
        if($this->year !='') $vehicles = $vehicles->where('vehicle.year', $this->year);
        if($this->vehicle_number !='') $vehicles = $vehicles->where('vehicle.vehicle_number', $this->vehicle_number);
        if($this->user_id !='') $vehicles = $vehicles->where('vehicle.user_id', $this->user_id);

       
        return $vehicles;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        // echo $this->vehicle_type;
        // exit;
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('lBfr<"table-responsive"t>ip')
                    ->parameters(['order' => [1, 'DESC']])
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
            ['data' => 'id', 'name' => 'vehicle.id', 'title' => 'Vehicle Id'],
            ['data' => 'company_name', 'name' => 'companies.name', 'title' => 'Company Name'],
            ['data' => 'driver_name', 'name' => 'users.first_name', 'title' => 'Driver Name'],
            ['data' => 'user_id', 'name' => 'vehicle.user_id', 'title' => 'Driver ID'],
            ['data' => 'vehicle_name', 'name' => 'vehicle.vehicle_name', 'title' => 'Make / Model'],
            ['data' => 'vehicle_number', 'name' => 'vehicle_number', 'title' => 'Vehicle Number'],
            ['data' => 'status', 'name' => 'vehicle.status', 'title' => 'Status'],
            ['data' => 'year', 'name' => 'vehicle.year', 'title' => 'Year'],
            ['data' => 'vehicle_type', 'name' => 'vehicle.vehicle_type', 'title' => 'Vehicle Type'],
            ['data' => 'default_type', 'name' => 'vehicle.default_type', 'title' => 'Default', 'className' => 'text-center vertical-middle'],
            ['data' => 'change', 'name' => 'change', 'title' => 'Change By', 'className' => 'text-center vertical-middle'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'className' => 'text-center', 'orderable' => false, 'searchable' => false, 'exportable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'vehicles_' . date('YmdHis');
    }
}
