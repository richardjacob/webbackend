<?php

/**
 * manual Booking Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    manual Booking
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use App\Models\ProfilePicture;
use App\Models\DriverLocation;
use App\Models\Location;
use App\Models\ManageFare;
use App\Models\CarType;
use App\Models\ScheduleRide;
use App\Models\PeakFareDetail;
use App\Models\Trips;
use App\Models\Currency;
use App\Models\FilterObject;
use App\Models\Request as RideRequest;
use App\Http\Helper\RequestHelper;
use Validator;
use DB;
use Auth;

class PaymentHelperTest extends Controller
{
	public function __construct(RequestHelper $request)
    {
        $this->request_helper = $request;
    }
	
    public function fare_estimation(Request $request)//Request $request
    {
    	$request->location_id = 1;
    	$request->vehicle_type = "4";    
    	// mobile_number	
    	// pickup_latitude
    	// pickup_longitude
    	// drop_latitude
    	// drop_longitude
    	// date_time

    	$rider = User::where('mobile_number',$request->mobile_number)->where('user_type','Rider')->first();
    	
        try {
            $timezone = $this->request_helper->getTimeZone($request->pickup_latitude, $request->pickup_longitude);

            logger('location timezone : '.$timezone);

            $polyline = @$this->request_helper->GetPolyline($request->pickup_latitude, $request->drop_latitude, $request->pickup_longitude, $request->drop_longitude);

            LogDistanceMatrix("manual booking store");
            $get_fare_estimation = $this->request_helper->GetDrivingDistance($request->pickup_latitude, $request->drop_latitude, $request->pickup_longitude, $request->drop_longitude);
        } catch(\Exception $e) {
            flashMessage('danger', 'Invalid Request');
            return redirect(LOGIN_USER_TYPE.'/later_booking');
        }
        
        $travel_minutes = round(floor(round($get_fare_estimation['time'] / 60)));
        $total_distance = round(floor($get_fare_estimation['distance'] / 1000) . '.' . floor($get_fare_estimation['distance'] % 1000));

        $end_time = strtotime("+".$travel_minutes." minutes", strtotime($request->date_time));

        
        $fare_details = ManageFare::where('location_id',$request->location_id)->where('vehicle_id',$request->vehicle_type)->first();

        $base_fare = round($fare_details->base_fare + $fare_details->per_km * $total_distance);
        echo $fare_estimation = numberFormat($base_fare + round($fare_details->per_min * $travel_minutes));

        
        
    }
}