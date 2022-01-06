<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\CompanyTransactionHistoryPaidToAleshaDataTable;
use App\DataTables\CompanyTransactionHistoryBalanceWithdrawDataTable;
use App\DataTables\CompanyTransactionHistoryPayoutDataTable;
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

class CompanyTransactionHistory extends Controller
{
    public function paid_to_alesha(CompanyTransactionHistoryPaidToAleshaDataTable $dataTable, Request $r)
    {
        $driver_id = $r->driver_id;
        $start_date = $r->start_date;
        $end_date = $r->end_date;
        if(LOGIN_USER_TYPE == 'admin') $company_id = $r->company_id;
        else if(LOGIN_USER_TYPE == 'company') $company_id =auth()->guard('company')->user()->id;

        $data['driver_id'] = $driver_id;
        $data['company_id'] = $company_id;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        
        $array = array(
            'driver_id' => $driver_id,
            'company_id' => $company_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        );
        
        return $dataTable->with($array)->render('admin.company.transaction_history.paid_to_alesha', $data);
    }

    public function balance_withdraw(CompanyTransactionHistoryBalanceWithdrawDataTable $dataTable, Request $r)
    {
        $driver_id = $r->driver_id;
        $start_date = $r->start_date;
        $end_date = $r->end_date;
        if(LOGIN_USER_TYPE == 'admin') $company_id = $r->company_id;
        else if(LOGIN_USER_TYPE == 'company') $company_id =auth()->guard('company')->user()->id;

        $data['driver_id'] = $driver_id;
        $data['company_id'] = $company_id;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        
        $array = array(
            'driver_id' => $driver_id,
            'company_id' => $company_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        );
        

        return $dataTable->with($array)->render('admin.company.transaction_history.balance_withdraw', $data);
    }

    public function payout(CompanyTransactionHistoryPayoutDataTable $dataTable, Request $r)
    {
        $driver_id = $r->driver_id;
        $start_date = $r->start_date;
        $end_date = $r->end_date;
        if(LOGIN_USER_TYPE == 'admin') $company_id = $r->company_id;
        else if(LOGIN_USER_TYPE == 'company') $company_id =auth()->guard('company')->user()->id;

        $data['driver_id'] = $driver_id;
        $data['company_id'] = $company_id;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        
        $array = array(
            'driver_id' => $driver_id,
            'company_id' => $company_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        );
        

        return $dataTable->with($array)->render('admin.company.transaction_history.payout', $data);
    }

}
