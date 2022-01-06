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
use App\DataTables\DriverDataTable;
use App\Models\User;
use App\Models\Trips;
use App\Models\DriverAddress;
use App\Models\DriverDocuments;
use App\Models\Country;
use App\Models\CarType;
use App\Models\ProfilePicture;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\ReferralUser;
use App\Models\DriverOweAmount;
use App\Models\PayoutPreference;
use App\Models\PayoutCredentials;
use App\Models\Documents;
use App\Models\Bonus;
use App\Models\Optional;
use App\Models\Nid;
use App\Models\DriverBalance;
use Validator;
use DB;
use Image;
use Auth;

class DriverController extends Controller
{
    /**
     * Load Datatable for Driver
     *
     * @param array $dataTable  Instance of Driver DataTable
     * @return datatable
     */

    public function __construct()
    {
        $this->bonus_helper = resolve('App\Http\Helper\BonusHelper');
        $card_slip = Optional::where('type', 'card_slip')->select('value', 'name_en')->get();
        $nid_passport = Optional::where('type', 'nid_passport')->select('value', 'name_en')->get();

        foreach ($card_slip as $value_name) {
            $card_slip_array[$value_name->value] = $value_name->name_en;
        }

        foreach ($nid_passport as $value_name) {
            $nid_passport_array[$value_name->value] = $value_name->name_en;
        }

        $this->card_slip_array = $card_slip_array;
        $this->nid_passport_array = $nid_passport_array;
    }

    public function image_type($file_name)
    {
        $file_type = getimagesize($file_name)['mime'];
        $output = "";
        switch ($file_type) {
            case 'image/jpeg':
                $output = imagecreatefromjpeg($file_name);
                break;
            case 'image/jpeg':
                $output = imagecreatefromjpeg($file_name);
                break;
            case 'image/gif':
                $output = imagecreatefromgif($file_name);
                break;
            case 'image/png':
                $output = imagecreatefrompng($file_name);
                break;
            default:
                $output = "";
                break;
        }
        return $output;
    }

