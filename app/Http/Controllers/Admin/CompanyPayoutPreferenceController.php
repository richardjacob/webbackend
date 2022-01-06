<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\CompanyPayoutPreferenceDataTable;
use App\Models\CompanyPayoutPreference;
use App\Models\CompanyPayoutCredentials;
use App\Http\Start\Helpers;
use DB;
use Validator;
use Auth;
use Route;
use Hash;

class CompanyPayoutPreferenceController extends Controller
{
    public function __construct()
    {
        $this->permission_helper = resolve('App\Http\Helper\HubHelper');
        $this->payment_gateway = explode(',', DB::table('payment_gateway')->where('name','payout_methods')->pluck('value')->first());
        $this->account_type = array('Saving', 'Fixed', 'Salary');
        $this->holder_type = array('Individual', 'Company');
        $this->defaults = array('yes' => 'Yes', 'no' => 'No');
        $this->helper = new Helpers;
    }
    

    public function index(CompanyPayoutPreferenceDataTable $dataTable)
    {       
        return $dataTable->render('admin.company_payout_preference.view');
    }

    public function add(Request $request)
    { 
        $data['payment_gateway'] =  $this->payment_gateway;
        $data['account_type'] =  $this->account_type;
        $data['holder_type'] =  $this->holder_type;
        $data['defaults'] =  $this->defaults;
        
        if($request->isMethod('GET')) {
            return view('admin.company_payout_preference.add')->with($data);
        }

        if($request->submit) {  
            if($request->payout_method == 'banktransfer'){
                $this->validate($request,[
                    'payout_method'=> 'required',
                    'account_number'=> 'required',
                    'holder_name'=> 'required',
                    'account_type'=> 'required',
                    'holder_type'=> 'required',                    
                    'bank_name'=> 'required',
                    'branch_name'=> 'required',
                    'default'=> 'required',
                 ]);                
            }else{
                $this->validate($request,[
                    'payout_method'=> 'required',
                    'account_number_mb'=> 'required',
                    'default_mb'=> 'required',
                 ]);
            }

            $exits_record = DB::table('company_payout_preference')
                                ->where('company_id', Auth::guard('company')->user()->id)
                                ->where('payout_method', $request->payout_method)
                                ->count();  

            if($exits_record > 0)  {
                return back()->withInput()
                            ->withErrors(['exists_method' => $request->payout_method.' is alread exists.']);
            }else{                
                $country = DB::table('country')
                            ->where('id', Auth::guard('company')->user()->country_id)
                            ->pluck('long_name')
                            ->first();
                         
                if($request->payout_method == 'banktransfer'){                    
                    $exits_account_number = DB::table('company_payout_preference')
                                                ->where('company_id', '!=', Auth::guard('company')->user()->id)
                                                ->where('account_number', $request->account_number)
                                                ->count();  

                    if($exits_account_number > 0){
                        return back()->withInput()
                                    ->withErrors(['exists_method' => $request->account_number.' is alread used by another partner.']);
                    }

                    $company_payout_preference = new CompanyPayoutPreference();
                    $company_payout_preference->payout_method = $request->payout_method;
                    $company_payout_preference->account_number = $request->account_number;
                    $company_payout_preference->holder_name = $request->holder_name;
                    $company_payout_preference->account_type = $request->account_type;
                    $company_payout_preference->holder_type = $request->holder_type;
                    $company_payout_preference->bank_name = $request->bank_name;
                    $company_payout_preference->branch_name = $request->branch_name;
                    $company_payout_preference->routing_number = $request->routing_number;
                    $default = $request->default;
                }else{                    
                    $exits_account_number = DB::table('company_payout_preference')
                                                ->where('company_id', '!=', Auth::guard('company')->user()->id)
                                                ->where('account_number', $request->account_number_mb)
                                                ->count();  
                                                                     
                    if($exits_account_number > 0){
                        return back()->withInput()
                                    ->withErrors(['exists_method' => $request->account_number_mb.' is alread used by another partner.']);
                    }

                    $company_payout_preference = new CompanyPayoutPreference();
                    $company_payout_preference->payout_method = $request->payout_method;
                    $company_payout_preference->account_number = $request->account_number_mb;
                    $default = $request->default_mb;
                }
                                
                $company_payout_preference->currency_code = 'BDT';
                $company_payout_preference->company_id = Auth::guard('company')->user()->id;
                $company_payout_preference->country = $country;
                
                if($company_payout_preference->save()){
                    
                    $exits_company_payout_credentials = DB::table('company_payout_credentials')
                                                            ->where('company_id', Auth::guard('company')->user()->id)
                                                            ->count(); 
                    if($exits_company_payout_credentials > 0 AND  $default == 'yes'){
                            DB::table('company_payout_credentials')
                                ->where('company_id', Auth::guard('company')->user()->id)
                                ->update(['default' => 'no']);
                    }

                    if($exits_company_payout_credentials == 0)  $default = 'yes';

                    $company_payout_credential = new CompanyPayoutCredentials();
                    $company_payout_credential->company_id = Auth::guard('company')->user()->id;
                    $company_payout_credential->preference_id = $company_payout_preference->id;
                    $company_payout_credential->default =  $default;
                    $company_payout_credential->type = $request->payout_method;
                    if($request->payout_method == 'banktransfer'){
                        $company_payout_credential->payout_id = $request->account_number;
                    }else{
                        $company_payout_credential->payout_id = $request->account_number_mb;
                    }

                    $company_payout_credential->save();

                    $this->helper->flash_message('success', 'Added Successfully'); 
                    return redirect('company/payout_preference');
                    
                }

            }    flashMessage('success', 'Added Successfully');
        }
        
        return redirect('company/payout_preference');
    }

