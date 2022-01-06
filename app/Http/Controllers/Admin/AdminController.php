<?php

/**
 * Admin Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Admin
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\AdminusersDataTable;
use Auth;
use DB;
use App\Models\Admin;
use App\Models\User;
use App\Models\Request as RideRequest;
use App\Models\Trips;
use App\Models\Country;
use App\Models\Role;
use App\Models\Currency;
use App\Models\Company;
use App\Http\Start\Helpers;
use App\Models\HubEmployee;
use Validator;
use Session;

class AdminController extends Controller
{
    /**
     * Load Index View for Dashboard
     *
     * @return view index
     */
    public function index(Request $request)
    {
        $data['users_count'] = User::count();
        $graph_type = $request->graph_type ? : 'Weekly';

        //if login user is company then only get company user
        $data['total_driver'] = User::where('user_type','Driver')
        ->where(function($query)  {
            if(LOGIN_USER_TYPE=='company') { 
                $query->where('company_id',Auth::guard('company')->user()->id);
            }
        })
        ->count();
        $data['total_rider'] = User::where('user_type','Rider')->count();

        //if login user is company then only get company drivers
        $data['today_driver_count'] = User::whereDate('created_at', '=', date('Y-m-d'))
        ->where(function($query)  {
            if(LOGIN_USER_TYPE=='company') { 
                $query->where('company_id',Auth::guard('company')->user()->id);
            }
        })
        ->where('user_type','Driver')
        ->count();
        $data['today_rider_count'] = User::whereDate('created_at', '=', date('Y-m-d'))->where('user_type','Rider')->count();

        if(LOGIN_USER_TYPE=='company') {  //if login user is company then revenue calculated from company trips
            $data['today_revenue'] = Trips::whereDate('created_at', '=', date('Y-m-d'))
                ->where('status','Completed')
                ->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                })
                ->get();
            $data['today_revenue'] = $data['today_revenue']->sum('driver_or_company_earning');
        } else {
            $data['today_revenue'] =  Trips::whereDate('created_at', '=', date('Y-m-d'))
            ->where('status','Completed')->get();
            $data['today_revenue'] = $data['today_revenue']->sum('commission');
        }

        //if login user is company then get only company driver's trip
        $data['today_trips'] = Trips::whereDate('created_at', '=', date('Y-m-d'))
        ->where(function($query)  {
            if(LOGIN_USER_TYPE=='company') {    
                $query->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                });
            }
        })
        ->count();

        //if login user is company then get only company driver's trip
        $data['total_trips'] = Trips::
        where(function($query)  {
            if(LOGIN_USER_TYPE=='company') {  
                $query->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                });
            }
        })
        ->count();

        //if login user is company then get only company driver's trip
        $data['total_success_trips'] = Trips::where('status','Completed')
        ->where(function($query)  {
            if(LOGIN_USER_TYPE=='company') {  
                $query->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                });
            }
        })
        ->count();

        if(LOGIN_USER_TYPE=='company') {   //if login user is company then revenue is sum of trip amount
            $data['total_revenue'] = Trips::where('status','Completed')
                ->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                })->get();
            $data['total_revenue'] = $data['total_revenue']->sum('driver_or_company_earning');
        }else{  //if login user is admin then revenue is sum of admin commission
            $data['total_revenue'] = Trips::where('status','Completed')->get();
            $data['total_revenue'] = $data['total_revenue']->sum('commission');
        }

        if(LOGIN_USER_TYPE=='company') {
            $data['admin_paid_amount'] = Trips::where('status','Completed')
                ->where('driver_payout','>',0)
                ->where('payment_mode','<>','Cash')
                ->whereHas('driver',function($q){
                    $q->where('company_id',Auth::guard('company')->user()->id);
                })
                ->whereHas('driver_payment',function($q1){
                    $q1->where('admin_payout_status','Paid');
                })->get();

            $data['admin_paid_amount'] = $data['admin_paid_amount']->sum('driver_payout');

            $data['admin_pending_amount'] = Trips::where('status','Completed')
                ->where('driver_payout','>',0)
                ->where('payment_mode','<>','Cash')
                ->whereHas('driver',function($q){
                    $q->where('company_id',Auth::guard('company')->user()->id);
                })
                ->whereHas('driver_payment',function($q1){
                    $q1->where('admin_payout_status','Pending');
                })->get();

            $data['admin_pending_amount'] = $data['admin_pending_amount']->sum('driver_payout');
        }

        $default_currency = Currency::active()->defaultCurrency()->first();
        if (LOGIN_USER_TYPE=='company' && session()->get('currency') != null) {  //if login user is company then get session currency
            $default_currency = Currency::whereCode(session()->get('currency'))->first();
        }
        $data['currency_code'] = $default_currency->symbol;

        $data['recent_trips'] = RideRequest::
        with(['trips','users','car_type','request'])
        ->where(function($query)  {
            if(LOGIN_USER_TYPE=='company') { //if login user is company then get only company driver's trip
                $query->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                });
            }
        })
        ->groupBy('group_id')
        ->orderBy('group_id','desc')
        ->limit(10)->get();


        //Sales Graph
        //if login user is company then total earning is sum of trip amount .
        //If login user is admin then total revenue is sum of admin commission

        $last_year = date('Y',strtotime(date('Y').' -1 year'));
        $last_year_month_date = date('Y-m-d',strtotime(date('Y-m-d').' -1 year'));
        $curr_month =date('m');
        $chart_array_quarter = [];
        $select_option_array = [];
        $chart_array_half_year = [];

        // Common Query
        $chart = Trips::where('status', 'Completed')
                        ->where(function($query)  {
                        if(LOGIN_USER_TYPE=='company') {  
                            $query->whereHas('driver',function($q1){
                                $q1->where('company_id',Auth::guard('company')->user()->id);
                            });
                        }
                 });


        //Monthly Query
        if($graph_type == 'Monthly'){
            $monthly_start_date = $last_year_month_date;
            for($i=1; $i<=12; $i++){
                $monthly_start_date = date('Y-m-01',strtotime($monthly_start_date.' +1 month'));
                $monthly_end_date = date("Y-m-t 23:59:59", strtotime($monthly_start_date));
                
                ${'month'.$i.'_chart'} = clone($chart);
                ${'monthly_amount'}[$i] = floatval(${'month'.$i.'_chart'}->where('created_at', '>=', $monthly_start_date)
                                                                          ->where('created_at', '<=', $monthly_end_date)
                                                                          ->get()
                                                                          ->sum(LOGIN_USER_TYPE=='company'?'driver_or_company_earning':'commission'));
                
                $year = date('y', strtotime($monthly_start_date));
                $month = date('F', strtotime($monthly_start_date));            

                $array['y'] = $month.', '.$year;
                $array['amount'] = number_format(${'monthly_amount'}[$i],2,'.','');
                $chart_array_monthly[] = $array;
            }
            $data['line_chart_data'] = json_encode($chart_array_monthly);
        }    
        
        //Quarter Query        
        if($graph_type == 'Quarterly'){
            $quarter1_chart=clone($chart);
            $quarter2_chart=clone($chart);
            $quarter3_chart=clone($chart);
            $quarter4_chart=clone($chart);

            if($curr_month == 1 OR $curr_month == 2 OR $curr_month == 3 ){
                $quarter1 = ['04', '05', '06', $last_year];
                $quarter2 = ['07', '08', '09', $last_year];
                $quarter3 = ['10', '11', '12', $last_year];
                $quarter4 = ['01', '02', '03', date('Y')];
            }
            else if($curr_month == 4 OR $curr_month == 5 OR $curr_month == 6 ){
                $quarter1 = ['07', '08', '09', $last_year];
                $quarter2 = ['10', '11', '12', $last_year];
                $quarter3 = ['01', '02', '03', date('Y')];
                $quarter4 = ['04', '05', '06', date('Y')];
            }
            else if($curr_month == 7 OR $curr_month == 8 OR $curr_month == 9 ){
                $quarter1 = ['10', '11', '12', $last_year];
                $quarter2 = ['01', '02', '03', date('Y')];
                $quarter3 = ['04', '05', '06', date('Y')];
                $quarter4 = ['07', '08', '09', date('Y')];
            }
            else if($curr_month == 10 OR $curr_month == 11 OR $curr_month == 12 ){
                $quarter1 = ['01', '02', '03', date('Y')];
                $quarter2 = ['04', '05', '06', date('Y')];
                $quarter3 = ['07', '08', '09', date('Y')];
                $quarter4 = ['10', '11', '12', date('Y')];
            }

            for($i=1; $i<=4; $i++){
                $quarter = ${'quarter'.$i};
                $quarter_start_date = $quarter[3].'-'.$quarter[0].'-01';
                $quarter_end_date = date("Y-m-t", strtotime($quarter[3].'-'.$quarter[2].'-01')).' 23:59:59';

                $quarter_chart = ${'quarter'.$i.'_chart'};           

                $quarter_amount[$i]= floatval($quarter_chart->where('created_at', '>=', $quarter_start_date)
                                                           ->where('created_at', '<=', $quarter_end_date)
                                                           ->get()
                                                           ->sum(LOGIN_USER_TYPE=='company'?'driver_or_company_earning':'commission'));  

                $year = $quarter[3];
                $month = date('F', strtotime(date('Y-'.$quarter[2].'-01')));

                $array['y'] = $month.', '.$year;
                $array['amount'] = number_format($quarter_amount[$i],2,'.','');
                $chart_array_quarter[] = $array;              
            }             
            $data['line_chart_data'] = json_encode($chart_array_quarter);
        }

        //Half Yearly
        if($graph_type == 'Half Yearly'){
            $half_yearly1_chart=clone($chart);
            $half_yearly2_chart=clone($chart);
            $half_yearly3_chart=clone($chart);
            $half_yearly4_chart=clone($chart);
            $half_yearly5_chart=clone($chart);
            $half_yearly6_chart=clone($chart);

            $half_yearly1_start_date = date('Y-m-01',strtotime(' -36 month'));
            $half_yearly2_start_date = date('Y-m-01',strtotime(' -30 month'));
            $half_yearly3_start_date = date('Y-m-01',strtotime(' -24 month'));
            $half_yearly4_start_date = date('Y-m-01',strtotime(' -18 month'));
            $half_yearly5_start_date = date('Y-m-01',strtotime(' -12 month'));
            $half_yearly6_start_date = date('Y-m-01',strtotime(' -6 month'));

            $half_yearly1_end_date = date('Y-m-t 23:59:59',strtotime($half_yearly1_start_date.' 5 month'));
            $half_yearly2_end_date = date('Y-m-t 23:59:59',strtotime($half_yearly2_start_date.' 5 month'));
            $half_yearly3_end_date = date('Y-m-t 23:59:59',strtotime($half_yearly3_start_date.' 5 month'));
            $half_yearly4_end_date = date('Y-m-t 23:59:59',strtotime($half_yearly4_start_date.' 5 month'));
            $half_yearly5_end_date = date('Y-m-t 23:59:59',strtotime($half_yearly5_start_date.' 5 month'));
            $half_yearly6_end_date = date('Y-m-t 23:59:59',strtotime($half_yearly6_start_date.' 5 month'));        

            for($i=1; $i<=6; $i++){
                $half_yearly_chart = ${'half_yearly'.$i.'_chart'};    
                $half_yearly_start_date = ${'half_yearly'.$i.'_start_date'}; 
                $half_yearly_end_date = ${'half_yearly'.$i.'_end_date'};     



                $half_yearly_amount[$i]= floatval($half_yearly_chart->where('created_at', '>=', $half_yearly_start_date)
                                                                ->where('created_at', '<=', $half_yearly_end_date)
                                                                ->get()
                                                                ->sum(LOGIN_USER_TYPE=='company'?'driver_or_company_earning':'commission'));  
               
                $array['y'] = date("M y", strtotime($half_yearly_start_date)).' - '.date("M y", strtotime($half_yearly_end_date));
                $array['amount'] = number_format($half_yearly_amount[$i],2,'.','');
                $chart_array_half_year[] = $array;              
            } 
            $data['line_chart_data'] = json_encode($chart_array_half_year);
        }

        //Yearly
        if($graph_type == 'Yearly'){
            $yearly1_chart=clone($chart);
            $yearly2_chart=clone($chart);
            $yearly3_chart=clone($chart);
            $yearly4_chart=clone($chart);
            $yearly5_chart=clone($chart);
            $yearly6_chart=clone($chart);
            $yearly7_chart=clone($chart);

            $yearly1_start_date = date('Y-01-01',strtotime(' -6 year'));
            $yearly2_start_date = date('Y-01-01',strtotime(' -5 year'));
            $yearly3_start_date = date('Y-01-01',strtotime(' -4 year'));
            $yearly4_start_date = date('Y-01-01',strtotime(' -3 year'));
            $yearly5_start_date = date('Y-01-01',strtotime(' -2 year'));
            $yearly6_start_date = date('Y-01-01',strtotime(' -1 year'));
            $yearly7_start_date = date('Y-01-01');

            $yearly1_end_date = date('Y-m-t 23:59:59',strtotime($yearly1_start_date.' 11 month'));
            $yearly2_end_date = date('Y-m-t 23:59:59',strtotime($yearly2_start_date.' 11 month'));
            $yearly3_end_date = date('Y-m-t 23:59:59',strtotime($yearly3_start_date.' 11 month'));
            $yearly4_end_date = date('Y-m-t 23:59:59',strtotime($yearly4_start_date.' 11 month'));
            $yearly5_end_date = date('Y-m-t 23:59:59',strtotime($yearly5_start_date.' 11 month'));
            $yearly6_end_date = date('Y-m-t 23:59:59',strtotime($yearly6_start_date.' 11 month'));
            $yearly7_end_date = date('Y-m-d H:i:s');
            

            for($i=1; $i<=7; $i++){
                $yearly_chart = ${'yearly'.$i.'_chart'};    
                $yearly_start_date = ${'yearly'.$i.'_start_date'}; 
                $yearly_end_date = ${'yearly'.$i.'_end_date'};     



                $yearly_amount[$i]= floatval($yearly_chart->where('created_at', '>=', $yearly_start_date)
                                                                ->where('created_at', '<=', $yearly_end_date)
                                                                ->get()
                                                                ->sum(LOGIN_USER_TYPE=='company'?'driver_or_company_earning':'commission'));  
               
                $array['y'] = date("Y", strtotime($yearly_start_date));
                $array['amount'] = number_format($yearly_amount[$i],2,'.','');
                $chart_array_year[] = $array;              
            }
            $data['line_chart_data'] = json_encode($chart_array_year);
        }

        //weekly
        if($graph_type == 'Weekly'){
            $weekly1_chart=clone($chart);
            $weekly2_chart=clone($chart);
            $weekly3_chart=clone($chart);
            $weekly4_chart=clone($chart);
            $weekly5_chart=clone($chart);

            $weekly1_start_date = date('Y-m-d',strtotime(' -30 day'));
            $weekly2_start_date = date('Y-m-d',strtotime(' -23 day'));
            $weekly3_start_date = date('Y-m-d',strtotime(' -16 day'));
            $weekly4_start_date = date('Y-m-d',strtotime(' -9 day'));
            $weekly5_start_date = date('Y-m-d',strtotime(' -2 day'));

            $weekly1_end_date = date('Y-m-d 23:59:59',strtotime($weekly1_start_date.' 6 day'));
            $weekly2_end_date = date('Y-m-d 23:59:59',strtotime($weekly2_start_date.' 6 day'));
            $weekly3_end_date = date('Y-m-d 23:59:59',strtotime($weekly3_start_date.' 6 day'));
            $weekly4_end_date = date('Y-m-d 23:59:59',strtotime($weekly4_start_date.' 6 day'));
            $weekly5_end_date = date('Y-m-d 23:59:59');        

            for($i=1; $i<=5; $i++){
                $weekly_chart = ${'weekly'.$i.'_chart'};    
                $weekly_start_date = ${'weekly'.$i.'_start_date'}; 
                $weekly_end_date = ${'weekly'.$i.'_end_date'};     

                $weekly_amount[$i]= floatval($weekly_chart->where('created_at', '>=', $weekly_start_date)
                                                                ->where('created_at', '<=', $weekly_end_date)
                                                                ->get()
                                                                ->sum(LOGIN_USER_TYPE=='company'?'driver_or_company_earning':'commission'));  
               
                $array['y'] = date("d F", strtotime($weekly_start_date)).' - '.date("d F", strtotime($weekly_end_date));
                $array['amount'] = number_format($weekly_amount[$i],2,'.','');
                $chart_array_weekly[] = $array;              
            }
            $data['line_chart_data'] = json_encode($chart_array_weekly);
        }

        $data['select_option_array'] = array('Weekly','Monthly','Quarterly','Half Yearly','Yearly');
        $data['graph_type'] = $graph_type;

        return view('admin.index', $data);
    }

    public function session_set_from_another_domain(Request $request){
        echo $request->username_from_main_domain;
       session([
            'username_from_main_domain' => $request->username_from_main_domain,
            'password_from_main_domain' => $request->password_from_main_domain,
        ]);
        echo session('username_from_main_domain');
        if(session('username_from_main_domain') !='') echo "1";
    }
    




    /**
     * Load Datatable for Admin Users
     *
     * @param array $dataTable  Instance of AdminuserDataTable
     * @return datatable
     */
    public function view(AdminusersDataTable $dataTable)
    {
        return $dataTable->render('admin.admin_users.view');
    }

    /**
     * Load Login View
     *
     * @return view login
     */
    public function login()
    {
        return view('admin.login');
    }

    /**
     * Add Admin User Details
     *
     * @param array $request    Input values
     * @return redirect     to Admin Users View
     */
    public function add(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['roles'] = Role::all()->pluck('name','id'); //->where('name', '!=', 'admin')
            $data['countries'] = Country::codeSelect();

            return view('admin.admin_users.add', $data);  
        }

        if($request->submit) {
            // Add Admin User Validation Rules
            $rules = array(
                'username'      => 'required|unique:admins',
                'email'         => 'required|email|unique:admins',
                'password'      => 'required',
                'role'          => 'required',
                'status'        => 'required',
                'country_code'  => 'required',
                'mobile_number' => 'required|numeric',
            );

            // Add Admin User Validation Custom Names
            $attributes = array(
                'username'      => 'Username',
                'email'         => 'Email',
                'password'      => 'Password',
                'role'          => 'Role',
                'status'        => 'Status',
                'country_code'  => 'Country Code',
                'mobile_number' => 'Mobile Number',
            );

            $validator = Validator::make($request->all(), $rules, [], $attributes);
            $validator->setAttributeNames($attributes); 

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }

            $admin = new Admin;
            $admin->username = $request->username;
            $admin->email    = $request->email;
            $admin->password = $request->password;
            $admin->status   = $request->status;
            $admin->country_code = $request->country_code;
            $admin->mobile_number   = $request->mobile_number;
            $admin->save();

            $admin->attachRole($request->role); 
           
            flashMessage('success', 'Added Successfully'); 
        }

        return redirect('admin/admin_user');
    }

    /**
     * Update Admin User Details
     *
     * @param array $request    Input values
     * @return redirect     to Admin Users View
     */
    public function update(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['result']  = Admin::find($request->id);
            $data['roles'] = Role::all()->pluck('name','id');
            $data['countries'] = Country::codeSelect();
            if($data['result']) {
                return view('admin.admin_users.edit', $data);    
            }
            flashMessage('danger', 'Invalid ID');
            return redirect('admin/admin_user');
        }
        if($request->submit) {
            // Edit Admin User Validation Rules
            $rules = array(
                'username'   => 'required|unique:admins,username,'.$request->id,
                'email'      => 'required|email|unique:admins,email,'.$request->id,
                'country_code'     => 'required',
                'mobile_number'     => 'required|numeric',
                'role'       => 'required',
                'status'     => 'required'
            );

            // Edit Admin User Validation Custom Fields Name
            $attributes = array(
                'username'   => 'Username',
                'email'      => 'Email',
                'country_code' => 'Country Code',
                'mobile_number' => 'Mobile Number',
                'role'       => 'Role',
                'status'     => 'Status'
            );

            $validator = Validator::make($request->all(), $rules, [], $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $admins = Admin::active()->count();
            if($admins==1 && $request->status=='Inactive') {
                flashMessage('danger', 'You can\'t inactive the last one. Atleast one should be available.');
                return back();
            }

            $admin = Admin::find($request->id);

            $admin->username = $request->username;
            $admin->email    = $request->email;
            $admin->country_code = $request->country_code;
            $admin->mobile_number = $request->mobile_number;
            $admin->status   = $request->status;
            
            if($request->filled("password")) {
                $admin->password = $request->password;
            }
            $admin->save();

            $role_id = Role::role_user($request->id)->role_id;

            if($role_id!=$request->role) {
                $admin->detachRole($role_id);
                $admin->attachRole($request->role);
            }
        
            flashMessage('success', 'Updated Successfully');

            // Redirect to dashboard when current user not have a permission to view admin users
            if(!Auth::guard('admin')->user()->can('manage_admin')) {
                return redirect('admin/dashboard');
            }

        }
        return redirect('admin/admin_user');
    }

    /**
     * Login Authentication
     *
     * @param array $request Input values
     * @return redirect     to dashboard
     */

    public function authenticate_no_csrf(Request $request){
        return self::authenticate($request);
    }

    public function authenticate(Request $request)
    { 
        if($request->getmethod() == 'GET') {
            return redirect()->route('admin_login');
        }

        if ($request->user_type == 'Company') {
            $login_column = is_numeric($request->username)?'mobile_number':'email';

            $company = Company::where($login_column, $request->username)->first();
            

            if(isset($company) && $company->status == 'Pending') { 
                flashMessage('danger', 'You account still pending contact with Alesha Ride customer care for activated');
                request()->flashExcept('password');
                return redirect('admin/login')->withInput(request()->except('password'));
            }else {

                if (isset($company) && $company->status != "Inactive") {
                
                    $guard = Auth::guard('company')->attempt([$login_column => $request->username, 'password' => $request->password]);
                    if ($guard) {
                        $domain = '//' .env('ADMIN_PANEL_SUB_DOMAIN') . '.' . env('DOMAIN')."/company/dashboard";
                        // return redirect('company/dashboard');
                        return redirect()->to($domain);
                    }
                    flashMessage('danger', 'Log In Failed. Please Check Your Email(or)Mobile/Password');
                    request()->flashExcept('password');
                    return redirect('admin/login')->withInput(request()->except('password'));
                }
    
                else if(isset($company) && $company->status == 'Inactive') { 
                    flashMessage('danger', 'Log In Failed. You are Blocked by Admin.');
                    request()->flashExcept('password');
                    return redirect('admin/login')->withInput(request()->except('password'));
                }
                else  {                
                    flashMessage('danger', 'Log In Failed. Please Check Your Email(or)Mobile/Password');
                    request()->flashExcept('password');
                    return redirect('admin/login')->withInput(request()->except('password'));
                }
            }
            
           

        }
            // =============Hub start============
        else if ($request->user_type == 'Hub') {
            $login_column = is_numeric($request->username)?'mobile_number':'email';

            $hub_employee = HubEmployee::where($login_column, $request->username)->first();
            if (isset($hub_employee) && $hub_employee->status != "Inactive") {
                
                $guard = Auth::guard('hub')->attempt([$login_column => $request->username, 'password' => $request->password]);
                if ($guard) {
                    return redirect('hub/dashboard');
                }
                flashMessage('danger', 'Log In Failed. Please Check Your Email(or)Mobile/Password');
                request()->flashExcept('password');
                return redirect('admin/login')->withInput(request()->except('password'));
            }

            else if(isset($hub_employee) && $hub_employee->status == 'Inactive') { 
                flashMessage('danger', 'Log In Failed. You are Blocked by Admin.');
                request()->flashExcept('password');
                return redirect('admin/login')->withInput(request()->except('password'));
            }


        }
            // ==============Hub End===========





        else{
            $admin = Admin::where('username', $request->username)->first();

            if(isset($admin) && $admin->status != 'Inactive') {
                if(Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password])) {
                    Session::put('admin_password', $request->password);
                    return redirect()->intended('admin/dashboard');
                }

                flashMessage('danger', 'Log In Failed. Please Check Your Username/Password');
                request()->flashExcept('password');
                return redirect('admin/login')->withInput(request()->except('password'));
            }
            else if(isset($admin) && $admin->status == 'Inactive') { 
                flashMessage('danger', 'Log In Failed. You are Blocked by Admin.');
                request()->flashExcept('password');
                return redirect('admin/login')->withInput(request()->except('password'));
            }
            
            else  {                
                flashMessage('danger', 'Log In Failed. Please Check Your Username/Password');
                request()->flashExcept('password');
                return redirect('admin/login')->withInput(request()->except('password'));
            }
        }

        // flashMessage('danger', 'Log In Failed. You are Blocked by Admin.');
        flashMessage('danger', 'Log In Failed. Please Check Your Username/Password');
        request()->flashExcept('password');
        return redirect('admin/login')->withInput(request()->except('password'));
    }

    /**
     * Admin Logout
     */
    public function logout()
    {
        if (LOGIN_USER_TYPE == 'admin'){
            Auth::guard('admin')->logout();
            return redirect('admin/login');
        }else if (LOGIN_USER_TYPE == 'company'){
            Auth::guard('company')->logout();
           $domains = '//' . env('DOMAIN');
            return redirect()->to($domains);
        }
       
        
    }


    public function delete(Request $request)
    {
        $admins = Admin::active()->count();
        if($admins==1) {
            flashMessage('danger', 'You can\'t delete the last one. Atleast one should be available.');
            return back();
        }

        $admin = Admin::where('id',$request->id)->first();
        if($admin) {
            $roles_user = DB::table('role_user')->where('user_id',$request->id)->delete();
            $admin = $admin->delete();
            flashMessage('success', 'Deleted Successfully');
        } else {
            flashMessage('danger', 'You can\'t able to delete');
        }
        return redirect('admin/admin_user');
    }
}
