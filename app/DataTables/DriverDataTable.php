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

use App\Models\User;
use App\Models\Rating;
use App\Models\DriverRemarks;
use App\Models\Vehicle;
use Yajra\DataTables\Services\DataTable;
use DB;

class DriverDataTable extends DataTable
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
            ->addColumn('rating', function ($users) {
                $total_rating = DB::table('rating')
                                ->select(DB::raw('sum(rider_rating) as rating'))
                                ->where('driver_id', $users->id)
                                ->where('rider_rating', '>', 0)
                                ->first()
                                ->rating;

                $total_rating_count = Rating::where('driver_id', $users->id)
                                            ->where('rider_rating','>', 0)
                                            ->get()
                                            ->count();

                $driver_rating = '0.00';
                if ($total_rating_count != 0) {
                    $driver_rating = (string) round(($total_rating / $total_rating_count), 2);
                }

                return $driver_rating;
            })
            ->addColumn('activity', function ($users) {
                $last_updated_at = DB::table('activities')
                                        ->where('user_id',  $users->id)
                                        ->orderBy('id', 'DESC')
                                        ->pluck('updated_at')
                                        ->first();
                if($last_updated_at !=''){
                    $difference = time() - strtotime($last_updated_at);
                    if($difference > 180) return "X";
                    else return "Online";
                }
                else return "X";
            })
            ->addColumn('status', function ($users) {
                return driver_status($users->id);
            }) 
            ->addColumn('remarks', function ($users) {
                $data = DB::table('driver_remarks')
                            ->select(
                                    'driver_remarks.id as id',
                                    'driver_remarks.conversation as conversation',
                                    'driver_remarks.remarks as remarks',
                                    'driver_remarks.conversation_date as conversation_date',
                                    'driver_remarks.followup_date as followup_date',
                                    'driver_remarks.remarks_status as remarks_status',
                                    'hub_employees.employee_name as employee',
                                    'hub_employees.refaral_id as refaral_id',
                                )
                            ->leftJoin('hub_employees', function($join) {
                                $join->on('driver_remarks.hub_employee_id', '=', 'hub_employees.id');
                            })
                            ->where('driver_remarks.driver_id',  $users->id)
                            ->orderBy('driver_remarks.id', 'DESC')
                            ->first();

                $output = "";

                if(@$data->id !=''){
                    $output.= "<span data-html='true' data-toggle='tooltip' data-placement='top' title='";
                    if(@$data->conversation !='') $output.= 'Conversation : '.@$data->conversation;
                    if(@$data->remarks !='') $output.= '<br />Remarks : '.@$data->remarks;
                    if(@$data->conversation_date !='' AND @$data->conversation_date !='0000-00-00 00:00:00') $output.= '<br />'.'Contact Date : '.@date("d-m-Y h:i A ", strtotime(@$data->conversation_date));
                    if(@$data->followup_date !='' AND @$data->followup_date !='0000-00-00 00:00:00') $output.= '<br />Follow up Date : '.@date("d-m-Y h:i A ", strtotime(@$data->followup_date));
                    if(@$data->employee !='') $output.= '<br />Contact by : '.@$data->employee.' ('.@$data->refaral_id.')';

                    if(@$data->remarks_status !='') {
                        if(@$data->remarks_status =='1') $output.= '<br />Status : Completed';
                        else $output.= '<br />Status : Inprocessing';
                    }
                    $output.="'>";
                }
               

                if(@$data->remarks_status =='1') $output.= "<i class=\"fa fa-check\" aria-hidden=\"true\"></i> ";
                else if(@$data->remarks_status =='0') $output.= "<i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ";
                if(@$data->remarks !='') $output.= $data->remarks;
                
                $output.="</span>";

                return $output;
            })
            ->addColumn('vehicle', function ($users) {
                // $vehicles = Vehicle::where('user_id', $users->id)->select('id','vehicle_number')->get();
                // $output = "";
                // foreach ($vehicles as $vehicle) {
                //     $output.='<a href="'.url(LOGIN_USER_TYPE.'/edit_vehicle/'.$vehicle->id).'">'.vehicle_number_en($vehicle->vehicle_number).'</a><br>';
                // }
                // return $output;

                return driver_vehicle($users->id, 'link', 'en');
            })
            ->addColumn('action', function ($users) {
                $edit = (LOGIN_USER_TYPE=='company' || auth('admin')->user()->can('update_driver')) ? '<a href="'.url(LOGIN_USER_TYPE.'/edit_driver/'.$users->id).'" class="btn btn-xs btn-primary" style="margin-bottom:5px;"><i class="glyphicon glyphicon-edit" title="Edit"></i></a>&nbsp;' : '';
                $delete = (auth()->guard('company')->user()!=null || auth('admin')->user()->can('delete_driver')) ? '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_driver/'.$users->id).'" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#confirm-delete" style="margin-bottom:5px;"><i class="glyphicon glyphicon-trash" title="Delete"></i></a>&nbsp;':'';

                $remarks_id = DriverRemarks::where('driver_id', $users->id)->pluck('id')->first();

                if($remarks_id ==''){

                    $add_remarks = (LOGIN_USER_TYPE=='company' || LOGIN_USER_TYPE=='hub' || auth('admin')->user()->can('add_drivers_remarks')) ? '<a href="'.url(LOGIN_USER_TYPE.'/add_drivers_remarks/'.$users->id).'" class="btn btn-xs btn-success" style="margin-bottom:5px;"><i class="fa fa-comment" title="Add Remarks"></i></a>&nbsp;' : '';
                }
                else{
                    $add_remarks = (LOGIN_USER_TYPE=='company' || LOGIN_USER_TYPE=='hub' || auth('admin')->user()->can('add_drivers_remarks')) ? '<a class="btn btn-xs btn-success" disabled style="margin-bottom:5px;"><i class="fa fa-comment" title="Add Remarks"></i></a>&nbsp;' : '';
                }

                $change_partner = (auth('admin')->user()->can('change_partner')) ? '<a href="'.url(LOGIN_USER_TYPE.'/change_partner/'.$users->id).'" class="btn btn-xs btn-warning" style="margin-bottom:5px;"><i class="fa fa-building" title="Change Partner"></i></a>&nbsp;' : '';

                if(LOGIN_USER_TYPE !='company'){
                    return $edit.$delete.$add_remarks.$change_partner;
                }else return $edit;                
            })
            ->addColumn('driver_name', function ($users) {
                if(LOGIN_USER_TYPE == 'admin'){
                    $add = (auth('admin')->user()->can('view_driver_profile')) ? '<a href="'.url(LOGIN_USER_TYPE.'/driver/profile/'.$users->id).'" target="_blank">'.$users->driver_name.'</a>&nbsp;' : $users->driver_name;
                    return $add;
                }
                else return $users->driver_name;
            })
            ->filterColumn('driver_name', function ($query, $keyword) {
                $keywords = trim($keyword);
                $query->whereRaw("CONCAT(first_name, last_name) like ?", ["%{$keywords}%"]);
             })
            ->rawcolumns(['remarks','vehicle','action','created_at','driver_name'])
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
        /* only for Package */
        /*$users = $model->select('users.id as id', 'users.first_name', 'users.last_name', 'users.email', 'users.country_code', 'users.mobile_number', 'users.status', 'users.user_type', 'users.company_id', 'users.created_at', DB::raw('CONCAT("+",users.country_code," ",users.mobile_number) AS mobile'))
        ->where('user_type','Driver')->groupBy('id');*/

        $users = DB::Table('users')->select(
            'users.id as id',
            // 'users.first_name',
            // 'users.last_name',
            'users.email',
            'users.country_code',
            'users.status',
            'companies.name as company_name',
            //'users.created_at',
            // DB::raw('CONCAT("XXXXXX",Right(users.mobile_number,4)) AS hidden_mobile'),
            DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
            DB::raw('CONCAT(\'0\',users.mobile_number) AS mobile_number'),
            DB::raw("(
                CASE
                WHEN users.gender=1 THEN 'Male'
                WHEN users.gender=2 THEN 'Female'
                WHEN users.gender=3 THEN 'Other'
                ELSE '' 
                END
             ) AS gender"),
            DB::raw('DATE_FORMAT(users.created_at, "%d-%b-%Y<br>%h:%i %p") as created_at')
        )
        ->leftJoin('companies', function($join) {
            $join->on('users.company_id', '=', 'companies.id');
        })->where('users.user_type','Driver')->groupBy('id');

        //If login user is company then get that company drivers only
        if (LOGIN_USER_TYPE=='company') {
            $users = $users->where('users.company_id', auth()->guard('company')->user()->id);
        }

        if($this->driver_id !='') $users = $users->where('users.id', $this->driver_id);
        else{
            if($this->start_date !='')  {
                list($d, $m, $y) = explode('-', $this->start_date);
                $date1 =  $y.'-'.$m.'-'.$d;
                $users = $users->whereDate('users.created_at', '>=', $date1);
            }
            if($this->end_date !='')  {
                list($d, $m, $y) = explode('-', $this->end_date);
                $date2 =  $y.'-'.$m.'-'.$d;
                $users = $users->whereDate('users.created_at', '<=', $date2);
            }
        }
        
        // if (LOGIN_USER_TYPE=='hub') {
        //     $role_id = auth()->guard('hub')->user()->role_id;

        //     if($role_id == '4'){ // manager
        //         $users = $users->where('hub_id',  auth()->guard('hub')->user()->hub_id);
        //     }
        //     else if($role_id == '5'){ // aquisition staff
        //         $users = $users->where('hub_employee_id',auth()->guard('hub')->user()->id);
        //     }        
        // }
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
        // $mobile_number_column = (isLiveEnv())?'hidden_mobile':'mobile_number';
        $columns = [
            ['data' => 'DT_RowIndex', 'orderable' => false, 'title' => 'Serial', 'searchable' => false], 
            ['data' => 'id', 'name' => 'users.id', 'title' => 'Driver ID'],
            ['data' => 'driver_name', 'name' => 'driver_name', 'title' => 'Driver Name'],
        ];
        if (LOGIN_USER_TYPE!='company') {
            $columns[] = ['data' => 'company_name', 'name' => 'companies.name', 'title' => 'Company Name'];
        }
         $more_columns = [
            ['data' => 'email', 'name' => 'users.email', 'title' => 'Email'],
            ['data' => 'gender', 'name' => 'gender', 'title' => 'Gender'],
            ['data' => 'status', 'name' => 'users.status', 'title' => 'Status'],
            ['data' => 'mobile_number', 'name' => 'mobile_number', 'title' => 'Mobile Number'],
            ['data' => 'remarks', 'name' => 'remarks', 'title' => 'Remarks'],
            ['data' => 'vehicle', 'name' => 'vehicle', 'title' => 'Vehicle'],
            ['data' => 'rating', 'name' => 'rating', 'title' => 'Rating', 'orderable' => false, 'searchable' => false, 'exportable' => false],
            ['data' => 'activity', 'name' => 'activity', 'title' => 'Activity' ],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'exportable' => false],
        ];

        return array_merge($columns,$more_columns);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'drivers_' . date('YmdHis');
    }
    

}
