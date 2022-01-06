<?php

/**
 * Documents Controller
 *
 * @package     Makent
 * @subpackage  Controller
 * @category    Documents
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\ComplainComplainListDataTable;
use Validator;
use App\Models\Complain;
use App\Models\ComplainMovement;
use App\Models\ComplainCategory;
use App\Models\ComplainSubCategory;
use App\Models\Trips;

use App\Http\Start\Helpers;
use DB;
use Auth;


class ComplainListController extends Controller
{
    protected $helper; 
    public function __construct()
    {
        $this->helper = new Helpers;
        $this->request_helper = resolve('App\Http\Helper\RequestHelper');
    }

    /**
     * Load Datatable for Complain
     *
     * @param array $dataTable  Instance of ComplainComplainListDataTable
     * @return datatable
     */
    public function index(ComplainComplainListDataTable $dataTable, Request $r)
    {
        $cat_list = ComplainCategory::get()->pluck('category', 'id')->toArray(); 
        $sub_cat_list = ComplainSubCategory::where('complain_cat_id', $r->cat_id)->get()->pluck('sub_category', 'id')->toArray();  

        $complain_by = $r->complain_by;
        $driver_id   = $r->driver_id;
        $rider_id    = $r->rider_id;
        $trip_id     = $r->trip_id;
        $cat_id      = $r->cat_id;
        $sub_cat_id  = $r->sub_cat_id;
        $status      = $r->status;
        $start_date  = $r->start_date;
        $end_date    = $r->end_date;

        $data['complain_by']  = $complain_by;
        $data['driver_id']    = $driver_id;
        $data['rider_id']     = $rider_id;
        $data['trip_id']      = $trip_id;
        $data['cat_id']       = $cat_id;
        $data['sub_cat_id']   = $sub_cat_id;
        $data['status']       = $status;
        $data['start_date']   = $start_date;
        $data['end_date']     = $end_date;
        $data['cat_list']     = $cat_list;
        $data['sub_cat_list'] = $sub_cat_list;
        
        
        $array = array(
            'complain_by' => $complain_by,
            'driver_id'   => $driver_id,
            'rider_id'    => $rider_id,
            'trip_id'     => $trip_id,
            'cat_id'      => $cat_id,
            'sub_cat_id'  => $sub_cat_id,
            'status'      => $status,
            'start_date'  => $start_date,
            'end_date'    => $end_date,
        );

        return $dataTable->with($array)->render('admin.complain.view', $data);
    }


    /**
     * Load Datatable for Complain
     *
     * @param array $dataTable  Instance of ComplainComplainListDataTable
     * @return datatable
     */
    public function movement_complain(Request $request)
    {   
        if($request->isMethod("GET")) {       	
            $data['result'] = Complain::find($request->id);  
            $data['last_record'] = ComplainMovement::where('complain_id', $request->id)->orderBy('id', 'DESC')->first();  

            return view('admin.complain.movement_complain',$data);
        } 
        elseif ($request->submit) {

            $rules = array(
                'process_by' => 'required',
                'process' => 'required',  
                'remarks' => 'required',                   
                'status' => 'required'           
            );
       
            $validator = Validator::make($request->all(), $rules);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $table              = new ComplainMovement;
            $table->complain_id = $request->id;
            $table->process_by  = $request->process_by;
            $table->process     = $request->process;
            $table->remarks     = $request->remarks;
            $table->status      = $request->status;
            $table->entry_by_id = Auth::guard('admin')->user()->id;
            $table->updated_by_id = Auth::guard('admin')->user()->id;
            $table->save();

            $table_complain  = Complain::find($request->id);
            if( $request->status == '1' ){
                $table_complain->status = '1';
            } else {
                $table_complain->status = '2';
            }
            $table_complain->save();


            $complain = Complain::find($request->id);    
            $trip = Trips::find($complain->trip_id);
            $push_data['push_title'] = $request->process;
            $push_data['data'] = array(
                'arrive_now' => array(
                    'status' => $request->status,
                    'trip_id' => $complain->trip_id,
                )
            );
            $this->request_helper->SendPushNotification($trip->users, $push_data);

            flashMessage('success', __('messages.user.add_success'));
        }
        return redirect('admin/complain_list');

    }


    /**
     * Edit Movement of complain
     *
     * @param array $request Input values
     * @return redirect to complain view
     */
    public function edit_movement_complain(Request $request)
    {
        $rules = array(
            'process_by' => 'required',
            'process' => 'required',  
            'remarks' => 'required',                   
            'status' => 'required'           
        );
    
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $table                = ComplainMovement::find($request->complain_movement_id);
        $table->process_by    = $request->process_by;
        $table->process       = $request->process;
        $table->remarks       = $request->remarks;
        $table->status        = $request->status;
        $table->updated_by_id = Auth::guard('admin')->user()->id;
        $table->save();

        $table_complain  = Complain::find($table->complain_id);
        if( $request->status == '1' ){
            $table_complain->status = '1';
        } else {
            $table_complain->status = '2';
        }
        $table_complain->save();

        flashMessage('success', __('messages.user.update_success'));
        return redirect('admin/complain_list');
    }

    public function tracking_movement_complain(Request $request){   
        $result = DB::table('complains')
                        ->select(
                            'complains.id as id',
                            'complains.trip_id as trip_id',
                            'complains.pickup_location as pickup_location',
                            'complains.drop_location as drop_location',
                            'complains.complain_content as complain_content',
                            'complains.complain_by as complain_by',
                            'complains.vehicle_number as vehicle_number',
                            
                            'complains.vehicle_id as vehicle_id',
                            'complains.rider_id as rider_id',
                            'complains.driver_id as driver_id',
                            
                            'complain_categories.category as category',
                            'complain_sub_categories.sub_category as sub_category',
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
                        })
                        ->where('complains.id', $request->id)
                        ->first();

        $driver = DB::table('users')
                        ->select(DB::raw('CONCAT(first_name, \' \', last_name) AS driver'))
                        ->where('id', $result->driver_id)
                        ->pluck('driver')
                        ->first();
       
        $data['result'] = $result;  
        $data['driver'] = $driver; 
        $data['movement'] = ComplainMovement::where('complain_id', $request->id)->orderBy('id', 'DESC')->get();  
        return view('admin.complain.tracking',$data);
    }
    

    
}
