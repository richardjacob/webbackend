<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use App\Models\Vehicle;
use App\Models\Nid;
use App\Models\User;
use App\Models\DriverAddress;
use App\Models\Company;
use App\Models\CarType;

class AjaxController extends Controller
{
    public function __construct()
    {
        $this->doc_helper = resolve('App\Http\Helper\DocumentVerificationHelper');
        $this->bonus_helper = resolve('App\Http\Helper\BonusHelper');
    }

    public function driver_status_update(Request $request){
        //Expire document not checked
        // registration_paper => 6,
        // nid => 8,
        // enlistment_certificate => 11,
        // fitness_certificate => 12,

        $sl = $request->sl;
        $id = $request->id;
        $user_id = $request->user_id;
        $col = $request->col;
        $tab = $request->tab;

        $updateable = '1';	

        if($tab == "profile_picture"){
            $user_id = $id;
            $update = DB::table($tab)->where('user_id', $id)->update([$col => '1', $col.'_by' => Auth::guard('admin')->user()->id, $col.'_time' => date('Y-m-d H:i:s') ]);
        }
        else if($tab == "users"){
            $update_array = array($col => '1', $col.'_by' => Auth::guard('admin')->user()->id, $col.'_time' => date('Y-m-d H:i:s'));
            if($col == 'active') {
                $update_array = array_merge($update_array, array('status' => 'Active'));
                $default_vehicle = Vehicle::where('user_id', $user_id)->where('default_type', '1')->first();
                if(!is_object($default_vehicle)){
                    $no_default_vehicle = Vehicle::where('user_id', $user_id)
                                                ->where('default_type', '0')
                                                ->orderBy('id', 'DESC')
                                                ->first();

                    if(is_object($no_default_vehicle)){
                        $no_default_vehicle->default_type = '1';
                        $no_default_vehicle->is_active = '1';    
                        $no_default_vehicle->status = 'Active';                          
                        $no_default_vehicle->save();
                    }
                }
            }
            $update2 = DB::table($tab)->where('id', $user_id)->update($update_array);
            
            if($update2){
                if($col == 'active'){
                    $user = User::find($user_id);
                    $this->bonus_helper->driver_joining_bonus($user);
                    $this->bonus_helper->driver_referral_bonus_new($user);                    
                    //$this->bonus_helper->add_update_online_bonus($user->id, date('Y-m-d'));
                }                
                echo '<i class="fa fa-check text-success" aria-hidden="true" title="'.ucwords($col).' by '.Auth::guard('admin')->user()->username.' at '.date('d-M-Y, h:i A').' "></i>|'.$sl;
                exit;
            }
        }else{   
            $info = DB::table($tab)->where('id', $id)->first();
            if($info->document_id != '6' AND $info->document_id != '8' AND $info->document_id != '11' AND $info->document_id != '12'){
                if(strtotime($info->expired_date) < strtotime(date('Y-m-d'))) $updateable = '0';
            }

            if($updateable == '1'){
                $arrray = array(
                        $col => '1', 
                        $col.'_by' => Auth::guard('admin')->user()->id, 
                        $col.'_time' => date('Y-m-d H:i:s') 
                    );
                if($col == 'verified') $arrray = array_merge($arrray, ['status' => '1']);

                $update = DB::table($tab)->where('id', $id)->update($arrray);
            }
            
        }

        if(@$update){
            if(self::driver_status_group_update($user_id, $col) == '1'){
                echo '<i class="fa fa-check text-success" aria-hidden="true" title="'.ucwords($col).' by '.Auth::guard('admin')->user()->username.' at '.date('d-M-Y, h:i A').' "></i>|'.$sl;
            }
            else echo '<i class="fa fa-check text-success" aria-hidden="true" title="'.ucwords($col).' by '.Auth::guard('admin')->user()->username.' at '.date('d-M-Y, h:i A').' "></i>';
        }
        else {
            if($updateable == '0') echo "Expired document is not changeable.";
            else echo "Error!";   
        }     
    } //users

