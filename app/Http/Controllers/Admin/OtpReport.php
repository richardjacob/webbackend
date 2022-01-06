<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Otp;

class OtpReport extends Controller
{   
    public function index(Request $request)
    {
        if(!$_POST)
        {
            return view('admin.driver.otp');
        }
        else if($request->submit)
        {
            $mobile_number = $request->mobile_number;
            if($mobile_number !=''){
                $data = Otp::where('mobile_number', $mobile_number)
                            ->orderBy('id', 'DESC')
                            ->first();
                if(!is_object($data))  {
                    $err = "OTP not found by ".$mobile_number;
                    return view('admin.driver.otp')->with('err', $err);
                }
                return view('admin.driver.otp')->with('data', $data)->with('mobile_number', $mobile_number);                            
            }else{
                $err = "Mobile Number field is required.";
                return view('admin.driver.otp')->with('err', $err);
            }

            return view('admin.driver.otp')->with('data', $data);
        }
    }
} 
