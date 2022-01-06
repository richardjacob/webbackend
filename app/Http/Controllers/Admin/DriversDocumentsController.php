<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\HubAcquisitionListDataTable;
use App\Models\Hub;
use App\Models\HubEmployee;
use App\Models\Documents;
use App\Models\DriverDocuments;
use App\Models\User;
use DB;
use Validator;
use Auth;
use Route;
use Hash;

class DriversDocumentsController extends Controller
{
    

    public function index(Request $request)
    {
        if($request->document_id  !='') $req = '1';
        else $req = '';

        $document_id_array = $request->document_id;
        $print = $request->print;
        $per_page = $request->per_page ?? 20;
        $given = $request->given ?? 'Given';
        $checkAll = $request->checkAll;


        if(is_array($document_id_array)){
            if($given == 'Given'){
                foreach ($document_id_array as $document_id) {
                    $result = DriverDocuments::where('document_id', $document_id)
                                                    ->get()->pluck('user_id');
                    if(is_object($result)){
                        $user_by_doc[] = $result->toArray();
                    }
                }                
            }
            else{
                $all_user_id = DB::table('users')
                                    ->where('user_type', 'Driver')
                                    ->get()->pluck('id')->toArray();
                sort($all_user_id);

                $document_user_id = array();
                $common_user_id = array();
                $user_by_doc = array();
                
                foreach ($document_id_array as $document_id) {
                    $document_user_id_object = DriverDocuments::where('document_id', $document_id)
                                                ->whereIn('user_id', $all_user_id)
                                                ->get()->pluck('user_id');

                    if(is_object($document_user_id_object) AND count($document_user_id_object) > 0){
                        $document_user_id = $document_user_id_object->toArray();
                        sort($document_user_id);
                        
                        $user_by_doc[] = differences($all_user_id, $document_user_id);
                        sort($user_by_doc);
                    }
                }
            }

            $user_id_array = array();

            switch (count($user_by_doc)) {
                case 1: 
                    $user_id_array = $user_by_doc[0];
                    break;
                case 2: 
                    $one_two = array_intersect($user_by_doc[0], $user_by_doc[1]);
                    $user_id_array = $one_two;
                    break;
                case 3: 
                    $one_two = array_intersect($user_by_doc[0], $user_by_doc[1]);
                    $three = $user_by_doc[2];
                    $user_id_array = array_intersect($one_two, $three);
                    break;
                case 4: 
                    $one_two = array_intersect($user_by_doc[0], $user_by_doc[1]);
                    $three_four = array_intersect($user_by_doc[2], $user_by_doc[3]);
                    $user_id_array = array_intersect($one_two, $three_four);
                    break;  
                case 5: 
                    $one_two = array_intersect($user_by_doc[0], $user_by_doc[1]);
                    $three_four = array_intersect($user_by_doc[2], $user_by_doc[3]);
                    $one_four = array_intersect($one_two, $three_four);
                    $five = $user_by_doc[4];
                    $user_id_array = array_intersect($one_four, $five);
                    break; 
                case 6: 
                    $one_two = array_intersect($user_by_doc[0], $user_by_doc[1]);
                    $three_four = array_intersect($user_by_doc[2], $user_by_doc[3]);
                    $five_six = array_intersect($user_by_doc[4], $user_by_doc[5]);
                    $one_four = array_intersect($one_two, $three_four);
                    $user_id_array = array_intersect($one_four, $five_six);
                    break;
                case 7: 
                    $one_two = array_intersect($user_by_doc[0], $user_by_doc[1]);
                    $three_four = array_intersect($user_by_doc[2], $user_by_doc[3]);
                    $five_six = array_intersect($user_by_doc[4], $user_by_doc[5]);

                    $one_four = array_intersect($one_two, $three_four);
                    $one_six = array_intersect($one_four, $five_six);
                    $user_id_array = array_intersect($one_six, $user_by_doc[6]);
                    break;

                case 8: 
                    $one_two = array_intersect($user_by_doc[0], $user_by_doc[1]);
                    $three_four = array_intersect($user_by_doc[2], $user_by_doc[3]);
                    $five_six = array_intersect($user_by_doc[4], $user_by_doc[5]);
                    $seven_eight = array_intersect($user_by_doc[6], $user_by_doc[7]);

                    $one_four = array_intersect($one_two, $three_four);
                    $one_six = array_intersect($one_four, $five_six);
                    $user_id_array = array_intersect($one_six, $seven_eight);
                    break;

                case 9: 
                    $one_two = array_intersect($user_by_doc[0], $user_by_doc[1]);
                    $three_four = array_intersect($user_by_doc[2], $user_by_doc[3]);
                    $five_six = array_intersect($user_by_doc[4], $user_by_doc[5]);
                    $seven_eight = array_intersect($user_by_doc[6], $user_by_doc[7]);

                    $one_four = array_intersect($one_two, $three_four);
                    $one_six = array_intersect($one_four, $five_six);
                    $one_eight = array_intersect($one_six, $seven_eight);
                    $user_id_array = array_intersect($one_eight, $user_by_doc[8]);
                    break;

                case 10: 
                    $one_two = array_intersect($user_by_doc[0], $user_by_doc[1]);
                    $three_four = array_intersect($user_by_doc[2], $user_by_doc[3]);
                    $five_six = array_intersect($user_by_doc[4], $user_by_doc[5]);
                    $seven_eight = array_intersect($user_by_doc[6], $user_by_doc[7]);
                    $nine_ten = array_intersect($user_by_doc[8], $user_by_doc[9]);

                    $one_four = array_intersect($one_two, $three_four);
                    $one_six = array_intersect($one_four, $five_six);
                    $one_eight = array_intersect($one_six, $seven_eight);
                    $user_id_array = array_intersect($one_eight, $nine_ten);

                    break;
                default:
                    $user_id='';
                    break;
            }

            $user_id = array_unique($user_id_array);

            DB::statement(DB::raw('set @serial=0'));
            $list =  DB::Table('users')->select(
                    'users.id as id',
                    DB::raw('DATE_FORMAT(users.created_at, "%d-%b-%Y") as created_at'),
                    DB::raw('@serial  := @serial  + 1 AS serial'),
                    DB::raw("CONCAT(users.first_name,' ',users.last_name) AS driver_name"),
                    DB::raw("CONCAT('0',users.mobile_number) AS mobile_number")
            )
            ->whereIn('id', $user_id);
        }        

        $data['req']            = $req;
        $data['given']          = $given;
        $data['checkAll']          = $checkAll;
        
        $data['per_page']       = $per_page;
        $data['document_id']    = $document_id_array ?? array();

        $data['driver_documents']  = Documents::where('type', 'Driver')
                                            ->where('status', 'Active')
                                            ->select('id','document_name')
                                            ->get();

        $data['car_documents']  = Documents::where('type', 'Vehicle')
                                            ->where('status', 'Active')
                                            ->select('id','document_name')
                                            ->get();                                            

        if($print){ 
            if(isset($list)) {
                $list = $list->get();
                $data['list'] = $list;
            }
            return view('admin.driver.drivers_document_print', $data);
        }
        else{   
            if(isset($list)) {         
                $list = $list->paginate($per_page);
                $data['list'] = $list;
            }
            return view('admin.driver.drivers_document', $data);
        }
    }

