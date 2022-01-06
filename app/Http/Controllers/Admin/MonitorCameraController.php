<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\MonitorCameraDataTable;
use DB;
use App\Models\User;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\MonitorCamera;
use Validator;

class MonitorCameraController extends Controller
{
    public function index(MonitorCameraDataTable $dataTable, $id = '')
    { 
        return $dataTable->render('admin.driver_monitor_camera.view');
    }
    public function add(Request $request)
    {
    	if($request->isMethod("GET")) {

            //Inactive Company could not add driver
            	if (LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->status != 'Active') {
                abort(404);
            }

            $results = User::Where('user_type','Driver')->Where('status','Active')->get();
            $driver_info = $results->map( function($user) {
                return $user->first_name.' - '.$user->mobile_number;
            })->toArray();
            $data['email_address_list'] = json_encode($driver_info);
        	// return $data;
            return view('admin.driver_monitor_camera.add', $data);
        }
        if($request->submit) {
            // Add Driver Validation Rules
            // return $request;
        $this->validate($request,[
                'driver_id'=>'required',
                'vehicle_id'=>'required|unique:monitor_cameras',
                // 'monitor_sim'=>'required|numeric|regex:/\+?(88)?0?1[356789][0-9]{8}\b/',
                // 'monitor_imei'=>'required|alpha_dash|min:2',
                // 'monitor_ip'=>'required|ip',
                // 'monitor_status'=>'required',
                // 'camera_sim'=>'required|numeric|regex:/\+?(88)?0?1[356789][0-9]{8}\b/',
                // 'camera_imei'=>'required|alpha_dash|min:2',
                // 'camera_ip'=>'required|ip',
                // 'camera_status'=>'required',
            ]);

        $driver_monitor_cameras = new MonitorCamera();
        $driver_monitor_cameras->driver_id = $request->driver_id;
        $driver_monitor_cameras->vehicle_id = $request->vehicle_id;
        $driver_monitor_cameras->monitor_sim = $request->monitor_sim;
        $driver_monitor_cameras->monitor_imei  = $request->monitor_imei;
        $driver_monitor_cameras->monitor_ip  = $request->monitor_ip;
        $driver_monitor_cameras->monitor_status  = $request->monitor_status;
        $driver_monitor_cameras->camera_sim = $request->camera_sim;
        $driver_monitor_cameras->camera_imei  = $request->camera_imei;
        $driver_monitor_cameras->camera_ip  = $request->camera_ip;
        $driver_monitor_cameras->camera_status  = $request->camera_status;
        $driver_monitor_cameras->save();

        flashMessage('success', 'Added Successfully');
        }
        
        return redirect('admin/monitor_camera');
        
    }

    public function update(Request $request)
    {
        if($request->isMethod("GET")) {
            $result = MonitorCamera::findOrFail($request->id);

            $vehicles = Vehicle::where('user_id', $result->driver_id)->get();
            $vehicle_data = array();
            foreach($vehicles as $vehicle){
                $vehicle_data[$vehicle->id] = $vehicle->vehicle_name;
            }

            $data['result'] = $result;
            $data['vehicle'] = $vehicle_data;
            return view('admin.driver_monitor_camera.edit', $data);
        }
        if($request->submit) {
            // Add Driver Validation Rules
            $this->validate($request,[
                'driver_id'=>'required',
                'vehicle_id'=>'required',
                // 'monitor_sim'=>'required|numeric|regex:/\+?(88)?0?1[356789][0-9]{8}\b/',
                // 'monitor_imei'=>'required|alpha_dash|min:2',
                // 'monitor_ip'=>'required|ip',
                // 'monitor_status'=>'required',
                // 'camera_sim'=>'required|numeric|regex:/\+?(88)?0?1[356789][0-9]{8}\b/',
                // 'camera_imei'=>'required|alpha_dash|min:2',
                // 'camera_ip'=>'required|ip',
                // 'camera_status'=>'required',
                ]);

        $driver_monitor_cameras =MonitorCamera::findOrFail($request->id);
        $driver_monitor_cameras->driver_id = $request->driver_id;
        $driver_monitor_cameras->vehicle_id = $request->vehicle_id;
        $driver_monitor_cameras->monitor_sim = $request->monitor_sim;
        $driver_monitor_cameras->monitor_imei  = $request->monitor_imei;
        $driver_monitor_cameras->monitor_ip  = $request->monitor_ip;
        $driver_monitor_cameras->monitor_status  = $request->monitor_status;
        $driver_monitor_cameras->camera_sim = $request->camera_sim;
        $driver_monitor_cameras->camera_imei  = $request->camera_imei;
        $driver_monitor_cameras->camera_ip  = $request->camera_ip;
        $driver_monitor_cameras->camera_status  = $request->camera_status;
        $driver_monitor_cameras->save();
            
            flashMessage('success', 'Updated Successfully');
        }
        return redirect('admin/monitor_camera');
        
    }


	public function suggestion(Request $r)
	{
	    $keywords = $r->keywords;
	    $first = substr($keywords,0,1);
	    if($first == "0") $keywords = substr($keywords,1);

	    if($keywords !=''){
	        $list = DB::table('users')
                        ->where('user_type', 'Driver');

	        $list = $list->where(function($query) use ($keywords){
	        	$query->orWhere('first_name', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('last_name', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('email', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('mobile_number', 'LIKE', '%'.$keywords.'%');
	        });
	        $list = $list->get();

	        $output = "";

	        foreach($list as $sl => $data){
	            if($sl == 0) $output.= '<ul id="suggesstion_list">';

	            $label = $data->first_name." ".$data->last_name. "(".$data->mobile_number.")";
	            $val = $data->id;

	            $vehicles = Vehicle::where('user_id', $val)->get();
	            $option = "";
	            foreach ($vehicles as $vehicle) {
	            	$option.= $vehicle->id."_".$vehicle->vehicle_name."|";
	            }
	            $option = rtrim($option, '|');
	  
	            $output.= '<li onclick=\'select_from_suggestion("'.$label.'", "'.$val.'", "'.$option.'")\'> '.$label.'</li>';
                

	            if(count($list) == $sl + 1)  $output.= '</ul>';
	        }
	        echo $output; 
	    }
	}

    public function suggestion_rider(Request $r)
    {
        $keywords = $r->keywords;
        $first = substr($keywords,0,1);
        if($first == "0") $keywords = substr($keywords,1);

        if($keywords !=''){
            $list = User::leftJoin('wallet', 'users.id', '=', 'wallet.user_id')
                        ->whereNull('wallet.user_id')
                        ->where('users.user_type', 'Rider')
                        ->whereStatus('Active');

            $list = $list->where(function($query) use ($keywords){
                $query->orWhere('first_name', 'LIKE', '%'.$keywords.'%');
                $query->orWhere('last_name', 'LIKE', '%'.$keywords.'%');
                $query->orWhere('email', 'LIKE', '%'.$keywords.'%');
                $query->orWhere('mobile_number', 'LIKE', '%'.$keywords.'%');
            });
            $list = $list->select('users.first_name','users.last_name','users.mobile_number', 'users.id')->get();

            $output = "";
            foreach($list as $sl => $data){
                if($sl == 0) $output.= '<ul id="suggesstion_list">';

                $label = $data->first_name." ".$data->last_name. "(".$data->mobile_number.")";
                $val = $data->id;
              
      
                $output.= '<li onclick=\'select_from_suggestion("'.$label.'", "'.$val.'")\'> '.$label.'</li>';            

                if(count($list) == $sl + 1)  $output.= '</ul>';
            }
            echo $output; 
        }
    }


    

}