    public function driver_status_group_update($user_id, $col){
        $total_required_doc = DB::table('documents')->where('type', '!=', 'Company')
                                                    ->where('status', 'Active') 
                                                    ->count();

        $photo = DB::table('profile_picture')
                        ->select(DB::raw('count(*) as total'))
                        ->where('user_id', $user_id)
                        ->where($col, '1')
                        ->pluck('total')
                        ->first();

        $document = DB::table('driver_documents')
                        ->select(DB::raw('count(*) as total'))
                        ->where('user_id', $user_id)
                        ->where($col, '1')
                        ->pluck('total')
                        ->first();

        if($photo == '1' AND $document == $total_required_doc) {
            $update = DB::table('users')->where('id', $user_id)->update([$col => '1', $col.'_by' => Auth::guard('admin')->user()->id, $col.'_time' => date('Y-m-d H:i:s')]);
            if($update) return '1';
        }      
    }


    public function driver_status_uncheck(Request $request){
        $sl = $request->sl;
        $id = $request->id;
        $user_id = $request->user_id;
        $col = $request->col;
        $tab = $request->tab;
    
        if($tab == "profile_picture"){
            $user_id = $id;
            $update = DB::table($tab)->where('user_id', $id)->update([$col => '0', $col.'_by' => Auth::guard('admin')->user()->id, $col.'_time' => date('Y-m-d H:i:s') ]);
        }
        // else if($tab == "users"){
        //     $update2 = DB::table($tab)->where('id', $user_id)->update([$col => '0', $col.'_by' => Auth::guard('admin')->user()->id, $col.'_time' => date('Y-m-d H:i:s') ]);
        //     if($update2){                
        //         echo '<i class="fa fa-check text-success" aria-hidden="true" title="'.ucwords($col).' by '.Auth::guard('admin')->user()->username.' at '.date('d-M-Y, h:i A').' "></i>|'.$sl;
        //         exit;
        //     }
        //}
        else{ 
            //if($col == 'verified') $col = 'checked';
            $arrray = array(
                    $col => '0', 
                    $col.'_by' => Auth::guard('admin')->user()->id, 
                    $col.'_time' => date('Y-m-d H:i:s') 
                );
            if($col == 'verified') $arrray = array_merge($arrray, ['status' => '0']);
            $update = DB::table($tab)->where('id', $id)->update($arrray);
        }

        if(@$update){
            $update2 = DB::table("users")->where('id', $user_id)->update([$col => '0', $col.'_by' => Auth::guard('admin')->user()->id, $col.'_time' => date('Y-m-d H:i:s') ]);
            echo "Removed";            
        }  
    } //users