    public function only_checked_documents(Request $request)
    {
        if($request->document_id  !='') $req = '1';
        else $req = '';
        $merge_uncheck = array();

        if($request->document_id  !=''){
            $document_id_array = $request->document_id;
            $print = $request->print;
            $per_page = $request->per_page ?? 20;
            $checkAll = $request->checkAll;

            $documents = DB::table('documents')
                                ->where('status', 'Active')
                                ->where('type', '!=', 'Company')
                                ->pluck('id')
                                ->toArray();

            $unchecked_document_id = array_diff($documents,$document_id_array);

            $user_id_array = array();
            foreach ($document_id_array as $sl => $document_id) {
                $result = DriverDocuments::where('document_id', $document_id)
                                                ->get()->pluck('user_id');
                if(is_object($result)){
                    //$user_by_doc[] = $result->toArray();
                    $user_by_doc = $result->toArray();

                    if($sl > 0) {
                        $user_id_array = array_intersect($user_id_array, $user_by_doc);   
                    }
                    else $user_id_array  = $user_by_doc; 
                }
            }  

            $user_id = array_unique($user_id_array);
            
            foreach ($unchecked_document_id as $sl => $document_id) {
                $uncheck_user_id = DriverDocuments::where('document_id', $document_id)
                                        ->whereIn('user_id', $user_id)
                                        ->pluck('user_id')
                                        ->toArray();   

                $merge_uncheck[] = $uncheck_user_id;        
            }

            $final_array = array();
            foreach ($merge_uncheck as $m) {
                if($sl > 0) {
                    $final_array = array_merge($final_array, $m);   
                }
                else $final_array  = $m;   
            }
            $final_array = array_unique($final_array);

            if(isset($user_id)){
                $final_id = array_diff($user_id, $final_array);

                DB::statement(DB::raw('set @serial=0'));
                $list =  DB::Table('users')->select(
                        'users.id as id',
                        DB::raw('DATE_FORMAT(users.created_at, "%d-%b-%Y") as created_at'),
                        DB::raw('@serial  := @serial  + 1 AS serial'),
                        DB::raw("CONCAT(users.first_name,' ',users.last_name) AS driver_name"),
                        DB::raw("CONCAT('0',users.mobile_number) AS mobile_number")
                )
                ->whereIn('id', $final_id);
            }

        }



        $data['req']            = $req;
        $data['checkAll']         = @$checkAll;
        
        $data['per_page']       = @$per_page;
        $data['document_id']    = $document_id_array ?? array();

        $data['driver_documents']  = Documents::where('type', 'Driver')
                                            ->where('status', 'Active')
                                            ->select('id','document_name')
                                            ->get();

        $data['car_documents']  = Documents::where('type', 'Vehicle')
                                            ->where('status', 'Active')
                                            ->select('id','document_name')
                                            ->get();                                            

        if(@$print){ 
            if(isset($list)) {
                $list = $list->get();
                $data['list'] = $list;
            }
            return view('admin.driver.only_checked_documents_print', $data);
        }
        else{   
            if(isset($list)) {         
                $list = $list->paginate($per_page);
                $data['list'] = $list;
            }
            return view('admin.driver.only_checked_documents', $data);
        }
    }
    

    
}
