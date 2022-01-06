<?php

/**
 * Trip Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Trip
 * @author      Trioangle Product Team
 * @version     2.2.1
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PushNotificationService;
use App\Models\ComplainCategory;
use App\Models\ComplainSubCategory;
use App\Models\Complain;
use App\Models\Trips;
use App\Models\Vehicle;
use App\Models\ComplainMovement;
use App\Models\User;
use App\Models\PcrMsg;
use App\Models\DriverLocation;
use App\Models\RiderLocation;
use DB;
use JWTAuth;
use Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CustomLog;

class ComplainController extends Controller
{
	/**
	 * @var PushNotificationService
	 */
	private $pushNotificationService;

	public function __construct(PushNotificationService $pushNotificationService)
	{
		DB::enableQueryLog();
		$this->request_helper = resolve('App\Http\Helper\RequestHelper');
	}


	
	/**
	 * Send Field name with category and sub category
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function ComplainFieldsWithCategory(Request $request) 
	{
        Log::info("fields_with_category Stp:1 :", $request->all());
        $user_details = JWTAuth::parseToken()->authenticate();
        $language = $user_details->language;

        if($language == 'bn'){
            $category = 'category_bn';
            $sub_category = 'sub_category_bn';
            $field_category = 'শ্রেণী';
            $field_sub_category = 'উপ শ্রেণী';
            $field_sub_complain = 'অভিযোগ';
        } else {
            $category = 'category';
            $sub_category = 'sub_category';
            $field_category = 'Category';
            $field_sub_category = 'Sub Category';
            $field_sub_complain = 'Complain';
        }

        $cats = ComplainCategory::where('status', '1')->select('id', $category)->get();

        $array_cat = array();
        $array_sub = array();
        foreach($cats as $cat){
            $sub_cats = ComplainSubCategory::where('complain_cat_id', $cat->id)
                                            ->select('id', $sub_category)
                                            ->where('status', '1')->get();
            $array_sub = array();
            foreach($sub_cats as $sub_cat){
                $array_sub[] = array(
                                'sub_category_id' => $sub_cat->id,
                                'sub_category_en' => $sub_cat-> $sub_category
                            );
            }

            $array_cat[] = array(
                            'category_id' => $cat->id,
                            'category_en' => $cat->$category,
                            'sub_category' => $array_sub
                        );

        }

        $array_fields[] = array(
                        'key' => 'category_id',
                        'value' => $field_category
        );

        $array_fields[] = array(
            'key' => 'sub_category_id',
            'value' => $field_sub_category
        );

        $array_fields[] = array(
            'key' => 'complain',
            'value' => $field_sub_complain
        );

        $data = array(
                    'status_code'     => '1',
                    'status_message'  => 'Success', 
                    'category' => $array_cat,
                    'fields' => $array_fields
                );

        return response()->json($data);
	}

    /**
	 * Save complains in db
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function addComplain(Request $request) 
	{
        Log::info("addComplain Stp:1 :", $request->all());
        $user_details = JWTAuth::parseToken()->authenticate();

        $rules = array( 
			'trip_id' => 'required|exists:trips,id',
			'category_id' => 'required|exists:complain_categories,id',
			'sub_category_id' => 'required|exists:complain_sub_categories,id',
			'complain' => 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
				'status_code' => '0',
				'status_message' => $validator->messages()->first()
			]);
		}

        $trip = Trips::where('id', $request->trip_id)->first();
        $vehicle = Vehicle::where('user_id', $trip->driver_id)->first();
        $complain_location = NULL;


		$trip_file = public_path() . '/trip_file/'.$trip->id . '_file.json';
        if(file_exists($trip_file)){
            $trip_content = json_decode(file_get_contents($trip_file)); 
            if(is_array($trip_content) OR is_object($trip_content)){
                $last_location = $trip_content[count($trip_content)-1];
                $complain_location = $this->request_helper->GetLocation($last_location->latitude, $last_location->longitude); 
                if($complain_location == '') {
                    $complain_location = '{"latitude": "'.$last_location->latitude.'", "longitude": "'.$last_location->longitude.'"}';
                } 
            }
        }

        $table = new Complain();
        $table->cat_id = $request->category_id;
        $table->sub_cat_id = $request->sub_category_id;
        $table->complain_content = $request->complain;
        $table->trip_id = $request->trip_id;

        $table->complain_by = $user_details->user_type;
        $table->rider_id = $trip->user_id;
        $table->driver_id = $trip->driver_id;
        $table->vehicle_id = $vehicle->id;
        $table->vehicle_number = $vehicle->vehicle_number;
        $table->pickup_location = $trip->pickup_location;
        $table->drop_location = $trip->drop_location;
        $table->status = '0';
        if($trip->status != 'Completed'){
            $table->complain_location = $complain_location;
        }

        if($table->save()){
            return response()->json([
				'status_code' => '1',
				'status_message' => 'Success'
			]);
        } else {
            return response()->json([
				'status_code' => '0',
				'status_message' => __('messages.api.not_save')
			]);
        }
	}

    public function complainHistory(Request $request) 
	{
        $result = DB::table('complains')
                        ->select(
                            'complains.id as id',
                            'complains.trip_id as trip_id',
                            'complains.pickup_location as pickup_location',
                            'complains.drop_location as drop_location',
                            'complains.complain_content as complain_content',
                            'complains.complain_by as complain_by',
                            'complains.vehicle_number as vehicle_number',
                            
                            'complains.vehicle_id as vehicle_id',
                            'complains.rider_id as rider_id',
                            'complains.driver_id as driver_id',
                            
                            'complain_categories.category as category',
                            'complain_sub_categories.sub_category as sub_category',
                            DB::raw('CONCAT(users.first_name, \' \', users.last_name) AS rider'),
                            DB::raw("(
                                CASE
                                WHEN complains.status='0' THEN 'Pending'
                                WHEN complains.status='1' THEN 'Completed'
                                WHEN complains.status='2' THEN 'Processing'
                                ELSE '' 
                                END
                            ) AS status"),
                            DB::raw('DATE_FORMAT(complains.created_at, "%d-%b-%Y, %h:%i %p") as created_at')
                        )
                        ->leftJoin('complain_categories', function($join) {
                            $join->on('complains.cat_id', '=', 'complain_categories.id');
                        })
                        ->leftJoin('complain_sub_categories', function($join) {
                            $join->on('complains.sub_cat_id', '=', 'complain_sub_categories.id');
                        })
                        ->leftJoin('users', function($join) {
                            $join->on('complains.rider_id', '=', 'users.id');
                        })
                        ->where('complains.id', $request->complain_id)
                        ->first();

        if(is_object($result)){
            $driver = DB::table('users')
                        ->select(DB::raw('CONCAT(first_name, \' \', last_name) AS driver_name'), 'driving_licence_number')
                        ->where('id', $result->driver_id)
                        ->first();
            $tracDetails = ComplainMovement::select('remarks', 'created_at')
                                                ->where('complain_id', $request->complain_id)
                                                ->orderBy('id', 'DESC')
                                                ->get(); 

            $complain_details[] = array('key' => 'Complain', 'value' => $result->complain_content);
            $complain_details[] = array('key' => 'Complain Number', 'value' => $result->id);
            $complain_details[] = array('key' => 'Complain Tim', 'value' => date(" h:i a, d M Y", strtotime($result->created_at)));
            $complain_details[] = array('key' => 'Category', 'value' => $result->category);
            $complain_details[] = array('key' => 'Sub Category', 'value' => $result->sub_category);
            //$complain_details[] = array('key' => 'Complain by', 'value' => $result->complain_by);
            $complain_details[] = array('key' => 'Trip ID', 'value' => $result->trip_id);
            $complain_details[] = array('key' => 'Rider', 'value' => $result->rider);
            $complain_details[] = array('key' => 'Driver Name', 'value' => $driver->driver_name);
            $complain_details[] = array('key' => 'Driving Licence No.', 'value' => $driver->driving_licence_number);
            $complain_details[] = array('key' => 'Pickup Location', 'value' => $result->pickup_location);
            $complain_details[] = array('key' => 'Drop Location', 'value' => $result->drop_location);
            $complain_details[] = array('key' => 'Vehicle Number', 'value' => $result->vehicle_number);
            $complain_details[] = array('key' => 'Status', 'value' => $result->status);

            $trackingInfo = array();
            foreach($tracDetails as $key => $track){
                $trackingInfo[] = array(
                    'sl#'        => $key+1,
                    //'process_by' => $track->process_by,
                    //'process'    => $track->process,
                    'remarks'    => $track->remarks,
                    //'status'     => $track->status,
                    'date'       => date("d-m-Y", strtotime($track->created_at))
                );
            }

            $array = array(
                'status_code'      => '1',
                'status_message'   => 'Success',
                'complain_details' => $complain_details,
                'tracking'         => $trackingInfo
            );

            return response()->json($array);

        } else{
            return response()->json([
				'status_code' => '0',
				'status_message' => __('messages.api.invalid_info')
			]);

            
        }
       
    }

    public function messagePCR(Request $request) 
	{
        $trip_id = $request->trip_id;
        $msg = $request->msg;
        $user_details = JWTAuth::parseToken()->authenticate();
        $language = $user_details->language;
        $user_type = $user_details->user_type;

        if($trip_id !=''){
            $trip = Trips::find($trip_id);
            $driver = User::find($trip->driver_id);
            $rider = User::find($trip->user_id);

            $complain_location = "";
            $trip_file = public_path() . '/trip_file/'.$trip->id . '_file.json';
            if(file_exists($trip_file)){
                $trip_content = json_decode(file_get_contents($trip_file));        
                $last_location = $trip_content[count($trip_content)-1];
                //$complain_location = $this->request_helper->GetLocation($last_location->latitude, $last_location->longitude); 
                //if($complain_location == '') {
                    $complain_location = '{"latitude": "'.$last_location->latitude.'", "longitude": "'.$last_location->longitude.'"}';
                //}             
            }


            if($user_type == "Rider"){
                $msg = "Iam using Alesharide ride sharing service. I am facing problem. My name is ";
                $msg.= $rider->first_name." ".$rider->last_name.", ";
                $msg.= "Driver name is ".$driver->first_name." ".$driver->last_name.", Licence No. ".$driver->driving_licence_number." ";
                if($complain_location !='') $msg.= "my location is ".$complain_location." "; 
                $msg.= "Please help me.";
            } 
            elseif ($user_type == "Driver"){
                $msg = "Iam using Alesharide ride sharing service. I am facing problem. My name is ";
                $msg.= $driver->first_name." ".$driver->last_name.", ";
                $msg.= "Licence No. ".$driver->driving_licence_number.", ";
                $msg.= "Rider name is ".$rider->first_name." ".$rider->last_name.", ";
                if($complain_location !='') $msg.= "my location is ".$complain_location." "; 
                $msg.= "Please help me.";
            }

            $table = new PcrMsg();
            $table->rider_id = $rider->id;
            $table->driver_id = $driver->id;
            $table->msg = $msg;
            $table->location = $complain_location;
            $table->save();
        }
        elseif($msg !=''){
            $table = new PcrMsg();

            if($user_type == 'Driver') {
                $table->driver_id = $user_details->id;

                $driver_location = DriverLocation::where('user_id', $driver->id)->orderBy('id', 'DESC')->first();
                if(is_object($driver_location)){
                    $table->location = '{"latitude": "'.$driver_location->latitude.'", "longitude": "'.$driver_location->longitude.'"}';
                }                
            }
            else if($user_type == 'Rider') {
                $table->rider_id = $user_details->id;

                $rider_location = RiderLocation::where('user_id', $user_details->id)->orderBy('id', 'DESC')->first();
                if(is_object($rider_location)){
                    $table->location = '{"latitude": "'.$rider_location->latitude.'", "longitude": "'.$rider_location->longitude.'"}';
                }
            }

            $table->msg = $msg;
            $table->save();

        }
        // $url_999 = "&".$msg;
        // file_get_contents($url_999);

        return response()->json([
            'status_code' => '1',
            'status_message' => $msg
        ]);
    }   

}
