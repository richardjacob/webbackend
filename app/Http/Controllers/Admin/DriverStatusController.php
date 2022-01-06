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

class DriverStatusController extends Controller
{
    public function index(Request $request)
    {
        $type = explode('/', Route::current()->uri())[2];
        //$type = $request->type ?? 'all_documents';
        $per_page = $request->per_page ?? 10;
        $driver_id = $request->driver_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $nid_verify = $request->nid_verify;
        if($nid_verify == '') $nid_verify = '0';


        $total_required_doc = DB::table('documents')->where('type', '!=', 'Company')
                                        ->where('status', 'Active') 
                                        ->count();

        $documents_id = DB::table('documents')->where('type', '!=', 'Company')
                                        ->where('status', 'Active')
                                        ->get()
                                        ->pluck('id')
                                        ->toArray();
        
        if($type == 'all_documents'){
            $list = DB::table('driver_documents')
                                ->select(
                                    'driver_documents.id as id', 
                                    'driver_documents.user_id as user_id', 
                                    'driver_documents.document as document_link',   
                                                                     
                                    DB::raw('count(*) as total'),
                                    DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                                    DB::raw('CONCAT(\'0\', users.mobile_number) AS mobile_number'),  
                                    'users.first_name as first_name', 
                                    'users.last_name as last_name',  
                                    'users.email as email',  
                                    'users.mobile_number as mobile',   
                                    'users.nid_number as nid_number',   
                                    'users.driving_licence_number as driving_licence_number',  
                                    'users.passport_number as passport_number',
                                    'users.is_owner as is_owner', 

                                    'driver_address.address_line1 as address_line1',  
                                    'driver_address.address_line2 as address_line2',  
                                    'driver_address.city as city',  
                                    'driver_address.state as state',   
                                    'driver_address.postal_code as postal_code',                                     

                                    'profile_picture.src as photo',    

                                    'driver_documents.checked as document_checked',    
                                    'profile_picture.checked as photo_checked',
                                    'profile_picture.checked_by as checked_by', 
                                    DB::raw('DATE_FORMAT(profile_picture.checked_time, "%d-%b-%Y, %h:%i %p") as checked_time'),
                                    DB::raw('CONCAT(companies.name, \' - \', companies.company_id) AS partner')
                                )
                                ->leftJoin('users', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'users.id');
                                })
                                ->leftJoin('profile_picture', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'profile_picture.user_id');
                                })
                                ->leftJoin('driver_address', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'driver_address.user_id');
                                })
                                ->leftJoin('companies', function($join) {
                                    $join->on('users.company_id', '=', 'companies.id');
                                })
                                
                                ->whereIn('driver_documents.document_id', $documents_id)
                                ->having(DB::raw('total'), '>=', $total_required_doc)

                                ->where('users.checked', '0')
                                ->where('users.verified', '0')
                                ->where('users.trained', '0')

                                ->where('profile_picture.src', '!=', '');

                                if($driver_id !='') $list = $list->where('users.id', $driver_id);
                                if($start_date !='') $list = $list->where('users.created_at', '>=', date("Y-m-d", strtotime($start_date)));
                                if($end_date !='') $list = $list->where('users.created_at', '<=', date("Y-m-d", strtotime($end_date)));

                                $list = $list->groupBy('user_id')
                                            ->orderBy('user_id', 'ASC')
                                            ->paginate($per_page);

        }

        else if($type == 'checked'){
            $list = DB::table('driver_documents')
                                ->select(
                                    'driver_documents.id as id', 
                                    'driver_documents.user_id as user_id',  
                                    'driver_documents.document as document_link',   
                                                                     
                                    DB::raw('count(*) as total'),
                                    DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                                    DB::raw('CONCAT(\'0\', users.mobile_number) AS mobile_number'),                                    
                                    'users.first_name as first_name', 
                                    'users.last_name as last_name',  
                                    'users.email as email',  
                                    'users.mobile_number as mobile',   
                                    'users.nid_number as nid_number',   
                                    'users.driving_licence_number as driving_licence_number',  
                                    'users.passport_number as passport_number',
                                    'users.is_owner as is_owner', 
                                    'users.checked_by as last_checked_by', 
                                    DB::raw('DATE_FORMAT(users.checked_time, "%d-%b-%Y, %h:%i %p") as checked_time') ,

                                    'driver_address.address_line1 as address_line1',  
                                    'driver_address.address_line2 as address_line2',  
                                    'driver_address.city as city',  
                                    'driver_address.state as state',   
                                    'driver_address.postal_code as postal_code',  
                                    
                                    'profile_picture.src as photo', 

                                    'driver_documents.verified as document_verified', 
                                    'profile_picture.verified as photo_verified',
                                    'profile_picture.verified_by as verified_by', 
                                    DB::raw('DATE_FORMAT(profile_picture.verified_time, "%d-%b-%Y, %h:%i %p") as verified_time') ,
                                    DB::raw('CONCAT(companies.name, \' - 0\', companies.mobile_number) AS partner')
                                )
                                ->leftJoin('users', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'users.id');
                                })
                                ->leftJoin('profile_picture', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'profile_picture.user_id');
                                })
                                ->leftJoin('driver_address', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'driver_address.user_id');
                                })
                                ->leftJoin('companies', function($join) {
                                    $join->on('users.company_id', '=', 'companies.id');
                                })
                                
                                ->whereIn('driver_documents.document_id', $documents_id)
                                ->having(DB::raw('total'), '>=', $total_required_doc)

                                ->where('users.checked', '1')
                                ->where('users.verified', '0')
                                ->where('users.trained', '0')

                                ->where('profile_picture.src', '!=', '');

                                if($driver_id !='') $list = $list->where('users.id', $driver_id);
                                if($start_date !='') $list = $list->where('users.created_at', '>=', date("Y-m-d", strtotime($start_date)));
                                if($end_date !='') $list = $list->where('users.created_at', '<=', date("Y-m-d", strtotime($end_date)));

                                $list = $list->groupBy('user_id')
                                ->orderBy('user_id', 'ASC')
                                ->paginate($per_page);

        }

        else if($type == 'verified'){
            $list = DB::table('driver_documents')
                                ->select(
                                    'driver_documents.id as id', 
                                    'driver_documents.user_id as user_id',  
                                    'driver_documents.document as document_link',   
                                                                     
                                    DB::raw('count(*) as total'),
                                    DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                                    DB::raw('CONCAT(\'0\', users.mobile_number) AS mobile_number'),
                                    'users.nid_number as nid_number',    
                                    'users.passport_number as passport_number',
                                    'users.verified_by as last_verified_by', 
                                    DB::raw('DATE_FORMAT(users.verified_time, "%d-%b-%Y, %h:%i %p") as verified_time') ,                              
                                    
                                    'profile_picture.src as photo', 
                                    'users.driving_licence_number as driving_licence_number',                                    
                                    
                                )
                                ->leftJoin('users', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'users.id');
                                })
                                ->leftJoin('profile_picture', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'profile_picture.user_id');
                                })
                                
                                ->whereIn('driver_documents.document_id', $documents_id)
                                ->having(DB::raw('total'), '>=', $total_required_doc)

                                ->where('users.checked', '1')
                                ->where('users.verified', '1')
                                ->where('users.trained', '0')

                                ->where('profile_picture.src', '!=', '');

                                if($driver_id !='') $list = $list->where('users.id', $driver_id);
                                if($start_date !='') $list = $list->where('users.created_at', '>=', date("Y-m-d", strtotime($start_date)));
                                if($end_date !='') $list = $list->where('users.created_at', '<=', date("Y-m-d", strtotime($end_date)));

                                $list = $list->groupBy('user_id')
                                            ->orderBy('user_id', 'ASC')
                                            ->paginate($per_page);

        }

        else if($type == 'trained'){
            $list = DB::table('driver_documents')
                                ->select(
                                    'driver_documents.id as id', 
                                    'driver_documents.user_id as user_id',  
                                    'driver_documents.document as document_link',   
                                                                     
                                    DB::raw('count(*) as total'),
                                    DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                                    DB::raw('CONCAT(\'0\', users.mobile_number) AS mobile_number'),
                                    'users.nid_number as nid_number',     
                                    'users.passport_number as passport_number',                             
                                    
                                    'profile_picture.src as photo', 
                                    'users.driving_licence_number as driving_licence_number',     
                                    'users.trained_by as last_trained_by', 
                                    DB::raw('DATE_FORMAT(users.trained_time, "%d-%b-%Y, %h:%i %p") as trained_time') ,                                  
                                    
                                )
                                ->leftJoin('users', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'users.id');
                                })
                                ->leftJoin('profile_picture', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'profile_picture.user_id');
                                })
                                
                                ->whereIn('driver_documents.document_id', $documents_id)
                                ->having(DB::raw('total'), '>=', $total_required_doc)

                                ->where('users.checked', '1')
                                ->where('users.verified', '1')
                                ->where('users.trained', '1')
                                ->where('users.active', '0')

                                ->where('profile_picture.src', '!=', '');

                                if($driver_id !='') $list = $list->where('users.id', $driver_id);
                                if($start_date !='') $list = $list->where('users.created_at', '>=', date("Y-m-d", strtotime($start_date)));
                                if($end_date !='') $list = $list->where('users.created_at', '<=', date("Y-m-d", strtotime($end_date)));

                                $list = $list->groupBy('user_id')
                                            ->orderBy('user_id', 'ASC')
                                            ->paginate($per_page);

        }

        else if($type == 'active'){
            $list = DB::table('driver_documents')
                                ->select(
                                    'driver_documents.id as id', 
                                    'driver_documents.user_id as user_id',  
                                    'driver_documents.document as document_link',   
                                                                     
                                    DB::raw('count(*) as total'),
                                    DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                                    DB::raw('CONCAT(\'0\', users.mobile_number) AS mobile_number'),
                                    'users.nid_number as nid_number',   
                                    'users.passport_number as passport_number',                               
                                    
                                    'profile_picture.src as photo', 
                                    'users.driving_licence_number as driving_licence_number',    
                                    'users.active_by as last_active_by', 
                                    DB::raw('DATE_FORMAT(users.active_time, "%d-%b-%Y, %h:%i %p") as active_time') ,                                   
                                    
                                )
                                ->leftJoin('users', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'users.id');
                                })
                                ->leftJoin('profile_picture', function($join) {
                                    $join->on('driver_documents.user_id', '=', 'profile_picture.user_id');
                                })
                                
                                ->whereIn('driver_documents.document_id', $documents_id)
                                ->having(DB::raw('total'), '>=', $total_required_doc)

                                ->where('users.checked', '1')
                                ->where('users.verified', '1')
                                ->where('users.trained', '1')
                                ->where('users.active', '1')

                                ->where('profile_picture.src', '!=', '');

                                if($driver_id !='') $list = $list->where('users.id', $driver_id);
                                if($start_date !='') $list = $list->where('users.created_at', '>=', date("Y-m-d", strtotime($start_date)));
                                if($end_date !='') $list = $list->where('users.created_at', '<=', date("Y-m-d", strtotime($end_date)));

                                $list = $list->groupBy('user_id')
                                            ->orderBy('user_id', 'ASC')
                                            ->paginate($per_page);

        }

        else if($type == 'owner'){
            $driver_id = $request->driver_id;

            $list = DB::table('users')
                                ->select(
                                    DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                                    DB::raw('CONCAT(\'0\', users.mobile_number) AS mobile_number'),
                                    DB::raw('DATE_FORMAT(users.active_time, "%d-%b-%Y, %h:%i %p") as active_time') , 
                                    'users.id as id', 
                                    'users.email as email', 
                                    'users.nid_number as nid_number',  
                                    'users.passport_number as passport_number',
                                    'users.driving_licence_number as driving_licence_number',  
                                    'users.status as status',                                
                                    
                                    'profile_picture.src as photo',   
                                    'driver_address.address_line1 as address_line1',   
                                    'driver_address.address_line2 as address_line2',   
                                    'driver_address.city as city',   
                                    'driver_address.state as state',    
                                    'driver_address.postal_code as postal_code',                                  
                                    
                                )
                                ->leftJoin('profile_picture', function($join) {
                                    $join->on('users.id', '=', 'profile_picture.user_id');
                                })
                                ->leftJoin('driver_address', function($join) {
                                    $join->on('users.id', '=', 'driver_address.user_id');
                                })
                                ->where('users.is_owner', '1');



            if($driver_id !='') $list = $list->where('users.id', $driver_id);
            if($start_date !='') $list = $list->where('users.created_at', '>=', date("Y-m-d", strtotime($start_date)));
            if($end_date !='') $list = $list->where('users.created_at', '<=', date("Y-m-d", strtotime($end_date)));
            
            $list = $list->orderBy('id', 'ASC')
                        ->paginate($per_page);

        }
        
        else if($type == 'partner'){
            $company_id = $request->company_id;
            $list = DB::table('companies');

            if($company_id !='') $list = $list->where('company_id', $company_id);
            if($start_date !='') $list = $list->where('created_at', '>=', date("Y-m-d", strtotime($start_date)));
            if($end_date !='') $list = $list->where('created_at', '<=', date("Y-m-d", strtotime($end_date)));
            
            $list = $list->orderBy('id', 'ASC')
                        ->paginate($per_page);

        }

        else if($type == 'drivers_under_partner'){
            $driver_id = $request->driver_id;

            $list = DB::table('users')
                                ->select(
                                    DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                                    DB::raw('CONCAT(\'0\', users.mobile_number) AS mobile_number'),
                                    DB::raw('DATE_FORMAT(users.created_at, "%d-%b-%Y") as created_at') , 
                                    DB::raw('DATE_FORMAT(users.active_time, "%d-%b-%Y, %h:%i %p") as active_time') , 
                                    'users.id as id', 
                                    'users.company_id as company_id', 
                                    'users.email as email', 
                                    'users.nid_number as nid_number',  
                                    'users.passport_number as passport_number',
                                    'users.driving_licence_number as driving_licence_number',  
                                    'users.status as status',                                
                                    
                                    'profile_picture.src as photo',   
                                    'driver_address.address_line1 as address_line1',   
                                    'driver_address.address_line2 as address_line2',   
                                    'driver_address.city as city',   
                                    'driver_address.state as state',    
                                    'driver_address.postal_code as postal_code',
                                )
                                ->leftJoin('profile_picture', function($join) {
                                    $join->on('users.id', '=', 'profile_picture.user_id');
                                })
                                ->leftJoin('driver_address', function($join) {
                                    $join->on('users.id', '=', 'driver_address.user_id');
                                })
                                ->where('users.is_owner', '0')
                                ->where('users.user_type', 'Driver')
                                ->where('users.company_id', '!=', '1');

            if($driver_id !='') $list = $list->where('users.id', $driver_id);
            if($start_date !='') $list = $list->where('users.created_at', '>=', date("Y-m-d", strtotime($start_date)));
            if($end_date !='') $list = $list->where('users.created_at', '<=', date("Y-m-d", strtotime($end_date)));
            
            $list = $list->orderBy('id', 'ASC')
                        ->paginate($per_page);

        }

        else if($type == 'uncheck_owner_driver'){
            $driver_id = $request->driver_id;

            $list = DB::table('users')
                                ->select(
                                    DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                                    DB::raw('CONCAT(\'0\', users.mobile_number) AS mobile_number'),
                                    DB::raw('DATE_FORMAT(users.created_at, "%d-%b-%Y") as created_at') , 
                                    DB::raw('DATE_FORMAT(users.active_time, "%d-%b-%Y, %h:%i %p") as active_time') , 
                                    'users.id as id', 
                                    'users.company_id as company_id', 
                                    'users.email as email', 
                                    'users.nid_number as nid_number',  
                                    'users.passport_number as passport_number',
                                    'users.driving_licence_number as driving_licence_number',  
                                    'users.status as status',                             
                                    
                                    'profile_picture.src as photo',   
                                    'driver_address.address_line1 as address_line1',   
                                    'driver_address.address_line2 as address_line2',   
                                    'driver_address.city as city',   
                                    'driver_address.state as state',    
                                    'driver_address.postal_code as postal_code',
                                )
                                ->leftJoin('profile_picture', function($join) {
                                    $join->on('users.id', '=', 'profile_picture.user_id');
                                })
                                ->leftJoin('driver_address', function($join) {
                                    $join->on('users.id', '=', 'driver_address.user_id');
                                })
                                ->where('users.user_type', 'Driver')
                                ->whereNull('users.is_owner');

            if($driver_id !='') $list = $list->where('users.id', $driver_id);
            if($start_date !='') $list = $list->where('users.created_at', '>=', date("Y-m-d", strtotime($start_date)));
            if($end_date !='') $list = $list->where('users.created_at', '<=', date("Y-m-d", strtotime($end_date)));
            
            $list = $list->orderBy('id', 'ASC')
                        ->paginate($per_page);

        }

        else if($type == 'nid'){          
            if($nid_verify == '0' || $nid_verify == '1' || $nid_verify == '2' || $nid_verify == 'checked_nid' || $nid_verify == 'not_checked_nid'){
                $list = DB::table('users')
                        ->select(
                            DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                            DB::raw('CONCAT(\'0\', users.mobile_number) AS mobile_number'),
                            DB::raw('DATE_FORMAT(users.created_at, "%d-%b-%Y") as created_at') , 
                            DB::raw('DATE_FORMAT(users.active_time, "%d-%b-%Y, %h:%i %p") as active_time') ,
                            'users.id as id', 
                            'users.company_id as company_id', 
                            'users.email as email', 
                            'users.nid_number as nid_number',  
                            'users.passport_number as passport_number', 
                            'users.status as status', 
                            'driver_documents.id as driver_document_id',  
                            'driver_documents.status as nid_status',    
                            'driver_documents.verified_by as verified_by',  
                            'driver_documents.checked_by as checked_by',  
                            'driver_documents.document as document', 
                            DB::raw('DATE_FORMAT(driver_documents.checked_time, "%d-%b-%Y, %h:%i %p") as checked_time') ,
                            DB::raw('DATE_FORMAT(driver_documents.verified_time, "%d-%b-%Y, %h:%i %p") as verified_time') ,
                            'profile_picture.src as photo',                                
                        )
                        ->leftJoin('driver_documents', function($join) {
                            $join->on('users.id', '=', 'driver_documents.user_id');
                        })
                        ->leftJoin('profile_picture', function($join) {
                            $join->on('users.id', '=', 'profile_picture.user_id');
                        })
                        ->where('driver_documents.document_id', '8')
                        ->where('users.user_type', 'Driver');
                        //->where('users.nid_number', '!=', '');
                
                if($nid_verify == '1') $list = $list->where('driver_documents.status', $nid_verify);
                elseif($nid_verify == '0') $list = $list->where('driver_documents.status', $nid_verify);
                elseif($nid_verify == '2') $list = $list->where('driver_documents.status', $nid_verify);
                elseif($nid_verify == 'checked_nid') {
                    $nid_user_id = DB::table('nid')->where('user_id', '!=', '')->groupBy('user_id')->pluck('user_id')->all();
                    $list = $list->whereIn('users.id', $nid_user_id);          
                } 
                elseif($nid_verify == 'not_checked_nid'){
                    $nid_user_id = DB::table('nid')->where('user_id', '!=', '')->groupBy('user_id')->pluck('user_id')->all();
                    $list = $list->whereNotIn('users.id', $nid_user_id);  
                }

            }else{
                $driver_doc_user_id = DB::table('driver_documents')
                                            ->where('document_id', '8')
                                            ->groupBy('user_id')
                                            ->pluck('user_id')
                                            ->all();

                $list = DB::table('users')
                        ->select(
                            DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS driver_name'),
                            DB::raw('CONCAT(\'0\', users.mobile_number) AS mobile_number'),
                            DB::raw('DATE_FORMAT(users.created_at, "%d-%b-%Y") as created_at') , 
                            DB::raw('DATE_FORMAT(users.active_time, "%d-%b-%Y, %h:%i %p") as active_time') ,
                            'users.id as id', 
                            'users.company_id', 
                            'users.email', 
                            'users.nid_number',  
                            'users.passport_number', 
                            'users.status'                               
                        )
                        ->whereNotIn('id', $driver_doc_user_id)
                        ->where('user_type', 'Driver');

               
            }       
            
            if($driver_id !='') $list = $list->where('users.id', $driver_id);
            if($start_date !='') $list = $list->where('users.created_at', '>=', date("Y-m-d", strtotime($start_date)));
            if($end_date !='') $list = $list->where('users.created_at', '<=', date("Y-m-d", strtotime($end_date)));

            $list = $list->orderBy('id', 'ASC')
                        ->paginate($per_page);

        }
        
        $data['list']           = $list;
        $data['type']           = $type;
        $data['per_page']       = $per_page;
        $data['driver_id']      = $driver_id;
        $data['start_date']     = $start_date;
        $data['end_date']       = $end_date;
        $data['nid_verify']     = $nid_verify;
        

        return view('admin.driver.driver_status', $data);
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
