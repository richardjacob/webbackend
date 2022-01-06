<?php

/**
 * Driver Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Driver
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\DriverRemarksDataTable;
use App\DataTables\DriverRemarksViewDataTable;
use App\Models\User;
use App\Models\DriverRemarks;
use App\Models\Optional;
use DB;
use Validator;
use Auth;

class DriverRemarksController extends Controller
{
    public function __construct()
    {
        $this->processing_status_list = Optional::where('type', 'processing_status')
                                            ->select('value', 'name_en')
                                            ->get();
    }
    public function index(DriverRemarksDataTable $dataTable, $id ='', Request $r)
    { 
        $driver_id = $r->driver_id;
        $start_date = $r->start_date;
        $end_date = $r->end_date;


        $data['status'] = $id;
        $data['driver_id'] = $driver_id;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        
        $array = array(
            'id' => $id,
            'driver_id' => $driver_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        );

        return $dataTable
                    ->with($array)
                    ->render('admin.drivers_remarks.view', $data);
    }

   
    public function add($driver_id='', Request $request)
    {
        $data['processing_status_list'] = $this->processing_status_list;

        if($request->isMethod("GET")) {
            $data['driver_id'] = $driver_id;
            return view('admin.drivers_remarks.add', $data);
        }

        if($request->submit) {
            // Add Driver Validation Rules
            $rules = array(
                'conversation'    => 'required',
                'conversation_date'     => 'required',
                'status'        => 'required',
            );


            // Add Driver Validation Custom Names
            $attributes['conversation']   = trans('messages.remarks.conversation');
            $attributes['conversation_date']    = trans('messages.remarks.conversation_date');
            $attributes['status']     = trans('messages.remarks.status');

            $messages = array(
                'required'            => ':attribute is required.',
            );

            $validator = Validator::make($request->all(), $rules,$messages, $attributes);
            
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $driver_remarks = new DriverRemarks;
            $user = User::where('id', $request->driver_id)
                         ->where('user_type', 'Driver')
                         ->first();

            if($request->conversation_date !=''){
              $conversation_date = date("Y-m-d H:i:s", strtotime(str_replace('/','-', $request->conversation_date)));   
            }
            else $conversation_date = NULL;

            if($request->followup_date !=''){
              $followup_date = date("Y-m-d H:i:s", strtotime(str_replace('/','-', $request->followup_date)));   
            }
            else $followup_date = NULL;

            $driver_remarks->driver_id          = $request->driver_id;
            $driver_remarks->conversation       = $request->conversation;
            $driver_remarks->remarks            = $request->remarks;
            $driver_remarks->conversation_date  = $conversation_date;
            $driver_remarks->followup_date      = $followup_date;
            $driver_remarks->remarks_status     = $request->status;
            $driver_remarks->processing_status  = $request->processing_status;

            if(LOGIN_USER_TYPE == 'hub') {
                $driver_remarks->hub_id   = Auth::guard('hub')->user()->hub_id;
                $driver_remarks->hub_employee_id   = Auth::guard('hub')->user()->id;
            }
            else if(LOGIN_USER_TYPE == 'company') $driver_remarks->company_id   = Auth::guard('company')->user()->id;
            else if(LOGIN_USER_TYPE == 'admin') $driver_remarks->admin_id   = Auth::guard('admin')->user()->id;
           
            $driver_remarks->save();
                     
            flashMessage('success', trans('messages.user.add_success'));
            return redirect(LOGIN_USER_TYPE.'/drivers_remarks');
        }

        return redirect(LOGIN_USER_TYPE.'/drivers_remarks');
    }

    public function update(Request $request)
    {
        $data['processing_status_list'] = $this->processing_status_list;
        
        if($request->isMethod("GET")) {
            $data['result']  = DriverRemarks::find($request->id);

            if($data['result']) {
                return view('admin.drivers_remarks.edit', $data);
            } else {
                flashMessage('danger', 'Invalid ID');
                return redirect(LOGIN_USER_TYPE.'/drivers_remarks'); 
            }
        }
        
        if($request->submit) {
            $rules = array(
                'conversation'    => 'required',
                'conversation_date'     => 'required',
                'status'        => 'required',
            );

            $attributes['conversation']   = trans('messages.remarks.conversation');
            $attributes['conversation_date']    = trans('messages.remarks.conversation_date');
            $attributes['status']     = trans('messages.remarks.status');

            $messages = array(
                'required'            => ':attribute is required.',
            );

            $validator = Validator::make($request->all(), $rules,$messages, $attributes);
            
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
           
            $driver_remarks = DriverRemarks::find($request->id);

            if($request->conversation_date !=''){
              $conversation_date = date("Y-m-d H:i:s", strtotime(str_replace('/','-', $request->conversation_date)));   
            }
            else $conversation_date = NULL;

            if($request->followup_date !=''){
              $followup_date = date("Y-m-d H:i:s", strtotime(str_replace('/','-', $request->followup_date)));   
            }
            else $followup_date = NULL;

            $driver_remarks->driver_id          = $request->driver_id;
            $driver_remarks->conversation       = $request->conversation;
            $driver_remarks->remarks            = $request->remarks;
            $driver_remarks->conversation_date  = $conversation_date;
            $driver_remarks->followup_date      = $followup_date;
            $driver_remarks->remarks_status     = $request->status;
            $driver_remarks->processing_status  = $request->processing_status;

            if(LOGIN_USER_TYPE == 'hub') {
                $driver_remarks->hub_id   = Auth::guard('hub')->user()->hub_id;
                $driver_remarks->hub_employee_id   = Auth::guard('hub')->user()->id;
            }
            else if(LOGIN_USER_TYPE == 'company') $driver_remarks->company_id   = Auth::guard('company')->user()->id;
            else if(LOGIN_USER_TYPE == 'admin') $driver_remarks->admin_id   = Auth::guard('admin')->user()->id;
           
            $driver_remarks->save();
            flashMessage('success', 'Updated Successfully');
        }
        return redirect(LOGIN_USER_TYPE.'/drivers_remarks');
    }

    public function view(DriverRemarksViewDataTable $dataTable, $driver_id)
    {
        

        if(LOGIN_USER_TYPE=='company' || LOGIN_USER_TYPE=='hub' || auth('admin')->user()->can('view_drivers_remarks')){
            //echo $driver_id;
            //return $dataTable->render('admin.drivers_remarks.view');
            $user_object = User::where('id', $driver_id)
                            ->where('user_type', 'Driver')
                            ->first();

            if(is_object($user_object)){
                $user = array(
                    'name' => $user_object->first_name.' '.$user_object->last_name,
                    'mobile' => $user_object->mobile_number,
                    'status' => $user_object->status,
                    'driver_id' => $driver_id
                );
            }else{
                $user = array(
                    'name' => '',
                    'mobile' => '',
                    'status' => '',
                    'driver_id' => ''
                );
            }
            return $dataTable->with('driver_id', $driver_id)
                                    ->render('admin.drivers_remarks.view', $user);


        }else {
            flashMessage('danger', trans('messages.remarks.view_permission'));
            return redirect(LOGIN_USER_TYPE.'/drivers_remarks');
        }
    }

   

}
