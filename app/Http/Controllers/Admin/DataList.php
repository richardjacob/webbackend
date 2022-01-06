<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trips;
use App\Models\Company;
use App\Models\User;
use App\Models\HubEmployee;
use DB;
use App\DataTables\OweTripListDataTable;
use App\DataTables\CompanyDriverListDataTable;
use App\DataTables\HubDriverAcquisitionDataTable;
use Session;

class DataList extends Controller
{  
    public function owe_trip(OweTripListDataTable $dataTable, $company_or_driver, $id)
    {
        Session::put('company_or_driver', $company_or_driver);
        Session::put('id', $id);

        if($company_or_driver == 'company') $name = Company::where('id',$id)->pluck('name')->first();
        else {
        	$driver_name = User::where('id',$id)->select('first_name', 'last_name')->first();
        	$name = $driver_name->first_name.' '.$driver_name->last_name;
        }

        return $dataTable->render('admin.data_list.owe_trip', ['name' => $name]);
    }

    public function company_driver(CompanyDriverListDataTable $dataTable, $id, Request $r) 
    {
        //Session::put('id', $id);
        $array = array(
                'id' => $id, 
                'driver_id' => $r->driver_id,
                'start_date' => $r->start_date, 
                'end_date' => $r->end_date,   
            );
        return $dataTable->with($array)->render('admin.data_list.company_driver', $array);
    }
    public function hub_acquisition_driver(HubDriverAcquisitionDataTable $dataTable, $id, $all='') 
    {
        $array = array('id' => $id);
        $employee = HubEmployee::find($id);

        if($all == ''){
            $array = array_merge($array, array(
                                    'start_date' => date('Y-m-d'), 
                                    'end_date' => date('Y-m-d')
                                ));
        }else{
            $array = array_merge($array, array(
                                    'start_date' => $employee->created_at->format('Y-m-d'), 
                                    'end_date' => date('Y-m-d')
                                ));
        }
        $data = array_merge($array, array('name' => $employee->employee_name));

        return $dataTable->with($array)->render('admin.data_list.company_driver', $data);
    }

    public function hub_acquisition_driver_search(Request $request, HubDriverAcquisitionDataTable $dataTable) 
    {
        $id = $request->id;
        $employee = HubEmployee::find($id);

        if($request->start_date !='') $start_date = date('Y-m-d', strtotime($request->start_date));
        else $start_date = date('Y-m-d');

        if($request->end_date !='') $end_date = date('Y-m-d', strtotime($request->end_date)); 
        else $end_date = date('Y-m-d');

        $array = array(
                    'id' => $id,
                    'start_date' => $start_date, 
                    'end_date' => $end_date
                );
        $data = array_merge($array, array('name' => $employee->employee_name));

        return $dataTable->with($array)->render('admin.data_list.company_driver', $data);
    }

    
}