    public function view(Request $request)
    {
        $data = DB::table('company_payout_preference')
                         ->select(
                             'company_payout_preference.id as id',
                             'company_payout_preference.payout_method as payout_method', 
                             'company_payout_preference.account_number as account_number',
                             'company_payout_preference.currency_code as currency_code',
                             'company_payout_preference.routing_number as routing_number',
                             'company_payout_preference.holder_name as holder_name',
                             'company_payout_preference.holder_type as holder_type',
                             'company_payout_preference.account_type as account_type',
                             'company_payout_preference.bank_name as bank_name',
                             'company_payout_preference.branch_name as branch_name',
                             DB::raw('DATE_FORMAT(company_payout_preference.created_at, "%d-%b-%Y<br>%h:%i %p") as created_at'),
                             'company_payout_credentials.default as default',
                         )
                        ->leftJoin('company_payout_credentials', function($join) {
                            $join->on('company_payout_preference.id', '=', 'company_payout_credentials.preference_id');
                        })                      
                        ->where('company_payout_preference.company_id',  Auth::guard('company')->user()->id)
                        ->where('company_payout_preference.id',  $request->id)
                        ->first();

        return view('admin.company_payout_preference.single_view')->with('data', $data);
    }

    public function update(Request $request)
    {
        $data['payment_gateway'] =  $this->payment_gateway;
        $data['account_type'] =  $this->account_type;
        $data['holder_type'] =  $this->holder_type;
        $data['defaults'] =  $this->defaults;

        if($request->isMethod("GET")) {
            $data['result'] = DB::table('company_payout_preference')
                         ->select(
                             'company_payout_preference.id as id',
                             'company_payout_preference.payout_method as payout_method', 
                             'company_payout_preference.account_number as account_number',
                             'company_payout_preference.currency_code as currency_code',
                             'company_payout_preference.routing_number as routing_number',
                             'company_payout_preference.holder_name as holder_name',
                             'company_payout_preference.holder_type as holder_type',
                             'company_payout_preference.account_type as account_type',
                             'company_payout_preference.bank_name as bank_name',
                             'company_payout_preference.branch_name as branch_name',
                             DB::raw('DATE_FORMAT(company_payout_preference.created_at, "%d-%b-%Y<br>%h:%i %p") as created_at'),
                             'company_payout_credentials.default as default',
                         )
                        ->leftJoin('company_payout_credentials', function($join) {
                            $join->on('company_payout_preference.id', '=', 'company_payout_credentials.preference_id');
                        })                      
                        ->where('company_payout_preference.company_id',  Auth::guard('company')->user()->id)
                        ->where('company_payout_preference.id',  $request->id)
                        ->first();

            return view('admin.company_payout_preference.edit')->with($data);
        }
        else if($request->submit) {
            if($request->payout_method == 'banktransfer'){
                $this->validate($request,[
                    'payout_method'=> 'required',
                    'account_number'=> 'required',
                    'holder_name'=> 'required',
                    'account_type'=> 'required',
                    'holder_type'=> 'required',                    
                    'bank_name'=> 'required',
                    'branch_name'=> 'required',
                    'default'=> 'required',
                 ]);                
            }else{
                $this->validate($request,[
                    'payout_method'=> 'required',
                    'account_number_mb'=> 'required',
                 ]);
            }
           
            $exits_record = DB::table('company_payout_preference')
                                ->where('company_id', Auth::guard('company')->user()->id)
                                ->where('payout_method', $request->payout_method)
                                ->where('id', '!=', $request->id)
                                ->count();  

            if($exits_record > 0)  {
                return back()->withInput()
                        ->withErrors(['exists_method' => $request->payout_method.' is alread exists.']);
            }else{
                if($request->payout_method == 'banktransfer'){
                    $exits_account_number = DB::table('company_payout_preference')
                                                ->where('company_id', '!=', Auth::guard('company')->user()->id)
                                                ->where('account_number', $request->account_number)
                                                ->count();  

                    if($exits_account_number > 0){
                        return back()->withInput()
                                    ->withErrors(['exists_method' => $request->account_number.' is alread used by another partner.']);
                    }

                    $company_payout_preference = CompanyPayoutPreference::where('id', $request->id)
                                                                        ->where('company_id', Auth::guard('company')->user()->id)
                                                                        ->first();

                    $company_payout_preference->payout_method = $request->payout_method;
                    $company_payout_preference->account_number = $request->account_number;
                    $company_payout_preference->holder_name = $request->holder_name;
                    $company_payout_preference->account_type = $request->account_type;
                    $company_payout_preference->holder_type = $request->holder_type;
                    $company_payout_preference->bank_name = $request->bank_name;
                    $company_payout_preference->branch_name = $request->branch_name;
                    $company_payout_preference->routing_number = $request->routing_number;
                    $payout_id = $request->account_number;
                    $default = $request->default;
                }else{
                    $exits_account_number = DB::table('company_payout_preference')
                                                ->where('company_id', '!=', Auth::guard('company')->user()->id)
                                                ->where('account_number', $request->account_number_mb)
                                                ->count();  
                                                                     
                    if($exits_account_number > 0){
                        return back()->withInput()
                                    ->withErrors(['exists_method' => $request->account_number_mb.' is alread used by another partner.']);
                    }
                    
                    $company_payout_preference = CompanyPayoutPreference::where('id', $request->id)
                                                                        ->where('company_id', Auth::guard('company')->user()->id)
                                                                        ->first();
                    $company_payout_preference->payout_method = $request->payout_method;
                    $company_payout_preference->account_number = $request->account_number_mb;
                    $payout_id = $request->account_number_mb;
                    $default = $request->default_mb;
                }

                if($company_payout_preference->save()){
                    if($default == 'yes'){
                            DB::table('company_payout_credentials')
                                ->where('company_id', Auth::guard('company')->user()->id)
                                ->update(['default' => 'no']);
                    }                    
                   
                    $company_payout_credential = CompanyPayoutCredentials::where('preference_id', $request->id)
                                                                            ->where('company_id', Auth::guard('company')->user()->id)
                                                                            ->first();
                    $company_payout_credential->default = $default;
                    $company_payout_credential->type = $request->payout_method;
                    $company_payout_credential->payout_id = $payout_id;
                    if($company_payout_credential->save()){
                        flashMessage('success', 'Updated Successfully');
                    }
                }
            }            
        }
        return redirect('company/payout_preference');
    }
    public function delete(Request $request)
    {       
        
        $payout_preference = CompanyPayoutPreference::where('id', $request->id)
                                                ->where('company_id', Auth::guard('company')->user()->id)
                                                ->first();

        if(is_object($payout_preference)){
            $payout_credential = CompanyPayoutCredentials::where('preference_id', $request->id)
                                                ->where('company_id', Auth::guard('company')->user()->id)
                                                ->first();
            if(is_object($payout_credential)) $payout_credential->delete();

            if($payout_preference->delete()){
                flashMessage('success', 'Deleted Successfully');
                return redirect('company/payout_preference');
            }
        }                         

        flashMessage('danger', 'Information did not deleted');
        return redirect('company/payout_preference');
    }

    
}