    public function merge($filename_x, $filename_y, $filename_result)
    {
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

    public function index(DriverDataTable $dataTable, Request $r)
    {  
        $driver_id = $r->driver_id;
        $start_date = $r->start_date;
        $end_date = $r->end_date;

        $data['driver_id'] = $driver_id;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        
        $array = array(
            'driver_id' => $driver_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        );
        return $dataTable->with($array)->render('admin.driver.view', $data);
    }

    public function get_documents(Request $request)
    {

        $country_code = $request->country_code;

        $vehicle_id = 0;
        if (isset($request->vehicle_id)) {
            $vehicle_id = $request->vehicle_id;
        }

        if ($request->document_for == 'Company') {
            $user = Company::find($request->user_id);
        } else {
            $user = User::find($request->user_id);
        }

        if (isset($user->country_code) && $country_code == $user->country_code) {
            $driver_doc = UserDocuments($request->document_for, $user, $vehicle_id);
            $driver_doc = json_decode($driver_doc);

            if ($request->document_for == 'Vehicle') {
                foreach ($driver_doc as $key => $doc) {
                    if ($doc->country_code != 'all' && $doc->country_code != $user->country_code) {
                        unset($driver_doc[$key]);
                    }
                }
            }
        } else {
            $driver_doc = CheckDocument($request->document_for, $country_code);
        }

        return json_encode($driver_doc);
    }

    /**
     * Add a New Driver
     *
     * @param array $request  Input values
     * @return redirect     to Driver view
     */
    public function add(Request $request)
    {
        if ($request->isMethod("GET")) {
            //Inactive Company could not add driver
            if (LOGIN_USER_TYPE == 'company' && Auth::guard('company')->user()->status != 'Active') {
                abort(404);
            }
            $data['country_code_option'] = Country::select('long_name', 'phone_code', 'id')->get();
            $data['country_name_option'] = Country::pluck('long_name', 'short_name');
            $data['company'] = Company::where('status', 'Active')->pluck('name', 'id');
            $driver_doc = Documents::Active()->where(['type' => 'Driver', 'country_code' => 'all'])->select('id', 'document_name', 'expire_on_date')->get();
            $data['driver_doc'] = $driver_doc;  //json_encode($driver_doc);
            $data['card_slip_array'] = $this->card_slip_array;
            $data['nid_passport_array'] = $this->nid_passport_array;

            return view('admin.driver.add', $data);
        }
        if ($request->submit) {
            // Add Driver Validation Rules
            $rules = array(
                'first_name'    => 'required',
                'last_name'     => 'required',
                //'email'         => 'required|email',
                'mobile_number' => 'required|numeric|regex:/\+?(88)?0?1[356789][0-9]{8}\b/',
                'password'      => ['required', 'string', 'min:8', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/',],
                'country_code'  => 'required',
                'gender'        => 'required',
                'user_type'     => 'required',
               
            );

            $driver_doc = CheckDocument('Driver', $request->country_code ?? 'all');

            if ($driver_doc->count() > 0) {
                foreach ($driver_doc as $key => $value) {
                    // $rules['file_'.$value->id] = 'required|mimes:jpg,jpeg,png,gif';
                    // $attributes['file_'.$value->id] = $value->doc_name;

                    $rules[$value->doc_name] = 'required|mimes:jpg,jpeg,png,gif';
                    $rules[$value->doc_name . "_document_type"] = 'required';
                    if ($request->hasFile($value->doc_name . '_back')) {
                        $rules[$value->doc_name . '_back'] = 'mimes:jpg,jpeg,png,gif';
                    }

                    if ($value->expire_on_date == 'Yes') {
                        $rules['expired_date_' . $value->id] = 'required|date|date_format:Y-m-d';
                        $attributes['expired_date_' . $value->id] = 'Expired Date';
                    }
                }
            }

            //Bank details are required only for company drivers & Not required for Admin drivers
            // if ((LOGIN_USER_TYPE != 'company' && $request->company_name != 1) || (LOGIN_USER_TYPE == 'company' && Auth::guard('company')->user()->id != 1)) {
            //     $rules['account_holder_name'] = 'required';
            //     $rules['account_number'] = 'required';
            //     $rules['bank_name'] = 'required';
            //     $rules['bank_location'] = 'required';
            //     $rules['bank_code'] = 'required';
            // }

            if (LOGIN_USER_TYPE != 'company') {
                $rules['company_name'] = 'required';
                $rules['status'] = 'required';
            }
            if ($request->profile_picture != '') {
                $rules['profile_picture'] = 'mimes:jpg,jpeg,png,gif';
            }
            // Add Driver Validation Custom Names
            $attributes['first_name']   = trans('messages.user.firstname');
            $attributes['last_name']    = trans('messages.user.lastname');
            //$attributes['email']        = trans('messages.user.email');
            $attributes['password']     = trans('messages.user.paswrd');
            $attributes['country_code'] = trans('messages.user.country_code');
            $attributes['gender']       = trans('messages.profile.gender');
            $attributes['user_type']    = trans('messages.user.user_type');
            $attributes['status']       = trans('messages.driver_dashboard.status');
            $attributes['account_holder_name'] = 'Account Holder Name';
            $attributes['account_number'] = 'Account Number';
            $attributes['bank_name']    = 'Name of Bank';
            $attributes['bank_location'] = 'Bank Location';
            $attributes['bank_code']    = 'BIC/SWIFT Code';

            // Edit Rider Validation Custom Fields message
            $messages = array(
                'required'            => ':attribute is required.',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
                'password.regex' => 'Your password must be at least 8 characters With Alphabet And Number',
            );

           

            $validator = Validator::make($request->all(), $rules, $messages, $attributes);

            $validator->after(function ($validator) use ($request) {
                $user = User::where('mobile_number', substr($request->mobile_number, -10))->where('user_type', $request->user_type)->where('country_id', $request->country_id)->count();

                $user_email = User::where('email', $request->email)->where('email', '!=', '')->where('user_type', $request->user_type)->count();

                if ($user) {
                    $validator->errors()->add('mobile_number', trans('messages.user.mobile_no_exists'));
                }

                if ($user_email) {
                    $validator->errors()->add('email', trans('messages.user.email_exists'));
                }
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            if ($request->status == "Active") {
                flashMessage('danger', 'Please ensure the driver has atleast one default vehicle, if not you can\'t activate.');
                return back()->withInput();
            }

            $user = new User;

            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            $user->country_code = $request->country_code;
            $user->country_id   = $request->country_id;
            $user->gender       = $request->gender;
            $user->mobile_number = substr($request->mobile_number, -10);
            $user->unique_id     = substr($request->mobile_number, -10) . "_" . strtolower($request->user_type);
            $user->password     = $request->password;
            if (LOGIN_USER_TYPE == 'admin') {
                $user->status       = $request->status;
            }else {
                $user->status       = "Pending";
            }
            $user->user_type    = $request->user_type;
            $user->passport_number    = $request->passport_number;
            
            $user->driving_licence_number    = $request->driving_licence_number;
            if (LOGIN_USER_TYPE == 'company') {
                $user->company_id = Auth::guard('company')->user()->id;
            } else {
                $user->company_id = $request->company_name;
            }
            $user->save();            

            if($request->nid_number !=''){
                $nid_info = Nid::where('nid', $request->nid_number)->first();
                if(is_object($nid_info)){
                    if($nid_info->user_id == ''){
                        $nid_table = Nid::find($nid_info->id);
                        $nid_table->user_id = $user->id;
                        if($nid_table->save()){
                            $user_table = User::find($user->id);
                            $user_table->nid_number = $request->nid_number;
                            $user_table->save();
                        }
                    }
                }
                
                
            }

            //$this->bonus_helper->driver_signup_bonus($user);
            $this->bonus_helper->set_driver_online_bonus($user);

            if ($request->profile_picture != '') {
                //profile_picture
                $user_profile_image = new ProfilePicture;
                $user_profile_image->user_id = $user->id;
                
                $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
                $target_dir = '/images/users/' . $user->id;
                $target_path = asset($target_dir) . '/';

                if ($request->hasFile('profile_picture')) {
                    $image = $request->file('profile_picture');

                    $extension = $image->getClientOriginalExtension();
                    $file_name = "profile_pic_" . time() . "." . $extension;
                    $compress_size = array(
                        ["height" => 225, "width" => 225],
                    );
                    $options = compact('target_dir', 'file_name', 'compress_size');


                    $upload_result = $image_uploader->upload($image, $options);
                    if (!$upload_result['status']) {
                        return response()->json([
                            'status_code'       => "0",
                            'status_message'    => $upload_result['status_message'],
                        ]);
                    }else{
                        if($upload_result['file_name'] !=''){
                            $this->transferFile(env('ADMIN_PANEL_SUB_DOMAIN'), env('DRIVER_API_SUB_DOMAIN'), $upload_result['file_name'], $user->id);
                        }
                    }

                    $user_profile_image->photo_source = 'Local';
                    $user_profile_image->src = $target_path . $file_name;
                    $user_profile_image->save();
                }
            }else {
                //profile_picture
                $user_pic = new ProfilePicture;
                $user_pic->user_id      = $user->id;
                $user_pic->src          = '';
                $user_pic->photo_source = 'Local';
                $user_pic->save();

            }















            $user_address = new DriverAddress;
            $user_address->user_id       = $user->id;
            $user_address->address_line1 = $request->address_line1 ? $request->address_line1 : '';
            $user_address->address_line2 = $request->address_line2 ? $request->address_line2 : '';
            $user_address->city          = $request->city ? $request->city : '';
            $user_address->state         = $request->state ? $request->state : '';
            $user_address->postal_code   = $request->postal_code ? $request->postal_code : '';
            $user_address->save();

            // if ($user->company_id != null && $user->company_id != 1) {
            //     $payout_preference = PayoutPreference::firstOrNew(['user_id' => $user->id, 'payout_method' => "BankTransfer"]);
            //     $payout_preference->user_id = $user->id;
            //     $payout_preference->country = "IN";
            //     $payout_preference->account_number  = $request->account_number;
            //     $payout_preference->holder_name     = $request->account_holder_name;
            //     $payout_preference->holder_type     = "company";
            //     $payout_preference->paypal_email    = $request->account_number;

            //     $payout_preference->phone_number    = $request->mobile_number ?? '';
            //     $payout_preference->branch_code     = $request->bank_code ?? '';
            //     $payout_preference->bank_name       = $request->bank_name ?? '';
            //     $payout_preference->bank_location   = $request->bank_location ?? '';
            //     $payout_preference->payout_method   = "BankTransfer";
            //     $payout_preference->address_kanji   = json_encode([]);
            //     $payout_preference->save();

            //     $payout_credentials = PayoutCredentials::firstOrNew(['user_id' => $user->id, 'type' => "BankTransfer"]);
            //     $payout_credentials->user_id = $user->id;
            //     $payout_credentials->preference_id = $payout_preference->id;
            //     $payout_credentials->payout_id = $request->account_number;
            //     $payout_credentials->type = "BankTransfer";
            //     $payout_credentials->default = 'yes';
            //     $payout_credentials->save();
            // }

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
            $target_dir = '/images/users/' . $user->id;
            $target_path = asset($target_dir) . '/';
            $fileExtAllowed = array("jpg", "jpeg", "gif", "png");
            $r = ltrim($target_dir, '/') . '/';

            if ($driver_doc) {
                foreach ($driver_doc as $key => $value) {
                    if ($request->hasFile($value->doc_name)) {
                        $time = time();
                        if ($request->hasFile($value->doc_name . '_back')) {
                            $document_name = $value->doc_name;
                            $document = $request->file($document_name);
                            $extension = $document->getClientOriginalExtension();

                            if (in_array(strtolower($extension), $fileExtAllowed)) {
                                $options = compact('target_dir');
                                $upload_result = $image_uploader->upload($document, $options);

                                $file_name1 = 'image-' . $time . '.' . $extension;
                                $replace_file_name1 = '1-' . $value->doc_name . $time . '.' . $extension;

                                rename($r . $file_name1, $r . $replace_file_name1);
                            }

                            $document_name2 = $value->doc_name . '_back';
                            $document2 = $request->file($document_name2);
                            $extension2 = $document2->getClientOriginalExtension();
                            if (in_array(strtolower($extension2), $fileExtAllowed)) {
                                $options2 = compact('target_dir');
                                $upload_result2 = $image_uploader->upload($document2, $options2);

                                $file_name2 = 'image-' . $time . '.' . $extension2;
                                $replace_file_name2 = '2-' . $value->doc_name . $time . '.' . $extension2;
                                rename($r . $file_name2, $r . $replace_file_name2);
                            }

                            $merged_file = $value->doc_name . $time . '.' . $extension2;

                            if (isset($replace_file_name1) and isset($replace_file_name2)) {
                                self::merge($r . $replace_file_name1, $r . $replace_file_name2, $r . $merged_file);

                                unlink($r . $replace_file_name1);
                                unlink($r . $replace_file_name2);
                                $upload_result['file_name'] = $merged_file;
                            } else {
                                flashMessage('danger', 'Image did not merged.');
                                return back();
                            }
                        } else {
                            $document_name = $value->doc_name;
                            $document = $request->file($document_name);
                            $extension = $document->getClientOriginalExtension();
                            $file_name = $document_name . "_" . time() . "." . $extension;
                            $options = compact('target_dir', 'file_name');
                            $upload_result = $image_uploader->upload($document, $options);
                        }

                        if (!$upload_result['status']) {
                            flashMessage('danger', $upload_result['status_message']);
                            return back();
                        }

                        $document_status = $value->doc_name . "_status";
                        $document_type = $value->doc_name . "_document_type";
                        $expired_date_key = 'expired_date_' . $value->id;

                        $user_doc = new DriverDocuments;
                        $user_doc->user_id = $user->id;
                        $user_doc->document_id = $value->id;
                        $user_doc->document = $target_path . $upload_result['file_name'];

                        if (LOGIN_USER_TYPE == 'admin') {
                            $user_doc->status = $request->$document_status;
                        }else {
                            $user_doc->status ="0";
                        }
                        $user_doc->document_type = $request->$document_type;
                        $user_doc->expired_date = $request->$expired_date_key;
                        $user_doc->save();
                        
                        if($upload_result['file_name'] !=''){
                            $this->transferFile(env('ADMIN_PANEL_SUB_DOMAIN'), env('DRIVER_API_SUB_DOMAIN'), $upload_result['file_name'], $user->id);
                            //was off
                        }
                    }
                }
            }

            flashMessage('success', trans('messages.user.add_success'));
            return redirect(LOGIN_USER_TYPE . '/driver');
        }

        return redirect(LOGIN_USER_TYPE . '/driver');
    }

    /**
     * Update Driver Details
     *
     * @param array $request    Input values
     * @return redirect     to Driver View
     */
    public function update(Request $request)
    {
        if ($request->isMethod("GET")) {
            $data['result']  = $user  = User::find($request->id);

            //If login user is company then company can edit only that company's driver details
            if ($data['result']) {
                $data['address']            = DriverAddress::where('user_id', $request->id)->first();
                $data['country_code_option'] = Country::select('long_name', 'phone_code', 'id')->get();
                $data['company']            = Company::where('status', 'Active')->pluck('name', 'id');
                $data['path']               = url('images/users/' . $request->id);

                $data['driver_doc'] = CheckDocument('Driver', $user->country_code);
                $data['driver_documents'] = DriverDocuments::where('type', 'Driver')->where('vehicle_id', 0)->where('user_id', $user->id)->get();

                $data['card_slip_array'] = $this->card_slip_array;
                $data['nid_passport_array'] = $this->nid_passport_array;

                return view('admin.driver.edit', $data);
            } else {
                flashMessage('danger', 'Invalid ID');
                return redirect(LOGIN_USER_TYPE . '/driver');
            }
        }

        if ($request->submit) {

            // Edit Driver Validation Rules

            if ($request->password != null) {
                $rules = array(
                    'first_name'    => 'required',
                    'last_name'     => 'required',
                    //'email'         => 'required|email',
                    // 'status'        => 'required',
                    'password'      =>  ['required', 'string', 'min:8', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/',],
                    'country_code'  => 'required',
                    'gender'        => 'required',
                );
            } else {
                $rules = array(
                    'first_name'    => 'required',
                    'last_name'     => 'required',
                    //'email'         => 'required|email',
                    // 'status'        => 'required',
                    // 'password'      =>  ['required','string','min:8','regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/',],
                    'country_code'  => 'required',
                    'gender'        => 'required',
                );
            }
            //Bank details are updated only for company's drivers.
            // if ((LOGIN_USER_TYPE != 'company' && $request->company_name != 1) || (LOGIN_USER_TYPE == 'company' && Auth::guard('company')->user()->id != 1)) {
            //     $rules['account_holder_name'] = 'required';
            //     $rules['account_number'] = 'required';
            //     $rules['bank_name'] = 'required';
            //     $rules['bank_location'] = 'required';
            //     $rules['bank_code'] = 'required';
            // }

            if (LOGIN_USER_TYPE != 'company') {
                $rules['company_name'] = 'required';
                $rules['status'] = 'required';
            }
            if ($request->profile_picture != '') {
                $rules['profile_picture'] = 'required|mimes:jpg,jpeg,png,gif';
            }

            // Edit Driver Validation Custom Fields Name
            $attributes = array(
                'first_name'    => trans('messages.user.firstname'),
                'last_name'     => trans('messages.user.lastname'),
                //'email'         => trans('messages.user.email'),
                'status'        => trans('messages.driver_dashboard.status'),
                'mobile_number' => trans('messages.profile.phone'),
                'country_ode'   => trans('messages.user.country_code'),
                'gender'        => trans('messages.profile.gender'),
                'account_holder_name' => 'Account Holder Name',
                'account_number' => 'Account Number',
                'bank_name'     => 'Name of Bank',
                'bank_location' => 'Bank Location',
                'bank_code'     => 'BIC/SWIFT Code',
            );

            // Edit Rider Validation Custom Fields message
            $messages = array(
                'required'            => ':attribute is required.',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
                'password.regex' => 'Your password must be at least 8 characters With Alphabet And Number',
            );

            $user = User::find($request->id);
            if ($user->country_code != $request->country_code) {
            
                $driver_doc = CheckDocument('Driver', $request->country_code ?? 'all');
                if ($driver_doc->count() > 0) {
                    foreach ($driver_doc as $key => $value) {
                        // $rules['file_'.$value->id] = 'required|mimes:jpg,jpeg,png,gif';
                        // $attributes['file_'.$value->id] = $value->doc_name;

                        $rules[$value->doc_name] = 'required|mimes:jpg,jpeg,png,gif';
                        $rules[$value->doc_name . "_document_type"] = 'required';
                        if ($request->hasFile($value->doc_name . '_back')) {
                            $rules[$value->doc_name . '_back'] = 'mimes:jpg,jpeg,png,gif';
                        }

                        if ($value->expire_on_date == 'Yes') {
                            $rules['expired_date_' . $value->id] = 'required|date|date_format:Y-m-d';
                            $attributes['expired_date_' . $value->id] = 'Expired Date';
                        }
                    }
                }
            } else { 
              
                $driver_document = UserDocuments('Driver', $user, 0);
                $result = json_decode($driver_document, true);
                foreach ($result as $key => $value) {
                    $document_type=$value['doc_name']."_document_type";
                    $rules[$document_type] = 'required';
                    if ($value['document'] == '') {
                        $rules[$value['doc_name']] = 'required|mimes:jpg,jpeg,png,gif';
                        if ($request->hasFile($value['doc_name'] . '_back')) {
                            $rules[$value['doc_name'] . '_back'] = 'mimes:jpg,jpeg,png,gif';
                        }

                        // $rules[$value['doc_name']] = 'required|mimes:jpg,jpeg,png,gif';
                        // if ($request->hasFile($value['doc_name'] . '_back')) {
                        //     $rules[$value['doc_name'] . '_back'] = 'required|mimes:jpg,jpeg,png,gif';
                        // }
                    }
                    if ($value['expiry_required'] == '1') {
                        if ($value['expired_date'] == '' || $value['expired_date'] == '0000-00-00') {
                            $rules['expired_date_' . $value['id']] = 'required|date';
                            $attributes['expired_date_' . $value['id']] = 'Expired Date';
                        }
                    }
                }
            }

            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
            if ($request->mobile_number != "") {
                $validator->after(function ($validator) use ($request) {
                    $user = User::where('mobile_number', substr($request->mobile_number, -10))->where('user_type', $request->user_type)->where('country_id', $request->country_id)->where('id', '!=', $request->id)->count();
                    if ($user) {
                        $validator->errors()->add('mobile_number', trans('messages.user.mobile_no_exists'));
                    }
                });
            }

            $messages = array(
                'required'  => ':attribute is required.',
            );

            // $validator->after(function ($validator) use($request) {
            //     $user_email = User::where('email', $request->email)->where('user_type', $request->user_type)->where('id','!=', $request->id)->count();
            //     if($user_email) {
            //         $validator->errors()->add('email',trans('messages.user.email_exists'));
            //     }
            // });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = User::find($request->id);
            if ($request->status == "Active" && !$user->vehicle) {
                flashMessage('danger', 'Please ensure the driver has atleast one default vehicle, if not you can\'t activate.');
                return back();
            }

            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            if (LOGIN_USER_TYPE == 'admin') {
                $user->status       = $request->status;
            }else {
                $user->status       = "Pending";
            }
            $user->country_code = $request->country_code;
            $user->gender       = $request->gender;
            //$user->nid_number   = $request->nid_number;
            $user->passport_number    = $request->passport_number;
            $user->driving_licence_number    = $request->driving_licence_number;
            $user->unique_id = substr($request->mobile_number, -10) . "_" . strtolower($request->user_type);
            if ($request->mobile_number != "") {
                $user->mobile_number = substr($request->mobile_number, -10);
            }
            $user->user_type    = $request->user_type;
            if ($request->password != '') {
                $user->password = $request->password;
            }
            if (LOGIN_USER_TYPE == 'company') {
                $user->company_id = Auth::guard('company')->user()->id;
            } else {
                $user->company_id = $request->company_name;
            }
            $user->country_id = $request->country_id;
            $user->save();


            if ($request->profile_picture != '') {
                $user_profile_image = ProfilePicture::find($user->id);

                if (!$user_profile_image) {
                    $user_profile_image = new ProfilePicture;
                    $user_profile_image->user_id = $user->id;
                }

                $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
                $target_dir = '/images/users/' . $user->id;
                $target_path = asset($target_dir) . '/';

                if ($request->hasFile('profile_picture')) {
                    $image = $request->file('profile_picture');

                    $extension = $image->getClientOriginalExtension();
                    $file_name = "profile_pic_" . time() . "." . $extension;
                    $compress_size = array(
                        ["height" => 225, "width" => 225],
                        //["height" => 450, "width" => 250],
                    );
                    $options = compact('target_dir', 'file_name', 'compress_size');


                    $upload_result = $image_uploader->upload($image, $options);
                    if (!$upload_result['status']) {
                        return response()->json([
                            'status_code'       => "0",
                            'status_message'    => $upload_result['status_message'],
                        ]);
                    }else{
                        if($upload_result['file_name'] !=''){
                            $this->transferFile(env('ADMIN_PANEL_SUB_DOMAIN'), env('DRIVER_API_SUB_DOMAIN'), $upload_result['file_name'], $user->id);
                        }
                    }

                    $user_profile_image->photo_source = 'Local';
                    $user_profile_image->src = $target_path . $file_name;
                    $user_profile_image->save();
                }
            }

            Vehicle::where('user_id', $user->id)->update(['company_id' => $user->company_id]);

            $user_address = DriverAddress::where('user_id',  $user->id)->first();
            if ($user_address == '') {
                $user_address = new DriverAddress;
            }
            $user_address->user_id       = $user->id;
            $user_address->address_line1 = $request->address_line1;
            $user_address->address_line2 = $request->address_line2;
            $user_address->city          = $request->city;
            $user_address->state         = $request->state;
            $user_address->postal_code   = $request->postal_code;
            $user_address->save();

            // if ($user->company_id != null && $user->company_id != 1) {
            //     $payout_preference = PayoutPreference::firstOrNew(['user_id' => $user->id, 'payout_method' => "BankTransfer"]);
            //     $payout_preference->user_id = $user->id;
            //     $payout_preference->country = "IN";
            //     $payout_preference->account_number  = $request->account_number;
            //     $payout_preference->holder_name     = $request->account_holder_name;
            //     $payout_preference->holder_type     = "company";
            //     $payout_preference->paypal_email    = $request->account_number;
            //     $payout_preference->phone_number    = $request->mobile_number ?? '';
            //     $payout_preference->branch_code     = $request->bank_code ?? '';
            //     $payout_preference->bank_name       = $request->bank_name ?? '';
            //     $payout_preference->bank_location   = $request->bank_location ?? '';
            //     $payout_preference->payout_method   = "BankTransfer";
            //     $payout_preference->address_kanji   = json_encode([]);
            //     $payout_preference->save();

            //     $payout_credentials = PayoutCredentials::firstOrNew(['user_id' => $user->id, 'type' => "BankTransfer"]);
            //     $payout_credentials->user_id = $user->id;
            //     $payout_credentials->preference_id = $payout_preference->id;
            //     $payout_credentials->payout_id = $request->account_number;
            //     $payout_credentials->type = "BankTransfer";
            //     $payout_credentials->default = 'yes';
            //     $payout_credentials->save();
            // }

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
            $target_dir = '/images/users/' . $user->id;
            $target_path = asset($target_dir) . '/';
            $fileExtAllowed = array("jpg", "jpeg", "gif", "png");
            $r = ltrim($target_dir, '/') . '/';

            $driver_doc = CheckDocument('Driver', $request->country_code);
            if ($driver_doc) {
                foreach ($driver_doc as $key => $value) {
                    if ($request->hasFile($value->doc_name)) {
                        $time = time();
                        if ($request->hasFile($value->doc_name . '_back')) {
                            $document_name = $value->doc_name;
                            $document = $request->file($document_name);
                            $extension = $document->getClientOriginalExtension();

                            if (in_array(strtolower($extension), $fileExtAllowed)) {
                                $options = compact('target_dir');
                                $upload_result = $image_uploader->upload($document, $options);

                                $file_name1 = 'image-' . $time . '.' . $extension;
                                $replace_file_name1 = '1-' . $value->doc_name . $time . '.' . $extension;

                                rename($r . $file_name1, $r . $replace_file_name1);
                            }

                            $document_name2 = $value->doc_name . '_back';
                            $document2 = $request->file($document_name2);
                            $extension2 = $document2->getClientOriginalExtension();
                            if (in_array(strtolower($extension2), $fileExtAllowed)) {
                                $options2 = compact('target_dir');
                                $upload_result2 = $image_uploader->upload($document2, $options2);

                                $file_name2 = 'image-' . $time . '.' . $extension2;
                                $replace_file_name2 = '2-' . $value->doc_name . $time . '.' . $extension2;
                                rename($r . $file_name2, $r . $replace_file_name2);
                            }

                            $merged_file = $value->doc_name . $time . '.' . $extension2;

                            if (isset($replace_file_name1) and isset($replace_file_name2)) {
                                self::merge($r . $replace_file_name1, $r . $replace_file_name2, $r . $merged_file);

                                unlink($r . $replace_file_name1);
                                unlink($r . $replace_file_name2);
                                $upload_result['file_name'] = $merged_file;
                            } else {
                                flashMessage('danger', 'Image did not merged.');
                                return back();
                            }
                        } else {
                            $document_name = $value->doc_name;
                            $document = $request->file($document_name);
                            $extension = $document->getClientOriginalExtension();
                            $file_name = $document_name . "_" . time() . "." . $extension;
                            $options = compact('target_dir', 'file_name');
                            $upload_result = $image_uploader->upload($document, $options);
                        }

                        if (!$upload_result['status']) {
                            flashMessage('danger', $upload_result['status_message']);
                            return back();
                        }

                        $user_doc = DriverDocuments::where('type', 'Driver')->where('vehicle_id', 0)->where('user_id', $user->id)->where('document_id', $value->id)->first();

                        if ($user_doc == '') {
                            $user_doc = new DriverDocuments;
                        }
                        $document_status = $value->doc_name . "_status";
                        $document_type = $value->doc_name . "_document_type";

                        $user_doc->user_id = $user->id;
                        $user_doc->document_id = $value->id;
                        $user_doc->document = $target_path . $upload_result['file_name'];
                        if (LOGIN_USER_TYPE == 'admin') {
                            $user_doc->status = $request->$document_status;
                        }else {
                            $user_doc->status ="0";
                        }
                        $user_doc->document_type = $request->$document_type;
                        $user_doc->save();
                        
                        if($upload_result['file_name'] !=''){
                            $this->transferFile(env('ADMIN_PANEL_SUB_DOMAIN'), env('DRIVER_API_SUB_DOMAIN'), $upload_result['file_name'], $user->id);
                        }                     
                    }
                }

                $DeleteOldDocument = DriverDocuments::where('type', 'Driver')->where('vehicle_id', 0)->where('user_id', $user->id)->whereNotIn('document_id', $driver_doc->pluck('id')->toArray())->delete();

                foreach ($driver_doc as $key => $value) {
                    $document_status = $value->doc_name . "_status";
                    $document_type = $value->doc_name . "_document_type";

                    $user_doc = DriverDocuments::where('type', 'Driver')->where('vehicle_id', 0)->where('user_id', $user->id)->where('document_id', $value->id)->first();
                  //  $user_doc->status = $request->$document_status;

                    if (LOGIN_USER_TYPE == 'admin') {
                        $user_doc->status = $request->$document_status;
                    }else {
                        $user_doc->status ="0";
                    }


                    $expired_date_key = 'expired_date_' . $value->id;
                    $user_doc->expired_date = $request->$expired_date_key;
                    $user_doc->document_type = $request->$document_type;
                    $user_doc->save();
                }
            }

            if ($user->status == 'Active' AND ($user->active_time=="" OR $user->active_time=="0000-00-00 00:00:00")){
                //$this->bonus_helper->add_update_online_bonus($user->id, date('Y-m-d'));

                $updated_user = User::find($user->id);
				$updated_user->active_time = date('Y-m-d H:i:s');
				$updated_user->save();

                $this->bonus_helper->driver_joining_bonus($user);
                $this->bonus_helper->driver_referral_bonus_new($user);
                $this->bonus_helper->add_update_online_bonus($user->id, date('Y-m-d'));
            } 

            flashMessage('success', 'Updated Successfully');
        }
        return redirect(LOGIN_USER_TYPE . '/driver');
    }

    public function url_get_contents($Url)
    {
        if (!function_exists('curl_init')) {
            die('CURL is not installed!');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * Delete Driver
     *
     * @param array $request    Input values
     * @return redirect     to Driver View
     */
    public function delete(Request $request)
    {
        $result = $this->canDestroy($request->id);

        if ($result['status'] == 0) {
            flashMessage('error', $result['message']);
            return back();
        }
        $driver_owe_amount = DriverOweAmount::where('user_id', $request->id)->first();

        if (is_object($driver_owe_amount)) {
            if ($driver_owe_amount->amount == 0) {
                $driver_owe_amount->delete();
            }
        }
        try {
            User::find($request->id)->delete();
        } catch (\Exception $e) {
            $driver_owe_amount = DriverOweAmount::where('user_id', $request->id)->first();
            if ($driver_owe_amount == '') {
                DriverOweAmount::create([
                    'user_id' => $request->id,
                    'amount' => 0,
                    'currency_code' => 'USD',
                ]);
            }
            flashMessage('error', 'Driver have some trips, So can\'t delete this driver');
            // flashMessage('error',$e->getMessage());
            return back();
        }

        flashMessage('success', 'Deleted Successfully');
        return redirect(LOGIN_USER_TYPE . '/driver');
    }

    // Check Given User deletable or not
    public function canDestroy($user_id)
    {
        $return  = array('status' => '1', 'message' => '');

        //Company can delete only this company's drivers.
        if (LOGIN_USER_TYPE == 'company') {
            $user = User::find($user_id);
            if ($user->company_id != Auth::guard('company')->user()->id) {
                $return = ['status' => 0, 'message' => 'Invalid ID'];
                return $return;
            }
        }

        $driver_trips   = Trips::where('driver_id', $user_id)->count();
        $user_referral  = ReferralUser::where('user_id', $user_id)->orWhere('referral_id', $user_id)->count();

        if ($driver_trips) {
            $return = ['status' => 0, 'message' => 'Driver have some trips, So can\'t delete this driver'];
        } else if ($user_referral) {
            $return = ['status' => 0, 'message' => 'Rider have referrals, So can\'t delete this driver'];
        }
        return $return;
    }

    public function profile($user_id)
    {
        $driver = User::where('user_type', 'Driver')->find($user_id);
        $data['data'] = $driver;
        $data['profile_picture'] = DB::table('profile_picture')->where('user_id', $user_id)->pluck('src')->first();
        $data['driver_doc'] = DB::table('driver_documents')->where('user_id', $user_id)->where('type', 'Driver')->get();
        $data['vehicle_doc'] = DB::table('driver_documents')->where('user_id', $user_id)->where('type', 'Vehicle')->get();
        $data['address'] = DB::table('driver_address')->where('user_id', $user_id)->first();
        $data['country'] = country::where('id', $driver->country_id)->pluck('long_name')->first();

        return view('admin.driver.profile', $data);
    }

    public function change_partner($user_id, Request $request)
    {
        if ($request->isMethod("GET")) {        
            $driver = User::where('user_type', 'Driver')->find($user_id);
            if(is_object($driver)){
                $company = Company::find($driver->company_id);
                $vehicles = Vehicle::where('user_id', $user_id)->get();

                $owe_amount = DriverOweAmount::where('user_id', $user_id)->pluck('amount')->first();
                
                $payment_pending_trips = Trips::DriverPayoutTripsOnly()->where('driver_id', $user_id);
                $payout_amount = $payment_pending_trips->get()->sum('driver_payout');

                $bonus_due_amount = DriverBalance::where('user_id', $user_id)
                                            ->where('status', '!=', 'paid')
                                            ->sum('amount');                

                $data['data'] = $driver;
                $data['company'] = $company;
                $data['vehicles'] = $vehicles;
                $data['owe_amount'] = $owe_amount;
                $data['payout_amount'] = $payout_amount;
                $data['bonus_due_amount'] = $bonus_due_amount;

                return view('admin.driver.change_partner', $data);
            }
            else{
                flashMessage('error', 'Driver id is not correct');
                return back();
            }
        }
        else{            
            $user_id = $request->user_id;
            $company_id = $request->company_id;
            $confirm = $request->confirm;

            $rules = array(
                'company_id'    => 'required',
                'confirm'       => 'required'
            );                       
            
            $attributes['company_id']   = 'New Partner';
            $attributes['confirm']      = 'Confurmation';

            $messages = array(
                'required'            => ':attribute is required.'
            );
           

            $validator = Validator::make($request->all(), $rules, $messages, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }


            //change company of the driver
            $user = User::where('user_type', 'driver')->find($user_id);
            $user->company_id = $company_id;
            $user->partner_change_by = @Auth::guard('admin')->user()->id;
            $user->partner_change_at = now();
            $user->save();

            // set vecicle this driver to dummy company and dummy driver
            //Dummy company : company_id = 2, Dummy Driver : user_id = 1
            Vehicle::where('user_id', $user_id)
                    ->update(array(
                        'company_id' => '2', 
                        'user_id' => '1', 
                        'driver_change_by' => @Auth::guard('admin')->user()->id, 
                        'driver_change_at' => now()
                    )); 

            flashMessage('success', trans('messages.user.update_success'));
            return redirect(LOGIN_USER_TYPE . '/driver');

        }
    }

    
}
