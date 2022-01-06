<?php

/**
 * Rider Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Rider
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\RiderDataTable;
use App\Models\User;
use App\Models\Trips;
use App\Models\Wallet;
use App\Models\UsersPromoCode;
use App\Models\Country;
use App\Models\ProfilePicture;
use App\Models\PaymentMethod;
use App\Models\RiderLocation;
use App\Models\ApiCredentials;
use App\Models\ReferralUser;
use App\Models\RiderGroup;
use App\Http\Start\Helpers;
use Validator;
use App\Models\Hub;
use DB;

class RiderController extends Controller
{
    /**
     * Load Datatable for Rider
     *
     * @param array $dataTable  Instance of RiderDataTable
     * @return datatable
     */
    public function __construct()
    {
        $this->bonus_helper = resolve('App\Http\Helper\BonusHelper');
    }

    public function index(RiderDataTable $dataTable)
    {
        return $dataTable->render('admin.rider.view');
    }

    /**
     * Add a New Rider
     *
     * @param array $request  Input values
     * @return redirect     to Rider view
     */
    public function add(Request $request)
    {
        if ($request->isMethod('GET')) {
            $data['country_code_option'] = Country::select('long_name', 'phone_code', 'id')->get();
            $data['google_api'] = ApiCredentials::where('id', '');
            return view('admin.rider.add', $data);
        }
        if ($request->submit) {
            $rules = array(
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required|email',
                'mobile_number' => 'required|numeric|regex:/\+?(88)?0?1[356789][0-9]{8}\b/',
                'password'      =>  ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',],
                // 'country_code'  => 'required',
                'gender'        => 'required',
                'user_type'     => 'required',
                'status'        => 'required',
                'is_our_employee' => 'required',
            );

            // Add Rider Validation Custom Names
            $attributes = array(
                'first_name' => trans('messages.user.firstname'),
                'last_name' => trans('messages.user.lastname'),
                'email' => trans('messages.user.email'),
                'password' => trans('messages.user.paswrd'),
                'country_code' => trans('messages.user.country_code'),
                'gender' => trans('messages.profile.gender'),
                'user_type' => trans('messages.user.user_type'),
                'mobile_number' => trans('messages.user.mobile'),
                'status' => trans('messages.driver_dashboard.status'),
                'is_our_employee' => trans('messages.driver_dashboard.is_our_employee'),
            );

            // Edit Rider Validation Custom Fields message
            $messages = array(
                'required' => ':attribute ' . trans('messages.home.field_is_required') . '',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );

            $validator = Validator::make($request->all(), $rules, $messages, $attributes);
            $validator->after(function ($validator) use ($request) {
                $user = User::where('mobile_number', substr($request->mobile_number, -10))->where('user_type', $request->user_type)->where('country_id', $request->country_id)->count();

                $user_email = User::where('email', $request->email)
                    ->where('user_type', $request->user_type)
                    ->where('email', '!=', '')
                    ->count();

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

            $user = new User;
            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            $user->is_email_valid = $request->is_email_valid;
            $user->country_code = "880";
            $user->country_id   = $request->country_id;
            $user->mobile_number = substr($request->mobile_number, -10);
            $user->unique_id     = substr($request->mobile_number, -10) . "_" . strtolower($request->user_type);
            $user->gender       = $request->gender;
            $user->password     = $request->password;
            $user->user_type    = $request->user_type;
            $user->status       = $request->status;
            $user->is_our_employee       = $request->is_our_employee;
            $user->save();
            $this->bonus_helper->rider_cashback1($user);

            $user_pic = new ProfilePicture;
            $user_pic->user_id      = $user->id;
            $user_pic->src          = "";
            $user_pic->photo_source = 'Local';
            $user_pic->save();

            $location = new RiderLocation;
            $location->user_id          = $user->id;
            $location->home             = $request->home_location ? $request->home_location : '';
            $location->work             = $request->work_location ? $request->work_location : '';
            $location->home_latitude    = $request->home_latitude ? $request->home_latitude : '';
            $location->home_longitude   = $request->home_longitude ? $request->home_longitude : '';
            $location->work_latitude    = $request->work_latitude ? $request->work_latitude : '';
            $location->work_longitude   = $request->work_longitude ? $request->work_longitude : '';
            $location->save();

            flashMessage('success', 'Added Successfully');
        }
        return redirect('admin/rider');
    }

    /**
     * Update Rider Details
     *
     * @param array $request    Input values
     * @return redirect     to Rider View
     */
    public function update(Request $request)
    {
        if ($request->isMethod("GET")) {
            $data['result'] = User::find($request->id);
            if ($data['result']) {
                $data['country_code_option'] = Country::select('long_name', 'phone_code', 'id')->get();
                $data['location'] = RiderLocation::where('user_id', $request->id)->first();
                return view('admin.rider.edit', $data);
            }
            flashMessage('danger', 'Invalid ID');
            return redirect('admin/rider');
        }
        if ($request->submit) {
            if ($request->password != null) {
                $rules = array(
                    'first_name'    => 'required',
                    'last_name'     => 'required',
                    'email'         => 'required|email',
                    'mobile_number' => 'required|numeric|regex:/\+?(88)?0?1[356789][0-9]{8}\b/',
                    // 'country_code'  => 'required',
                    'gender'        => 'required',
                    'password'      =>  ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/',],
                    'status'        => 'required',
                    'is_our_employee' => 'required',
                );
            } else {
                $rules = array(
                    'first_name'    => 'required',
                    'last_name'     => 'required',
                    'email'         => 'required|email',
                    'mobile_number' => 'required|numeric|regex:/\+?(88)?0?1[356789][0-9]{8}\b/',
                    // 'country_code'  => 'required',
                    'gender'        => 'required',
                    // 'password'      =>  ['required','string','min:8','regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/',],
                    'status'        => 'required',
                    'is_our_employee' => 'required',
                );
            }
            // Edit Rider Validation Custom Fields message
            $messages = array(
                'required' => ':attribute ' . trans('messages.home.field_is_required') . '',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );
            // Edit Rider Validation Custom Fields Name
            $attributes = array(
                'first_name' => trans('messages.user.firstname'),
                'last_name' => trans('messages.user.lastname'),
                'email' => trans('messages.user.email'),
                'password' => trans('messages.user.paswrd'),
                'country_code' => trans('messages.user.country_code'),
                'gender' => trans('messages.profile.gender'),
                'mobile_number' => trans('messages.user.mobile'),
                'status' => trans('messages.driver_dashboard.status'),
                'is_our_employee' => trans('messages.driver_dashboard.is_our_employee'),
            );

            $validator = Validator::make($request->all(), $rules, $messages, $attributes);

            if ($request->mobile_number != "") {
                $validator->after(function ($validator) use ($request) {
                    $user = User::where('mobile_number', substr($request->mobile_number, -10))->where('user_type', $request->user_type)->where('country_id', $request->country_id)->where('id', '!=', $request->id)->count();

                    if ($user) {
                        $validator->errors()->add('mobile_number', trans('messages.user.mobile_no_exists'));
                    }
                });
            }

            $validator->after(function ($validator) use ($request) {
                $user_email = User::where('email', $request->email)
                    ->where('email', '!=', '')
                    ->where('user_type', $request->user_type)
                    ->where('id', '!=', $request->id)->count();
                if ($user_email) {
                    $validator->errors()->add('email', trans('messages.user.email_exists'));
                }
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = User::find($request->id);

            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            $user->is_email_valid        = $request->is_email_valid;
            $user->country_code = "880";
            $user->gender       = $request->gender;
            if ($request->mobile_number != "") {
                $user->mobile_number = substr($request->mobile_number, -10);
            }

            $user->user_type    = $request->user_type;
            if ($request->password != '') {
                $user->password = $request->password;
            }
            $user->unique_id     = substr($request->mobile_number, -10) . "_" . strtolower($request->user_type);
            $user->country_id = $request->country_id;
            $user->status       = $request->status;
            $user->is_our_employee  = $request->is_our_employee;
            $user->save();

            $location = RiderLocation::where('user_id', $request->id)->first();
            if ($location == '') {
                $location   = new RiderLocation;
            }
            $location->user_id          =   $request->id;
            $location->home             =   $request->home_location ? $request->home_location : '';
            $location->work             =   $request->work_location ? $request->work_location : '';
            $location->home_latitude    =   $request->home_latitude ? $request->home_latitude : '';
            $location->home_longitude   =   $request->home_longitude ? $request->home_longitude : '';
            $location->work_latitude    =   $request->work_latitude ? $request->work_latitude : '';
            $location->work_longitude   =   $request->work_longitude ? $request->work_longitude : '';
            $location->save();

            flashMessage('success', trans('messages.user.update_success'));
        }

        return redirect('admin/rider');
    }

    /**
     * Delete Rider
     *
     * @param array $request    Input values
     * @return redirect     to Rider View
     */
    public function delete(Request $request)
    {
        $result = $this->canDestroy($request->id);

        if ($result['status'] == 0) {
            flashMessage('error', $result['message']);
            return back();
        }
        try {
            PaymentMethod::where('user_id', $request->id)->delete();
            User::find($request->id)->delete();
        } catch (\Exception $e) {
            flashMessage('error', 'Rider have wallet or promo or trips, So can\'t delete this rider.');
            return back();
        }

        flashMessage('success', 'Deleted Successfully');
        return redirect('admin/rider');
    }

    // Check Given User deletable or not
    public function canDestroy($user_id)
    {
        $return  = array('status' => '1', 'message' => '');

        $user_promo = UsersPromoCode::where('user_id', $user_id)->count();
        $user_wallet = Wallet::where('user_id', $user_id)->count();
        $user_trips = Trips::where('user_id', $user_id)->count();
        $user_referral = ReferralUser::where('user_id', $user_id)->orWhere('referral_id', $user_id)->count();

        if ($user_promo || $user_wallet || $user_trips) {
            $return = ['status' => 0, 'message' => 'Rider have wallet or promo or trips, So can\'t delete this rider'];
        } else if ($user_referral) {
            $return = ['status' => 0, 'message' => 'Rider have referrals, So can\'t delete this rider'];
        }
        return $return;
    }

    
    
   
}
