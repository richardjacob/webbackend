<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trips;
use DB;

class ModalDataList extends Controller
{
    // public function owe_trip($company_or_driver, $id){
    //     if($company_or_driver == 'company'){
    //         $driver_id_array = array();
    //         foreach(DB::table('users')->select('id')->where('company_id', $id)->get()->toArray() as $userId){
    //             $driver_id_array[] = $userId->id;
    //         }            
    //         $list = Trips::whereIn('driver_id', $driver_id_array)->where('owe_amount','>','0')->get();
    //     }else{ // driver
    //         $list = Trips::where('driver_id', $id)->where('owe_amount','>','0')->get();
    //     }

    //     if(isset($list)){
    //         return view('admin.modal_data_list')->with('list', $list);
    //     }
    // }

    public function index(OweTripListDataTable $dataTable)
    {
        return $dataTable->render('admin.data_list.owe_trip');
    }
}