    public function suggestion(Request $r)
	{
	    $keywords = $r->keywords;
        $user_type = $r->user_type;

	    $first = substr($keywords,0,1);
	    if($first == "0") $keywords = substr($keywords,1);

	    if($keywords !=''){
	        $list = DB::table('users')->where('user_type',$user_type);

	        $list = $list->where(function($query) use ($keywords){
	        	$query->orWhere('first_name', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('last_name', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('email', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('mobile_number', 'LIKE', '%'.$keywords.'%');
                $query->orWhere('id', 'LIKE', '%'.$keywords.'%');
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
        
    public function suggestion_for_user(Request $r)
	{
	    $keywords = $r->keywords;
        $user_type = $r->user_type;
        $search_field_id = $r->search_field_id;
        $search_value_id = $r->search_value_id;         

	    $first = substr($keywords,0,1);
	    if($first == "0") $keywords = substr($keywords,1);

	    if($keywords !=''){
	        $list = DB::table('users')->where('user_type',$user_type);

	        $list = $list->where(function($query) use ($keywords){
	        	$query->orWhere('first_name', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('last_name', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('email', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('mobile_number', 'LIKE', '%'.$keywords.'%');
                $query->orWhere('id', 'LIKE', '%'.$keywords.'%');
	        });
	        $list = $list->get();

	        $output = "";

	        foreach($list as $sl => $data){
	            if($sl == 0) $output.= '<ul id="suggesstion_list">';

	            $label = $data->first_name." ".$data->last_name. "(".$data->mobile_number.")";
	            $val = $data->id;
	  
	            $output.= '<li onclick=\'select_from_suggestion_for_user("'.$label.'", "'.$val.'", "'.$search_field_id.'", "'.$search_value_id.'")\'> '.$label.'</li>';
                

	            if(count($list) == $sl + 1)  $output.= '</ul>';
	        }
	        echo $output; 
	    }
    }
    
    public function suggestion_contact(Request $r)
	{
	    $keywords = $r->keywords;
	    $first = substr($keywords,0,1);
	    if($first == "0") $keywords = substr($keywords,1);

	    if($keywords !=''){
	        $list = DB::table('contacts');

	        $list = $list->where(function($query) use ($keywords){
	        	$query->orWhere('name', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('email', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('mobile', 'LIKE', '%'.$keywords.'%');
	        });
	        $list = $list->get();

	        $output = "";

	        foreach($list as $sl => $data){
	            if($sl == 0) $output.= '<ul id="suggesstion_list">';

	            $label = $data->name." ".$data->email. "(".$data->mobile.")";
	            $val = $data->id;
	  
	            $output.= '<li onclick=\'select_from_suggestion_for_contact("'.$label.'", "'.$val.'")\'> '.$label.'</li>';
                

	            if(count($list) == $sl + 1)  $output.= '</ul>';
	        }
	        echo $output; 
	    }
    }


    public function complain_sub_category_option(Request $request)
	{
        $cat_id = $request->cat_id;
       
        $list = DB::table('complain_sub_categories')
                    ->where('complain_cat_id', $cat_id)
                    ->get();

        $output = '<option value="">Select</option>';
        foreach($list as $sl => $data){
            $output.= '<option value="'.$data->id.'">'.$data->sub_category.'</option>';
        }
        echo $output;
    }

    public function driver_expired_date_update(Request $request){
        $id = $request->id;
        $exp_date = $request->exp_date;
        list($d,$m,$y) = explode("-", $exp_date);
        $expDate = $y.'-'.$m.'-'.$d;

        echo $update = DB::table('driver_documents')->where('id', $id)->update(['expired_date' => $expDate]);
    } 

    public function update_driver_info(Request $request){
        $driver_id = $request->driver_id;
        //dd($request);

        $user_table = User::find($driver_id);
        $user_table->first_name = $request->first_name;
        $user_table->last_name = $request->last_name;
        $user_table->email = $request->email;
        $user_table->driving_licence_number = $request->driving_licence_number; 
        $user_table->passport_number = $request->passport_number; 
        $user_table->save();

        $adress_table = DriverAddress::where('user_id', $driver_id)->first();
        $adress_table->address_line1 =  $request->address_line1;
        $adress_table->address_line2 =  $request->address_line2;
        $adress_table->city =  $request->city;
        $adress_table->state =  $request->state;
        $adress_table->postal_code =  $request->postal_code;
        $adress_table->save();

        echo "1";      
    }

    /*
    public function verify_nid(Request $request) 
    {       
        $nid = $request->nid;
        $user_id = $request->user_id;
        if($nid =='' OR $user_id ==''){
            return 'Both NID and Driver ID is required.';
        }
        else{
            $user_nid_exist = DB::table('users')->where('nid_number', $nid)->first();

            if(is_object($user_nid_exist)){
                if($user_id == $user_nid_exist->id){
                    return trans('messages.api.you_used_nid');
                }else{
                    return trans('messages.api.driver_used_nid');
                }                
            }else{
                $destinationPath = public_path() . "/images/nid_photo/";
                if (!is_dir($destinationPath)) mkdir($destinationPath, 0777, true);
                
                if (!is_object(Nid::where('nid', $nid)->first())) {
                    $nid_response = $this->doc_helper->nid($nid);

                    if (array_key_exists('message', $nid_response)) {
                        return $nid_response['message'];
                        //return trans('messages.api.nid_digit');
                    }
                    else if(array_key_exists('title', $nid_response) AND $nid_response['title'] == 'Unauthorized') {                
                        $err_table = new TechnicalError;
                        $err_table->type = 'nid_verification';
                        $err_table->error_details = $nid_response['title'];
                        $err_table->save();

                        return trans('messages.api.nid_unauthorized');
                    }
                    else if (array_key_exists('voter', $nid_response)) { 
                        $voter = $nid_response['voter'];

                        $nameEn = $voter['nameEn'] ?? '';
                        if($nameEn !=''){
                            $fatherEn = $voter['fatherEn'];
                            $motherEn = $voter['motherEn'];
                            $spouseEn = $voter['spouseEn'];
                            $presentAddressEn = $voter['presentAddressEn'];
                            $permanentAddressEn = $voter['permanentAddressEn'];

                            $name = $voter['name'];
                            $father = $voter['father'];
                            $mother = $voter['mother'];
                            $spouse = $voter['spouse'];
                            $presentAddress = $voter['presentAddress'];
                            $permanentAddress = $voter['permanentAddress'];

                            $gender = $voter['gender'];
                            $dob = $voter['dob'];
                            $photo = $voter['photo'];

                            if($gender == 'male') $gender ='1';
                            else if($gender == 'female') $gender ='1';
                            else $gender ='3';

                            list($m,$d,$y) = explode("/", $dob);
                            $dob = $y.'-'.$m.'-'.$d;

                            $table = new Nid;
                            $table->nid = $nid;
                            $table->user_id = $user_id;
                            $table->name_en = $nameEn;
                            $table->father_en = $fatherEn;
                            $table->mother_en = $motherEn;
                            $table->spouse_en = $spouseEn;
                            $table->present_address_en = $presentAddressEn;
                            $table->permanent_address_en = $permanentAddressEn;

                            $table->name = $name;
                            $table->father = $father;
                            $table->mother = $mother;
                            $table->spouse = $spouse;
                            $table->present_address = $presentAddress;
                            $table->permanent_address = $permanentAddress;

                            $table->gender = $gender;
                            $table->dob = $dob;
                            $table->photo = $photo;

                            if($table->save()){                                  
                                $user_table = User::find($user_id);
                                $user_table->nid_number = $nid;
                                $user_table->save();

                                $link = self::base64_to_jpeg($photo, $destinationPath, $nid.'.png');

                                return trans('messages.success');
                            }else{
                                $err_table = new TechnicalError;
                                $err_table->type = 'db_save';
                                $err_table->error_details = 'nid table is not saving record.';
                                $err_table->save();
                                
                                return trans('messages.api.db_not_save');
                            } // if($nameEn !='')
                        }
                        else{
                            return trans('messages.api.db_not_save');
                        }
                    }   
                }else{
                    $photo_name = $nid.".png";
                    $profile_photo = "public/images/nid_photo/".$photo_name;
                
                    if(!file_exists($profile_photo)){                
                        $user_table = User::find($user_id);
                        $user_table->nid_number = $nid;
                        $user_table->save();

                        $base64_string = Nid::where('nid', $nid)->first()->photo;
                        $destinationPath = public_path() . "/images/nid_photo/";                
                        self::base64_to_jpeg($base64_string, $destinationPath, $photo_name);
                    }
                    return trans('messages.api.nid_no_exists');
                }
            }
        }
    }
    */

    public function update_image($nid, $user_id='') {
        $photo_name = $nid.".png";
        $profile_photo = "public/images/nid_photo/".$photo_name;

        if(!file_exists($profile_photo)){   
            if($user_id !='-' AND $user_id !=''){             
                $user_table = User::find($user_id);
                $user_table->nid_number = $nid;
                $user_table->save();
            }

            $base64_string = Nid::where('nid', $nid)->first();

            if(is_object($base64_string)){
                $photo = $base64_string->photo;
                $destinationPath = public_path() . "/images/nid_photo/";                
                self::base64_to_jpeg($photo, $destinationPath, $photo_name);
            }
        }
        
    }
    
    public function verify_nid(Request $request) 
    {       
        $nid = $request->nid;
        $user_id = $request->user_id;
        $try_to_verify = true;

        if($nid =='' OR $user_id ==''){
            $try_to_verify = false;
            return trans('messages.api.nid_required'); 
        }
        else{
            $user_nid_exist = DB::table('users')->where('nid_number', $nid)->first();

            if(is_object($user_nid_exist) AND $user_id != '-'){
                if($user_id == $user_nid_exist->id){
                    if (!is_object(Nid::where('nid', $nid)->first())) {
                        $try_to_verify = true;
                    }
                    else {
                        $try_to_verify = false;
                        self::update_image($nid, $user_id);                        
                        return trans('messages.api.you_used_nid');
                    }
                }else{
                    $try_to_verify = false;
                    self::update_image($nid);  
                    return trans('messages.api.driver_used_nid');
                }                
            }
            
            if($try_to_verify){
                $destinationPath = public_path() . "/images/nid_photo/";
                if (!is_dir($destinationPath)) mkdir($destinationPath, 0777, true);
                
                if (!is_object(Nid::where('nid', $nid)->first())) {
                    $nid_response = $this->doc_helper->nid($nid);
                    if(is_array($nid_response)){
                        if (array_key_exists('message', $nid_response)) {
                            return $nid_response['message'];
                            //return trans('messages.api.nid_digit');
                        }
                        else if(array_key_exists('title', $nid_response) AND $nid_response['title'] == 'Unauthorized') {                
                            $err_table = new TechnicalError;
                            $err_table->type = 'nid_verification';
                            $err_table->error_details = $nid_response['title'];
                            $err_table->save();

                            return trans('messages.api.nid_unauthorized');
                        }
                        else if (array_key_exists('voter', $nid_response)) { 
                            $voter = $nid_response['voter'];

                            $nameEn = $voter['nameEn'] ?? '';
                            if($nameEn !=''){
                                $fatherEn = $voter['fatherEn'];
                                $motherEn = $voter['motherEn'];
                                $spouseEn = $voter['spouseEn'];
                                $presentAddressEn = $voter['presentAddressEn'];
                                $permanentAddressEn = $voter['permanentAddressEn'];

                                $name = $voter['name'];
                                $father = $voter['father'];
                                $mother = $voter['mother'];
                                $spouse = $voter['spouse'];
                                $presentAddress = $voter['presentAddress'];
                                $permanentAddress = $voter['permanentAddress'];

                                $gender = $voter['gender'];
                                $dob = $voter['dob'];
                                $photo = $voter['photo'];

                                if($gender == 'male') $gender ='1';
                                else if($gender == 'female') $gender ='1';
                                else $gender ='3';

                                list($m,$d,$y) = explode("/", $dob);
                                $dob = $y.'-'.$m.'-'.$d;

                                $table = new Nid;
                                $table->nid = $nid;
                                if($user_id !='-'){
                                    $table->user_id = $user_id;
                                }                            
                                $table->name_en = $nameEn;
                                $table->father_en = $fatherEn;
                                $table->mother_en = $motherEn;
                                $table->spouse_en = $spouseEn;
                                $table->present_address_en = $presentAddressEn;
                                $table->permanent_address_en = $permanentAddressEn;

                                $table->name = $name;
                                $table->father = $father;
                                $table->mother = $mother;
                                $table->spouse = $spouse;
                                $table->present_address = $presentAddress;
                                $table->permanent_address = $permanentAddress;

                                $table->gender = $gender;
                                $table->dob = $dob;
                                $table->photo = $photo;

                                if($table->save()){    
                                    if($user_id !='-'){                            
                                        $user_table = User::find($user_id);
                                        $user_table->nid_number = $nid;
                                        $user_table->save();
                                    }

                                    $link = self::base64_to_jpeg($photo, $destinationPath, $nid.'.png');

                                    return trans('messages.success');
                                }else{
                                    $err_table = new TechnicalError;
                                    $err_table->type = 'db_save';
                                    $err_table->error_details = 'nid table is not saving record.';
                                    $err_table->save();
                                    
                                    return trans('messages.api.db_not_save');
                                } // if($nameEn !='')
                            }
                            else{
                                return trans('messages.api.db_not_save');
                            }
                        }  
                    } else return trans('messages.api.db_not_save');
                }else{
                    self::update_image($nid, $user_id);
                    return trans('messages.api.nid_no_exists');
                }
            }
        }
    }

    public function base64_to_jpeg($base64_string, $output_file, $photo_name)
    {
        try{
            $ifp = @fopen($output_file.$photo_name, 'wb');
            if (@$ifp) {
                $data = explode(',', $base64_string);
                fwrite($ifp, base64_decode($data[0]));
                fclose($ifp);
                return "//".env('ADMIN_PANEL_SUB_DOMAIN').".".env('DOMAIN')."/images/nid_photo/".$photo_name;
            }
        }
        catch (Exception $e)
        {
            
        }

    }

    public function set_partner(Request $r)
    {
        $driver_id = $r->driver_id;
        $is_owner = $r->is_owner;
        $partner_id = $r->partner_id;
        $driver = User::find($driver_id);
        
        if($is_owner == '1'){
            $user_table = User::find($driver_id);
            $user_table->is_owner = $is_owner;
            $user_table->save();
            echo '1';
        }
        else if($is_owner == '0'){
            if($partner_id !=''){
                $user_table = User::find($driver_id);
                $user_table->is_owner = $is_owner;
                $user_table->company_id = $partner_id;
                if($user_table->save()) echo "1";
            }else{
                $exits = Company::where('mobile_number', substr($r->mobile_number, -10))->count();
                if ($exits) {
                    echo trans('messages.user.mobile_no_exists');
                }
                else{
                    $last_company_id = Company::orderBy('id', 'DESC')->skip('0')->take('1')->pluck('company_id')->first();
                    if($last_company_id == '' OR $last_company_id == '0') $last_company_id = 1000;
                    else $last_company_id++;

                    $company = new Company;
                    $company->name = $r->name;
                    $company->mobile_number = substr($r->mobile_number, -10);
                    $company->email = $r->email;
                    $company->vat_number = $r->vat_number;
                    $company->address = $r->address;
                    $company->city = $r->city;
                    $company->state = $r->state;
                    $company->status = $r->status;
                    $company->company_id = $last_company_id;

                    if($r->status == ''){
                        $company->status = 'Pending';
                    }
                    else{
                        $company->status = $r->status;
                    }
                    
                    $company->country_code = $driver->country_code;
                    $company->country = $driver->country_code;
                    $company->country_id = $driver->country_id;

                    $company->postal_code = $r->postal_code;
                    $company->company_commission = $r->company_commission;
                    $company->password = substr($r->mobile_number, -10);
                        
                    if($company->save()){
                        $user_table = User::find($driver_id);
                        $user_table->is_owner = $is_owner;
                        $user_table->company_id = $company->id;
                        $user_table->save();
                        echo "1";
                    }  
                }
            }           
        }




        

    }

    public function suggestion_partner(Request $r)
	{
	    $keywords = $r->keywords;
        $first = substr($keywords,0,1);
	    if($first == "0") $keywords = substr($keywords,1);	    

	    if($keywords !=''){
	        $list = DB::table('companies');

	        $list = $list->where(function($query) use ($keywords){
	        	$query->orWhere('name', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('mobile_number', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('company_id', 'LIKE', '%'.$keywords.'%');
	        });
	        $list = $list->get();

	        $output = "";

	        foreach($list as $sl => $data){
	            if($sl == 0) $output.= '<ul id="suggesstion_list">';

	            $label = $data->name." (0".$data->mobile_number." - ".$data->company_id.") ";
	            $val = $data->id;

	            	  
	            $output.= '<li onclick=\'select_from_suggestion("'.$label.'", "'.$val.'")\'> '.$label.'</li>';
	        }
	        echo $output; 
	    }
    }

    public function suggestion_company(Request $r)
	{
	    $keywords = $r->keywords;
        $first = substr($keywords,0,1);
	    if($first == "0") $keywords = substr($keywords,1);	    

	    if($keywords !=''){
	        $list = DB::table('companies');

	        $list = $list->where(function($query) use ($keywords){
	        	$query->orWhere('name', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('mobile_number', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('company_id', 'LIKE', '%'.$keywords.'%');
	        });
	        $list = $list->get();

	        $output = "";

	        foreach($list as $sl => $data){
	            if($sl == 0) $output.= '<ul id="suggesstion_list">';

	            $label = $data->name." (0".$data->mobile_number." - ".$data->company_id.") ";
	            $val = $data->id;	            	  
	            $output.= '<li onclick=\'select_from_company_suggestion("'.$label.'", "'.$val.'")\'> '.$label.'</li>';
	        }
	        echo $output; 
	    }
    }

    public function suggestion_company_driver(Request $r)
	{
	    $company_id = $r->company_id;
        $keywords = $r->keywords;
        $first = substr($keywords,0,1);
	    if($first == "0") $keywords = substr($keywords,1);	          

	    if($keywords !=''){ // AND $company_id !=''
	        $list = DB::table('users');
            if($company_id !='') $list = $list->where('company_id', $company_id);

            $list = $list->where('user_type', 'Driver');

	        $list = $list->where(function($query) use ($keywords){
	        	$query->orWhere('first_name', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('last_name', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('mobile_number', 'LIKE', '%'.$keywords.'%');
	        	$query->orWhere('id', 'LIKE', '%'.$keywords.'%');
	        });
	        $list = $list->get();

	        $output = "";

	        foreach($list as $sl => $data){
	            if($sl == 0) $output.= '<ul id="suggesstion_list">';

	            $label = $data->first_name." ".$data->last_name." (0".$data->mobile_number." - ".$data->id.") ";
	            $val = $data->id;	            	  
	            $output.= '<li onclick=\'select_from_suggestion_company_driver("'.$label.'", "'.$val.'")\'> '.$label.'</li>';
	        }
            echo $output;
	    }
    }


    public function change_vehicle_type(Request $r)
    {
        $id = $r->id;
        $vehicle_type = $r->vehicle_type;

        $vehicle_id = CarType::where('car_name', $vehicle_type)->pluck('id')->first();

        $vehicle = Vehicle::find($id);
        $vehicle->vehicle_type = $vehicle_type;
        $vehicle->vehicle_id = $vehicle_id;
        if($vehicle->save()) echo "Success";
        else echo "Information is not saved. Please try later.";
    }

    public function verify_invalid(Request $r)
    {
        $driver_id = $r->driver_id;
        $driver_document_id = $r->driver_document_id;
        $action = $r->action;
        //echo $driver_id."-".$driver_document_id."-".$action;
        $info = DB::table('driver_documents')
                        ->where('id', $driver_document_id)
                        ->where('user_id', $driver_id)
                        ->first();

        if(is_object($info)){
            if($action == 'Verified'){   
                $arrray = array(
                    'checked' => '1', 
                    'checked_by' => Auth::guard('admin')->user()->id, 
                    'checked_time' => date('Y-m-d H:i:s'),
                    'verified' => '1', 
                    'verified_by' => Auth::guard('admin')->user()->id, 
                    'verified_time' => date('Y-m-d H:i:s'),
                    'status' => '1',   
                );
                $update = DB::table('driver_documents')->where('id', $info->id)->update($arrray);          
            }
            else if($action == 'Invalid'){
                $arrray = array(
                    'checked' => '1', 
                    'checked_by' => Auth::guard('admin')->user()->id, 
                    'checked_time' => date('Y-m-d H:i:s'),
                    'status' => '2', 
                );
                $update = DB::table('driver_documents')->where('id', $info->id)->update($arrray);
            }
        }

        if(isset($update)) echo "1";
        else echo "Information did not saved. Please try later.";
    }

    
}
