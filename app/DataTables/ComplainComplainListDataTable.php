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

use App\Models\Complain;
use Yajra\DataTables\Services\DataTable;
use DB;

class ComplainComplainListDataTable extends DataTable
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
            ->addColumn('driver', function ($data) {
                return DB::table('users')
                            ->select(
                                DB::raw('CONCAT(first_name, \' \', last_name) AS driver'),
                            )
                            ->where('id', $data->driver_id)
                            ->pluck('driver')
                            ->first();

                //return $data->driver_id;
            })          
            ->addColumn('action', function ($data) {
                //$edit = (auth('admin')->user()->can('edit_complain')) ? '<a href="'.url(LOGIN_USER_TYPE.'/edit_complain/'.$data->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                $movement = (auth('admin')->user()->can('movement_complain')) ? '<a href="'.url(LOGIN_USER_TYPE.'/movement_complain/'.$data->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-plus"></i></a>&nbsp;':'';
                $tracking = (auth('admin')->user()->can('tracking_movement_complain')) ? '<a href="'.url(LOGIN_USER_TYPE.'/tracking_movement_complain/'.$data->id).'" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-flash"></i></a>&nbsp;':'';
                return $movement.$tracking;
            })
            ->filterColumn('rider', function ($query, $keyword) {
                $keywords = trim($keyword);
                $query->whereRaw("CONCAT(first_name, last_name) like ?", ["%{$keywords}%"]);
             })
            ->rawcolumns(['action'])
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
        $complain = DB::table('complains')
                        ->select(
                            'complains.id as id',
                            'complains.trip_id',
                            'complains.pickup_location',
                            'complains.drop_location',
                            'complains.complain_content',
                            'complains.complain_by',
                            'complains.vehicle_number as vehicle_number',
                            
                            'complains.vehicle_id as vehicle_id',
                            'complains.rider_id as rider_id',
                            'complains.driver_id as driver_id',
                             
                            'complain_categories.category',
                            'complain_sub_categories.sub_category',
                            DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS rider'),
                            DB::raw("(
                                CASE
                                WHEN complains.status='0' THEN 'Pending'
                                WHEN complains.status='1' THEN 'Completed'
                                WHEN complains.status='2' THEN 'Processing'
                                ELSE '' 
                                END
                             ) AS status"),
                            DB::raw('DATE_FORMAT(complains.created_at, "%d-%b-%Y, %h:%i %p") as created_at')
                        )
                        ->leftJoin('complain_categories', function($join) {
                            $join->on('complains.cat_id', '=', 'complain_categories.id');
                        })
                        ->leftJoin('complain_sub_categories', function($join) {
                            $join->on('complains.sub_cat_id', '=', 'complain_sub_categories.id');
                        })
                        ->leftJoin('users', function($join) {
                            $join->on('complains.rider_id', '=', 'users.id');
                        });

        if($this->trip_id !='') $complain = $complain->where('complains.trip_id', $this->trip_id);
        else{
            
            if($this->complain_by !='') $complain = $complain->where('complains.complain_by', $this->complain_by);
            if($this->driver_id !='') $complain = $complain->where('complains.driver_id', $this->driver_id);
            if($this->rider_id !='') $complain = $complain->where('complains.rider_id', $this->rider_id);
            if($this->cat_id !='') $complain = $complain->where('complains.cat_id', $this->cat_id);
            if($this->sub_cat_id !='') $complain = $complain->where('complains.sub_cat_id', $this->sub_cat_id);
            if($this->status !='') $complain = $complain->where('complains.status', $this->status);
            
            if($this->start_date !='')  {
                list($d, $m, $y) = explode('-', $this->start_date);
                $date1 =  $y.'-'.$m.'-'.$d;
                $complain = $complain->whereDate('complains.created_at', '>=', $date1);
            }
            if($this->end_date !='')  {
                list($d, $m, $y) = explode('-', $this->end_date);
                $date2 =  $y.'-'.$m.'-'.$d;
                $complain = $complain->whereDate('complains.created_at', '<=', $date2);
            }
        }
        return $complain;
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
            ['data' => 'complain_by', 'name' => 'complains.complain_by', 'title' => 'Complain by'],            
            ['data' => 'trip_id', 'name' => 'complains.trip_id', 'title' => 'Trip ID'],
            ['data' => 'rider', 'name' => 'rider', 'title' => 'Rider'],
            ['data' => 'driver', 'name' => 'driver', 'title' => 'Driver'],
            ['data' => 'vehicle_number', 'name' => 'vehicle_number', 'title' => 'Vehicle Number'],
            ['data' => 'pickup_location', 'name' => 'complains.pickup_location', 'title' => 'Pickup Location'],
            ['data' => 'drop_location', 'name' => 'complains.drop_location', 'title' => 'Drop Location'],
            ['data' => 'complain_content', 'name' => 'complains.complain_content', 'title' => 'Complain'],
            ['data' => 'category', 'name' => 'complain_categories.category', 'title' => 'Category'],
            ['data' => 'sub_category', 'name' => 'complain_sub_categories.sub_category', 'title' => 'Sub Category'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created at'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
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
        return 'complain_category_' . date('YmdHis');
    }
    

}
