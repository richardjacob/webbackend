<?php

/**
 * Vehicle Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Vehicle
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\DataTables\VehicleDataTable;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Country;
use App\Models\CarType;
use App\Models\Company;
use App\Models\DriverDocuments;
use App\Models\Documents;
use App\Models\FilterObject;
use Validator;
use DB;
use Auth;
use App\Models\MakeVehicle;
use App\Models\VehicleModel;
use App\Models\DriverLocation;
use App\Models\VehicleCity;
use App\Models\VehicleRegistrationLetter;
use App\Models\VehicleClass;
use App\Models\VehicleMake;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Load Datatable for Driver
     *
     * @param array $dataTable  Instance of Driver DataTable
     * @return datatable
     */
    public function index(VehicleDataTable $dataTable, Request $request)
    {
        $vehicle_type = $request->vehicle_type;
        $year = $request->year;
        $vehicle_number = $request->vehicle_number;
        $user_id = $request->user_id;

        
        $data['vehicle_type'] = $vehicle_type;
        $data['year'] = $year;
        $data['vehicle_number'] = $vehicle_number;
        $data['user_id'] = $user_id;
        
        $array = array(
            'vehicle_type' => $vehicle_type,
            'year' => $year,
            'vehicle_number' => vehicle_number_bn($vehicle_number),
            'user_id' => $user_id
        );

        return $dataTable->with($array)->render('admin.vehicle.view', $data);
    }

    public function image_type($file_name){
        $file_type = getimagesize($file_name)['mime'];
        $output = "";
        switch ($file_type) {
            case 'image/jpeg': $output = imagecreatefromjpeg($file_name); break;
            case 'image/jpeg': $output = imagecreatefromjpeg($file_name); break;
            case 'image/gif': $output = imagecreatefromgif($file_name); break;
            case 'image/png': $output = imagecreatefrompng($file_name); break;            
            default: $output = ""; break;
        }
        return $output;
    }

    public function merge($filename_x, $filename_y, $filename_result) {
         // Get dimensions for specified images

         list($width_x, $height_x, $type) = getimagesize($filename_x);
         list($width_y, $height_y) = getimagesize($filename_y);

         // Create new image with desired dimensions

         $image = imagecreatetruecolor($width_x + $width_y, $height_x);

         // Load images and then copy to destination image
         $image_x = self::image_type($filename_x);
         $image_y = self::image_type($filename_y);

         //$image_y = imagecreatefromgif($filename_y);
         //$image_y = imagecreatefromjpeg($filename_y);

         imagecopy($image, $image_x, 0, 0, 0, 0, $width_x, $height_x);
         imagecopy($image, $image_y, $width_x, 0, 0, 0, $width_y, $height_y);
         

         // Save the resulting image to disk (as JPEG)

         imagejpeg($image, $filename_result);

         // Clean up

         imagedestroy($image);
         imagedestroy($image_x);
         imagedestroy($image_y);
    }

    /**
     * Add a New Driver
     *
     * @param array $request  Input values
     * @return redirect     to Driver view
     */
    public function add(Request $request)
    {
        $data['city'] = VehicleCity::get();
        $data['letter'] = VehicleRegistrationLetter::get();
        $data['class'] = VehicleClass::get();

        if($request->isMethod('GET')) {
            // Inactive company can not add any vehicle
            if(LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->status != 'Active') {
                abort(404);
            }

            $data['make'] = MakeVehicle::Active()->pluck('make_vehicle_name','id')->toArray();
            $data['model'] = VehicleModel::Active()->pluck('model_name','id')->toArray();
            $data['country_code_option'] = Country::select('long_name','phone_code')->get();
            $data['country_name_option'] = Country::pluck('long_name', 'short_name');
            //$data['company'] = Company::where('status','Active')->pluck('name','id');
            $data['car_type'] = CarType::where('status','Active')->get();
          
            $data['vehicle_documents'] = Documents::where('type', 'Vehicle')->get();

            if(LOGIN_USER_TYPE=='company'){
               $data['company_driver'] = DB::table('users')
                                        ->select('id', DB::raw('CONCAT(first_name, \' \', last_name) AS driver_name'))
                                        ->where('company_id', Auth::guard('company')->user()->id)
                                        ->get();
           }
            

            return view('admin.vehicle.add',$data);
        }

        if($request->submit) {
            $request->vehicle_number = $request->city.' '.$request->reg_letter.' '.$request->vehicle_class.'-'.number_en_to_bn($request->vehicle_number);

            if(LOGIN_USER_TYPE=='company') {
                $request->status = 'Inactive';
            }
           
            $rules = array(
                'driver_name'   => 'required',
                'vehicle_type'  => 'required',
                'vehicle_make_id' => 'required',
                'vehicle_model_id' => 'required',
                'vehicle_number' => 'required|unique:vehicle',
                // 'handicap' => 'required',
                // 'child_seat' => 'required',
                // 'sticker_mode' => 'required',
            );

            if(LOGIN_USER_TYPE!='company') {
                $rules['company_name'] = 'required';
                $rules['status'] = 'required';
            }

            $attributes = array(
                'status'        => trans('messages.driver_dashboard.status'),
                'vehicle_type'  => trans('messages.user.veh_type'),
                'vehicle_make_id' => 'Make',
                'vehicle_model_id' => 'Model',
                // 'handicap' => 'Handicap',
                // 'child_seat' => 'Child Seat',
                // 'sticker_mode'=> 'Sticker Mode',
            );
            
            $messages = array(
                'required'            => ':attribute is required.',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );


            if($request->driver_name) {
                $user = User::find($request->driver_name); 
                $vehicle_doc = CheckDocument('Vehicle',$user->country_code);
            } else {
                $vehicle_doc = CheckDocument('Vehicle','all');
            }
            

            // if($vehicle_doc->count() > 0){
            //     foreach ($vehicle_doc as $key => $value) {
            //         $rules[$value->doc_name] = 'required|mimes:jpg,jpeg,png,gif';
                        
            //         if($request->hasFile($value->doc_name.'_back')){
            //             $rules[$value->doc_name.'_back'] = 'required|mimes:jpg,jpeg,png,gif';
            //         }

            //         if($value->expire_on_date=='Yes') {
            //             $rules['expired_date_'.$value->id] = 'required|date';
            //             $attributes['expired_date_'.$value->id] = 'Expired Date';
            //         }
            //     }
            // }

            $validator = Validator::make($request->all(), $rules,$messages,$attributes);

        

            if($validator->fails()) {
                return back()->withErrors($validator)->withInput();;
            }

            $user_id = $request->driver_name;

            $vehicle = new Vehicle;
            $vehicle->user_id = $request->driver_name;
            $vehicle->company_id = (LOGIN_USER_TYPE != 'company') ? $request->company_name : Auth::guard('company')->user()->id;
            $vehicle->status = $request->status;
            $vehicle->save();

            $options = array();
       

            if($request->has('request_from') && $request->request_from=='1') {
                $options[] = 1;
              
            }
            $options[] = 2;
            $options[] = 3;

            // if($request->handicap=='1') {
            //     $options[] = 2;
            // }
            // if($request->child_seat=='1') {
            //     $options[] = 3;
            // }

            $filter_insert = FilterObject::optionsInsert('vehicle',$vehicle->id,$options);
            
            $vehicle_ids = implode(',', $request->vehicle_type);
            $type_name = '';
            foreach($request->vehicle_type as $vehicle_id) {
                // for vehicle type name
                if($type_name!='') {
                    $delimeter = ',';
                } else {
                    $delimeter = '';
                }
                $car_name = CarType::find($vehicle_id)->car_name;
                $type_name .= $delimeter.$car_name;
            }

            // for default selection
            if($request->default=='1') {
                // check pre default vehicle to update non default
                $pre_default_vehicle = Vehicle::getPreDefaultVehicle($vehicle->user_id,$vehicle_id);

                if($pre_default_vehicle) {
                    // set as non default vehicle
                    $pre_default_vehicle = Vehicle::find($pre_default_vehicle->id);
                    $pre_default_vehicle->default_type = 0;
                    $pre_default_vehicle->save();
                }
                $default = '1';
            } else {
                $default = '0';
            }

            //find is a first vehicle in drver
            $is_second_vehicle = Vehicle::where('user_id',$request->driver_name)->where('default_type','1')->where('is_active',1)->first();
            
            if(!$is_second_vehicle && $request->status=='Active')
                $vehicle->default_type = '1';
            else
                $vehicle->default_type = $request->default;

            $make_name = MakeVehicle::whereId($request->vehicle_make_id)->value('make_vehicle_name');
            $model_name = VehicleModel::whereId($request->vehicle_model_id)->value('model_name');

            $vehicle->vehicle_name      = $make_name.' '.$model_name;           
            $vehicle->vehicle_id        = $vehicle_ids;
            $vehicle->vehicle_number    = $request->vehicle_number;
            $vehicle->vehicle_type      = $type_name;
            $vehicle->vehicle_make_id   = $request->vehicle_make_id; 
            $vehicle->vehicle_model_id  = $request->vehicle_model_id; 
            $vehicle->default_type      = $default; 
            $vehicle->is_active         = $vehicle->status=='Active' ? 1:0;
            $vehicle->color             = $request->color;
            // $vehicle->sticker_mode      = $request->sticker_mode;
            $vehicle->year              = $request->year;
            $vehicle->save();

            if($vehicle_doc->count()){
                $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
                $target_dir = '/images/vehicle/'.$vehicle->id;
                $target_path = asset($target_dir).'/';
                $fileExtAllowed = array("jpg", "jpeg", "gif", "png");
                $r = ltrim($target_dir, '/').'/';

                foreach ($vehicle_doc as $key => $value){
                    if($request->hasFile($value->doc_name)){
                        $time = time();

                        if($request->hasFile($value->doc_name.'_back')){
                            $document_name = $value->doc_name;                    
                            $document = $request->file($document_name);
                            $extension = $document->getClientOriginalExtension();
                            
                            if (in_array(strtolower($extension), $fileExtAllowed)){
                                $options = compact('target_dir');
                                $upload_result = $image_uploader->upload($document,$options);

                                $file_name1 = 'image-'.$time.'.'.$extension;
                                $replace_file_name1 = '1-'.$value->doc_name.$time.'.'.$extension;
                                                           
                                rename($r.$file_name1, $r.$replace_file_name1);
                            }

                            $document_name2 = $value->doc_name.'_back';                    
                            $document2 = $request->file($document_name2);
                            $extension2 = $document2->getClientOriginalExtension();
                            if (in_array(strtolower($extension2), $fileExtAllowed)){
                                $options2 = compact('target_dir');
                                $upload_result2 = $image_uploader->upload($document2,$options2);

                                $file_name2 = 'image-'.$time.'.'.$extension2;
                                $replace_file_name2 = '2-'.$value->doc_name.$time.'.'.$extension2;                       
                                rename($r.$file_name2, $r.$replace_file_name2);
                            }

                            $merged_file = $value->doc_name.$time.'.'.$extension2;

                            if(isset($replace_file_name1) AND isset($replace_file_name2)){
                                self::merge($r.$replace_file_name1, $r.$replace_file_name2, $r.$merged_file);

                                unlink($r.$replace_file_name1);
                                unlink($r.$replace_file_name2);
                                $upload_result['file_name'] = $merged_file;
                            }
                        }
                        else{
                            $document_name = $value->doc_name;
                            $document = $request->file($document_name);
                            $extension = $document->getClientOriginalExtension();
                            $file_name = $document_name."_".time().".".$extension;
                            $options = compact('target_dir','file_name');
                            $upload_result = $image_uploader->upload($document,$options);
                        }

                        if(!$upload_result['status']) {
                            flashMessage('danger', $upload_result['status_message']);
                            return back();
                        }
                        else{
                            $this->transferVehicle(env('ADMIN_PANEL_SUB_DOMAIN'), env('DRIVER_API_SUB_DOMAIN'), $upload_result['file_name'], $vehicle->id);
                        } 

                        $vehicle_doc_update = DriverDocuments::where('type','Vehicle')->where('vehicle_id',$vehicle->id)->where('user_id',$user_id)->where('document_id',$value->id)->first();

                        if($vehicle_doc_update == ''){
                            $vehicle_doc_update = new DriverDocuments;
                        }
                        $document_status = $value->doc_name."_status";
                        
                        $vehicle_doc_update->type       = 'Vehicle';
                        $vehicle_doc_update->vehicle_id = $vehicle->id;
                        $vehicle_doc_update->user_id    = $user_id;
                        $vehicle_doc_update->document_id= $value->id;
                        $vehicle_doc_update->document   = $target_path.$upload_result['file_name'];
                        $vehicle_doc_update->status     = $request->$document_status;
                        $expired_date_key               = 'expired_date_'.$value->id;
                        $vehicle_doc_update->expired_date = $request->$expired_date_key;
                        $vehicle_doc_update->save();    
                        $other_update = 1;             
                    }
                }

                // foreach($vehicle_doc as $key => $value) {
                //     $document_name = $value->doc_name;
                //     $document = $request->file('file_'.$value->id);
                //     $extension = $document->getClientOriginalExtension();
                //     $file_name = $document_name."_".time().".".$extension;
                //     $options = compact('target_dir','file_name');
                //     $upload_result = $image_uploader->upload($document,$options);
                //     if(!$upload_result['status']) {
                //         flashMessage('danger', $upload_result['status_message']);
                //         return back();
                //     }
                //     $vehicle_doc = new DriverDocuments;
                //     $vehicle_doc->type = 'Vehicle';
                //     $vehicle_doc->vehicle_id = $vehicle->id;
                //     $vehicle_doc->user_id = $user->id;
                //     $vehicle_doc->document_id = $value->id;
                //     $vehicle_doc->document = $target_path.$upload_result['file_name'];
                //     $document_status = $value->doc_name."_status";
                //     $vehicle_doc->status = $request->$document_status;

                //     $expired_date_key = 'expired_date_'.$value->id;
                //     $vehicle_doc->expired_date = $request->$expired_date_key;
                //     $vehicle_doc->save();
                // }
            }
            
            $default_vehicle = Vehicle::getPreDefaultVehicle($vehicle->user_id);
            if($vehicle->status=='Inactive') {
                if(!$default_vehicle) {
                    User::where('id', $vehicle->user_id)->update(['status' => 'Car_details']);
                }
            }

            // for default selection update car type in driver location
            if($vehicle->default_type=='1') {

                $driver_location = DriverLocation::where('user_id', $vehicle->user_id)->first();

                if($driver_location) {
                    $dr_location['user_id']     = $vehicle->user_id;
                    $dr_location['latitude']    = $driver_location->latitude;
                    $dr_location['longitude']   = $driver_location->longitude;
                    $dr_location['status']      = $driver_location->status;
                    $dr_location['pool_trip_id']= $driver_location->pool_trip_id;

                    foreach($request->vehicle_type as $vehicle_type) {
                        $dr_location['car_id'] = $vehicle_type;
                        DriverLocation::updateOrCreate(['user_id' => $vehicle->user_id, 'car_id' => $vehicle_type], $dr_location);
                    }
                    DriverLocation::where('user_id',$vehicle->user_id)->whereNotIn('car_id',$request->vehicle_type)->delete();
                }                
            }
    
            flashMessage('success', trans('messages.user.add_success'));
        }

        return redirect(LOGIN_USER_TYPE.'/vehicle');
    }

    /**
     * Update Driver Details
     *
     * @param array $request    Input values
     * @return redirect     to Driver View
     */
    public function update(Request $request)
    {
        // $data['city'] = VehicleCity::select('city')->get();
        // $data['letter'] = VehicleRegistrationLetter::select('reg_letter')->get();
        // $data['class'] = VehicleClass::select('id','vehicle_class')->get();

        $data['city'] = VehicleCity::get();
        $data['letter'] = VehicleRegistrationLetter::get();
        $data['class'] = VehicleClass::get();
        
        if($request->isMethod("GET")) {
            $data['result'] = Vehicle::find($request->id);
            $data['make'] = MakeVehicle::Active()->pluck('make_vehicle_name','id')->toArray();
            if($data['result']) {
                $data['country_code_option']= Country::select('long_name','phone_code')->get();
                $data['car_type']           = CarType::where('status','Active')->get();
                $data['company']            = Company::whereId($data['result']->company_id)->first();
                $data['options']            = FilterObject::options('vehicle',$request->id);
                $data['path']               = url('images/users/'.$request->id);

               $user = User::find($data['result']->user_id);
               $country_code = $user->country_code;
               $data['vehicle_documents'] = CheckDocument('Vehicle',$country_code);
               //$data['user_documents']  =  DriverDocuments::where('user_id', $user->id)->get();
               $data['user_documents']  =  DriverDocuments::where('user_id', $user->id)->where('vehicle_id', $request->id)->get();
                
                return view('admin.vehicle.edit', $data);
            } else {
                flashMessage('danger', 'Invalid ID');
                return redirect(LOGIN_USER_TYPE.'/vehicle'); 
            }
        }

        if($request->submit) {
            $request->vehicle_number = $request->city.' '.$request->reg_letter.' '.$request->vehicle_class.'-'.number_en_to_bn($request->vehicle_number);
            
            $rules = array(
                
                'vehicle_type'  => 'required',
                'vehicle_make_id' =>'required',
                'vehicle_model_id' =>'required',
                'vehicle_number' => 'required',
                'color' =>'required',
                'year' => 'required',
                'vehicle_number' => 'required|unique:vehicle,vehicle_number,'.$request->id,
                // 'handicap' => 'required',
                // 'child_seat' => 'required',
                // 'sticker_mode' => 'required',
            );

            if(LOGIN_USER_TYPE!='company') {
                $rules['company_name'] = 'required';
                $rules['status'] = 'required';
            }

            $attributes = array(
                'permit'        => trans('messages.user.permit'),
                'vehicle_type'  => trans('messages.user.veh_type'),
                'vehicle_name'  => trans('messages.user.veh_name'),
                'vehicle_number'=> trans('messages.user.veh_no'),
                // 'handicap' => 'Handicap',
                // 'child_seat' => 'Child Seat',
                // 'sticker_mode'=> 'Sticker Mode',
            );

            $messages = array(
                'required'            => ':attribute is required.',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );

            $user = User::find($request->driver_name);
            $vehicle_documents = UserDocuments('Vehicle',$user,$request->id);
            $result = json_decode($vehicle_documents, true);
            foreach($result as $key => $value) {
                //validate for all document previous
                if($value['document'] == ''){
                    $rules[$value['doc_name']] = 'required|mimes:jpg,jpeg,png,gif';
                        
                    if($request->hasFile($value['doc_name'].'_back')){
                        $rules[$value['doc_name'].'_back'] = 'required|mimes:jpg,jpeg,png,gif';
                    }
                }
                if($value['expiry_required']=='1') {
                    if($value['expired_date'] == '' || $value['expired_date']=='0000-00-00') {
                        $rules['expired_date_'.$value['id']] = 'required|date';
                        $attributes['expired_date_'.$value['id']] = 'Expired Date';
                    }
                }                
            }

            $validator = Validator::make($request->all(), $rules,$messages,$attributes);

            if($validator->fails()) {
                //return back()->withErrors($validator)->withInput();
            }
            $vehicle = Vehicle::find($request->id);
            if(LOGIN_USER_TYPE!='company') {
               
                $vehicle->status = $request->status;
                $vehicle->save();
            }else {
                $vehicle->status = "Inactive";
                $vehicle->save();
            }

            $options = array();
            if($request->has('request_from') && $request->request_from=='1') {
                $options[] = 1;
            }
            $options[] = 2;
            $options[] = 3;

            // if($request->handicap=='1') {
            //     $options[] = 2;
            // }
            // if($request->child_seat=='1') {
            //     $options[] = 3;
            // }


            $filter_insert = FilterObject::optionsInsert('vehicle',$vehicle->id,$options);

            $vehicle_ids = implode(',', $request->vehicle_type);
            $type_name = '';
            foreach($request->vehicle_type as $vehicle_id) {
                // for vehicle type name
                if($type_name!='') {
                    $delimeter = ',';
                } else {
                    $delimeter = '';
                }
                $car_name = CarType::find($vehicle_id)->car_name;
                $type_name .= $delimeter.$car_name;
            }

            // for default selection update pre default vehicle
            if($request->default==1) {
                // get pre default vehicle
                $pre_default_vehicle = Vehicle::getPreDefaultVehicle($vehicle->user_id,$vehicle_id);

                if($pre_default_vehicle) {
                    // set as non default vehicle
                    $pre_default_vehicle = Vehicle::find($pre_default_vehicle->id);
                    $pre_default_vehicle->default_type = '0';
                    $pre_default_vehicle->save();
                }
            }

            //find is a first vehicle in drver
            $is_second_vehicle = Vehicle::where('user_id',$request->driver_name)->where('default_type','1')->where('is_active',1)->first();

            $make_name = MakeVehicle::whereId($request->vehicle_make_id)->value('make_vehicle_name');
            $model_name = VehicleModel::whereId($request->vehicle_model_id)->value('model_name');

            $vehicle = Vehicle::find($request->id);
            
            if(!$is_second_vehicle && $request->status=='Active')
                $vehicle->default_type = '1';
            else
                $vehicle->default_type = $request->default;
           
            $vehicle->vehicle_name      = $make_name.' '.$model_name;
            $vehicle->vehicle_id        = $vehicle_ids;
            $vehicle->vehicle_number    = $request->vehicle_number;
            $vehicle->vehicle_type      = $type_name;
            $vehicle->vehicle_make_id   = $request->vehicle_make_id; 
            $vehicle->vehicle_model_id  = $request->vehicle_model_id; 
            $vehicle->color             = $request->color;
            // $vehicle->sticker_mode      = $request->sticker_mode;
            $vehicle->is_active         = $vehicle->status=='Active' ? 1:0;
            $vehicle->year              = $request->year;
            $vehicle->save();

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
            $target_dir = '/images/vehicle/'.$vehicle->id;
            $target_path = asset($target_dir).'/';
            $fileExtAllowed = array("jpg", "jpeg", "gif", "png");
            $r = ltrim($target_dir, '/').'/';

            $user = User::find($request->driver_name); 
            $vehicle_doc = CheckDocument('Vehicle',$user->country_code);
            if($vehicle_doc){
                foreach ($vehicle_doc as $key => $value) {
                    if($request->hasFile($value->doc_name)){
                        $time = time();

                        if($request->hasFile($value->doc_name.'_back')){
                            $document_name = $value->doc_name;                    
                            $document = $request->file($document_name);
                            $extension = $document->getClientOriginalExtension();
                            
                            if (in_array(strtolower($extension), $fileExtAllowed)){
                                $options = compact('target_dir');
                                $upload_result = $image_uploader->upload($document,$options);

                                $file_name1 = 'image-'.$time.'.'.$extension;
                                $replace_file_name1 = '1-'.$value->doc_name.$time.'.'.$extension;
                                                           
                                rename($r.$file_name1, $r.$replace_file_name1);
                            }

                            $document_name2 = $value->doc_name.'_back';                    
                            $document2 = $request->file($document_name2);
                            $extension2 = $document2->getClientOriginalExtension();
                            if (in_array(strtolower($extension2), $fileExtAllowed)){
                                $options2 = compact('target_dir');
                                $upload_result2 = $image_uploader->upload($document2,$options2);

                                $file_name2 = 'image-'.$time.'.'.$extension2;
                                $replace_file_name2 = '2-'.$value->doc_name.$time.'.'.$extension2;                       
                                rename($r.$file_name2, $r.$replace_file_name2);
                            }

                            $merged_file = $value->doc_name.$time.'.'.$extension2;

                            if(isset($replace_file_name1) AND isset($replace_file_name2)){
                                self::merge($r.$replace_file_name1, $r.$replace_file_name2, $r.$merged_file);

                                unlink($r.$replace_file_name1);
                                unlink($r.$replace_file_name2);
                                $upload_result['file_name'] = $merged_file;
                            }
                            else{
                                flashMessage('danger', 'Image did not merged.');
                                return back();
                            }
                        }
                        else{
                            $document_name = $value->doc_name;
                            $document = $request->file($document_name);
                            $extension = $document->getClientOriginalExtension();
                            $file_name = $document_name."_".time().".".$extension;
                            $options = compact('target_dir','file_name');
                            $upload_result = $image_uploader->upload($document,$options);
                        }
                        
                        if(!$upload_result['status']) {
                            flashMessage('danger', $upload_result['status_message']);
                            return back();
                        }else{
                            $this->transferVehicle(env('ADMIN_PANEL_SUB_DOMAIN'), env('DRIVER_API_SUB_DOMAIN'), $upload_result['file_name'], $vehicle->id);
                        }

                        $user_doc = DriverDocuments::where('type','Vehicle')->where('vehicle_id',$vehicle->id)->where('user_id',$user->id)->where('document_id',$value->id)->first();

                        if($user_doc == ''){
                            $user_doc = new DriverDocuments;
                        }
                        $user_doc->type = 'Vehicle';
                        $user_doc->vehicle_id = $vehicle->id;
                        $user_doc->user_id = $user->id;
                        $user_doc->document_id = $value->id;
                        $user_doc->document = $target_path.$upload_result['file_name'];
                        $document_status = $value->doc_name."_status";
                        $user_doc->status = $request->$document_status; 
                        $user_doc->save();
                    }                   
                }                
                $DeleteOldDocument = DriverDocuments::where('type','Vehicle')->where('vehicle_id',$vehicle->id)->where('user_id',$user->id)->whereNotIn('document_id',$vehicle_doc->pluck('id')->toArray())->delete();
                foreach ($vehicle_doc as $key => $value) {
                    $user_doc = DriverDocuments::where('type','Vehicle')->where('vehicle_id',$vehicle->id)->where('user_id',$user->id)->where('document_id',$value->id)->first();
                    
                    if(is_object($user_doc) AND isset($document_status) AND isset($request->$document_status) AND $request->$document_status !=''){
                        $document_status = $value->doc_name."_status";
                        $user_doc->status = $request->$document_status; 

                        $expired_date_key = 'expired_date_'.$value->id;
                        $user_doc->expired_date = $request->$expired_date_key;

                        $user_doc->save();
                    }
                }
            }

            if($vehicle->status=='Inactive') {
                $default_vehicle = Vehicle::getPreDefaultVehicle($vehicle->user_id);
                if(!$default_vehicle) {
                    DriverLocation::where('user_id',$vehicle->user_id)->delete();
                    User::where('id', $vehicle->user_id)->update(['status' => 'Car_details']);
                }
            }

            // for default selection update car type in driver location
            if($vehicle->default_type=='1') {

                $driver_location = DriverLocation::where('user_id', $vehicle->user_id)->first();

                if($driver_location) {
                    $dr_location['user_id']     = $vehicle->user_id;
                    $dr_location['latitude']    = $driver_location->latitude;
                    $dr_location['longitude']   = $driver_location->longitude;
                    $dr_location['status']      = $driver_location->status;
                    $dr_location['pool_trip_id']= $driver_location->pool_trip_id;

                    foreach($request->vehicle_type as $vehicle_type) {
                        $dr_location['car_id'] = $vehicle_type;
                        DriverLocation::updateOrCreate(['user_id' => $vehicle->user_id, 'car_id' => $vehicle_type], $dr_location);
                    }
                    DriverLocation::where('user_id',$vehicle->user_id)->whereNotIn('car_id',$request->vehicle_type)->delete();
                }                
            }

            flashMessage('success', 'Updated Successfully');
            return redirect(LOGIN_USER_TYPE.'/vehicle');
        }

        return redirect(LOGIN_USER_TYPE.'/vehicle');
    }

    /**
     * Delete Driver
     *
     * @param array $request    Input values
     * @return redirect     to Driver View
     */
    public function delete(Request $request)
    {    
        $vehicle = Vehicle::find($request->id);

        //If login user is company then it can edit it's vehicle only
        if($vehicle==null || (LOGIN_USER_TYPE=='company' && $vehicle->company_id != Auth::guard('company')->user()->id)) {
            flashMessage('danger', 'Invalid ID');
            return redirect(LOGIN_USER_TYPE.'/vehicle');
        } else if($vehicle->default_type=='1') {
            flashMessage('danger', 'Default vehicle couldn\'t be deleted.');
            return redirect(LOGIN_USER_TYPE.'/vehicle');
        }

        try {
            $vehicle->delete();
            $filters_delete = FilterObject::whereObjectId($request->id)->delete();
            flashMessage('success', 'Deleted Successfully');            
        } catch (\Exception $e) {
            flashMessage('danger', $e->getMessage());
        }

        return redirect(LOGIN_USER_TYPE.'/vehicle');
    }

    /**
     * get Driver
     *
     * @param array $request Input values
     * @return response json
     */
    public function get_driver(Request $request,$company_id) {

        $drivers = User::select('id','first_name','last_name','country_code','gender')->where('user_type','Driver')->where('company_id',$company_id)->get();

        $drivers_list = $drivers->map(function($driver) {
            return [
                'id' => $driver->id,
                'first_name' => $driver->first_name,
                'last_name' => $driver->last_name,
                'country_code' => $driver->country_code,
                'gender' => $driver->gender,
                'vehicle_ids' => $driver->vehicles->pluck('vehicle_id'),
            ];
        });

        return response()->json([
            'status_code' => '1',
            'drivers' => $drivers_list,
        ]);
    }

    /**
     * validate unique vehicle number
     *
     * @param array $request Input values
     * @return response boolean
     */
    public function validate_vehicle_number(Request $request) {
        if(\Request::ajax()) {
            return Vehicle::whereVehicleNumber($request->vehicle_number)->where('id', '!=', $request->vehicle_id)->exists();
        }
    }

    /**
     * check pre default vehicle in trips or not
     *
     * @param array $request Input values
     * @return response numeric
     */
    public function check_default(Request $request) {
        if(\Request::ajax()) {
            return checkDefault($request->driver_id,$request->vehicle_id,$request->default);
        }
    }

    public function change_vehicle($vehicle_id, Request $request)
    {
        if ($request->isMethod("GET")) {   
            $vehicle = Vehicle::find($vehicle_id);
            if(is_object($vehicle)){
                $user_id = $vehicle->user_id;
                $driver = User::where('user_type', 'Driver')->find($vehicle->user_id);
                $company = Company::find($vehicle->company_id);
                $vehicle_model = VehicleModel::find($vehicle->vehicle_model_id);
                $vehicle_make = VehicleMake::find($vehicle->vehicle_make_id);
                $registration_paper = DriverDocuments::where('type', 'Vehicle')
                                                ->where('document_id', '6')
                                                ->where('vehicle_id', $vehicle_id)
                                                ->pluck('document')
                                                ->first();

                if(is_object($driver)){
                    if(is_object($vehicle_model)) $data['vehicle_model'] = $vehicle_model->model_name;
                    else $data['vehicle_model'] = '';

                    if(is_object($vehicle_make)) $data['vehicle_make'] = $vehicle_make->make_vehicle_name;
                    else $data['vehicle_make'] = '';

                    $data['company'] = $company;
                    $data['driver'] = $driver; 
                    $data['vehicle'] = $vehicle;
                    $data['registration_paper'] = $registration_paper;

                    return view('admin.vehicle.change_vehicle', $data);
                }
                else{
                    flashMessage('error', 'Driver id is not correct');
                    return back();
                }
            }
            else{
                flashMessage('error', 'Vehicle is not found');
                return back();
            }
        }
        else{            
            $vehicle_id = $request->vehicle_id;
            $driver_id = $request->driver_id;
            $confirm = $request->confirm;

            $rules = array(
                'driver_id'    => 'required',
                'confirm'       => 'required'
            );                       
            
            $attributes['driver_id']   = 'New Driver';
            $attributes['confirm']     = 'Confurmation';

            $messages = array(
                'required'            => ':attribute is required.'
            );
           
            $validator = Validator::make($request->all(), $rules, $messages, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $updated = DB::table('driver_documents')
                                ->where('vehicle_id', $vehicle_id)
                                ->update(array('user_id' => $driver_id));

            //if($updated){
                $vehicle_table = Vehicle::find($vehicle_id);
                $vehicle_table->user_id = $driver_id;
                $vehicle_table->company_id = DB::table('users')->find($driver_id)->company_id;
                $vehicle_table->save();
            //}
            
            flashMessage('success', trans('messages.user.update_success'));
            return redirect(LOGIN_USER_TYPE . '/vehicle');

        }
    }

    public function view($vehicle_id)
    {
            $vehicle = Vehicle::find($vehicle_id);
            if(is_object($vehicle)){
                $user_id = $vehicle->user_id;
                $driver = User::where('user_type', 'Driver')->find($vehicle->user_id);
                $company = Company::find($vehicle->company_id);
                $vehicle_model = VehicleModel::find($vehicle->vehicle_model_id);
                $vehicle_make = VehicleMake::find($vehicle->vehicle_make_id);
                $registration_paper = DriverDocuments::where('type', 'Vehicle')
                                                ->where('document_id', '6')
                                                ->where('vehicle_id', $vehicle_id)
                                                ->pluck('document')
                                                ->first();

                if(is_object($driver)){
                    if(is_object($vehicle_model)) $data['vehicle_model'] = $vehicle_model->model_name;
                    else $data['vehicle_model'] = '';

                    if(is_object($vehicle_make)) $data['vehicle_make'] = $vehicle_make->make_vehicle_name;
                    else $data['vehicle_make'] = '';

                    $data['company'] = $company;
                    $data['driver'] = $driver; 
                    $data['vehicle'] = $vehicle;
                    $data['registration_paper'] = $registration_paper;

                    return view('admin.vehicle.view_single_vehicle', $data);
                }
                else{
                    flashMessage('error', 'Driver id is not correct');
                    return back();
                }
            }
            else{
                flashMessage('error', 'Vehicle is not found');
                return back();
            }
        
    }

}
