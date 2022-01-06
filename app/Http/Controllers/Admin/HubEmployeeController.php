<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\HubEmployeeDataTable;
use App\DataTables\HubWiseEmployeeDataTable;
use App\DataTables\HubDriverAcquisitionDataTable;
use App\Models\HubEmployee;
use App\Models\Hub;
use App\Models\Role;
use App\Models\Language;
use DB;
use Validator;
use Auth;
use Route;
use Hash;

class HubEmployeeController extends Controller
{
    public function __construct()
    {
        $this->permission_helper = resolve('App\Http\Helper\HubHelper');
    }
    

    public function index(HubEmployeeDataTable $dataTable)
    {
        return $dataTable->render('admin.hub_employee.view');
    }

    public function add(Request $request)
    {
        
        if($request->isMethod('GET')) {
        	$data['hub'] = Hub::where('status','Active')->get();
        	//$data['role'] = Role::where('id', '4')->orWhere('id', '5')->get();
            $data['role'] = Role::all();

        	// return $data;
            return view('admin.hub_employee.add', $data);
        }

        if($request->submit) {  
            
            $this->validate($request,[
                'employee_name'=> 'required',
                'email'    => 'required',
                'password'    =>  ['required','string','min:8','regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/',],
                'hub_id'=> 'required',
                'role_id'=> 'required',
                'mobile_number'=> 'required|numeric|unique:hub_employees|regex:/\+?(88)?0?1[356789][0-9]{8}\b/',
                'status'=> 'required'
             ]);

        $hub_manage_employee = new HubEmployee();
        $hub_manage_employee->employee_name = $request->employee_name;
        $hub_manage_employee->email = $request->email;
        $hub_manage_employee->password = $request->password;
        $hub_manage_employee->hub_id  = $request->hub_id;
        $hub_manage_employee->role_id  = $request->role_id;
        $hub_manage_employee->mobile_number  = $request->mobile_number;
        $hub_manage_employee->status = $request->status;
        $hub_manage_employee->save();

        $id = $hub_manage_employee->id;

        $total_employees = DB::table('hub_employees')
                            ->where('hub_id', $request->hub_id)
                            ->count();  

        $refaral_id = $request->hub_id;
        if($total_employees < 10) $refaral_id = $refaral_id.'00'.$total_employees;
        else if($total_employees < 100) $refaral_id = $refaral_id.'0'.$total_employees;
        else $refaral_id = $refaral_id.$total_employees;

        $update_hub_manage_employee = HubEmployee::find($id);
        $update_hub_manage_employee->refaral_id = $refaral_id;
        $update_hub_manage_employee->save();



        flashMessage('success', 'Added Successfully');
        }
        
        return redirect('admin/manage_employee');
    }

    public function update(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['languages'] = Language::where('status', '=', 'Active')->pluck('name', 'value');
            $data['result'] = HubEmployee::findOrFail($request->id);
            $data['hub'] = Hub::where('status','Active')->pluck('name','id');
            $data['role'] = Role::all();
            return view('admin.hub_employee.edit', $data);
            // return $data;
        }
        else if($request->submit) {
            // Edit Help Validation Rules
            // return $request;
            $this->validate($request,[
                'employee_name'=> 'required',
                'email'    => 'required',
                // 'password'    =>  ['required','string','min:8','regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/',],
                'hub_id'=> 'required',
                'role_id'=> 'required',
                'mobile_number'=> 'required|numeric|regex:/\+?(88)?0?1[356789][0-9]{8}\b/',
                'status'=> 'required'
             ]);

            $hub_manage_employee = HubEmployee::findOrFail($request->id);
            $hub_manage_employee->employee_name  = $request->employee_name;
            $hub_manage_employee->email  = $request->email;

            if($request->password != '') {
                $hub_manage_employee->password = $request->password;
            }
            $hub_manage_employee->role_id = $request->role_id;
            $hub_manage_employee->hub_id = $request->hub_id;
            $hub_manage_employee->mobile_number = $request->mobile_number;
            $hub_manage_employee->status = $request->status;
            $hub_manage_employee->save();

            flashMessage('success', 'Updated Successfully');
        }
        return redirect('admin/manage_employee');
    }
    public function delete(Request $request)
    {
        $hub_employee = HubEmployee::findOrFail($request->id);
        $hub_employee->delete();

        flashMessage('success', 'Deleted Successfully');
        return redirect('admin/manage_employee');
    }

    public function employeelist(HubWiseEmployeeDataTable $dataTable){
        if($this->permission_helper->check_permission() == 1) 
        return $dataTable->render('admin.hub_employee_login.view');
    }

    public function acquisitionList(Request $request, HubDriverAcquisitionDataTable $dataTable)
    {
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');

        if($request->start_date !='') $start_date = date('Y-m-d', strtotime($request->start_date));
        if($request->end_date !='') $end_date = date('Y-m-d', strtotime($request->end_date));

        $array = array(
                    'start_date' => $start_date, 
                    'end_date' => $end_date
                );

        //if($this->permission_helper->check_permission() == 1){
            return $dataTable->with($array)->render('admin.hub_driver_acquisition.view', $array);
        //} 
        
    }
}
