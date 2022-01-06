<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Trips;
use App\Models\User;
use Mail;

class Invoice extends Controller
{
    public function index($rider_id_base64, $trip_id_base64)
    {
        if ($rider_id_base64 != '' and $trip_id_base64 != '') {
            $rider_id = base64_decode($rider_id_base64);
            $trip_id = base64_decode($trip_id_base64);

            $trip = $trip = Trips::where('user_id', $rider_id)->where('id', $trip_id)->first();

            if ($trip) {
                $driver = User::where('id', $trip->driver_id)->first();
                $rider = User::where('id', $rider_id)->first();

                return view('invoice')
                    ->with('trip', $trip)
                    ->with('driver', $driver)
                    ->with('rider', $rider)
                    ->with('email', '0');
            } else {
                echo "Information is not correct.";
            }
        }
    }

    public static function invoice_email($rider_id, $trip_id)
    {
        if ($rider_id != '' and $trip_id != '') {

            $rider_id = base64_decode($rider_id);
            $trip_id = base64_decode($trip_id);
            $trip = $trip = Trips::where('user_id', $rider_id)->where('id', $trip_id)->first();

            if ($trip) {
                $driver = User::where('id', $trip->driver_id)->first();
                $rider = User::where('id', $rider_id)->first();

                if ($driver == true and  $rider == true) {
                    if ($rider->email != '' and $rider->is_email_valid == '1') {
                        $data = array(
                            'trip' => $trip,
                            'driver' => $driver,
                            'rider' => $rider,
                            'email' => '1'
                        );
                        Mail::send('invoice', $data, function ($message) use ($rider) {
                            $message->to($rider->email, $rider->first_name . ' ' . $rider->last_name)->subject('Trip Invoice');
                            $message->from('noreply@alesharide.com', 'ALESHA RIDE');
                        });
                    }
                }
            }
        }
    }

    public function verify_user($user_id_base64)
    {
        $user_id = base64_decode($user_id_base64);

        $user = User::find($user_id);
        $output = "
                <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Account Verification</title>
                    </head>
                    <body>
                    <center>
                      <a href=\"https://alesharide.com\" style=\"text-decoration:none;\">
                        <img src=\"https://alesharide.com/images/logos/logo.png\" style=\"width:200px;\">
                      </a>
                    <br><br><br> <h3>                   
            ";

        if ($user != '') {
            $user->is_email_valid = '1';
            if ($user->save()) $output .= "Your email verified.";
            else  $output .= "Sorry! your information is not correct.";
        } else $output .= "Sorry! your information is not correct.";

        $output .= "
                </h3>
                <a href='https://alesharide.com'>Go to Home Page</a>
            </center>
            </body>
                    </html>";

        echo $output;
    }
}
