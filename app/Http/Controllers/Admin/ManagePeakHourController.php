<?php

/**
 * Manage Fare Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Manage Fare
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\ManagePeakHourDataTable;
use App\Models\PeakHour;

use App\Http\Start\Helpers;
use App\Models\ManageFare;
use App\Models\PeakFareDetail;
use App\Models\Currency;
use App\Models\Request as RideRequest;
use App\Models\CarType;
use Validator;

class ManagePeakHourController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Class $dataTable instance of ManageFareDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(ManagePeakHourDataTable $dataTable)
    {
        return $dataTable->render('admin.manage_peak_hour.view');
    }

    

    /**
     * Update Peak Based Details
     *
     * @param array $request  Input values
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if($request->isMethod('GET')) {
            $data['result'] = PeakHour::find($request->id);
            $data['times'] = array(
                '00:00:00' => '12:00 AM',
                '01:00:00' => '1:00 AM',
                '02:00:00' => '2:00 AM',
                '03:00:00' => '3:00 AM',
                '04:00:00' => '4:00 AM',
                '05:00:00' => '5:00 AM',
                '06:00:00' => '6:00 AM',
                '07:00:00' => '7:00 AM',
                '08:00:00' => '8:00 AM',
                '09:00:00' => '9:00 AM',
                '10:00:00' => '10:00 AM',
                '11:00:00' => '11:00 AM',
                '12:00:00' => '12:00 PM',
                '13:00:00' => '1:00 PM',
                '14:00:00' => '2:00 PM',
                '15:00:00' => '3:00 PM',
                '16:00:00' => '4:00 PM',
                '17:00:00' => '5:00 PM',
                '18:00:00' => '6:00 PM',
                '19:00:00' => '7:00 PM',
                '20:00:00' => '8:00 PM',
                '21:00:00' => '9:00 PM',
                '22:00:00' => '10:00 PM',
                '23:00:00' => '11:00 PM'
            );
            $data['status'] = array(
                '1' => 'Active',
                '0' => 'Inactive'
            );
            
            if(!$data['result']) {
                flashMessage('danger', 'Invalid ID');
                return redirect('admin/manage_peak_hour');
            }

            return view('admin.manage_peak_hour.edit', $data);
        }

        if($request->status == '1') {
            $rules = array(
                'start_time' => 'required',
                'end_time'   => 'required',
                'status'     => 'required',
            );
        }else{
            $rules = array(
                'status'    => 'required',
            );
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $peak_hour = PeakHour::find($request->id);       

        $peak_hour->start_time   = $request->start_time;
        $peak_hour->end_time   = $request->end_time; 
        $peak_hour->status   = $request->status;        
        $peak_hour->save();       

        flashMessage('success', 'Peak Hour updated Successfully');

        return redirect('admin/manage_peak_hour');
    }


}
