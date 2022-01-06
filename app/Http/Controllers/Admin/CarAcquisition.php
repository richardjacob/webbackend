<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\HubAcquisitionListDataTable;
use App\Models\Hub;
use App\Models\HubEmployee;
use DB;
use Validator;
use Auth;
use Route;
use Hash;

class CarAcquisition extends Controller
{
    // public function index(Request $request, HubAcquisitionListDataTable $dataTable)
    // {
    //     $hub_id = $request->hub_id;
    //     $employee_id = $request->employee_id;
    //     $code = $request->code;
        

    //     if($request->start_date !='') $start_date = date('Y-m-d', strtotime($request->start_date));
    //     else $start_date = date('Y-m-d');

    //     if($request->end_date !='') $end_date = date('Y-m-d', strtotime($request->end_date)); 
    //     else $end_date = date('Y-m-d');

    //     $array['hub_id']         = $hub_id ?? '';
    //     $array['employee_id']    = $employee_id ?? '';
    //     $array['code']           = $code ?? '';
    //     $array['start_date']     = $start_date;
    //     $array['end_date']       = $end_date;
    //     $array['hub_list']       = Hub::where('status', 'Active')->get();
                 
    //     $data = $array;
    //     $data['employee_list']   = HubEmployee::where('hub_id', $hub_id)
    //                                           ->select('id','employee_name')
    //                                           ->get();

    //     return $dataTable->with($array)->render('admin.hub_employee.hub_acquisition_list', $data);
    // }

    public function index(Request $request)
    {
        $hub_id = $request->hub_id;
        $employee_id = $request->employee_id;
        $code = $request->code;
        $print = $request->print;
        $per_page = $request->per_page ?? 20;
        $page = $request->page;

        if($request->start_date !=''){
            $start_date = date('Y-m-d', strtotime($request->start_date));
        }
        else $start_date = date('Y-m-d');

        if($request->end_date !=''){
            $end_date = date('Y-m-d', strtotime($request->end_date)); 
        }
        else $end_date = date('Y-m-d');

        DB::statement(DB::raw('set @serial=0'));
        $list =  DB::Table('users')->select(
                'users.id as id',
                'hub_employees.employee_name as employee_name',
                'hub_employees.mobile_number as employee_mobile_number',
                'hub_employees.refaral_id as refaral_id',
                'hubs.name as hub_name',
                DB::raw('DATE_FORMAT(users.created_at, "%d-%b-%Y") as created_at'),
                DB::raw('@serial  := @serial  + 1 AS serial'),
                DB::raw("CONCAT(users.first_name,' ',users.last_name) AS driver_name"),
                DB::raw("CONCAT('0',users.mobile_number) AS mobile_number")
        )
        ->leftJoin('hubs', function($join) {
            $join->on('users.hub_id', '=', 'hubs.id');
        })
        ->leftJoin('hub_employees', function($join) {
            $join->on('users.hub_employee_id', '=', 'hub_employees.id');
        })
        ->where('users.user_type', 'driver')
        ->where('users.hub_id', '!=', '')
        ->where('users.hub_employee_id', '!=', '');
        
        if($code !='') {
            $employee = HubEmployee::where('refaral_id', $code)->first();
            if($employee !='') $list = $list->where('users.hub_employee_id', $employee->id);
        }
        else{
            if($hub_id !='') {
                $data['hub_name'] = Hub::find($hub_id)->name;
                $list = $list->where('users.hub_id',  $hub_id);
            }
            if($employee_id !='') {
                if($employee_id !='') $data['employee_name'] = HubEmployee::find($employee_id)->employee_name;
                $list = $list->where('users.hub_employee_id',  $employee_id);
            }
        }

        if($start_date !='') {
            $list = $list->whereDate('users.created_at', '>=', $start_date);
        }
        if($end_date !='') {
            $list = $list->whereDate('users.created_at', '<=', $end_date);
        }
        

        $data['per_page']       = $per_page;
        $data['hub_id']         = $hub_id ?? '';
        $data['employee_id']    = $employee_id ?? '';
        $data['code']           = $code ?? '';
        $data['start_date']     = $start_date;
        $data['end_date']       = $end_date;

        $data['hub_list']       = Hub::where('status', 'Active')->get();
        $data['employee_list']  = HubEmployee::where('hub_id', $hub_id)
                                              ->select('id','employee_name')
                                              ->get();  

        if($print) { 
            $list = $list->get();
            $data['list'] = $list;
            return view('admin.driver.car_acquisition_print', $data);
        }
        else{            
            $list = $list->paginate($per_page);
            $data['list'] = $list;
            return view('admin.driver.car_acquisition', $data);
        }
    }
    

    public function hub_employee_ajax(Request $request){        
        $employees = HubEmployee::where('hub_id', $request->id)
                                ->select('id','employee_name')
                                ->get();

        $options = ' ';

        foreach ($employees as $k => $employee) {
            if($k == 0) $options = '<option value="">Select Employee</option>';
            $options.='<option value="'.$employee->id.'">'.$employee->employee_name.'</option>';
        }
        return $options;
    }
}
